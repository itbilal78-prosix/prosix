@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1>Edit Product</h1>
        <a href="{{ route('products.index') }}" class="btn btn-dark">Back</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="productForm" action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                </div>

                <!-- Category & Subcategory -->
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" id="categorySelect" class="form-control" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

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
                    <img id="previewImage" class="mt-3 rounded" style="max-width:150px; border:1px solid #ddd;"
                        src="{{ $product->image ? asset('storage/' . $product->image) : '' }}">
                </div>

                <button type="submit" class="btn btn-dark">Update Product</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categories = @json($categories);
    const categorySelect = document.getElementById('categorySelect');
    const subcategorySelect = document.getElementById('subcategorySelect');
    const imageInput = document.getElementById('imageInput');
    const previewImage = document.getElementById('previewImage');
    const form = document.getElementById('productForm');

    const selectedCategory = parseInt(categorySelect.value);
    const selectedSubcategory = {{ old('subcategory_id', $product->subcategory_id ?? 'null') }};
    
    // Populate subcategories for the selected category
    function populateSubcategories(categoryId, selectedSub = null){
        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
        if(!categoryId) return;
        const category = categories.find(c => c.id === categoryId);
        if(category && category.subcategories.length){
            category.subcategories.forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub.id;
                opt.textContent = sub.name;
                if(selectedSub && selectedSub == sub.id) opt.selected = true;
                subcategorySelect.appendChild(opt);
            });
        }
    }

    populateSubcategories(selectedCategory, selectedSubcategory);

    categorySelect.addEventListener('change', function(){
        populateSubcategories(parseInt(this.value));
    });

    // Image preview
    imageInput.addEventListener('change', function(){
        if(this.files && this.files[0]){
            previewImage.src = URL.createObjectURL(this.files[0]);
        }
    });

    // Form submit validation: enforce subcategory if category has subcategories
    form.addEventListener('submit', function(e){
        const catId = parseInt(categorySelect.value);
        if(!catId){
            alert('Please select a category.');
            e.preventDefault();
            return;
        }
        const category = categories.find(c => c.id === catId);
        if(category && category.subcategories.length > 0 && !subcategorySelect.value){
            alert('This category has subcategories. Please select a subcategory.');
            e.preventDefault();
            return;
        }
    });
});
</script>
@endsection
