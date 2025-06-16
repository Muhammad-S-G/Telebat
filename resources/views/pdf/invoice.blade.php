<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Invoice #{{ $payment_id }}</title>
  <style>
    body {
      font-family: DejaVu Sans, Arial, sans-serif;
      margin: 0;
      padding: 0;
      color: #333;
    }
    .container {
      width: 90%;
      margin: 0 auto;
      padding: 20px;
    }
    .header {
      text-align: center;
      margin-bottom: 40px;
    }
    .header h1 {
      margin: 0;
      font-size: 28px;
    }
    .header .sub {
      font-size: 14px;
      color: #777;
    }
    .info, .details {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    .info td, .details th, .details td {
      padding: 8px 12px;
      border: 1px solid #ddd;
    }
    .info td.label {
      background-color: #f5f5f5;
      width: 25%;
      font-weight: bold;
    }
    .details th {
      background-color: #f0f0f0;
      text-align: left;
      font-weight: normal;
    }
    .details td {
      text-align: right;
    }
    .footer {
      text-align: center;
      font-size: 12px;
      color: #aaa;
      position: fixed;
      bottom: 20px;
      width: 100%;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Invoice #{{ $payment_id }}</h1>
      <div class="sub">Payment Request #{{ $payment_request_id }}</div>
    </div>

    <table class="info">
      <tr>
        <td class="label">Date Paid</td>
        <td>{{ \Carbon\Carbon::parse($completed_at)->format('Y-m-d H:i') }}</td>
      </tr>
      <tr>
        <td class="label">Amount</td>
        <td>{{ number_format($price, 2) }} {{ strtoupper($currency) }}</td>
      </tr>
      <tr>
        <td class="label">Payment Method</td>
        <td>{{ ucfirst(strtolower($payment_method)) }}</td>
      </tr>
      <tr>
        <td class="label">Title</td>
        <td>{{ is_array($title) ? $title[app()->getLocale()] : $title }}</td>
      </tr>
      <tr>
        <td class="label">Description</td>
        <td>{{ is_array($description) ? $description[app()->getLocale()] : $description }}</td>
      </tr>
    </table>

    <div class="footer">
      Thank you for your business!<br>
      &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
  </div>
</body>
</html>
