@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">Categories</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('categories.create') }}" class="btn btn-dark btn-sm">
                Add Category
            </a>
            <a href="{{ route('categories.subcreate') }}" class="btn btn-outline-dark btn-sm">
                Add Sub-Category
            </a>
        </div>
    </div>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-dark py-2">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 bw-table">
                <thead>
                    <tr>
                        <th style="width:35%">Name</th>
                        <th style="width:15%">Thumbnail</th>
                        <th style="width:10%">Status</th>
                        <th style="width:10%">Highlight</th>
                        <th style="width:20%">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($categories->whereNull('parent_id') as $cat)

                    {{-- PARENT ROW --}}
                    <tr class="parent-row" data-id="{{ $cat->id }}">
                        <td class="fw-semibold">
                            <i class="bi bi-chevron-down toggle-icon me-2"></i>
                            {{ $cat->name }}
                        </td>

                        <td>
                            @if($cat->icon_image)
                                <img src="{{ $cat->icon_image }}" width="42" height="42" class="rounded object-fit-cover">
                            @else
                                —
                            @endif
                        </td>

                        <td>{{ $cat->status ? 'Active' : 'Inactive' }}</td>

                        <td class="text-center">
                            {!! $cat->highlight ? '✓' : '—' !!}
                        </td>

                        <td>
                            <a href="{{ route('categories.edit', $cat->id) }}" class="btn btn-xs btn-outline-dark me-1">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form action="{{ route('categories.destroy', $cat->id) }}"
                                  method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-dark"
                                        onclick="return confirm('Delete this category and all sub-categories?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- SUB CATEGORIES --}}
                    @foreach($cat->subcategories as $sub)
                    <tr class="sub-row sub-of-{{ $cat->id }}">
                        <td colspan="5">
                            <div class="sub-row-inner">

                                <div class="sub-name">
                                    ↳ {{ $sub->name }}
                                </div>

                                <div class="sub-thumb">
                                    @if($sub->icon_image)
                                        <img src="{{ $sub->icon_image }}" width="30" height="30">
                                    @else
                                        —
                                    @endif
                                </div>

                                <div class="sub-status">
                                    {{ $sub->status ? 'Active' : 'Inactive' }}
                                </div>

                                <div class="sub-highlight">
                                    {!! $sub->highlight ? '✓' : '—' !!}
                                </div>

                                <div class="sub-actions">
                                    <a href="{{ route('categories.edit', $sub->id) }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('categories.destroy', $sub->id) }}"
                                          method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Delete this sub-category?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </td>
                    </tr>
                    @endforeach

                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            No categories found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- STYLES --}}
<style>
/* TABLE */
.bw-table th {
    background: #000;
    color: #fff;
    font-weight: 600;
}
.bw-table td {
    color: #000;
}

/* PARENT */
.parent-row {
    cursor: pointer;
    background: #fff;
}
.parent-row:hover {
    background: #f3f3f3;
}

/* SUB ROW */
.sub-row {
    display: none;
}

/* SUB INNER BOX (NARROW) */
.sub-row-inner {
    width: 85%;
    margin-left: 160px;
    display: grid;
    grid-template-columns: 2fr 80px 100px 80px 120px;
    align-items: center;
    padding: 6px 10px;
    border: 1px solid #000;
    margin-bottom: 6px;
    font-size: 13px;
}

/* ICONS */
.sub-row-inner i {
    color: #000;
    font-size: 14px;
}
.sub-row-inner button {
    border: none;
    background: transparent;
    padding: 0;
    margin-left: 6px;
}

/* IMAGE */
.object-fit-cover {
    object-fit: cover;
}
.sub-thumb img {
    border: 1px solid #000;
    object-fit: cover;
}

/* TOGGLE ICON */
.toggle-icon {
    transition: transform .25s ease;
}
.bi-chevron-up {
    transform: rotate(180deg);
}
</style>

{{-- SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.parent-row').forEach(row => {
        row.addEventListener('click', e => {
            if (e.target.closest('a, button, form')) return;

            const id = row.dataset.id;
            const subs = document.querySelectorAll(`.sub-of-${id}`);
            const icon = row.querySelector('.toggle-icon');

            const open = subs.length && subs[0].style.display !== 'table-row';

            subs.forEach(r => r.style.display = open ? 'table-row' : 'none');

            icon.classList.toggle('bi-chevron-down', !open);
            icon.classList.toggle('bi-chevron-up', open);
        });
    });
});
</script>
@endsection
