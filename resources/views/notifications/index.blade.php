@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="mx-auto max-w-4xl">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifikasi</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if($unreadCount > 0)
                    {{ $unreadCount }} notifikasi belum dibaca
                @else
                    Semua notifikasi sudah dibaca
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if($unreadCount > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Tandai semua dibaca
                </button>
            </form>
            @endif
            
            <a href="{{ route('notifications.settings') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Pengaturan
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-6 flex items-center gap-4">
        <div class="flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 p-1">
            <a href="{{ route('notifications.index') }}" 
               class="px-3 py-1.5 text-sm font-medium rounded-md {{ !request('status') ? 'bg-brand-500 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                Semua
            </a>
            <a href="{{ route('notifications.index', ['status' => 'unread']) }}" 
               class="px-3 py-1.5 text-sm font-medium rounded-md {{ request('status') === 'unread' ? 'bg-brand-500 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                Belum Dibaca
            </a>
            <a href="{{ route('notifications.index', ['status' => 'read']) }}" 
               class="px-3 py-1.5 text-sm font-medium rounded-md {{ request('status') === 'read' ? 'bg-brand-500 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                Sudah Dibaca
            </a>
        </div>

        <select onchange="window.location.href = this.value" class="text-sm rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
            <option value="{{ route('notifications.index', array_merge(request()->except('type'), [])) }}" {{ !request('type') ? 'selected' : '' }}>
                Semua Tipe
            </option>
            @foreach($types as $key => $label)
            <option value="{{ route('notifications.index', array_merge(request()->all(), ['type' => $key])) }}" {{ request('type') === $key ? 'selected' : '' }}>
                {{ $label }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Notifications List --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($notifications as $notification)
        <div class="flex items-start gap-4 p-4 {{ $notification->isUnread() ? 'bg-brand-50/50 dark:bg-brand-900/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            {{-- Icon --}}
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                @switch($notification->color)
                    @case('blue') bg-blue-100 dark:bg-blue-900/30 @break
                    @case('green') bg-green-100 dark:bg-green-900/30 @break
                    @case('yellow') bg-yellow-100 dark:bg-yellow-900/30 @break
                    @case('red') bg-red-100 dark:bg-red-900/30 @break
                    @case('purple') bg-purple-100 dark:bg-purple-900/30 @break
                    @case('orange') bg-orange-100 dark:bg-orange-900/30 @break
                    @default bg-gray-100 dark:bg-gray-700 @break
                @endswitch
            ">
                @include('partials.notification-icon', ['icon' => $notification->icon, 'color' => $notification->color])
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $notification->title }}
                            @if($notification->isUnread())
                            <span class="inline-flex w-2 h-2 ml-1 rounded-full bg-brand-500"></span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                            {{ $notification->message }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2">
                        @if($notification->action_url)
                        <a href="{{ route('notifications.view', $notification) }}" 
                           class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                            Lihat
                        </a>
                        @endif

                        @if($notification->isUnread())
                        <form action="{{ route('notifications.mark-read', $notification) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                Tandai dibaca
                            </button>
                        </form>
                        @else
                        <form action="{{ route('notifications.mark-unread', $notification) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                Tandai belum dibaca
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus notifikasi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Tidak ada notifikasi</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                @if(request('status') === 'unread')
                    Semua notifikasi sudah dibaca
                @elseif(request('status') === 'read')
                    Tidak ada notifikasi yang sudah dibaca
                @else
                    Anda belum memiliki notifikasi
                @endif
            </p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->withQueryString()->links() }}
    </div>
    @endif

    {{-- Bulk Actions --}}
    @if($notifications->total() > 0)
    <div class="mt-6 flex items-center justify-between text-sm">
        <span class="text-gray-500 dark:text-gray-400">
            Menampilkan {{ $notifications->firstItem() }}-{{ $notifications->lastItem() }} dari {{ $notifications->total() }} notifikasi
        </span>
        <div class="flex items-center gap-4">
            <form action="{{ route('notifications.destroy-all-read') }}" method="POST" 
                  onsubmit="return confirm('Hapus semua notifikasi yang sudah dibaca?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    Hapus yang sudah dibaca
                </button>
            </form>
            <form action="{{ route('notifications.destroy-all') }}" method="POST"
                  onsubmit="return confirm('Hapus SEMUA notifikasi? Tindakan ini tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                    Hapus semua
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
