<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Att-Ch8') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-slate-50 text-slate-900 selection:bg-indigo-500 selection:text-white dark:bg-slate-950 dark:text-slate-100 flex flex-col min-h-screen relative overflow-x-hidden">

    <!-- Background Aurora Glows -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-indigo-600/20 dark:bg-indigo-600/30 blur-[120px] mix-blend-multiply dark:mix-blend-lighten"></div>
        <div class="absolute top-[20%] -right-[10%] w-[40%] h-[50%] rounded-full bg-fuchsia-600/20 dark:bg-fuchsia-600/30 blur-[120px] mix-blend-multiply dark:mix-blend-lighten"></div>
        <div class="absolute -bottom-[20%] left-[20%] w-[60%] h-[40%] rounded-full bg-blue-600/20 dark:bg-blue-600/30 blur-[120px] mix-blend-multiply dark:mix-blend-lighten"></div>
    </div>

    <!-- Navigation -->
    <header class="absolute top-0 w-full z-50 py-6">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between" aria-label="Top">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/30">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-2xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-indigo-700 to-purple-700 dark:from-indigo-400 dark:to-purple-400">Att-Ch8</span>
            </div>
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center py-2.5 px-6 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition-all hover:scale-105">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center justify-center py-2.5 px-5 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-200/50 dark:hover:bg-slate-800/50 transition-colors">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center py-2.5 px-6 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 transition-all hover:-translate-y-0.5">Start Free</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <div class="relative z-10 flex-grow flex items-center pt-32 pb-20 lg:pt-48 lg:pb-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="lg:grid lg:grid-cols-12 lg:gap-16 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left lg:col-span-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100/50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium text-sm mb-6 border border-indigo-200 dark:border-indigo-800/50">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-500 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                        </span>
                        Pi-HR Automation Now Live
                    </div>
                    <h1 class="text-5xl tracking-tight font-extrabold text-slate-900 dark:text-white sm:leading-tight lg:text-6xl xl:text-7xl">
                        <span class="block mb-2">Automate your checks</span>
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">with Att-Ch8</span>
                    </h1>
                    <p class="mt-6 text-base text-slate-600 dark:text-slate-300 sm:text-lg lg:text-xl font-medium leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Take back your time with automated HR platform check-ins. Schedule your active windows, provide your credentials, and let our secure background workers handle your daily interactions seamlessly.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-2xl text-lg font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 shadow-xl shadow-indigo-500/30 hover:shadow-indigo-500/50 transition-all hover:-translate-y-1">
                                    Go to Dashboard
                                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-xl text-lg font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 shadow-xl shadow-indigo-500/30 hover:shadow-indigo-500/50 transition-all hover:-translate-y-1">
                                    Get Started Free
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 border-2 border-slate-200 dark:border-slate-800 text-lg font-bold rounded-xl text-slate-700 dark:text-slate-200 bg-white/50 dark:bg-slate-900/50 hover:bg-white dark:hover:bg-slate-800 shadow-sm transition-all hover:-translate-y-1 backdrop-blur-sm">
                                    Sign In
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>

                <!-- Right Content (Card) -->
                <div class="mt-20 lg:mt-0 lg:col-span-6 relative">
                    <!-- Decorative Elements -->
                    <div class="absolute -top-6 -right-6 w-32 h-32 bg-yellow-400/20 rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-blue-400/20 rounded-full blur-2xl"></div>
                    
                    <div class="relative rounded-[2rem] backdrop-blur-xl bg-white/70 dark:bg-slate-900/70 border border-white/50 dark:border-slate-700/50 shadow-2xl overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 pointer-events-none"></div>
                        <div class="p-8 sm:p-10 relative z-10">
                            <div class="flex items-center justify-between mb-8">
                                <span class="text-sm font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">How it works</span>
                                <div class="flex gap-2">
                                    <div class="h-3 w-3 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                                    <div class="h-3 w-3 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                                    <div class="h-3 w-3 rounded-full bg-indigo-500 shadow-sm shadow-indigo-500/50"></div>
                                </div>
                            </div>
                            
                            <div class="space-y-8">
                                <!-- Step 1 -->
                                <div class="group flex gap-5">
                                    <div class="flex-shrink-0 mt-1 h-12 w-12 flex items-center justify-center rounded-2xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-extrabold text-xl shadow-inner group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 group-hover:scale-110 group-hover:shadow-indigo-500/30">
                                        1
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Connect Platform</h3>
                                        <p class="text-slate-600 dark:text-slate-400 font-medium leading-relaxed">Add your authentication details securely for supported platforms including <span class="font-extrabold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-1.5 py-0.5 rounded">Pi-HR</span>, Workday, BambooHR, and more.</p>
                                    </div>
                                </div>
                                <!-- Step 2 -->
                                <div class="group flex gap-5">
                                    <div class="flex-shrink-0 mt-1 h-12 w-12 flex items-center justify-center rounded-2xl bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 font-extrabold text-xl shadow-inner group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 group-hover:scale-110 group-hover:shadow-purple-500/30">
                                        2
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Schedule Action</h3>
                                        <p class="text-slate-600 dark:text-slate-400 font-medium leading-relaxed">Pick a time, set your precise latitude/longitude coordinates, and toggle the automated action active.</p>
                                    </div>
                                </div>
                                <!-- Step 3 -->
                                <div class="group flex gap-5">
                                    <div class="flex-shrink-0 mt-1 h-12 w-12 flex items-center justify-center rounded-2xl bg-pink-100 dark:bg-pink-900/50 text-pink-700 dark:text-pink-300 font-extrabold text-xl shadow-inner group-hover:bg-pink-600 group-hover:text-white transition-all duration-300 group-hover:scale-110 group-hover:shadow-pink-500/30">
                                        3
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Automate & Notify</h3>
                                        <p class="text-slate-600 dark:text-slate-400 font-medium leading-relaxed">Our robust background workers execute the payload and instantly notify you via Mailgun or Teams.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="relative z-10 py-8 border-t border-slate-200/50 dark:border-slate-800/50 mt-auto backdrop-blur-lg bg-white/50 dark:bg-slate-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                &copy; {{ date('Y') }} Att-Ch8 Platform. All rights reserved.
            </span>
            <div class="flex space-x-6 text-slate-400">
            </div>
        </div>
    </footer>

</body>
</html>