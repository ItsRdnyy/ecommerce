@extends('admin.layout')
@section('title', 'Order System')
@section('nav-orders', 'bg-[#f5f3ef] text-gray-900')
@section('content')
<div class="mb-8"><h1 class="font-serif-display text-[36px] text-gray-900 mb-2">Order System</h1><p class="text-[14px] text-gray-600">Retail and B2B orders split view.</p></div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white border border-[#e8e5e0] px-6 py-5"><p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Retail Orders</p><p class="text-[28px] font-light text-gray-900">{{ $retailOrders->count() }}</p></div>
    <div class="bg-white border border-[#e8e5e0] px-6 py-5"><p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">B2B Orders</p><p class="text-[28px] font-light text-gray-900">{{ $b2bOrders->count() }}</p></div>
    <div class="bg-white border border-[#e8e5e0] px-6 py-5"><p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Processing</p><p class="text-[28px] font-light text-gray-900">{{ $retailOrders->where('status','processing')->count()+$b2bOrders->where('status','processing')->count() }}</p></div>
    <div class="bg-white border border-[#e8e5e0] px-6 py-5"><p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Pending</p><p class="text-[28px] font-light text-gray-900">{{ $retailOrders->where('status','pending')->count()+$b2bOrders->where('status','pending')->count() }}</p></div>
</div>
<div class="bg-white border border-[#e8e5e0] mb-6">
    <div class="px-6 py-4 border-b border-[#e8e5e0]"><h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">Retail Orders (1–3 pcs)</h2></div>
    <div class="overflow-x-auto"><table class="w-full text-left"><thead><tr class="border-b border-[#e8e5e0] bg-[#faf9f7]">
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">ID</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Buyer</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Business</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Total</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Status</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Date</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Action</th>
    </tr></thead><tbody>
    @forelse($retailOrders as $o)
    <tr class="border-b border-[#f0ede8] hover:bg-[#faf9f7]">
        <td class="px-6 py-4 text-[14px]">#{{ $o->id }}</td>
        <td class="px-6 py-4 text-[14px]">{{ $o->buyer->name ?? '—' }}</td>
        <td class="px-6 py-4 text-[14px]">{{ $o->business->name ?? '—' }}</td>
        <td class="px-6 py-4 text-[14px] font-medium">₱{{ number_format($o->total,2) }}</td>
        <td class="px-6 py-4"><span class="inline-flex px-2 py-0.5 text-[11px] font-semibold uppercase rounded-full {{ $o->status=='pending'?'bg-yellow-100 text-yellow-800':($o->status=='processing'?'bg-blue-100 text-blue-800':($o->status=='shipped'?'bg-purple-100 text-purple-800':($o->status=='delivered'?'bg-green-100 text-green-800':'bg-gray-100 text-gray-800'))) }}">{{ $o->status }}</span></td>
        <td class="px-6 py-4 text-[13px] text-gray-500">{{ $o->created_at->format('M d, Y') }}</td>
        <td class="px-6 py-4">
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.orders.status', $o) }}" class="inline">@csrf @method('PATCH')
                    <select name="status" onchange="this.form.submit()" class="text-[12px] border border-[#e8e5e0] rounded px-2 py-1 bg-white cursor-pointer">
                        <option value="pending" {{ $o->status=='pending'?'selected':'' }}>Pending</option>
                        <option value="processing" {{ $o->status=='processing'?'selected':'' }}>Processing</option>
                        <option value="shipped" {{ $o->status=='shipped'?'selected':'' }}>Shipped</option>
                        <option value="delivered" {{ $o->status=='delivered'?'selected':'' }}>Delivered</option>
                        <option value="cancelled" {{ $o->status=='cancelled'?'selected':'' }}>Cancelled</option>
                    </select>
                </form>
                <button type="button" onclick="toggleDetails({{ $o->id }})" class="text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500 hover:text-black border border-[#e8e5e0] hover:border-black px-2.5 py-1 bg-white transition-colors cursor-pointer">
                    Details
                </button>
            </div>
        </td>
    </tr>
    <tr id="details-{{ $o->id }}" class="hidden bg-[#faf9f7] border-b border-[#f0ede8]">
        <td colspan="7" class="px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Shipping Address -->
                <div>
                    <h4 class="text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-2">Shipping Address</h4>
                    @if($o->shipping_address)
                        <p class="text-[13px] font-medium text-gray-900">{{ $o->shipping_address['name'] ?? '—' }}</p>
                        <p class="text-[12px] text-gray-600 mt-1 leading-relaxed">
                            {{ $o->shipping_address['line1'] ?? '' }}<br>
                            {{ $o->shipping_address['city'] ?? '' }}, {{ $o->shipping_address['state'] ?? '' }} {{ $o->shipping_address['postal'] ?? '' }}<br>
                            {{ $o->shipping_address['country'] ?? '' }}
                        </p>
                    @else
                        <p class="text-[12px] text-gray-500">No shipping address provided.</p>
                    @endif
                </div>

                <!-- Billing Address -->
                <div>
                    <h4 class="text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-2">Billing Address</h4>
                    @if($o->billing_address)
                        <p class="text-[13px] font-medium text-gray-900">{{ $o->billing_address['name'] ?? '—' }}</p>
                        <p class="text-[12px] text-gray-600 mt-1 leading-relaxed">
                            {{ $o->billing_address['line1'] ?? '' }}<br>
                            {{ $o->billing_address['city'] ?? '' }}, {{ $o->billing_address['state'] ?? '' }} {{ $o->billing_address['postal'] ?? '' }}<br>
                            {{ $o->billing_address['country'] ?? '' }}
                        </p>
                    @else
                        <p class="text-[12px] text-gray-500">No billing address provided.</p>
                    @endif
                </div>

                <!-- Payment & Meta -->
                <div>
                    <h4 class="text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-2">Meta & Notes</h4>
                    <p class="text-[12px] text-gray-600"><span class="font-semibold text-gray-500 uppercase text-[9px] tracking-wider">Payment Method:</span> 
                        {{ $o->payments->first() ? ucfirst($o->payments->first()->method) : 'N/A' }}
                    </p>
                    <p class="text-[12px] text-gray-600 mt-1"><span class="font-semibold text-gray-500 uppercase text-[9px] tracking-wider">Estimated Delivery:</span> 
                        {{ $o->estimated_delivery_date ? \Carbon\Carbon::parse($o->estimated_delivery_date)->format('M d, Y') : 'N/A' }}
                    </p>
                    @if($o->notes)
                        <div class="mt-2 p-2 border border-[#e8e5e0] bg-white text-[12px] text-gray-600 italic">
                            "{{ $o->notes }}"
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ordered Items Table -->
            <div class="border-t border-[#e8e5e0] pt-4">
                <h4 class="text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-3">Order Items</h4>
                <div class="bg-white border border-[#e8e5e0] rounded overflow-hidden">
                    <table class="w-full text-left text-[12px]">
                        <thead>
                            <tr class="bg-[#faf9f7] border-b border-[#e8e5e0] text-gray-500 text-[10px] uppercase tracking-wider font-semibold">
                                <th class="px-4 py-2">Product</th>
                                <th class="px-4 py-2">Variant</th>
                                <th class="px-4 py-2 text-right">Price</th>
                                <th class="px-4 py-2 text-center">Qty</th>
                                <th class="px-4 py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($o->items as $item)
                            <tr class="border-b border-[#f0ede8] last:border-b-0">
                                <td class="px-4 py-2.5 font-medium text-gray-900">{{ $item->product->name }}</td>
                                <td class="px-4 py-2.5 text-gray-500">{{ $item->variant_name ?? '—' }}</td>
                                <td class="px-4 py-2.5 text-right">₱{{ number_format($item->price, 2) }}</td>
                                <td class="px-4 py-2.5 text-center">{{ $item->quantity }}</td>
                                <td class="px-4 py-2.5 text-right font-medium text-gray-900">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </td>
    </tr>
    @empty<tr><td colspan="7" class="px-6 py-10 text-center text-[14px] text-gray-500">No retail orders.</td></tr>@endforelse
    </tbody></table></div>
</div>
<div class="bg-white border border-[#e8e5e0]">
    <div class="px-6 py-4 border-b border-[#e8e5e0]"><h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">B2B Bulk Orders</h2></div>
    <div class="overflow-x-auto"><table class="w-full text-left"><thead><tr class="border-b border-[#e8e5e0] bg-[#faf9f7]">
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">ID</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Buyer</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Business</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Total</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Status</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Date</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Action</th>
    </tr></thead><tbody>
    @forelse($b2bOrders as $o)
    <tr class="border-b border-[#f0ede8] hover:bg-[#faf9f7]">
        <td class="px-6 py-4 text-[14px]">#{{ $o->id }}</td>
        <td class="px-6 py-4 text-[14px]">{{ $o->buyer->name ?? '—' }}</td>
        <td class="px-6 py-4 text-[14px]">{{ $o->business->name ?? '—' }}</td>
        <td class="px-6 py-4 text-[14px] font-medium">₱{{ number_format($o->total,2) }}</td>
        <td class="px-6 py-4"><span class="inline-flex px-2 py-0.5 text-[11px] font-semibold uppercase rounded-full {{ $o->status=='pending'?'bg-yellow-100 text-yellow-800':($o->status=='processing'?'bg-blue-100 text-blue-800':($o->status=='shipped'?'bg-purple-100 text-purple-800':($o->status=='delivered'?'bg-green-100 text-green-800':'bg-gray-100 text-gray-800'))) }}">{{ $o->status }}</span></td>
        <td class="px-6 py-4 text-[13px] text-gray-500">{{ $o->created_at->format('M d, Y') }}</td>
        <td class="px-6 py-4">
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.orders.status', $o) }}" class="inline">@csrf @method('PATCH')
                    <select name="status" onchange="this.form.submit()" class="text-[12px] border border-[#e8e5e0] rounded px-2 py-1 bg-white cursor-pointer">
                        <option value="pending" {{ $o->status=='pending'?'selected':'' }}>Pending</option>
                        <option value="processing" {{ $o->status=='processing'?'selected':'' }}>Processing</option>
                        <option value="shipped" {{ $o->status=='shipped'?'selected':'' }}>Shipped</option>
                        <option value="delivered" {{ $o->status=='delivered'?'selected':'' }}>Delivered</option>
                        <option value="cancelled" {{ $o->status=='cancelled'?'selected':'' }}>Cancelled</option>
                    </select>
                </form>
                <button type="button" onclick="toggleDetails({{ $o->id }})" class="text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500 hover:text-black border border-[#e8e5e0] hover:border-black px-2.5 py-1 bg-white transition-colors cursor-pointer">
                    Details
                </button>
            </div>
        </td>
    </tr>
    <tr id="details-{{ $o->id }}" class="hidden bg-[#faf9f7] border-b border-[#f0ede8]">
        <td colspan="7" class="px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Shipping Address -->
                <div>
                    <h4 class="text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-2">Shipping Address</h4>
                    @if($o->shipping_address)
                        <p class="text-[13px] font-medium text-gray-900">{{ $o->shipping_address['name'] ?? '—' }}</p>
                        <p class="text-[12px] text-gray-600 mt-1 leading-relaxed">
                            {{ $o->shipping_address['line1'] ?? '' }}<br>
                            {{ $o->shipping_address['city'] ?? '' }}, {{ $o->shipping_address['state'] ?? '' }} {{ $o->shipping_address['postal'] ?? '' }}<br>
                            {{ $o->shipping_address['country'] ?? '' }}
                        </p>
                    @else
                        <p class="text-[12px] text-gray-500">No shipping address provided.</p>
                    @endif
                </div>

                <!-- Billing Address -->
                <div>
                    <h4 class="text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-2">Billing Address</h4>
                    @if($o->billing_address)
                        <p class="text-[13px] font-medium text-gray-900">{{ $o->billing_address['name'] ?? '—' }}</p>
                        <p class="text-[12px] text-gray-600 mt-1 leading-relaxed">
                            {{ $o->billing_address['line1'] ?? '' }}<br>
                            {{ $o->billing_address['city'] ?? '' }}, {{ $o->billing_address['state'] ?? '' }} {{ $o->billing_address['postal'] ?? '' }}<br>
                            {{ $o->billing_address['country'] ?? '' }}
                        </p>
                    @else
                        <p class="text-[12px] text-gray-500">No billing address provided.</p>
                    @endif
                </div>

                <!-- Payment & Meta -->
                <div>
                    <h4 class="text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-2">Meta & Notes</h4>
                    <p class="text-[12px] text-gray-600"><span class="font-semibold text-gray-500 uppercase text-[9px] tracking-wider">Payment Method:</span> 
                        {{ $o->payments->first() ? ucfirst($o->payments->first()->method) : 'N/A' }}
                    </p>
                    <p class="text-[12px] text-gray-600 mt-1"><span class="font-semibold text-gray-500 uppercase text-[9px] tracking-wider">Estimated Delivery:</span> 
                        {{ $o->estimated_delivery_date ? \Carbon\Carbon::parse($o->estimated_delivery_date)->format('M d, Y') : 'N/A' }}
                    </p>
                    @if($o->notes)
                        <div class="mt-2 p-2 border border-[#e8e5e0] bg-white text-[12px] text-gray-600 italic">
                            "{{ $o->notes }}"
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ordered Items Table -->
            <div class="border-t border-[#e8e5e0] pt-4">
                <h4 class="text-[10px] font-semibold tracking-[0.1em] uppercase text-gray-500 mb-3">Order Items</h4>
                <div class="bg-white border border-[#e8e5e0] rounded overflow-hidden">
                    <table class="w-full text-left text-[12px]">
                        <thead>
                            <tr class="bg-[#faf9f7] border-b border-[#e8e5e0] text-gray-500 text-[10px] uppercase tracking-wider font-semibold">
                                <th class="px-4 py-2">Product</th>
                                <th class="px-4 py-2">Variant</th>
                                <th class="px-4 py-2 text-right">Price</th>
                                <th class="px-4 py-2 text-center">Qty</th>
                                <th class="px-4 py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($o->items as $item)
                            <tr class="border-b border-[#f0ede8] last:border-b-0">
                                <td class="px-4 py-2.5 font-medium text-gray-900">{{ $item->product->name }}</td>
                                <td class="px-4 py-2.5 text-gray-500">{{ $item->variant_name ?? '—' }}</td>
                                <td class="px-4 py-2.5 text-right">₱{{ number_format($item->price, 2) }}</td>
                                <td class="px-4 py-2.5 text-center">{{ $item->quantity }}</td>
                                <td class="px-4 py-2.5 text-right font-medium text-gray-900">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </td>
    </tr>
    @empty<tr><td colspan="7" class="px-6 py-10 text-center text-[14px] text-gray-500">No B2B orders.</td></tr>@endforelse
    </tbody></table></div>
</div>

<script>
function toggleDetails(orderId) {
    const row = document.getElementById('details-' + orderId);
    if (row) {
        row.classList.toggle('hidden');
    }
}
</script>
@endsection
