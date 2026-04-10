<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Salary Slip - {{ $data['teacher_name'] ?? 'Teacher' }}</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 40px;
            font-size: 14px;
        }

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .logo-section {
            flex: 0 0 auto;
        }

        .logo {
            width: 90px;
            height: auto;
        }

        .header {
            flex: 1;
            text-align: center;
        }

        .header h2 {
            margin: 5px 0;
            font-weight: 700;
            font-size: 24px;
        }

        .header h3 {
            margin: 3px 0;
            font-weight: 600;
        }

        .date {
            flex: 0 0 auto;
            font-size: 13px;
            text-align: right;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th.right-align,
        td.right-align {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .signature-area {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 100px;
            font-size: 18px;
        }

        .teacher-info-table {
            margin-top: 15px;
            border: none;
        }

        .teacher-info-table th {
            text-align: left;
            background-color: #f2f2f2;
            padding: 8px 10px;
            border: 1px solid #ddd;
        }

        .teacher-info-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }

        .status-paid {
            color: green;
            font-weight: bold;
        }

        .status-unpaid {
            color: #666;
            font-style: italic;
        }

        .small-text {
            color: #666;
            font-size: 12px;
            line-height: 1.4;
        }

        .summary-table td,
        .summary-table th {
            padding: 8px 10px;
        }
    </style>
</head>

<body>

@php
    $data = $data ?? [];
    $isSuccess = ($data['status'] ?? '') === 'success';

    $teacherId = $data['teacher_id'] ?? '';
    $teacherName = $data['teacher_name'] ?? '';
    $monthYear = $data['month_year'] ?? '';
    $monthYearDisplay = $data['month_year_display'] ?? '';
    $dateGenerated = $data['date_generated'] ?? '';
    $isSalaryPaid = $data['is_salary_paid'] ?? false;

    $earnings = $data['earnings'] ?? [];
    $deductions = $data['deductions'] ?? [];

    $totalClassAmount = $data['total_class_amount'] ?? 0;
    $totalTeacherEarnings = $data['total_teacher_earnings'] ?? 0;
    $totalOrganizeCut = $data['total_organize_cut'] ?? 0;
    $totalInstitutionCut = $data['total_institution_cut'] ?? 0;

    $totalAddition = $data['total_addition'] ?? 0;
    $totalDeductions = $data['total_deductions'] ?? 0;
    $netSalary = $data['net_salary'] ?? 0;

    $paymentMethod = $data['payment_method'] ?? 'Cash / Bank Deposit';
@endphp

@if(!$isSuccess)
    <div class="error-message">
        <h3>Error Loading Salary Slip</h3>
        <p>{{ $data['message'] ?? 'Unknown error occurred' }}</p>
        <p>Teacher ID: {{ $teacherId ?? 'N/A' }}, Month: {{ $monthYear ?? 'N/A' }}</p>
    </div>
@elseif(empty($earnings) && empty($deductions))
    <div class="error-message">
        <h3>No Data Available</h3>
        <p>No classes or payments found for this period.</p>
    </div>
@else

    <div class="top-section">
        <div class="logo-section">
            <img src="{{ asset('uploads/logo/black_logo.png') }}" class="logo" alt="Logo">
        </div>

        <div class="header">
            <h2>VISION ACADEMY OF HIGHER EDUCATION</h2>
            <h3>Kurunegala</h3>
            <h3>Salary Slip</h3>
        </div>

        <div class="date">
            <strong>Date:</strong> {{ $dateGenerated }}
        </div>
    </div>

    <table class="teacher-info-table">
        <tr>
            <th>Teacher ID</th>
            <td>{{ $teacherId }}</td>
            <th>Teacher Name</th>
            <td>{{ $teacherName }}</td>
        </tr>
        <tr>
            <th>Month/Year</th>
            <td>{{ $monthYearDisplay }}</td>
            <th>Payment Status</th>
            <td class="{{ $isSalaryPaid ? 'status-paid' : 'status-unpaid' }}">
                {{ $isSalaryPaid ? 'Paid' : 'Unpaid' }}
            </td>
        </tr>
    </table>

    <table>
        <tr class="bold">
            <th>Earnings</th>
            <th class="right-align">Amount (Rs.)</th>
            <th>Deductions</th>
            <th class="right-align">Amount (Rs.)</th>
        </tr>

        @php
            $maxRows = max(count($earnings), count($deductions));
        @endphp

        @for($i = 0; $i < $maxRows; $i++)
            <tr>
                @if(isset($earnings[$i]))
                    <td>
                        {{ $earnings[$i]['description'] ?? '' }}<br>
                        <div class="small-text">
                            {{ isset($earnings[$i]['class_total']) ? 'Class Total: Rs. ' . number_format($earnings[$i]['class_total'], 2) : '' }}<br>
                            Teacher %: {{ $earnings[$i]['teacher_percentage'] ?? 0 }}% = Rs.
                            {{ number_format($earnings[$i]['teacher_share'] ?? 0, 2) }}<br>
                            Organize %: {{ $earnings[$i]['organize_percentage'] ?? 0 }}% = Rs.
                            {{ number_format($earnings[$i]['organize_cut'] ?? 0, 2) }}<br>
                            Institute Cut: Rs. {{ number_format($earnings[$i]['institution_cut'] ?? 0, 2) }}
                        </div>
                    </td>
                    <td class="right-align">{{ number_format($earnings[$i]['amount'] ?? 0, 2) }}</td>
                @else
                    <td></td>
                    <td></td>
                @endif

                @if(isset($deductions[$i]))
                    <td>{{ $deductions[$i]['description'] ?? '' }}</td>
                    <td class="right-align">{{ number_format($deductions[$i]['amount'] ?? 0, 2) }}</td>
                @else
                    <td></td>
                    <td></td>
                @endif
            </tr>
        @endfor

        <tr class="bold">
            <td>Total Addition</td>
            <td class="right-align">{{ number_format($totalAddition, 2) }}</td>
            <td>Total Deductions</td>
            <td class="right-align">{{ number_format($totalDeductions, 2) }}</td>
        </tr>

        <tr class="bold">
            <td colspan="3">Net Salary</td>
            <td class="right-align">{{ number_format($netSalary, 2) }}</td>
        </tr>
    </table>

    <table class="teacher-info-table summary-table" style="margin-top: 20px;">
        <tr>
            <th>Total Class Amount</th>
            <td>Rs. {{ number_format($totalClassAmount, 2) }}</td>
            <th>Total Teacher Earnings</th>
            <td>Rs. {{ number_format($totalTeacherEarnings, 2) }}</td>
        </tr>
        <tr>
            <th>Total Organize Cut</th>
            <td>Rs. {{ number_format($totalOrganizeCut, 2) }}</td>
            <th>Total Institution Cut</th>
            <td>Rs. {{ number_format($totalInstitutionCut, 2) }}</td>
        </tr>
    </table>

    <table class="teacher-info-table" style="margin-top: 20px;">
        <tr>
            <th>Payment Method</th>
            <td>{{ $paymentMethod }}</td>
            <th>Salary Period</th>
            <td>{{ $monthYearDisplay }}</td>
        </tr>
    </table>

    <div class="signature-area">
        <div><strong>Teacher's Signature:</strong> _____________</div>
        <div><strong>Vision Owner:</strong> _____________</div>
    </div>

@endif

<script>
    window.addEventListener('load', function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('autoPrint') === 'true') {
            setTimeout(() => window.print(), 1000);
        }
    });
</script>

</body>
</html>