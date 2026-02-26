@extends('layouts.dashboard')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-black mb-0">All Blogs</h2>

    <a href="{{ route('blogs.create') }}" class="btn btn-dark">
        <i class="bi bi-plus-lg me-1"></i> Add New Blog
    </a>
</div>

@if(session('success'))
    <div class="alert alert-dark border border-dark">{{ session('success') }}</div>
@endif

<table class="table table-bordered border-dark">
    <thead class="table-light">
        <tr class="text-black">
            <th>Title</th>
            <th>Description</th>
            <th>Image</th>
            <th>Video</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($blogs as $blog)
        <tr>
            <td>{{ $blog->title }}</td>
            <td>{{ $blog->description }}</td>
            <td>
                @if($blog->image)
                    <img src="{{ asset('storage/'.$blog->image) }}" width="100" class="border rounded">
                @endif
            </td>
            <td>
                @if($blog->video)
                    <a href="{{ asset('storage/'.$blog->video) }}" target="_blank" class="text-black">
                        <i class="bi bi-play-circle me-1"></i> Video
                    </a>
                @endif
            </td>
            <td>
                <a href="{{ route('blogs.edit',$blog->id) }}" class="btn btn-sm btn-dark me-1">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                <form action="{{ route('blogs.destroy',$blog->id) }}" method="POST" style="display:inline-block">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-dark" onclick="return confirm('Delete?')">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $blogs->links() }}

{{-- Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style scoped>
.btn-dark, .btn-outline-dark {
    transition: all 0.3s ease;
}
.btn-dark:hover, .btn-outline-dark:hover {
    background-color: #fff;
    color: #000;
    border-color: #000;
}
.table-bordered, .table-bordered th, .table-bordered td {
    border: 1px solid #000 !important;
}
.text-black { color: #000 !important; }
img.border { border: 1px solid #000; border-radius: 6px; }
</style>

@endsection
