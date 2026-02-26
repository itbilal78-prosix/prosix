@extends('layouts.dashboard')
@section('content')

    <div class="container mt-4">

        <h2 class="text-black mb-4">Edit Blog</h2>

        <a href="{{ route('blogs.index') }}" class="btn btn-outline-dark mb-3">
            <i class="bi bi-arrow-left-circle me-2"></i> Back to List
        </a>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-dark border border-dark mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data"
            class="p-4 border rounded shadow-sm bg-white">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div class="mb-3">
                <label class="fw-semibold text-black">Title</label>
                <input type="text" name="title" class="form-control border-dark" value="{{ $blog->title }}" required>
            </div>

            {{-- Description --}}
            <div class="mb-3">
                <label class="fw-semibold text-black">Description</label>
                <textarea name="description" class="form-control border-dark" required>{{ $blog->description }}</textarea>
            </div>

            {{-- Body Content --}}
            <div class="mb-3">
                <label class="fw-semibold text-black">Body Content</label>
                <textarea name="body" class="form-control border-dark" rows="6" required>{{ $blog->body }}</textarea>
            </div>

            {{-- Image Upload --}}
            <div class="mb-3">
                <label class="fw-semibold text-black">Image</label>
                <input type="file" name="image" class="form-control border-dark" accept="image/*"
                    onchange="previewImage(this,'imagePreview')">
                <div class="mt-2">
                    <img id="imagePreview" src="{{ $blog->image ? asset('storage/' . $blog->image) : '' }}" width="150"
                        class="border rounded" style="{{ $blog->image ? '' : 'display:none;' }}">
                </div>
            </div>

            {{-- Video Upload --}}
            {{-- Video Upload --}}
            <div class="mb-3">
                <label class="fw-semibold text-black">Video</label>

                <input type="file" name="video" class="form-control border-dark" accept="video/*"
                    onchange="previewVideo(this)">

                <div class="mt-2">
                    <video id="videoPreview" width="300" controls class="border rounded"
                        style="{{ $blog->video ? '' : 'display:none;' }}">

                        <source id="videoSource" src="{{ $blog->video ? asset('storage/' . $blog->video) : '' }}"
                            type="video/mp4">

                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark px-4 py-2">
                    <i class="bi bi-pencil-square me-2"></i> Update Blog
                </button>
                <a href="{{ route('blogs.index') }}" class="btn btn-outline-dark px-4 py-2">Cancel</a>
            </div>

        </form>
    </div>

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

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

@endsection
