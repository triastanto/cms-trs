<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', setting('site_name', config('app.name')))</title>
    <meta name="description" content="@yield('description', setting('site_description', ''))">

    <!-- SEO Meta Tags -->
    @stack('meta')

    <!-- RSS Feed -->
    <link rel="alternate" type="application/rss+xml" title="{{ setting('site_name', config('app.name')) }} RSS Feed" href="{{ route('rss') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="antialiased bg-white text-gray-900">
    <!-- Header -->
    <header class="border-b border-gray-200">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-900 hover:text-gray-700 transition">
                        {{ setting('site_name', config('app.name')) }}
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex md:items-center md:gap-8">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition {{ request()->routeIs('home') ? 'text-gray-900' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('blog.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition {{ request()->routeIs('blog.*') ? 'text-gray-900' : '' }}">
                        Blog
                    </a>

                    <!-- Search Icon -->
                    <button
                        type="button"
                        onclick="document.getElementById('search-input').focus()"
                        class="text-gray-500 hover:text-gray-900 transition"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile menu button -->
                <button
                    type="button"
                    class="md:hidden text-gray-500 hover:text-gray-900"
                    onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="flex flex-col gap-2">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900 py-2 {{ request()->routeIs('home') ? 'text-gray-900' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('blog.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900 py-2 {{ request()->routeIs('blog.*') ? 'text-gray-900' : '' }}">
                        Blog
                    </a>
                </div>
            </div>
        </nav>

        <!-- Search Bar -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 border-t border-gray-100">
            <form action="{{ route('blog.search') }}" method="GET" class="max-w-lg mx-auto">
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        id="search-input"
                        placeholder="Search articles..."
                        value="{{ request('q') }}"
                        class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                    >
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 mt-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                        About
                    </h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        {{ setting('site_description', 'A minimal blog built with Laravel and Filament.') }}
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                        Quick Links
                    </h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                Home
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('blog.index') }}" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                Blog
                            </a>
                        </li>
                        <li>
                            <a href="/admin" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                Admin
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- RSS & Social -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                        Subscribe
                    </h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="/rss" class="text-sm text-gray-600 hover:text-gray-900 transition inline-flex items-center gap-2">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19 7.38 20 6.18 20C5 20 4 19 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1z"/>
                                </svg>
                                RSS Feed
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-200 mt-8 pt-8 text-center">
                <p class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} {{ setting('site_name', config('app.name')) }}. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
