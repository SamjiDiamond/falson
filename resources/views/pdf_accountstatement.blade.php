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
<table style="border: none;">
    <tbody>
    <tr style="border: none;">
        <td style="border: none;">
            <h4>PLANET-F</h4>
        </td>
        <td style="border: none; text-align: right;">
            <img src="{{env('APP_LOGO')}}" width="70px" height="70px">
        </td>
    </tr>
    </tbody>
</table>


<h5 style="color: #0c5460; text-align: start">Statement of Account</h5>

<div class="header">
    <hr style="color: #0c93fe;">
    <p style="font-style: italic; color: #0c93fe; margin-bottom: 30px">Account Holder Information</p>
    <p>Name: <span style="color: #0c93fe;">{{ $user->full_name }}</span></p>
    <p>Address: <span style="color: #0c93fe;">{{ $user->address }}</span></p>
    <p>Email: <span style="color: #0c93fe;">{{ $user->email }}</span></p>
    <p><span style="color: #0c5460">Statement Period:</span> <span
            style="color: #0c93fe;">From: {{ $startDate }} to: {{ $endDate }}</span></p>
    <hr style="color: #0c93fe;">
</div>

<p style="color: #0c5460;">Account Summary</p>
<table>
    <thead>
    <tr style="color: #00c47e">
        <th>ID</th>
        <th>Date</th>
        <th>Description</th>
        <th>Status</th>
        <th>Amount (NGN)</th>
        <th>B.Before (NGN)</th>
        <th>B.After (NGN)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($trans as $transaction)
        <tr>
            <td>{{$i++}}</td>
            <td>{{ $transaction->date }}</td>
            <td>{{ $transaction->description }}</td>
            <td>{{ $transaction->status }}</td>
            <td>{{number_format($transaction->amount) }}</td>
            <td>{{number_format($transaction->i_wallet)}}</td>
            <td>{{number_format($transaction->f_wallet)}}</td>

        </tr>
    @endforeach
    </tbody>
</table>

<p>Available Balance: NGN {{number_format($user->wallet)}}</p>

<div class="footer">
    <div style="text-align: left;">
        <img src="{{env('APP_LOGO')}}" width="30px" height="30px"> Planet-F
    </div>

    <hr>
    <hr>
    <p>Falson Global Services.</p>
</div>
</body>
</html>
