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
<body class="font-sans-body text-gray-900 antialiased" data-is-authenticated="{{ auth()->check() ? 'true' : 'false' }}" data-cart-count-url="{{ route('cart.count') }}" data-cart-add-url="{{ route('cart.add') }}" data-notif-count-url="{{ route('notifications.count') }}">

    <!-- Navigation -->
    <nav class="bg-[#f5f3ef] border-b border-[#e8e5e0] relative z-50">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10">
            <div class="flex items-center justify-between h-[70px]">
                <!-- Left Links -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="/" class="text-[11px] font-medium tracking-[0.12em] uppercase text-gray-800 hover:text-black transition-colors">Home</a>
                    <a href="{{ route('products') }}" class="text-[11px] font-medium tracking-[0.12em] uppercase text-gray-800 hover:text-black transition-colors">Collections</a>
                    <a href="{{ route('home') }}#products" class="text-[11px] font-medium tracking-[0.12em] uppercase text-gray-800 hover:text-black transition-colors">New Arrivals</a>
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" class="md:hidden p-2 text-gray-800 focus:outline-none" data-mobile-menu-toggle>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Logo -->
                <a href="/" class="absolute left-1/2 -translate-x-1/2 text-[14px] sm:text-[18px] md:text-[22px] font-semibold tracking-[0.2em] uppercase text-gray-900 whitespace-nowrap">
                    PureFit <span class="hidden sm:inline">Apparel </span>
                </a>

                <!-- Right Links -->
                <div class="flex items-center gap-3.5 sm:gap-5">

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
                        <div class="relative">
                            <button type="button" data-account-toggle class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-800 hover:text-black transition-colors flex items-center gap-1 focus:outline-none p-2 -m-2">
                                <span class="hidden sm:inline">Account</span>
                                <svg class="w-5 h-5 sm:hidden pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                <svg class="w-3 h-3 hidden sm:inline pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                            </button>
                            <div data-account-dropdown class="absolute right-0 top-full mt-2 w-56 bg-white border border-[#e8e5e0] shadow-lg opacity-0 invisible transition-all z-50 rounded-xl overflow-hidden">
                                <!-- User Info Header -->
                                <div class="px-4 py-3 border-b border-[#e8e5e0] bg-[#f5f3ef]/40">
                                    <p class="text-[12px] font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                    
                                </div>
                                @if(auth()->user()->isAdmin())
                                    <a href="/" class="block px-4 py-3 text-[11px] font-medium tracking-[0.1em] uppercase text-gray-800 hover:bg-[#f5f3ef]">Dashboard</a>
                                @elseif(auth()->user()->isBusiness())
                                    <a href="{{ route('business.dashboard') }}" class="block px-4 py-3 text-[11px] font-medium tracking-[0.1em] uppercase text-gray-800 hover:bg-[#f5f3ef]">Dashboard</a>
                                @else
                                    <a href="/" class="block px-4 py-3 text-[11px] font-medium tracking-[0.1em] uppercase text-gray-800 hover:bg-[#f5f3ef]">Dashboard</a>
                                @endif
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
            
            <div class="px-6 py-4 space-y-1">
                <a href="/" class="block py-2 text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-800 hover:text-black transition-colors">Home</a>
                <a href="{{ route('products') }}" class="block py-2 text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-800 hover:text-black transition-colors">Collections</a>
                <a href="{{ route('home') }}#products" class="block py-2 text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-800 hover:text-black transition-colors">New Arrivals</a>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif
    @if(session('info'))
        <div class="max-w-[1400px] mx-auto px-6 lg:px-10 mt-4">
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-xl flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="text-sm font-medium">{{ session('info') }}</span>
            </div>
        </div>
    @endif

    @yield('content')

    @stack('scripts')

    <!-- Layout JavaScript -->
    <script src="{{ asset('js/layout.js') }}"></script>

</body>
</html>
