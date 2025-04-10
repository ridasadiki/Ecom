@extends('layouts.admin')

@section('title', 'Customer Details')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Customer Details</h2>
                    <a href="{{ route('admin.customers.index') }}" class="btn-secondary">Back to Customers</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->email ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Joined Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at ? $user->created_at->format('F d, Y') : 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order History</h3>
                        @if($user->orders && $user->orders->count() > 0)
                            <div class="space-y-4">
                                @foreach($user->orders as $order)
                                    <div class="border rounded-lg p-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-sm font-medium text-gray-900">Order #{{ $order->id }}</span>
                                            <span class="text-sm text-gray-500">{{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600">Total: ${{ number_format($order->total ?? 0, 2) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No orders found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
