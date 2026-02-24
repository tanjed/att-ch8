<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My HR Platforms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">{{ __('Connected Platforms') }}</h3>
                        <a href="{{ route('user.credentials.create') }}"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Add Platform') }}
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
                                    Platform</th>
                                <th
                                    class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    Username</th>
                                <th
                                    class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($credentials as $credential)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="p-4 border-b border-gray-50 dark:border-gray-600">
                                        {{ $credential->platform->name }}
                                    </td>
                                    <td class="p-4 border-b border-gray-50 dark:border-gray-600">
                                        {{ $credential->username }}
                                    </td>
                                    <td class="p-4 border-b border-gray-50 dark:border-gray-600">
                                        <a href="{{ route('user.credentials.edit', $credential) }}"
                                            class="text-blue-500 hover:underline mr-2">Edit</a>
                                        <form action="{{ route('user.credentials.destroy', $credential) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('Delete this platform connection?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @if($credentials->isEmpty())
                                <tr>
                                    <td colspan="3" class="p-4 text-center">You haven't connected any assigned platforms
                                        yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>