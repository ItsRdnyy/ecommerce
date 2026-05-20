@extends('admin.layout')

@section('title', 'Transaction Logs')
@section('nav-transactions', 'bg-[#f5f3ef] text-gray-900')

@section('content')
<div class="mb-8">
    <h1 class="font-serif-display text-[36px] text-gray-900 mb-2">Transaction Logs</h1>
    <p class="text-[14px] text-gray-600">Audit trail for all platform activities.</p>
</div>

@if($logs->count())
<div class="bg-white border border-[#e8e5e0]">
    <div class="px-6 py-4 border-b border-[#e8e5e0]">
        <h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">Recent Activity</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-[#e8e5e0] bg-[#faf9f7]">
                    <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Time</th>
                    <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">User</th>
                    <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Action</th>
                    <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Entity</th>
                    <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">Entity ID</th>
                    <th class="px-6 py-3.5 text-[11px] font-semibold tracking-[0.1em] uppercase text-gray-500">IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr class="border-b border-[#f0ede8] hover:bg-[#faf9f7]">
                    <td class="px-6 py-4 text-[13px] text-gray-500">{{ $log->created_at->format('M d H:i') }}</td>
                    <td class="px-6 py-4 text-[13px]">{{ $log->user?->name ?? 'System' }}</td>
                    <td class="px-6 py-4 text-[13px] font-medium">{{ $log->action }}</td>
                    <td class="px-6 py-4 text-[13px] text-gray-600">{{ class_basename($log->entity_type) }}</td>
                    <td class="px-6 py-4 text-[13px] text-gray-500">{{ $log->entity_id }}</td>
                    <td class="px-6 py-4 text-[13px] text-gray-500">{{ $log->ip_address ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $logs->links() }}</div>
@else
<div class="bg-white border border-[#e8e5e0] p-16 text-center">
    <p class="text-[13px] text-gray-600">No transaction logs found.</p>
</div>
@endif
@endsection
