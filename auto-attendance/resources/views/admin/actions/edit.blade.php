<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Platform Action') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('admin.actions.update', ['action' => $platformAction->id]) }}">
                        @csrf
                        @method('PUT')

                        <!-- Platform Selection -->
                        <div>
                            <x-input-label for="platform_id" :value="__('Select Platform')" />
                            <select id="platform_id" name="platform_id"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full"
                                required autofocus>
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform->id }}" {{ old('platform_id', $platformAction->platform_id) == $platform->id ? 'selected' : '' }}>
                                        {{ $platform->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('platform_id')" class="mt-2" />
                        </div>

                        <!-- Action Name -->
                        <div class="mt-4">
                            <x-input-label for="name" :value="__('Action Name (e.g. Clock In)')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name', $platformAction->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- API cURL Template -->
                        <div class="mt-4">
                            <x-input-label for="api_curl_template" :value="__('API cURL Template')" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                Paste the cURL command used to hit this API. This will be used as a template, appending
                                the Bearer token later if you leave it out. Otherwise, explicitly use
                                <strong>[TOKEN]</strong> where the token should go.
                            </p>
                            <textarea id="api_curl_template" name="api_curl_template" rows="5"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full"
                                required>{{ old('api_curl_template', $platformAction->api_curl_template) }}</textarea>
                            <x-input-error :messages="$errors->get('api_curl_template')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.actions.index') }}"
                                class="text-gray-500 hover:text-gray-700 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>