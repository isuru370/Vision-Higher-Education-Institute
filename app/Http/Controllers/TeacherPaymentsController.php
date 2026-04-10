<?php

namespace App\Http\Controllers;

use App\Services\TeacherPaymentsService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TeacherPaymentsController extends Controller
{
    protected $teacherPaymentsService;
    public function __construct(TeacherPaymentsService $teacherPaymentsService)
    {
        $this->teacherPaymentsService = $teacherPaymentsService;
    }

    public function getMonthlyPayments($yearMonth)
    {
        return $this->teacherPaymentsService->fetchTeacherPaymentsByMonth($yearMonth);
    }

    public function fetchTeacherPaymentsCurrentMonth()
    {
        return $this->teacherPaymentsService->fetchTeacherPaymentsCurrentMonth();
    }
    public function fetchTeacherClassPayments($teacherId, $yearMonth)
    {
        return $this->teacherPaymentsService->fetchTeacherPaymentsByTeacher($teacherId, $yearMonth);
    }
    public function getTeacherClassWiseStudentPaymentStatus($teacherId, $yearMonth, Request $request)
    {
        return $this->teacherPaymentsService->getTeacherClassWiseStudentPaymentStatus($teacherId, $yearMonth, $request);
    }
    public function fetchSalarySlipDataTest($teacherId, $yearMonth)
    {
        return $this->teacherPaymentsService->fetchSalarySlipDataTest($teacherId, $yearMonth);
    }



    public function fetchTeacherPaymentsDaily(Request $request)
    {
        try {
            $response = $this->teacherPaymentsService->fetchTeacherPaymentsDaily();
            $result = $response->getData(true);

            // check success
            if (($result['status'] ?? 'error') !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Failed to fetch daily payments.'
                ], $response->getStatusCode());
            }

            $data = $result['data'] ?? [];
            $yearMonth = $result['year_month'] ?? now()->format('Y-m');
            $daysInMonth = $result['days_in_month'] ?? now()->daysInMonth;

            $filename = "daily_teacher_payments_{$yearMonth}.pdf";

            $pdf = Pdf::loadView('reports.pdf.daily-payments', [
                'data' => $data,
                'year_month' => $yearMonth,
                'days_in_month' => $daysInMonth,
            ]);

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate daily PDF report.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchTeacherPaymentsWeekly()
    {
        try {
            $response = $this->teacherPaymentsService->fetchTeacherPaymentsWeekly();
            $result = $response->getData(true);

            if (($result['status'] ?? 'error') !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Failed to fetch weekly payments.'
                ], $response->getStatusCode());
            }

            $data = $result['data'] ?? [];
            $yearMonth = $result['year_month'] ?? now()->format('Y-m');

            $filename = "weekly_teacher_payments_{$yearMonth}.pdf";

            $pdf = Pdf::loadView('reports.pdf.weekly-payments', [
                'data' => $data,
                'year_month' => $yearMonth,
            ]);

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate weekly PDF report.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchTeacherPaymentsDailyByTeacher(Request $request)
    {
        $response = $this->teacherPaymentsService->fetchTeacherPaymentsDailyByTeacher($request);
        $result = $response->getData(true);

        // Get teacher name from the data
        $teacherName = $result['data']['teacher_name'] ?? 'teacher';
        $date = $result['date'] ?? $request->day ?? date('Y-m-d');

        // Clean teacher name for filename (remove special characters, replace spaces with underscore)
        $cleanTeacherName = preg_replace('/[^A-Za-z0-9]/', '_', $teacherName);
        $cleanTeacherName = preg_replace('/_+/', '_', $cleanTeacherName);
        $cleanTeacherName = trim($cleanTeacherName, '_');

        // Format date for filename
        $formattedDate = str_replace('-', '_', $date);

        // Generate filename
        $filename = "daily_teacher_payment_{$cleanTeacherName}_{$formattedDate}.pdf";

        $pdf = Pdf::loadView('reports.pdf.daily-teacher-payment', [
            'data' => $result['data'] ?? [],
            'date' => $date,
        ]);

        return $pdf->download($filename);
    }

    public function fetchTeacherPaymentsWeeklyByTeacher(Request $request)
    {
        try {
            $response = $this->teacherPaymentsService->fetchTeacherPaymentsWeeklyByTeacher($request);
            $result = $response->getData(true);

            // Check if service returned success
            if (($result['status'] ?? 'error') !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Failed to fetch weekly teacher payment report.'
                ], $response->getStatusCode());
            }

            $data = $result['data'] ?? [];
            $teacherName = $data['teacher_name'] ?? 'teacher';
            $startDate = $result['start_date'] ?? $request->start_date ?? date('Y-m-d');
            $endDate = $result['end_date'] ?? $request->end_date ?? date('Y-m-d');

            // Clean teacher name for filename
            $cleanTeacherName = preg_replace('/[^A-Za-z0-9]/', '_', $teacherName);
            $cleanTeacherName = preg_replace('/_+/', '_', $cleanTeacherName);
            $cleanTeacherName = trim($cleanTeacherName, '_');

            if (empty($cleanTeacherName)) {
                $cleanTeacherName = 'teacher';
            }

            // Format dates for filename
            $formattedStartDate = str_replace('-', '_', $startDate);
            $formattedEndDate = str_replace('-', '_', $endDate);

            // Generate filename
            $filename = "weekly_teacher_payment_{$cleanTeacherName}_{$formattedStartDate}_to_{$formattedEndDate}.pdf";

            $pdf = Pdf::loadView('reports.pdf.weekly-teacher-payment', [
                'data' => $data,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate weekly PDF report.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function studentPaymentMonthCheck($teacherId, $yearMonth)
    {
        return $this->teacherPaymentsService->studentPaymentMonthCheck($teacherId, $yearMonth);
    }
    public function showSalarySlip($teacherId, $yearMonth)
    {
        try {
            $data = $this->teacherPaymentsService
                ->fetchSalarySlipData($teacherId, $yearMonth);

            if (($data['status'] ?? 'error') !== 'success') {
                return view('teacher_payment.salary-slip-exact', ['data' => $data]);
            }

            return view('teacher_payment.salary-slip-exact', ['data' => $data]);
        } catch (\Exception $e) {

            return view('teacher_payment.salary-slip-exact', [
                'data' => [
                    'status' => 'error',
                    'message' => 'Unexpected error occurred.',
                    'teacher_id' => $teacherId,
                    'month_year' => $yearMonth
                ]
            ]);
        }
    }



    public function teachersExpenses($yearMonth)
    {
        return $this->teacherPaymentsService->teachersExpenses($yearMonth);
    }

    public function togglePaymentStatus(Request $request, $id)
    {
        return $this->teacherPaymentsService->togglePaymentStatus($request, $id);
    }
    public function storeTeacherPayments(Request $request)
    {
        return $this->teacherPaymentsService->storeTeacherPayments($request);
    }



    // web page routes

    public function indexPage()
    {
        return view('teacher_payment.index');
    }
    public function paymentPage($teacherId)
    {
        return view('teacher_payment.salary', ['teacherId' => $teacherId]);
    }
    public function historyPage($teacherId)
    {
        return view('teacher_payment.history', ['teacherId' => $teacherId]);
    }
    public function viewPage($teacherId)
    {
        return view('teacher_payment.view', ['teacherId' => $teacherId]);
    }
    public function expensesPage()
    {
        return view('teacher_payment.expenses');
    }
}
