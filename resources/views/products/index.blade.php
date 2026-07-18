@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">



    <!-- Action Buttons -->
<div class="mt-2 d-flex justify-content-end gap-2 flex-wrap action-toolbar">        <button id="addFeaturedBtn" class="btn btn-dark px-3">
            <i class="bi bi-star-fill me-1"></i> Add to Featured
        </button>
        <button id="removeFeaturedBtn" class="btn btn-outline-secondary px-3">
            <i class="bi bi-star me-1"></i> Remove Featured
        </button>
        <button id="addApparelBtn" class="btn btn-dark px-3">
            <i class="bi bi-bag-fill me-1"></i> Add to Apparel
        </button>
        <button id="removeApparelBtn" class="btn btn-outline-secondary px-3">
            <i class="bi bi-bag me-1"></i> Remove Apparel
        </button>
        <button id="assignCategoryBtn" class="btn btn-dark px-3">
            <i class="bi bi-tag-fill me-1"></i> Add to Category
        </button>
        <button id="removeCategoryBtn" class="btn btn-outline-secondary px-3">
            <i class="bi bi-tag me-1"></i> Remove Category
        </button>
    </div>

    {{-- @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif --}}
<!-- Header -->
    <div class="d-flex justify-content-between align-items-center mt-5 flex-wrap gap-3">
        <h1 class="mb-0 fw-bold">Products Management</h1>
        <div class="d-flex align-items-center gap-2">
            <input type="text" id="searchInput" class="form-control form-control-sm"
                placeholder="Search by name..." style="min-width: 220px;">
            <a href="{{ route('products.create') }}" class="btn btn-dark" style="min-width: 150px;">+ Add Product</a>
        </div>
    </div>
    {{-- ═══════════════════════════════════════════════
         CATEGORY CARDS (top navigation — like Models page)
    ════════════════════════════════════════════════ --}}
    <div class="cat-cards-row mb-4" id="catCardsRow">

        {{-- "All Products" Card --}}
        <div class="cat-card active" data-target="block-all" onclick="switchCatBlock(this, 'block-all')">
            <div class="cat-card-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
            <div class="cat-card-name">All Products</div>
            {{-- <div class="cat-card-count">{{ $products->total() }} items</div> --}}
        </div>

        @foreach($categories as $cat)
        <div class="cat-card" data-target="block-cat-{{ $cat->id }}" onclick="switchCatBlock(this, 'block-cat-{{ $cat->id }}')">
            @if($cat->icon_image)
                <div class="cat-card-thumb">
                    <img src="{{ $cat->icon_image }}" alt="{{ $cat->name }}">
                </div>
            @else
                <div class="cat-card-icon"><i class="bi bi-tag-fill"></i></div>
            @endif
            <div class="cat-card-name">{{ $cat->name }}</div>
            <div class="cat-card-count">{{ $allProducts->where('category_id', $cat->id)->where('show_in_category', true)->count() }} items</div>
        </div>
        @endforeach

    </div>

    {{-- ═══════════════════════════════════════════════
         BLOCK: ALL PRODUCTS
    ════════════════════════════════════════════════ --}}
    <div class="cat-block" id="block-all">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-white" style="background:#1a1a1a;">
                <thead class="table-dark">
                    <tr>
                        <th width="40"><input type="checkbox" id="selectAll"></th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Image</th>
                        <th class="text-center">Status & Actions</th>
                    </tr>
                </thead>
                <tbody id="productsTable">
                    @forelse($products as $product)
                        <tr style="background:#222;">
                            <td><input type="checkbox" class="product-checkbox" value="{{ $product->id }}"></td>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td>
                                @if($product->show_in_category && $product->category)
                                    <span class="badge bg-dark">{{ $product->category->name }}</span>
                                @elseif($product->category)
                                    <span class="badge bg-secondary" title="Assigned but not shown">
                                        {{ $product->category->name }} <i class="bi bi-eye-slash-fill ms-1"></i>
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @if($product->show_in_category && $product->subcategory)
                                    <span class="badge bg-dark">{{ $product->subcategory->name }}</span>
                                @elseif($product->subcategory)
                                    <span class="badge bg-secondary" title="Assigned but not shown">
                                        {{ $product->subcategory->name }} <i class="bi bi-eye-slash-fill ms-1"></i>
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}" class="rounded"
                                        style="max-width:70px;height:auto;border:1px solid #444;">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($product->is_featured)
                                    <span class="badge bg-dark text-white mb-1 d-block text-center">
                                        <i class="bi bi-star-fill me-1"></i> Featured
                                    </span>
                                @endif
                                @if ($product->is_apparel)
                                    <span class="badge bg-dark text-white mb-1 d-block text-center">
                                        <i class="bi bi-bag-fill me-1"></i> Apparel
                                    </span>
                                @endif
                                @if ($product->show_in_category)
                                    <span class="badge bg-dark text-white mb-1 d-block text-center">
                                        <i class="bi bi-tag-fill me-1"></i> In Category
                                    </span>
                                @endif
                                <div class="d-flex justify-content-center gap-2 mt-1">
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-dark">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-dark"
                                            onclick="return confirm('Delete this product?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No products found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- <div class="d-flex justify-content-end mt-4" id="paginationLinks">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div> --}}
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         BLOCKS: PER CATEGORY PRODUCTS
    ════════════════════════════════════════════════ --}}
    @foreach($categories as $cat)
    <div class="cat-block" id="block-cat-{{ $cat->id }}" style="display:none;">

        {{-- Category Header --}}
        <div class="cat-block-header mb-3">
            <div class="d-flex align-items-center gap-3">
                @if($cat->icon_image)
                    <img src="{{ $cat->icon_image }}" width="48" height="48" class="rounded" style="object-fit:cover;border:2px solid #000;">
                @endif
                <div>
                    <h4 class="fw-bold mb-0">{{ $cat->name }}</h4>
                    <small class="text-muted">{{ $allProducts->where('category_id', $cat->id)->where('show_in_category', true)->count() }} products in this category</small>
                </div>
            </div>
        </div>

        {{-- Subcategory Tabs (if any) --}}
        @if($cat->subcategories->count() > 0)
        <div class="sub-tabs mb-3" id="subtabs-{{ $cat->id }}">
            <button class="sub-tab active" data-cat="{{ $cat->id }}" data-sub="all"
                onclick="switchSubTab(this, {{ $cat->id }}, 'all')">
                All
            </button>
            @foreach($cat->subcategories as $sub)
            <button class="sub-tab" data-cat="{{ $cat->id }}" data-sub="{{ $sub->id }}"
                onclick="switchSubTab(this, {{ $cat->id }}, {{ $sub->id }})">
                {{ $sub->name }}
            </button>
            @endforeach
        </div>
        @endif

        {{-- Products Table per Category --}}
        <div class="table-responsive" id="cat-table-{{ $cat->id }}">
            <table class="table table-bordered table-hover align-middle text-white" style="background:#1a1a1a;">
                <thead class="table-dark">
                    <tr>
                        <th width="40"><input type="checkbox" class="selectAllCat" data-cat="{{ $cat->id }}"></th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Subcategory</th>
                        <th>Image</th>
                        <th class="text-center">Status & Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $catProducts = $allProducts->where('category_id', $cat->id)->where('show_in_category', true);
                    @endphp
                    @forelse($catProducts as $product)
                        <tr style="background:#222;" class="cat-product-row sub-row-{{ $product->subcategory_id ?? 'none' }}">
                            <td><input type="checkbox" class="product-checkbox" value="{{ $product->id }}"></td>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td>
                                @if($product->subcategory)
                                    <span class="badge bg-dark">{{ $product->subcategory->name }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}" class="rounded"
                                        style="max-width:70px;height:auto;border:1px solid #444;">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($product->is_featured)
                                    <span class="badge bg-dark text-white mb-1 d-block">
                                        <i class="bi bi-star-fill me-1"></i> Featured
                                    </span>
                                @endif
                                @if ($product->is_apparel)
                                    <span class="badge bg-dark text-white mb-1 d-block">
                                        <i class="bi bi-bag-fill me-1"></i> Apparel
                                    </span>
                                @endif
                                <div class="d-flex justify-content-center gap-2 mt-1">
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-dark">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-dark"
                                            onclick="return confirm('Delete this product?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No products in this category</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
    @endforeach

</div>{{-- /container --}}

<!-- ASSIGN CATEGORY MODAL -->
<div class="modal fade" id="assignCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:#1a1a1a; color:#fff; border:1px solid #444;">
            <div class="modal-header" style="border-color:#444;">
                <h5 class="modal-title"><i class="bi bi-tag-fill me-2"></i>Assign Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Category Select Karo</label>
                    <select id="modalCategorySelect" class="form-select" style="background:#2a2a2a;color:#fff;border-color:#555;">
                        <option value="">-- Category Select Karo --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3" id="modalSubcategoryWrapper" style="display:none;">
                    <label class="form-label fw-bold">Subcategory <small class="text-muted fw-normal">(optional)</small></label>
                    <select id="modalSubcategorySelect" class="form-select" style="background:#2a2a2a;color:#fff;border-color:#555;">
                        <option value="">-- Subcategory Select Karo --</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer" style="border-color:#444;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmAssignBtn" class="btn btn-light">
                    <i class="bi bi-check-lg me-1"></i> Done
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ STYLES ═══ --}}
<style>
    .action-toolbar {
    position: fixed;
    top: 70px;        /* header ke niche fix */
    left: 260px;      /* sidebar width */
    right: 20px;
    z-index: 9999;
    background: white;
    padding: 0px 0px 10px 10px ;
    border-radius: 8px;
}
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

.cat-card-icon {
    font-size: 26px;
    margin-bottom: 6px;
    line-height: 1;
}

.cat-card-thumb {
    width: 44px;
    height: 44px;
    margin: 0 auto 6px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,.1);
}

.cat-card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cat-card.active .cat-card-thumb {
    border-color: rgba(255,255,255,.3);
}

.cat-card-name {
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 3px;
    line-height: 1.3;
}

.cat-card-count {
    font-size: 11px;
    opacity: .6;
}

/* ── Category Block Header ── */
.cat-block-header {
    background: #f8f8f8;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 14px 18px;
}

/* ── Subcategory Tabs ── */
.sub-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.sub-tab {
    background: #fff;
    border: 1.5px solid #ccc;
    border-radius: 20px;
    padding: 5px 16px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
}

.sub-tab:hover {
    border-color: #000;
}

.sub-tab.active {
    background: #000;
    border-color: #000;
    color: #fff;
}
</style>

{{-- ═══ SCRIPTS ═══ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const allCategories = @json($categories->load('subcategories'));

    // ── Category Card Switch ──
    window.switchCatBlock = function(card, targetId) {
        document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
        document.querySelectorAll('.cat-block').forEach(b => b.style.display = 'none');
        document.getElementById(targetId).style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // ── Subcategory Tab Switch ──
    window.switchSubTab = function(btn, catId, subId) {
        document.querySelectorAll(`#subtabs-${catId} .sub-tab`).forEach(t => t.classList.remove('active'));
        btn.classList.add('active');

        const rows = document.querySelectorAll(`#block-cat-${catId} .cat-product-row`);
        rows.forEach(row => {
            if (subId === 'all') {
                row.style.display = '';
            } else {
                row.style.display = row.classList.contains(`sub-row-${subId}`) ? '' : 'none';
            }
        });
    };

    // ── Select All (main table) ──
    document.getElementById('selectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = this.checked);
    });

    // ── Select All per Category ──
    document.querySelectorAll('.selectAllCat').forEach(sa => {
        sa.addEventListener('change', function() {
            const catId = this.dataset.cat;
            document.querySelectorAll(`#block-cat-${catId} .product-checkbox`).forEach(cb => cb.checked = this.checked);
        });
    });

    // ── Search ──
    document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.trim();
        fetch(`{{ route('products.index') }}?search=${encodeURIComponent(q)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const tb = doc.getElementById('productsTable');
            const pg = doc.getElementById('paginationLinks');
            if (tb) document.getElementById('productsTable').innerHTML = tb.innerHTML;
            if (pg) document.getElementById('paginationLinks').innerHTML = pg.innerHTML;
        });
    });

    // ── Helper: get selected IDs ──
    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
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

    // ── Featured ──
    document.getElementById('addFeaturedBtn').addEventListener('click', () => handleFeatured('add'));
    document.getElementById('removeFeaturedBtn').addEventListener('click', () => handleFeatured('remove'));
    async function handleFeatured(action) {
        const ids = getSelectedIds();
        if (!ids.length) { alert('Pehle koi product select karo!'); return; }
        try {
            const data = await postAction('{{ route("products.featured") }}', { product_ids: ids, action });
            if (data.success) { alert(data.message); location.reload(); }
            else alert('Error: ' + data.message);
        } catch (e) { alert('Network error!'); }
    }

    // ── Apparel ──
    document.getElementById('addApparelBtn').addEventListener('click', () => handleApparel('add'));
    document.getElementById('removeApparelBtn').addEventListener('click', () => handleApparel('remove'));
    async function handleApparel(action) {
        const ids = getSelectedIds();
        if (!ids.length) { alert('Pehle koi product select karo!'); return; }
        try {
            const data = await postAction('{{ route("products.apparel") }}', { product_ids: ids, action });
            if (data.success) { alert(data.message); location.reload(); }
            else alert('Error: ' + data.message);
        } catch (e) { alert('Network error!'); }
    }

    // ── Assign Category Modal ──
    document.getElementById('assignCategoryBtn').addEventListener('click', function () {
        const ids = getSelectedIds();
        if (!ids.length) { alert('Pehle koi product select karo!'); return; }
        document.getElementById('modalCategorySelect').value = '';
        document.getElementById('modalSubcategorySelect').innerHTML = '<option value="">-- Subcategory Select Karo --</option>';
        document.getElementById('modalSubcategoryWrapper').style.display = 'none';
        new bootstrap.Modal(document.getElementById('assignCategoryModal')).show();
    });

    document.getElementById('modalCategorySelect').addEventListener('change', function () {
        const catId = parseInt(this.value);
        const subWrapper = document.getElementById('modalSubcategoryWrapper');
        const subSelect = document.getElementById('modalSubcategorySelect');
        subSelect.innerHTML = '<option value="">-- Subcategory Select Karo --</option>';
        if (!catId) { subWrapper.style.display = 'none'; return; }
        const cat = allCategories.find(c => c.id === catId);
        if (cat && cat.subcategories && cat.subcategories.length > 0) {
            cat.subcategories.forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub.id;
                opt.textContent = sub.name;
                subSelect.appendChild(opt);
            });
            subWrapper.style.display = 'block';
        } else {
            subWrapper.style.display = 'none';
        }
    });

    document.getElementById('confirmAssignBtn').addEventListener('click', async function () {
        const ids = getSelectedIds();
        const catId = document.getElementById('modalCategorySelect').value;
        const subId = document.getElementById('modalSubcategorySelect').value;
        if (!catId) { alert('Category select karo!'); return; }
        try {
            const data = await postAction('{{ route("products.bulkCategory") }}', {
                product_ids: ids,
                category_id: catId,
                subcategory_id: subId || null,
                show_in_category: true
            });
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('assignCategoryModal')).hide();
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (e) { alert('Network error!'); }
    });

    // ── Remove Category ──
    document.getElementById('removeCategoryBtn').addEventListener('click', async function () {
        const ids = getSelectedIds();
        if (!ids.length) { alert('Pehle koi product select karo!'); return; }
        if (!confirm('Selected products ko category se remove karna hai?')) return;
        try {
            const data = await postAction('{{ route("products.bulkCategory") }}', {
                product_ids: ids,
                category_id: null,
                subcategory_id: null,
                show_in_category: false
            });
            if (data.success) { alert(data.message); location.reload(); }
            else alert('Error: ' + data.message);
        } catch (e) { alert('Network error!'); }
    });

});
</script>
@endsection
