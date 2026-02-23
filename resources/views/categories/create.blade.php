@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h2>{{ request()->has('parent_id') ? 'Add Subcategory' : 'Add New Category' }}</h2>

    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Navigation / Parent Category</label>
            
            @if(request()->has('parent_id'))
                @php
                    $parent = \App\Models\Category::find(request()->get('parent_id'));
                @endphp
                <input type="text" class="form-control" value="{{ $parent->name }}" disabled>
                <input type="hidden" name="parent_id" value="{{ $parent->id }}">
            @else
                <select name="navigation_id" class="form-select">
                    <option value="">Select Navigation (Dropdown ke liye)</option>
                    @foreach($navigations as $nav)
                        <option value="{{ $nav->id }}">
                            {{ $nav->title }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

      <div class="mb-3">
    <label>Highlight</label>
    <input type="checkbox" name="highlight" value="1"> <!-- value="1" zaroori hai -->
</div>

  <div class="mb-3 highlight-image-field" style="display: {{ old('highlight') || (isset($category) && $category->highlight) ? 'block' : 'none' }};">
    <label>Highlight Special Image</label>
    <input type="file" name="highlight_image" class="form-control" onchange="previewImage(this, 'highlightPreview')">
    <img id="highlightPreview" src="" class="mt-2" style="max-width: 150px; display: none; border:1px solid #000; border-radius:4px;">
</div>
        <div class="mb-3">
            <label>Set Password?</label>
            <input type="checkbox" id="hasPassword" onchange="togglePasswordField()">
        </div>

        <div class="mb-3" id="passwordField" style="display:none;">
            <label>Password</label>
            <input type="text" name="password" class="form-control">
        </div>



<div class="mb-3">
    <label>Icon Image</label>
    <input type="file" name="icon_image" class="form-control" onchange="previewImage(this, 'iconPreview')">
    <img id="iconPreview" src="" class="mt-2" style="max-width: 100px; display: none; border:1px solid #000; border-radius:4px;">
</div>


        <button class="btn save_btn">Save</button>
    </form>

<style>
    .save_btn{
        background-color: black;
        color: white;
    }
    .save_btn:hover{
 background-color: rgb(255, 255, 255);
    color: rgb(0, 0, 0);
    border: 1px solid black;
    transform: translateY(-2px);    }
</style>
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}

// Password toggle
function togglePasswordField() {
    const checkbox = document.getElementById('hasPassword');
    document.getElementById('passwordField').style.display = checkbox.checked ? 'block' : 'none';
}

// Highlight toggle
document.querySelector('input[name="highlight"]').addEventListener('change', function() {
    document.querySelector('.highlight-image-field').style.display = this.checked ? 'block' : 'none';
});

    </script>
</div>
@endsection
