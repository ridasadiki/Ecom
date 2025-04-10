<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - @yield('title')</title>
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
</head>
<body class="bg-gray-100">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-indigo-600">
                            Admin Dashboard
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('admin.products.index') }}" 
                           class="{{ request()->routeIs('admin.products.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Products
                        </a>
                        <a href="{{ route('admin.categories.index') }}" 
                           class="{{ request()->routeIs('admin.categories.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Categories
                        </a>
                        <a href="{{ route('admin.customers.index') }}" 
                           class="{{ request()->routeIs('admin.customers.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Customers
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="text-gray-600 hover:text-gray-900 group flex items-center px-2 py-2 text-base font-medium rounded-md">
                            <svg class="text-gray-400 group-hover:text-gray-500 mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Categories
                        </a>

                        <a href="{{ route('admin.chat.index') }}" class="text-gray-600 hover:text-gray-900 group flex items-center px-2 py-2 text-base font-medium rounded-md">
                            <svg class="text-gray-400 group-hover:text-gray-500 mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            Customer Chat
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-500 mr-4">{{ Auth::user()->email }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="ml-3">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
