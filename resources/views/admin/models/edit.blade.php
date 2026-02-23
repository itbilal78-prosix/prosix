@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">

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

        <form action="{{ route('models.update', $model->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card shadow-sm p-3">

                <!-- BASIC INFO -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Model Name *</label>
                        <input type="text" name="model_name" class="form-control form-control-sm"
                            value="{{ old('model_name', $model->model_name) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Title (Display Name)</label>
                        <input type="text" name="title" class="form-control form-control-sm"
                            value="{{ old('title', $model->title) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Price ($)</label>
                        <input type="number" name="price" class="form-control form-control-sm" step="0.01"
                            value="{{ old('price', $model->price) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control form-control-sm"
                            value="{{ old('description', $model->description) }}">
                    </div>
                </div>
                <!-- NAVIGATION, CATEGORY, SUBCATEGORY -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Navigation (optional)</label>
                        <select name="navigation_id" class="form-control form-control-sm">
                            <option value="">-- None --</option>
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
                            <option value="">-- None --</option>
                            @foreach ($parentCategories as $cat)
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
                            <option value="">-- Select category first or leave empty --</option>
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
                                    @php
                                        $field = $view . '_' . $color;
                                        $imagePath = $model->$field ? asset('uploads/models/' . $model->$field) : null;
                                    @endphp
                                    <div class="image-box">
                                        <label class="small text-capitalize">{{ $view }}
                                            {{ $color }}</label>

                                        <input type="file" name="{{ $field }}" class="d-none"
                                            accept="{{ $color === 'svg' ? 'image/svg+xml' : 'image/png' }}"
                                            onchange="fieldPreview(this,'{{ $view }}','{{ $color }}')"
                                            id="{{ $field }}_input">

                                        <div class="img-preview {{ $imagePath ? '' : 'empty-preview' }}"
                                            onclick="document.getElementById('{{ $field }}_input').click()">
                                            @if ($imagePath)
                                                <img src="{{ $imagePath }}" alt="{{ $field }}">
                                                <div class="close-btn"
                                                    onclick="removeImage('{{ $view }}','{{ $color }}')">×
                                                </div>
                                            @else
                                                <span>Click</span>
                                            @endif
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
                                    @php
                                        $frontFile = $model->{'front_' . $color} ?? '';
                                    @endphp
                                    <img id="p_{{ $color }}" class="layer-img"
                                        src="{{ $frontFile ? asset('uploads/models/' . $frontFile) : '' }}">
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
                                <i class="bi bi-save"></i> Update Model
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
        .image-box {
            width: calc(33.33% - 0.5rem);
            margin-bottom: 0.5rem;
        }

        .img-preview {
            width: 100%;
            height: 60px;
            object-fit: contain;
            border: 1px dotted #000;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8f9fa;
            font-size: 10px;
            color: #000;
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

        /* Save/Cancel buttons */
        .btn-save-mode {
            background: #000;
            color: #fff;
            border: 1px solid #000;
        }

        [data-bs-theme="light"] .btn-save-mode {
            background: #fff;
            color: #000;
            border: 1px solid #000;
        }

        .btn-cancel-mode {
            background: transparent;
            color: #000;
            border: 1px solid #000;
        }

        [data-bs-theme="dark"] .btn-cancel-mode {
            color: #fff;
            border: 1px solid #fff;
        }

        .btn-outline-secondary {
            border-color: #000;
            color: #000;
        }

        [data-bs-theme="dark"] .btn-outline-secondary {
            border-color: #fff;
            color: #fff;
        }
    </style>

    {{-- SCRIPT --}}
    <script>
        let views = ['front', 'back', 'left', 'right'];
        let currentView = 0;

        // Store existing images for JS
        const existingImages = {
            front: {
                black: "{{ $model->front_black ? asset('uploads/models/' . $model->front_black) : '' }}",
                white: "{{ $model->front_white ? asset('uploads/models/' . $model->front_white) : '' }}",
                svg: "{{ $model->front_svg ? asset('uploads/models/' . $model->front_svg) : '' }}"
            },
            back: {
                black: "{{ $model->back_black ? asset('uploads/models/' . $model->back_black) : '' }}",
                white: "{{ $model->back_white ? asset('uploads/models/' . $model->back_white) : '' }}",
                svg: "{{ $model->back_svg ? asset('uploads/models/' . $model->back_svg) : '' }}"
            },
            left: {
                black: "{{ $model->left_black ? asset('uploads/models/' . $model->left_black) : '' }}",
                white: "{{ $model->left_white ? asset('uploads/models/' . $model->left_white) : '' }}",
                svg: "{{ $model->left_svg ? asset('uploads/models/' . $model->left_svg) : '' }}"
            },
            right: {
                black: "{{ $model->right_black ? asset('uploads/models/' . $model->right_black) : '' }}",
                white: "{{ $model->right_white ? asset('uploads/models/' . $model->right_white) : '' }}",
                svg: "{{ $model->right_svg ? asset('uploads/models/' . $model->right_svg) : '' }}"
            }
        };

        function fieldPreview(input, view, type) {
            const container = input.nextElementSibling;
            if (!input.files.length) {
                container.innerHTML = '<span>Click</span>';
                document.getElementById('p_' + type).src = existingImages['front'][type] || '';
                return;
            }

            const reader = new FileReader();
            reader.onload = e => {
                container.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:contain;">
            <div class="close-btn" onclick="removeImage('${view}','${type}')">×</div>`;

                if (view === 'front') {
                    document.getElementById('p_' + type).src = e.target.result;
                }
            };
            reader.readAsDataURL(input.files[0]);
        }

        function removeImage(view, type) {
            const input = document.getElementById(view + '_' + type + '_input');
            input.value = '';
            const container = input.nextElementSibling;
            container.innerHTML = '<span>Click</span>';

            // Reset 3D thumbnail if front
            if (view === 'front') {
                document.getElementById('p_' + type).src = existingImages['front'][type] || '';
            }
        }

        function showThumbnail(viewIndex) {
            const view = views[viewIndex];
            ['black', 'white', 'svg'].forEach(color => {
                const input = document.getElementById(view + '_' + color + '_input');
                const img = document.getElementById('p_' + color);
                if (input && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => img.src = e.target.result;
                    reader.readAsDataURL(input.files[0]);
                } else {
                    img.src = existingImages[view][color] || '';
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

        // Subcategory loader placeholder
        function loadSubcategories() {
            let categoryId = document.getElementById('categorySelect').value;
            let subSelect = document.getElementById('subcategorySelect');
            subSelect.innerHTML = '<option value="">Select Subcategory</option>';
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
