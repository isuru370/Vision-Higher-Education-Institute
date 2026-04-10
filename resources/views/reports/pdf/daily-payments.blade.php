<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Teacher Payments</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0 0 5px 0;
        }

        .header p {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table, th, td {
            border: 1px solid #222;
        }

        th, td {
            padding: 8px;
        }

        th {
            background: #f2f2f2;
            text-align: left;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Daily Teacher Payments Report</h2>
        <p>Month: {{ $year_month ?? date('Y-m') }}</p>
        <p>Days in Month: {{ $days_in_month ?? 0 }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Teacher Name</th>
                <th class="text-end">Payment Count</th>
                <th class="text-end">Total Payment Amount</th>
                <th class="text-end">Gross Teacher Earning</th>
                <th class="text-end">Total Organize Cut</th>
                <th class="text-end">Advance Deducted</th>
                <th class="text-end">Net Teacher Payable</th>
                <th class="text-end">Daily Salary</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['teacher_name'] ?? '-' }}</td>
                    <td class="text-end">{{ $row['payment_count'] ?? 0 }}</td>
                    <td class="text-end">{{ number_format($row['total_payment_amount'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['gross_teacher_earning'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['total_organize_cut'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['advance_deducted_this_month'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['net_teacher_payable'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['daily_salary'] ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No data found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>