@extends('layouts.dashboard')

@section('content')
<div class="container">
<h2>
    @if(request()->has('parent_id'))
        Add Subcategory 
        @php
            $parent = \App\Models\Category::find(request()->get('parent_id'));
        @endphp
        (Parent: {{ $parent?->name ?? 'N/A' }})
    @else
        Add Subcategory 
    @endif
</h2>

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

        <div class="form-group mb-3">
            <label>Parent Category <span class="text-danger">*</span></label>
            <select name="parent_id" class="form-control" required>
                <option value="">Select Parent Category</option>
                @foreach($parentCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Highlight</label>
            <input type="checkbox" name="highlight">
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
            <img id="iconPreview" src="" class="mt-2" style="max-width: 120px; display: none; border:1px solid #000; border-radius:4px;">
        </div>

        <button class="btn save_btn">Save</button>
    </form>

    <style>
        .save_btn{
            background-color: black;
            color: white;
            border: 1px solid black;
            transition: all 0.3s ease;
        }
        .save_btn:hover{
            background-color: #fff;
            color: #000;
            border: 1px solid black;
            transform: translateY(-2px);
        }
    </style>

    <script>
        function togglePasswordField() {
            const checkbox = document.getElementById('hasPassword');
            document.getElementById('passwordField').style.display = checkbox.checked ? 'block' : 'none';
        }

        // Image Preview Function
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
    </script>
</div>
@endsection
