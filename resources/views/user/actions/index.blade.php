<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Automated Actions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">{{ __('Scheduled Actions') }}</h3>
                        <a href="{{ route('user.actions.create') }}"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Schedule Action') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full text-left table-auto min-w-max">
                            <thead>
                                <tr>
                                    <th
                                        class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                        Platform Action</th>
                                    <th
                                        class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-left">
                                        Target Time</th>
                                    <th
                                        class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-left">
                                        Next Execution Time</th>
                                    <th
                                        class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-left">
                                        Status</th>
                                    <th
                                        class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-left">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($actionSettings as $setting)
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ !$setting->is_active ? 'opacity-50' : '' }}">
                                        <td class="p-4 border-b border-gray-50 dark:border-gray-600">
                                            {{ $setting->platformAction->platform->name }} -
                                            {{ $setting->platformAction->name }}
                                        </td>
                                        <td class="p-4 border-b border-gray-50 dark:border-gray-600 align-top">
                                            {{ \Carbon\Carbon::parse($setting->target_time)->format('h:i A') }}
                                            @if($setting->buffer_minutes > 0)
                                                <span class="text-xs text-gray-400 block">&plusmn;{{ $setting->buffer_minutes }}
                                                    mins</span>
                                            @endif
                                        </td>
                                        <td class="p-4 border-b border-gray-50 dark:border-gray-600 align-top">
                                            {{ \Carbon\Carbon::parse($setting->next_execution_time)->format('h:i A') }}
                                        </td>
                                        <td class="p-4 border-b border-gray-50 dark:border-gray-600">
                                            @if($setting->is_active)
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="p-4 border-b border-gray-50 dark:border-gray-600">
                                            <a href="{{ route('user.actions.edit', $setting) }}"
                                                class="text-blue-500 hover:underline mr-2">Edit</a>
                                            <form action="{{ route('user.actions.destroy', $setting) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('Delete this scheduled action?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($actionSettings->isEmpty())
                                    <tr>
                                        <td colspan="4" class="p-4 text-center">You haven't scheduled any automated actions.
                                        </td>
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