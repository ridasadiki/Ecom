@extends('layouts.admin')

@section('title', 'Generate Product')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Generate Product with AI</h2>
                    <a href="{{ route('admin.products.index') }}" 
                        class="btn-secondary">
                        Back to Products
                    </a>
                </div>

                @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
                @endif
                
                <form id="generateForm" class="space-y-6 max-w-2xl mx-auto">
                    @csrf
                    
                    <!-- Product Keywords -->
                    <div>
                        <label for="keywords" class="form-label">Product Keywords</label>
                        <p class="text-sm text-gray-500 mb-2">
                            Enter descriptive keywords for your product (e.g., "modern leather office chair")
                        </p>
                        <input type="text" name="keywords" id="keywords" required
                            class="input-field @error('keywords') border-red-500 @enderror"
                            placeholder="Enter product keywords">
                        @error('keywords')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" id="category_id" required
                            class="input-field @error('category_id') border-red-500 @enderror">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image Style -->
                    <div>
                        <label for="image_style" class="form-label">Image Style</label>
                        <p class="text-sm text-gray-500 mb-2">
                            Additional style keywords for the image (e.g., "minimalist, professional photo, white background")
                        </p>
                        <input type="text" name="image_style" id="image_style"
                            class="input-field"
                            value="professional product photography, white background, high resolution"
                            placeholder="Enter image style keywords">
                    </div>

                    <div id="previewSection" class="hidden space-y-4 border-t pt-4">
                        <h3 class="text-lg font-semibold">Generated Preview</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium mb-2">Product Details</h4>
                                <div id="productDetails" class="bg-gray-50 p-4 rounded-lg"></div>
                            </div>
                            <div>
                                <h4 class="font-medium mb-2">Product Image</h4>
                                <img id="productImage" class="w-full h-64 object-contain bg-gray-100 rounded-lg" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4">
                        <button type="button" onclick="window.location.href='{{ route('admin.products.index') }}'" 
                            class="btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary" id="generateBtn">
                            Generate Product
                        </button>
                        <button type="button" class="btn-primary hidden" id="saveBtn">
                            Save Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('generateForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const generateBtn = document.getElementById('generateBtn');
    const saveBtn = document.getElementById('saveBtn');
    const previewSection = document.getElementById('previewSection');
    
    try {
        generateBtn.disabled = true;
        generateBtn.innerHTML = 'Generating...';
        
        const response = await fetch('{{ route("admin.products.generate.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                keywords: document.getElementById('keywords').value,
                category_id: document.getElementById('category_id').value,
                image_style: document.getElementById('image_style').value
            })
        });
        
        if (!response.ok) {
            throw new Error('Failed to generate product');
        }

        const data = await response.json();
        if (data.success) {
            // Show preview
            document.getElementById('productDetails').innerHTML = `
                <p><strong>Name:</strong> ${data.product.name}</p>
                <p><strong>Description:</strong> ${data.product.description}</p>
                <p><strong>Price:</strong> $${data.product.price}</p>
                <p><strong>Category:</strong> ${data.product.category_name}</p>
            `;
            document.getElementById('productImage').src = '/storage/' + data.product.image;
            previewSection.classList.remove('hidden');
            generateBtn.classList.add('hidden');
            saveBtn.classList.remove('hidden');
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert(error.message || 'Failed to generate product. Please try again.');
    } finally {
        generateBtn.disabled = false;
        generateBtn.innerHTML = 'Generate Product';
    }
});

document.getElementById('saveBtn').addEventListener('click', function() {
    window.location.href = '{{ route("admin.products.index") }}';
});
</script>
@endpush
