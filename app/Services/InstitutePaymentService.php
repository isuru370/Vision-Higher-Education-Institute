<?php

namespace App\Services;

use App\Models\AdmissionPayments;
use App\Models\ExtraIncomes;
use App\Models\InstitutePayment;
use App\Models\Payments;
use App\Models\Teacher;
use App\Models\TeacherPayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class InstitutePaymentService
{

    public function fetchInstitutePaymentByMonth($yearMonth)
    {
        try {
            $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth()->startOfDay();
            $endOfMonth   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth()->endOfDay();
            $monthYear    = Carbon::createFromFormat('Y-m', $yearMonth)->format('m Y');
            $organizerPercentage = 10.0;

            // ---------- INSTITUTE INCOME ----------
            $admissionPayment = (float) AdmissionPayments::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $extraIncome = (float) ExtraIncomes::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $totalExpenese = (float) InstitutePayment::where('status', 1)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('payment');

            // ---------- TEACHER PAYMENTS ----------
            $teacherAdvance = (float) TeacherPayment::where('status', 1)
                ->where('reason_code', '!=', 'salary')
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('payment');

            $teacherSalary = (float) TeacherPayment::where('status', 1)
                ->where('reason_code', 'salary')
                ->where('payment_for', $monthYear)
                ->sum('payment');

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
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
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

                        // invalid percentage setup protect
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
                $totalForMonth = round((float) $payments->sum('amount'), 2);
                $teacherTotalEarning = round((float) $classWiseTotals->sum('teacher_earning'), 2);
                $organizerTotalIncome = round((float) $classWiseTotals->sum('organizer_income'), 2);
                $institutionTotalIncome = round((float) $classWiseTotals->sum('institute_income'), 2);

                $totalTeacherPayments += $totalForMonth;
                $totalTeacherEarnings += $teacherTotalEarning;
                $totalOrganizerIncome += $organizerTotalIncome;
                $totalInstituteIncome += $institutionTotalIncome;

                // ---------- ADVANCE & SALARY ----------
                $teacherMonthlyAdvance = (float) TeacherPayment::where('teacher_id', $teacher->id)
                    ->where('status', 1)
                    ->where('reason_code', '!=', 'salary')
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->sum('payment');

                $teacherMonthlySalary = (float) TeacherPayment::where('teacher_id', $teacher->id)
                    ->where('status', 1)
                    ->where('reason_code', 'salary')
                    ->where('payment_for', $monthYear)
                    ->sum('payment');

                $teacherNetEarning = round(
                    $teacherTotalEarning - ($teacherMonthlyAdvance + $teacherMonthlySalary),
                    2
                );

                $totalTeacherNetEarnings += $teacherNetEarning;

                // ---------- RESULT ----------
                $result[] = [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => trim($teacher->fname . ' ' . $teacher->lname),
                    'total_payments_this_month' => $totalForMonth,
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
            $netIncome = round($totalWithExtraIncome - $totalExpenese, 2);

            return response()->json([
                'status' => 'success',
                'year_month' => $yearMonth,
                'summary' => [
                    'total_teacher_payments' => round($totalTeacherPayments, 2),
                    'total_teacher_earnings' => round($totalTeacherEarnings, 2),
                    'total_teacher_advances' => round($teacherAdvance, 2),
                    'total_teacher_salaries' => round($teacherSalary, 2),
                    'total_teacher_net_earnings' => round($totalTeacherNetEarnings, 2),
                    'total_organizer_income' => round($totalOrganizerIncome, 2),
                    'total_institute_from_classes' => round($totalInstituteIncome, 2),
                    'admission_payments' => round($admissionPayment, 2),
                    'extra_income_for_month' => round($extraIncome, 2),
                    'total_institute_expenese' => round($totalExpenese, 2),
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




    public function fetchExtraIncome($date)
    {
        try {
            // Parse the date to get year and month
            $yearMonth = Carbon::parse($date)->format('Y-m');

            // Get all extra incomes for the specific month
            $extraIncomes = ExtraIncomes::whereYear('created_at', Carbon::parse($date)->year)
                ->whereMonth('created_at', Carbon::parse($date)->month)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $extraIncomes,
                'total' => $extraIncomes->sum('amount')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchInstituteExpenses($yearMonth)
    {
        try {
            $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth()->startOfDay();
            $endOfMonth   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth()->endOfDay();
            $organizerPercentage = 10.0;

            // ---------- ADMISSION PAYMENTS ----------
            $admissionPayment = (float) AdmissionPayments::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            // ---------- TEACHER PAYMENTS & CLASS-WISE INCOME ----------
            $teachers = Teacher::where('is_active', 1)
                ->select('id', 'fname', 'lname')
                ->get();

            $totalIncomeFromClasses = 0.0;
            $totalOrganizerIncome = 0.0;
            $totalTeacherEarnings = 0.0;

            foreach ($teachers as $teacher) {
                $payments = Payments::where('status', 1)
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->whereHas('studentStudentClass.studentClass', function ($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id)
                            ->where('is_active', 1);
                    })
                    ->with('studentStudentClass.studentClass')
                    ->get();

                $classWiseTotals = $payments
                    ->groupBy(function ($p) {
                        return optional(optional($p->studentStudentClass)->studentClass)->id;
                    })
                    ->map(function ($group) use ($organizerPercentage) {
                        $class = optional(optional($group->first()->studentStudentClass)->studentClass);

                        if (!$class || !$class->id) {
                            return [
                                'teacher_earning' => 0.0,
                                'organizer_income' => 0.0,
                                'institution_income' => 0.0,
                            ];
                        }

                        $classTotal = (float) $group->sum('amount');
                        $teacherPercentage = (float) ($class->teacher_percentage ?? 0);

                        if (($teacherPercentage + $organizerPercentage) > 100) {
                            return [
                                'teacher_earning' => 0.0,
                                'organizer_income' => 0.0,
                                'institution_income' => 0.0,
                            ];
                        }

                        $teacherEarning = round(($classTotal * $teacherPercentage) / 100, 2);
                        $organizerIncome = round(($classTotal * $organizerPercentage) / 100, 2);
                        $institutionIncome = round($classTotal - ($teacherEarning + $organizerIncome), 2);

                        return [
                            'teacher_earning' => $teacherEarning,
                            'organizer_income' => $organizerIncome,
                            'institution_income' => $institutionIncome,
                        ];
                    });

                $totalTeacherEarnings += (float) $classWiseTotals->sum('teacher_earning');
                $totalOrganizerIncome += (float) $classWiseTotals->sum('organizer_income');
                $totalIncomeFromClasses += (float) $classWiseTotals->sum('institution_income');
            }

            // ---------- EXTRA INCOME ----------
            $extraIncome = (float) ExtraIncomes::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            // ---------- EXPENSES ----------
            $expenses = InstitutePayment::where('status', 1)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->orderBy('date', 'desc')
                ->get();

            $totalExpenses = (float) $expenses->sum('payment');

            // ---------- NET CALCULATION ----------
            $grossIncome = round($totalIncomeFromClasses + $extraIncome + $admissionPayment, 2);
            $netTotal = round($grossIncome - $totalExpenses, 2);

            // ---------- EXPENSE DETAILS ----------
            $expenseDetails = $expenses->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'date' => $expense->date ? Carbon::parse($expense->date)->format('Y-m-d') : null,
                    'reason' => $expense->reason,
                    'reason_code' => $expense->reason_code,
                    'amount' => round((float) $expense->payment, 2),
                    'status' => $expense->status,
                    'created_at' => $expense->created_at ? Carbon::parse($expense->created_at)->format('Y-m-d H:i:s') : null,
                    'updated_at' => $expense->updated_at ? Carbon::parse($expense->updated_at)->format('Y-m-d H:i:s') : null,
                ];
            });

            return response()->json([
                'status' => 'success',
                'year_month' => $yearMonth,
                'summary' => [
                    'teacher_earnings_from_classes' => round($totalTeacherEarnings, 2),
                    'organizer_income_from_classes' => round($totalOrganizerIncome, 2),
                    'income_from_classes' => round($totalIncomeFromClasses, 2),
                    'extra_income' => round($extraIncome, 2),
                    'admission_payment' => round($admissionPayment, 2),
                    'gross_income' => $grossIncome,
                    'total_expenses' => round($totalExpenses, 2),
                    'net_total' => $netTotal
                ],
                'expense_details' => $expenseDetails
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function fetchYearlyIncomeChart($year)
    {
        try {
            $monthlyData = [];
            $monthLabels = [];
            $monthlyGrossIncomes = [];
            $organizerPercentage = 10.0;

            for ($month = 1; $month <= 12; $month++) {
                $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth()->startOfDay();
                $endOfMonth   = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

                $monthLabel = Carbon::create($year, $month, 1)->format('M');
                $monthLabels[] = $monthLabel;

                $payments = Payments::where('status', 1)
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->with('studentStudentClass.studentClass.teacher')
                    ->get();

                $classWiseIncome = $payments
                    ->groupBy(function ($p) {
                        return optional(optional($p->studentStudentClass)->studentClass)->id;
                    })
                    ->map(function ($group) use ($organizerPercentage) {
                        $class = optional(optional($group->first()->studentStudentClass)->studentClass);
                        $teacher = optional($class)->teacher;

                        if (!$class || !$class->id) {
                            return [
                                'class_id' => null,
                                'class_name' => null,
                                'teacher_percentage' => 0.0,
                                'organizer_percentage' => $organizerPercentage,
                                'class_total' => 0.0,
                                'teacher_earning' => 0.0,
                                'organizer_income' => 0.0,
                                'institution_income' => 0.0
                            ];
                        }

                        $classTotal = (float) $group->sum('amount');
                        $teacherPercentage = (float) ($class->teacher_percentage ?? 0);

                        if ($teacher && $teacher->is_active) {
                            if (($teacherPercentage + $organizerPercentage) > 100) {
                                $teacherEarning = 0.0;
                                $organizerIncome = 0.0;
                                $institutionIncome = 0.0;
                            } else {
                                $teacherEarning = round(($classTotal * $teacherPercentage) / 100, 2);
                                $organizerIncome = round(($classTotal * $organizerPercentage) / 100, 2);
                                $institutionIncome = round($classTotal - ($teacherEarning + $organizerIncome), 2);
                            }
                        } else {
                            // no active teacher -> full amount goes to institute
                            $teacherEarning = 0.0;
                            $organizerIncome = 0.0;
                            $institutionIncome = round($classTotal, 2);
                        }

                        return [
                            'class_id' => $class->id,
                            'class_name' => $class->class_name,
                            'teacher_percentage' => $teacherPercentage,
                            'organizer_percentage' => $organizerPercentage,
                            'class_total' => round($classTotal, 2),
                            'teacher_earning' => $teacherEarning,
                            'organizer_income' => $organizerIncome,
                            'institution_income' => $institutionIncome
                        ];
                    })
                    ->values();

                $totalIncomeFromClasses = round((float) $classWiseIncome->sum('institution_income'), 2);
                $totalOrganizerIncome = round((float) $classWiseIncome->sum('organizer_income'), 2);
                $totalTeacherEarnings = round((float) $classWiseIncome->sum('teacher_earning'), 2);

                // Extra income
                $extraIncome = (float) ExtraIncomes::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                // Admission income for this month
                $admissionIncome = (float) AdmissionPayments::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                $grossIncome = round($totalIncomeFromClasses + $extraIncome + $admissionIncome, 2);
                $monthlyGrossIncomes[] = $grossIncome;

                $monthlyData[] = [
                    'month' => $monthLabel,
                    'month_number' => $month,
                    'income_from_classes' => $totalIncomeFromClasses,
                    'teacher_earnings_from_classes' => $totalTeacherEarnings,
                    'organizer_income_from_classes' => $totalOrganizerIncome,
                    'extra_income' => round($extraIncome, 2),
                    'admission_income' => round($admissionIncome, 2),
                    'gross_income' => $grossIncome
                ];
            }

            // Yearly totals
            $yearlyIncomeFromClasses = array_sum(array_column($monthlyData, 'income_from_classes'));
            $yearlyTeacherEarnings = array_sum(array_column($monthlyData, 'teacher_earnings_from_classes'));
            $yearlyOrganizerIncome = array_sum(array_column($monthlyData, 'organizer_income_from_classes'));
            $yearlyExtraIncome = array_sum(array_column($monthlyData, 'extra_income'));
            $yearlyAdmissionIncome = array_sum(array_column($monthlyData, 'admission_income'));
            $yearlyGrossIncome = array_sum($monthlyGrossIncomes);

            return response()->json([
                'status' => 'success',
                'year' => $year,
                'summary' => [
                    'yearly_income_from_classes' => round($yearlyIncomeFromClasses, 2),
                    'yearly_teacher_earnings_from_classes' => round($yearlyTeacherEarnings, 2),
                    'yearly_organizer_income_from_classes' => round($yearlyOrganizerIncome, 2),
                    'yearly_extra_income' => round($yearlyExtraIncome, 2),
                    'yearly_admission_income' => round($yearlyAdmissionIncome, 2),
                    'yearly_gross_income' => round($yearlyGrossIncome, 2)
                ],
                'chart_data' => [
                    'labels' => $monthLabels,
                    'gross_incomes' => $monthlyGrossIncomes,
                    'detailed_data' => $monthlyData
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }




    public function institutePaymentStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'reason_code' => 'required|string|max:50',
                'payment' => 'required|numeric|min:0',
                'reason' => 'nullable|string|max:255', // nullable කරන්න
            ]);

            $income = InstitutePayment::create([
                'payment' => $validated['payment'],
                'date' => now(),
                'reason' => $validated['reason'] ?? '', // empty string ලෙස හෝ
                'reason_code' => $validated['reason_code'],
                'status' => 1,
                'user_id' => auth()->id() ?? null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Institute payment saved successfully',
                'data' => $income,
                'current_server_time' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'current_server_time' => now()->format('Y-m-d H:i:s')
            ], 500);
        }
    }

    public function institutePaymentDestroy($id)
    {
        try {
            // Find the payment record
            $payment = InstitutePayment::find($id);

            // Check if payment exists
            if (!$payment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment record not found'
                ], 404);
            }

            // Update status to 0 (soft delete or deactivate)
            $payment->update([
                'status' => 0,
                'user_id' => auth()->id() ?? null,
                'updated_at' => now() // Optional: update timestamp
            ]);


            return response()->json([
                'status' => 'success',
                'message' => 'Payment record deactivated successfully',
                'data' => $payment
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function extraIncomeStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
            ]);

            $income = ExtraIncomes::create([
                'reason' => $validated['reason'],
                'amount' => $validated['amount'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Extra income saved successfully',
                'data' => $income
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function extraIncomeDelete($id)
    {
        try {
            // Find the income record by ID
            $income = ExtraIncomes::find($id);

            if (!$income) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Income record not found'
                ], 404);
            }

            // Check if the record is from the current month
            $incomeMonth = Carbon::parse($income->created_at)->format('Y-m');
            $currentMonth = Carbon::now()->format('Y-m');

            if ($incomeMonth !== $currentMonth) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'You can only delete extra incomes from the current month'
                ], 403);
            }

            // Now safe to delete
            $income->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Extra income deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
