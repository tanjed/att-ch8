<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Att-Ch8') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="antialiased bg-gray-50 text-gray-900 font-sans selection:bg-indigo-500 selection:text-white dark:bg-gray-900 dark:text-gray-100 flex flex-col min-h-screen">

    <!-- Navigation -->
    <header class="absolute top-0 w-full z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Top">
            <div class="w-full py-6 flex items-center justify-between border-b border-indigo-500 lg:border-none">
                <div class="flex items-center">
                    <a href="#">
                        <span class="sr-only">Att-Ch8</span>
                        <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-indigo-600">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </a>
                    <span
                        class="ml-3 text-xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400">Att-Ch8</span>
                </div>
                <div class="ml-10 space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="inline-block bg-indigo-500 py-2 px-4 border border-transparent rounded-md text-base font-medium text-white hover:bg-opacity-75">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-block bg-white dark:bg-gray-800 py-2 px-4 border border-transparent rounded-md text-base font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700">Log
                                in</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="inline-block bg-indigo-600 dark:bg-indigo-500 py-2 px-4 border border-transparent rounded-md text-base font-medium text-white hover:bg-indigo-700 dark:hover:bg-indigo-600">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <div class="relative overflow-hidden flex-grow">
        <div class="absolute inset-y-0 h-full w-full" aria-hidden="true">
            <div class="relative h-full">
                <svg class="absolute right-full transform translate-y-1/3 translate-x-1/4 md:translate-y-1/2 sm:translate-x-1/2 lg:translate-x-full"
                    width="404" height="784" fill="none" viewBox="0 0 404 784">
                    <defs>
                        <pattern id="e229dbec-10e9-49ee-8ec3-0286ca089edf" x="0" y="0" width="20" height="20"
                            patternUnits="userSpaceOnUse">
                            <rect x="0" y="0" width="4" height="4" class="text-gray-200 dark:text-gray-800"
                                fill="currentColor" />
                        </pattern>
                    </defs>
                    <rect width="404" height="784" fill="url(#e229dbec-10e9-49ee-8ec3-0286ca089edf)" />
                </svg>
                <svg class="absolute left-full transform -translate-y-3/4 -translate-x-1/4 sm:-translate-x-1/2 md:-translate-y-1/2 lg:-translate-x-3/4"
                    width="404" height="784" fill="none" viewBox="0 0 404 784">
                    <defs>
                        <pattern id="d2a68204-c383-44b1-b99f-42ccff4e5365" x="0" y="0" width="20" height="20"
                            patternUnits="userSpaceOnUse">
                            <rect x="0" y="0" width="4" height="4" class="text-gray-200 dark:text-gray-800"
                                fill="currentColor" />
                        </pattern>
                    </defs>
                    <rect width="404" height="784" fill="url(#d2a68204-c383-44b1-b99f-42ccff4e5365)" />
                </svg>
            </div>
        </div>

        <div class="relative pt-32 pb-16 sm:pb-24 lg:pb-32">
            <main class="mt-16 sm:mt-24">
                <div class="mx-auto max-w-7xl">
                    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                        <div
                            class="px-4 sm:px-6 sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left lg:flex lg:items-center">
                            <div>
                                <h1
                                    class="mt-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:mt-5 sm:leading-none lg:mt-6 lg:text-5xl xl:text-6xl">
                                    <span class="block">Automate your checks</span>
                                    <span class="block text-indigo-600 dark:text-indigo-400">with Att-Ch8</span>
                                </h1>
                                <p
                                    class="mt-3 text-base text-gray-500 dark:text-gray-400 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl">
                                    Take back your time with automated HR platform check-ins. Schedule your active
                                    windows, provide your credentials, and let the background worker handle your daily
                                    interactions seamlessly.
                                </p>
                                <div class="mt-8 sm:max-w-lg sm:mx-auto sm:text-center lg:text-left lg:mx-0">
                                    <div class="mt-3 sm:mt-0">
                                        @if (Route::has('login'))
                                            @auth
                                                <a href="{{ url('/dashboard') }}"
                                                    class="block w-full rounded-md px-5 py-3 bg-indigo-600 text-base font-medium text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:px-10">Go
                                                    to Dashboard</a>
                                            @else
                                                <a href="{{ route('register') }}"
                                                    class="block w-full rounded-md px-5 py-3 bg-indigo-600 text-base font-medium text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:px-10">Get
                                                    started</a>
                                            @endauth
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-16 sm:mt-24 lg:mt-0 lg:col-span-6">
                            <div
                                class="bg-white dark:bg-gray-800 sm:max-w-md sm:w-full sm:mx-auto sm:rounded-lg sm:overflow-hidden shadow-xl ring-1 ring-black ring-opacity-5">
                                <div class="px-4 py-8 sm:px-10">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">How it works</p>
                                        <div class="mt-6 flex flex-col gap-6">
                                            <div class="flex items-start">
                                                <div
                                                    class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-md bg-indigo-500 text-white">
                                                    1
                                                </div>
                                                <div class="ml-4">
                                                    <h3
                                                        class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                                        Connect Platform</h3>
                                                    <p class="mt-2 text-base text-gray-500 dark:text-gray-400">Add your
                                                        authentication details securely for platforms like Workday,
                                                        BambooHR, etc.</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <div
                                                    class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-md bg-indigo-500 text-white">
                                                    2
                                                </div>
                                                <div class="ml-4">
                                                    <h3
                                                        class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                                        Schedule Action</h3>
                                                    <p class="mt-2 text-base text-gray-500 dark:text-gray-400">Pick a
                                                        time, set your latitude/longitude, and toggle the action active.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <div
                                                    class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-md bg-indigo-500 text-white">
                                                    3
                                                </div>
                                                <div class="ml-4">
                                                    <h3
                                                        class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                                        Automate & Notify</h3>
                                                    <p class="mt-2 text-base text-gray-500 dark:text-gray-400">Our
                                                        background workers execute the cURL request and notify you via
                                                        Mailgun or Teams Webhook.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 mt-auto">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
            <div class="mt-8 md:mt-0 md:order-1">
                <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} Att-Ch8 Platform. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

</body>

</html>