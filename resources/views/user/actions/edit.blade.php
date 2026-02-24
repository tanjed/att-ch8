<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Scheduled Action') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('user.actions.update', $action) }}">
                        @csrf
                        @method('PUT')

                        <!-- Action Selection -->
                        <div>
                            <x-input-label for="platform_action_id" :value="__('Select Action to Automate')" />
                            <select id="platform_action_id" name="platform_action_id"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full"
                                required>
                                @foreach($platformActions as $pAction)
                                    <option value="{{ $pAction->id }}" {{ old('platform_action_id', $action->platform_action_id) == $pAction->id ? 'selected' : '' }}>
                                        {{ $pAction->platform->name }} - {{ $pAction->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('platform_action_id')" class="mt-2" />
                        </div>

                        <!-- Target Time -->
                        <div class="mt-4">
                            <x-input-label for="target_time" :value="__('Target Time (24-hour format)')" />
                            <x-text-input id="target_time" class="block mt-1 w-full" type="time" name="target_time"
                                :value="old('target_time', \Carbon\Carbon::parse($action->target_time)->format('H:i'))"
                                required onclick="this.showPicker()" />
                            <x-input-error :messages="$errors->get('target_time')" class="mt-2" />
                        </div>

                        <!-- Buffer Minutes -->
                        <div class="mt-4">
                            <x-input-label for="buffer_minutes" :value="__('Buffer Minutes (Optional)')" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                For example, entering <strong>10</strong> means the action will fire randomly within
                                &plusmn;10 minutes of the Target Time. Next Execution Time will automatically
                                recalculate once updated.
                            </p>
                            <x-text-input id="buffer_minutes" class="block mt-1 w-full" type="number" min="0"
                                name="buffer_minutes" :value="old('buffer_minutes', $action->buffer_minutes)" />
                            <x-input-error :messages="$errors->get('buffer_minutes')" class="mt-2" />
                        </div>

                        <!-- Display Calculated Next Execution Time -->
                        @if($action->buffer_minutes > 0)
                            <div
                                class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold">Next Scheduled Automation:</span>
                                    <span>
                                        {{ \Carbon\Carbon::parse($action->next_execution_time)->format('h:i A') }}
                                        (randomized by buffer)
                                    </span>
                                </p>
                            </div>
                        @else
                            <div
                                class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold">Next Scheduled Automation:</span>
                                    <span>
                                        {{ \Carbon\Carbon::parse($action->target_time)->format('h:i A') }}
                                        (no buffer applied)
                                    </span>
                                </p>
                            </div>
                        @endif

                        <!-- Weekly Off Days -->
                        <div class="mt-4">
                            <x-input-label :value="__('Weekly Off Days (Optional)')" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                Select any days of the week you do <strong>not</strong> want this action to run.
                            </p>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-2">
                                @php
                                    $savedOffDays = old('weekly_off_days', $action->weekly_off_days ?? []);
                                @endphp
                                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="weekly_off_days[]" value="{{ $day }}"
                                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            {{ in_array($day, $savedOffDays) ? 'checked' : '' }}>
                                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ $day }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('weekly_off_days')" class="mt-2" />
                        </div>

                        <!-- Active Toggle -->
                        <div class="mt-4">
                            <label for="is_active" class="inline-flex items-center">
                                <input id="is_active" type="checkbox"
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                    name="is_active" value="1" {{ old('is_active', $action->is_active) ? 'checked' : '' }}>
                                <span
                                    class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Action is Active') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('user.actions.index') }}"
                                class="text-gray-500 hover:text-gray-700 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Update Settings') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>