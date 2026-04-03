<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Weekly Teacher Payments</title>
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
            text-align: left;
        }
        th {
            background: #f2f2f2;
        }
        .text-end {
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Weekly Teacher Payments Report</h2>
        <p>From: {{ $start_date ?? '-' }} | To: {{ $end_date ?? '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Teacher Name</th>
                <th class="text-end">Monthly Salary</th>
                <th class="text-end">Weekly Salary</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['teacher_name'] ?? '-' }}</td>
                    <td class="text-end">{{ number_format($row['monthly_salary'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['weekly_salary'] ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No data found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>