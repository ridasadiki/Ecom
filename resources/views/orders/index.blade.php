BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_app_cluster@extends('layout.app')

@section('csrf')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">All Orders</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Order</a></li>
                    <li class="breadcrumb-item active">All Orders</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<div class="container mt-4">
    

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('orders.create') }}" class="btn btn-primary">Add Order</a>
    </div>

    <!-- Table without the "table-responsive" class -->
    <div class="table-container">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Client Name</th>
                    <th>Client Address</th>
                    <th>Client Phone</th>
                    <th>Delivered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->product->name }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->total_price }}</td>
                        <td>{{ $order->client_name }}</td>
                        <td>{{ $order->client_address }}</td>
                        <td>{{ $order->client_phone }}</td>
                        <td>
                            <!-- Delivered Split Button Dropdown -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-{{ $order->is_delivered == 1 ? 'success' : 'danger' }}">
                                    {{ $order->is_delivered == 1 ? 'Delivered' : 'Not Delivered' }}
                                </button>
                                <button type="button" class="btn btn-{{ $order->is_delivered == 1 ? 'success' : 'danger' }} dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-delivery-status="1" data-order-id="{{ $order->id }}">Delivered</a></li>
                                    <li><a class="dropdown-item" href="#" data-delivery-status="0" data-order-id="{{ $order->id }}">Not Delivered</a></li>
                                </ul>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning btn-sm">Edit</a>

                            <!-- Delete Button to Open Modal -->
                            <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $order->id }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript to Handle Delivered Dropdown Change -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropdownItems = document.querySelectorAll(".dropdown-item");

        dropdownItems.forEach(item => {
            item.addEventListener("click", function(event) {
                const isDelivered = this.getAttribute("data-delivery-status");
                const orderId = this.getAttribute("data-order-id");

                // Send the update via AJAX
                fetch(`/orders/${orderId}/update-delivery-status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        is_delivered: isDelivered
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("Delivery status updated");
                        // Update the button text accordingly
                        const button = this.closest('.btn-group').querySelector('.btn');
                        button.textContent = isDelivered == 1 ? 'Delivered' : 'Not Delivered';
                        button.classList.remove('btn-danger', 'btn-success');
                        button.classList.add(isDelivered == 1 ? 'btn-success' : 'btn-danger');
                    } else {
                        console.error("Error updating delivery status");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                });
            });
        });
    });
</script>

@endsection
