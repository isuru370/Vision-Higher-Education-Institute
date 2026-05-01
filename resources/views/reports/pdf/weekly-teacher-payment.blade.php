<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Teacher Payment Report</title>
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

        .header h4 {
            margin: 2px 0 0 0;
            font-size: 10px;
            font-weight: bold;
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
        }

        .summary-table,
        .count-table,
        .breakdown-table {
            margin-top: 4px;
        }

        .summary-table th,
        .summary-table td,
        .count-table th,
        .count-table td,
        .breakdown-table th,
        .breakdown-table td {
            border: 1px solid #999;
            padding: 4px 5px;
            vertical-align: top;
        }

        .summary-table th,
        .count-table th,
        .breakdown-table th {
            background: #f5f5f5;
            font-size: 8px;
            font-weight: bold;
        }

        .summary-table td,
        .count-table td,
        .breakdown-table td {
            font-size: 8px;
        }

        .summary-table th,
        .count-table th {
            text-align: left;
            width: 36%;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .class-name {
            font-weight: bold;
            font-size: 8px;
        }

        .grade-name {
            display: block;
            margin-top: 1px;
            font-size: 7px;
            color: #666;
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

        .signature-section {
            margin-top: 22px;
        }

        .signature-box {
            width: 180px;
            margin-left: auto;
            text-align: center;
        }

        .signature-title {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 16px;
        }

        .signature-line {
            border-top: 1px solid #000;
            padding-top: 4px;
            font-size: 7px;
            color: #555;
        }

        .calc-line {
            display: block;
            margin-bottom: 2px;
            white-space: nowrap;
        }

        .calc-type {
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Teacher Payment Report</h2>
        <h4>{{ $data['teacher_name'] ?? '-' }}</h4>
        <p>From: {{ $start_date ?? '-' }} | To: {{ $end_date ?? '-' }}</p>
    </div>

    <div class="section-title">Summary</div>
    <table class="summary-table">
        <tr>
            <th>Teacher ID</th>
            <td>{{ $data['teacher_id'] ?? '-' }}</td>
        </tr>
        <tr>
            <th>Payment Count</th>
            <td class="text-end">{{ $data['summary']['payment_count'] ?? ($data['payment_count'] ?? 0) }}</td>
        </tr>
        <tr>
            <th>Total Payment Amount</th>
            <td class="text-end">{{ number_format($data['summary']['total_payment_amount'] ?? ($data['total_payment_amount'] ?? 0), 2) }}</td>
        </tr>
        <tr>
            <th>Gross Teacher Earning</th>
            <td class="text-end">{{ number_format($data['summary']['gross_teacher_earning'] ?? ($data['gross_teacher_earning'] ?? 0), 2) }}</td>
        </tr>
        <tr>
            <th>Total Organize Cut</th>
            <td class="text-end">{{ number_format($data['summary']['total_organize_cut'] ?? ($data['total_organize_cut'] ?? 0), 2) }}</td>
        </tr>
        <tr>
            <th>Advance Deducted</th>
            <td class="text-end">{{ number_format($data['summary']['advance_deducted_for_week'] ?? ($data['advance_deducted_for_week'] ?? 0), 2) }}</td>
        </tr>
        <tr>
            <th>Net Teacher Payable</th>
            <td class="text-end highlight">{{ number_format($data['summary']['net_teacher_payable'] ?? ($data['net_teacher_payable'] ?? 0), 2) }}</td>
        </tr>
        <tr>
            <th>Institution Income</th>
            <td class="text-end">{{ number_format($data['summary']['institution_income'] ?? ($data['institution_income'] ?? 0), 2) }}</td>
        </tr>
    </table>

    <div class="section-title">Fee Type Counts</div>
    <table class="count-table">
        <tr>
            <th>Free Card Count</th>
            <td class="text-end">{{ $data['summary']['free_card_count'] ?? 0 }}</td>
        </tr>
        <tr>
            <th>Half Card Count</th>
            <td class="text-end">{{ $data['summary']['half_card_count'] ?? 0 }}</td>
        </tr>
        <tr>
            <th>Custom Fee Count</th>
            <td class="text-end">{{ $data['summary']['custom_fee_count'] ?? 0 }}</td>
        </tr>
        <tr>
            <th>Discounted Count</th>
            <td class="text-end">{{ $data['summary']['discounted_count'] ?? 0 }}</td>
        </tr>
        <tr>
            <th>Default Fee Count</th>
            <td class="text-end">{{ $data['summary']['default_fee_count'] ?? 0 }}</td>
        </tr>
    </table>

    <div class="section-title">Class / Category Summary</div>
    <table class="breakdown-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">#</th>
                <th style="width: 22%;">Class</th>
                <th style="width: 12%;">Category</th>
                <th style="width: 23%;">Fee Breakdown</th>
                <th style="width: 8%;" class="text-end">%</th>
                <th style="width: 10%;" class="text-end">Income</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rowNo = 1;
                $hasRows = false;
            @endphp

            @forelse(($data['class_category_breakdown'] ?? []) as $classItem)
                @forelse(($classItem['categories'] ?? []) as $category)
                    @php
                        $hasRows = true;
                        $teacherCut = (float)($category['teacher_cut'] ?? 0);
                        $organizeCut = (float)($category['organize_cut'] ?? 0);
                        $teacherPercentage = (float)($classItem['teacher_percentage'] ?? 0);
                        $organizePercentage = (float)($classItem['organize_percentage'] ?? 0);
                    @endphp

                    <tr>
                        <td class="text-center">{{ $rowNo++ }}</td>
                        <td>
                            <span class="class-name">{{ $classItem['class_name'] ?? '-' }}</span>
                            <span class="grade-name">{{ $classItem['grade_name'] ?? '-' }}</span>
                        </td>
                        <td>{{ $category['category_name'] ?? '-' }}</td>
                        <td>
                            @if(!empty($category['groups']))
                                @foreach($category['groups'] as $group)
                                    <span class="calc-line">
                                        <span class="calc-type">{{ strtoupper(str_replace('_', ' ', $group['type'] ?? '-')) }}</span>
                                        :
                                        {{ number_format($group['fee'] ?? 0, 2) }}
                                        × {{ $group['count'] ?? 0 }}
                                        = {{ number_format($group['total'] ?? 0, 2) }}
                                    </span>
                                @endforeach
                            @elseif(!empty($category['calculation_lines']))
                                @foreach($category['calculation_lines'] as $line)
                                    <span class="calc-line">{{ $line }}</span>
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($teacherPercentage, 2) }}</td>
                        <td class="text-end">{{ number_format($teacherCut, 2) }}</td>
                    </tr>
                @empty
                @endforelse
            @empty
            @endforelse

            @if(!$hasRows)
                <tr>
                    <td colspan="9" class="text-center">No summary data found</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="section-title">Payment Details</div>
    <table class="breakdown-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">#</th>
                <th style="width: 12%;">Student ID</th>
                <th style="width: 18%;">Student</th>
                <th style="width: 12%;">Category</th>
                <th style="width: 9%;" class="text-end">Paid</th>
                <th style="width: 9%;" class="text-end">Default</th>
                <th style="width: 10%;" class="text-end">Effective</th>
                <th style="width: 10%;">Rule</th>
                <th style="width: 8%;" class="text-end">T Cut</th>
                <th style="width: 8%;" class="text-end">O Cut</th>
            </tr>
        </thead>
        <tbody>
            @php
                $detailNo = 1;
                $hasDetails = false;
            @endphp

            @forelse(($data['payment_details'] ?? []) as $detail)
                @php $hasDetails = true; @endphp
                <tr>
                    <td class="text-center">{{ $detailNo++ }}</td>
                    <td>{{ $detail['student_custom_id'] ?? '-' }}</td>
                    <td>{{ $detail['student_name'] ?? '-' }}</td>
                    <td>{{ $detail['category_name'] ?? '-' }}</td>
                    <td class="text-end">{{ number_format($detail['amount'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($detail['default_fee'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($detail['effective_fee_reference'] ?? 0, 2) }}</td>
                    <td>{{ strtoupper(str_replace('_', ' ', $detail['effective_fee_rule'] ?? '-')) }}</td>
                    <td class="text-end">{{ number_format($detail['teacher_cut'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($detail['organize_cut'] ?? 0, 2) }}</td>
                </tr>
            @empty
            @endforelse

            @if(!$hasDetails)
                <tr>
                    <td colspan="10" class="text-center">No payment details found</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">INSTITUTE AUTHORITY</div>
            <div class="signature-line">(Authorized Signature)</div>
        </div>
    </div>

    <div class="footer-note">
        Generated on {{ date('Y-m-d H:i') }}
    </div>

</body>
</html>