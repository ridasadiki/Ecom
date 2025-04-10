@extends('layout.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">All Products</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Products</a></li>
                    <li class="breadcrumb-item active">All Products</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<div class="container mt-4">

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('products.create') }}" class="btn btn-primary m-1 ">Add Product</a>
        <!-- Generate Product Button -->
        <a href="{{ route('openai.form') }}" class="btn btn-secondary m-1">Generate Product</a>

    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="img-thumbnail" width="50">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm">Edit</a>

                            <!-- Delete Button to Open Modal -->
                            <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Generate Product Modal -->
<div class="modal fade" id="generateProductModal" tabindex="-1" aria-labelledby="generateProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateProductModalLabel">Generate Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Add form elements for category selection and keywords here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="generateProduct()">Generate</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="productName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to Handle Modal -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const deleteModal = document.getElementById("deleteModal");
        const deleteForm = document.getElementById("deleteForm");
        const productName = document.getElementById("productName");

        document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", function () {
                const productId = this.getAttribute("data-id");
                const name = this.getAttribute("data-name");

                productName.textContent = name;
                deleteForm.setAttribute("action", "/products/" + productId);
            });
        });
    });

    function generateProduct() {
        // Implement your API call here to generate product details
    }
</script>
@endsection
