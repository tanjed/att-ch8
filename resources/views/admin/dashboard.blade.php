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
                    <h3 class="text-lg font-medium mb-6">{{ __('Overview: All User Actions') }}</h3>

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