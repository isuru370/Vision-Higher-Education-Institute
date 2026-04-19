<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function indexPage()
    {
        return view('reports.index');
    }

    public function downloadDailyReportPdf($day)
    {
        $report = $this->reportService->generateAllDailyPaymentsReport($day);

        $pdf = Pdf::loadView('reports.daily_pdf', compact('report'));

        return $pdf->download('daily-report-' . $day . '.pdf');
    }

    // =========================
    // INSTITUTE REPORTS
    // =========================

    public function getInstituteMonthlyReport($yearMonth)
    {
        return $this->reportService->fetchInstitutePaymentByMonth($yearMonth);
    }

    public function getInstituteDailyReport($date)
    {
        return $this->reportService->fetchInstitutePaymentByDate($date);
    }

    public function getInstituteDateRangeReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        return $this->reportService->fetchInstitutePaymentByDateRange(
            $request->start_date,
            $request->end_date
        );
    }

    // =========================
    // OPTIONAL PDF DOWNLOADS
    // =========================

    public function downloadInstituteMonthlyReportPdf($yearMonth)
    {
        $response = $this->reportService->fetchInstitutePaymentByMonth($yearMonth);
        $report = $response->getData(true);

        $pdf = Pdf::loadView('reports.institute_monthly_pdf', compact('report'));

        return $pdf->download('institute-monthly-report-' . $yearMonth . '.pdf');
    }

    public function downloadInstituteDailyReportPdf($date)
    {
        $response = $this->reportService->fetchInstitutePaymentByDate($date);
        $report = $response->getData(true);

        $pdf = Pdf::loadView('reports.institute_daily_pdf', compact('report'));

        return $pdf->download('institute-daily-report-' . $date . '.pdf');
    }

    public function downloadInstituteDateRangeReportPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $response = $this->reportService->fetchInstitutePaymentByDateRange(
            $request->start_date,
            $request->end_date
        );

        $report = $response->getData(true);

        $pdf = Pdf::loadView('reports.institute_range_pdf', compact('report'));

        return $pdf->download(
            'institute-range-report-' . $request->start_date . '-to-' . $request->end_date . '.pdf'
        );
    }
}
