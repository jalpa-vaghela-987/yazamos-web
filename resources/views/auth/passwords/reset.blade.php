<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }
        .card {
            background-color: #ffffff0d;
            border: none;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }
        .btn-custom {
            background-color: #ff5e62;
            border: none;
        }
        .btn-custom:hover {
            background-color: #ff9966;
        }
    </style>
</head>
<body>
    <div class="container col-md-5">
        <div class="card">
            <h3 class="mb-4 text-center">🔒 Reset Your Password</h3>

            @if(session('status'))
                <div class="alert alert-success text-dark">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" class="form-control" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-custom w-100 mt-3">Reset Password</button>
            </form>
        </div>
    </div>
</body>
</html>
