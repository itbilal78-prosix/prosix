@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

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

        {{-- Per Category card --}}
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

        {{-- Uncategorized card --}}
        @if($uncategorized->count() > 0)
            <div class="cat-card" data-target="block-uncategorized"
                 onclick="switchBlock(this,'block-uncategorized')">
                <div class="cat-card-icon"><i class="bi bi-question-circle-fill"></i></div>
                <div class="cat-card-name">Uncategorized</div>
                <div class="cat-card-count">{{ $uncategorized->count() }} items</div>
            </div>
        @endif

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
        <p class="confirm-msg">Are you sure you want to delete this template?</p>
        <div class="confirm-btns">
            <button id="confirmCancel" class="btn btn-outline-dark">Cancel</button>
            <button id="confirmOk" class="btn btn-dark">Delete</button>
        </div>
    </div>
</div>

<style>
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
    .confirm-overlay { position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:99998; display:flex; align-items:center; justify-content:center; }
    .confirm-box { background:#fff; border-radius:16px; padding:2rem 2.5rem; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,.3); min-width:300px; animation:popIn .25s ease-out; }
    @keyframes popIn { from{transform:scale(.8);opacity:0} to{transform:scale(1);opacity:1} }
    .confirm-title { font-weight:700; font-size:18px; margin-bottom:6px; }
    .confirm-msg { color:#555; font-size:14px; margin-bottom:20px; }
    .confirm-btns { display:flex; gap:12px; justify-content:center; }
    .confirm-btns .btn { min-width:100px; }
</style>

<script>
    function switchBlock(card, targetId) {
        document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
        document.querySelectorAll('.cat-block').forEach(b => b.style.display = 'none');
        document.getElementById(targetId).style.display = 'block';
    }

    function confirmDelete(e, form) {
        e.preventDefault();
        const modal = document.getElementById('confirmModal');
        modal.style.display = 'flex';
        document.getElementById('confirmOk').onclick = () => { modal.style.display = 'none'; form.submit(); };
        document.getElementById('confirmCancel').onclick = () => { modal.style.display = 'none'; };
    }
</script>

@endsection
