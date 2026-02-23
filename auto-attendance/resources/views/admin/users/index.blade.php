<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">{{ __('Registered Users') }}</h3>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <table class="w-full text-left table-auto min-w-max">
                        <thead>
                            <tr>
                                <th
                                    class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    Name</th>
                                <th
                                    class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    Email</th>
                                <th
                                    class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    Role</th>
                                <th
                                    class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    Joined Date</th>
                                <th
                                    class="p-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="p-4 border-b border-gray-50 dark:border-gray-600">
                                        {{ $user->name }}
                                    </td>
                                    <td class="p-4 border-b border-gray-50 dark:border-gray-600">
                                        {{ $user->email }}
                                    </td>
                                    <td class="p-4 border-b border-gray-50 dark:border-gray-600">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $user->role === 'super_admin' ? 'bg-indigo-100 text-indigo-800' : ($user->role === 'Manager' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="p-4 border-b border-gray-50 dark:border-gray-600">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="p-4 border-b border-gray-50 dark:border-gray-600">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="text-blue-500 hover:underline mr-2">Edit Role</a>
                                    </td>
                                </tr>
                            @endforeach
                            @if($users->isEmpty())
                                <tr>
                                    <td colspan="5" class="p-4 text-center">No users found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>