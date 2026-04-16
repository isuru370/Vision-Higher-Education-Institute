<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\Payments;
use App\Models\Teacher;
use App\Models\TeacherPayment;
use Carbon\Carbon;
use Exception;
use Throwable;
use Illuminate\Support\Collection;

class TeacherLedgerSummaryService
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

            // පෙර මාසයේ closing balance
            $openingBalance = $this->getOpeningBalance($yearMonth);

            // Ledger entries එකතු කරන්න
            $entries = collect()
                ->merge($this->teacherIncomeEntries($start, $end))
                ->merge($this->teacherPaymentEntries($start, $end))
                ->sortBy('date')
                ->values();

            // Running balance apply කරන්න
            $ledger = $this->applyRunningBalance($entries, $openingBalance);

            // Summary calculate කරන්න
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
     * Get Opening Balance (Previous Month Net Balance)
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

            // optional limit: before 2026-01 return 0
            if ($monthStart->lt(Carbon::create(2026, 1, 1)->startOfMonth())) {
                return 0.0;
            }

            $totalBalance = 0.0;

            $teachers = Teacher::where('is_active', 1)->get();

            foreach ($teachers as $teacher) {
                $teacherTotalShare = 0.0;

                $classes = ClassRoom::where('teacher_id', $teacher->id)
                    ->where('is_active', 1)
                    ->get();

                foreach ($classes as $class) {
                    $classEarnings = Payments::where('status', 1)
                        ->whereBetween('payment_date', [$monthStart, $monthEnd])
                        ->whereHas('studentStudentClass.studentClass', function ($q) use ($class) {
                            $q->where('id', $class->id)
                                ->where('is_active', 1);
                        })
                        ->sum('amount');

                    $breakdown = $this->calculatePaymentBreakdown(
                        (float) $classEarnings,
                        (float) ($class->teacher_percentage ?? 0)
                    );

                    $teacherTotalShare += $breakdown['teacher_share'];
                }

                $teacherPaid = TeacherPayment::where('status', 1)
                    ->whereBetween('date', [$monthStart, $monthEnd])
                    ->where('teacher_id', $teacher->id)
                    ->sum('payment');

                $teacherNet = $teacherTotalShare - (float) $teacherPaid;

                $totalBalance += $teacherNet;
            }

            return round($totalBalance, 2);
        } catch (Throwable $e) {
            return 0.0;
        }
    }

    /**
     * Teacher income ledger entries
     */
    private function teacherIncomeEntries(Carbon $start, Carbon $end): Collection
    {
        $entries = collect();

        // Group payments by class and date
        $payments = Payments::with(['studentStudentClass.studentClass.teacher'])
            ->where('status', 1)
            ->whereBetween('payment_date', [$start, $end])
            ->whereHas('studentStudentClass.studentClass', function ($q) {
                $q->where('is_active', 1)
                    ->whereHas('teacher', function ($q2) {
                        $q2->where('is_active', 1);
                    });
            })
            ->orderBy('payment_date')
            ->get();

        // Group by class_id and date (එක class එකකට දිනකට එක් entry එකක්)
        $groupedPayments = [];

        foreach ($payments as $p) {
            $class = optional($p->studentStudentClass)->studentClass;
            $teacher = optional($class)->teacher;

            if (!$teacher || !$class) {
                continue;
            }

            $date = Carbon::parse($p->payment_date)->format('Y-m-d');
            $key = $class->id . '|' . $date;

            if (!isset($groupedPayments[$key])) {
                $groupedPayments[$key] = [
                    'date' => Carbon::parse($p->payment_date)->startOfDay(),
                    'teacher' => $teacher,
                    'class' => $class,
                    'total_amount' => 0.0
                ];
            }

            $breakdown = $this->calculatePaymentBreakdown(
                (float) $p->amount,
                (float) ($class->teacher_percentage ?? 0)
            );

            // Ledger එකට එන්නේ teacher share විතරයි
            $groupedPayments[$key]['total_amount'] += $breakdown['teacher_share'];
        }

        // Create entries
        foreach ($groupedPayments as $group) {
            if ($group['total_amount'] > 0) {
                $entries->push([
                    'date' => $group['date'],
                    'description' => 'Income - ' . trim($group['teacher']->fname . ' ' . $group['teacher']->lname) .
                        ' (' . $group['class']->class_name . ')',
                    'receipt' => (float) round($group['total_amount'], 2),
                    'payment' => 0.0
                ]);
            }
        }

        return $entries;
    }

    /**
     * Teacher payment ledger entries
     */
    private function teacherPaymentEntries(Carbon $start, Carbon $end): Collection
    {
        return TeacherPayment::with('teacher:id,fname,lname')
            ->where('status', 1)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get()
            ->map(function ($t) {
                $teacherName = trim(
                    ($t->teacher->fname ?? '') . ' ' . ($t->teacher->lname ?? '')
                );

                $description = $t->reason_code
                    ? $t->reason_code . ' - ' . $teacherName
                    : $teacherName;

                return [
                    'date' => Carbon::parse($t->date)->startOfDay(),
                    'description' => $description,
                    'receipt' => 0.0,
                    'payment' => (float) $t->payment
                ];
            });
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

        $teacherPercentage = max(0, $teacherPercentage);

        // Invalid setup avoid කරන්න
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
