<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            {{-- Header --}}
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">User Management</h1>
                <p class="text-sm text-gray-500 mt-1">Manage and monitor all registered users.</p>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Top Bar: Tabs + Search + Filter --}}
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-6 pt-5 pb-4 border-b border-gray-100 gap-4">

                    {{-- Tabs --}}
                    <nav class="flex items-center gap-1" aria-label="Tabs">
                        @foreach ([
        'all' => 'View all',
        'general' => 'General',
        'admin' => 'Admin',
        'creator' => 'Creator',
    ] as $key => $label)
                            <a href="{{ route('users.index', ['role' => $key === 'all' ? null : $key]) }}"
                                class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                                      {{ request('role', 'all') === $key
                                          ? 'bg-gray-900 text-white'
                                          : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </nav>

                    {{-- Search & Filter --}}
                    <div class="flex items-center gap-3">
                        {{-- Search --}}
                        <form method="GET" action="{{ route('users.index') }}" class="relative">
                            <input type="hidden" name="role" value="{{ request('role') }}">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Search..."
                                    class="pl-9 pr-4 py-2 w-56 text-sm bg-gray-50 border border-gray-200 rounded-xl
                                           focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent
                                           placeholder-gray-400 text-gray-700" />
                            </div>
                        </form>

                        {{-- Filter Button --}}
                        <button
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700
                                       border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 010 2H4a1 1 0 01-1-1zM6 10a1 1 0 011-1h10a1 1 0 010 2H7a1 1 0 01-1-1zM10 16a1 1 0 011-1h2a1 1 0 010 2h-2a1 1 0 01-1-1z" />
                            </svg>
                            Filters
                        </button>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="px-6 py-3 w-10">
                                    <input type="checkbox" id="select-all"
                                        class="w-4 h-4 rounded border-gray-300 text-gray-800 cursor-pointer
                                                  focus:ring-gray-400 focus:ring-offset-0">
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                    Name</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                    Role</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                    Projects</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                    Enrolled</th>
                                <th class="px-4 py-3 w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50/70 transition-colors group">

                                    {{-- Checkbox --}}
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="selected[]" value="{{ $user->id }}"
                                            class="row-checkbox w-4 h-4 rounded border-gray-300 text-gray-800
                                                      cursor-pointer focus:ring-gray-400 focus:ring-offset-0">
                                    </td>

                                    {{-- Name + Avatar --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                                                alt="{{ $user->name }}"
                                                class="w-9 h-9 rounded-full object-cover ring-2 ring-white shadow-sm flex-shrink-0">
                                            <div>
                                                <p class="font-semibold text-gray-800 leading-tight">{{ $user->name }}
                                                </p>
                                                <p class="text-xs text-gray-400">@{{ $user - > username }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Role Badge --}}
                                    <td class="px-4 py-4">
                                        @php
                                            $roleStyles = [
                                                'General' => 'bg-emerald-50 text-emerald-600 border border-emerald-200',
                                                'Admin' => 'bg-amber-50 text-amber-600 border border-amber-200',
                                                'Creator' => 'bg-gray-100 text-gray-600 border border-gray-200',
                                            ];
                                            $style =
                                                $roleStyles[$user->role] ??
                                                'bg-gray-100 text-gray-600 border border-gray-200';
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium {{ $style }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>

                                    {{-- Projects --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-baseline gap-1">
                                            <span
                                                class="font-semibold text-gray-800">{{ $user->projects_count }}</span>
                                            <span class="text-gray-400 text-xs">/ {{ $user->projects_limit }}</span>
                                        </div>
                                        {{-- Progress bar --}}
                                        <div class="mt-1.5 w-24 h-1 bg-gray-100 rounded-full overflow-hidden">
                                            @php $pct = min(100, ($user->projects_count / max(1, $user->projects_limit)) * 100); @endphp
                                            <div class="h-full rounded-full {{ $pct >= 80 ? 'bg-amber-400' : 'bg-emerald-400' }}"
                                                style="width: {{ $pct }}%"></div>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-1.5">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full {{ $user->status === 'Active' ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                                            <span class="text-gray-700">{{ $user->status }}</span>
                                        </div>
                                    </td>

                                    {{-- Enrolled --}}
                                    <td class="px-4 py-4 text-gray-500">
                                        {{ \Carbon\Carbon::parse($user->enrolled_at)->format('M j, Y') }}
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-4 py-4">
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100
                                                           opacity-0 group-hover:opacity-100 transition-all">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                </svg>
                                            </button>

                                            <div x-show="open" @click.outside="open = false"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="opacity-100 scale-100"
                                                x-transition:leave-end="opacity-0 scale-95"
                                                class="absolute right-0 z-20 mt-1 w-40 bg-white border border-gray-100
                                                        rounded-xl shadow-lg py-1 text-sm">
                                                <a href="{{ route('users.show', $user) }}"
                                                    class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-50">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </a>
                                                <a href="{{ route('users.edit', $user) }}"
                                                    class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-50">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                                    onsubmit="return confirm('Delete this user?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="w-full flex items-center gap-2 px-4 py-2 text-red-500 hover:bg-red-50">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="font-medium">No users found</p>
                                        <p class="text-sm mt-1">Try adjusting your search or filters.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            Showing <span class="font-medium text-gray-700">{{ $users->firstItem() }}</span>
                            to <span class="font-medium text-gray-700">{{ $users->lastItem() }}</span>
                            of <span class="font-medium text-gray-700">{{ $users->total() }}</span> users
                        </p>
                        {{ $users->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Select All Script --}}
    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
        });
    </script>
</x-app-layout>
