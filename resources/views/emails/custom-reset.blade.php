<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset Request</title>
    <style>
        body {
            background-color: #f4f6f8;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        h1 {
            color: #2c3e50;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
        }
        .reset-button {
            display: inline-block;
            background-color: #4a90e2;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            color: #999;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Password Reset Request</h1>

    <p>Hello <strong>{{ $user->name ?? 'User' }}</strong>,</p>

    <p>We received a request to reset your password. Click the button below to set a new password:</p>

    <p style="text-align: center;">
        <a href="{{ $url }}" class="reset-button">Reset Password</a>
    </p>

    <p>This link will expire in <strong>60 minutes</strong>.</p>

    <p>If you did not request a password reset, you can safely ignore this email.</p>

    <div class="footer">
        &copy; {{ date('Y') }} yazamos. All rights reserved.
    </div>
</div>

</body>
</html>
