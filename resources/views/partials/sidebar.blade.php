<aside :class="sidebarToggle ? 'translate-x-0' : '-translate-x-full'"
    class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white duration-300 ease-linear dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0"
    @click.outside="sidebarToggle = false">

    <!-- Sidebar Header -->
    <div class="flex items-center justify-between gap-2 px-6 py-5.5 lg:py-6.5">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <span class="text-2xl flex items-center justify-center font-bold text-brand-600 dark:text-brand-400">
                <div>
                    <img src="{{ asset('build/assets/img/logokjpp.svg') }}" alt="logo">
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400">Kantor Jasa Penilai Publik </span>
                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400">cihuy</span>
                </div>
            </span>
        </a>

        <button class="block lg:hidden" @click.stop="sidebarToggle = !sidebarToggle" aria-controls="sidebar"
            :aria-expanded="sidebarToggle">
            <svg class="fill-current" width="20" height="18" viewBox="0 0 20 18" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M19 8.175H2.98748L9.36248 1.6875C9.69998 1.35 9.69998 0.825 9.36248 0.4875C9.02498 0.15 8.49998 0.15 8.16248 0.4875L0.399976 8.3625C0.0624756 8.7 0.0624756 9.225 0.399976 9.5625L8.16248 17.4375C8.31248 17.5875 8.53748 17.7 8.76248 17.7C8.98748 17.7 9.17498 17.625 9.36248 17.475C9.69998 17.1375 9.69998 16.6125 9.36248 16.275L3.02498 9.8625H19C19.45 9.8625 19.825 9.4875 19.825 9.0375C19.825 8.55 19.45 8.175 19 8.175Z"
                    fill="" />
            </svg>
        </button>
    </div>

    <!-- Sidebar Menu -->
    <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
        <nav class="px-4 py-4 lg:px-6" x-data="{ selected: $persist('Dashboard') }">
            <!-- Menu Group -->
            <div>
                <h3 class="mb-4 ml-4 text-sm font-medium text-gray-400 uppercase">MENU</h3>

                <ul class="mb-6 flex flex-col gap-1.5">
                    <ul class="mb-6 flex flex-col gap-1.5">
                        <!-- Dashboard Menu Item -->
                        <li>
                            <a href="{{ route('dashboard') }}" @click="selected = 'Dashboard'"
                                class="group relative flex items-center gap-2.5 rounded-lg px-4 py-2 font-medium text-sm duration-300 ease-in-out"
                                :class="selected === 'Dashboard' ?
                                    'bg-brand-50 text-brand-500 dark:bg-brand-500/[0.12] dark:text-brand-400' :
                                    'text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-gray-300'">
                                <svg class="transition-colors duration-300"
                                    :class="selected === 'Dashboard' ? 'fill-brand-500 dark:fill-brand-400' :
                                        'fill-gray-500 group-hover:fill-gray-700 dark:fill-gray-400 dark:group-hover:fill-gray-300'"
                                    width="24" height="24" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M6.10322 0.956299H2.53135C1.5751 0.956299 0.787598 1.7438 0.787598 2.70005V6.27192C0.787598 7.22817 1.5751 8.01567 2.53135 8.01567H6.10322C7.05947 8.01567 7.84697 7.22817 7.84697 6.27192V2.72817C7.8751 1.7438 7.0876 0.956299 6.10322 0.956299ZM6.60947 6.30005C6.60947 6.5813 6.38447 6.8063 6.10322 6.8063H2.53135C2.2501 6.8063 2.0251 6.5813 2.0251 6.30005V2.72817C2.0251 2.44692 2.2501 2.22192 2.53135 2.22192H6.10322C6.38447 2.22192 6.60947 2.44692 6.60947 2.72817V6.30005Z"
                                        fill="" />
                                    <path
                                        d="M15.4689 0.956299H11.8971C10.9408 0.956299 10.1533 1.7438 10.1533 2.70005V6.27192C10.1533 7.22817 10.9408 8.01567 11.8971 8.01567H15.4689C16.4252 8.01567 17.2127 7.22817 17.2127 6.27192V2.72817C17.2127 1.7438 16.4252 0.956299 15.4689 0.956299ZM15.9752 6.30005C15.9752 6.5813 15.7502 6.8063 15.4689 6.8063H11.8971C11.6158 6.8063 11.3908 6.5813 11.3908 6.30005V2.72817C11.3908 2.44692 11.6158 2.22192 11.8971 2.22192H15.4689C15.7502 2.22192 15.9752 2.44692 15.9752 2.72817V6.30005Z"
                                        fill="" />
                                    <path
                                        d="M6.10322 9.92822H2.53135C1.5751 9.92822 0.787598 10.7157 0.787598 11.672V15.2438C0.787598 16.2001 1.5751 16.9876 2.53135 16.9876H6.10322C7.05947 16.9876 7.84697 16.2001 7.84697 15.2438V11.7001C7.8751 10.7157 7.0876 9.92822 6.10322 9.92822ZM6.60947 15.272C6.60947 15.5532 6.38447 15.7782 6.10322 15.7782H2.53135C2.2501 15.7782 2.0251 15.5532 2.0251 15.272V11.7001C2.0251 11.4188 2.2501 11.1938 2.53135 11.1938H6.10322C6.38447 11.1938 6.60947 11.4188 6.60947 11.7001V15.272Z"
                                        fill="" />
                                    <path
                                        d="M15.4689 9.92822H11.8971C10.9408 9.92822 10.1533 10.7157 10.1533 11.672V15.2438C10.1533 16.2001 10.9408 16.9876 11.8971 16.9876H15.4689C16.4252 16.9876 17.2127 16.2001 17.2127 15.2438V11.7001C17.2127 10.7157 16.4252 9.92822 15.4689 9.92822ZM15.9752 15.272C15.9752 15.5532 15.7502 15.7782 15.4689 15.7782H11.8971C11.6158 15.7782 11.3908 15.5532 11.3908 15.272V11.7001C11.3908 11.4188 11.6158 11.1938 11.8971 11.1938H15.4689C15.7502 11.1938 15.9752 11.4188 15.9752 11.7001V15.272Z"
                                        fill="" />
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="#" @click="selected = 'Tracking'"
                                class="group relative flex items-center gap-2.5 rounded-lg px-4 py-2 font-medium text-sm duration-300 ease-in-out"
                                :class="selected === 'Tracking' ?
                                    'bg-brand-50 text-brand-500 dark:bg-brand-500/[0.12] dark:text-brand-400' :
                                    'text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-gray-300'">
                                <svg class="transition-colors duration-300"
                                    :class="selected === 'Tracking' ? 'fill-brand-500 dark:fill-brand-400' :
                                        'fill-gray-500 group-hover:fill-gray-700 dark:fill-gray-400 dark:group-hover:fill-gray-300'"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M13.1716 3H9C7.11438 3 6.17157 3 5.58579 3.58579C5 4.17157 5 5.11438 5 7V17C5 18.8856 5 19.8284 5.58579 20.4142C6.17157 21 7.11438 21 9 21H15C16.8856 21 17.8284 21 18.4142 20.4142C19 19.8284 19 18.8856 19 17V8.82843C19 8.41968 19 8.2153 18.9239 8.03153C18.8478 7.84776 18.7032 7.70324 18.4142 7.41421L14.5858 3.58579C14.2968 3.29676 14.1522 3.15224 13.9685 3.07612C13.7847 3 13.5803 3 13.1716 3Z"
                                        stroke="currentColor" stroke-width="2" />
                                    <path d="M9 13L15 13" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                    <path d="M9 17L13 17" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                    <path d="M13 3V7C13 7.94281 13 8.41421 13.2929 8.70711C13.5858 9 14.0572 9 15 9H19"
                                        stroke="currentColor" stroke-width="2" />
                                </svg>

                                Tracking
                            </a>
                        </li>
                        <li>
                            <a href="#" @click="selected = 'AI Assistant'"
                                class="group relative flex items-center gap-2.5 rounded-lg px-4 py-2 font-medium text-sm duration-300 ease-in-out"
                                :class="selected === 'AI Assistant' ?
                                    'bg-brand-50 text-brand-500 dark:bg-brand-500/[0.12] dark:text-brand-400' :
                                    'text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-gray-300'">
                                <svg class="transition-colors duration-300"
                                    :class="selected === 'AI Assistant' ? 'fill-brand-500 dark:fill-brand-400' :
                                        'fill-gray-500 group-hover:fill-gray-700 dark:fill-gray-400 dark:group-hover:fill-gray-300'"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.7587 5H15.2413C16.0463 4.99999 16.7106 4.99998 17.2518 5.04419C17.8139 5.09012 18.3306 5.18868 18.816 5.43597C19.5686 5.81947 20.1805 6.43139 20.564 7.18404C20.8113 7.66937 20.9099 8.18608 20.9558 8.74817C21 9.28936 21 9.95372 21 10.7587V15.2413C21 16.0463 21 16.7106 20.9558 17.2518C20.9099 17.8139 20.8113 18.3306 20.564 18.816C20.1805 19.5686 19.5686 20.1805 18.816 20.564C18.3306 20.8113 17.8139 20.9099 17.2518 20.9558C16.7106 21 16.0463 21 15.2413 21H8.75873C7.95374 21 7.28938 21 6.74817 20.9558C6.18608 20.9099 5.66937 20.8113 5.18404 20.564C4.43139 20.1805 3.81947 19.5686 3.43597 18.816C3.18868 18.3306 3.09012 17.8139 3.04419 17.2518C2.99998 16.7106 2.99999 16.0463 3 15.2413V10.7587C2.99999 9.95373 2.99998 9.28937 3.04419 8.74817C3.09012 8.18608 3.18868 7.66937 3.43597 7.18404C3.81947 6.43139 4.43139 5.81947 5.18404 5.43597C5.66937 5.18868 6.18608 5.09012 6.74817 5.04419C7.28937 4.99998 7.95373 4.99999 8.7587 5ZM6.91104 7.03755C6.47262 7.07337 6.24842 7.1383 6.09202 7.21799C5.7157 7.40973 5.40973 7.71569 5.21799 8.09202C5.1383 8.24842 5.07337 8.47262 5.03755 8.91104C5.00078 9.36113 5 9.94342 5 10.8V15.2C5 16.0566 5.00078 16.6389 5.03755 17.089C5.07337 17.5274 5.1383 17.7516 5.21799 17.908C5.40973 18.2843 5.7157 18.5903 6.09202 18.782C6.24842 18.8617 6.47262 18.9266 6.91104 18.9624C7.36113 18.9992 7.94342 19 8.8 19H15.2C16.0566 19 16.6389 18.9992 17.089 18.9624C17.5274 18.9266 17.7516 18.8617 17.908 18.782C18.2843 18.5903 18.5903 18.2843 18.782 17.908C18.8617 17.7516 18.9266 17.5274 18.9624 17.089C18.9992 16.6389 19 16.0566 19 15.2V10.8C19 9.94342 18.9992 9.36113 18.9624 8.91104C18.9266 8.47262 18.8617 8.24842 18.782 8.09202C18.5903 7.71569 18.2843 7.40973 17.908 7.21799C17.7516 7.1383 17.5274 7.07337 17.089 7.03755C16.6389 7.00078 16.0566 7 15.2 7H8.8C7.94342 7 7.36113 7.00078 6.91104 7.03755Z"
                                        fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10 2.43L10 3.00006L8 2.99994L8.00009 1.59801L8.27826 1.3079C8.52078 1.05498 8.88254 0.687155 9.51489 0.412789C10.1265 0.147442 10.9151 0 12 0C13.085 0 13.8736 0.147454 14.4852 0.412808C15.1175 0.687178 15.4793 1.055 15.7218 1.30788L16 1.59802V3H14V2.42998C13.9115 2.36092 13.8144 2.30192 13.6891 2.24754C13.4093 2.12611 12.9151 2 12 2C11.085 2 10.5908 2.12611 10.3109 2.24754C10.1856 2.30192 10.0886 2.36092 10 2.43Z"
                                        fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M12.0005 17C10.8097 17.0006 9.59337 16.695 8.4 15.8L9.6 14.2C10.4066 14.805 11.1886 15.0004 11.9995 15C12.8103 14.9996 13.594 14.8045 14.4 14.2L15.6 15.8C14.406 16.6955 13.1879 16.9994 12.0005 17Z"
                                        fill="currentColor" />
                                    <path
                                        d="M7 11.5C7 10.6716 7.67157 10 8.5 10C9.32843 10 10 10.6716 10 11.5C10 12.3284 9.32843 13 8.5 13C7.67157 13 7 12.3284 7 11.5Z"
                                        fill="currentColor" />
                                    <path
                                        d="M14 11.5C14 10.6716 14.6716 10 15.5 10C16.3284 10 17 10.6716 17 11.5C17 12.3284 16.3284 13 15.5 13C14.6716 13 14 12.3284 14 11.5Z"
                                        fill="currentColor" />
                                </svg>

                                AI Assistant
                            </a>
                        </li>
                    </ul>
            </div>
        </nav>
    </div>
</aside>
