<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- KPI Cards Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Total Users Card -->
                        <div
                            class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-semibold text-gray-500 tracking-wide uppercase">All Users</h4>
                                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15.21 14.223a5 5 0 014.782 4.777v1.01H4.008v-1.01a5 5 0 014.782-4.777M12 4.354v-1.127M12 4.354a4 4 0 100 5.292">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalUsers }}</h2>
                                <p
                                    class="text-xs font-semibold text-green-600 mt-2 bg-green-50 inline-block px-2 py-1 rounded-md">
                                    {{ $verifiedUsers }} Verified
                                </p>
                            </div>
                        </div>

                        <!-- Credentials Set Card -->
                        <div
                            class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-semibold text-gray-500 tracking-wide uppercase">Stored
                                    Credentials</h4>
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalCredentials }}
                                </h2>
                                <p class="text-sm text-gray-500 mt-1 dark:text-gray-400">Tokens & Passwords</p>
                            </div>
                        </div>

                        <!-- Scheduled Actions Card -->
                        <div
                            class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-semibold text-gray-500 tracking-wide uppercase">Scheduled
                                    Actions</h4>
                                <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalActions }}</h2>
                                <p class="text-sm text-gray-500 mt-1 dark:text-gray-400">Total automations run by CRON
                                </p>
                            </div>
                        </div>

                        <!-- Today's Executions Card -->
                        <div
                            class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-semibold text-gray-500 tracking-wide uppercase">Activity Today
                                </h4>
                                <div class="p-2 bg-orange-50 text-orange-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $todayExecutions }}</h2>
                                <div class="mt-2 flex gap-3 text-sm">
                                    <span class="flex items-center text-green-600 font-medium">
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-1.5"></span>
                                        {{ $todaySuccess }} Success
                                    </span>
                                    <span class="flex items-center text-red-500 font-medium">
                                        <span class="w-2 h-2 rounded-full bg-red-400 mr-1.5"></span> {{ $todayFailed }}
                                        Failed
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mb-6 border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                        <h3 class="text-lg font-medium">{{ __('Overview: All User Actions') }}</h3>

                        <form method="GET" action="{{ route('admin.dashboard') }}"
                            class="w-full lg:w-auto mt-4 sm:mt-0 flex flex-col sm:flex-row gap-2">

                            <x-input-label for="platform_id" :value="__('Filter by Platform')" class="sr-only" />
                            <select id="platform_id" name="platform_id" onchange="this.form.submit()"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full sm:w-auto md:w-[150px] lg:w-[200px]">
                                <option value="">-- All Platforms --</option>
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform->id }}" {{ $selectedPlatformId == $platform->id ? 'selected' : '' }}>
                                        {{ $platform->name }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-label for="action_id" :value="__('Filter by Action')" class="sr-only" />
                            <select id="action_id" name="action_id" onchange="this.form.submit()"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full sm:w-auto md:w-[150px] lg:w-[200px]">
                                <option value="">-- All Actions --</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action->id }}" {{ $selectedActionId == $action->id ? 'selected' : '' }}>
                                        {{ $action->platform->name }} - {{ $action->name }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-label for="user_id" :value="__('Filter by User')" class="sr-only" />
                            <select id="user_id" name="user_id" onchange="this.form.submit()"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full sm:w-auto md:w-[200px] lg:w-[250px]">
                                <option value="">-- All Users --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left table-auto min-w-max text-sm">
                            <thead>
                                <tr>
                                    <th
                                        class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 font-semibold text-gray-500 dark:text-gray-400">
                                        User</th>
                                    <th
                                        class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 font-semibold text-gray-500 dark:text-gray-400">
                                        Platform Action</th>
                                    <th
                                        class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 font-semibold text-gray-500 dark:text-gray-400">
                                        Target / Buffer</th>
                                    <th
                                        class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 font-semibold text-gray-500 dark:text-gray-400">
                                        Next Execution</th>
                                    <th
                                        class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 font-semibold text-gray-500 dark:text-gray-400">
                                        Weekly Off Days</th>
                                    <th
                                        class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 font-semibold text-gray-500 dark:text-gray-400">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allActions as $setting)
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ !$setting->is_active ? 'opacity-50' : '' }}">
                                        <td class="p-4 border-b border-gray-50 dark:border-gray-600 align-top">
                                            <strong>{{ $setting->user->name }}</strong><br>
                                            <span class="text-xs text-gray-500">{{ $setting->user->email }}</span>
                                        </td>
                                        <td class="p-4 border-b border-gray-50 dark:border-gray-600 align-top">
                                            <span class="font-medium text-indigo-600 dark:text-indigo-400">
                                                {{ $setting->platformAction->platform->name }}
                                            </span><br>
                                            {{ $setting->platformAction->name }}
                                        </td>
                                        <td class="p-4 border-b border-gray-50 dark:border-gray-600 align-top">
                                            {{ \Carbon\Carbon::parse($setting->target_time)->format('h:i A') }}
                                            @if($setting->buffer_minutes > 0)
                                                <span
                                                    class="text-xs text-gray-400 block mt-1">&plusmn;{{ $setting->buffer_minutes }}
                                                    mins</span>
                                            @else
                                                <span class="text-xs text-gray-400 block mt-1">Exact Time</span>
                                            @endif
                                        </td>
                                        <td class="p-4 border-b border-gray-50 dark:border-gray-600 align-top font-medium">
                                            {{ \Carbon\Carbon::parse($setting->next_execution_time)->format('h:i A') }}
                                        </td>
                                        <td class="p-4 border-b border-gray-50 dark:border-gray-600 align-top">
                                            @if(!empty($setting->weekly_off_days))
                                                <span
                                                    class="text-xs bg-gray-100 dark:bg-gray-900 px-2 py-1 rounded inline-block text-gray-600 dark:text-gray-400">
                                                    {{ implode(', ', $setting->weekly_off_days) }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400 italic">None</span>
                                            @endif
                                        </td>
                                        <td class="p-4 border-b border-gray-50 dark:border-gray-600 align-top">
                                            @if($setting->is_active)
                                                <span
                                                    class="px-2 py-1 inline-flex text-[10px] leading-4 font-bold rounded-full bg-green-100 text-green-800 uppercase tracking-wide">Active</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 inline-flex text-[10px] leading-4 font-bold rounded-full bg-gray-100 text-gray-600 uppercase tracking-wide">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @if($allActions->isEmpty())
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-gray-500">No automated actions have been
                                            scheduled by any user yet.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>