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