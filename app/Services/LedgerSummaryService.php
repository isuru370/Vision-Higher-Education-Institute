<?php

namespace App\Services;

use App\Models\AdmissionPayments;
use App\Models\ExtraIncomes;
use App\Models\InstitutePayment;
use App\Models\Payments;
use Carbon\Carbon;
use Exception;
use Throwable;
use Illuminate\Support\Collection;

class LedgerSummaryService
{
    /**
     * Monthly Ledger Summary (Cash Based)
     */
    public function monthlyLedgerSummary(string $yearMonth): array
    {
        try {
            $date  = Carbon::createFromFormat('Y-m', $yearMonth);
            $start = $date->copy()->startOfMonth()->startOfDay();
            $end   = $date->copy()->endOfMonth()->endOfDay();

            $openingBalance = $this->getOpeningBalance($yearMonth);

            $entries = collect()
                ->merge($this->classIncomeEntries($start, $end))
                ->merge($this->admissionEntries($start, $end))
                ->merge($this->extraIncomeEntries($start, $end))
                ->merge($this->instituteExpenseEntries($start, $end))
                ->sortBy('date')
                ->values();

            $ledger = $this->applyRunningBalance($entries, $openingBalance);

            $summary = $this->calculateSummary($ledger);

            return [
                'status' => 'success',
                'data' => [
                    'period' => [
                        'month' => $date->format('F Y'),
                        'start_date' => $start->format('Y-m-d'),
                        'end_date' => $end->format('Y-m-d'),
                    ],
                    'opening_balance' => round($openingBalance, 2),
                    'ledger' => $ledger,
                    'summary' => $summary,
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Ledger calculation failed'
            ];
        }
    }

    /**
     * Get Opening Balance (Previous Month Closing Balance)
     */
    private function getOpeningBalance(string $yearMonth): float
    {
        try {
            if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $yearMonth)) {
                return 0.0;
            }

            $requestedMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $previousMonth = $requestedMonth->copy()->subMonth();

            $monthStart = $previousMonth->copy()->startOfMonth()->startOfDay();
            $monthEnd   = $previousMonth->copy()->endOfMonth()->endOfDay();

            $openingBalance = 0.0;

            // ---------- CLASS PAYMENTS (Institute share only) ----------
            $classPayments = Payments::with(['studentStudentClass.studentClass.teacher'])
                ->where('status', 1)
                ->whereBetween('payment_date', [$monthStart, $monthEnd])
                ->get();

            $totalInstituteReceipts = 0.0;

            foreach ($classPayments as $payment) {
                $class = optional($payment->studentStudentClass)->studentClass;
                $teacher = optional($class)->teacher;
                $teacherPercentage = (float) ($class->teacher_percentage ?? 0);

                if ($class && $teacher && $teacher->is_active) {
                    $breakdown = $this->calculatePaymentBreakdown(
                        (float) $payment->amount,
                        $teacherPercentage
                    );

                    $instituteShare = $breakdown['institute_share'];
                } else {
                    // no active teacher/class → full amount institute share
                    $instituteShare = (float) $payment->amount;
                }

                $totalInstituteReceipts += $instituteShare;
            }

            // ---------- ADMISSION PAYMENTS ----------
            $totalInstituteReceipts += (float) AdmissionPayments::whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');

            // ---------- EXTRA INCOMES ----------
            $totalInstituteReceipts += (float) ExtraIncomes::whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');

            // ---------- EXPENSES ----------
            $totalExpenses = (float) InstitutePayment::where('status', 1)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('payment');

            $openingBalance += ($totalInstituteReceipts - $totalExpenses);

            return round($openingBalance, 2);
        } catch (Throwable $e) {
            return 0.0;
        }
    }

    /**
     * Class income ledger entries (INSTITUTE SHARE ONLY)
     */
    private function classIncomeEntries(Carbon $start, Carbon $end): Collection
    {
        $entries = collect();

        $paymentsByDate = Payments::with(['studentStudentClass.studentClass.teacher'])
            ->where('status', 1)
            ->whereBetween('payment_date', [$start, $end])
            ->orderBy('payment_date')
            ->get()
            ->groupBy(fn($payment) => Carbon::parse($payment->payment_date)->format('Y-m-d'));

        foreach ($paymentsByDate as $date => $payments) {
            $totalForDay = 0.0;
            $paymentCount = $payments->count();

            foreach ($payments as $payment) {
                $class = optional($payment->studentStudentClass)->studentClass;
                $teacher = optional($class)->teacher;
                $teacherPercentage = (float) ($class->teacher_percentage ?? 0);

                if ($class && $teacher && $teacher->is_active) {
                    $breakdown = $this->calculatePaymentBreakdown(
                        (float) $payment->amount,
                        $teacherPercentage
                    );

                    $instituteShare = $breakdown['institute_share'];
                } else {
                    $instituteShare = (float) $payment->amount;
                }

                $totalForDay += $instituteShare;
            }

            if ($totalForDay > 0) {
                $entries->push([
                    'date' => Carbon::parse($date)->startOfDay(),
                    'description' => "Class Fee ({$paymentCount})",
                    'receipt' => (float) round($totalForDay, 2),
                    'payment' => 0.0
                ]);
            }
        }

        return $entries;
    }

    /**
     * Admission ledger entries
     */
    private function admissionEntries(Carbon $start, Carbon $end): Collection
    {
        return AdmissionPayments::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as day, SUM(amount) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn($p) => [
                'date' => Carbon::parse($p->day)->startOfDay(),
                'description' => 'Admission Fee',
                'receipt' => (float) $p->total,
                'payment' => 0.0
            ]);
    }

    /**
     * Extra income ledger entries
     */
    private function extraIncomeEntries(Carbon $start, Carbon $end): Collection
    {
        return ExtraIncomes::whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get()
            ->map(fn($e) => [
                'date' => Carbon::parse($e->created_at)->startOfDay(),
                'description' => $e->reason ?: 'Extra Income',
                'receipt' => (float) $e->amount,
                'payment' => 0.0
            ]);
    }

    /**
     * Institute expense ledger entries
     */
    private function instituteExpenseEntries(Carbon $start, Carbon $end): Collection
    {
        return InstitutePayment::where('status', 1)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get()
            ->map(fn($e) => [
                'date' => Carbon::parse($e->date)->startOfDay(),
                'description' => $e->reason ?: 'Institute Expense',
                'receipt' => 0.0,
                'payment' => (float) $e->payment
            ]);
    }

    /**
     * Apply running balance
     */
    private function applyRunningBalance($entries, float $openingBalance)
    {
        $balance = $openingBalance;

        return $entries->map(function ($e) use (&$balance) {
            $balance += ((float) $e['receipt']) - ((float) $e['payment']);

            return [
                'date' => Carbon::parse($e['date'])->format('d M Y'),
                'description' => $e['description'],
                'receipt' => $e['receipt'] > 0 ? number_format((float) $e['receipt'], 2) : '',
                'payment' => $e['payment'] > 0 ? number_format((float) $e['payment'], 2) : '',
                'balance' => number_format($balance, 2)
            ];
        });
    }

    /**
     * Calculate summary
     */
    private function calculateSummary($ledger)
    {
        $receipts = $ledger->sum(fn($l) => (float) str_replace(',', '', $l['receipt'] ?: 0));
        $payments = $ledger->sum(fn($l) => (float) str_replace(',', '', $l['payment'] ?: 0));

        return [
            'total_receipts' => round($receipts, 2),
            'total_payments' => round($payments, 2),
            'net_change' => round($receipts - $payments, 2),
            'closing_balance' => $ledger->last()['balance'] ?? '0.00'
        ];
    }

    /**
     * Payment breakdown:
     * Teacher % + Organizer 10% + Institute remainder
     */
    private function calculatePaymentBreakdown(float $amount, float $teacherPercentage): array
    {
        $organizerPercentage = 10.0;

        $amount = max(0, $amount);
        $teacherPercentage = max(0, $teacherPercentage);

        // invalid percentage setup
        if (($teacherPercentage + $organizerPercentage) > 100) {
            return [
                'teacher_share' => 0.0,
                'organizer_share' => 0.0,
                'institute_share' => 0.0,
            ];
        }

        $teacherShare = round(($amount * $teacherPercentage) / 100, 2);
        $organizerShare = round(($amount * $organizerPercentage) / 100, 2);
        $instituteShare = round($amount - ($teacherShare + $organizerShare), 2);

        return [
            'teacher_share' => $teacherShare,
            'organizer_share' => $organizerShare,
            'institute_share' => $instituteShare,
        ];
    }
}