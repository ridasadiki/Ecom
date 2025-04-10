@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Product Details</h1>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.products.edit', $product) }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Product
                        </a>
                        <a href="{{ route('admin.products.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                            </svg>
                            Back to Products
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        @if($product->image)
                            <div class="relative group rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center" style="height: 400px;">
                                <img src="{{ Storage::url($product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="object-cover w-full h-full">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-opacity duration-200 flex items-center justify-center">
                                    <span class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        AI Generated Image
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="h-96 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-24 h-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h2>
                            <p class="mt-2 text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                        </div>

                        <div class="flex items-baseline">
                            <span class="text-2xl font-semibold text-gray-900">${{ number_format($product->price, 2) }}</span>
                            <span class="ml-4 px-2.5 py-0.5 text-xs font-semibold text-indigo-800 bg-indigo-100 rounded-full">
                                {{ $product->category->name }}
                            </span>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Description</h3>
                            <div class="mt-2 prose prose-sm text-gray-500">
                                {{ $product->description }}
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-sm font-medium text-gray-900">Product Details</h3>
                            <dl class="mt-4 space-y-4">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                                    <dd class="text-sm text-gray-900">{{ $product->created_at->format('M d, Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ $product->updated_at->format('M d, Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Category</dt>
                                    <dd class="text-sm text-gray-900">{{ $product->category->name }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <form action="{{ route('admin.products.destroy', $product) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete Product
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
