<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white dark:bg-gray-900">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Att-Ch8') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans antialiased text-gray-900 dark:text-gray-100 flex">

    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 w-full">
        <div class="mx-auto w-full max-w-sm lg:w-96">
            <div>
                <a href="/" class="flex items-center gap-2">
                    <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-indigo-600">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400">Att-Ch8</span>
                </a>
                <h2 class="mt-8 text-2xl font-bold leading-9 tracking-tight text-gray-900 dark:text-white">Welcome back
                </h2>
                <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Securely connect your platforms and automate your tasks.
                </p>
            </div>

            <div class="mt-10">
                {{ $slot }}
            </div>
        </div>
    </div>

    <div class="relative hidden w-0 flex-1 lg:block bg-indigo-600 dark:bg-indigo-900">
        <div
            class="absolute inset-0 h-full w-full object-cover flex items-center justify-center flex-col text-center p-12 bg-gradient-to-br from-indigo-500 to-indigo-800 dark:from-indigo-800 dark:to-gray-900">
            <h1 class="text-4xl font-extrabold text-white mb-4">Master Your Schedule</h1>
            <p class="text-indigo-200 text-lg max-w-lg">
                Let Att-Ch8 handle your repetitive HR tasks securely. Just set the targets, and we manage the execution
                and notifications on your behalf.
            </p>
        </div>
    </div>

</body>

</html>