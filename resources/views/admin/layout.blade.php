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
                <p class="text-[10px] font-bold tracking-[0.15em] uppercase text-gray-400 mb-4 px-3">Control Center</p>

                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-gray-800 rounded hover:bg-[#f5f3ef] transition-colors @yield('nav-overview', '')">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.orders') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-gray-500 rounded hover:bg-[#f5f3ef] hover:text-gray-800 transition-colors @yield('nav-orders', '')">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0H6m15 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h-.008M8.25 12a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h-.008m6.75-7.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h-.008M12.75 12a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h-.008m-1.125-7.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h-.008"/></svg>
                        Orders
                    </a>
                    <a href="{{ route('admin.products') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-gray-500 rounded hover:bg-[#f5f3ef] hover:text-gray-800 transition-colors @yield('nav-products', '')">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                        Products
                    </a>
                    <a href="{{ route('admin.reviews') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-gray-500 rounded hover:bg-[#f5f3ef] hover:text-gray-800 transition-colors @yield('nav-reviews', '')">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499c.19-.443.805-.443.99 0l1.98 4.542 4.96.486c.484.047.678.647.318.974l-3.69 3.355 1.01 4.886c.1.485-.41.86-.818.598L10 15.688l-4.27 2.45c-.407.234-.917-.11-.818-.598l1.01-4.886-3.69-3.355c-.36-.327-.166-.927.318-.974l4.96-.486 1.98-4.542Z"/></svg>
                        Reviews
                    </a>
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-gray-500 rounded hover:bg-[#f5f3ef] hover:text-gray-800 transition-colors @yield('nav-users', '')">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                        Users
                    </a>
                    <a href="{{ route('admin.messages') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-gray-500 rounded hover:bg-[#f5f3ef] hover:text-gray-800 transition-colors @yield('nav-messages', '')">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3h9m-9 3h3m-6.75 4.125a3 3 0 0 0 3 3h7.5a3 3 0 0 0 3-3V6.75a3 3 0 0 0-3-3h-7.5a3 3 0 0 0-3 3v11.625Z"/></svg>
                        Messages
                    </a>
                    <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3 py-2.5 text-[13px] font-medium text-gray-500 rounded hover:bg-[#f5f3ef] hover:text-gray-800 transition-colors @yield('nav-settings', '')">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.352c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.991l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.87l-.212 1.281c-.09.543-.56.94-1.11.94h-2.352c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.375-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.991l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.75.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.87l.213-1.281Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        Settings
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
