<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') — PureFit Apparel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-serif-display { font-family: 'Instrument Serif', serif; }
        .font-sans-body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="font-sans-body text-gray-900 antialiased bg-[#f5f3ef] min-h-screen">

    <!-- Top Bar -->
    <nav class="bg-[#111] text-white fixed top-0 left-0 right-0 z-50">
        <div class="max-w-full mx-auto px-6">
            <div class="flex items-center justify-between h-[60px]">
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-[16px] font-semibold tracking-[0.2em] uppercase">
                        PureFit Apparel
                    </a>
                    <span class="text-[11px] font-medium tracking-[0.1em] uppercase text-gray-400 hidden sm:inline">Admin Panel</span>
                </div>
                <div class="flex items-center gap-5">
                    <span class="text-[13px] text-gray-300 hidden sm:inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-400 hover:text-white transition-colors bg-transparent border-none cursor-pointer">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex pt-[60px] min-h-screen">
        <!-- Sidebar -->
        <aside class="w-[260px] bg-white border-r border-[#e8e5e0] fixed top-[60px] bottom-0 left-0 overflow-y-auto z-40 hidden md:block">
            <div class="px-5 py-6">
                <!-- Overview Section -->
                <p class="text-[10px] font-bold tracking-[0.15em] uppercase text-gray-400 mb-4 px-3">Overview</p>
                <nav class="space-y-1 mb-8">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium text-gray-500 rounded hover:bg-[#f5f3ef] hover:text-gray-800 transition-colors @yield('nav-overview', '')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z"/></svg>
                        Dashboard
                    </a>
                </nav>

                <!-- Operations Section -->
                <p class="text-[10px] font-bold tracking-[0.15em] uppercase text-gray-400 mb-4 px-3">Operations</p>
                <nav class="space-y-1 mb-8">
                    <a href="{{ route('admin.orders') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium text-gray-500 rounded hover:bg-[#f5f3ef] hover:text-gray-800 transition-colors @yield('nav-orders', '')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                        Orders
                    </a>
                    <a href="{{ route('admin.products') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium text-gray-500 rounded hover:bg-[#f5f3ef] hover:text-gray-800 transition-colors @yield('nav-products', '')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                        Products
                    </a>
                </nav>

                <!-- Management Section -->
                <p class="text-[10px] font-bold tracking-[0.15em] uppercase text-gray-400 mb-4 px-3">Management</p>
                <nav class="space-y-1 mb-8">
                    <a href="{{ route('admin.businesses') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium text-gray-500 rounded hover:bg-[#f5f3ef] hover:text-gray-800 transition-colors @yield('nav-business', '')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.651V9.35m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.721"/></svg>
                        Businesses
                    </a>
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium text-gray-500 rounded hover:bg-[#f5f3ef] hover:text-gray-800 transition-colors @yield('nav-users', '')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                        Users
                    </a>
                </nav>

                <!-- Communication Section -->
                <p class="text-[10px] font-bold tracking-[0.15em] uppercase text-gray-400 mb-4 px-3">Communication</p>
                <nav class="space-y-1 mb-8">
                    <a href="{{ route('admin.messages') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium text-gray-500 rounded hover:bg-[#f5f3ef] hover:text-gray-800 transition-colors @yield('nav-messages', '')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3h9m-9 3h3m-6.75 4.125a3 3 0 0 0 3 3h7.5a3 3 0 0 0 3-3V6.75a3 3 0 0 0-3-3h-7.5a3 3 0 0 0-3 3v11.625Z"/></svg>
                        Messages
                    </a>
                </nav>

                <!-- System Section -->
                <p class="text-[10px] font-bold tracking-[0.15em] uppercase text-gray-400 mb-4 px-3">System</p>
                <nav class="space-y-1">
                    <a href="{{ route('admin.transactions') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium text-gray-500 rounded hover:bg-[#f5f3ef] hover:text-gray-800 transition-colors @yield('nav-transactions', '')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        Audit Logs
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 md:ml-[260px] p-6 lg:p-10">
            @yield('content')
        </main>
    </div>

</body>
</html>
