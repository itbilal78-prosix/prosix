@extends('layouts.dashboard')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp

    <div class="container-fluid">
        <div class="d-flex justify-content-center align-items-center mb-4 flex-wrap position-relative">

            <h1 class="mb-2 text-center">All Models</h1>


            <a href="{{ route('models.create') }}" class="btn btn-dark mb-2 position-absolute end-0">
                <i class="bi bi-plus-lg"></i> Add Model
            </a>

        </div>

        @if (session('success'))
            <div class="alert alert-success fade show" id="successAlert">
                {{ session('success') }}
            </div>
        @endif

        {{-- Hidden category data for JS --}}
        <script>
            const allCategories = [
                @foreach ($categories as $index => $category)
                    {
                        id: 'cat-{{ $category->id }}',
                        name: '{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }} : {{ addslashes($category->name) }}'
                    },
                @endforeach
            ];
        </script>
   <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap">

                            <!-- LEFT SIDE -->
                            <div class="d-flex align-items-center gap-2">
                                <input type="checkbox" id="selectAllModels">
                                <label for="selectAllModels" class="mb-0">Select All</label>
                            </div>

                            <!-- RIGHT SIDE -->
                            <div class="d-flex gap-2 flex-wrap">
                                <button id="addFeaturedBtn" class="btn btn-dark btn-sm">
                                    <i class="bi bi-star-fill me-1"></i>
                                    Add to Featured
                                </button>

                                <button id="removeFeaturedBtn" class="btn btn-outline-dark btn-sm">
                                    Remove Featured
                                </button>

                                <button id="addApparelBtn" class="btn btn-dark btn-sm">
                                  <i class="bi bi-bag-fill me-1"></i> Add to Apparel
                                </button>

                                <button id="removeApparelBtn" class="btn btn-outline-dark btn-sm">
                                    Remove Apparel
                                </button>
                            </div>

                        </div>
        <div id="sortableModels">
            @forelse($categories as $index => $category)
                <div class="category-block {{ $index === 0 ? '' : 'hidden-block' }}" id="cat-{{ $category->id }}">

                    {{-- Category Title + Dropdown Icon --}}
                    <div class="d-flex align-items-center gap-2 mb-4 mt-2 position-relative">
                        <h4 class="fw-bold mb-0">
                            <span class="cat-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            {{ $category->name }}
                        </h4>

                        {{-- Dropdown Toggle Button --}}
                        <div class="cat-dropdown-wrap">
                            <button class="cat-dropdown-btn" onclick="toggleDropdown(this)" title="Switch Category">
                                <i class="bi bi-chevron-down arrow-icon"></i>
                            </button>

                            {{-- Dropdown List --}}
                            <div class="cat-dropdown-menu">
                                @foreach ($categories as $i => $cat)
                                    <div class="cat-dropdown-item {{ $cat->id === $category->id ? 'current' : '' }}"
                                        onclick="switchCategory('cat-{{ $category->id }}', 'cat-{{ $cat->id }}', this)">
                                        <span class="item-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        {{ $cat->name }}
                                        @if ($cat->id === $category->id)
                                            <i class="bi bi-check2 ms-auto"></i>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @foreach ($category->models->groupBy('model_name') as $modelName => $models)
                        <div class="model-name-divider mb-3 mt-4">
                            <span>{{ $modelName }} — {{ $models->count() }} Models</span>
                        </div>

                        <div class="row mb-4">
                            @foreach ($models as $model)
                                <div class="col-md-2 mb-4">
                                    <div class="card model-card shadow-sm h-100 position-relative">

                                        <!-- 🔥 CHECKBOX -->
                                        <div class="position-absolute top-0 start-0 p-2"
                                            style="z-index: 999; background: white; border-radius:6px;">
                                            <input type="checkbox" class="model-checkbox" value="{{ $model->id }}">
                                        </div>
                                        <div class="card-image-wrapper text-center">
                                            @if ($model->thumbnail)
                                                <img src="{{ asset('uploads/models/' . $model->thumbnail) }}"
                                                    style="max-width:100%;max-height:100%;object-fit:contain;">
                                            @else
                                                <img src="{{ asset('uploads/models/' . ($model->custom_front_svg ?: $model->front_svg)) }}"
                                                    class="img-layer svg">
                                                @if ($model->front_white)
                                                    <img src="{{ asset('uploads/models/' . $model->front_white) }}"
                                                        class="img-layer white">
                                                @endif
                                                @if ($model->front_black)
                                                    <img src="{{ asset('uploads/models/' . $model->front_black) }}"
                                                        class="img-layer black">
                                                @endif
                                            @endif
                                        </div>

                                        <div class="card-body p-2">

                                            {{-- ⭐ BADGES --}}
                                            @if ($model->is_featured)
                                                <span class="badge bg-dark mb-1 d-block">⭐ Featured</span>
                                            @endif

                                            @if ($model->is_apparel)
                                                <span class="badge bg-secondary mb-1 d-block">👕 Apparel</span>
                                            @endif

                                            <div class="d-flex justify-content-between mb-1">
                                                <strong>{{ $model->title }}</strong>
                                                <span>${{ number_format($model->price ?? 0, 2) }}</span>
                                            </div>
                                            <p class="card-text small">{{ Str::limit($model->description, 50) }}</p>
                                        </div>

                                        <div class="card-footer p-2">
                                            <div class="d-flex gap-2 mb-2">
                                                <a href="{{ route('models.show', $model->id) }}"
                                                    class="btn btn-custom btn-sm flex-fill">Customize</a>
                                                <a href="{{ route('models.edit', $model->id) }}"
                                                    class="btn btn-custom btn-sm flex-fill">Edit</a>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('models.duplicate', $model->id) }}" method="POST"
                                                    class="flex-fill">
                                                    @csrf
                                                    <button class="btn btn-custom btn-sm w-100">Duplicate</button>
                                                </form>
                                                <form action="{{ route('models.destroy', $model->id) }}" method="POST"
                                                    class="flex-fill">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-custom btn-sm w-100">Delete</button>
                                                </form>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

            @empty
                <div class="alert alert-info text-center">No Categories</div>
            @endforelse
        </div>
    </div>

    <style>
        .hidden-block {
            display: none;
        }

        /* Category number badge */
        .cat-num {
            background: #000;
            color: #fff;
            border-radius: 6px;
            padding: 2px 10px;
            font-size: 13px;
            margin-right: 4px;
        }

        /* Dropdown wrapper */
        .cat-dropdown-wrap {
            position: relative;
            display: inline-block;
        }

        /* Dropdown trigger button */
        .cat-dropdown-btn {
            background: #000;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 2px 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: .2s;
        }

        .cat-dropdown-btn:hover {
            background: #333;
        }

        .cat-dropdown-btn .arrow-icon {
            font-size: 11px;
            transition: transform .25s;
        }

        .cat-dropdown-btn.open .arrow-icon {
            transform: rotate(180deg);
        }

        /* Dropdown menu */
        .cat-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
            min-width: 220px;
            z-index: 999;
            overflow: hidden;
            animation: dropFade .2s ease;
        }

        .cat-dropdown-menu.show {
            display: block;
        }

        @keyframes dropFade {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Dropdown item */
        .cat-dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: background .15s;
            border-bottom: 1px solid #f5f5f5;
        }

        .cat-dropdown-item:last-child {
            border-bottom: none;
        }

        .cat-dropdown-item:hover {
            background: #f5f5f5;
        }

        .cat-dropdown-item.current {
            background: #000;
            color: #fff;
        }

        .cat-dropdown-item.current:hover {
            background: #222;
        }

        .item-num {
            background: rgba(0, 0, 0, .08);
            border-radius: 4px;
            padding: 1px 6px;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .current .item-num {
            background: rgba(255, 255, 255, .2);
        }

        /* Model name divider */
        .model-name-divider {
            display: flex;
            align-items: center;
        }

        .model-name-divider::before,
        .model-name-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #000000;
        }

        .model-name-divider span::before {
            content: "( ";
            color: #000000;
            font-weight: 400;
            letter-spacing: 2px;
        }

        .model-name-divider span::after {
            content: " )";
            color: #000000;
            font-weight: 400;
            letter-spacing: 2px;
        }

        .model-name-divider span {
            font-size: 20px;
            font-weight: 700;
            white-space: nowrap;
        }

        /* Model cards */
        .model-card {
            border-radius: 12px;
            border: 1px solid #e8e8e8;
            background: #fff;
            transition: all .35s ease;
        }

        .model-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 12px 24px rgba(0, 0, 0, .12) !important;
        }

        .card-image-wrapper {
            position: relative;
            height: 180px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .img-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .black {
            z-index: 3;
            mix-blend-mode: screen;
        }

        .white {
            z-index: 2;
            mix-blend-mode: multiply;
        }

        .svg {
            z-index: 1;
        }

        .card-text {
            color: #555;
            font-size: 13px;
        }

        .btn-custom {
            background: #000;
            color: #fff;
            border: 1px solid #000;
            border-radius: 6px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            transition: .3s;
        }

        .btn-custom:hover {
            background: #333;
            color: #fff;
        }

        .card-footer .btn,
        .card-footer form {
            height: 36px;
        }

        .model-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #000;
        }

        .card-image-wrapper {
            position: relative;
            z-index: 1;
        }
    </style>

    <script>
        // Toggle dropdown open/close
        function toggleDropdown(btn) {
            const menu = btn.nextElementSibling;
            const isOpen = menu.classList.contains('show');

            // Close all open dropdowns first
            document.querySelectorAll('.cat-dropdown-menu.show').forEach(m => m.classList.remove('show'));
            document.querySelectorAll('.cat-dropdown-btn.open').forEach(b => b.classList.remove('open'));

            if (!isOpen) {
                menu.classList.add('show');
                btn.classList.add('open');
            }
        }

        // Switch to selected category
        function switchCategory(currentId, targetId, clickedItem) {
            // Hide current
            document.getElementById(currentId).classList.add('hidden-block');
            // Show target
            document.getElementById(targetId).classList.remove('hidden-block');
            // Close all dropdowns
            document.querySelectorAll('.cat-dropdown-menu.show').forEach(m => m.classList.remove('show'));
            document.querySelectorAll('.cat-dropdown-btn.open').forEach(b => b.classList.remove('open'));
            // Scroll top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.cat-dropdown-wrap')) {
                document.querySelectorAll('.cat-dropdown-menu.show').forEach(m => m.classList.remove('show'));
                document.querySelectorAll('.cat-dropdown-btn.open').forEach(b => b.classList.remove('open'));
            }
        });

        // Auto hide alert
        setTimeout(() => {
            const alert = document.getElementById('successAlert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 500);
            }
        }, 2000);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 🔥 SELECT ALL FUNCTION
            document.getElementById('selectAllModels').addEventListener('change', function() {

                let checkboxes = document.querySelectorAll('.model-checkbox');

                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = document.getElementById('selectAllModels').checked;
                });

            });

            function getSelectedIds() {
                return Array.from(document.querySelectorAll('.model-checkbox:checked'))
                    .map(cb => cb.value);
            }

            async function postAction(url, body) {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(body)
                });
                return res.json();
            }

            async function handle(url, action) {
                const ids = getSelectedIds();

                if (!ids.length) {
                    alert('Pehle koi model select karo!');
                    return;
                }

                const data = await postAction(url, {
                    product_ids: ids,
                    action: action
                });

                if (data.success) {
                    alert(data.message);
                    location.reload();
                }
            }

            document.getElementById('addFeaturedBtn')
                ?.addEventListener('click', () => handle('{{ route('models.featured') }}', 'add'));

            document.getElementById('removeFeaturedBtn')
                ?.addEventListener('click', () => handle('{{ route('models.featured') }}', 'remove'));

            document.getElementById('addApparelBtn')
                ?.addEventListener('click', () => handle('{{ route('models.apparel') }}', 'add'));

            document.getElementById('removeApparelBtn')
                ?.addEventListener('click', () => handle('{{ route('models.apparel') }}', 'remove'));

        });
    </script>
@endsection
