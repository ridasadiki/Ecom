@extends('layout.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Categories</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Category</a></li>
                    <li class="breadcrumb-item active">All Categories</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<div class="container mt-4">

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('categories.create') }}" class="btn btn-primary">Add Category</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning btn-sm">Edit</a>

                            <!-- Delete Button to Open Modal -->
                            <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
                Are you sure you want to delete <strong id="categoryName"></strong>?
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
        const categoryName = document.getElementById("categoryName");
        
        document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", function () {
                const categoryId = this.getAttribute("data-id");
                const name = this.getAttribute("data-name");

                categoryName.textContent = name;
                deleteForm.setAttribute("action", "/categories/" + categoryId);
            });
        });
    });
</script>
@endsection
