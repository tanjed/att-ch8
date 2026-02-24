<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Platform Credentials') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('user.credentials.update', $credential) }}">
                        @csrf
                        @method('PUT')

                        <!-- Platform Selection -->
                        <div>
                            <x-input-label for="platform_id" :value="__('Select Platform')" />
                            <select id="platform_id" name="platform_id"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full"
                                required autofocus>
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform->id }}" {{ old('platform_id', $credential->platform_id) == $platform->id ? 'selected' : '' }}>
                                        {{ $platform->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('platform_id')" class="mt-2" />
                        </div>

                        <!-- Username -->
                        <div class="mt-4">
                            <x-input-label for="username" :value="__('Username / Employee ID')" />
                            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username"
                                :value="old('username', $credential->username)" required />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Enter your password again to update
                                these credentials securely.</p>
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                                required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Location Map -->
                        <div class="mt-4">
                            <x-input-label for="location" :value="__('Current Location (Coordinates)')" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                Please allow location access to automatically fetch your coordinates, or click on the
                                map to update your location.
                            </p>

                            <!-- MapLibre GL CSS & JS -->
                            <link href="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.css" rel="stylesheet" />
                            <script src="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.js"></script>

                            <div id="map" style="height: 300px; width: 100%; border-radius: 0.375rem; z-index: 1;">
                            </div>

                            <x-text-input id="location"
                                class="block mt-2 w-full bg-gray-100 dark:bg-gray-800 text-gray-500" type="text"
                                name="location" :value="old('location', $credential->location)" readonly required
                                placeholder="Waiting for location..." />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                var map = new maplibregl.Map({
                                    container: 'map',
                                    style: 'https://basemaps.cartocdn.com/gl/voyager-gl-style/style.json',
                                    center: [90.4125, 23.8103], // [lng, lat]
                                    zoom: 13
                                });

                                map.addControl(new maplibregl.NavigationControl());

                                var marker = new maplibregl.Marker({ draggable: true });
                                var locationInput = document.getElementById('location');

                                function updateMarker(lat, lng) {
                                    marker.setLngLat([lng, lat]).addTo(map);
                                    map.flyTo({ center: [lng, lat], zoom: 15 });
                                    locationInput.value = lat + ',' + lng;
                                }

                                marker.on('dragend', function () {
                                    var lngLat = marker.getLngLat();
                                    locationInput.value = lngLat.lat + ',' + lngLat.lng;
                                });

                                map.on('click', function (e) {
                                    updateMarker(e.lngLat.lat, e.lngLat.lng);
                                });

                                map.on('load', function () {
                                    if (locationInput.value) {
                                        var parts = locationInput.value.split(',');
                                        if (parts.length === 2) {
                                            updateMarker(parseFloat(parts[0]), parseFloat(parts[1]));
                                        }
                                    } else if (navigator.geolocation) {
                                        navigator.geolocation.getCurrentPosition(function (position) {
                                            if (!locationInput.value) {
                                                updateMarker(position.coords.latitude, position.coords.longitude);
                                            }
                                        }, function () {
                                            console.log("Geolocation failed or denied.");
                                        });
                                    }
                                });
                            });
                        </script>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('user.credentials.index') }}"
                                class="text-gray-500 hover:text-gray-700 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Update Credentials') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>