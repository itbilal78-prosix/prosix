@extends('layouts.dashboard')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp

    {{-- ════ TOAST CONTAINER ════ --}}
    <div id="toast-container"></div>

    {{-- Laravel session success → toast --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
        </script>
    @endif

    <div class="container-fluid">

        {{-- ── Page Header ── --}}
        <div class="d-flex justify-content-center align-items-center add-model flex-wrap position-relative">
            <h1 class="mb-2 text-center">All Models</h1>
            <a href="{{ route('models.create') }}" class="btn btn-dark mb-2 position-absolute end-0">
                <i class="bi bi-plus-lg"></i> Add Model
            </a>
        </div>

        <script>
            const allCategories = @json($categories->load('subcategories'));
        </script>

        {{-- ════════════════════════════════════════
             CATEGORY CARDS ROW
        ════════════════════════════════════════ --}}
        <div class="cat-cards-row mb-4" id="catCardsRow">

            <div class="cat-card active" data-target="block-all" onclick="switchCatBlock(this,'block-all')">
                <div class="cat-card-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                <div class="cat-card-name">All Models</div>
                <div class="cat-card-count">{{ $categories->sum(fn($c) => $c->models->count()) }} items</div>
            </div>

            @foreach ($categories as $cat)
                @if ($cat->models->count() === 0)
                    @continue
                @endif
                <div class="cat-card" data-target="block-cat-{{ $cat->id }}"
                    onclick="switchCatBlock(this,'block-cat-{{ $cat->id }}')">
                    @if ($cat->icon_image)
                        <div class="cat-card-thumb"><img src="{{ $cat->icon_image }}" alt="{{ $cat->name }}"></div>
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

            <div class="block-toolbar mb-3" data-block="block-all">
                <div class="d-flex align-items-center gap-2">
                    <input type="checkbox" class="block-select-all" id="sa-block-all" data-block="block-all">
                    <label for="sa-block-all" class="mb-0 fw-semibold" style="font-size:14px;">Select All</label>
                </div>
                <div class="toolbar-btns d-flex gap-2 flex-wrap"></div>
            </div>

            @forelse($categories as $category)
                @if ($category->models->count() === 0)
                    @continue
                @endif

                @foreach ($category->models->groupBy('model_name') as $modelName => $models)
                    <div class="model-group-wrapper" data-group="{{ $modelName }}">

                        <div class="model-name-divider mb-3 mt-4">
                            <span>{{ $modelName }}</span>
                        </div>

                        <div class="row mb-4 model-group-row sortable-row">

                            @foreach ($models as $model)
                                <div class="col-md-2 mb-4">
                                    <div class="card model-card shadow-sm h-100 position-relative"
                                        data-featured="{{ $model->is_featured ? '1' : '0' }}"
                                        data-apparel="{{ $model->is_apparel ? '1' : '0' }}">

                                        <div class="position-absolute top-0 start-0 p-2 cb-wrap">
                                            <input type="checkbox" class="model-checkbox" value="{{ $model->id }}"
                                                data-block="block-all">
                                        </div>

                                        @if ($model->subcategory)
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
                                                <span class="badge bg-dark mb-1 d-block">Featured</span>
                                            @endif
                                            @if ($model->is_apparel)
                                                <span class="badge bg-secondary mb-1 d-block">Apparel</span>
                                            @endif
                                            <div class="d-flex justify-content-between mb-1">
                                                <strong>{{ $model->title }}</strong>
                                                <span>${{ number_format($model->price ?? 0, 2) }}</span>
                                            </div>
                                            <p class="card-text small">
                                                {{ Str::limit($model->description, 50) }}
                                            </p>
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
                                                    class="flex-fill" onsubmit="return confirmDelete(event, this)">
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

                    </div>
                @endforeach

            @empty
                <div class="alert alert-info text-center">No Categories</div>
            @endforelse
        </div>

        {{-- ════════════════════════════════════════
             BLOCKS: PER CATEGORY
        ════════════════════════════════════════ --}}
        @foreach ($categories as $cat)
            <div class="cat-block" id="block-cat-{{ $cat->id }}" style="display:none;">

                <div class="cat-block-header mb-3">
                    <div class="d-flex align-items-center gap-3">
                        @if ($cat->icon_image)
                            <img src="{{ $cat->icon_image }}" width="48" height="48" class="rounded"
                                style="object-fit:cover;border:2px solid #000;">
                        @endif
                        <div>
                            <h4 class="fw-bold mb-0">{{ $cat->name }}</h4>
                            <small class="text-muted">{{ $cat->models->count() }} models in this category</small>
                        </div>
                    </div>
                </div>

                <div class="block-toolbar mb-3" data-block="block-cat-{{ $cat->id }}">
                    <div class="d-flex align-items-center gap-2">
                        <input type="checkbox" class="block-select-all" id="sa-block-cat-{{ $cat->id }}"
                            data-block="block-cat-{{ $cat->id }}">
                        <label for="sa-block-cat-{{ $cat->id }}" class="mb-0 fw-semibold"
                            style="font-size:14px;">Select All</label>
                    </div>
                    <div class="toolbar-btns d-flex gap-2 flex-wrap"></div>
                </div>

                @if ($cat->subcategories->count() > 0)
                    <div class="sub-tabs mb-4" id="subtabs-{{ $cat->id }}">
                        <button class="sub-tab active"
                            onclick="switchModelSubTab(this,{{ $cat->id }},'all')">All</button>
                        @foreach ($cat->subcategories as $sub)
                            <button class="sub-tab"
                                onclick="switchModelSubTab(this,{{ $cat->id }},{{ $sub->id }})">
                                {{ $sub->name }}
                                <span class="sub-tab-count">{{ $cat->models->where('subcategory_id', $sub->id)->count() }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif

                @foreach ($cat->models->groupBy('model_name') as $modelName => $models)
                    <div class="model-group-wrapper" data-group="{{ $modelName }}">

                        <div class="model-name-divider mb-3 mt-4">
                            <span>{{ $modelName }} — {{ $models->count() }} Models</span>
                        </div>

                        <div class="row mb-4 model-group-row sortable-row" data-cat="{{ $cat->id }}"
                            data-subcats="{{ $models->pluck('subcategory_id')->unique()->filter()->implode(',') ?: 'none' }}">
                            @foreach ($models as $model)
                                <div class="col-md-2 mb-4 model-col-item" data-id="{{ $model->id }}"
                                    data-cat="{{ $cat->id }}" data-sub="{{ $model->subcategory_id ?? 'none' }}">
                                    <div class="card model-card shadow-sm h-100 position-relative"
                                        data-featured="{{ $model->is_featured ? '1' : '0' }}"
                                        data-apparel="{{ $model->is_apparel ? '1' : '0' }}">

                                        <div class="position-absolute top-0 start-0 p-2 cb-wrap">
                                            <input type="checkbox" class="model-checkbox" value="{{ $model->id }}"
                                                data-block="block-cat-{{ $cat->id }}">
                                        </div>

                                        @if ($model->subcategory)
                                            <div class="position-absolute top-0 end-0 p-2" style="z-index:999;">
                                                <span class="badge bg-dark"
                                                    style="font-size:10px;">{{ $model->subcategory->name }}</span>
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
                                                <span class="badge bg-dark mb-1 d-block">Featured</span>
                                            @endif
                                            @if ($model->is_apparel)
                                                <span class="badge bg-secondary mb-1 d-block">Apparel</span>
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
                                                    class="flex-fill" onsubmit="return confirmDelete(event, this)">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-custom btn-sm w-100">Delete</button>
                                                </form>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endforeach

                @if ($cat->models->count() === 0)
                    <div class="text-center py-5 text-muted">No models in this category</div>
                @endif

            </div>
        @endforeach

    </div>

    {{-- ════ CONFIRM DELETE MODAL ════ --}}
    <div id="confirmModal" class="confirm-overlay" style="display:none;">
        <div class="confirm-box">
            <div class="confirm-icon"></div>
            <h5 class="confirm-title">Delete Model?</h5>
            <p class="confirm-msg">Are you sure you want to delete these models?</p>
            <div class="confirm-btns">
                <button id="confirmCancel" class="btn btn-outline-dark">Cancel</button>
                <button id="confirmOk" class="btn btn-dark">Delete</button>
            </div>
        </div>
    </div>

    {{-- ════ STYLES ════ --}}
    <style>
        /* ── Toast ── */
        #toast-container {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 999999;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            pointer-events: none;
        }
        .toast-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 22px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            box-shadow: 0 8px 30px rgba(0,0,0,0.22);
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
            transition: all 0.35s cubic-bezier(.4,0,.2,1);
            pointer-events: auto;
            min-width: 240px;
            max-width: 420px;
            text-align: center;
            justify-content: center;
            letter-spacing: 0.01em;
        }
        .toast-item.show {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        .toast-item.toast-success { background: linear-gradient(135deg,#000000,#313131); }
        .toast-item.toast-error   { background: linear-gradient(135deg,#000000,#292929); }
        .toast-item.toast-warning { background: linear-gradient(135deg,#000000,#d97706); }
        .toast-item .toast-icon   { font-size: 18px; }

        /* ── Confirm Modal ── */
        .confirm-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 99998;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .confirm-box {
            background: #fff;
            border-radius: 16px;
            padding: 2rem 2.5rem;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: popIn .25s ease-out;
            min-width: 300px;
        }
        @keyframes popIn {
            from { transform: scale(0.8); opacity: 0; }
            to   { transform: scale(1);   opacity: 1; }
        }
        .confirm-icon { font-size: 40px; margin-bottom: 10px; }
        .confirm-title { font-weight: 700; font-size: 18px; margin-bottom: 6px; }
        .confirm-msg { color: #555; font-size: 14px; margin-bottom: 20px; }
        .confirm-btns { display: flex; gap: 12px; justify-content: center; }
        .confirm-btns .btn { min-width: 100px; }

        /* ── Toolbar ── */
        .block-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f5f5f5;
            border: 1.5px solid #e0e0e0;
            border-radius: 0px 0px 10px 10px;
            padding: 10px 16px;
            flex-wrap: wrap;
            gap: 10px;
            position: fixed;
            top: 75px;
            left: 260px;
            right: 20px;
            z-index: 9999;
        }
        .add-model { margin-top: 5%; }

        /* ── Category Cards ── */
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
        .cat-card:hover { border-color: #000; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,.12); }
        .cat-card.active { background: #000; border-color: #000; color: #fff; }
        .cat-card-icon { font-size: 26px; margin-bottom: 6px; line-height: 1; }
        .cat-card-thumb { width: 44px; height: 44px; margin: 0 auto 6px; border-radius: 8px; overflow: hidden; border: 1px solid rgba(0,0,0,.1); }
        .cat-card-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .cat-card.active .cat-card-thumb { border-color: rgba(255,255,255,.3); }
        .cat-card-name { font-size: 12px; font-weight: 700; margin-bottom: 3px; line-height: 1.3; }
        .cat-card-count { font-size: 11px; opacity: .6; }

        /* ── Category Block Header ── */
        .cat-block-header { background: #f8f8f8; border: 1px solid #e0e0e0; border-radius: 12px; padding: 14px 18px; }

        /* ── Sub Tabs ── */
        .sub-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
        .sub-tab { background: #fff; border: 1.5px solid #ccc; border-radius: 20px; padding: 5px 16px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 6px; }
        .sub-tab:hover { border-color: #000; }
        .sub-tab.active { background: #000; border-color: #000; color: #fff; }
        .sub-tab-count { background: rgba(0,0,0,.1); border-radius: 10px; padding: 1px 7px; font-size: 11px; }
        .sub-tab.active .sub-tab-count { background: rgba(255,255,255,.25); }

        /* ── Divider ── */
        .model-name-divider { display: flex; align-items: center; }
        .model-name-divider::before, .model-name-divider::after { content: ''; flex: 1; height: 1px; background: #000; }
        .model-name-divider span { font-size: 20px; font-weight: 700; white-space: nowrap; padding: 0 12px; }
        .model-name-divider span::before { content: "( "; }
        .model-name-divider span::after  { content: " )"; }

        /* ── Model Card ── */
        .model-card { border-radius: 12px; border: 1px solid #e8e8e8; background: #fff; transition: all .35s ease; }
        .model-card:hover { transform: translateY(-6px) scale(1.02); box-shadow: 0 12px 24px rgba(0,0,0,.12) !important; }
        .card-image-wrapper { position: relative; height: 180px; overflow: hidden; display: flex; align-items: center; justify-content: center; z-index: 1; }
        .img-layer { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; }
        .black { z-index: 3; mix-blend-mode: screen; }
        .white { z-index: 2; mix-blend-mode: multiply; }
        .svg   { z-index: 1; }
        .card-text { color: #555; font-size: 13px; }
        .cb-wrap { z-index: 999; background: #fff; border-radius: 6px; }

        /* ── Buttons ── */
        .btn-custom { background: #000; color: #fff; border: 1px solid #000; border-radius: 6px; height: 36px; display: flex; align-items: center; justify-content: center; font-size: 13px; transition: .3s; }
        .btn-custom:hover { background: #333; color: #fff; }
        .card-footer .btn, .card-footer form { height: 36px; }
        .model-checkbox, .block-select-all { width: 18px; height: 18px; cursor: pointer; accent-color: #000; }
    </style>

    {{-- ════ SCRIPTS ════ --}}
    <script>

        // ════════════════════════════════════
        // TOAST HELPER
        // ════════════════════════════════════
        function showToast(message, type = 'success', duration = 3000) {
            const icons = { success: '✓', error: '✕', warning: '⚠' };
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast-item toast-${type}`;
            toast.innerHTML = `<span class="toast-icon">${icons[type] || '✓'}</span> ${message}`;
            container.appendChild(toast);
            requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.add('show')));
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 400);
            }, duration);
        }

        // ════════════════════════════════════
        // CONFIRM DELETE (single model)
        // ════════════════════════════════════
        function confirmDelete(e, form) {
            e.preventDefault();
            const modal = document.getElementById('confirmModal');
            modal.style.display = 'flex';

            document.getElementById('confirmOk').onclick = () => {
                modal.style.display = 'none';
                form.submit();
            };
            document.getElementById('confirmCancel').onclick = () => {
                modal.style.display = 'none';
            };
            return false;
        }

        document.addEventListener('DOMContentLoaded', function () {

            // ─── Session restore ───
            const BLOCK_KEY  = 'activeModelBlock';
            const SCROLL_KEY = 'modelPageScrollY';

            const savedBlock = sessionStorage.getItem(BLOCK_KEY);
            if (savedBlock) {
                const tB = document.getElementById(savedBlock);
                const tC = document.querySelector(`.cat-card[data-target="${savedBlock}"]`);
                if (tB && tC) {
                    document.querySelectorAll('.cat-block').forEach(b => b.style.display = 'none');
                    document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
                    tB.style.display = 'block';
                    tC.classList.add('active');
                }
            }
            const savedScroll = sessionStorage.getItem(SCROLL_KEY);
            if (savedScroll) {
                requestAnimationFrame(() => {
                    window.scrollTo({ top: parseInt(savedScroll), behavior: 'instant' });
                    sessionStorage.removeItem(SCROLL_KEY);
                });
            }
            window.addEventListener('beforeunload', () => sessionStorage.setItem(SCROLL_KEY, window.scrollY));

            // ─── Switch category block ───
            window.switchCatBlock = function (card, targetId) {
                document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                document.querySelectorAll('.cat-block').forEach(b => b.style.display = 'none');
                document.getElementById(targetId).style.display = 'block';
                sessionStorage.setItem(BLOCK_KEY, targetId);
                document.querySelectorAll('.model-checkbox, .block-select-all').forEach(cb => cb.checked = false);
                document.querySelectorAll('.block-toolbar').forEach(t => renderToolbar(t.dataset.block));
            };

            // ─── Subcategory tab switch ───
            window.switchModelSubTab = function (btn, catId, subId) {
                document.querySelectorAll(`#subtabs-${catId} .sub-tab`).forEach(t => t.classList.remove('active'));
                btn.classList.add('active');

                document.querySelectorAll(`.model-col-item[data-cat="${catId}"]`).forEach(col => {
                    col.style.display = (subId === 'all' || col.dataset.sub == subId) ? '' : 'none';
                });

                document.querySelectorAll(`.model-group-divider[data-cat="${catId}"],.model-group-row[data-cat="${catId}"]`).forEach(el => {
                    if (subId === 'all') { el.style.display = ''; return; }
                    const sc = el.dataset.subcats ? el.dataset.subcats.split(',') : [];
                    el.style.display = sc.includes(String(subId)) ? '' : 'none';
                });

                document.querySelectorAll(`.model-group-row[data-cat="${catId}"]`).forEach(row => {
                    if (row.style.display === 'none') return;
                    const vis = Array.from(row.querySelectorAll('.model-col-item')).filter(c => c.style.display !== 'none');
                    row.style.display = vis.length ? '' : 'none';
                });

                const blockId = `block-cat-${catId}`;
                document.querySelectorAll(`.model-checkbox[data-block="${blockId}"]`).forEach(cb => cb.checked = false);
                const saEl = document.querySelector(`.block-select-all[data-block="${blockId}"]`);
                if (saEl) saEl.checked = false;
                renderToolbar(blockId);
            };

            // ─── Render toolbar ───
            function renderToolbar(blockId) {
                const toolbar = document.querySelector(`.block-toolbar[data-block="${blockId}"] .toolbar-btns`);
                if (!toolbar) return;

                const checked = Array.from(document.querySelectorAll(`.model-checkbox[data-block="${blockId}"]:checked`))
                    .filter(cb => cb.offsetParent !== null);

                toolbar.innerHTML = '';

                if (checked.length === 0) {
                    toolbar.innerHTML = `
                        <button class="btn btn-dark btn-sm opacity-50" disabled><i class="bi bi-star-fill me-1"></i> Add to Featured</button>
                        <button class="btn btn-dark btn-sm opacity-50" disabled><i class="bi bi-bag-fill me-1"></i> Add to Apparel</button>
                        <button class="btn btn-secondary btn-sm opacity-50" disabled><i class="bi bi-copy me-1"></i> Duplicate</button>
                        <button class="btn btn-danger btn-sm opacity-50" disabled><i class="bi bi-trash-fill me-1"></i> Delete</button>`;
                    return;
                }

                const cards    = checked.map(cb => cb.closest('.model-card'));
                const allFeat  = cards.every(c => c.dataset.featured === '1');
                const noneFeat = cards.every(c => c.dataset.featured === '0');
                const allApp   = cards.every(c => c.dataset.apparel  === '1');
                const noneApp  = cards.every(c => c.dataset.apparel  === '0');
                const n = checked.length;

                if (!allFeat)  addBtn(toolbar, 'btn-dark',         `<i class="bi bi-star-fill me-1"></i> Add Featured (${n})`,    () => doAction('{{ route('models.featured') }}', 'add',    blockId));
                if (!noneFeat) addBtn(toolbar, 'btn-outline-dark',  `<i class="bi bi-star me-1"></i> Remove Featured (${n})`,      () => doAction('{{ route('models.featured') }}', 'remove', blockId));
                if (!allApp)   addBtn(toolbar, 'btn-dark',         `<i class="bi bi-bag-fill me-1"></i> Add Apparel (${n})`,       () => doAction('{{ route('models.apparel') }}',  'add',    blockId));
                if (!noneApp)  addBtn(toolbar, 'btn-outline-dark',  `<i class="bi bi-bag me-1"></i> Remove Apparel (${n})`,        () => doAction('{{ route('models.apparel') }}',  'remove', blockId));
                addBtn(toolbar, 'btn-secondary', `<i class="bi bi-copy me-1"></i> Duplicate (${n})`,         () => bulkDuplicate(blockId));
                addBtn(toolbar, 'btn-danger',    `<i class="bi bi-trash-fill me-1"></i> Delete (${n})`,      () => bulkDelete(blockId));
            }

            function addBtn(container, cls, html, fn) {
                const b = document.createElement('button');
                b.className = `btn ${cls} btn-sm`;
                b.innerHTML = html;
                b.addEventListener('click', fn);
                container.appendChild(b);
            }

            // ─── Select All ───
            document.querySelectorAll('.block-select-all').forEach(sa => {
                sa.addEventListener('change', function () {
                    const blockId = this.dataset.block;
                    document.querySelectorAll(`.model-checkbox[data-block="${blockId}"]`).forEach(cb => {
                        if (cb.offsetParent !== null) cb.checked = this.checked;
                    });
                    renderToolbar(blockId);
                });
            });

            // ─── Individual checkbox (shift-click) ───
            let lastChecked = null;
            document.querySelectorAll('.model-checkbox').forEach(cb => {
                cb.addEventListener('click', function (e) {
                    const blockId = this.dataset.block;
                    if (e.shiftKey && lastChecked && lastChecked.dataset.block === blockId) {
                        const all = Array.from(document.querySelectorAll(`.model-checkbox[data-block="${blockId}"]`)).filter(c => c.offsetParent !== null);
                        const s = all.indexOf(this), en = all.indexOf(lastChecked);
                        for (let i = Math.min(s, en); i <= Math.max(s, en); i++) all[i].checked = lastChecked.checked;
                    }
                    lastChecked = this;
                    renderToolbar(blockId);
                    const all = Array.from(document.querySelectorAll(`.model-checkbox[data-block="${blockId}"]`)).filter(c => c.offsetParent !== null);
                    const saEl = document.querySelector(`.block-select-all[data-block="${blockId}"]`);
                    if (saEl) saEl.checked = all.length > 0 && all.every(c => c.checked);
                });
            });

            // ─── doAction (featured / apparel) ───
            async function doAction(url, action, blockId) {
                const ids = Array.from(document.querySelectorAll(`.model-checkbox[data-block="${blockId}"]:checked`))
                    .filter(cb => cb.offsetParent !== null).map(cb => cb.value);
                if (!ids.length) { showToast('Koi model select nahi kiya!', 'warning'); return; }

                const res  = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ product_ids: ids, action })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1400);
                } else {
                    showToast('Error: ' + data.message, 'error');
                }
            }

            // ─── Bulk Delete ───
            async function bulkDelete(blockId) {
                const ids = Array.from(document.querySelectorAll(`.model-checkbox[data-block="${blockId}"]:checked`))
                    .filter(cb => cb.offsetParent !== null).map(cb => cb.value);
                if (!ids.length) { showToast('Koi model select nahi kiya!', 'warning'); return; }

                // Custom confirm modal
                const modal = document.getElementById('confirmModal');
                document.querySelector('.confirm-title').textContent = `${ids.length} Model(s) Delete?`;
                document.querySelector('.confirm-msg').textContent   = 'Are you sure you want to delete these models?';
                modal.style.display = 'flex';

                document.getElementById('confirmOk').onclick = async () => {
                    modal.style.display = 'none';
                    sessionStorage.setItem('activeModelBlock', blockId);
                    sessionStorage.setItem('modelPageScrollY', window.scrollY);

                    const res  = await fetch('{{ route('models.bulkDestroy') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ product_ids: ids })
                    });
                    const data = await res.json();
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => location.reload(), 1400);
                    } else {
                        showToast('Error: ' + data.message, 'error');
                    }
                };
                document.getElementById('confirmCancel').onclick = () => { modal.style.display = 'none'; };
            }

            // ─── Bulk Duplicate ───
            async function bulkDuplicate(blockId) {
                const ids = Array.from(document.querySelectorAll(`.model-checkbox[data-block="${blockId}"]:checked`))
                    .filter(cb => cb.offsetParent !== null).map(cb => cb.value);
                if (!ids.length) { showToast('Koi model select nahi kiya!', 'warning'); return; }

                // Custom confirm modal
                const modal = document.getElementById('confirmModal');
                document.querySelector('.confirm-title').textContent = `${ids.length} Model(s) Duplicate?`;
                document.querySelector('.confirm-msg').textContent   = 'Are you sure you want to duplicate models?';
                document.querySelector('.confirm-icon').textContent  = '';
                modal.style.display = 'flex';

                document.getElementById('confirmOk').onclick = async () => {
                    modal.style.display = 'none';
                    sessionStorage.setItem('activeModelBlock', blockId);
                    sessionStorage.setItem('modelPageScrollY', window.scrollY);

                    const res  = await fetch('{{ route('models.bulkDuplicate') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ product_ids: ids })
                    });
                    const data = await res.json();
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => location.reload(), 1400);
                    } else {
                        showToast('Error: ' + data.message, 'error');
                    }
                };
                document.getElementById('confirmCancel').onclick = () => {
                    modal.style.display = 'none';
                    document.querySelector('.confirm-icon').textContent = '';
                };
            }

            // ─── Init toolbars ───
            document.querySelectorAll('.block-toolbar').forEach(t => renderToolbar(t.dataset.block));

        }); // DOMContentLoaded
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".cat-block").forEach(container => {
                new Sortable(container, {
                    animation: 200,
                    draggable: ".model-group-wrapper",
                    ghostClass: "dragging",
                    onEnd: function () {
                        let order = [];
                        container.querySelectorAll(".model-group-wrapper").forEach((group, index) => {
                            order.push({ model_name: group.dataset.group, position: index + 1 });
                        });
                        fetch("{{ route('models.updateOrder') }}", {
                            method: "POST",
                            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                            body: JSON.stringify({ order: order })
                        });
                    }
                });
            });
        });
    </script>
@endsection
