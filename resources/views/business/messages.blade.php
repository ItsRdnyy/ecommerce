@extends('business.layout')

@section('title', 'Business Messages')
@section('nav-messages', 'bg-[#f5f3ef] text-gray-900')

@section('content')
<div class="mb-8">
    <h1 class="font-serif-display text-[36px] text-gray-900 mb-2">Business Messages</h1>
    <p class="text-[14px] text-gray-600">View and manage messages sent to your business from the "Get in Touch" section.</p>
</div>

@if($messages->count())
<div class="bg-white border border-[#e8e5e0]">
    <div class="px-6 py-4 border-b border-[#e8e5e0]">
        <h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">Recent Messages</h2>
    </div>
    <div>
        @foreach($messages as $message)
        <div class="p-6 border-b border-[#e8e5e0] last:border-b-0">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-[14px] font-bold text-gray-900">{{ $message->name }}</span>
                        <span class="text-[11px] text-gray-500">&lt;{{ $message->email }}&gt;</span>
                        <span class="text-[11px] text-gray-400">on {{ $message->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <p class="text-[13px] text-gray-700 whitespace-pre-wrap">{{ $message->message }}</p>
                </div>
                <div class="shrink-0">
                    <span class="text-[10px] font-semibold tracking-[0.1em] uppercase px-2 py-1 border
                        {{ $message->status === 'read' ? 'border-green-600 text-green-700' : 'border-red-600 text-red-700' }}">
                        {{ ucfirst($message->status) }}
                    </span>
                </div>
            </div>
            <form action="{{ route('business.messages.status', $message) }}" method="post" class="flex gap-2 mt-4">
                @csrf
                @method('patch')
                <select name="status" onchange="this.form.submit()" class="text-[12px] border border-[#e8e5e0] rounded px-2 py-1 bg-white cursor-pointer">
                    <option value="unread" {{ $message->status === 'unread' ? 'selected' : '' }}>Mark as Unread</option>
                    <option value="read" {{ $message->status === 'read' ? 'selected' : '' }}>Mark as Read</option>
                </select>
            </form>
        </div>
        @endforeach
    </div>
</div>
<div class="mt-6">{{ $messages->links() }}</div>
@else
<div class="bg-white border border-[#e8e5e0] p-16 text-center">
    <p class="text-[13px] text-gray-600">No messages found for your business.</p>
</div>
@endif
@endsection
