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

        .class-title {
            margin-top: 18px;
            font-size: 14px;
            font-weight: bold;
        }

        .grade-title {
            margin-top: 3px;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: bold;
        }

        /* Signature Section - Single Row */
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
        <h2>Daily Teacher Payment Report</h2>
        <h4>{{ $data['teacher_name'] ?? '-' }}</h4>
        <p>Date: {{ $date ?? '-' }}</p>
    </div>

    <!-- Summary Table -->
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
            <td class="text-end">{{ number_format($data['advance_deducted_for_day'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Net Teacher Payable</th>
            <td class="text-end">{{ number_format($data['net_teacher_payable'] ?? 0, 2) }}</td>
        </tr>
    </table>

    <div class="section-title">Class and Category Breakdown</div>

    @forelse(($data['class_category_breakdown'] ?? []) as $classItem)

        <div class="class-title">
            {{ $classItem['class_name'] ?? '-' }}
        </div>

        <div class="grade-title">
            {{ $classItem['grade_name'] ?? '-' }}
        </div>

        <table class="breakdown-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category Name</th>
                    <th class="text-end">Teacher %</th>
                    <th class="text-end">Organize %</th>
                    <th class="text-end">Payment Count</th>
                    <th class="text-end">Total Amount</th>
                    <th class="text-end">Teacher Cut</th>
                    <th class="text-end">Organize Cut</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($classItem['categories'] ?? []) as $index => $category)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $category['category_name'] ?? '-' }}</td>
                        <td class="text-end">{{ number_format($classItem['teacher_percentage'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($classItem['organize_percentage'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ $category['payment_count'] ?? 0 }}</td>
                        <td class="text-end">{{ number_format($category['total_amount'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($category['teacher_cut'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($category['organize_cut'] ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No category data found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @empty
        <table class="breakdown-table">
            <tr>
                <td class="text-center">No class/category breakdown found</td>
            </tr>
        </table>
    @endforelse

    <!-- Signature Section - Single Row -->
    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-box">
                <div class="signature-title">INSTITUTE AUTHORITY</div>
                <div class="signature-line"></div>
                <div class="signature-label">(Authorized Signature)</div>
                <div class="date-line">Date: {{ $date ?? date('Y-m-d') }}</div>
            </div>
        </div>
    </div>

</body>
</html>