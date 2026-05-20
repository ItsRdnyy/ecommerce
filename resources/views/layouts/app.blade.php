<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PureFit Apparel') — PureFit Apparel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-serif-display { font-family: 'Instrument Serif', serif; }
        .font-sans-body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="font-sans-body text-gray-900 antialiased" data-is-authenticated="{{ auth()->check() ? 'true' : 'false' }}" data-cart-count-url="{{ route('cart.count') }}" data-cart-add-url="{{ route('cart.add') }}">

    <!-- Navigation -->
    <nav class="bg-[#f5f3ef] border-b border-[#e8e5e0]">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10">
            <div class="flex items-center justify-between h-[70px]">
                <!-- Left Links -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('products') }}" class="text-[11px] font-medium tracking-[0.12em] uppercase text-gray-800 hover:text-black transition-colors">Shop</a>
                    <a href="{{ route('products') }}?collections=1" class="text-[11px] font-medium tracking-[0.12em] uppercase text-gray-800 hover:text-black transition-colors">Collections</a>
                    <a href="{{ route('home') }}#products" class="text-[11px] font-medium tracking-[0.12em] uppercase text-gray-800 hover:text-black transition-colors">New Arrivals</a>
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" class="md:hidden p-2 text-gray-800" data-mobile-menu-toggle>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Logo -->
                <a href="/" class="absolute left-1/2 -translate-x-1/2 text-[22px] font-semibold tracking-[0.2em] uppercase text-gray-900">
                    PureFit Apparel
                </a>

                <!-- Right Links -->
                <div class="flex items-center gap-5">

                    @auth
                        <a href="{{ route('cart.index') }}" class="text-gray-800 hover:text-black relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                            <span id="cart-count" class="absolute -top-1 -right-1 w-4 h-4 bg-gray-900 text-white text-[9px] flex items-center justify-center rounded-full hidden">0</span>
                        </a>
                        <a href="{{ route('notifications.index') }}" class="text-gray-800 hover:text-black relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                            <span id="notif-count" class="absolute -top-1 -right-1 w-4 h-4 bg-red-600 text-white text-[9px] flex items-center justify-center rounded-full hidden"></span>
                        </a>
                    @endauth

                    @guest
                        <a href="{{ route('login', ['redirect' => url()->full()]) }}" class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-800 hover:text-black transition-colors">
                            Log In
                        </a>
                    @else
                        <div class="relative group">
                            <button class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-800 hover:text-black transition-colors flex items-center gap-1">
                                Account
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                            </button>
                            <div class="absolute right-0 top-full mt-2 w-48 bg-white border border-[#e8e5e0] shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                                <a href="/" class="block px-4 py-3 text-[11px] font-medium tracking-[0.1em] uppercase text-gray-800 hover:bg-[#f5f3ef]">Dashboard</a>
                                <a href="{{ route('orders.index') }}" class="block px-4 py-3 text-[11px] font-medium tracking-[0.1em] uppercase text-gray-800 hover:bg-[#f5f3ef]">Orders</a>
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-3 text-[11px] font-medium tracking-[0.1em] uppercase text-red-600 hover:bg-[#f5f3ef]">Logout</button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-[#f5f3ef] border-t border-[#e8e5e0]">
            <div class="px-6 py-4 space-y-3">
                <a href="{{ route('products') }}" class="block text-[11px] font-medium tracking-[0.12em] uppercase text-gray-800">Men</a>
                <a href="{{ route('products') }}" class="block text-[11px] font-medium tracking-[0.12em] uppercase text-gray-800">Women</a>
                <a href="{{ route('products') }}" class="block text-[11px] font-medium tracking-[0.12em] uppercase text-gray-800">Collections</a>
                <a href="{{ route('products') }}" class="block text-[11px] font-medium tracking-[0.12em] uppercase text-gray-800">New Arrivals</a>
            </div>
        </div>
    </nav>

    @yield('content')

    @stack('scripts')

    <!-- Layout JavaScript -->
    <script src="{{ asset('js/layout.js') }}"></script>

</body>
</html>
