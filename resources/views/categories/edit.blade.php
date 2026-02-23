@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ $category->parent_id ? 'Edit Subcategory' : 'Edit Category' }}</h2>

    <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="p-4 border rounded bg-white">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="form-label fw-bold">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Status</label>
            <select name="status" class="form-select">
                <option value="1" {{ old('status', $category->status) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !old('status', $category->status) ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Protect parent_id for subcategories -->
        @if($category->parent_id)
            <input type="hidden" name="parent_id" value="{{ $category->parent_id }}">
            <div class="mb-4">
                <label class="form-label fw-bold">Parent Category</label>
                <input type="text" class="form-control bg-light" value="{{ $category->parent?->name ?? '—' }}" disabled>
            </div>
        @else
            <div class="mb-4">
                <label class="form-label fw-bold">Navigation</label>
                <select name="navigation_id" class="form-select">
                    <option value="">No navigation (standalone)</option>
                    @foreach($navigations as $nav)
                        <option value="{{ $nav->id }}" {{ old('navigation_id', $category->navigation_id) == $nav->id ? 'selected' : '' }}>
                            {{ $nav->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="mb-4 form-check">
            <input type="checkbox" name="highlight" value="1" class="form-check-input" id="highlight" {{ old('highlight', $category->highlight) ? 'checked' : '' }}>
            <label class="form-check-label" for="highlight">Highlight on homepage</label>
        </div>

        <div class="mb-4" id="highlightField" style="display: {{ old('highlight', $category->highlight) ? 'block' : 'none' }};">
            <label class="form-label fw-bold">Highlight Image</label>
            <input type="file" name="highlight_image" class="form-control" accept="image/*">
            @if($category->highlight_image)
                <img src="{{ $category->highlight_image }}" alt="Preview" class="img-thumbnail mt-2" width="140">
            @endif
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Icon Image</label>
            <input type="file" name="icon_image" class="form-control" accept="image/*">
            @if($category->icon_image)
                <img src="{{ $category->icon_image }}" alt="Preview" class="img-thumbnail mt-2" width="120">
            @endif
        </div>

        <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="hasPassword" {{ $category->password ? 'checked' : '' }}>
            <label class="form-check-label" for="hasPassword">Password protected?</label>
        </div>

        <div class="mb-4" id="passwordField" style="display: {{ $category->password ? 'block' : 'none' }};">
            <label class="form-label fw-bold">Password</label>
            <input type="password" name="password" class="form-control" placeholder="{{ $category->password ? '••••••• (leave blank to keep)' : 'New password' }}">
            <small class="text-muted">Leave blank to keep current password</small>
        </div>

        <button type="submit" class="btn btn-dark px-5">Update</button>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary ms-3">Cancel</a>
    </form>
</div>

<script>
document.getElementById('hasPassword').addEventListener('change', function() {
    document.getElementById('passwordField').style.display = this.checked ? 'block' : 'none';
});

document.getElementById('highlight').addEventListener('change', function() {
    document.getElementById('highlightField').style.display = this.checked ? 'block' : 'none';
});
</script>
@endsection