    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — PureFit Apparel</title>
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
                Create account.
            </h1>
            <p class="text-[14px] text-gray-600">
                Join us to start shopping.
            </p>
        </div>

        <!-- Register Form -->
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-700 mb-2">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full bg-white border border-[#ddd8d0] px-4 py-3 text-[14px] text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#111] transition-colors"
                       placeholder="Your full name">
                @error('name')
                    <p class="mt-2 text-[12px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="w-full bg-white border border-[#ddd8d0] px-4 py-3 text-[14px] text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#111] transition-colors"
                       placeholder="you@example.com">
                @error('email')
                    <p class="mt-2 text-[12px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-700 mb-3">Account Type</label>
                <div class="space-y-3">
                    <label class="flex items-center p-3 bg-white border border-[#ddd8d0] rounded cursor-pointer hover:border-[#111] transition-colors">
                        <input type="radio" name="account_type" value="personal" {{ old('account_type', 'personal') === 'personal' ? 'checked' : '' }}
                               class="w-4 h-4 border-gray-300 accent-[#111]">
                        <span class="ml-3 text-[14px] text-gray-900">Personal Use (Buyer)</span>
                    </label>
                    <label class="flex items-center p-3 bg-white border border-[#ddd8d0] rounded cursor-pointer hover:border-[#111] transition-colors">
                        <input type="radio" name="account_type" value="business" {{ old('account_type') === 'business' ? 'checked' : '' }}
                               class="w-4 h-4 border-gray-300 accent-[#111]">
                        <span class="ml-3 text-[14px] text-gray-900">Business Use (Seller)</span>
                    </label>
                </div>
                @error('account_type')
                    <p class="mt-2 text-[12px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="business-fields" class="{{ old('account_type') === 'business' ? '' : 'hidden' }} space-y-5">
                <div>
                    <label for="business_name" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-700 mb-2">Business Name</label>
                    <input type="text" id="business_name" name="business_name" value="{{ old('business_name') }}"
                           class="w-full bg-white border border-[#ddd8d0] px-4 py-3 text-[14px] text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#111] transition-colors"
                           placeholder="Your company name">
                    @error('business_name')
                        <p class="mt-2 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tax_id" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-700 mb-2">Tax ID / Business Registration</label>
                    <input type="text" id="tax_id" name="tax_id" value="{{ old('tax_id') }}"
                           class="w-full bg-white border border-[#ddd8d0] px-4 py-3 text-[14px] text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#111] transition-colors"
                           placeholder="Tax ID or Registration Number">
                    @error('tax_id')
                        <p class="mt-2 text-[12px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="password" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-700 mb-2">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                           class="w-full bg-white border border-[#ddd8d0] pl-4 pr-12 py-3 text-[14px] text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#111] transition-colors"
                           placeholder="Create a password">
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
                           placeholder="Confirm your password">
                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-semibold tracking-wider text-gray-500 uppercase hover:text-black focus:outline-none select-none">
                        Show
                    </button>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-[#111] text-white text-[11px] font-semibold tracking-[0.12em] uppercase px-8 py-3.5 hover:bg-gray-800 transition-colors">
                Create Account
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-[#ddd8d0]"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-[#f5f3ef] px-4 text-[12px] text-gray-500 uppercase tracking-wider">or</span>
            </div>
        </div>

        <!-- Login Link -->
        <p class="text-center text-[14px] text-gray-600">
            Already have an account?
            <a href="{{ route('login') }}" class="text-gray-900 font-medium hover:underline underline-offset-2 ml-1">Log in</a>
        </p>
    </div>

    <script>
<<<<<<< HEAD
        document.querySelectorAll('input[name="account_type"]').forEach(input => {
            input.addEventListener('change', function() {
                const businessFields = document.getElementById('business-fields');
                if (this.value === 'business') {
                    businessFields.classList.remove('hidden');
                } else {
                    businessFields.classList.add('hidden');
                }
            });
        });
=======
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
>>>>>>> 90b90494cd213367d9f3d3bb0a8d06cd014d8b3b
    </script>
</body>
</html>
