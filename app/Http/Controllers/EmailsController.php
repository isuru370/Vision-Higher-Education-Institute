<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReportMail;
use App\Services\TeacherPaymentsService;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class EmailsController extends Controller
{
    protected $teacherPaymentsService;

    public function __construct(TeacherPaymentsService $teacherPaymentsService)
    {
        $this->teacherPaymentsService = $teacherPaymentsService;
    }

    // public function sendPaymentReport($teacherId, $yearMonth)
    // {
    //     try {
    //         // Validate YYYY-MM
    //         if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Invalid date format. Use YYYY-MM'
    //             ], 400);
    //         }

    //         // Get flat payment data
    //         $paymentData = $this->teacherPaymentsService
    //             ->studentPaymentMonthFlat($teacherId, $yearMonth);

    //         if (!$paymentData['success']) {
    //             return response()->json($paymentData, 400);
    //         }

    //         /* ---------- CALCULATE TOTALS FROM FLAT DATA ---------- */
    //         $students = $paymentData['students'];
    //         $totalAmount = collect($students)->sum('amount');
    //         $totalStudents = collect($students)->pluck('student_id')->unique()->count();

    //         /* ---------- FORMAT MONTH FOR DISPLAY ---------- */
    //         $formattedMonth = date('F Y', strtotime($yearMonth . '-01')); // මෙතන define කරන්න
    //         $fileNameMonth = date('F-Y', strtotime($yearMonth . '-01'));

    //         /* ---------- PDF DATA ---------- */
    //         $pdfViewData = [
    //             'students' => $students,
    //             'teacher' => $paymentData['teacher'],
    //             'yearMonth' => $yearMonth,
    //             'month' => $formattedMonth,  // ✅ දැන් හරියටම define කරලා
    //             'totalAmount' => $totalAmount,
    //             'totalStudents' => $totalStudents
    //         ];

    //         // Generate PDF
    //         $pdf = Pdf::loadView('emails.payment_report', $pdfViewData)
    //             ->setPaper('A4', 'portrait');

    //         $fileName = "payment-report-{$teacherId}-{$fileNameMonth}.pdf";

    //         // Check teacher email
    //         $teacherEmail = $paymentData['teacher']['email'] ?? null;
    //         if (!$teacherEmail) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Teacher email not found'
    //             ], 400);
    //         }

    //         // Send email
    //         Mail::to($teacherEmail)->send(new PaymentReportMail(
    //             $paymentData,
    //             $pdf->output(),
    //             $fileName,
    //             $teacherId,
    //             $formattedMonth
    //         ));

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Payment report sent successfully',
    //             'file_name' => $fileName,
    //             'month' => $formattedMonth,
    //             'total_students' => $totalStudents,
    //             'total_amount' => $totalAmount
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function downloadPaymentReport($teacherId, $yearMonth)
    {
        try {
            if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date format. Use YYYY-MM'
                ], 400);
            }

            $paymentData = $this->teacherPaymentsService->studentPaymentMonthFlat($teacherId, $yearMonth);

            if (!($paymentData['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => $paymentData['message'] ?? 'Failed to get payment data'
                ], 400);
            }

            $students = $paymentData['students'] ?? [];
            $classes = $paymentData['classes'] ?? [];
            $totals = $paymentData['totals'] ?? [];

            $totalAmount = round((float) ($totals['total_paid_amount'] ?? 0), 2);
            $totalTeacherCut = round((float) ($totals['total_teacher_cut'] ?? 0), 2);
            $totalOrganizeCut = round((float) ($totals['total_organize_cut'] ?? 0), 2);
            $totalInstituteCut = round((float) ($totals['total_institute_cut'] ?? 0), 2);

            $totalStudents = collect($students)
                ->pluck('student_id')
                ->filter()
                ->unique()
                ->count();

            $formattedMonth = date('F Y', strtotime($yearMonth . '-01'));
            $fileNameMonth = date('F-Y', strtotime($yearMonth . '-01'));

            $teacherName = $paymentData['teacher']['name'] ?? 'teacher';
            $cleanTeacherName = preg_replace('/[^A-Za-z0-9]/', '_', $teacherName);
            $cleanTeacherName = preg_replace('/_+/', '_', $cleanTeacherName);
            $cleanTeacherName = trim($cleanTeacherName, '_');

            if (empty($cleanTeacherName)) {
                $cleanTeacherName = 'teacher';
            }

            $pdfViewData = [
                'students' => $students,
                'teacher' => $paymentData['teacher'] ?? [],
                'yearMonth' => $yearMonth,
                'month' => $formattedMonth,
                'totalAmount' => $totalAmount,
                'totalStudents' => $totalStudents,
                'totalTeacherCut' => $totalTeacherCut,
                'totalOrganizeCut' => $totalOrganizeCut,
                'totalInstituteCut' => $totalInstituteCut,
                'classData' => $classes,
            ];

            $pdf = Pdf::loadView('emails.payment_report', $pdfViewData)
                ->setPaper('A4', 'portrait');

            $fileName = "payment_report_{$cleanTeacherName}_{$fileNameMonth}.pdf";

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download payment report.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
