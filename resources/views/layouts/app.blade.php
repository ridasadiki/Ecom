<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel E-Commerce') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5', // indigo-600
                    }
                }
            }
        }
    </script>

    <!-- Custom Styles -->
    <style type="text/tailwindcss">
        @layer utilities {
            .btn-primary {
                @apply px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200;
            }
            .btn-secondary {
                @apply px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors duration-200;
            }
            .input-field {
                @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500;
            }
            .form-label {
                @apply block text-sm font-medium text-gray-700;
            }
        }
    </style>

    <!-- Scripts -->
    <script>
        // Add any custom JavaScript here
    </script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600">
                                {{ config('app.name', 'Laravel E-Commerce') }}
                            </a>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('products.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                Products
                            </a>
                            @auth
                                <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-gray-900 relative">
                                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ Auth::check() ? Auth::user()->getCart()->items->count() : 0 }}
                                    </span>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('wishlist.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('wishlist.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                    <span class="relative inline-flex">
                                        <span>Wishlist</span>
                                        @if(auth()->user()->wishlistItems()->count() > 0)
                                            <span class="absolute -top-2 -right-4 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-indigo-600 rounded-full">
                                                {{ auth()->user()->wishlistItems()->count() }}
                                            </span>
                                        @endif
                                    </span>
                                </a>
                                <a href="{{ route('chat.index') }}" class="text-gray-600 hover:text-gray-900">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </a>
                            @endauth
                        </div>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        @guest
                            <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2">Login</a>
                            <a href="{{ route('register') }}" class="btn-primary ml-3">Register</a>
                        @else
                            <div class="ml-3 relative">
                                <div class="flex items-center">
                                    <span class="text-gray-700">{{ Auth::user()->name }}</span>
                                    @if(Auth::user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="ml-4 text-sm text-gray-500 hover:text-gray-700">Admin Dashboard</a>
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}" class="ml-4">
                                        @csrf
                                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel E-Commerce') }}. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    <!-- Alpine.js -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Stack for additional scripts -->
    @stack('scripts')
</body>
</html>
