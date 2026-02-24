<div class="flex h-16 shrink-0 items-center gap-2 mt-4">
    <div class="flex items-center justify-center h-8 w-8 rounded-lg bg-indigo-600">
        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
    <span class="text-xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400">Att-Ch8</span>
</div>
<nav class="flex flex-1 flex-col mt-6">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        @if (!auth()->check() || !in_array(auth()->user()->role, ['super_admin']))
            <li>
                <div class="text-xs font-semibold leading-6 text-gray-400 tracking-wider uppercase mb-2">User Panel</div>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="{{ request()->routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.credentials.index') }}"
                            class="{{ request()->routeIs('user.credentials.*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            My Credentials
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.actions.index') }}"
                            class="{{ request()->routeIs('user.actions.*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Automated Actions
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        @if (auth()->check() && in_array(auth()->user()->role, ['super_admin', 'Manager']))
            <li>
                <div class="text-xs font-semibold leading-6 text-gray-400 tracking-wider uppercase mb-2">Admin Panel</div>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                            class="{{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Overview
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.platforms.index') }}"
                            class="{{ request()->routeIs('admin.platforms.*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Manage Platforms
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.actions.index') }}"
                            class="{{ request()->routeIs('admin.actions.*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Platform API Actions
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}"
                            class="{{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Manage Users
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        <li class="mt-auto">
            <div class="text-xs font-semibold leading-6 text-gray-400 tracking-wider uppercase mb-2">Account</div>
            <ul role="list" class="-mx-2 space-y-1">
                <li>
                    <a href="{{ route('profile.edit') }}"
                        class="{{ request()->routeIs('profile.edit') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                        Profile Config
                    </a>
                </li>
                <li class="lg:hidden">
                    <!-- Only show logout on mobile nav here since desktop has it in top bar -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="w-full text-left text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>