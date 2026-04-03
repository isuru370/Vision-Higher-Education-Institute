<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Teacher Payment</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2, .header h4, .header p {
            margin: 0 0 5px 0;
        }
        .summary-table,
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .summary-table, .summary-table th, .summary-table td,
        .breakdown-table, .breakdown-table th, .breakdown-table td {
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
        .section-title {
            margin-top: 20px;
            margin-bottom: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Daily Teacher Payment Report</h2>
        <h4>{{ $data['teacher_name'] ?? '-' }}</h4>
        <p>Date: {{ $date ?? '-' }}</p>
    </div>

    <table class="summary-table">
        <tr>
            <th>Teacher ID</th>
            <td>{{ $data['teacher_id'] ?? '-' }}</td>
        </tr>
        <tr>
            <th>Total Payments For Day</th>
            <td class="text-end">{{ number_format($data['total_payments_for_day'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Gross Teacher Earning</th>
            <td class="text-end">{{ number_format($data['gross_teacher_earning'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Advance Deducted</th>
            <td class="text-end">{{ number_format($data['advance_deducted_for_day'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Net Teacher Payable</th>
            <td class="text-end">{{ number_format($data['net_teacher_payable'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Institution Income</th>
            <td class="text-end">{{ number_format($data['institution_income'] ?? 0, 2) }}</td>
        </tr>
    </table>

    <div class="section-title">Class Wise Breakdown</div>

    <table class="breakdown-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Class Name</th>
                <th class="text-end">Teacher %</th>
                <th class="text-end">Total Amount</th>
                <th class="text-end">Teacher Cut</th>
                <th class="text-end">Institution Cut</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($data['class_wise_breakdown'] ?? []) as $index => $class)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $class['class_name'] ?? '-' }}</td>
                    <td class="text-end">{{ number_format($class['teacher_percentage'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($class['total_amount'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($class['teacher_cut'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($class['institution_cut'] ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No class breakdown found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>