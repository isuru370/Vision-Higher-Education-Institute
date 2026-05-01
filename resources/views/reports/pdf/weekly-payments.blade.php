<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payments Report</title>

    <style>
        @page {
            margin: 14px 16px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8.5px;
            color: #111;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 13px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 8px;
            color: #555;
        }

        .section-title {
            margin-top: 10px;
            font-size: 9px;
            font-weight: bold;
            border-bottom: 1px solid #aaa;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 4px;
        }

        th {
            background: #f5f5f5;
            font-size: 8px;
        }

        td {
            font-size: 8px;
        }

        .text-end {
            text-align: right;
        }

        .highlight {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Teacher Payments Report</h2>
        <p>From: {{ $from_date }}</p>
        <p>To: {{ $to_date }}</p>
    </div>

    <div class="section-title">Teacher Summary</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Teacher Name</th>
                <th class="text-end">Payments</th>
                <th class="text-end">Total Paid</th>
                <th class="text-end">Teacher Earn</th>
                <th class="text-end">Org Cut</th>
                <th class="text-end">Net Payable</th>
                <th class="text-end">Weekly Salary</th>
            </tr>
        </thead>
        <tbody>

            @php
                $grandTotal = 0;
                $grandTeacher = 0;
                $grandOrg = 0;
                $grandNet = 0;
            @endphp

            @forelse($data as $index => $row)
                @php
                    $summary = $row['summary'] ?? [];

                    $count = $summary['payment_count'] ?? 0;
                    $total = $summary['total_payment_amount'] ?? 0;
                    $teacher = $summary['gross_teacher_earning'] ?? 0;
                    $org = $summary['total_organize_cut'] ?? 0;
                    $net = $summary['net_teacher_payable'] ?? 0;
                    $weekly = $summary['weekly_salary'] ?? 0;

                    $grandTotal += $total;
                    $grandTeacher += $teacher;
                    $grandOrg += $org;
                    $grandNet += $net;
                @endphp

                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['teacher_name'] }}</td>
                    <td class="text-end">{{ $count }}</td>
                    <td class="text-end">{{ number_format($total, 2) }}</td>
                    <td class="text-end">{{ number_format($teacher, 2) }}</td>
                    <td class="text-end">{{ number_format($org, 2) }}</td>
                    <td class="text-end highlight">{{ number_format($net, 2) }}</td>
                    <td class="text-end highlight">{{ number_format($weekly, 2) }}</td>
                </tr>

            @empty
                <tr>
                    <td colspan="8" class="text-center">No data found</td>
                </tr>
            @endforelse

            @if(count($data))
                <tr>
                    <td colspan="3" class="text-end highlight">Grand Total</td>
                    <td class="text-end highlight">{{ number_format($grandTotal, 2) }}</td>
                    <td class="text-end highlight">{{ number_format($grandTeacher, 2) }}</td>
                    <td class="text-end highlight">{{ number_format($grandOrg, 2) }}</td>
                    <td class="text-end highlight">{{ number_format($grandNet, 2) }}</td>
                    <td></td>
                </tr>
            @endif

        </tbody>
    </table>

    <div style="margin-top:10px; font-size:7px; text-align:right;">
        Generated on {{ date('Y-m-d H:i') }}
    </div>

</body>

</html>