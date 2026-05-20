@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<section class="bg-[#f5f3ef] min-h-screen py-10">
    <div class="max-w-[1000px] mx-auto px-6 lg:px-10">
        <h1 class="font-serif-display text-[32px] text-gray-900 mb-8">Checkout</h1>

        @if($cart && $cart->items->count())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2">
                <form action="{{ route('checkout.store') }}" method="post" class="space-y-6">
                    @csrf
                    <div class="bg-white border border-[#e8e5e0] p-6">
                        <h2 class="text-[11px] font-semibold tracking-[0.15em] uppercase text-gray-900 mb-5">Shipping Address</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-600 mb-1">Full Name</label>
                                <input type="text" name="shipping_address[name]" required class="w-full bg-transparent border border-[#e8e5e0] text-[13px] py-2 px-3 focus:outline-none focus:border-black">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-600 mb-1">Address </label>
                                <input type="text" name="shipping_address[line1]" required class="w-full bg-transparent border border-[#e8e5e0] text-[13px] py-2 px-3 focus:outline-none focus:border-black">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-600 mb-1">City</label>
                                <input type="text" name="shipping_address[city]" required class="w-full bg-transparent border border-[#e8e5e0] text-[13px] py-2 px-3 focus:outline-none focus:border-black">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-600 mb-1">Province</label>
                                <input type="text" name="shipping_address[state]" required class="w-full bg-transparent border border-[#e8e5e0] text-[13px] py-2 px-3 focus:outline-none focus:border-black">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-600 mb-1">Postal Code</label>
                                <input type="text" name="shipping_address[postal]" required class="w-full bg-transparent border border-[#e8e5e0] text-[13px] py-2 px-3 focus:outline-none focus:border-black">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-600 mb-1">Contact Number</label>
                                <input type="text" name="shipping_address[contact]" required class="w-full bg-transparent border border-[#e8e5e0] text-[13px] py-2 px-3 focus:outline-none focus:border-black">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-[#e8e5e0] p-6">
                        <h2 class="text-[11px] font-semibold tracking-[0.15em] uppercase text-gray-900 mb-5">Payment Method</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach(['stripe'=>'Credit Card','paypal'=>'PayPal','bank_transfer'=>'Bank Transfer','cod'=>'Cash on Delivery'] as $value=>$label)
                            <label class="cursor-pointer border border-[#e8e5e0] p-3 hover:border-black transition-colors has-[:checked]:border-black has-[:checked]:bg-[#f5f3ef]">
                                <input type="radio" name="payment_method" value="{{ $value }}" class="hidden" {{ $loop->first ? 'checked' : '' }}>
                                <span class="text-[11px] font-medium">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white border border-[#e8e5e0] p-6">
                        <label class="block text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-600 mb-1">Order Notes</label>
                        <textarea name="notes" rows="3" class="w-full bg-transparent border border-[#e8e5e0] text-[13px] py-2 px-3 focus:outline-none focus:border-black"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#111] text-white text-[11px] font-semibold tracking-[0.12em] uppercase py-4 hover:bg-gray-800 transition-colors">Place Order</button>
                </form>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white border border-[#e8e5e0] p-6 sticky top-6">
                    <h2 class="text-[11px] font-semibold tracking-[0.15em] uppercase text-gray-900 mb-5">Order Summary</h2>
                    @foreach($cart->items as $item)
                    <div class="flex justify-between text-[12px] py-3 border-b border-[#e8e5e0]">
                        <div>
                            <p class="font-medium text-gray-900">{{ $item->product->name }} x{{ $item->quantity }}</p>
                            <p class="text-gray-500 text-[10px] uppercase tracking-wider">{{ $item->type }}</p>
                            @if($item->discount_amount > 0)
                                <p class="text-green-700 text-[10px] font-semibold">Quantity discount applied (-₱{{ number_format($item->discount_amount, 2) }})</p>
                            @endif
                        </div>
                        <div class="text-right space-y-0.5">
                            @if($item->discount_amount > 0)
                                <p class="text-[10px] text-gray-400 line-through">₱{{ number_format(($item->unit_price * $item->quantity) + $item->discount_amount, 2) }}</p>
                            @endif
                            <p class="font-semibold text-gray-900">₱{{ number_format($item->unit_price * $item->quantity, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                    <div class="flex justify-between text-[13px] pt-3">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">₱{{ number_format($cart->total + $cart->discount_total, 2) }}</span>
                    </div>
                    @if($cart->discount_total > 0)
                    <div class="flex justify-between text-[13px] pt-1">
                        <span class="text-green-700">Discounts</span>
                        <span class="font-medium text-green-700">-₱{{ number_format($cart->discount_total, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-[13px] pt-1">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium">₱{{ number_format($cart->shipping_total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-[18px] font-semibold text-gray-900 pt-4 border-t border-[#e8e5e0] mt-4">
                        <span>Total</span>
                        <span>₱{{ number_format($cart->total + $cart->shipping_total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white border border-[#e8e5e0] p-16 text-center">
            <p class="text-[13px] text-gray-600 mb-6">Your cart is empty.</p>
            <a href="{{ route('products') }}" class="inline-block bg-[#111] text-white text-[11px] font-semibold tracking-[0.12em] uppercase py-3 px-8 hover:bg-gray-800 transition-colors">Continue Shopping</a>
        </div>
        @endif
    </div>
</section>
@endsection
