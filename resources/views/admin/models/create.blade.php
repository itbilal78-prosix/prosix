@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">

        <form action="{{ route('models.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card shadow-sm p-3">

                {{-- ERRORS --}}
                @if ($errors->any())
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- BASIC INFO: Name, Price, Description -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Model Name *</label>
                        <input type="text" name="model_name" class="form-control form-control-sm" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Title (Display Name)</label>
                        <input type="text" name="title" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Price ($)</label>
                        <input type="number" name="price" class="form-control form-control-sm" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control form-control-sm">
                    </div>
                </div>
                <!-- NAVIGATION, CATEGORY, SUBCATEGORY -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Navigation (optional)</label>
                        <select name="navigation_id" class="form-control form-control-sm">
                            <option value="">None</option>
                            @foreach ($navigations as $nav)
                                <option value="{{ $nav->id }}"
                                    {{ old('navigation_id', $model->navigation_id ?? '') == $nav->id ? 'selected' : '' }}>
                                    {{ $nav->title ?? $nav->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Category (optional)</label>
                        <select name="category_id" id="categorySelect" class="form-control form-control-sm">
                            <option value="">None</option>
                            @foreach ($parentCategories as $cat)
                                <!-- ← yahan $parentCategories likho -->
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $model->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Subcategory (optional)</label>
                        <select name="subcategory_id" id="subcategorySelect" class="form-control form-control-sm">
                            <option value="">None (select category first)</option>
                        </select>
                    </div>
                </div>

                <!-- IMAGE UPLOADS -->
                @php
                    $views = ['front', 'back', 'left', 'right'];
                    $colors = ['black', 'white', 'svg'];
                @endphp
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($views as $view)
                                @foreach ($colors as $color)
                                    <div class="image-box">
                                        <label class="small text-capitalize">{{ $view }} {{ $color }}</label>

                                        <input type="file" name="{{ $view }}_{{ $color }}"
                                            class="d-none" accept="{{ $color === 'svg' ? 'image/svg+xml' : 'image/png' }}"
                                            onchange="fieldPreview(this,'{{ $view }}','{{ $color }}')"
                                            id="{{ $view }}_{{ $color }}_input">

                                        <div class="img-preview empty-preview"
                                            onclick="document.getElementById('{{ $view }}_{{ $color }}_input').click()">
                                            <span>Click</span>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <!-- THUMBNAIL + 3D ROTATE -->
                    <div class="col-md-4">
                        <div class="border rounded p-2 text-center">
                            <div class="fw-bold small mb-2">3D Thumbnail Preview</div>

                            <div class="thumbnail-3d mb-2" id="thumbnail-3d">
                                @foreach ($colors as $color)
                                    <img id="p_{{ $color }}" class="layer-img">
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                    onclick="prevView()">
                                    <i class="bi bi-arrow-left"></i> Prev
                                </button>
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                    onclick="nextView()">
                                    Next <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>

                            <!-- SAVE / CANCEL BUTTONS -->
                            <button type="submit"
                                class="btn btn-save-mode btn-sm mb-1 w-100 d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-save"></i> Save Model
                            </button>
                            <a href="{{ route('models.index') }}"
                                class="btn btn-cancel-mode btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>

                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- STYLES --}}
    <style>
        /* Save button adaptive */
        .btn-save-mode {
            background-color: #000;
            /* dark background default */
            color: #fff;
            /* white text default */
            border: 1px solid #000;
        }

        [data-bs-theme="light"] .btn-save-mode {
            background-color: #fff;
            /* light background in light mode */
            color: #000;
            /* black text */
            border: 1px solid #000;
        }

        /* Cancel button adaptive */
        .btn-cancel-mode {
            background-color: transparent;
            color: #000;
            border: 1px solid #000;
        }

        [data-bs-theme="dark"] .btn-cancel-mode {
            color: #fff;
            border: 1px solid #fff;
        }

        /* Prev/Next buttons adaptive */
        .btn-outline-secondary {
            border-color: #000;
            color: #000;
        }

        [data-bs-theme="dark"] .btn-outline-secondary {
            border-color: #fff;
            color: #fff;
        }

        .image-box {
            width: calc(33.33% - 0.5rem);
            margin-bottom: 0.5rem;
        }

        .img-preview {
            width: 100%;
            height: 60px;
            object-fit: contain;
            border: 1px dotted #000;
            /* black dotted border */
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8f9fa;
            font-size: 10px;
            color: #000;
            /* black text */
            position: relative;
        }

        .img-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .img-preview .close-btn {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #fff;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 12px;
            color: #000;
            cursor: pointer;
            z-index: 10;
            border: 1px solid #000;
        }

        .empty-preview span {
            pointer-events: none;
        }

        /* 3D Thumbnail Preview */
        .thumbnail-3d {
            width: 180px;
            height: 180px;
            margin: auto;
            position: relative;
            border: 2px dashed #bbb;
            perspective: 1000px;
            overflow: hidden;
        }

        .layer-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        #p_black {
            mix-blend-mode: screen;
            z-index: 3;
        }

        #p_white {
            mix-blend-mode: multiply;
            z-index: 2;
        }

        #p_svg {
            z-index: 1;
        }
    </style>

    {{-- SCRIPT --}}
    <script>
        let views = ['front', 'back', 'left', 'right'];
        let currentView = 0;






        function fieldPreview(input, view, type) {
            const container = input.nextElementSibling; // the .img-preview div

            if (!input.files.length) {
                container.innerHTML = '<span>Click</span>';
                return;
            }

            const reader = new FileReader();

            reader.onload = e => {
                // Add image + close button
                container.innerHTML = `
            <img src="${e.target.result}" alt="${view}_${type}">
<div class="close-btn" onclick="removeImage('${view}','${type}')">×</div>
        `;

                // Update main 3D thumbnail (if front view)
                if (view === 'front') {
                    const t = document.getElementById('p_' + type);
                    t.src = e.target.result;
                }
            }

            reader.readAsDataURL(input.files[0]);
        }

        // Remove image function
        function removeImage(view, type) {
            const input = document.getElementById(view + '_' + type + '_input');
            input.value = ''; // clear file input
            const container = input.nextElementSibling;
            container.innerHTML = '<span>Click</span>';

            // Clear 3D thumbnail if front
            if (view === 'front') {
                const t = document.getElementById('p_' + type);
                t.src = '';
            }
        }

        // 3D Thumbnail rotation functions
        function showThumbnail(viewIndex) {
            const view = views[viewIndex];
            ['black', 'white', 'svg'].forEach(color => {
                const input = document.getElementById(view + '_' + color + '_input');
                if (input && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = document.getElementById('p_' + color);
                        img.src = e.target.result;
                    }
                    reader.readAsDataURL(input.files[0]);
                } else {
                    document.getElementById('p_' + color).src = '';
                }
            });
        }

        function nextView() {
            currentView = (currentView + 1) % views.length;
            showThumbnail(currentView);
        }

        function prevView() {
            currentView = (currentView - 1 + views.length) % views.length;
            showThumbnail(currentView);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const catSelect = document.getElementById('categorySelect');
            const subSelect = document.getElementById('subcategorySelect');

            const categoriesWithSubs = @json($parentCategories);

            function loadSubcategories() {
                const catId = catSelect.value;
                subSelect.innerHTML = '<option value="">-- None --</option>';

                if (!catId) return;

                const found = categoriesWithSubs.find(c => c.id == catId);
                if (found && found.subcategories) {
                    found.subcategories.forEach(sub => {
                        const opt = document.createElement('option');
                        opt.value = sub.id;
                        opt.textContent = sub.name;
                        if (sub.id == {{ old('subcategory_id', $model->subcategory_id ?? 'null') }}) {
                            opt.selected = true;
                        }
                        subSelect.appendChild(opt);
                    });
                }
            }

            catSelect.addEventListener('change', loadSubcategories);

            // Edit mode mein already selected category ke sub load karo
            loadSubcategories();
        });
    </script>

@endsection
