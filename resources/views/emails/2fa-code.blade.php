<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Two-Factor Authentication Setup</title>
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
        .secret-key {
            background-color: #eef6ff;
            border: 1px dashed #4a90e2;
            color: #2c3e50;
            font-weight: bold;
            font-size: 20px;
            text-align: center;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            word-break: break-all;
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
    <h1>Two-Factor Authentication Setup</h1>

    <p>Hello <strong>{{ $user->name }}</strong>,</p>

    <p>To enhance the security of your account, please use the secret key below to set up Two-Factor Authentication in your preferred authenticator app (such as Google Authenticator, Authy, or Microsoft Authenticator):</p>

    <div class="secret-key">
        {{ $user->google2fa_secret }}
    </div>

    <p>If you did not request to enable Two-Factor Authentication, please ignore this email or contact support immediately.</p>

    <div class="footer">
        &copy; {{ date('Y') }} yazamos. All rights reserved.
    </div>
</div>

</body>
</html>
