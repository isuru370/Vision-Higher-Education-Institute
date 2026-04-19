<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Institute Monthly Report</title>
    <style>
        @page {
            margin: 18px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
            margin: 0;
            padding: 0;
            line-height: 1.35;
        }

        .report-header {
            width: 100%;
            margin-bottom: 14px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 8px;
        }

        .report-header table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-header td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1f2d3d;
            margin-bottom: 2px;
        }

        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #34495e;
            margin-top: 3px;
        }

        .report-meta {
            text-align: right;
            font-size: 9px;
            color: #555;
        }

        .section-title {
            background: #2c3e50;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            padding: 6px 8px;
            margin: 14px 0 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th, td {
            border: 1px solid #bbb;
            padding: 5px 6px;
            vertical-align: middle;
        }

        th {
            background: #ecf0f1;
            font-weight: bold;
            font-size: 9.5px;
        }

        td {
            font-size: 9.5px;
        }

        .summary-table th {
            width: 65%;
            text-align: left;
        }

        .summary-table td {
            width: 35%;
            text-align: right;
            font-weight: bold;
        }

        .teacher-name {
            font-size: 11px;
            font-weight: bold;
            color: #1f2d3d;
            margin: 10px 0 6px 0;
            padding: 4px 0;
            border-bottom: 1px solid #dcdcdc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #666;
        }

        .highlight-row td {
            font-weight: bold;
            background: #f8f9fa;
        }

        .footer-note {
            margin-top: 10px;
            font-size: 8.5px;
            text-align: center;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 6px;
        }

        .teacher-block {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .compact-table th,
        .compact-table td {
            padding: 4px 5px;
        }
    </style>
</head>
<body>

    <div class="report-header">
        <table>
            <tr>
                <td>
                    <div class="company-name">Vision Higher Education Institute</div>
                    <div class="report-title">Institute Monthly Report</div>
                </td>
                <td class="report-meta">
                    <div><strong>Period:</strong> {{ $report['year_month'] ?? ($report['start_date'] ?? '-') }}</div>
                    <div><strong>Generated Date:</strong> {{ date('Y-m-d') }}</div>
                    <div><strong>Report Type:</strong> Monthly</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Summary</div>
    <table class="summary-table compact-table">
        <tr>
            <th>Total Teacher Payments</th>
            <td>{{ number_format($report['summary']['total_teacher_payments'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Total Teacher Earnings</th>
            <td>{{ number_format($report['summary']['total_teacher_earnings'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Total Teacher Advances</th>
            <td>{{ number_format($report['summary']['total_teacher_advances'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Total Teacher Salaries</th>
            <td>{{ number_format($report['summary']['total_teacher_salaries'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Total Teacher Net Earnings</th>
            <td>{{ number_format($report['summary']['total_teacher_net_earnings'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Total Organizer Income</th>
            <td>{{ number_format($report['summary']['total_organizer_income'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Total Institute From Classes</th>
            <td>{{ number_format($report['summary']['total_institute_from_classes'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Admission Payments</th>
            <td>{{ number_format($report['summary']['admission_payments'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Extra Income</th>
            <td>{{ number_format($report['summary']['extra_income_for_period'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Total Institute Expense</th>
            <td>{{ number_format($report['summary']['total_institute_expense'] ?? 0, 2) }}</td>
        </tr>
        <tr class="highlight-row">
            <th>Institute Gross Income</th>
            <td>{{ number_format($report['summary']['institute_gross_income'] ?? 0, 2) }}</td>
        </tr>
        <tr class="highlight-row">
            <th>Institute Net Income</th>
            <td>{{ number_format($report['summary']['institute_net_income'] ?? 0, 2) }}</td>
        </tr>
    </table>

    <div class="section-title">Teacher Details</div>

    @forelse($report['data'] ?? [] as $teacher)
        <div class="teacher-block">
            <div class="teacher-name">
                {{ $teacher['teacher_name'] ?? '-' }}
            </div>

            <table class="compact-table">
                <tr>
                    <th>Total Payments</th>
                    <th>Teacher Earning</th>
                    <th>Advance</th>
                    <th>Salary</th>
                    <th>Net Earning</th>
                    <th>Organizer Income</th>
                    <th>Institute Income</th>
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

            <table class="compact-table">
                <thead>
                    <tr>
                        <th style="width: 28%;">Class Name</th>
                        <th style="width: 14%;" class="text-right">Total Amount</th>
                        <th style="width: 10%;" class="text-center">Teacher %</th>
                        <th style="width: 16%;" class="text-right">Teacher Earning</th>
                        <th style="width: 16%;" class="text-right">Organizer Income</th>
                        <th style="width: 16%;" class="text-right">Institute Income</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teacher['class_wise_totals'] ?? [] as $class)
                        <tr>
                            <td>{{ $class['class_name'] ?? '-' }}</td>
                            <td class="text-right">{{ number_format($class['total_amount'] ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($class['teacher_percentage'] ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($class['teacher_earning'] ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($class['organizer_income'] ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($class['institute_income'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center muted">No class-wise data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @empty
        <table>
            <tr>
                <td class="text-center muted">No teacher data available for this period.</td>
            </tr>
        </table>
    @endforelse

    <div class="footer-note">
        This is a system generated report.
    </div>

</body>
</html>