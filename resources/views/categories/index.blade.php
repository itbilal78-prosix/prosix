@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Categories</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('categories.create') }}" class="btn btn-dark btn-sm">Add Category</a>
            <a href="{{ route('categories.subcreate') }}" class="btn btn-outline-dark btn-sm">Add Sub-Category</a>
        </div>
    </div>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-dark py-2">{{ session('success') }}</div>
    @endif

    {{-- ── NAVIGATION CARDS ── --}}
    <div class="cat-nav mb-4">
        @foreach($navigations as $nav)
            <div class="cat-card" data-id="{{ $nav->id }}" onclick="selectNav({{ $nav->id }})">
                <span class="cat-card-name">{{ $nav->title }}</span>
            </div>
        @endforeach
    </div>

    {{-- ── EMPTY STATE ── --}}
    <div id="emptyState" class="empty-box">
        <i class="bi bi-hand-index-thumb fs-2 text-muted"></i>
        <p class="mt-2 mb-0 text-muted fw-semibold">Select a navigation to view categories</p>
    </div>

    {{-- ── NO DATA MSG ── --}}
    <div id="noDataMsg" class="empty-box d-none">
        <i class="bi bi-inbox fs-2 text-muted"></i>
        <p class="mt-2 mb-0 text-muted fw-semibold">No categories found for this navigation</p>
    </div>

    {{-- ── TABLE ── --}}
    <div id="catTable" class="card border-0 shadow-sm d-none">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 bw-table">
                <thead>
                    <tr>
                        <th style="width:35%">Name</th>
                        <th style="width:15%">Thumbnail</th>
                        <th style="width:15%">Status</th>
                        <th style="width:15%">Highlight</th>
                        <th style="width:20%">Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>
    </div>

</div>

{{-- ── DATA ── --}}
@php
    $navData = [];

    foreach($navigations as $nav) {
        $cats = [];

        foreach($categories->where('navigation_id', $nav->id)->whereNull('parent_id') as $cat) {
            $subs = $cat->subcategories->sortBy('position')->map(function($sub) {
                return [
                    'id'        => $sub->id,
                    'name'      => $sub->name,
                    'icon'      => $sub->icon_image ?? '',
                    'status'    => $sub->status ? 'Active' : 'Inactive',
                    'highlight' => $sub->highlight ? '✓' : '—',
                    'edit_url'  => route('categories.edit', $sub->id),
                    'del_url'   => route('categories.destroy', $sub->id),
                ];
            })->values()->toArray();

            $cats[] = [
                'id'        => $cat->id,
                'name'      => $cat->name,
                'icon'      => $cat->icon_image ?? '',
                'status'    => $cat->status ? 'Active' : 'Inactive',
                'highlight' => $cat->highlight ? '✓' : '—',
                'edit_url'  => route('categories.edit', $cat->id),
                'del_url'   => route('categories.destroy', $cat->id),
                'subs'      => $subs,
            ];
        }

        $navData[$nav->id] = $cats;
    }
@endphp

<script>
    const navData   = @json($navData);
    const csrfToken = '{{ csrf_token() }}';

    function selectNav(navId) {
        document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
        document.querySelector(`.cat-card[data-id="${navId}"]`)?.classList.add('active');

        document.getElementById('emptyState').classList.add('d-none');
        document.getElementById('noDataMsg').classList.add('d-none');
        document.getElementById('catTable').classList.add('d-none');

        const cats = navData[navId] || [];

        if (!cats.length) {
            document.getElementById('noDataMsg').classList.remove('d-none');
            return;
        }

        let html = '';

        cats.forEach(cat => {
            const thumb = cat.icon
                ? `<img src="${cat.icon}" width="40" height="40" style="object-fit:cover;border:1px solid #ccc;border-radius:4px;">`
                : '—';

            const arrow = cat.subs.length
                ? `<i class="bi bi-chevron-down toggle-icon me-2" style="font-size:13px;transition:transform .2s;"></i>`
                : `<span style="display:inline-block;width:20px;"></span>`;

            const clickAttr = cat.subs.length
                ? `onclick="toggleSubs(${cat.id})" style="cursor:pointer;"`
                : '';

            html += `
            <tr class="parent-row" data-cat="${cat.id}" ${clickAttr}>
                <td class="fw-semibold">${arrow}${cat.name}</td>
                <td>${thumb}</td>
                <td>${cat.status}</td>
                <td class="text-center">${cat.highlight}</td>
                <td>
                    <a href="${cat.edit_url}" class="btn btn-xs btn-outline-dark me-1">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button class="btn btn-xs btn-outline-dark" onclick="event.stopPropagation();deleteRow('${cat.del_url}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;

            cat.subs.forEach(sub => {
                const sThumb = sub.icon
                    ? `<img src="${sub.icon}" width="34" height="34" style="object-fit:cover;border:1px solid #ccc;border-radius:4px;">`
                    : '—';

                html += `
                <tr class="sub-row sub-of-${cat.id}" style="display:none;">
                    <td>
                        <div class="sub-name-wrap">
                            <span class="sub-tree-line"></span>
                            <span class="sub-name">${sub.name}</span>
                        </div>
                    </td>
                    <td>${sThumb}</td>
                    <td><span class="sub-text">${sub.status}</span></td>
                    <td class="text-center"><span class="sub-text">${sub.highlight}</span></td>
                    <td>
                        <a href="${sub.edit_url}" class="btn btn-xs btn-outline-dark me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button class="btn btn-xs btn-outline-dark" onclick="deleteRow('${sub.del_url}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
            });
        });

        document.getElementById('tableBody').innerHTML = html;
        document.getElementById('catTable').classList.remove('d-none');
    }

    function toggleSubs(catId) {
        const subs = document.querySelectorAll(`.sub-of-${catId}`);
        const icon = document.querySelector(`tr[data-cat="${catId}"] .toggle-icon`);
        const open = subs.length && subs[0].style.display !== 'table-row';

        subs.forEach(r => r.style.display = open ? 'table-row' : 'none');
        if (icon) icon.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    function deleteRow(url) {
        if (!confirm('Delete?')) return;
        const f = document.createElement('form');
        f.method = 'POST'; f.action = url;
        f.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">
                       <input type="hidden" name="_method" value="DELETE">`;
        document.body.appendChild(f);
        f.submit();
    }
</script>

<style>
    /* ── NAV PILLS ── */
    .cat-nav { display: flex; flex-wrap: wrap; gap: 10px; }

    .cat-card {
        padding: 8px 18px;
        border: 2px solid #ddd;
        border-radius: 20px;
        background: #fff;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        color: #444;
        transition: all .15s ease;
    }
    .cat-card:hover  { border-color: #555; background: #f5f5f5; }
    .cat-card.active { border-color: #000; background: #000; color: #fff; }

    /* ── TABLE ── */
    .bw-table th { background: #000; color: #fff; font-weight: 600; }
    .bw-table td { color: #000; }

    .parent-row { background: #fff; }
    .parent-row:hover { background: #f9f9f9; }

    /* ── SUB ROW ── */
    .sub-row { background: #f7f7f7; }
    .sub-row td { border-top: none !important; padding-top: 6px; padding-bottom: 6px; }

    .sub-name-wrap {
        display: flex;
        align-items: center;
        padding-left: 40px;  /* main indent */
        gap: 8px;
    }

    .sub-tree-line {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-left: 2px solid #bbb;
        border-bottom: 2px solid #bbb;
        border-radius: 0 0 0 4px;
        flex-shrink: 0;
        margin-bottom: 4px;
    }

    .sub-name {
        font-size: 13px;
        color: #444;
        font-weight: 500;
    }

    .sub-text {
        font-size: 13px;
        color: #666;
        padding-left: 4px;
    }

    /* ── EMPTY BOX ── */
    .empty-box {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        min-height: 160px;
        border: 2px dashed #ddd;
        border-radius: 12px;
        background: #fafafa;
        text-align: center; padding: 20px;
    }

    .btn-xs { padding: 3px 8px; font-size: 12px; }
</style>
@endsection
