@extends('layouts.dashboard')

@section('content')
    <div class="container mt-4">
        <h1 class="text-black mb-4">Edit Deal</h1>

        <form action="{{ route('deals.update', $deal) }}" method="POST" enctype="multipart/form-data"
            class="p-4 border rounded shadow-sm bg-white">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-black">Title *</label>
                <input type="text" name="title" class="form-control border-dark text-black"
                    value="{{ old('title', $deal->title) }}" required>
            </div>

            <!-- Subtitle -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-black">Subtitle</label>
                <input type="text" name="subtitle" class="form-control border-dark text-black"
                    value="{{ old('subtitle', $deal->subtitle) }}">
            </div>

            <hr>

            <!-- ================= IMAGES ================= -->
            <h5 class="fw-bold text-black mb-3">Deal Images</h5>

            <div class="row">
                @foreach ($deal->images as $img)
                    <div class="col-md-3 mb-4">

                        <img src="{{ $img->image_path }}" class="img-fluid img-thumbnail mb-2"
                            id="preview{{ $img->id }}">

                        <!-- Replace Image -->
                        <input type="file" name="replace_images[{{ $img->id }}]"
                            class="form-control border-dark mb-2" accept="image/*"
                            onchange="previewImage(event, {{ $img->id }})">

                        <!-- Existing Link -->
                        <input type="text" name="existing_links[{{ $img->id }}]"
                            class="form-control border-dark mb-2" value="{{ $img->link }}" placeholder="Image Link">

                        <!-- Existing Ribbon -->
                        <input type="text" name="existing_labels[{{ $img->id }}]" class="form-control border-dark"
                            value="{{ $img->label }}" placeholder="Ribbon Text (SALE / NEW)">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="delete_images[]"
                                value="{{ $img->id }}">
                            <label class="form-check-label text-danger">
                                Delete Image
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <hr>

            <!-- ADD NEW IMAGES -->
            <h5 class="fw-bold text-black mb-3">Add New Images</h5>

            <div id="newImagesWrapper"></div>

            <button type="button" class="btn btn-outline-dark mb-4" onclick="addNewImageField()">
                + Add Image
            </button>

            <hr>

            <!-- ================= BANNERS ================= -->
            <h5 class="fw-bold text-black mb-3">Deal Banners</h5>

            <div class="row">
                @foreach ($deal->banners as $banner)
                    <div class="col-md-4 mb-4">

                        <img src="{{ $banner->image_path }}" class="img-fluid img-thumbnail mb-2"
                            id="bannerPreview{{ $banner->id }}">

                        <input type="file" name="replace_banners[{{ $banner->id }}]" class="form-control border-dark"
                            accept="image/*" onchange="previewBanner(event, {{ $banner->id }})">
                              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="delete_banners[]" value="{{ $banner->id }}">

                <label class="form-check-label text-danger">
                    Delete Banner
                </label>
            </div>
                    </div>
                @endforeach
            </div>

            <!-- Add New Banners -->
            <div class="mb-4">
                <input type="file" name="banners[]" multiple class="form-control border-dark" accept="image/*">
            </div>

            <button type="submit" class="btn btn-dark">Update Deal</button>
            <a href="{{ route('deals.index') }}" class="btn btn-outline-dark">Cancel</a>

        </form>
    </div>

    <script>
        function previewImage(event, id) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('preview' + id).src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function previewBanner(event, id) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('bannerPreview' + id).src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function addNewImageField() {

            const wrapper = document.getElementById('newImagesWrapper');

            const div = document.createElement('div');
            div.classList.add('mb-3', 'border', 'p-3', 'rounded');

            div.innerHTML = `
        <label class="fw-semibold">Image</label>
        <input type="file" name="images[]" class="form-control mb-2" accept="image/*" required>

        <label class="fw-semibold">Image Link</label>
        <input type="text" name="links[]" class="form-control mb-2" placeholder="Optional link">

        <label class="fw-semibold">Ribbon Text</label>
        <input type="text" name="labels[]" class="form-control mb-2" placeholder="SALE / NEW">

        <button type="button"
            class="btn btn-sm btn-danger"
            onclick="this.parentElement.remove()">
            Remove
        </button>
    `;

            wrapper.appendChild(div);
        }
    </script>
@endsection
