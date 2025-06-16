<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { color: #2c3e50; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
        .button { background-color: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .footer { margin-top: 30px; font-size: 12px; color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="header">Welcome to Tele BAT your loyal butler 🦇</div>
    <p>Hi {{$user->email}},</p>
    <p>We're thrilled to have you on board. 🎉</p>
    
    <p><strong>Here’s what you can do next:</strong></p>
    <ul>
        <li>✨ <strong>Verify your account !!</strong>: <a href="[Profile Setup Link]">Click here</a></li>
        <li>📱 <strong>Download our app</strong>: 
            <a href="[App Store Link]">App Store</a> | 
            <a href="[Google Play Link]">Google Play</a>
        </li>
    </ul>
    
    <p>Need help? Reply to this email or visit our <a href="[Support Page]">Support Center</a>.</p>
    
    <a href="[CTA Link]" class="button">Get Started</a>
    
    <div class="footer">
        <p>Best regards,<br>[Your Name]<br>[Company Team]</p>
        <p><a href="[Website]">[Company Website]</a></p>
        <p>Follow us: 
            <a href="[Twitter]">Twitter/X</a> | 
            <a href="[Instagram]">Instagram</a> | 
            <a href="[LinkedIn]">LinkedIn</a>
        </p>
    </div>
</body>
</html>