@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="mb-0">Products Management</h1>
        <div class="d-flex align-items-center gap-2">
            <input type="text" id="searchInput" class="form-control form-control-sm"
                placeholder="Search by name..." style="min-width: 220px; ">
            <a href="{{ route('products.create') }}" class="btn  btn-dark" style="min-width: 150px;">+ Add Product</a>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mb-4 d-flex justify-content-end gap-2 flex-wrap">
        <!-- Featured -->
        <button id="addFeaturedBtn" class="btn btn-dark px-3">
            <i class="bi bi-star-fill me-1"></i> Add to Featured
        </button>
        <button id="removeFeaturedBtn" class="btn btn-outline-secondary px-3">
            <i class="bi bi-star me-1"></i> Remove Featured
        </button>

        <!-- Apparel -->
        <button id="addApparelBtn" class="btn btn-dark px-3">
            <i class="bi bi-bag-fill me-1"></i> Add to Apparel
        </button>
        <button id="removeApparelBtn" class="btn btn-outline-secondary px-3">
            <i class="bi bi-bag me-1"></i> Remove Apparel
        </button>

        <!-- ✅ Category Assign -->
        <button id="assignCategoryBtn" class="btn btn-dark px-3">
            <i class="bi bi-tag-fill me-1"></i> Add to Category
        </button>
        <button id="removeCategoryBtn" class="btn btn-outline-secondary px-3">
            <i class="bi bi-tag me-1"></i> Remove Category
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Table -->
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

                        <!-- Category - sirf show_in_category=true ho toh dikhao -->
                        <td>
                            @if($product->show_in_category && $product->category)
                                <span class="badge bg-dark">{{ $product->category->name }}</span>
                            @elseif($product->category)
                                <span class="badge bg-secondary" title="Assigned but not shown on frontend">
                                    {{ $product->category->name }} <i class="bi bi-eye-slash-fill ms-1"></i>
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>

                        <!-- Subcategory -->
                        <td>
                            @if($product->show_in_category && $product->subcategory)
                                <span class="badge bg-dark">{{ $product->subcategory->name }}</span>
                            @elseif($product->subcategory)
                                <span class="badge bg-secondary" title="Assigned but not shown on frontend">
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

        <div class="d-flex justify-content-end mt-4" id="paginationLinks">
            {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- ✅ ASSIGN CATEGORY MODAL -->
<div class="modal fade" id="assignCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:#1a1a1a; color:#fff; border:1px solid #444;">
            <div class="modal-header" style="border-color:#444;">
                <h5 class="modal-title"><i class="bi bi-tag-fill me-2"></i>Assign Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <!-- Info Note -->


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
                <button type="button" id="confirmAssignBtn" class="btn btn-secondary">
                    <i class="bi bi-check-lg me-1"></i> Done
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── All categories with subcategories ──
    const allCategories = @json($categories->load('subcategories'));

    // ── Select All ──
    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = this.checked);
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
            document.getElementById('productsTable').innerHTML = doc.getElementById('productsTable').innerHTML;
            document.getElementById('paginationLinks').innerHTML = doc.getElementById('paginationLinks').innerHTML;
        });
    });

    // ── Helper: get selected IDs ──
    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
    }

    // ── Helper: POST request ──
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

    // ── FEATURED ──
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

    // ── APPAREL ──
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

    // ── ADD TO CATEGORY - Modal open ──
    document.getElementById('assignCategoryBtn').addEventListener('click', function () {
        const ids = getSelectedIds();
        if (!ids.length) { alert('Pehle koi product select karo!'); return; }
        // Reset modal
        document.getElementById('modalCategorySelect').value = '';
        document.getElementById('modalSubcategorySelect').innerHTML = '<option value="">-- Subcategory Select Karo --</option>';
        document.getElementById('modalSubcategoryWrapper').style.display = 'none';
        new bootstrap.Modal(document.getElementById('assignCategoryModal')).show();
    });

    // ── Category change → subcategories load ──
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

    // ── Confirm Assign → show_in_category = true ──
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
                show_in_category: true   // ✅ KEY: frontend pe show karo
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

    // ── REMOVE FROM CATEGORY → show_in_category = false ──
    document.getElementById('removeCategoryBtn').addEventListener('click', async function () {
        const ids = getSelectedIds();
        if (!ids.length) { alert('Pehle koi product select karo!'); return; }
        if (!confirm('Selected products ko category se remove karna hai?')) return;

        try {
            const data = await postAction('{{ route("products.bulkCategory") }}', {
                product_ids: ids,
                category_id: null,
                subcategory_id: null,
                show_in_category: false  // ✅ KEY: frontend se hide karo
            });

            if (data.success) { alert(data.message); location.reload(); }
            else alert('Error: ' + data.message);
        } catch (e) { alert('Network error!'); }
    });

});
</script>
@endsection
