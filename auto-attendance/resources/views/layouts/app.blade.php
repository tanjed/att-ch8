<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50 dark:bg-gray-900">

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

<body class="h-full font-sans antialiased text-gray-900 dark:text-white" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80"></div>

        <div class="fixed inset-0 flex">
            <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                class="relative mr-16 flex w-full max-w-xs flex-1">

                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Sidebar content -->
                <div
                    class="flex flex-col gap-y-5 overflow-y-auto bg-white dark:bg-gray-800 px-6 pb-4 shadow-xl w-full h-full">
                    @include('layouts.navigation')
                </div>
            </div>
        </div>
    </div>

    <!-- Static Desktop Sidebar -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
        <div
            class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 px-6 pb-4">
            @include('layouts.navigation')
        </div>
    </div>

    <!-- Main Content Wrapper -->
    <div class="lg:pl-72 flex flex-col min-h-screen">
        <!-- Mobile Header bar -->
        <div
            class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8 lg:hidden">
            <button type="button" class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-200 lg:hidden"
                @click="sidebarOpen = true">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12h18M3 6h18m-18 6h18" />
                </svg>
            </button>
            <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6 justify-end items-center">
                <span
                    class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">{{ Auth::user()->name ?? 'User' }}</span>
            </div>
        </div>

        <!-- Page Heading -->
        @isset($header)
            <header
                class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 hidden lg:block">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    {{ $header }}

                    <div class="flex items-center gap-4">
                        <span
                            class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name ?? 'User' }}
                            ({{ Auth::user()->role ?? 'Role' }})</span>
                        <!-- Profile Dropdown (Simplified) -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200 px-4 py-2 bg-indigo-50 dark:bg-indigo-900/40 rounded-md">Logout</button>
                        </form>
                    </div>
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-1 py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        <!-- Donation Banner (Consumer Only) -->
        @if(Auth::check() && Auth::user()->role === 'CONSUMER')
            <div x-data="{ showDonationBanner: sessionStorage.getItem('hideDonationBanner') !== 'true' }"
                x-show="showDonationBanner"
                class="fixed bottom-0 left-0 right-0 z-50 pointer-events-none lg:pl-72 flex justify-center pb-4 px-4 sm:px-6 lg:px-8">
                <div
                    class="pointer-events-auto flex flex-col sm:flex-row items-center justify-between gap-y-3 sm:gap-x-6 bg-indigo-600 px-6 py-4 sm:rounded-xl shadow-lg w-full max-w-2xl relative">
                    <div class="flex-1 text-center sm:text-left flex flex-col sm:flex-row sm:items-center sm:gap-4">
                        <p class="text-base font-bold text-white whitespace-nowrap">
                            Buy me a halim 🍲
                        </p>
                        <div class="hidden sm:block w-px h-6 bg-indigo-400"></div>
                        <p class="text-sm text-indigo-100 flex items-center justify-center sm:justify-start gap-2">
                            <span>Send bKash:</span>
                            <span
                                class="font-bold text-yellow-300 text-base tracking-wider bg-indigo-700/50 px-2 py-0.5 rounded border border-indigo-400/30">01629535307</span>
                        </p>
                    </div>
                    <button @click="showDonationBanner = false; sessionStorage.setItem('hideDonationBanner', 'true')"
                        type="button"
                        class="absolute top-2 right-2 sm:static sm:top-auto sm:right-auto p-1.5 hover:bg-indigo-500 rounded-md transition-colors text-indigo-200 hover:text-white flex-shrink-0">
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path
                                d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
</body>

</html>