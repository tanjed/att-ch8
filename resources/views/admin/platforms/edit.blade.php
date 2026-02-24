<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit HR Platform') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('admin.platforms.update', $platform) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Platform Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name', $platform->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Icon -->
                        <div class="mt-4">
                            <x-input-label for="icon" :value="__('Platform Logo/Icon (Optional)')" />
                            @if($platform->icon && str_starts_with($platform->icon, '/storage/'))
                                <div class="mb-2">
                                    <p class="text-sm text-gray-500">Current Logo:</p>
                                    <img src="{{ $platform->icon }}" alt="Logo"
                                        class="h-12 w-12 object-contain mt-1 border rounded p-1">
                                </div>
                            @endif
                            <input id="icon"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                type="file" name="icon" accept="image/*" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Upload a new image file (PNG, JPG,
                                SVG) to replace the current one.</p>
                            <x-input-error :messages="$errors->get('icon')" class="mt-2" />
                        </div>

                        <!-- Auth cURL Template -->
                        <div class="mt-4">
                            <x-input-label for="authentication_curl_template" :value="__('Authentication cURL Template')" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                Paste the exact cURL request used to log in. Use the literal text
                                <strong>[USERNAME]</strong> instead of
                                the actual username/email, and <strong>[PASSWORD]</strong> instead of the actual
                                password. The system will dynamically inject the user's saved credentials here when
                                automating.
                            </p>
                            <textarea id="authentication_curl_template" name="authentication_curl_template" rows="6"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full">{{ old('authentication_curl_template', $platform->authentication_curl_template) }}</textarea>
                            <x-input-error :messages="$errors->get('authentication_curl_template')" class="mt-2" />
                        </div>

                        <!-- Token Path Key -->
                        <div class="mt-4">
                            <x-input-label for="auth_token_key" :value="__('Auth Token JSON Key Path')" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                Specify exactly how to reach the access token in the JSON response using dot
                                notation.<br>
                                Example: If the response is <code>{"data": {"access_token": "abc"}}</code>, you would
                                enter <code>data.access_token</code>.
                            </p>
                            <x-text-input id="auth_token_key" class="block mt-1 w-full" type="text"
                                name="auth_token_key" :value="old('auth_token_key', $platform->auth_token_key)"
                                placeholder="e.g. data.access_token or token" />
                            <x-input-error :messages="$errors->get('auth_token_key')" class="mt-2" />
                        </div>

                        <!-- Refresh cURL Template -->
                        <div class="mt-4 border-t pt-4">
                            <x-input-label for="refresh_curl_template" :value="__('Refresh Token cURL Template (Optional)')" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                If the platform supports token refreshing, paste the cURL here. Use
                                <strong>[REFRESH_TOKEN]</strong> to inject the currently saved refresh token
                                dynamically.
                            </p>
                            <textarea id="refresh_curl_template" name="refresh_curl_template" rows="4"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full">{{ old('refresh_curl_template', $platform->refresh_curl_template) }}</textarea>
                            <x-input-error :messages="$errors->get('refresh_curl_template')" class="mt-2" />
                        </div>

                        <!-- Refresh Token Path Key -->
                        <div class="mt-4">
                            <x-input-label for="refresh_token_key" :value="__('Refresh Token JSON Key Path (Optional)')" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                Specify how to reach the refresh token in the login/refresh JSON response.<br>
                                Example: <code>data.refresh_token</code> or <code>refresh_token</code>
                            </p>
                            <x-text-input id="refresh_token_key" class="block mt-1 w-full" type="text"
                                name="refresh_token_key" :value="old('refresh_token_key', $platform->refresh_token_key)"
                                placeholder="e.g. data.refresh_token" />
                            <x-input-error :messages="$errors->get('refresh_token_key')" class="mt-2" />
                        </div>

                        <!-- Related Auth cURL Template -->
                        <div class="mt-4 border-t pt-4">
                            <x-input-label for="related_auth_curl" :value="__('Related Auth cURL Template (Optional)')" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                An intermediate API call that should occur right after login prior to the main action.
                                E.g., a "user-screen-permissions" call. Use <strong>[TOKEN]</strong> dynamically.
                            </p>
                            <textarea id="related_auth_curl" name="related_auth_curl" rows="4"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full">{{ old('related_auth_curl', $platform->related_auth_curl) }}</textarea>
                            <x-input-error :messages="$errors->get('related_auth_curl')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.platforms.index') }}"
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