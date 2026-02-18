<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signing in with Google...</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 text-center">
        <div class="mx-auto w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center mb-4">
            <!-- Google icon -->
            <svg class="w-8 h-8" viewBox="0 0 48 48" aria-hidden="true">
                <path fill="#FFC107"
                    d="M43.611 20.083H42V20H24v8h11.303C33.648 32.659 29.215 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z" />
                <path fill="#FF3D00"
                    d="M6.306 14.691l6.571 4.819C14.655 16.108 18.961 13 24 13c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z" />
                <path fill="#4CAF50"
                    d="M24 44c5.113 0 9.806-1.963 13.333-5.156l-6.159-5.211C29.109 35.091 26.7 36 24 36c-5.194 0-9.614-3.317-11.287-7.946l-6.518 5.02C9.505 39.556 16.227 44 24 44z" />
                <path fill="#1976D2"
                    d="M43.611 20.083H42V20H24v8h11.303c-.795 2.214-2.231 4.084-4.129 5.633l.003-.002 6.159 5.211C36.9 39.156 44 34 44 24c0-1.341-.138-2.65-.389-3.917z" />
            </svg>
        </div>

        <h1 class="text-xl font-semibold text-gray-800">Signing you in…</h1>

        <p class="text-gray-600 mt-2">Welcome <span class="font-medium">{{ $userName ?? 'User' }}</span> 👋<br>We’re
            redirecting you to your dashboard.</p>

        <div class="flex justify-center mt-6">
            <div class="w-12 h-12 rounded-full border-4 border-gray-200 border-t-blue-600 animate-spin"></div>
        </div>

        <div class="mt-6 text-sm text-gray-600">If you are not redirected,
            <a class="text-blue-600 hover:underline font-medium" href="{{ $redirectTo ?? route('login') }}">click
                here</a>.
        </div>

        <div class="mt-4 text-xs text-gray-400">Secure login powered by Google OAuth</div>
    </div>

    <script>
        (function() {
            const redirectTo = @json($redirectTo ?? '');
            if (redirectTo) {
                setTimeout(() => window.location.href = redirectTo, 1200);
            }
        })();
    </script>

</body>

</html>
