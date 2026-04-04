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



    public function fetchTeacherPaymentsDaily()
    {
        $response = $this->teacherPaymentsService->fetchTeacherPaymentsDaily();
        $result = $response->getData(true);

        $pdf = Pdf::loadView('reports.pdf.daily-payments', [
            'data' => $result['data'] ?? [],
            'day'  => $request->day ?? now()->toDateString(),
        ]);

        return $pdf->download('daily-teacher-payments.pdf');
    }

    public function fetchTeacherPaymentsWeekly()
    {
        $response = $this->teacherPaymentsService->fetchTeacherPaymentsWeekly();
        $result = $response->getData(true);

        $pdf = Pdf::loadView('reports.pdf.weekly-payments', [
            'data' => $result['data'] ?? [],
        ]);

        return $pdf->download('weekly-teacher-payments.pdf');
    }

    public function fetchTeacherPaymentsDailyByTeacher(Request $request)
    {
        $response = $this->teacherPaymentsService->fetchTeacherPaymentsDailyByTeacher($request);
        $result = $response->getData(true);

        $pdf = Pdf::loadView('reports.pdf.daily-teacher-payment', [
            'data' => $result['data'] ?? [],
            'date' => $result['date'] ?? $request->day,
        ]);

        return $pdf->download('daily-teacher-payment.pdf');
    }

    public function fetchTeacherPaymentsWeeklyByTeacher(Request $request)
    {
        $response = $this->teacherPaymentsService->fetchTeacherPaymentsWeeklyByTeacher($request);
        $result = $response->getData(true);

        $pdf = Pdf::loadView('reports.pdf.weekly-teacher-payment', [
            'data' => $result['data'] ?? [],
            'start_date' => $result['start_date'] ?? $request->start_date,
            'end_date'   => $result['end_date'] ?? $request->end_date,
        ]);

        return $pdf->download('weekly-teacher-payment.pdf');
    }

    public function studentPaymentMonthCheck($teacherId, $yearMonth)
    {
        return $this->teacherPaymentsService->studentPaymentMonthCheck($teacherId, $yearMonth);
    }
    public function showSalarySlip($teacherId, $yearMonth)
    {
        try {
            // Get data FROM SERVICE (returns ARRAY)
            $data = $this->teacherPaymentsService->fetchSalarySlipData($teacherId, $yearMonth);

            // Pass $data to Blade
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
