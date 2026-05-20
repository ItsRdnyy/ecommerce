@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<section class="bg-[#f5f3ef] min-h-screen py-10">
    <div class="max-w-[800px] mx-auto px-6 lg:px-10">
        <div class="flex items-center justify-between mb-8">
            <h1 class="font-serif-display text-[32px] text-gray-900">Notifications</h1>
            <form action="{{ route('notifications.read_all') }}" method="post">
                @csrf
                @method('patch')
                <button type="submit" class="text-[11px] font-semibold tracking-[0.12em] uppercase text-gray-600 hover:text-black">Mark all read</button>
            </form>
        </div>

        @if($notifications->count())
        <div class="space-y-3">
            @foreach($notifications as $notif)
            <div class="bg-white border {{ $notif->read_at ? 'border-[#e8e5e0]' : 'border-gray-400' }} p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            @if(!$notif->read_at)
                                <span class="w-2 h-2 bg-[#111] rounded-full shrink-0"></span>
                            @endif
                            <h3 class="text-[14px] font-medium text-gray-900">{{ $notif->title }}</h3>
                        </div>
                        <p class="text-[13px] text-gray-700">{{ $notif->message }}</p>
                        <p class="text-[11px] text-gray-500 mt-2">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notif->read_at)
                    <form action="{{ route('notifications.read', $notif) }}" method="post" class="shrink-0">
                        @csrf
                        @method('patch')
                        <button type="submit" class="text-[11px] text-gray-500 hover:text-black underline">Mark read</button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $notifications->links() }}</div>
        @else
        <div class="bg-white border border-[#e8e5e0] p-16 text-center">
            <p class="text-[13px] text-gray-600">No notifications yet.</p>
        </div>
        @endif
    </div>
</section>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle individual "Mark read" forms
    document.querySelectorAll('form[action*="/notifications/"]').forEach(form => {
        if (!form.action.includes('/read-all')) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: new FormData(this)
                    });
                    
                    if (response.ok) {
                        // Remove the notification item from DOM
                        const notifItem = this.closest('.bg-white');
                        notifItem.style.opacity = '0.5';
                        notifItem.style.borderColor = '#e8e5e0';
                        
                        // Remove the black dot and button
                        const dot = notifItem.querySelector('.bg-\\[\\#111\\]');
                        if (dot) dot.remove();
                        
                        this.remove();
                        
                        // Update notification count badge
                        if (window.Layout && window.Layout.updateNotificationCount) {
                            window.Layout.updateNotificationCount();
                        }
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                }
            });
        }
    });

    // Handle "Mark all read" form
    const markAllForm = document.querySelector('form[action*="/read-all"]');
    if (markAllForm) {
        markAllForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: new FormData(this)
                });
                
                if (response.ok) {
                    // Remove all black dots and buttons
                    document.querySelectorAll('.bg-\\[\\#111\\]').forEach(dot => dot.remove());
                    document.querySelectorAll('form[action*="/notifications/"]:not([action*="/read-all"])').forEach(form => form.remove());
                    
                    // Update all notification borders
                    document.querySelectorAll('.border-gray-400').forEach(item => {
                        item.classList.remove('border-gray-400');
                        item.classList.add('border-[#e8e5e0]');
                    });
                    
                    // Update notification count badge
                    if (window.Layout && window.Layout.updateNotificationCount) {
                        window.Layout.updateNotificationCount();
                    }
                }
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            }
        });
    }
});
</script>
@endpush
@endsection
