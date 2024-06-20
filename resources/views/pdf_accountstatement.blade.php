<!DOCTYPE html>
<html>
<head>
    <title>Account Statement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header, .footer {
            text-align: center;
        }

        .header {
            margin-bottom: 20px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>PLANETF Account Statement</h1>
    <p>{{ $user->full_name }}</p>
    <p>{{ $user->email }}</p>
    <p>From: {{ $startDate }} To: {{ $endDate }}</p>
</div>

<table>
    <thead>
    <tr>
        <th>id</th>
        <th>Date</th>
        <th>Description</th>
        <th>Status</th>
        <th>Amount</th>
        <th>I. Wallet</th>
        <th>F. Wallet</th>
    </tr>
    </thead>
    <tbody>
    @foreach($trans as $transaction)
        <tr>
            <td>{{$i++}}</td>
            <td>{{ $transaction->date }}</td>
            <td>{{ $transaction->description }}</td>
            <td>{{ $transaction->status }}</td>
            <td>#{{ $transaction->amount }}</td>
            <td>#{{number_format($transaction->i_wallet)}}</td>
            <td>#{{number_format($transaction->f_wallet)}}</td>

        </tr>
    @endforeach
    </tbody>
</table>

<div class="footer">
    <p>Generated on {{ date('Y-m-d') }}</p>
</div>
</body>
</html>
