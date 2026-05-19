@extends('admin.layout')
@section('title', 'User Management')
@section('nav-users', 'bg-[#f5f3ef] text-gray-900')
@section('content')
<div class="mb-8"><h1 class="font-serif-display text-[36px] text-gray-900 mb-2">User Management</h1><p class="text-[14px] text-gray-600">Manage all platform users and accounts.</p></div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white border border-[#e8e5e0] px-6 py-5"><p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Total Users</p><p class="text-[28px] font-light text-gray-900">{{ $businesses->count() }}</p></div>
    <div class="bg-white border border-[#e8e5e0] px-6 py-5"><p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Pending Approval</p><p class="text-[28px] font-light text-gray-900">{{ $businesses->where('status', 'pending')->count() }}</p></div>
    <div class="bg-white border border-[#e8e5e0] px-6 py-5"><p class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-500 mb-1">Active</p><p class="text-[28px] font-light text-gray-900">{{ $businesses->where('status', 'active')->count() }}</p></div>
</div>
<div class="bg-white border border-[#e8e5e0]">
    <div class="px-6 py-4 border-b border-[#e8e5e0]"><h2 class="text-[13px] font-semibold tracking-[0.1em] uppercase text-gray-700">All Users</h2></div>
    <div class="overflow-x-auto"><table class="w-full text-left"><thead><tr class="border-b border-[#e8e5e0] bg-[#faf9f7]">
        <th class="px-6 py-3.5 text-[11px] font-semibold uppercase text-gray-500">Name</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold uppercase text-gray-500">Email</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold uppercase text-gray-500">Role</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold uppercase text-gray-500">Status</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold uppercase text-gray-500">Registered</th>
        <th class="px-6 py-3.5 text-[11px] font-semibold uppercase text-gray-500">Action</th>
    </tr></thead><tbody>
    @forelse($businesses as $u)
    <tr class="border-b border-[#f0ede8] hover:bg-[#faf9f7]">
        <td class="px-6 py-4 text-[14px] font-medium">{{ $u->name }}</td>
        <td class="px-6 py-4 text-[14px] text-gray-600">{{ $u->email }}</td>
        <td class="px-6 py-4"><span class="inline-flex items-center px-3 py-1 text-[11px] font-semibold tracking-wider uppercase rounded-full {{ $u->role === 'business' ? 'bg-[#e8e5e0] text-gray-800' : 'bg-[#f0ede8] text-gray-700' }}">{{ $u->role }}</span></td>
        <td class="px-6 py-4">
            @if($u->status === 'pending')
                <span class="inline-flex gap-1.5 text-[12px] font-medium text-yellow-600"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mt-1.5"></span>Pending</span>
            @elseif($u->status === 'active')
                <span class="inline-flex gap-1.5 text-[12px] font-medium text-green-700"><span class="w-1.5 h-1.5 rounded-full bg-green-500 mt-1.5"></span>{{ ucfirst($u->status) }}</span>
            @elseif($u->status === 'suspended')
                <span class="inline-flex gap-1.5 text-[12px] font-medium text-orange-600"><span class="w-1.5 h-1.5 rounded-full bg-orange-500 mt-1.5"></span>Suspended</span>
            @else
                <span class="inline-flex gap-1.5 text-[12px] font-medium text-red-600"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mt-1.5"></span>Rejected</span>
            @endif
        </td>
        <td class="px-6 py-4 text-[13px] text-gray-500">{{ $u->created_at->format('M d, Y') }}</td>
        <td class="px-6 py-4">
            @if($u->status === 'pending')
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('admin.users.approve', $u) }}" class="inline">@csrf<button type="submit" class="text-[12px] font-semibold uppercase text-green-700 hover:text-green-900 underline underline-offset-2">Approve</button></form>
                    <form method="POST" action="{{ route('admin.users.reject', $u) }}" class="inline">@csrf<button type="submit" class="text-[12px] font-semibold uppercase text-red-600 hover:text-red-800 underline underline-offset-2">Reject</button></form>
                </div>
            @elseif($u->status === 'active')
                <form method="POST" action="{{ route('admin.users.status', $u) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="suspended">
                    <button type="submit" class="text-[12px] font-semibold uppercase text-yellow-700 hover:text-yellow-900 underline underline-offset-2">Suspend</button>
                </form>
            @elseif($u->status === 'suspended' || $u->status === 'rejected')
                <form method="POST" action="{{ route('admin.users.status', $u) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="text-[12px] font-semibold uppercase text-green-700 hover:text-green-900 underline underline-offset-2">Activate</button>
                </form>
            @else
                <span class="text-[12px] text-gray-400">No action</span>
            @endif
        </td>
    </tr>
    @empty<tr><td colspan="6" class="px-6 py-10 text-center text-[14px] text-gray-500">No users found.</td></tr>@endforelse
    </tbody></table></div>
</div>
@endsection
