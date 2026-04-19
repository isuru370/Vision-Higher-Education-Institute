<?php


namespace App\Services;

use App\Models\AdmissionPayments;
use App\Models\ExtraIncomes;
use App\Models\InstitutePayment;
use App\Models\Payments;
use App\Models\TeacherPayment;
use App\Models\Teacher;
use Carbon\Carbon;
use Exception;

class ReportService
{

    public function fetchInstitutePaymentByMonth($yearMonth)
    {
        try {
            $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)
                ->startOfMonth()
                ->toDateString();

            $endOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)
                ->endOfMonth()
                ->toDateString();

            return $this->fetchInstitutePaymentByDateRange($startOfMonth, $endOfMonth, 'monthly');
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchInstitutePaymentByDate($date)
    {
        try {
            $day = Carbon::parse($date)->toDateString();

            return $this->fetchInstitutePaymentByDateRange($day, $day, 'daily');
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function fetchInstitutePaymentByDateRange($startDate, $endDate, $reportType = 'range')
    {
        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end   = Carbon::parse($endDate)->endOfDay();

            $organizerPercentage = 10.0;

            // label values
            $monthYear = $start->format('m Y');

            // ---------- INSTITUTE INCOME ----------
            $admissionPayment = (float) AdmissionPayments::whereBetween('created_at', [$start, $end])
                ->sum('amount');

            $extraIncome = (float) ExtraIncomes::whereBetween('created_at', [$start, $end])
                ->sum('amount');

            $totalExpense = (float) InstitutePayment::where('status', 1)
                ->whereBetween('date', [$start, $end])
                ->sum('payment');

            // ---------- TEACHER PAYMENTS SUMMARY ----------
            $teacherAdvance = (float) TeacherPayment::where('status', 1)
                ->where('reason_code', '!=', 'salary')
                ->whereBetween('date', [$start, $end])
                ->sum('payment');

            // Monthly report = salary by payment_for month
            // Daily / Range report = salary by actual paid date
            if ($reportType === 'monthly') {
                $teacherSalary = (float) TeacherPayment::where('status', 1)
                    ->where('reason_code', 'salary')
                    ->where('payment_for', $monthYear)
                    ->sum('payment');
            } else {
                $teacherSalary = (float) TeacherPayment::where('status', 1)
                    ->where('reason_code', 'salary')
                    ->whereBetween('date', [$start, $end])
                    ->sum('payment');
            }

            // ---------- TEACHERS ----------
            $teachers = Teacher::select('id', 'fname', 'lname')
                ->where('is_active', 1)
                ->get();

            $result = [];

            $totalTeacherPayments = 0.0;
            $totalTeacherEarnings = 0.0;
            $totalTeacherNetEarnings = 0.0;
            $totalInstituteIncome = 0.0;
            $totalOrganizerIncome = 0.0;

            foreach ($teachers as $teacher) {

                $payments = Payments::where('status', 1)
                    ->whereBetween('payment_date', [$start, $end])
                    ->whereHas('studentStudentClass.studentClass', function ($q) use ($teacher) {
                        $q->where('is_active', 1)
                            ->where('teacher_id', $teacher->id);
                    })
                    ->with('studentStudentClass.studentClass')
                    ->get();

                // ---------- CLASS WISE ----------
                $classWiseTotals = $payments
                    ->groupBy(function ($p) {
                        return optional(optional($p->studentStudentClass)->studentClass)->id;
                    })
                    ->map(function ($group) use ($organizerPercentage) {

                        $class = optional(optional($group->first()->studentStudentClass)->studentClass);

                        if (!$class || !$class->id) {
                            return [
                                'class_id' => null,
                                'class_name' => null,
                                'teacher_percentage' => 0.0,
                                'organizer_percentage' => $organizerPercentage,
                                'total_amount' => 0.0,
                                'teacher_earning' => 0.0,
                                'organizer_income' => 0.0,
                                'institute_income' => 0.0
                            ];
                        }

                        $classTotal = (float) $group->sum('amount');
                        $teacherPercentage = (float) ($class->teacher_percentage ?? 0);

                        if (($teacherPercentage + $organizerPercentage) > 100) {
                            $teacherEarning = 0.0;
                            $organizerIncome = 0.0;
                            $instituteIncome = 0.0;
                        } else {
                            $teacherEarning = round(($classTotal * $teacherPercentage) / 100, 2);
                            $organizerIncome = round(($classTotal * $organizerPercentage) / 100, 2);
                            $instituteIncome = round($classTotal - ($teacherEarning + $organizerIncome), 2);
                        }

                        return [
                            'class_id' => $class->id,
                            'class_name' => $class->class_name,
                            'teacher_percentage' => $teacherPercentage,
                            'organizer_percentage' => $organizerPercentage,
                            'total_amount' => round($classTotal, 2),
                            'teacher_earning' => $teacherEarning,
                            'organizer_income' => $organizerIncome,
                            'institute_income' => $instituteIncome
                        ];
                    })
                    ->values();

                if ($classWiseTotals->isEmpty()) {
                    $classWiseTotals = collect([[
                        'class_id' => null,
                        'class_name' => null,
                        'teacher_percentage' => 0.0,
                        'organizer_percentage' => $organizerPercentage,
                        'total_amount' => 0.0,
                        'teacher_earning' => 0.0,
                        'organizer_income' => 0.0,
                        'institute_income' => 0.0,
                    ]]);
                }

                // ---------- TOTALS ----------
                $totalForPeriod = round((float) $payments->sum('amount'), 2);
                $teacherTotalEarning = round((float) $classWiseTotals->sum('teacher_earning'), 2);
                $organizerTotalIncome = round((float) $classWiseTotals->sum('organizer_income'), 2);
                $institutionTotalIncome = round((float) $classWiseTotals->sum('institute_income'), 2);

                $totalTeacherPayments += $totalForPeriod;
                $totalTeacherEarnings += $teacherTotalEarning;
                $totalOrganizerIncome += $organizerTotalIncome;
                $totalInstituteIncome += $institutionTotalIncome;

                // ---------- ADVANCE & SALARY ----------
                $teacherMonthlyAdvance = (float) TeacherPayment::where('teacher_id', $teacher->id)
                    ->where('status', 1)
                    ->where('reason_code', '!=', 'salary')
                    ->whereBetween('date', [$start, $end])
                    ->sum('payment');

                if ($reportType === 'monthly') {
                    $teacherMonthlySalary = (float) TeacherPayment::where('teacher_id', $teacher->id)
                        ->where('status', 1)
                        ->where('reason_code', 'salary')
                        ->where('payment_for', $monthYear)
                        ->sum('payment');
                } else {
                    $teacherMonthlySalary = (float) TeacherPayment::where('teacher_id', $teacher->id)
                        ->where('status', 1)
                        ->where('reason_code', 'salary')
                        ->whereBetween('date', [$start, $end])
                        ->sum('payment');
                }

                $teacherNetEarning = round(
                    $teacherTotalEarning - ($teacherMonthlyAdvance + $teacherMonthlySalary),
                    2
                );

                $totalTeacherNetEarnings += $teacherNetEarning;

                // ---------- RESULT ----------
                $result[] = [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => trim($teacher->fname . ' ' . $teacher->lname),
                    'total_payments_for_period' => $totalForPeriod,
                    'teacher_total_earning' => $teacherTotalEarning,
                    'teacher_advance' => round($teacherMonthlyAdvance, 2),
                    'teacher_salary' => round($teacherMonthlySalary, 2),
                    'teacher_net_earning' => $teacherNetEarning,
                    'organizer_total_income' => $organizerTotalIncome,
                    'institution_total_income' => $institutionTotalIncome,
                    'class_wise_totals' => $classWiseTotals
                ];
            }

            // ---------- FINAL CALCULATIONS ----------
            $instituteIncomeWithAdmission = round($totalInstituteIncome + $admissionPayment, 2);
            $totalWithExtraIncome = round($instituteIncomeWithAdmission + $extraIncome, 2);
            $netIncome = round($totalWithExtraIncome - $totalExpense, 2);

            return response()->json([
                'status' => 'success',
                'report_type' => $reportType,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'summary' => [
                    'total_teacher_payments' => round($totalTeacherPayments, 2),
                    'total_teacher_earnings' => round($totalTeacherEarnings, 2),
                    'total_teacher_advances' => round($teacherAdvance, 2),
                    'total_teacher_salaries' => round($teacherSalary, 2),
                    'total_teacher_net_earnings' => round($totalTeacherNetEarnings, 2),
                    'total_organizer_income' => round($totalOrganizerIncome, 2),
                    'total_institute_from_classes' => round($totalInstituteIncome, 2),
                    'admission_payments' => round($admissionPayment, 2),
                    'extra_income_for_period' => round($extraIncome, 2),
                    'total_institute_expense' => round($totalExpense, 2),
                    'institute_gross_income' => $totalWithExtraIncome,
                    'institute_net_income' => $netIncome
                ],
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function generateStudentsReport()
    {
        // Logic to generate payment report
        return "Students report generated.";
    }

    public function generateTeachersReport()
    {
        // Logic to generate attendance report
        return "Teachers report generated.";
    }

    public function generateClassesReport()
    {
        // Logic to generate exam report
        return "Classes report generated.";
    }

    /*
    // Add payment report generation methods as needed
    */

    public function generateYearlyPaymentsReport($year)
    {
        // Logic to generate exam report
        return "Exams report generated.";
    }

    public function generateMonthlyPaymentsReport($month)
    {
        // Logic to generate exam report
        return "Exams report generated.";
    }

    public function generateDayllyPaymentsReport($day)
    {
        // Logic to generate exam report
        return "Exams report generated.";
    }



    public function generateAllDailyPaymentsReport($day)
    {
        // ---------- RECEIPTS (RECORDS) ----------
        $studentPaymentRecords = Payments::where('status', 1)
            ->whereDate('created_at', $day)
            ->orderBy('created_at', 'asc')
            ->get();

        $admissionPaymentRecords = AdmissionPayments::whereDate('created_at', $day)
            ->orderBy('created_at', 'asc')
            ->get();

        $extraIncomeRecords = ExtraIncomes::whereDate('created_at', $day)
            ->orderBy('created_at', 'asc')
            ->get();

        // ---------- RECEIPTS (TOTALS) ----------
        $studentPayments = $studentPaymentRecords->sum('amount');
        $admissionPayments = $admissionPaymentRecords->sum('amount');
        $extraIncomes = $extraIncomeRecords->sum('amount');

        $totalReceipts = $studentPayments + $admissionPayments + $extraIncomes;

        // ---------- PAYMENTS (RECORDS) ----------
        $teacherPaymentRecords = TeacherPayment::where('status', 1)
            ->whereDate('created_at', $day)
            ->orderBy('created_at', 'asc')
            ->get();

        $institutePaymentRecords = InstitutePayment::where('status', 1)
            ->whereDate('created_at', $day)
            ->orderBy('created_at', 'asc')
            ->get();

        // ---------- PAYMENTS (TOTALS) ----------
        $teacherPayments = $teacherPaymentRecords->sum('payment');
        $institutePayments = $institutePaymentRecords->sum('payment');

        $totalPayments = $teacherPayments + $institutePayments;

        // ---------- BALANCE ----------
        $balance = $totalReceipts - $totalPayments;

        // ---------- RETURN FULL REPORT ----------
        return [
            'date' => $day,

            // RECEIPTS
            'student_payment_records' => $studentPaymentRecords,
            'admission_payment_records' => $admissionPaymentRecords,
            'extra_income_records' => $extraIncomeRecords,

            'student_payments_total' => $studentPayments,
            'admission_payments_total' => $admissionPayments,
            'extra_incomes_total' => $extraIncomes,
            'total_receipts' => $totalReceipts,

            // PAYMENTS
            'teacher_payment_records' => $teacherPaymentRecords,
            'institute_payment_records' => $institutePaymentRecords,

            'teacher_payments_total' => $teacherPayments,
            'institute_payments_total' => $institutePayments,
            'total_payments' => $totalPayments,

            // BALANCE
            'balance' => $balance
        ];
    }
}
