@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1>Add Product</h1>
        <a href="{{ route('products.index') }}" class="btn btn-dark">Back</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

  
    <div class="card shadow-sm">
        <div class="card-body">
            <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
                </div>

                <!-- Category OPTIONAL -->
                <div class="mb-3">
                    <label class="form-label">Category <small class="text-muted">(optional)</small></label>
                    <select name="category_id" id="categorySelect" class="form-control">
                        <option value="">-- No Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Subcategory OPTIONAL -->
                <div class="mb-3" id="subcategoryWrapper" style="display:none;">
                    <label class="form-label">Subcategory <small class="text-muted">(optional)</small></label>
                    <select name="subcategory_id" id="subcategorySelect" class="form-control">
                        <option value="">-- No Subcategory --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" id="imageInput" class="form-control">
                    <img id="previewImage" class="mt-3 rounded d-none" style="max-width:150px;border:1px solid #ddd;">
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
    const subcategoryWrapper = document.getElementById('subcategoryWrapper');
    const imageInput = document.getElementById('imageInput');
    const previewImage = document.getElementById('previewImage');

    function loadSubcategories(catId) {
        subcategorySelect.innerHTML = '<option value="">-- No Subcategory --</option>';
        if (!catId) { subcategoryWrapper.style.display = 'none'; return; }
        const cat = categories.find(c => c.id === parseInt(catId));
        if (cat && cat.subcategories && cat.subcategories.length > 0) {
            cat.subcategories.forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub.id;
                opt.textContent = sub.name;
                subcategorySelect.appendChild(opt);
            });
            subcategoryWrapper.style.display = 'block';
        } else {
            subcategoryWrapper.style.display = 'none';
        }
    }

    categorySelect.addEventListener('change', function () { loadSubcategories(this.value); });

    // Restore old values on validation error
    const oldCatId = '{{ old('category_id') }}';
    const oldSubId = '{{ old('subcategory_id') }}';
    if (oldCatId) {
        loadSubcategories(oldCatId);
        setTimeout(() => { subcategorySelect.value = oldSubId; }, 50);
    }

    // Image preview
    imageInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            previewImage.src = URL.createObjectURL(this.files[0]);
            previewImage.classList.remove('d-none');
        }
    });
});
</script>
@endsection
