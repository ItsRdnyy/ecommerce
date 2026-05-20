@extends('business.layout')

@section('title', 'Account Settings')
@section('nav-settings', 'bg-[#f5f3ef] text-gray-900')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="font-serif-display text-[36px] text-gray-900 mb-2">Account Settings</h1>
        <p class="text-[14px] text-gray-600">Manage your account security and preferences</p>
    </div>

    <!-- Account Info -->
    <div class="bg-white border border-[#e8e5e0] max-w-2xl mb-6">
        <div class="px-6 py-4 border-b border-[#e8e5e0]">
            <h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">Account Information</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Name</p>
                <p class="text-[14px] text-gray-900">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Email</p>
                <p class="text-[14px] text-gray-900">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Role</p>
                <p class="text-[14px] text-gray-900 capitalize">{{ $user->role }}</p>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="bg-white border border-[#e8e5e0] max-w-2xl">
        <div class="px-6 py-4 border-b border-[#e8e5e0]">
            <h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">Change Password</h2>
        </div>
        
        <form method="POST" action="{{ route('business.settings.password') }}" class="p-6">
            @csrf
            
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-[13px]">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-[13px]">
                    {{ session('error') }}
                </div>
            @endif

            @error('current_password')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-[13px]">
                    {{ $message }}
                </div>
            @enderror

            @error('password')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-[13px]">
                    {{ $message }}
                </div>
            @enderror

            <!-- Current Password -->
            <div class="mb-6">
                <label for="current_password" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-2">Current Password</label>
                <input type="password" name="current_password" id="current_password" class="w-full px-4 py-3 border border-[#e8e5e0] text-[14px] text-gray-900 focus:outline-none focus:border-gray-900 transition-colors" required>
            </div>

            <!-- New Password -->
            <div class="mb-6">
                <label for="password" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-2">New Password</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-3 border border-[#e8e5e0] text-[14px] text-gray-900 focus:outline-none focus:border-gray-900 transition-colors" required>
                <p class="text-[11px] text-gray-400 mt-1">Minimum 8 characters</p>
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-2">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-3 border border-[#e8e5e0] text-[14px] text-gray-900 focus:outline-none focus:border-gray-900 transition-colors" required>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" class="px-6 py-3 bg-[#111] text-white text-[12px] font-semibold tracking-[0.1em] uppercase hover:bg-gray-800 transition-colors">
                    Update Password
                </button>
            </div>
        </form>
    </div>
@endsection
