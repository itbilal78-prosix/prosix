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

        {{-- Hidden category+subcategory data for JS --}}
        <script>
            const allCategories = @json($categories->load('subcategories'));
        </script>

        {{-- ─── Action Buttons ─── --}}
        <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <input type="checkbox" id="selectAllModels">
                <label for="selectAllModels" class="mb-0">Select All</label>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button id="addFeaturedBtn" class="btn btn-dark btn-sm">
                    <i class="bi bi-star-fill me-1"></i> Add to Featured
                </button>
                <button id="removeFeaturedBtn" class="btn btn-outline-dark btn-sm">
                    <i class="bi bi-star me-1"></i> Remove Featured
                </button>
                <button id="addApparelBtn" class="btn btn-dark btn-sm">
                    <i class="bi bi-bag-fill me-1"></i> Add to Apparel
                </button>
                <button id="removeApparelBtn" class="btn btn-outline-dark btn-sm">
                    <i class="bi bi-bag me-1"></i> Remove Apparel
                </button>
            </div>
        </div>

        {{-- ════════════════════════════════════════
             CATEGORY CARDS ROW  (same style as products)
        ════════════════════════════════════════ --}}
        <div class="cat-cards-row mb-4" id="catCardsRow">

            {{-- All Models card --}}
            <div class="cat-card active" data-target="block-all"
                 onclick="switchCatBlock(this, 'block-all')">
                <div class="cat-card-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                <div class="cat-card-name">All Models</div>
                <div class="cat-card-count">{{ $categories->sum(fn($c) => $c->models->count()) }} items</div>
            </div>

            @foreach($categories as $cat)
            @if($cat->models->count() === 0) @continue @endif
            <div class="cat-card" data-target="block-cat-{{ $cat->id }}"
                 onclick="switchCatBlock(this, 'block-cat-{{ $cat->id }}')">
                @if($cat->icon_image)
                    <div class="cat-card-thumb">
                        <img src="{{ $cat->icon_image }}" alt="{{ $cat->name }}">
                    </div>
                @else
                    <div class="cat-card-icon"><i class="bi bi-tag-fill"></i></div>
                @endif
                <div class="cat-card-name">{{ $cat->name }}</div>
                <div class="cat-card-count">{{ $cat->models->count() }} items</div>
            </div>
            @endforeach

        </div>

        {{-- ════════════════════════════════════════
             BLOCK: ALL MODELS
        ════════════════════════════════════════ --}}
        <div class="cat-block" id="block-all">
            @forelse($categories as $category)
                @if($category->models->count() === 0) @continue @endif

                @foreach ($category->models->groupBy('model_name') as $modelName => $models)
                    <div class="model-name-divider mb-3 mt-4">
                        <span>{{ $modelName }}</span>
                    </div>
                    <div class="row mb-4">
                        @foreach ($models as $model)
                        <div class="col-md-2 mb-4">
                            <div class="card model-card shadow-sm h-100 position-relative">
                                <div class="position-absolute top-0 start-0 p-2" style="z-index:999;background:#fff;border-radius:6px;">
                                    <input type="checkbox" class="model-checkbox" value="{{ $model->id }}">
                                </div>
                                @if($model->subcategory)
                                <div class="position-absolute top-0 end-0 p-2" style="z-index:999;">
                                    <span class="badge bg-dark" style="font-size:10px;">{{ $model->subcategory->name }}</span>
                                </div>
                                @endif
                                <div class="card-image-wrapper text-center">
                                    @if ($model->thumbnail)
                                        <img src="{{ asset('uploads/models/' . $model->thumbnail) }}" style="max-width:100%;max-height:100%;object-fit:contain;">
                                    @else
                                        <img src="{{ asset('uploads/models/' . ($model->custom_front_svg ?: $model->front_svg)) }}" class="img-layer svg">
                                        @if ($model->front_white)
                                            <img src="{{ asset('uploads/models/' . $model->front_white) }}" class="img-layer white">
                                        @endif
                                        @if ($model->front_black)
                                            <img src="{{ asset('uploads/models/' . $model->front_black) }}" class="img-layer black">
                                        @endif
                                    @endif
                                </div>
                                <div class="card-body p-2">
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
                                        <a href="{{ route('models.show', $model->id) }}" class="btn btn-custom btn-sm flex-fill">Customize</a>
                                        <a href="{{ route('models.edit', $model->id) }}" class="btn btn-custom btn-sm flex-fill">Edit</a>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('models.duplicate', $model->id) }}" method="POST" class="flex-fill">
                                            @csrf
                                            <button class="btn btn-custom btn-sm w-100">Duplicate</button>
                                        </form>
                                        <form action="{{ route('models.destroy', $model->id) }}" method="POST" class="flex-fill">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-custom btn-sm w-100" onclick="return confirm('Delete?')">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endforeach

            @empty
                <div class="alert alert-info text-center">No Categories</div>
            @endforelse
        </div>

        {{-- ════════════════════════════════════════
             BLOCKS: PER CATEGORY  (with subcategory tabs)
        ════════════════════════════════════════ --}}
        @foreach($categories as $cat)
        <div class="cat-block" id="block-cat-{{ $cat->id }}" style="display:none;">

            {{-- Category Header --}}
            <div class="cat-block-header mb-3">
                <div class="d-flex align-items-center gap-3">
                    @if($cat->icon_image)
                        <img src="{{ $cat->icon_image }}" width="48" height="48"
                             class="rounded" style="object-fit:cover;border:2px solid #000;">
                    @endif
                    <div>
                        <h4 class="fw-bold mb-0">{{ $cat->name }}</h4>
                        <small class="text-muted">{{ $cat->models->count() }} models in this category</small>
                    </div>
                </div>
            </div>

            {{-- Subcategory Tabs --}}
            @if($cat->subcategories->count() > 0)
            <div class="sub-tabs mb-4" id="subtabs-{{ $cat->id }}">
                <button class="sub-tab active"
                        onclick="switchModelSubTab(this, {{ $cat->id }}, 'all')">
                    All
                </button>
                @foreach($cat->subcategories as $sub)
                <button class="sub-tab"
                        onclick="switchModelSubTab(this, {{ $cat->id }}, {{ $sub->id }})">
                    {{ $sub->name }}
                    <span class="sub-tab-count">
                        {{ $cat->models->where('subcategory_id', $sub->id)->count() }}
                    </span>
                </button>
                @endforeach
            </div>
            @endif

            {{-- Models Grid per Category, grouped by model_name --}}
            @foreach ($cat->models->groupBy('model_name') as $modelName => $models)
                <div class="model-name-divider mb-3 mt-4 model-group-divider"
                     data-cat="{{ $cat->id }}"
                     data-subcats="{{ $models->pluck('subcategory_id')->unique()->filter()->implode(',') ?: 'none' }}">
                    <span>{{ $modelName }} — {{ $models->count() }} Models</span>
                </div>

                <div class="row mb-4 model-group-row"
                     data-cat="{{ $cat->id }}"
                     data-subcats="{{ $models->pluck('subcategory_id')->unique()->filter()->implode(',') ?: 'none' }}">
                    @foreach ($models as $model)
                        <div class="col-md-2 mb-4 model-col-item"
                             data-cat="{{ $cat->id }}"
                             data-sub="{{ $model->subcategory_id ?? 'none' }}">
                            <div class="card model-card shadow-sm h-100 position-relative">

                                {{-- Checkbox --}}
                                <div class="position-absolute top-0 start-0 p-2"
                                     style="z-index:999;background:#fff;border-radius:6px;">
                                    <input type="checkbox" class="model-checkbox" value="{{ $model->id }}">
                                </div>

                                {{-- Subcategory badge (top-right) --}}
                                @if($model->subcategory)
                                <div class="position-absolute top-0 end-0 p-2" style="z-index:999;">
                                    <span class="badge bg-dark" style="font-size:10px;">
                                        {{ $model->subcategory->name }}
                                    </span>
                                </div>
                                @endif

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
                                        <form action="{{ route('models.duplicate', $model->id) }}"
                                              method="POST" class="flex-fill">
                                            @csrf
                                            <button class="btn btn-custom btn-sm w-100">Duplicate</button>
                                        </form>
                                        <form action="{{ route('models.destroy', $model->id) }}"
                                              method="POST" class="flex-fill">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-custom btn-sm w-100"
                                                    onclick="return confirm('Delete this model?')">Delete</button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            {{-- Empty state --}}
            @if($cat->models->count() === 0)
                <div class="text-center py-5 text-muted">No models in this category</div>
            @endif

        </div>
        @endforeach

    </div>{{-- /container --}}

    {{-- ════ STYLES ════ --}}
    <style>
        /* ── Category Cards Row ── */
        .cat-cards-row {
            display: flex;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 8px;
            scrollbar-width: thin;
        }

        .cat-card {
            flex: 0 0 130px;
            background: #fff;
            border: 2px solid #e0e0e0;
            border-radius: 14px;
            padding: 14px 10px;
            text-align: center;
            cursor: pointer;
            transition: all .25s ease;
            user-select: none;
        }

        .cat-card:hover {
            border-color: #000;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,.12);
        }

        .cat-card.active {
            background: #000;
            border-color: #000;
            color: #fff;
        }

        .cat-card-icon { font-size: 26px; margin-bottom: 6px; line-height: 1; }

        .cat-card-thumb {
            width: 44px; height: 44px;
            margin: 0 auto 6px;
            border-radius: 8px; overflow: hidden;
            border: 1px solid rgba(0,0,0,.1);
        }
        .cat-card-thumb img { width:100%; height:100%; object-fit:cover; }
        .cat-card.active .cat-card-thumb { border-color: rgba(255,255,255,.3); }

        .cat-card-name  { font-size:12px; font-weight:700; margin-bottom:3px; line-height:1.3; }
        .cat-card-count { font-size:11px; opacity:.6; }

        /* ── Category Block Header ── */
        .cat-block-header {
            background: #f8f8f8;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 14px 18px;
        }

        /* ── Subcategory Tabs ── */
        .sub-tabs { display:flex; gap:8px; flex-wrap:wrap; }

        .sub-tab {
            background: #fff;
            border: 1.5px solid #ccc;
            border-radius: 20px;
            padding: 5px 16px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .sub-tab:hover   { border-color: #000; }
        .sub-tab.active  { background:#000; border-color:#000; color:#fff; }

        .sub-tab-count {
            background: rgba(0,0,0,.1);
            border-radius: 10px;
            padding: 1px 7px;
            font-size: 11px;
        }
        .sub-tab.active .sub-tab-count { background: rgba(255,255,255,.25); }

        /* ── Model name divider ── */
        .model-name-divider {
            display:flex; align-items:center;
        }
        .model-name-divider::before,
        .model-name-divider::after {
            content:''; flex:1; height:1px; background:#000;
        }
        .model-name-divider span {
            font-size:20px; font-weight:700; white-space:nowrap;
            padding: 0 12px;
        }
        .model-name-divider span::before { content:"( "; }
        .model-name-divider span::after  { content:" )"; }

        /* Sub-variant: smaller for sub-groups */
        .model-name-divider--sub span { font-size:15px; }
        .model-name-divider--sub::before,
        .model-name-divider--sub::after { background:#ccc; }

        /* ── Model cards ── */
        .model-card {
            border-radius: 12px;
            border: 1px solid #e8e8e8;
            background: #fff;
            transition: all .35s ease;
        }
        .model-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 12px 24px rgba(0,0,0,.12) !important;
        }

        .card-image-wrapper {
            position:relative; height:180px; overflow:hidden;
            display:flex; align-items:center; justify-content:center; z-index:1;
        }
        .img-layer { position:absolute; top:0; left:0; width:100%; height:100%; object-fit:contain; }
        .black { z-index:3; mix-blend-mode:screen; }
        .white { z-index:2; mix-blend-mode:multiply; }
        .svg   { z-index:1; }

        .card-text { color:#555; font-size:13px; }

        .btn-custom {
            background:#000; color:#fff;
            border:1px solid #000; border-radius:6px;
            height:36px; display:flex;
            align-items:center; justify-content:center;
            font-size:13px; transition:.3s;
        }
        .btn-custom:hover { background:#333; color:#fff; }
        .card-footer .btn, .card-footer form { height:36px; }

        .model-checkbox { width:18px; height:18px; cursor:pointer; accent-color:#000; }

        /* Empty subcategory state */
        .sub-empty {
            display: none;
            text-align: center;
            padding: 40px;
            color: #aaa;
            font-size: 15px;
        }
    </style>

    {{-- ════ SCRIPTS ════ --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Category Card Switch ──
        window.switchCatBlock = function(card, targetId) {
            document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            document.querySelectorAll('.cat-block').forEach(b => b.style.display = 'none');
            document.getElementById(targetId).style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };

        // ── Subcategory Tab Switch for Models ──
        window.switchModelSubTab = function(btn, catId, subId) {
            // Update tab active state
            document.querySelectorAll(`#subtabs-${catId} .sub-tab`)
                    .forEach(t => t.classList.remove('active'));
            btn.classList.add('active');

            // Show/hide individual model card columns
            document.querySelectorAll(`.model-col-item[data-cat="${catId}"]`)
                    .forEach(col => {
                        if (subId === 'all') {
                            col.style.display = '';
                        } else {
                            col.style.display = (col.dataset.sub == subId) ? '' : 'none';
                        }
                    });

            // Show/hide group dividers & rows
            document.querySelectorAll(`.model-group-divider[data-cat="${catId}"],
                                       .model-group-row[data-cat="${catId}"]`)
                    .forEach(el => {
                        if (subId === 'all') {
                            el.style.display = '';
                        } else {
                            // Check if this group has any models for the selected subcategory
                            const subcats = el.dataset.subcats ? el.dataset.subcats.split(',') : [];
                            el.style.display = subcats.includes(String(subId)) ? '' : 'none';
                        }
                    });

            // After filtering rows, check if any visible cols exist in each row
            document.querySelectorAll(`.model-group-row[data-cat="${catId}"]`)
                    .forEach(row => {
                        if (row.style.display === 'none') return;
                        const visibleCols = Array.from(
                            row.querySelectorAll('.model-col-item')
                        ).filter(c => c.style.display !== 'none');
                        row.style.display = visibleCols.length ? '' : 'none';
                        // hide matching divider too if row hidden
                    });
        };

        // ── Select All ──
        document.getElementById('selectAllModels')?.addEventListener('change', function () {
            document.querySelectorAll('.model-checkbox')
                    .forEach(cb => cb.checked = this.checked);
        });

        // ── Helper: get selected IDs ──
        function getSelectedIds() {
            return Array.from(document.querySelectorAll('.model-checkbox:checked'))
                        .map(cb => cb.value);
        }

        // ── Helper: POST ──
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
            if (!ids.length) { alert('Pehle koi model select karo!'); return; }
            const data = await postAction(url, { product_ids: ids, action });
            if (data.success) { alert(data.message); location.reload(); }
            else alert('Error: ' + data.message);
        }

        document.getElementById('addFeaturedBtn')
            ?.addEventListener('click', () => handle('{{ route("models.featured") }}', 'add'));
        document.getElementById('removeFeaturedBtn')
            ?.addEventListener('click', () => handle('{{ route("models.featured") }}', 'remove'));
        document.getElementById('addApparelBtn')
            ?.addEventListener('click', () => handle('{{ route("models.apparel") }}', 'add'));
        document.getElementById('removeApparelBtn')
            ?.addEventListener('click', () => handle('{{ route("models.apparel") }}', 'remove'));

        // ── Auto-hide success alert ──
        setTimeout(() => {
            const alert = document.getElementById('successAlert');
            if (alert) { alert.classList.remove('show'); setTimeout(() => alert.remove(), 500); }
        }, 2000);

    });
    </script>
@endsection
