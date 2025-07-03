<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Our Platform</title>
    <style>
        body {
            background-color: #f8fafc;
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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        h1 {
            color: #2c3e50;
            font-size: 26px;
            text-align: center;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
        }
        .highlight {
            background-color: #eef5ff;
            padding: 10px;
            border-radius: 6px;
            margin: 10px 0;
        }
        a.button {
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
    <h1>Welcome, Entrepreneur!</h1>

    <p>Your account has been created successfully. Below are your login details:</p>

    <div class="highlight">
        <p><strong>Email:</strong> {{ $email }}</p>
{{--        <p><strong>Password:</strong> {{ $password }}</p>--}}
        <p><strong>Role:</strong> {{ $role }}</p>
    </div>

    <p><strong>Important:</strong> For your security, <strong>Two-Factor Authentication (2FA)</strong> is required.
    You will be prompted to set it up on your first login.</p>

    <p style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}login" class="button">Login Here</a>
    </p>

    <div class="footer">
        &copy; {{ date('Y') }} yazamos. All rights reserved.
    </div>
</div>

</body>
</html>
