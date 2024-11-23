<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 50px auto;
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 0;
            border-radius: 8px 8px 0 0;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin: 20px 0;
        }
        .email-body {
            color: #333333;
            font-size: 16px;
        }
        .email-footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777777;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h2>Password Reset Request</h2>
    </div>
    <div class="email-body">
        <p>Hello,</p>
        <p>You requested to reset your password. Use the OTP code below to reset your password:</p>
        <div class="otp">{{ $token }}</div>
        <p>This OTP is valid for 60 minutes. If you didn’t request a password reset, please ignore this email.</p>
    </div>
    <div class="email-footer">
        <p>&copy; {{ date('Y') }} {{env('APP_NAME')}}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
