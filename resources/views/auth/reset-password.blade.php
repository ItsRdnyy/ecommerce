<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — PureFit Apparel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-serif-display { font-family: 'Instrument Serif', serif; }
        .font-sans-body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="font-sans-body text-gray-900 antialiased bg-[#f5f3ef] min-h-screen flex items-center justify-center">

    <div class="w-full max-w-[420px] px-6">
        <!-- Logo -->
        <div class="text-center mb-10">
            <a href="/" class="text-[22px] font-semibold tracking-[0.2em] uppercase text-gray-900">
                PureFit Apparel
            </a>
        </div>

        <!-- Heading -->
        <div class="text-center mb-8">
            <h1 class="font-serif-display text-[32px] sm:text-[40px] leading-[1.1] text-gray-900 mb-3">
                Reset password.
            </h1>
            <p class="text-[14px] text-gray-600">
                Enter your new password below.
            </p>
        </div>

        <!-- Reset Password Form -->
        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label for="email" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ $email }}" readonly
                       class="w-full bg-gray-100 border border-[#ddd8d0] px-4 py-3 text-[14px] text-gray-600 cursor-not-allowed">
            </div>

            <div>
                <label for="password" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-700 mb-2">New Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                           class="w-full bg-white border border-[#ddd8d0] pl-4 pr-12 py-3 text-[14px] text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#111] transition-colors"
                           placeholder="Enter your new password">
                    <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-semibold tracking-wider text-gray-500 uppercase hover:text-black focus:outline-none select-none">
                        Show
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-[12px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-700 mb-2">Confirm Password</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full bg-white border border-[#ddd8d0] pl-4 pr-12 py-3 text-[14px] text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#111] transition-colors"
                           placeholder="Confirm your new password">
                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-semibold tracking-wider text-gray-500 uppercase hover:text-black focus:outline-none select-none">
                        Show
                    </button>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-[#111] text-white text-[11px] font-semibold tracking-[0.12em] uppercase px-8 py-3.5 hover:bg-gray-800 transition-colors">
                Reset Password
            </button>
        </form>

        <!-- Back to Login -->
        <p class="text-center text-[14px] text-gray-600 mt-8">
            Remember your password?
            <a href="{{ route('login') }}" class="text-gray-900 font-medium hover:underline underline-offset-2 ml-1">Log in</a>
        </p>
    </div>

    <script>
        function togglePasswordVisibility(fieldId, button) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                button.textContent = 'Hide';
            } else {
                field.type = 'password';
                button.textContent = 'Show';
            }
        }
    </script>
</body>
</html>
