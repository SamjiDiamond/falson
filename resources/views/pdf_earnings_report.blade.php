<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Earnings Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { margin: 0 0 8px; }
        p { margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f3f3f3; }
    </style>
</head>
<body>
<h2>PlanetF Earnings Report</h2>
<p>Date Range: {{ \Carbon\Carbon::parse($from)->format('Y-m-d') }} to {{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}</p>
<p>Total Income: ₦{{ number_format($incomeSum) }} | Total Expenses: ₦{{ number_format($expenseSum) }} | Profit: ₦{{ number_format($profit) }}</p>

<h3>Income By GL</h3>
<table>
    <thead>
    <tr>
        <th>GL</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($incomeByGl as $row)
        <tr>
            <td>{{ $row->gl }}</td>
            <td>₦{{ number_format($row->total_amount) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h3>Expenses By GL</h3>
<table>
    <thead>
    <tr>
        <th>GL</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($expenseByGl as $row)
        <tr>
            <td>{{ $row->gl }}</td>
            <td>₦{{ number_format($row->total_amount) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>

