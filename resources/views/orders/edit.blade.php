@extends('layout.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Order</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
                    <li class="breadcrumb-item active">Edit Order</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-default m-5">
            <div class="card-body">
                <form action="{{ route('orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Product</label>
                                <select name="product_id" class="form-control select2" required>
                                    <option value="" disabled>Select Product</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ $product->id == $order->product_id ? 'selected' : '' }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $order->quantity) }}" placeholder="Enter quantity" required min="1">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Client Name</label>
                                <input type="text" name="client_name" class="form-control" value="{{ old('client_name', $order->client_name) }}" placeholder="Enter client name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Client Address</label>
                                <input type="text" name="client_address" class="form-control" value="{{ old('client_address', $order->client_address) }}" placeholder="Enter client address" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Client Phone</label>
                                <input type="text" name="client_phone" class="form-control" value="{{ old('client_phone', $order->client_phone) }}" placeholder="Enter client phone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Delivered</label>
                                <select name="is_delivered" class="form-control custom-select" required>
                                    <option value="1" {{ old('is_delivered', $order->is_delivered ?? '') == 1 ? 'selected' : '' }}>Delivered</option>
                                    <option value="0" {{ old('is_delivered', $order->is_delivered ?? '') == 0 ? 'selected' : '' }}>Not Delivered</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Order</button>
                </form>
            </div><!-- /.card-body -->
        </div>
    </div>
</section>
@endsection
