<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Daily Teacher Payments Report</title>
    <style>
        @page {
            margin: 14px 16px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8.5px;
            color: #111;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.2px;
        }

        .header p {
            margin: 2px 0 0 0;
            font-size: 8px;
            color: #555;
        }

        .section-title {
            margin-top: 10px;
            margin-bottom: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #aaa;
            padding-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 4px 5px;
            vertical-align: middle;
        }

        th {
            background: #f5f5f5;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
        }

        td {
            font-size: 8px;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .highlight {
            font-weight: bold;
        }

        .footer-note {
            margin-top: 12px;
            font-size: 7px;
            color: #666;
            text-align: right;
        }

        .muted {
            color: #666;
            font-size: 7px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Daily Teacher Payments Report</h2>
        <p>Selected Date: {{ $selected_date ?? date('Y-m-d') }}</p>
    </div>

    <div class="section-title">Teacher Summary</div>
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 20%;">Teacher Name</th>
                <th style="width: 8%;" class="text-end">Count</th>
                <th style="width: 12%;" class="text-end">Total Paid</th>
                <th style="width: 12%;" class="text-end">Teacher Earn</th>
                <th style="width: 12%;" class="text-end">Org. Cut</th>
                <th style="width: 12%;" class="text-end">Advance</th>
                <th style="width: 12%;" class="text-end">Net Payable</th>
                <th style="width: 12%;" class="text-end">Daily Salary</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandPaymentCount = 0;
                $grandTotalPaid = 0;
                $grandTeacherEarn = 0;
                $grandOrganizeCut = 0;
                $grandAdvance = 0;
                $grandNetPayable = 0;
                $grandDailySalary = 0;
            @endphp

            @forelse($data as $index => $row)
                @php
                    $summary = $row['summary'] ?? [];

                    $paymentCount = (int) ($summary['payment_count'] ?? 0);
                    $totalPaid = (float) ($summary['total_payment_amount'] ?? 0);
                    $teacherEarn = (float) ($summary['gross_teacher_earning'] ?? 0);
                    $organizeCut = (float) ($summary['total_organize_cut'] ?? 0);
                    $advance = (float) ($summary['advance_deducted_this_day'] ?? 0);
                    $netPayable = (float) ($summary['net_teacher_payable'] ?? 0);
                    $dailySalary = (float) ($summary['daily_salary'] ?? 0);

                    $grandPaymentCount += $paymentCount;
                    $grandTotalPaid += $totalPaid;
                    $grandTeacherEarn += $teacherEarn;
                    $grandOrganizeCut += $organizeCut;
                    $grandAdvance += $advance;
                    $grandNetPayable += $netPayable;
                    $grandDailySalary += $dailySalary;
                @endphp

                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['teacher_name'] ?? '-' }}</td>
                    <td class="text-end">{{ $paymentCount }}</td>
                    <td class="text-end">{{ number_format($totalPaid, 2) }}</td>
                    <td class="text-end">{{ number_format($teacherEarn, 2) }}</td>
                    <td class="text-end">{{ number_format($organizeCut, 2) }}</td>
                    <td class="text-end">{{ number_format($advance, 2) }}</td>
                    <td class="text-end highlight">{{ number_format($netPayable, 2) }}</td>
                    <td class="text-end highlight">{{ number_format($dailySalary, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No data found</td>
                </tr>
            @endforelse

            @if(!empty($data) && count($data) > 0)
                <tr>
                    <td colspan="2" class="highlight text-end">Grand Total</td>
                    <td class="text-end highlight">{{ $grandPaymentCount }}</td>
                    <td class="text-end highlight">{{ number_format($grandTotalPaid, 2) }}</td>
                    <td class="text-end highlight">{{ number_format($grandTeacherEarn, 2) }}</td>
                    <td class="text-end highlight">{{ number_format($grandOrganizeCut, 2) }}</td>
                    <td class="text-end highlight">{{ number_format($grandAdvance, 2) }}</td>
                    <td class="text-end highlight">{{ number_format($grandNetPayable, 2) }}</td>
                    <td class="text-end highlight">{{ number_format($grandDailySalary, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="section-title">Fee Type Counts by Teacher</div>
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 24%;">Teacher Name</th>
                <th style="width: 12%;" class="text-end">Free</th>
                <th style="width: 12%;" class="text-end">Half</th>
                <th style="width: 12%;" class="text-end">Custom</th>
                <th style="width: 12%;" class="text-end">Discounted</th>
                <th style="width: 12%;" class="text-end">Default</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                @php
                    $summary = $row['summary'] ?? [];
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['teacher_name'] ?? '-' }}</td>
                    <td class="text-end">{{ $summary['free_card_count'] ?? 0 }}</td>
                    <td class="text-end">{{ $summary['half_card_count'] ?? 0 }}</td>
                    <td class="text-end">{{ $summary['custom_fee_count'] ?? 0 }}</td>
                    <td class="text-end">{{ $summary['discounted_count'] ?? 0 }}</td>
                    <td class="text-end">{{ $summary['default_fee_count'] ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No count data found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        Generated on {{ date('Y-m-d H:i') }}
    </div>

</body>

</html>