@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Stats Cards -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Products</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalProducts }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Customers</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalUsers }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Sales Chart -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Sales Trend (Last 7 Days)</h3>
                    <canvas id="salesChart" height="300"></canvas>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Products by Category</h3>
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>

            <!-- Customer Registration Trend -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg col-span-2">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Registration Trend (Last 6 Weeks)</h3>
                    <canvas id="customerTrendChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <a href="{{ route('admin.products.create') }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Product
                    </a>

                    <a href="{{ route('admin.products.generate') }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        Generate Product
                    </a>

                    <button onclick="showQuickCategoryModal()"
                        class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Add Category
                    </button>

                    <a href="{{ route('admin.customers.index') }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        View Customers
                    </a>
                </div>
            </div>
        </div>

        <!-- Modal for Quick Category Creation -->
        <div id="quickCategoryModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" style="z-index: 50;">
            <div class="min-h-screen px-4 text-center">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                
                <!-- This element is to trick the browser into centering the modal contents. -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <iframe id="quickCategoryFrame" src="" class="w-full" style="height: 280px; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($salesData->pluck('date')) !!},
            datasets: [{
                label: 'Sales ($)',
                data: {!! json_encode($salesData->pluck('total_sales')->map(function($value) { return $value ?? 0; })) !!},
                borderColor: 'rgb(59, 130, 246)',
                tension: 0.1
            }, {
                label: 'Orders',
                data: {!! json_encode($salesData->pluck('order_count')->map(function($value) { return $value ?? 0; })) !!},
                borderColor: 'rgb(16, 185, 129)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Category Distribution Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryData->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($categoryData->pluck('count')->map(function($value) { return $value ?? 0; })) !!},
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(139, 92, 246)',
                    'rgb(244, 63, 94)',
                    'rgb(234, 179, 8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });

    // Customer Registration Trend Chart
    const customerCtx = document.getElementById('customerTrendChart').getContext('2d');
    new Chart(customerCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($customerTrend->pluck('week')) !!},
            datasets: [{
                label: 'New Customers',
                data: {!! json_encode($customerTrend->pluck('count')->map(function($value) { return $value ?? 0; })) !!},
                backgroundColor: 'rgb(59, 130, 246)',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            return 'Week of ' + context[0].label;
                        }
                    }
                }
            }
        }
    });

    function showQuickCategoryModal() {
        const modal = document.getElementById('quickCategoryModal');
        const frame = document.getElementById('quickCategoryFrame');
        frame.src = '{{ route("admin.categories.create.quick") }}';
        modal.classList.remove('hidden');
    }

    function closeModal() {
        const modal = document.getElementById('quickCategoryModal');
        const frame = document.getElementById('quickCategoryFrame');
        frame.src = '';
        modal.classList.add('hidden');
    }
</script>
@endpush
@endsection
