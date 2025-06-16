<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>One-Time Password</title>
  <style>
    body, html, p { margin: 0; padding: 0; }
    .email-container {
      max-width: 600px;
      margin: 40px auto;
      padding: 20px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      background-color: #ffffff;
    }
    .email-header {
      text-align: center;
      padding-bottom: 20px;
      border-bottom: 1px solid #e0e0e0;
    }
    .email-header h1 {
      font-size: 24px;
      color: #333333;
    }
    .email-body {
      padding: 20px 0;
      color: #555555;
      line-height: 1.6;
    }
    .email-body p {
      margin-bottom: 16px;
    }
    .otp-code {
      display: block;
      width: fit-content;
      margin: 0 auto 16px;
      padding: 12px 20px;
      font-size: 24px;
      letter-spacing: 4px;
      background-color: #f7f7f7;
      border: 1px dashed #cccccc;
      border-radius: 4px;
      color: #222222;
    }
    .email-footer {
      text-align: center;
      padding-top: 20px;
      border-top: 1px solid #e0e0e0;
      font-size: 12px;
      color: #999999;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="email-header">
      <h1>Your Verification Code</h1>
    </div>
    <div class="email-body">
      <p>{{ $messageText }}</p>
      <span class="otp-code">{{ $otp_code }}</span>
      <p>If you didn’t request this code, you can safely ignore this email.</p>
    </div>
    <div class="email-footer">
      <p>Need help? Contact our support team.</p>
      <p>&copy; {{ date('Y') }} telebat. All rights reserved.</p>
    </div>
  </div>
</body>
</html>
