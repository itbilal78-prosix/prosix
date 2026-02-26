@extends('layouts.dashboard')
@section('content')
    <h2 class="text-black">{{ isset($blog) ? 'Edit Blog' : 'Add New Blog' }}</h2>

    <form action="{{ isset($blog) ? route('blogs.update', $blog->id) : route('blogs.store') }}" method="POST"
        enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-white">
        @csrf
        @if (isset($blog))
            @method('PUT')
        @endif

        {{-- Title --}}
        <div class="mb-3">
            <label class="fw-semibold text-black">Title</label>
            <input type="text" name="title" class="form-control border-dark" value="{{ $blog->title ?? '' }}" required>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label class="fw-semibold text-black">Description</label>
            <textarea name="description" class="form-control border-dark" required>{{ $blog->description ?? '' }}</textarea>
        </div>

        {{-- Body --}}
        <div class="mb-3">
            <label class="fw-semibold text-black">Body Content</label>
            <textarea name="body" class="form-control border-dark" rows="6" required>{{ $blog->body ?? '' }}</textarea>
        </div>

        {{-- Image --}}
        <div class="mb-3">
            <label class="fw-semibold text-black">Image</label>
            <input type="file" name="image" class="form-control border-dark" accept="image/*"
                onchange="previewImage(this,'imagePreview')">
            @if (isset($blog) && $blog->image)
                <img id="imagePreview" src="{{ asset('storage/' . $blog->image) }}" width="150"
                    class="mt-2 border rounded">
            @else
                <img id="imagePreview" src="" width="150" class="mt-2 border rounded" style="display:none;">
            @endif
        </div>

        {{-- Video --}}
        {{-- Video --}}
        <div class="mb-3">
            <label class="fw-semibold text-black">Video</label>
            <input type="file" name="video" class="form-control border-dark" accept="video/*"
                onchange="previewVideo(this)">

            <video id="videoPreview" width="300" controls class="mt-2 border rounded" style="display:none;">
                <source id="videoSource" src="" type="video/mp4">
                Your browser does not support the video tag.
            </video>

            @if (isset($blog) && $blog->video)
                <video width="300" controls class="mt-2 border rounded">
                    <source src="{{ asset('storage/' . $blog->video) }}" type="video/mp4">
                </video>
            @endif
        </div>

        {{-- Buttons --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-dark px-4 py-2">
                {{ isset($blog) ? 'Update Blog' : 'Create Blog' }}
            </button>
            <a href="{{ route('blogs.index') }}" class="btn btn-outline-dark px-4 py-2">Cancel</a>
        </div>

    </form>

    {{-- Image Preview Script --}}
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
            }
        }

        function previewVideo(input) {
            const preview = document.getElementById('videoPreview');
            const source = document.getElementById('videoSource');

            if (input.files && input.files[0]) {
                const fileURL = URL.createObjectURL(input.files[0]);
                source.src = fileURL;
                preview.load();
                preview.style.display = 'block';
            }
        }
    </script>

    {{-- Black & White Theme --}}
    <style scoped>
        .form-control.border-dark {
            border: 1px solid #000;
            color: #000;
        }

        .form-control::placeholder {
            color: #6c757d;
        }

        .btn-dark {
            background-color: #000;
            color: #fff;
            border: 1px solid #000;
            transition: all 0.3s ease;
        }

        .btn-dark:hover {
            background-color: #fff;
            color: #000;
            border-color: #000;
        }

        .btn-outline-dark {
            color: #000;
            border: 1px solid #000;
            transition: all 0.3s ease;
        }

        .btn-outline-dark:hover {
            background-color: #000;
            color: #fff;
        }

        img.border {
            border: 1px solid #000;
            border-radius: 8px;
        }
    </style>
@endsection
