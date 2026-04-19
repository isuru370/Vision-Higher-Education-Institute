<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Institute Daily Report</title>

    <style>
        @page {
            margin: 18px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header table {
            width: 100%;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2d3d;
        }

        .subtitle {
            font-size: 12px;
            font-weight: bold;
            color: #34495e;
        }

        .meta {
            text-align: right;
            font-size: 9px;
            color: #555;
        }

        .section-title {
            background: #2c3e50;
            color: #fff;
            padding: 5px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th, td {
            border: 1px solid #bbb;
            padding: 4px;
        }

        th {
            background: #ecf0f1;
            font-size: 9px;
        }

        td {
            font-size: 9px;
        }

        .text-right {
            text-align: right;
        }

        .teacher-name {
            font-size: 11px;
            font-weight: bold;
            margin-top: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }

        .highlight {
            font-weight: bold;
            background: #f5f5f5;
        }

        .footer {
            margin-top: 10px;
            font-size: 8px;
            text-align: center;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="title">Vision Higher Education Institute</div>
                    <div class="subtitle">Institute Daily Report</div>
                </td>
                <td class="meta">
                    <div><strong>Date:</strong> {{ $report['start_date'] ?? '-' }}</div>
                    <div><strong>Generated:</strong> {{ date('Y-m-d') }}</div>
                    <div><strong>Type:</strong> Daily</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- SUMMARY --}}
    <div class="section-title">Summary</div>
    <table>
        <tr><th>Total Teacher Payments</th><td class="text-right">{{ number_format($report['summary']['total_teacher_payments'] ?? 0, 2) }}</td></tr>
        <tr><th>Total Teacher Earnings</th><td class="text-right">{{ number_format($report['summary']['total_teacher_earnings'] ?? 0, 2) }}</td></tr>
        <tr><th>Total Teacher Advances</th><td class="text-right">{{ number_format($report['summary']['total_teacher_advances'] ?? 0, 2) }}</td></tr>
        <tr><th>Total Teacher Salaries</th><td class="text-right">{{ number_format($report['summary']['total_teacher_salaries'] ?? 0, 2) }}</td></tr>
        <tr><th>Total Teacher Net Earnings</th><td class="text-right">{{ number_format($report['summary']['total_teacher_net_earnings'] ?? 0, 2) }}</td></tr>
        <tr><th>Total Organizer Income</th><td class="text-right">{{ number_format($report['summary']['total_organizer_income'] ?? 0, 2) }}</td></tr>
        <tr><th>Total Institute From Classes</th><td class="text-right">{{ number_format($report['summary']['total_institute_from_classes'] ?? 0, 2) }}</td></tr>
        <tr><th>Admission Payments</th><td class="text-right">{{ number_format($report['summary']['admission_payments'] ?? 0, 2) }}</td></tr>
        <tr><th>Extra Income</th><td class="text-right">{{ number_format($report['summary']['extra_income_for_period'] ?? 0, 2) }}</td></tr>
        <tr><th>Total Institute Expense</th><td class="text-right">{{ number_format($report['summary']['total_institute_expense'] ?? 0, 2) }}</td></tr>
        <tr class="highlight"><th>Institute Gross Income</th><td class="text-right">{{ number_format($report['summary']['institute_gross_income'] ?? 0, 2) }}</td></tr>
        <tr class="highlight"><th>Institute Net Income</th><td class="text-right">{{ number_format($report['summary']['institute_net_income'] ?? 0, 2) }}</td></tr>
    </table>

    {{-- TEACHERS --}}
    <div class="section-title">Teacher Details</div>

    @foreach($report['data'] ?? [] as $teacher)

        <div class="teacher-name">
            {{ $teacher['teacher_name'] ?? '-' }}
        </div>

        <table>
            <tr>
                <th>Total Payments</th>
                <th>Teacher Earning</th>
                <th>Advance</th>
                <th>Salary</th>
                <th>Net</th>
                <th>Organizer</th>
                <th>Institute</th>
            </tr>
            <tr>
                <td class="text-right">{{ number_format($teacher['total_payments_for_period'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($teacher['teacher_total_earning'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($teacher['teacher_advance'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($teacher['teacher_salary'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($teacher['teacher_net_earning'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($teacher['organizer_total_income'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($teacher['institution_total_income'] ?? 0, 2) }}</td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Total</th>
                    <th>%</th>
                    <th>Teacher</th>
                    <th>Organizer</th>
                    <th>Institute</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teacher['class_wise_totals'] ?? [] as $class)
                    <tr>
                        <td>{{ $class['class_name'] ?? '-' }}</td>
                        <td class="text-right">{{ number_format($class['total_amount'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($class['teacher_percentage'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($class['teacher_earning'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($class['organizer_income'] ?? 0, 2) }}</td>
                        <td class="text-right">{{ number_format($class['institute_income'] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endforeach

    {{-- FOOTER --}}
    <div class="footer">
        This is a system generated report - Vision Higher Education Institute
    </div>

</body>
</html>