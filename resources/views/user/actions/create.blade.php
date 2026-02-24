<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Schedule Automated Action') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('user.actions.store') }}">
                        @csrf

                        <!-- Action Selection -->
                        <div>
                            <x-input-label for="platform_action_id" :value="__('Select Action to Automate')" />
                            <select id="platform_action_id" name="platform_action_id"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full"
                                required autofocus>
                                <option value="" disabled selected>Select an action</option>
                                @foreach($platformActions as $action)
                                    <option value="{{ $action->id }}" {{ old('platform_action_id') == $action->id ? 'selected' : '' }}>
                                        {{ $action->platform->name }} - {{ $action->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('platform_action_id')" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-1">Make sure you have added credentials for this platform
                                in the Credentials tab.</p>
                        </div>

                        <!-- Target Time -->
                        <div class="mt-4">
                            <x-input-label for="target_time" :value="__('Time (24-hour format)')" />
                            <x-text-input id="target_time" class="block mt-1 w-full" type="time" name="target_time"
                                :value="old('target_time')" required onclick="this.showPicker()" />
                            <x-input-error :messages="$errors->get('target_time')" class="mt-2" />
                        </div>



                        <!-- Active Toggle -->
                        <div class="mt-4">
                            <label for="is_active" class="inline-flex items-center">
                                <input id="is_active" type="checkbox"
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                    name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <span
                                    class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Action is Active') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('user.actions.index') }}"
                                class="text-gray-500 hover:text-gray-700 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Schedule Settings') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>