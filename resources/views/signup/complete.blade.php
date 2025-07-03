<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup Complete - Yazamos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-purple-100 min-h-screen flex items-center justify-center px-4">

    <div class="bg-white shadow-xl rounded-2xl p-8 sm:p-10 w-full max-w-md">
        <div class="flex justify-center mb-4">
            <div class="bg-green-100 text-green-600 rounded-full p-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-2">Signup Complete</h2>
        <p class="text-center text-gray-600 mb-4">
            Your account has been successfully created. Welcome to <span class="font-medium text-purple-600">Yazamos</span>!
        </p>

        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <p class="text-gray-700 font-medium mb-2 text-center">Next Steps:</p>
            <ul class="text-gray-600 list-disc list-inside text-left text-sm">
                <li>Download and install our mobile app.</li>
                <li>Login using your registered credentials.</li>
                <li>Start exploring Yazamos features!</li>
            </ul>
        </div>

        <div class="text-center mb-4">
            <p class="text-sm text-gray-500">App available on:</p>
            <div class="flex justify-center gap-4 mt-2">
                <div class="flex items-center gap-1 text-gray-700 text-sm">
                    Google Play
                </div>
                <div class="flex items-center gap-1 text-gray-700 text-sm">
                    App Store
                </div>
            </div>
        </div>

        <!-- Login Button -->
        <div class="text-center mb-4">
            <a id="loginLink" href="#" class="inline-block bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold px-6 py-2 rounded-lg">
                Go to Login
            </a>
        </div>

        <p class="text-xs text-gray-400 text-center">Thank you for joining Yazamos!</p>
    </div>

    <script>
        // Assuming VUE_APP_URL is injected server-side or from .env to the HTML
        const baseUrl = '{{ env("VUE_APP_URL") }}'; // Use server-side rendering in Laravel
        document.getElementById('loginLink').href = baseUrl + 'login';
    </script>
</body>
</html>
