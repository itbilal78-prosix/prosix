@extends('layouts.dashboard')

@section('content')
    <div class="container mt-4">
        <h1 class="text-black mb-4">Add New Deal</h1>

        <form action="{{ route('deals.store') }}" method="POST" enctype="multipart/form-data"
            class="p-4 border rounded shadow-sm bg-white">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold text-black">Title *</label>
                <input type="text" name="title" class="form-control border-dark text-black" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-black">Subtitle</label>
                <input type="text" name="subtitle" class="form-control border-dark text-black">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-black">Description</label>
                <textarea name="description" class="form-control border-dark text-black" rows="4"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-black">Button Text</label>
                <input type="text" name="button_text" class="form-control border-dark text-black">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-black">Button Link</label>
                <input type="text" name="button_link" class="form-control border-dark text-black">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-black">Upload Images (max 6)</label>
                <input type="file" name="images[]" multiple class="form-control border-dark" accept="image/*"
                    id="dealImagesInput">
                <div class="mt-2 d-flex flex-wrap gap-2" id="dealImagePreview"></div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold text-black">
                    Upload Banner Images (Unlimited)
                </label>
                <input type="file" name="banners[]" multiple class="form-control border-dark" accept="image/*"
                    id="bannerImagesInput">

                <div class="mt-2 d-flex flex-wrap gap-2" id="bannerImagePreview"></div>
                <button type="submit" class="btn btn-dark">Save Deal</button>
                <a href="{{ route('deals.index') }}" class="btn btn-outline-dark">Cancel</a>
        </form>
    </div>

    <script>
        // Live preview for deal images
        document.getElementById('dealImagesInput').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('dealImagePreview');
            previewContainer.innerHTML = '';
            Array.from(e.target.files).forEach(file => {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.classList.add('img-thumbnail');
                img.style.width = '80px';
                img.style.height = '80px';
                img.style.objectFit = 'cover';
                previewContainer.appendChild(img);
            });
        });
    </script>
    <script>
        document.getElementById('bannerImagesInput')
            .addEventListener('change', function(e) {

                const previewContainer = document.getElementById('bannerImagePreview');
                previewContainer.innerHTML = '';

                Array.from(e.target.files).forEach(file => {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.classList.add('img-thumbnail');
                    img.style.width = '120px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    previewContainer.appendChild(img);
                });
            });
    </script>

    <style scoped>
        /* Black & White Theme */
        .text-black {
            color: #000 !important;
        }

        .text-white {
            color: #fff !important;
        }

        .form-control.border-dark {
            border: 1px solid #000 !important;
            color: #000 !important;
            background-color: #fff;
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

        .img-thumbnail {
            border: 1px solid #000;
            border-radius: 8px;
        }
    </style>
@endsection
