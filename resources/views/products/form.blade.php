@extends('layout.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Generate Product with AI</h4>
                </div>
                <div class="card-body">
                    <!-- Generation Form -->
                    <form id="generateForm" class="mb-4">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select form-select-lg mb-3" id="category" required>
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="keywords" class="form-label">Keywords (comma separated)</label>
                                <input type="text" class="form-control form-control-lg" id="keywords" placeholder="e.g., modern, stylish, durable" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg">Generate Product</button>
                            </div>
                        </div>
                    </form>

                    <!-- Loading Overlay -->
                    <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="background: rgba(0,0,0,0.5); z-index: 1050;">
                        <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
                            <div class="spinner-border mb-2" role="status"></div>
                            <p class="mb-0">Generating product...</p>
                        </div>
                    </div>

                    <!-- Result Container -->
                    <div id="resultContainer" class="d-none">
                        <hr>
                        <h5 class="mb-4">Generated Product Details</h5>
                        <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label">Product Name</label>
                                        <input type="text" class="form-control form-control-lg" id="name" name="name" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="price" class="form-label">Price ($)</label>
                                        <input type="number" step="0.01" class="form-control form-control-lg" id="price" name="price" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-select form-select-lg" id="category_id" name="category_id" required>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Generated Image</label>
                                        <div class="border rounded p-3 text-center bg-light position-relative" style="min-height: 300px;">
                                            <div id="imageLoadingSpinner" class="position-absolute top-50 start-50 translate-middle d-none">
                                                <div class="spinner-border text-primary" role="status"></div>
                                                <p class="mt-2 text-primary">Generating image...</p>
                                            </div>
                                            <div id="imageError" class="position-absolute top-50 start-50 translate-middle text-danger d-none">
                                                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                                <p class="mb-0">Failed to generate image</p>
                                            </div>
                                            <img id="productImage" src="" alt="Generated product image" class="img-fluid mb-2 d-none" style="max-height: 300px; object-fit: contain;">
                                            <input type="hidden" id="image" name="image">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success btn-lg">Save Product</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@push('scripts')
<script>
document.getElementById('generateForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const loadingOverlay = document.getElementById('loadingOverlay');
    const resultContainer = document.getElementById('resultContainer');
    const imageLoadingSpinner = document.getElementById('imageLoadingSpinner');
    const imageError = document.getElementById('imageError');
    const productImage = document.getElementById('productImage');
    
    try {
        loadingOverlay.classList.remove('d-none');
        
        // Get form values
        const category_id = document.getElementById('category').value;
        const keywords = document.getElementById('keywords').value;
        
        // Generate product data
        const response = await fetch('/generate-product', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                category_id: category_id,
                keywords: keywords 
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Failed to generate product');
        }

        const data = await response.json();

        // Fill in the form with generated data
        document.getElementById('name').value = data.product.name;
        document.getElementById('description').value = data.product.description;
        document.getElementById('price').value = data.product.price;
        document.getElementById('category_id').value = category_id;

        // Show the result container first
        resultContainer.classList.remove('d-none');
        
        // Reset image states
        imageError.classList.add('d-none');
        imageLoadingSpinner.classList.remove('d-none');
        productImage.classList.add('d-none');

        // Generate image based on product name and description
        const imagePrompt = `${data.product.name} - ${data.product.description.split('.')[0]}`;
        console.log('Generating image with prompt:', imagePrompt);
        
        const imageResponse = await fetch('/generate-image', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ prompt: imagePrompt })
        });

        const imageData = await imageResponse.json();
        console.log('Image generation response:', imageData);

        if (!imageResponse.ok || imageData.error) {
            throw new Error(imageData.error || 'Failed to generate image');
        }

        // Create a new image object to preload the image
        const img = new Image();
        img.onload = function() {
            // Hide loading spinner and show the image once it's loaded
            imageLoadingSpinner.classList.add('d-none');
            imageError.classList.add('d-none');
            productImage.classList.remove('d-none');
        };
        img.onerror = function() {
            throw new Error('Failed to load the generated image');
        };

        // Set the source using the full URL from the response
        const imagePath = imageData.url || `/storage/${imageData.image}`;
        img.src = imagePath;
        productImage.src = imagePath;
        document.getElementById('image').value = imageData.image;

    } catch (error) {
        console.error('Error:', error);
        imageLoadingSpinner.classList.add('d-none');
        imageError.classList.remove('d-none');
        imageError.querySelector('p').textContent = error.message;
    } finally {
        loadingOverlay.classList.add('d-none');
    }
});

// Add form submission handler
document.getElementById('productForm').addEventListener('submit', function(e) {
    const imageInput = document.getElementById('image');
    if (!imageInput.value) {
        e.preventDefault();
        alert('Please wait for the image to be generated before saving the product.');
        return;
    }

    // Log form data before submission
    const formData = new FormData(this);
    console.log('Submitting product with data:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ':', value);
    }
});
</script>
@endpush
@endsection
