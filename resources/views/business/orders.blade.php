@extends('business.layout')

@section('title', 'Orders')
@section('nav-orders', 'bg-[#f5f3ef] text-gray-900')

@section('content')

    <!-- Header -->
    <div class="mb-8">
        <h1 class="font-serif-display text-[36px] text-gray-900 mb-2">Orders</h1>
        <p class="text-[14px] text-gray-600">Manage incoming orders and shipments.</p>
    </div>

    <!-- Retail Orders -->
    <div class="bg-white border border-[#e8e5e0] mb-10">
        <div class="px-6 py-4 border-b border-[#e8e5e0]">
            <h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">Retail Orders</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-[#e8e5e0] bg-[#faf9f7]">
                        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Order ID</th>
                        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Customer</th>
                        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Total</th>
                        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Status</th>
                        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Date</th>
                        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($retailOrders as $order)
                        <tr class="border-b border-[#f0ede8] hover:bg-[#faf9f7] transition-colors">
                            <td class="px-6 py-4 text-[14px]">#{{ $order->id }}</td>
                            <td class="px-6 py-4 text-[14px]">
                                <div>{{ $order->buyer->name ?? '—' }}</div>
                                <div class="text-[12px] text-gray-500">{{ $order->buyer->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-[14px] font-medium">₱{{ number_format($order->total, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-0.5 text-[11px] font-semibold uppercase rounded-full {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : ($order->status == 'processing' ? 'bg-blue-100 text-blue-800' : ($order->status == 'shipped' ? 'bg-purple-100 text-purple-800' : ($order->status == 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))) }}">{{ $order->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($order->status === 'cancelled')
                                        <span class="inline-flex px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wider border border-red-200 bg-red-50 text-red-700">
                                            Cancelled
                                        </span>
                                    @else
                                        @php
                                            $states = ['pending', 'processing', 'shipped', 'delivered'];
                                            $currentIndex = array_search($order->status, $states);
                                        @endphp
                                        <form method="POST" action="{{ route('business.orders.status', $order) }}" class="inline m-0 p-0">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" data-current="{{ $order->status }}" onchange="handleStatusChange(this)" class="text-[11px] font-semibold tracking-wider uppercase border border-[#e8e5e0] rounded px-2.5 py-1.5 bg-[#f5f3ef] hover:border-black transition-all cursor-pointer text-gray-800 select-none">
                                                @foreach($states as $index => $state)
                                                    @php
                                                        $label = match($state) {
                                                            'pending' => 'Pending',
                                                            'processing' => 'Process',
                                                            'shipped' => 'Ship',
                                                            'delivered' => 'Deliver',
                                                        };
                                                        
                                                        $optionDisabled = ($index != $currentIndex && $index != $currentIndex + 1);
                                                    @endphp
                                                    <option value="{{ $state }}" {{ $order->status == $state ? 'selected' : '' }} {{ $optionDisabled ? 'disabled' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                                @if(in_array($order->status, ['pending', 'processing']))
                                                    <option value="cancelled">Cancel Order</option>
                                                @endif
                                            </select>
                                        </form>
                                    @endif

                                    <button type="button" onclick="toggleDetails({{ $order->id }})" class="text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500 hover:text-black border border-[#e8e5e0] hover:border-black px-2.5 py-1 bg-white transition-colors cursor-pointer select-none">
                                        Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr id="details-{{ $order->id }}" class="hidden bg-[#faf9f7]">
                            <td colspan="6" class="px-6 py-4 border-b border-[#f0ede8]">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <!-- Order Items -->
                                    <div>
                                        <div class="text-[11px] font-semibold tracking-wider uppercase text-gray-500 mb-3">Order Items</div>
                                        <div class="space-y-2">
                                            @foreach($order->items as $item)
                                                <div class="flex justify-between items-start text-[13px]">
                                                    <div>
                                                        <span class="font-medium text-gray-900">{{ $item->product->name ?? 'Unknown Product' }}</span>
                                                        @if($item->variant_name)
                                                            <span class="text-[11px] text-gray-500 block">{{ $item->variant_name }}</span>
                                                        @endif
                                                        <span class="text-gray-500 text-[11px]">Qty: {{ $item->quantity }} × ₱{{ number_format($item->price ?? $item->unit_price, 2) }}</span>
                                                    </div>
                                                    <span class="font-medium text-gray-900">₱{{ number_format(($item->price ?? $item->unit_price) * $item->quantity, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="flex justify-between items-center pt-3 mt-3 border-t border-[#e8e5e0]">
                                            <span class="text-[12px] font-semibold text-gray-700">Subtotal:</span>
                                            <span class="text-[13px] font-medium text-gray-900">₱{{ number_format($order->subtotal, 2) }}</span>
                                        </div>
                                        @if($order->discount_total > 0)
                                        <div class="flex justify-between items-center pt-1">
                                            <span class="text-[12px] text-green-700 font-semibold">Discount:</span>
                                            <span class="text-[13px] font-medium text-green-700">-₱{{ number_format($order->discount_total, 2) }}</span>
                                        </div>
                                        @endif
                                        <div class="flex justify-between items-center pt-1">
                                            <span class="text-[12px] text-gray-600">Shipping Fee:</span>
                                            <span class="text-[13px] font-medium text-gray-900">₱{{ number_format($order->shipping_fee, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pt-1">
                                            <span class="text-[12px] text-gray-600">Platform Fee:</span>
                                            <span class="text-[13px] font-medium text-gray-900">₱{{ number_format($order->platform_fee, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pt-2 mt-2 border-t border-[#e8e5e0]">
                                            <span class="text-[12px] font-bold text-gray-800">Total:</span>
                                            <span class="text-[14px] font-bold text-gray-900">₱{{ number_format($order->total, 2) }}</span>
                                        </div>
                                    </div>

                                    <!-- Shipping & Customer Checkout Info -->
                                    <div class="border-t md:border-t-0 md:border-l border-[#e8e5e0] pt-6 md:pt-0 md:pl-8">
                                        <div class="text-[11px] font-semibold tracking-wider uppercase text-gray-500 mb-3">Delivery Information</div>
                                        @if($order->shipping_address)
                                            <div class="text-[13px] text-gray-800 space-y-1">
                                                <p><span class="text-gray-500 font-medium">Recipient:</span> <strong class="text-gray-900">{{ $order->shipping_address['name'] ?? '—' }}</strong></p>
                                                <p><span class="text-gray-500 font-medium">Address:</span> {{ $order->shipping_address['line1'] ?? '' }}</p>
                                                <p><span class="text-gray-500 font-medium">City/State:</span> {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal'] ?? '' }}</p>
                                                @if(!empty($order->shipping_address['contact']))
                                                    <p><span class="text-gray-500 font-medium">Contact Number:</span> <strong class="text-gray-900">{{ $order->shipping_address['contact'] }}</strong></p>
                                                @elseif(!empty($order->shipping_address['country']))
                                                    <p><span class="text-gray-500 font-medium">Country:</span> {{ $order->shipping_address['country'] }}</p>
                                                @endif
                                                @php
                                                    $paymentMethod = $order->payments->first() ? $order->payments->first()->method : null;
                                                    $paymentMethodName = match($paymentMethod) {
                                                        'stripe' => 'Credit Card',
                                                        'paypal' => 'PayPal',
                                                        'bank_transfer' => 'Bank Transfer',
                                                        'cod' => 'Cash on Delivery',
                                                        default => $paymentMethod ? ucfirst($paymentMethod) : 'N/A'
                                                    };
                                                @endphp
                                                <p class="mt-1"><span class="text-gray-500 font-medium">Payment Method:</span> <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium bg-[#f5f3ef] text-gray-850 border border-[#ddd8d0] uppercase tracking-wider">{{ $paymentMethodName }}</span></p>
                                            </div>
                                        @else
                                            <p class="text-[12px] text-gray-500 italic">No delivery details provided.</p>
                                        @endif

                                        @if($order->notes)
                                            <div class="mt-4 pt-4 border-t border-[#e8e5e0]">
                                                <div class="text-[11px] font-semibold tracking-wider uppercase text-gray-500 mb-1">Order Notes</div>
                                                <p class="text-[12px] text-gray-600 italic bg-[#f5f3ef] p-2.5 border border-[#e8e5e0]">"{{ $order->notes }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-[14px] text-gray-500">
                                No retail orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- B2B Orders -->
    <div class="bg-white border border-[#e8e5e0]">
        <div class="px-6 py-4 border-b border-[#e8e5e0]">
            <h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">Wholesale Orders</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-[#e8e5e0] bg-[#faf9f7]">
                        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Order ID</th>
                        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Customer</th>
                        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Total</th>
                        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Status</th>
                        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Date</th>
                        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($b2bOrders as $order)
                        <tr class="border-b border-[#f0ede8] hover:bg-[#faf9f7] transition-colors">
                            <td class="px-6 py-4 text-[14px]">#{{ $order->id }}</td>
                            <td class="px-6 py-4 text-[14px]">
                                <div>{{ $order->buyer->name ?? '—' }}</div>
                                <div class="text-[12px] text-gray-500">{{ $order->buyer->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-[14px] font-medium">₱{{ number_format($order->total, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-0.5 text-[11px] font-semibold uppercase rounded-full {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : ($order->status == 'processing' ? 'bg-blue-100 text-blue-800' : ($order->status == 'shipped' ? 'bg-purple-100 text-purple-800' : ($order->status == 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))) }}">{{ $order->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-[13px] text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($order->status === 'cancelled')
                                        <span class="inline-flex px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wider border border-red-200 bg-red-50 text-red-700">
                                            Cancelled
                                        </span>
                                    @else
                                        @php
                                            $states = ['pending', 'processing', 'shipped', 'delivered'];
                                            $currentIndex = array_search($order->status, $states);
                                        @endphp
                                        <form method="POST" action="{{ route('business.orders.status', $order) }}" class="inline m-0 p-0">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" data-current="{{ $order->status }}" onchange="handleStatusChange(this)" class="text-[11px] font-semibold tracking-wider uppercase border border-[#e8e5e0] rounded px-2.5 py-1.5 bg-[#f5f3ef] hover:border-black transition-all cursor-pointer text-gray-800 select-none">
                                                @foreach($states as $index => $state)
                                                    @php
                                                        $label = match($state) {
                                                            'pending' => 'Pending',
                                                            'processing' => 'Process',
                                                            'shipped' => 'Ship',
                                                            'delivered' => 'Deliver',
                                                        };
                                                        
                                                        $optionDisabled = ($index != $currentIndex && $index != $currentIndex + 1);
                                                    @endphp
                                                    <option value="{{ $state }}" {{ $order->status == $state ? 'selected' : '' }} {{ $optionDisabled ? 'disabled' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                                @if(in_array($order->status, ['pending', 'processing']))
                                                    <option value="cancelled">Cancel Order</option>
                                                @endif
                                            </select>
                                        </form>
                                    @endif

                                    <button type="button" onclick="toggleDetails({{ $order->id }})" class="text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500 hover:text-black border border-[#e8e5e0] hover:border-black px-2.5 py-1 bg-white transition-colors cursor-pointer select-none">
                                        Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr id="details-{{ $order->id }}" class="hidden bg-[#faf9f7]">
                            <td colspan="6" class="px-6 py-4 border-b border-[#f0ede8]">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <!-- Order Items -->
                                    <div>
                                        <div class="text-[11px] font-semibold tracking-wider uppercase text-gray-500 mb-3">Order Items</div>
                                        <div class="space-y-2">
                                            @foreach($order->items as $item)
                                                <div class="flex justify-between items-start text-[13px]">
                                                    <div>
                                                        <span class="font-medium text-gray-900">{{ $item->product->name ?? 'Unknown Product' }}</span>
                                                        @if($item->variant_name)
                                                            <span class="text-[11px] text-gray-500 block">{{ $item->variant_name }}</span>
                                                        @endif
                                                        <span class="text-gray-500 text-[11px]">Qty: {{ $item->quantity }} × ₱{{ number_format($item->price ?? $item->unit_price, 2) }}</span>
                                                    </div>
                                                    <span class="font-medium text-gray-900">₱{{ number_format(($item->price ?? $item->unit_price) * $item->quantity, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="flex justify-between items-center pt-3 mt-3 border-t border-[#e8e5e0]">
                                            <span class="text-[12px] font-semibold text-gray-700">Subtotal:</span>
                                            <span class="text-[13px] font-medium text-gray-900">₱{{ number_format($order->subtotal, 2) }}</span>
                                        </div>
                                        @if($order->discount_total > 0)
                                        <div class="flex justify-between items-center pt-1">
                                            <span class="text-[12px] text-green-700 font-semibold">Discount:</span>
                                            <span class="text-[13px] font-medium text-green-700">-₱{{ number_format($order->discount_total, 2) }}</span>
                                        </div>
                                        @endif
                                        <div class="flex justify-between items-center pt-1">
                                            <span class="text-[12px] text-gray-600">Shipping Fee:</span>
                                            <span class="text-[13px] font-medium text-gray-900">₱{{ number_format($order->shipping_fee, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pt-1">
                                            <span class="text-[12px] text-gray-600">Platform Fee:</span>
                                            <span class="text-[13px] font-medium text-gray-900">₱{{ number_format($order->platform_fee, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center pt-2 mt-2 border-t border-[#e8e5e0]">
                                            <span class="text-[12px] font-bold text-gray-800">Total:</span>
                                            <span class="text-[14px] font-bold text-gray-900">₱{{ number_format($order->total, 2) }}</span>
                                        </div>
                                    </div>

                                    <!-- Shipping & Customer Checkout Info -->
                                    <div class="border-t md:border-t-0 md:border-l border-[#e8e5e0] pt-6 md:pt-0 md:pl-8">
                                        <div class="text-[11px] font-semibold tracking-wider uppercase text-gray-500 mb-3">Delivery Information</div>
                                        @if($order->shipping_address)
                                            <div class="text-[13px] text-gray-800 space-y-1">
                                                <p><span class="text-gray-500 font-medium">Recipient:</span> <strong class="text-gray-900">{{ $order->shipping_address['name'] ?? '—' }}</strong></p>
                                                <p><span class="text-gray-500 font-medium">Address:</span> {{ $order->shipping_address['line1'] ?? '' }}</p>
                                                <p><span class="text-gray-500 font-medium">City/State:</span> {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal'] ?? '' }}</p>
                                                @if(!empty($order->shipping_address['contact']))
                                                    <p><span class="text-gray-500 font-medium">Contact Number:</span> <strong class="text-gray-900">{{ $order->shipping_address['contact'] }}</strong></p>
                                                @elseif(!empty($order->shipping_address['country']))
                                                    <p><span class="text-gray-500 font-medium">Country:</span> {{ $order->shipping_address['country'] }}</p>
                                                @endif
                                                @php
                                                    $paymentMethod = $order->payments->first() ? $order->payments->first()->method : null;
                                                    $paymentMethodName = match($paymentMethod) {
                                                        'stripe' => 'Credit Card',
                                                        'paypal' => 'PayPal',
                                                        'bank_transfer' => 'Bank Transfer',
                                                        'cod' => 'Cash on Delivery',
                                                        default => $paymentMethod ? ucfirst($paymentMethod) : 'N/A'
                                                    };
                                                @endphp
                                                <p class="mt-1"><span class="text-gray-500 font-medium">Payment Method:</span> <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium bg-[#f5f3ef] text-gray-850 border border-[#ddd8d0] uppercase tracking-wider">{{ $paymentMethodName }}</span></p>
                                            </div>
                                        @else
                                            <p class="text-[12px] text-gray-500 italic">No delivery details provided.</p>
                                        @endif

                                        @if($order->notes)
                                            <div class="mt-4 pt-4 border-t border-[#e8e5e0]">
                                                <div class="text-[11px] font-semibold tracking-wider uppercase text-gray-500 mb-1">Order Notes</div>
                                                <p class="text-[12px] text-gray-600 italic bg-[#f5f3ef] p-2.5 border border-[#e8e5e0]">"{{ $order->notes }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-[14px] text-gray-500">
                                No B2B orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function toggleDetails(orderId) {
        const row = document.getElementById('details-' + orderId);
        if (row) {
            row.classList.toggle('hidden');
        }
    }

    function handleStatusChange(selectEl) {
        if (selectEl.value === 'cancelled') {
            if (!confirm('Are you sure you want to cancel this order?')) {
                selectEl.value = selectEl.getAttribute('data-current');
                return;
            }
        }
        selectEl.form.submit();
    }
    </script>
@endsection
