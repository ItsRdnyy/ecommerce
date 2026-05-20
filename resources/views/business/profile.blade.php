@extends('business.layout')

@section('title', 'Business Profile')
@section('nav-profile', 'bg-[#f5f3ef] text-gray-900')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="font-serif-display text-[36px] text-gray-900 mb-2">Business Profile</h1>
        <p class="text-[14px] text-gray-600">Manage your business information and branding</p>
    </div>

    <!-- Profile Form -->
    <div class="bg-white border border-[#e8e5e0] max-w-2xl">
        <div class="px-6 py-4 border-b border-[#e8e5e0]">
            <h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">Business Information</h2>
        </div>
        
        <form method="POST" action="{{ route('business.profile.update') }}" enctype="multipart/form-data" class="p-6">
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

            @error('business_name')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-[13px]">
                    {{ $message }}
                </div>
            @enderror

            <!-- Logo Upload -->
            <div class="mb-6">
                <label class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-2">Business Logo</label>
                <div class="flex items-center gap-4">
                    @if($business && $business->logo)
                        <img src="{{ asset('storage/' . $business->logo) }}" alt="Business Logo" class="w-20 h-20 object-cover rounded border border-[#e8e5e0]">
                    @else
                        <div class="w-20 h-20 bg-[#f5f3ef] rounded border border-[#e8e5e0] flex items-center justify-center text-gray-400 text-[12px]">
                            No Logo
                        </div>
                    @endif
                    <div>
                        <input type="file" name="logo" id="logo" accept="image/jpeg,image/jpg,image/png,image/webp" class="block w-full text-[13px] text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:text-[12px] file:font-semibold file:bg-[#f5f3ef] file:text-gray-700 hover:file:bg-[#e8e5e0]">
                        <p class="text-[11px] text-gray-400 mt-1">Recommended: Square image, max 10MB</p>
                    </div>
                </div>
            </div>

            <!-- Business Name -->
            <div class="mb-6">
                <label for="business_name" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-2">Business Name</label>
                <input type="text" name="business_name" id="business_name" value="{{ old('business_name', $business->business_name ?? '') }}" class="w-full px-4 py-3 border border-[#e8e5e0] text-[14px] text-gray-900 focus:outline-none focus:border-gray-900 transition-colors" required>
            </div>

            <!-- Business Address -->
            <div class="mb-6">
                <label for="business_address" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-2">Business Address</label>
                <input type="text" name="business_address" id="business_address" value="{{ old('business_address', $business->business_address ?? '') }}" class="w-full px-4 py-3 border border-[#e8e5e0] text-[14px] text-gray-900 focus:outline-none focus:border-gray-900 transition-colors" placeholder="Street address, City, Province">
            </div>

            <!-- Business Phone -->
            <div class="mb-6">
                <label for="business_phone" class="block text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-2">Business Phone</label>
                <input type="text" name="business_phone" id="business_phone" value="{{ old('business_phone', $business->business_phone ?? '') }}" class="w-full px-4 py-3 border border-[#e8e5e0] text-[14px] text-gray-900 focus:outline-none focus:border-gray-900 transition-colors" placeholder="+63 XXX XXX XXXX">
            </div>


            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" class="px-6 py-3 bg-[#111] text-white text-[12px] font-semibold tracking-[0.1em] uppercase hover:bg-gray-800 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection
