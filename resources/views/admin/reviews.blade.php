@extends('admin.layout')

@section('title', 'Product Reviews Oversight')
@section('nav-reviews', 'bg-[#f5f3ef] text-gray-900')

@section('content')
<div class="mb-8">
    <h1 class="font-serif-display text-[36px] text-gray-900 mb-2">Product Reviews Oversight</h1>
    <p class="text-[14px] text-gray-600">Review, approve, and reject user product feedback.</p>
</div>

@if($reviews->count())
<div class="bg-white border border-[#e8e5e0]">
    <div class="px-6 py-4 border-b border-[#e8e5e0]">
        <h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">All Reviews</h2>
    </div>
    <div>
        @foreach($reviews as $review)
        <div class="p-6 border-b border-[#e8e5e0] last:border-b-0">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="flex">
                            @for($i=1;$i<=5;$i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span class="text-[11px] text-gray-500">by {{ $review->user->name }} on {{ $review->product->name }}</span>
                    </div>
                    <p class="text-[13px] text-gray-700">{{ $review->comment }}</p>
                </div>
                <div class="shrink-0">
                    <span class="text-[10px] font-semibold tracking-[0.1em] uppercase px-2 py-1 border
                        {{ $review->status === 'approved' ? 'border-green-600 text-green-700' : ($review->status === 'rejected' ? 'border-red-600 text-red-700' : 'border-gray-400 text-gray-600') }}">
                        {{ ucfirst($review->status) }}
                    </span>
                </div>
            </div>
            <form action="{{ route('admin.reviews.status', $review) }}" method="post" class="flex gap-2 mt-4">
                @csrf
                @method('patch')
                <select name="status" onchange="this.form.submit()" class="text-[12px] border border-[#e8e5e0] rounded px-2 py-1 bg-white cursor-pointer">
                    <option value="pending" {{ $review->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $review->status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $review->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </form>
        </div>
        @endforeach
    </div>
</div>
<div class="mt-6">{{ $reviews->links() }}</div>
@else
<div class="bg-white border border-[#e8e5e0] p-16 text-center">
    <p class="text-[13px] text-gray-600">No reviews found.</p>
</div>
@endif
@endsection
