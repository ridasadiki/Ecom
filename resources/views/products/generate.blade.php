@extends('layout.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Generate New Product</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Generate New Product</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-default">
            <div class="card-body">
                <!-- Simple Form with GET Method -->
                <form action="{{ route('getPrompt', ['category' => ':category', 'keys' => ':keywords']) }}" method="GET" id="generateForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category" id="categorySelect" class="form-control" required>
                                    <option value="" disabled selected>Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Keywords</label>
                                <textarea name="keywords" id="keywordsInput" class="form-control" placeholder="Enter product keywords" required></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Generate Product</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
