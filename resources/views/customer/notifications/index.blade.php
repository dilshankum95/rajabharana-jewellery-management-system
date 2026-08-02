<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="jewel-page-title text-xl">Notifications</h1>
                <p class="jewel-page-subtitle">Billing updates and payment confirmations</p>
            </div>
            @if($notifications->whereNull('read_at')->count() > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="jewel-btn-outline text-sm">Mark all as read</button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="jewel-card overflow-hidden divide-y divide-jewel-gold/10">
                @forelse($notifications as $notification)
                    <div @class([
                        'px-6 py-4',
                        'bg-sky-50/50' => is_null($notification->read_at),
                    ])>
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p @class(['text-sm', 'font-medium text-jewel-dark' => is_null($notification->read_at), 'text-gray-600' => ! is_null($notification->read_at)])>
                                    {{ $notification->data['message'] ?? 'Notification' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @if(is_null($notification->read_at))
                                <span class="shrink-0 w-2 h-2 rounded-full bg-sky-500 mt-2"></span>
                            @endif
                        </div>
                        @if(! empty($notification->data['url']))
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="mt-3">
                                @csrf
                                <button type="submit" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold">
                                    View invoice →
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="px-6 py-12 text-center text-gray-400">No notifications yet. You will be notified when an invoice is issued or a payment is recorded.</p>
                @endforelse
            </div>
            @if($notifications->hasPages())
                <div class="mt-4">{{ $notifications->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
