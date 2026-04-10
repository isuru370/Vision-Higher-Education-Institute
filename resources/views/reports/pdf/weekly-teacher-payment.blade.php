<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Weekly Teacher Payment</title>
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

        .header h2,
        .header h4,
        .header p {
            margin: 0 0 5px 0;
        }

        .summary-table,
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .summary-table,
        .summary-table th,
        .summary-table td,
        .breakdown-table,
        .breakdown-table th,
        .breakdown-table td {
            border: 1px solid #222;
        }

        th,
        td {
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

        .section-title {
            margin-top: 20px;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 50px;
            padding-top: 20px;
        }

        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            margin-top: 30px;
            padding-top: 5px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .signature-label {
            margin-top: 5px;
            font-size: 11px;
            color: #555;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .date-line {
            margin-top: 10px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Weekly Teacher Payment Report</h2>
        <h4>{{ $data['teacher_name'] ?? '-' }}</h4>
        <p>From: {{ $start_date ?? '-' }} | To: {{ $end_date ?? '-' }}</p>
    </div>

    <table class="summary-table">
        <tr>
            <th>Teacher ID</th>
            <td>{{ $data['teacher_id'] ?? '-' }}</td>
        </tr>
        <tr>
            <th>Payment Count</th>
            <td class="text-end">{{ $data['payment_count'] ?? 0 }}</td>
        </tr>
        <tr>
            <th>Total Payment Amount</th>
            <td class="text-end">{{ number_format($data['total_payment_amount'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Gross Teacher Earning</th>
            <td class="text-end">{{ number_format($data['gross_teacher_earning'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Total Organize Cut</th>
            <td class="text-end">{{ number_format($data['total_organize_cut'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Advance Deducted</th>
            <td class="text-end">{{ number_format($data['advance_deducted_for_week'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Net Teacher Payable</th>
            <td class="text-end">{{ number_format($data['net_teacher_payable'] ?? 0, 2) }}</td>
        </tr>
    </table>

    <div class="section-title">Class Wise Breakdown</div>

    <table class="breakdown-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Class Name</th>
                <th class="text-end">Teacher %</th>
                <th class="text-end">Organize %</th>
                <th class="text-end">Payment Count</th>
                <th class="text-end">Total Amount</th>
                <th class="text-end">Teacher Cut</th>
                <th class="text-end">Organize Cut</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($data['class_wise_breakdown'] ?? []) as $index => $class)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $class['class_name'] ?? '-' }}</td>
                    <td class="text-end">{{ number_format($class['teacher_percentage'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($class['organize_percentage'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ $class['payment_count'] ?? 0 }}</td>
                    <td class="text-end">{{ number_format($class['total_amount'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($class['teacher_cut'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($class['organize_cut'] ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No class breakdown found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-box">
                <div class="signature-title">INSTITUTE AUTHORITY</div>
                <div class="signature-line"></div>
                <div class="signature-label">(Authorized Signature)</div>
                <div class="date-line">Date: {{ date('Y-m-d') }}</div>
            </div>
        </div>
    </div>

</body>
</html>