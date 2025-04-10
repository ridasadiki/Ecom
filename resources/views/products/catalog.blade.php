@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="relative py-16">
            <div class="relative">
                <div class="text-center">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block">Discover Amazing</span>
                        <span class="block text-indigo-600">Products</span>
                    </h1>
                    <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                        Browse our curated collection of high-quality products. Find exactly what you're looking for with our advanced filters.
                    </p>
                </div>
            </div>
        </div>

        <!-- Search and Sort Bar -->
        <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 mb-8">
            <form method="GET" action="{{ route('products.index') }}" class="w-full md:w-96">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                <input type="hidden" name="sort" value="{{ request('sort', 'latest') }}">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                        class="w-full bg-white rounded-full pl-5 pr-12 py-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm" 
                        placeholder="Search products...">
                    <button type="submit" class="absolute right-4 top-3">
                        <svg class="h-6 w-6 text-gray-400 hover:text-indigo-500 transition-colors duration-150" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </form>

            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Sort by:</label>
                <select onchange="this.form.submit()" name="sort" form="filter-form"
                    class="rounded-full border-gray-300 py-2 pl-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm bg-white">
                    <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="price_low" {{ $sort === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ $sort === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Name</option>
                </select>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <div class="w-full lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-8">
                    <form id="filter-form" method="GET" action="{{ route('products.index') }}" class="space-y-8">
                        <input type="hidden" name="search" value="{{ $search ?? '' }}">
                        <!-- Categories -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                            <div class="space-y-3">
                                @foreach($categories as $category)
                                <div class="flex items-center">
                                    <input type="radio" name="category" value="{{ $category->id }}"
                                        {{ $selectedCategory == $category->id ? 'checked' : '' }}
                                        onchange="this.form.submit()"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded-full">
                                    <label class="ml-3 text-sm text-gray-600 hover:text-gray-900">
                                        {{ $category->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Price Range</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Price ($)</label>
                                    <input type="number" name="min_price" min="0" step="0.01"
                                        value="{{ $minPrice ?? '' }}" 
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Min price">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Price ($)</label>
                                    <input type="number" name="max_price" min="0" step="0.01"
                                        value="{{ $maxPrice ?? '' }}" 
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Max price">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <button type="submit" 
                                class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                Apply Filters
                            </button>
                            <a href="{{ route('products.index') }}" 
                                class="block w-full text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-150">
                                Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($products as $product)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <div class="aspect-w-4 aspect-h-3">
                            @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" 
                                class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300">
                            @else
                            <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-indigo-600">{{ $product->category->name }}</span>
                                <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <a href="{{ route('products.show', $product) }}" class="hover:text-indigo-600">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500 mb-4 line-clamp-2">
                                {{ $product->description }}
                            </p>
                            <form action="{{ route('cart.add', $product) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                    class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full">
                        <div class="text-center py-12 bg-white rounded-2xl shadow-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No products found</h3>
                            <p class="mt-2 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                            <a href="{{ route('products.index') }}" 
                                class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Clear All Filters
                            </a>
                        </div>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
