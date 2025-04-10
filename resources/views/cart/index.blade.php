@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Shopping Cart</h1>
            <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-500 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Continue Shopping
            </a>
        </div>

        @if(session('success'))
        <div class="mb-8 rounded-lg bg-green-50 p-4 border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-8 rounded-lg bg-red-50 p-4 border border-red-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if($cart->items->count() > 0)
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6">
                <ul role="list" class="divide-y divide-gray-200">
                    @foreach($cart->items as $item)
                    <li class="py-6 flex items-center">
                        <div class="flex-shrink-0 w-24 h-24 bg-gray-100 rounded-lg overflow-hidden">
                            @if($item->product->image)
                            <img src="{{ Storage::url($item->product->image) }}" 
                                alt="{{ $item->product->name }}" 
                                class="w-full h-full object-center object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            @endif
                        </div>

                        <div class="ml-6 flex-1 flex flex-col">
                            <div class="flex justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <a href="{{ route('products.show', $item->product) }}" class="hover:text-indigo-600">
                                            {{ $item->product->name }}
                                        </a>
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ $item->product->category->name }}</p>
                                </div>
                                <p class="text-lg font-medium text-gray-900">${{ number_format($item->price * $item->quantity, 2) }}</p>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center space-x-3">
                                    @csrf
                                    @method('PATCH')
                                    <label for="quantity" class="text-sm font-medium text-gray-700">Quantity</label>
                                    <select name="quantity" 
                                        class="rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                        onchange="this.form.submit()">
                                        @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ $item->quantity == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                        @endfor
                                    </select>
                                </form>

                                <form action="{{ route('cart.remove', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-500 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>

                <div class="mt-8 border-t border-gray-200 pt-8">
                    <div class="flex justify-between text-base font-medium text-gray-900">
                        <p>Subtotal</p>
                        <p>${{ number_format($cart->items->sum(function($item) { return $item->price * $item->quantity; }), 2) }}</p>
                    </div>
                    <p class="mt-0.5 text-sm text-gray-500">Shipping and taxes will be calculated at checkout.</p>

                    <div class="mt-6 space-y-4">
                        <a href="#" class="flex justify-center items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Proceed to Checkout
                        </a>

                        <form action="{{ route('cart.clear') }}" method="POST" class="text-center">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-gray-600 hover:text-gray-500 flex items-center justify-center w-full">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Clear Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center bg-white shadow-lg rounded-lg p-12">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Your cart is empty</h3>
            <p class="mt-2 text-sm text-gray-500">Start adding some amazing products to your cart!</p>
            <div class="mt-6">
                <a href="{{ route('products.index') }}" 
                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Start Shopping
                    <svg class="ml-2 -mr-1 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
