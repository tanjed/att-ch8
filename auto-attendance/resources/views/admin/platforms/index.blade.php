<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manage HR Platforms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">{{ __('Supported Platforms') }}</h3>
                        <a href="{{ route('admin.platforms.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Add New Platform') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <table class="w-full text-left table-auto min-w-max">
                        <thead>
                            <tr>
                                <th
                                    class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    ID</th>
                                <th
                                    class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    Name</th>
                                <th
                                    class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    Icon Class</th>
                                <th
                                    class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($platforms as $platform)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="p-4 border-b border-gray-50 dark:border-gray-600">{{ $platform->id }}
                                    </td>
                                    <td class="p-4 border-b border-gray-50 dark:border-gray-600">{{ $platform->name }}
                                    </td>
                                    <td class="p-4 border-b border-gray-50 dark:border-gray-600">
                                        @if($platform->icon && str_starts_with($platform->icon, '/storage/'))
                                            <div class="flex items-center">
                                                <img src="{{ asset($platform->icon) }}" alt="Logo"
                                                    class="h-10 w-32 object-left object-contain">
                                            </div>
                                        @else
                                            {{ $platform->icon }}
                                        @endif
                                    </td>
                                    <td class="p-4 border-b border-gray-50 dark:border-gray-600">
                                        <a href="{{ route('admin.platforms.edit', $platform) }}"
                                            class="text-blue-500 hover:underline mr-2">Edit</a>
                                        <form action="{{ route('admin.platforms.destroy', $platform) }}" method="POST"
                                            class="inline-block" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @if($platforms->isEmpty())
                                <tr>
                                    <td colspan="4" class="p-4 text-center">No platforms found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>