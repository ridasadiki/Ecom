@extends('layouts.app')

@section('content')
<div class="bg-white">
    <div class="max-w-2xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:max-w-7xl lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
            <!-- Image gallery -->
            <div class="flex flex-col">
                <div class="aspect-w-4 aspect-h-3 rounded-lg bg-gray-100 overflow-hidden">
                    @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" 
                        class="object-cover object-center w-full h-full">
                    @else
                    <div class="flex items-center justify-center h-full bg-gray-200">
                        <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product info -->
            <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">{{ $product->name }}</h1>
                
                <div class="mt-3">
                    <p class="text-3xl text-gray-900">${{ number_format($product->price, 2) }}</p>
                </div>

                <div class="mt-6">
                    <h3 class="sr-only">Description</h3>
                    <div class="text-base text-gray-700 space-y-6">
                        {{ $product->description }}
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="ml-2 text-sm text-gray-500">In stock</p>
                    </div>
                </div>

                <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-4">
                    @csrf
                    <div class="flex items-center space-x-4">
                        <input type="number" name="quantity" value="1" min="1" 
                               class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                            Add to Cart
                        </button>
                    </div>
                </form>
                @auth
                    <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="mt-4 inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-red-600">
                            @if(auth()->user()->hasInWishlist($product))
                                <svg class="w-6 h-6 fill-current text-red-600" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            @endif
                        </button>
                    </form>
                @endauth

                <!-- Category -->
                <div class="mt-6">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <p class="ml-2 text-sm text-gray-500">Category: {{ $product->category->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-16">
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Related Products</h2>
            
            <div class="mt-6 grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($relatedProducts as $relatedProduct)
                <div class="group relative">
                    <div class="aspect-w-4 aspect-h-3 rounded-lg overflow-hidden bg-gray-100">
                        @if($relatedProduct->image)
                        <img src="{{ Storage::url($relatedProduct->image) }}" alt="{{ $relatedProduct->name }}" 
                            class="object-cover object-center w-full h-full">
                        @else
                        <div class="flex items-center justify-center h-full bg-gray-200">
                            <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="mt-4">
                        <h3 class="text-sm text-gray-700">
                            <a href="{{ route('products.show', $relatedProduct) }}">
                                {{ $relatedProduct->name }}
                            </a>
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $relatedProduct->category->name }}</p>
                        <p class="mt-1 text-sm font-medium text-gray-900">${{ number_format($relatedProduct->price, 2) }}</p>
                    </div>
                    <div class="mt-2">
                        <form action="{{ route('cart.add', $relatedProduct) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-indigo-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">Customer Reviews</h2>
            
            @auth
                @if(!$product->reviews()->where('user_id', auth()->id())->exists())
                    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                        <h3 class="text-lg font-semibold mb-4">Write a Review</h3>
                        <form action="{{ route('reviews.store', $product) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Rating</label>
                                <div class="flex items-center space-x-2 mt-1">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" class="hidden peer" required>
                                        <label for="rating{{ $i }}" class="cursor-pointer text-2xl text-gray-300 peer-checked:text-yellow-400">★</label>
                                    @endfor
                                </div>
                            </div>
                            <div>
                                <label for="comment" class="block text-sm font-medium text-gray-700">Your Review</label>
                                <textarea name="comment" id="comment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Submit Review</button>
                        </form>
                    </div>
                @endif
            @endauth

            <div class="space-y-6">
                @forelse($product->reviews()->with('user')->where('is_approved', true)->latest()->get() as $review)
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <div class="flex items-center">
                                    <span class="text-yellow-400">
                                        @for($i = 5; $i >= 1; $i--)
                                            @if($i <= $review->rating)
                                                ★
                                            @else
                                                ☆
                                            @endif
                                        @endfor
                                    </span>
                                    @if($review->verified_purchase)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Verified Purchase
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600">By {{ $review->user->name }} • {{ $review->created_at->diffForHumans() }}</p>
                            </div>
                            @if(auth()->check() && (auth()->id() === $review->user_id || auth()->user()->isAdmin()))
                                <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                </form>
                            @endif
                        </div>
                        <p class="text-gray-700">{{ $review->comment }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">No reviews yet. Be the first to review this product!</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
