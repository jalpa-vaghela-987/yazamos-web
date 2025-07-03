<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Support Request</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2>Support Request</h2>

    <p><strong>Name:</strong> {{ $name }}</p>
    <p><strong>Email:</strong> {{ $email }}</p>
    <p><strong>Message:</strong><br>
    {!! nl2br(e($messageContent)) !!}</p>

    <hr>
    <p style="font-size: 0.9em; color: #888;">
        This message was sent from Yazamos Support Form.
    </p>
</body>
</html>
