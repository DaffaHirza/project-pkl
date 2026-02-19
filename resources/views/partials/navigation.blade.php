<header x-data="{ menuToggle: false }"
    class="sticky top-0 z-999 flex w-full bg-white drop-shadow-sm dark:bg-gray-dark dark:drop-shadow-none">
    <div class="flex flex-grow items-center justify-between px-4 py-4 shadow-sm md:px-6 2xl:px-11">
        <div class="flex items-center gap-2 sm:gap-4 lg:hidden">
            <!-- Hamburger Toggle Button -->
            <button aria-controls="sidebar" @click.stop="sidebarToggle = !sidebarToggle"
                class="z-99999 block rounded-lg border border-gray-200 bg-white p-1.5 shadow-sm dark:border-gray-800 dark:bg-gray-dark lg:hidden">
                <span class="relative block h-5.5 w-5.5 cursor-pointer">
                    <span class="block absolute right-0 h-full w-full">
                        <span
                            class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-gray-700 delay-[0] duration-200 ease-in-out dark:bg-white"
                            :class="!sidebarToggle && '!w-full delay-300'"></span>
                        <span
                            class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-gray-700 delay-150 duration-200 ease-in-out dark:bg-white"
                            :class="!sidebarToggle && '!w-full delay-400'"></span>
                        <span
                            class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-gray-700 delay-200 duration-200 ease-in-out dark:bg-white"
                            :class="!sidebarToggle && '!w-full delay-500'"></span>
                    </span>
                    <span class="absolute right-0 h-full w-full rotate-45">
                        <span
                            class="absolute left-2.5 top-0 block h-full w-0.5 rounded-sm bg-gray-700 delay-300 duration-200 ease-in-out dark:bg-white"
                            :class="!sidebarToggle && '!h-0 !delay-[0]'"></span>
                        <span
                            class="delay-400 absolute left-0 top-2.5 block h-0.5 w-full rounded-sm bg-gray-700 duration-200 ease-in-out dark:bg-white"
                            :class="!sidebarToggle && '!h-0 !delay-200'"></span>
                    </span>
                </span>
            </button>

            <a href="{{ url('/') }}" class="block flex-shrink-0 lg:hidden">
                <span class="text-xl font-bold text-brand-600 dark:text-brand-400">
                    {{ config('app.name', 'Laravel') }}
                </span>
            </a>
        </div>

        <!-- Sidebar Toggle for Desktop -->
        <!-- Sidebar Toggle for Desktop -->
        <button @click.stop="sidebarToggle = !sidebarToggle"
            class="hidden lg:flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/5">
            <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M2.25 6.75H15.75M2.25 11.25H15.75" stroke="currentColor" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>

        <div class="hidden sm:block">
            <!-- Search or other elements can go here -->
        </div>

        <div class="flex items-center gap-3 2xsm:gap-7">
            <!-- Notifications Dropdown -->
            @auth
            <div x-data="{ 
                open: false, 
                notifications: [], 
                unreadCount: 0,
                loading: false,
                async fetchNotifications() {
                    if (this.loading) return;
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('notifications.recent') }}');
                        const data = await res.json();
                        this.notifications = data.notifications;
                        this.unreadCount = data.unread_count;
                    } catch (e) {
                        console.error(e);
                    }
                    this.loading = false;
                }
            }" 
            x-init="fetchNotifications()"
            class="relative">
                <button @click="open = !open; if(open) fetchNotifications()" 
                    class="relative flex h-8.5 w-8.5 items-center justify-center rounded-full border border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/5">
                    <svg class="fill-gray-500 dark:fill-gray-400" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 6.44V9.77M20.59 12.5C20.5 13.14 19.98 13.65 19.34 13.65C19.23 13.65 19.13 13.64 19.03 13.61L17.45 13.12C17.34 13.77 16.77 14.25 16.1 14.25H7.9C7.23 14.25 6.66 13.77 6.55 13.12L4.97 13.61C4.87 13.64 4.77 13.65 4.66 13.65C4.02 13.65 3.5 13.14 3.41 12.5C3.24 11.18 4.36 10.25 5.69 10.25H18.31C19.64 10.25 20.76 11.18 20.59 12.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 22C14.21 22 16 20.21 16 18H8C8 20.21 9.79 22 12 22ZM18 10V8C18 4.69 15.31 2 12 2C8.69 2 6 4.69 6 8V10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span x-show="unreadCount > 0" 
                          x-text="unreadCount > 9 ? '9+' : unreadCount"
                          class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                    </span>
                </button>

                <!-- Notifications Panel -->
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-4 w-80 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
                        <a href="{{ route('notifications.index') }}" class="text-xs text-brand-500 hover:text-brand-600">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        <template x-if="notifications.length === 0">
                            <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada notifikasi
                            </div>
                        </template>
                        <template x-for="notif in notifications" :key="notif.id">
                            <a :href="notif.action_url ? '/notifications/' + notif.id + '/view' : '#'" 
                               class="block p-3 border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                               :class="{ 'bg-blue-50 dark:bg-blue-900/10': !notif.is_read }">
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notif.title"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2" x-text="notif.message"></p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1" x-text="notif.created_at"></p>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
            @endauth

            <!-- Dark Mode Toggle -->
            <button @click="darkMode = !darkMode"
                class="flex h-8.5 w-8.5 items-center justify-center rounded-full border border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/5">
                <svg x-show="!darkMode" class="fill-gray-700" width="16" height="16" viewBox="0 0 16 16"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M8 0C8.27614 0 8.5 0.223858 8.5 0.5V2.5C8.5 2.77614 8.27614 3 8 3C7.72386 3 7.5 2.77614 7.5 2.5V0.5C7.5 0.223858 7.72386 0 8 0Z"
                        fill="" />
                    <path
                        d="M11.5 8C11.5 9.933 9.933 11.5 8 11.5C6.067 11.5 4.5 9.933 4.5 8C4.5 6.067 6.067 4.5 8 4.5C9.933 4.5 11.5 6.067 11.5 8Z"
                        fill="" />
                    <path
                        d="M8 13C8.27614 13 8.5 13.2239 8.5 13.5V15.5C8.5 15.7761 8.27614 16 8 16C7.72386 16 7.5 15.7761 7.5 15.5V13.5C7.5 13.2239 7.72386 13 8 13Z"
                        fill="" />
                    <path
                        d="M13.6569 2.34315C13.8521 2.54841 13.8521 2.87499 13.6569 3.08025L12.2426 4.49446C12.0474 4.68972 11.7208 4.68972 11.5255 4.49446C11.3303 4.2992 11.3303 3.97262 11.5255 3.77736L12.9397 2.36314C13.135 2.16788 13.4616 2.16788 13.6569 2.34315Z"
                        fill="" />
                    <path
                        d="M4.47466 11.5256C4.66992 11.3303 4.9965 11.3303 5.19176 11.5256C5.38702 11.7208 5.38702 12.0474 5.19176 12.2427L3.77755 13.6569C3.58229 13.8521 3.25571 13.8521 3.06045 13.6569C2.86519 13.4616 2.86519 13.135 3.06045 12.9398L4.47466 11.5256Z"
                        fill="" />
                    <path
                        d="M16 8C16 8.27614 15.7761 8.5 15.5 8.5H13.5C13.2239 8.5 13 8.27614 13 8C13 7.72386 13.2239 7.5 13.5 7.5H15.5C15.7761 7.5 16 7.72386 16 8Z"
                        fill="" />
                    <path
                        d="M3 8C3 8.27614 2.77614 8.5 2.5 8.5H0.5C0.223858 8.5 0 8.27614 0 8C0 7.72386 0.223858 7.5 0.5 7.5H2.5C2.77614 7.5 3 7.72386 3 8Z"
                        fill="" />
                    <path
                        d="M12.2427 11.5256C12.438 11.7208 12.438 12.0474 12.2427 12.2427L10.8285 13.6569C10.6332 13.8521 10.3066 13.8521 10.1114 13.6569C9.91609 13.4616 9.91609 13.135 10.1114 12.9398L11.5256 11.5256C11.7208 11.3303 12.0474 11.3303 12.2427 11.5256Z"
                        fill="" />
                    <path
                        d="M5.19176 4.49446C5.38702 4.68972 5.38702 5.0163 5.19176 5.21156C4.9965 5.40682 4.66992 5.40682 4.47466 5.21156L3.06045 3.79735C2.86519 3.60209 2.86519 3.27551 3.06045 3.08025C3.25571 2.88499 3.58229 2.88499 3.77755 3.08025L5.19176 4.49446Z"
                        fill="" />
                </svg>
                <svg x-show="darkMode" class="fill-gray-400" width="16" height="16" viewBox="0 0 16 16"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M14.3533 10.62C14.2466 10.44 13.9466 10.16 13.1999 10.2933C12.7866 10.3667 12.3666 10.4 11.9466 10.38C10.3933 10.3133 8.98659 9.6 8.00659 8.5C7.13993 7.53333 6.60659 6.27333 6.59993 4.91333C6.59993 4.15333 6.74659 3.42 7.04659 2.72666C7.33993 2.05333 7.13326 1.7 6.98659 1.55333C6.83326 1.4 6.47326 1.18666 5.76659 1.48C3.03993 2.62666 1.35326 5.36 1.55326 8.28666C1.75326 11.04 3.68659 13.3933 6.24659 14.28C6.85993 14.4933 7.50659 14.62 8.17326 14.6467C8.27993 14.6533 8.38659 14.66 8.49326 14.66C10.7266 14.66 12.8199 13.6067 14.1399 11.8133C14.5866 11.1933 14.4666 10.8 14.3533 10.62Z"
                        fill="" />
                </svg>
            </button>

            <!-- User Dropdown -->
            @auth
                <div x-data="{ dropdownOpen: false }" class="relative">
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-3">
                        <span class="hidden text-right lg:block">
                            <span
                                class="block text-sm font-medium text-gray-700 dark:text-white">{{ Auth::user()->name }}</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400">Admin</span>
                        </span>

                        <span
                            class="h-10 w-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-medium dark:bg-brand-500/20 dark:text-brand-400">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </span>

                        <svg class="hidden fill-current sm:block text-gray-700 dark:text-gray-400"
                            :class="dropdownOpen && 'rotate-180'" width="12" height="8" viewBox="0 0 12 8"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M0.410765 0.910734C0.736202 0.585297 1.26384 0.585297 1.58928 0.910734L6.00002 5.32148L10.4108 0.910734C10.7362 0.585297 11.2638 0.585297 11.5893 0.910734C11.9147 1.23617 11.9147 1.76381 11.5893 2.08924L6.58928 7.08924C6.26384 7.41468 5.7362 7.41468 5.41077 7.08924L0.410765 2.08924C0.0853277 1.76381 0.0853277 1.23617 0.410765 0.910734Z"
                                fill="" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-transition
                        class="absolute right-0 mt-4 flex w-62.5 flex-col rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-dark">
                        <ul class="flex flex-col gap-5 border-b border-gray-200 px-6 py-7.5 dark:border-gray-800">
                            <li>
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-brand-500 lg:text-base">
                                    <svg class="fill-current" width="22" height="22" viewBox="0 0 22 22"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M11 9.62499C8.42188 9.62499 6.35938 7.59687 6.35938 5.12187C6.35938 2.64687 8.42188 0.618744 11 0.618744C13.5781 0.618744 15.6406 2.64687 15.6406 5.12187C15.6406 7.59687 13.5781 9.62499 11 9.62499ZM11 2.16562C9.28125 2.16562 7.90625 3.50624 7.90625 5.12187C7.90625 6.73749 9.28125 8.07812 11 8.07812C12.7188 8.07812 14.0938 6.73749 14.0938 5.12187C14.0938 3.50624 12.7188 2.16562 11 2.16562Z"
                                            fill="" />
                                        <path
                                            d="M17.7719 21.4156H4.2281C3.5406 21.4156 2.9906 20.8656 2.9906 20.1781V17.0844C2.9906 13.7156 5.7406 10.9656 9.10935 10.9656H12.925C16.2937 10.9656 19.0437 13.7156 19.0437 17.0844V20.1781C19.0094 20.8312 18.4594 21.4156 17.7719 21.4156ZM4.53748 19.8687H17.4969V17.0844C17.4969 14.575 15.4344 12.5125 12.925 12.5125H9.07498C6.5656 12.5125 4.5031 14.575 4.5031 17.0844V19.8687H4.53748Z"
                                            fill="" />
                                    </svg>
                                    My Profile
                                </a>
                            </li>
                        </ul>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-3.5 px-6 py-4 text-sm font-medium duration-300 ease-in-out hover:text-brand-500 lg:text-base">
                                <svg class="fill-current" width="22" height="22" viewBox="0 0 22 22" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M15.5375 0.618744H11.6531C10.7594 0.618744 10.0031 1.37499 10.0031 2.26874V4.64062C10.0031 5.05312 10.3469 5.39687 10.7594 5.39687C11.1719 5.39687 11.55 5.05312 11.55 4.64062V2.23437C11.55 2.16562 11.5844 2.13124 11.6531 2.13124H15.5375C16.3625 2.13124 17.0156 2.78437 17.0156 3.60937V18.3562C17.0156 19.1812 16.3625 19.8344 15.5375 19.8344H11.6531C11.5844 19.8344 11.55 19.8 11.55 19.7312V17.3594C11.55 16.9469 11.2062 16.6031 10.7594 16.6031C10.3125 16.6031 10.0031 16.9469 10.0031 17.3594V19.7312C10.0031 20.625 10.7594 21.3812 11.6531 21.3812H15.5375C17.2219 21.3812 18.5625 20.0062 18.5625 18.3562V3.64374C18.5625 1.95937 17.1875 0.618744 15.5375 0.618744Z"
                                        fill="" />
                                    <path
                                        d="M6.05001 11.7563H12.2031C12.6156 11.7563 12.9594 11.4125 12.9594 11C12.9594 10.5875 12.6156 10.2438 12.2031 10.2438H6.08439L8.21564 8.07813C8.52501 7.76875 8.52501 7.2875 8.21564 6.97812C7.90626 6.66875 7.42501 6.66875 7.11564 6.97812L3.67814 10.4844C3.36876 10.7938 3.36876 11.275 3.67814 11.5844L7.11564 15.0906C7.25314 15.2281 7.45939 15.3312 7.66564 15.3312C7.87189 15.3312 8.04376 15.2625 8.21564 15.125C8.52501 14.8156 8.52501 14.3344 8.21564 14.025L6.05001 11.7563Z"
                                        fill="" />
                                </svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</header>
