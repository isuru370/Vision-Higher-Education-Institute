<!DOCTYPE html>
<html>
<head>
    <title>Institute Date Range Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }
        h2, h4 {
            margin-bottom: 5px;
        }
        .summary, .teacher-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .summary th, .summary td,
        .teacher-table th, .teacher-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .teacher-section {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

    <h2>Institute Date Range Report</h2>
    <h4>From: {{ $report['start_date'] ?? '-' }} | To: {{ $report['end_date'] ?? '-' }}</h4>

    <h3>Summary</h3>
    <table class="summary">
        <tr><th>Total Teacher Payments</th><td>{{ $report['summary']['total_teacher_payments'] ?? 0 }}</td></tr>
        <tr><th>Total Teacher Earnings</th><td>{{ $report['summary']['total_teacher_earnings'] ?? 0 }}</td></tr>
        <tr><th>Total Teacher Advances</th><td>{{ $report['summary']['total_teacher_advances'] ?? 0 }}</td></tr>
        <tr><th>Total Teacher Salaries</th><td>{{ $report['summary']['total_teacher_salaries'] ?? 0 }}</td></tr>
        <tr><th>Total Teacher Net Earnings</th><td>{{ $report['summary']['total_teacher_net_earnings'] ?? 0 }}</td></tr>
        <tr><th>Total Organizer Income</th><td>{{ $report['summary']['total_organizer_income'] ?? 0 }}</td></tr>
        <tr><th>Total Institute From Classes</th><td>{{ $report['summary']['total_institute_from_classes'] ?? 0 }}</td></tr>
        <tr><th>Admission Payments</th><td>{{ $report['summary']['admission_payments'] ?? 0 }}</td></tr>
        <tr><th>Extra Income</th><td>{{ $report['summary']['extra_income_for_period'] ?? 0 }}</td></tr>
        <tr><th>Total Institute Expense</th><td>{{ $report['summary']['total_institute_expense'] ?? 0 }}</td></tr>
        <tr><th>Institute Gross Income</th><td>{{ $report['summary']['institute_gross_income'] ?? 0 }}</td></tr>
        <tr><th>Institute Net Income</th><td>{{ $report['summary']['institute_net_income'] ?? 0 }}</td></tr>
    </table>

    <h3>Teacher Details</h3>
    @foreach($report['data'] ?? [] as $teacher)
        <div class="teacher-section">
            <h4>{{ $teacher['teacher_name'] ?? '-' }}</h4>

            <table class="teacher-table">
                <tr><th>Total Payments</th><td>{{ $teacher['total_payments_for_period'] ?? 0 }}</td></tr>
                <tr><th>Teacher Earning</th><td>{{ $teacher['teacher_total_earning'] ?? 0 }}</td></tr>
                <tr><th>Teacher Advance</th><td>{{ $teacher['teacher_advance'] ?? 0 }}</td></tr>
                <tr><th>Teacher Salary</th><td>{{ $teacher['teacher_salary'] ?? 0 }}</td></tr>
                <tr><th>Teacher Net Earning</th><td>{{ $teacher['teacher_net_earning'] ?? 0 }}</td></tr>
                <tr><th>Organizer Income</th><td>{{ $teacher['organizer_total_income'] ?? 0 }}</td></tr>
                <tr><th>Institute Income</th><td>{{ $teacher['institution_total_income'] ?? 0 }}</td></tr>
            </table>

            <h5>Class Wise Totals</h5>
            <table class="teacher-table">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Total Amount</th>
                        <th>Teacher %</th>
                        <th>Teacher Earning</th>
                        <th>Organizer Income</th>
                        <th>Institute Income</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teacher['class_wise_totals'] ?? [] as $class)
                        <tr>
                            <td>{{ $class['class_name'] ?? '-' }}</td>
                            <td>{{ $class['total_amount'] ?? 0 }}</td>
                            <td>{{ $class['teacher_percentage'] ?? 0 }}</td>
                            <td>{{ $class['teacher_earning'] ?? 0 }}</td>
                            <td>{{ $class['organizer_income'] ?? 0 }}</td>
                            <td>{{ $class['institute_income'] ?? 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

</body>
</html>