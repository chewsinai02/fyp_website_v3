<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #673ab7;
            color: white;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            background-color: white;
            border-radius: 0 0 5px 5px;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            color: #673ab7;
            padding: 20px;
            margin: 20px 0;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://firebasestorage.googleapis.com/v0/b/fyptestv2-37c45.firebasestorage.app/o/assets%2Fimages%2Flogo.png?alt=media&token=c6df2181-1a6c-4fd7-8bc5-88697e2b1910" 
                alt="Logo" class="logo" style="width: 100px; height: 100px;">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <h2>Password Reset Request</h2>
            <p>Dear User,</p>
            <p>We received a request to reset your password. Please use the following OTP code to proceed with your password reset:</p>
            
            <div class="otp-code">
                {{ $otp }}
            </div>
            
            <p>This OTP will expire in 15 minutes. If you did not request this password reset, please ignore this email.</p>
            
            <p>Best regards,<br>{{ config('app.name') }} Team</p>
        </div>
        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 