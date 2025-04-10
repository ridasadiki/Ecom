@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">My Wishlist</h1>

    @if($wishlistItems->isEmpty())
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-500 mb-4">Your wishlist is empty</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Browse Products
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($wishlistItems as $item)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="relative pb-[56.25%]">
                        <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : asset('images/placeholder.png') }}" 
                             alt="{{ $item->product->name }}"
                             class="absolute h-full w-full object-cover">
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">{{ $item->product->name }}</h3>
                        <p class="text-gray-600 mb-2">{{ Str::limit($item->product->description, 100) }}</p>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-lg font-bold">${{ number_format($item->product->price, 2) }}</span>
                            <span class="text-sm text-gray-500">{{ $item->product->category->name }}</span>
                        </div>
                        <div class="flex space-x-2">
                            <form action="{{ route('wishlist.move-to-cart', $item) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    Move to Cart
                                </button>
                            </form>
                            <form action="{{ route('wishlist.toggle', $item->product) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-red-100 text-red-600 px-4 py-2 rounded-md hover:bg-red-200">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
