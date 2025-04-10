@extends('layout.app')

@section('content')
<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Create New Categor</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Create New Category</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card card-default ">
    <div class="card-body">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="name" class="form-control select2" placeholder="Enter category name" required>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <button type="submit" class="btn btn-primary">Save Category</button>
        </form>
    </div>
    <!-- /.card-body -->
</div>
</div>
</section >
</div>
</div>
<!-- /.card -->

@endsection
