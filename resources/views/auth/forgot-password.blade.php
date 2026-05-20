<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — PureFit Apparel</title>
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
                Forgot password?
            </h1>
            <p class="text-[14px] text-gray-600">
                No worries, we'll send you reset instructions.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 text-[13px] text-center">
                {{ session('status') }}
            </div>
        @endif

        <!-- Forgot Password Form -->
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="w-full bg-white border border-[#ddd8d0] px-4 py-3 text-[14px] text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#111] transition-colors"
                       placeholder="you@example.com">
                @error('email')
                    <p class="mt-2 text-[12px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-[#111] text-white text-[11px] font-semibold tracking-[0.12em] uppercase px-8 py-3.5 hover:bg-gray-800 transition-colors">
                Send Reset Link
            </button>
        </form>

        <!-- Back to Login -->
        <p class="text-center text-[14px] text-gray-600 mt-8">
            Remember your password?
            <a href="{{ route('login') }}" class="text-gray-900 font-medium hover:underline underline-offset-2 ml-1">Log in</a>
        </p>
    </div>

</body>
</html>
