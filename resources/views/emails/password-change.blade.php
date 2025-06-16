<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Password Changed Notification</title>
  <style>
    /* CLIENT-SPECIFIC STYLES */
    body, table, td { margin:0; padding:0; }
    img { border:0; height:auto; line-height:100%; outline:none; text-decoration:none; }
    table { border-collapse:collapse !important; }
    body { height:100% !important; width:100% !important; }

    /* MOBILE STYLES */
    @media screen and (max-width:600px) {
      .email-container { width:100% !important; }
      .fluid { width:100% !important; max-width:100% !important; height:auto !important; }
      .stack-column, .stack-column-center {
        display:block !important; width:100% !important; max-width:100% !important;
      }
      .stack-column-center { text-align:center !important; }
    }

    /* BASIC STYLING */
    .email-container {
      width:600px; margin:0 auto; background:#ffffff; border:1px solid #dddddd;
      font-family:Arial, sans-serif; color:#333333;
    }
    .header { background:#4a90e2; padding:20px; text-align:center; }
    .header h1 { color:#ffffff; font-size:24px; margin:0; }
    .body { padding:30px; }
    .body p { font-size:16px; line-height:1.5; margin:0 0 20px; }
    .button {
      display:inline-block; padding:12px 24px; background:#4a90e2; color:#ffffff;
      text-decoration:none; border-radius:4px; font-weight:bold;
    }
    .footer {
      background:#f9f9f9; padding:20px; text-align:center; font-size:12px; color:#777777;
    }
  </style>
</head>
<body>
  <table role="presentation" class="email-container">
    <tr>
      <td class="header">
        <h1>Password Changed</h1>
      </td>
    </tr>
    <tr>
      <td class="body">
        <p>Hi {{ $user->first_name }},</p>
        <p>We wanted to let you know that your account password was changed successfully.</p>
        <p>Thank you,<br/>The {{ config('app.name') }} Team</p>
      </td>
    </tr>
    <tr>
      <td class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
      </td>
    </tr>
  </table>
</body>
</html>
