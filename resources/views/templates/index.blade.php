@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    {{-- Toast Container --}}
    <div id="toast-container"></div>
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
        </script>
    @endif

    {{-- Page Header --}}
    <div class="d-flex justify-content-center align-items-center mb-4 position-relative" style="margin-top:5%">
        <h1 class="mb-0 text-center">All Templates</h1>
        <a href="{{ route('mascots.create') }}" class="btn btn-dark position-absolute end-0">
            <i class="bi bi-plus-lg"></i> Create Template
        </a>
    </div>

    {{-- Category Cards Row --}}
    <div class="cat-cards-row mb-4">

        {{-- ALL card --}}
        <div class="cat-card active" data-target="block-all" onclick="switchBlock(this,'block-all')">
            <div class="cat-card-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
            <div class="cat-card-name">All Templates</div>
            <div class="cat-card-count">
                {{ $categories->sum(fn($c) => $c->templates->count()) + $uncategorized->count() }} items
            </div>
        </div>

        @foreach($categories as $cat)
            @if($cat->templates->count() === 0) @continue @endif
            <div class="cat-card" data-target="block-cat-{{ $cat->id }}"
                 onclick="switchBlock(this,'block-cat-{{ $cat->id }}')">
                @if($cat->icon_image)
                    <div class="cat-card-thumb">
                        <img src="{{ $cat->icon_image }}" alt="{{ $cat->name }}">
                    </div>
                @else
                    <div class="cat-card-icon"><i class="bi bi-tag-fill"></i></div>
                @endif
                <div class="cat-card-name">{{ $cat->name }}</div>
                <div class="cat-card-count">{{ $cat->templates->count() }} items</div>
            </div>
        @endforeach

        @if($uncategorized->count() > 0)
            <div class="cat-card" data-target="block-uncategorized"
                 onclick="switchBlock(this,'block-uncategorized')">
                <div class="cat-card-icon"><i class="bi bi-question-circle-fill"></i></div>
                <div class="cat-card-name">Uncategorized</div>
                <div class="cat-card-count">{{ $uncategorized->count() }} items</div>
            </div>
        @endif

    </div>

    {{-- ===== FIXED TOP TOOLBAR ===== --}}
    <div class="tpl-block-toolbar" id="tplToolbar">
        <div class="d-flex align-items-center gap-2">
            <input type="checkbox" id="tplSelectAll" class="tpl-sa-checkbox">
            <label for="tplSelectAll" class="mb-0 fw-semibold" style="font-size:14px;">Select All</label>
        </div>
        <div class="tpl-toolbar-btns" id="tplToolbarBtns">
            <button class="btn btn-danger btn-sm opacity-50" disabled id="tplDeleteBtn">
                <i class="bi bi-trash-fill me-1"></i> Delete
            </button>
        </div>
    </div>

    {{-- BLOCK: ALL --}}
    <div class="cat-block" id="block-all">

        @foreach($categories as $cat)
            @if($cat->templates->count() === 0) @continue @endif
            <div class="model-name-divider mb-3 mt-4"><span>{{ $cat->name }}</span></div>
            <div class="row g-3 mb-4">
                @foreach($cat->templates as $template)
                    @include('templates._card', ['template' => $template])
                @endforeach
            </div>
        @endforeach

        @if($uncategorized->count() > 0)
            <div class="model-name-divider mb-3 mt-4"><span>Uncategorized</span></div>
            <div class="row g-3 mb-4">
                @foreach($uncategorized as $template)
                    @include('templates._card', ['template' => $template])
                @endforeach
            </div>
        @endif

        @if($categories->every(fn($c) => $c->templates->count() === 0) && $uncategorized->isEmpty())
            <div class="alert alert-info text-center">No Templates Found</div>
        @endif

    </div>

    {{-- BLOCKS: Per Category --}}
    @foreach($categories as $cat)
        <div class="cat-block" id="block-cat-{{ $cat->id }}" style="display:none;">

            <div class="cat-block-header mb-3">
                <div class="d-flex align-items-center gap-3">
                    @if($cat->icon_image)
                        <img src="{{ $cat->icon_image }}" width="48" height="48"
                             class="rounded" style="object-fit:cover;border:2px solid #000;">
                    @endif
                    <div>
                        <h4 class="fw-bold mb-0">{{ $cat->name }}</h4>
                        <small class="text-muted">{{ $cat->templates->count() }} templates</small>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                @forelse($cat->templates as $template)
                    @include('templates._card', ['template' => $template])
                @empty
                    <div class="text-center py-5 text-muted">No templates in this category</div>
                @endforelse
            </div>

        </div>
    @endforeach

    {{-- BLOCK: Uncategorized --}}
    @if($uncategorized->count() > 0)
        <div class="cat-block" id="block-uncategorized" style="display:none;">
            <div class="mb-3"><h4 class="fw-bold">Uncategorized</h4></div>
            <div class="row g-3">
                @foreach($uncategorized as $template)
                    @include('templates._card', ['template' => $template])
                @endforeach
            </div>
        </div>
    @endif

</div>

{{-- Delete Confirm Modal --}}
<div id="confirmModal" class="confirm-overlay" style="display:none;">
    <div class="confirm-box">
        <h5 class="confirm-title">Delete Template?</h5>
        <p class="confirm-msg">Are you sure you want to delete selected templates?</p>
        <div class="confirm-btns">
            <button id="confirmCancel" class="btn btn-outline-dark">Cancel</button>
            <button id="confirmOk" class="btn btn-dark">Delete</button>
        </div>
    </div>
</div>

<style>
    /* Toast */
    #toast-container {
        position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
        z-index: 999999; display: flex; flex-direction: column;
        align-items: center; gap: 10px; pointer-events: none;
    }
    .toast-item {
        display: flex; align-items: center; gap: 10px;
        padding: 13px 22px; border-radius: 12px; font-size: 14px;
        font-weight: 600; color: #fff; box-shadow: 0 8px 30px rgba(0,0,0,.22);
        opacity: 0; transform: translateY(-20px) scale(0.95);
        transition: all 0.35s cubic-bezier(.4,0,.2,1);
        pointer-events: auto; min-width: 240px; max-width: 420px;
        text-align: center; justify-content: center;
    }
    .toast-item.show { opacity:1; transform:translateY(0) scale(1); }
    .toast-item.toast-success { background: linear-gradient(135deg,#000,#313131); }
    .toast-item.toast-error   { background: linear-gradient(135deg,#c0392b,#e74c3c); }
    .toast-item.toast-warning { background: linear-gradient(135deg,#000,#d97706); }

    /* Fixed Toolbar */
    .tpl-block-toolbar {
        display: flex; align-items: center; justify-content: space-between;
        background: #f5f5f5; border: 1.5px solid #e0e0e0;
        border-radius: 0 0 10px 10px; padding: 10px 16px;
        flex-wrap: wrap; gap: 10px;
        position: fixed; top: 75px; left: 260px; right: 20px;
        z-index: 9999;
    }
    .tpl-sa-checkbox { width: 18px; height: 18px; cursor: pointer; accent-color: #000; }

    /* Template checkbox */
    .tpl-cb-wrap { position: absolute; top: 6px; left: 6px; z-index: 10; }
    .tpl-checkbox { width: 18px; height: 18px; cursor: pointer; accent-color: #000; }
    .template-card { position: relative; }
    .template-card.tpl-selected { outline: 2px solid #000; box-shadow: 0 0 0 4px rgba(0,0,0,.12)!important; }

    /* Cards Row */
    .cat-cards-row { display:flex; gap:14px; overflow-x:auto; padding-bottom:8px; scrollbar-width:thin; }
    .cat-card { flex:0 0 130px; background:#fff; border:2px solid #e0e0e0; border-radius:14px; padding:14px 10px; text-align:center; cursor:pointer; transition:all .25s; user-select:none; }
    .cat-card:hover { border-color:#000; transform:translateY(-3px); box-shadow:0 8px 20px rgba(0,0,0,.12); }
    .cat-card.active { background:#000; border-color:#000; color:#fff; }
    .cat-card-icon { font-size:26px; margin-bottom:6px; }
    .cat-card-thumb { width:44px; height:44px; margin:0 auto 6px; border-radius:8px; overflow:hidden; }
    .cat-card-thumb img { width:100%; height:100%; object-fit:cover; }
    .cat-card-name { font-size:12px; font-weight:700; margin-bottom:3px; }
    .cat-card-count { font-size:11px; opacity:.6; }
    .cat-block-header { background:#f8f8f8; border:1px solid #e0e0e0; border-radius:12px; padding:14px 18px; }
    .model-name-divider { display:flex; align-items:center; }
    .model-name-divider::before, .model-name-divider::after { content:''; flex:1; height:1px; background:#000; }
    .model-name-divider span { font-size:18px; font-weight:700; padding:0 12px; white-space:nowrap; }
    .template-card { border-radius:12px; border:1px solid #e8e8e8; background:#fff; transition:all .3s; }
    .template-card:hover { transform:translateY(-5px) scale(1.02); box-shadow:0 12px 24px rgba(0,0,0,.12)!important; }

    /* Confirm Modal */
    .confirm-overlay { position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:99998; display:flex; align-items:center; justify-content:center; }
    .confirm-box { background:#fff; border-radius:16px; padding:2rem 2.5rem; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,.3); min-width:300px; animation:popIn .25s ease-out; }
    @keyframes popIn { from{transform:scale(.8);opacity:0} to{transform:scale(1);opacity:1} }
    .confirm-title { font-weight:700; font-size:18px; margin-bottom:6px; }
    .confirm-msg { color:#555; font-size:14px; margin-bottom:20px; }
    .confirm-btns { display:flex; gap:12px; justify-content:center; }
    .confirm-btns .btn { min-width:100px; }
</style>

<script>
    // ── Toast ──
    function showToast(message, type = 'success', duration = 3000) {
        const icons = { success: '✓', error: '✕', warning: '⚠' };
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast-item toast-${type}`;
        toast.innerHTML = `<span>${icons[type]||'✓'}</span> ${message}`;
        container.appendChild(toast);
        requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.add('show')));
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 400); }, duration);
    }

    // ── Switch Block ──
    function switchBlock(card, targetId) {
        document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
        document.querySelectorAll('.cat-block').forEach(b => b.style.display = 'none');
        document.getElementById(targetId).style.display = 'block';
        // uncheck all when switching
        document.querySelectorAll('.tpl-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.template-card').forEach(c => c.classList.remove('tpl-selected'));
        document.getElementById('tplSelectAll').checked = false;
        renderToolbar();
    }

    // ── Single delete (from card button) ──
    function confirmDelete(e, form) {
        e.preventDefault();
        const modal = document.getElementById('confirmModal');
        document.querySelector('.confirm-msg').textContent = 'Are you sure you want to delete this template?';
        modal.style.display = 'flex';
        document.getElementById('confirmOk').onclick = () => { modal.style.display = 'none'; form.submit(); };
        document.getElementById('confirmCancel').onclick = () => { modal.style.display = 'none'; };
    }

    // ── Render Toolbar ──
    function renderToolbar() {
        const checked = getVisibleChecked();
        const btn = document.getElementById('tplDeleteBtn');
        if (checked.length > 0) {
            btn.disabled = false;
            btn.classList.remove('opacity-50');
            btn.innerHTML = `<i class="bi bi-trash-fill me-1"></i> Delete (${checked.length})`;
        } else {
            btn.disabled = true;
            btn.classList.add('opacity-50');
            btn.innerHTML = `<i class="bi bi-trash-fill me-1"></i> Delete`;
        }
        // sync select-all state
        const allVisible = getVisibleCheckboxes();
        const saEl = document.getElementById('tplSelectAll');
        saEl.checked = allVisible.length > 0 && allVisible.every(cb => cb.checked);
    }

    function getVisibleCheckboxes() {
        return Array.from(document.querySelectorAll('.tpl-checkbox'))
            .filter(cb => cb.offsetParent !== null);
    }

    function getVisibleChecked() {
        return getVisibleCheckboxes().filter(cb => cb.checked);
    }

    document.addEventListener('DOMContentLoaded', function () {

        // ── Select All ──
        document.getElementById('tplSelectAll').addEventListener('change', function () {
            getVisibleCheckboxes().forEach(cb => {
                cb.checked = this.checked;
                const card = cb.closest('.template-card');
                if (card) card.classList.toggle('tpl-selected', this.checked);
            });
            renderToolbar();
        });

        // ── Checkbox: shift-click & individual ──
        let lastChecked = null;
        document.addEventListener('change', function (e) {
            if (!e.target.classList.contains('tpl-checkbox')) return;
            const cb = e.target;
            const card = cb.closest('.template-card');
            if (card) card.classList.toggle('tpl-selected', cb.checked);
            renderToolbar();
        });

        document.addEventListener('click', function (e) {
            if (!e.target.classList.contains('tpl-checkbox')) return;
            const cb = e.target;
            if (e.shiftKey && lastChecked && lastChecked !== cb) {
                const all = getVisibleCheckboxes();
                const s = all.indexOf(cb);
                const en = all.indexOf(lastChecked);
                const state = lastChecked.checked;
                for (let i = Math.min(s, en); i <= Math.max(s, en); i++) {
                    all[i].checked = state;
                    const c = all[i].closest('.template-card');
                    if (c) c.classList.toggle('tpl-selected', state);
                }
                renderToolbar();
            }
            lastChecked = cb;
        });

        // ── Bulk Delete Button ──
        document.getElementById('tplDeleteBtn').addEventListener('click', function () {
            const ids = getVisibleChecked().map(cb => cb.value);
            if (!ids.length) { showToast('Koi template select nahi!', 'warning'); return; }

            const modal = document.getElementById('confirmModal');
            document.querySelector('.confirm-msg').textContent =
                `Are you sure you want to delete ${ids.length} template(s)?`;
            modal.style.display = 'flex';

            document.getElementById('confirmOk').onclick = async () => {
                modal.style.display = 'none';
                try {
                    const res = await fetch('{{ route('templates.bulkDestroy') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ ids })
                    });
                    const data = await res.json();
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => location.reload(), 1400);
                    } else {
                        showToast('Error: ' + data.message, 'error');
                    }
                } catch (err) {
                    showToast('Something went wrong!', 'error');
                }
            };
            document.getElementById('confirmCancel').onclick = () => { modal.style.display = 'none'; };
        });

        renderToolbar();
    });
</script>

@endsection
