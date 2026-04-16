<!DOCTYPE html>
<html>

<head>
    <title>Student Payment Report - {{ $month }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .teacher-info,
        .summary-box {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
        }

        .summary-box {
            background-color: #f8f8f8;
        }

        .section-title {
            margin-top: 20px;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #222;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .amount {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .free-card {
            color: orange;
            font-weight: bold;
        }

        .paid {
            color: green;
            font-weight: bold;
        }

        .unpaid {
            color: red;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2 style="margin: 0;">STUDENT PAYMENT REPORT</h2>
        <h3 style="margin: 5px 0;">Month: {{ $month }}</h3>
    </div>

    <div class="teacher-info">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 3px;">
                    <strong>Teacher:</strong> {{ $teacher['name'] ?? 'Unknown' }}
                </td>
                <td style="border: none; padding: 3px;">
                    <strong>Teacher ID:</strong> {{ $teacher['id'] ?? 'N/A' }}
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 3px;">
                    <strong>Email:</strong> {{ $teacher['email'] ?? 'N/A' }}
                </td>
                <td style="border: none; padding: 3px;">
                    <strong>Report Month:</strong> {{ $month ?? 'N/A' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <h4 style="margin: 0 0 10px 0;">FINANCIAL SUMMARY</h4>
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 4px;">
                    <strong>Total Students:</strong> {{ $totalStudents ?? 0 }}
                </td>
                <td style="border: none; padding: 4px;">
                    <strong>Total Payment Rows:</strong> {{ count($students ?? []) }}
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 4px;">
                    <strong>Total Collected:</strong>
                </td>
                <td style="border: none; padding: 4px; text-align: right;">
                    <strong>Rs. {{ number_format($totalAmount ?? 0, 2) }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">CLASS SUMMARY</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Class Name</th>
                <th class="amount">Total Students</th>
                <th class="amount">Paid</th>
                <th class="amount">Unpaid</th>
                <th class="amount">Free</th>
                <th class="amount">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classData as $index => $class)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $class['class_name'] ?? '-' }}</td>
                    <td class="amount">{{ $class['total_students'] ?? 0 }}</td>
                    <td class="amount">{{ $class['paid_students'] ?? 0 }}</td>
                    <td class="amount">{{ $class['unpaid_students'] ?? 0 }}</td>
                    <td class="amount">{{ $class['free_students'] ?? 0 }}</td>
                    <td class="amount">{{ number_format($class['paid_amount_total'] ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No class data found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">STUDENT PAYMENT DETAILS</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Class Name</th>
                <th>Payment Date</th>
                <th>Payment For</th>
                <th class="amount">Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student['custom_id'] ?? 'N/A' }}</td>
                    <td>{{ $student['student_name'] ?? 'Unknown' }}</td>
                    <td>{{ $student['class_name'] ?? '-' }}</td>
                    <td>
                        @if(!empty($student['date']))
                            {{ date('Y-m-d', strtotime($student['date'])) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $student['payment_for'] ?? 'N/A' }}</td>
                    <td class="amount">{{ number_format($student['amount'] ?? 0, 2) }}</td>
                    <td>
                        @if(($student['payment_status'] ?? '') === 'free')
                            <span class="free-card">Free Card</span>
                        @elseif(($student['payment_status'] ?? '') === 'paid')
                            <span class="paid">Paid</span>
                        @else
                            <span class="unpaid">Unpaid</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No payment data available for this month.</td>
                </tr>
            @endforelse

            @if(count($students ?? []) > 0)
                <tr style="font-weight: bold; background-color: #f0f0f0;">
                    <td colspan="6" style="text-align: right;">Total:</td>
                    <td class="amount">{{ number_format($totalAmount ?? 0, 2) }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Generated on:</strong> {{ date('Y-m-d H:i:s') }}</p>
        <p><strong>Report ID:</strong> PAY-{{ date('Ymd') }}-{{ $teacher['id'] ?? 'N/A' }}</p>
        <p><strong>Report Period:</strong> {{ $month }}</p>
    </div>

</body>

</html>