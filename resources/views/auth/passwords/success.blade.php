<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Changed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #00c9ff 0%, #92fe9d 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            background-color: #ffffffcc;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            text-align: center;
        }
        .icon {
            font-size: 64px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .btn-custom {
            background-color: #28a745;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container col-md-6">
        <div class="card">
            <div class="icon">✅</div>
            <h2 class="mb-3">Password Changed Successfully</h2>
            <p class="mb-4">You can now log in using your new password. Keep it secure and private!</p>
        </div>
    </div>
</body>
</html>
