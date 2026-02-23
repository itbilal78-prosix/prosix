@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1>Add Product</h1>
        <a href="{{ route('products.index') }}" class="btn btn-dark">Back</a>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Product Name -->
                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <!-- Price -->
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>

                <!-- Category -->
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" id="categorySelect" class="form-control" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Subcategory -->
                <div class="mb-3">
                    <label class="form-label">Subcategory</label>
                    <select name="subcategory_id" id="subcategorySelect" class="form-control">
                        <option value="">Select Subcategory</option>
                    </select>
                </div>

                <!-- Image -->
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" id="imageInput" class="form-control">
                    <img id="previewImage" class="mt-3 rounded d-none" style="max-width:150px; border:1px solid #ddd;">
                </div>

                <button type="submit" class="btn btn-dark">Save Product</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const categories = @json($categories);
    const categorySelect = document.getElementById('categorySelect');
    const subcategorySelect = document.getElementById('subcategorySelect');
    const imageInput = document.getElementById('imageInput');
    const previewImage = document.getElementById('previewImage');
    const form = document.getElementById('productForm');

    // Category → Subcategory
    categorySelect.addEventListener('change', function () {
        const id = parseInt(this.value);
        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
        if (!id) return;
        const category = categories.find(c => c.id === id);
        if (category && category.subcategories.length) {
            category.subcategories.forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub.id;
                opt.textContent = sub.name;
                subcategorySelect.appendChild(opt);
            });
        }
    });

    // Image preview
    imageInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            previewImage.src = URL.createObjectURL(this.files[0]);
            previewImage.classList.remove('d-none');
        }
    });

    // Form submit validation
    form.addEventListener('submit', function (e) {
        const catId = parseInt(categorySelect.value);
        if (!catId) {
            alert('Please select a category.');
            e.preventDefault();
            return;
        }

        const category = categories.find(c => c.id === catId);
        if (category && category.subcategories.length && !subcategorySelect.value) {
            alert('This category has subcategories. Please select a subcategory.');
            e.preventDefault();
            return;
        }
    });
});
</script>
@endsection
