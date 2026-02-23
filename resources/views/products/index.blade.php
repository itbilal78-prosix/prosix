@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="mb-0">Products Management</h1>
        <div class="d-flex align-items-center gap-2">
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search by name..."
                style="min-width: 220px;">
            <a href="{{ route('products.create') }}" class="btn btn-dark">+ Add Product</a>
        </div>
    </div>

    <!-- Featured / Apparel Action Buttons -->
    <div class="mb-4 d-flex justify-content-end gap-3 flex-wrap">
        <button id="addFeaturedBtn" class="btn btn-dark px-4">
            <i class="bi bi-star-fill me-1"></i> Add to Featured
        </button>
        <button id="removeFeaturedBtn" class="btn btn-secondary px-4">
            <i class="bi bi-star me-1"></i> Remove from Featured
        </button>
        <button id="addApparelBtn" class="btn btn-dark px-4">
            <i class="bi bi-plus-circle me-1"></i> Apparel Add
        </button>
        <button id="removeApparelBtn" class="btn btn-secondary px-4">
            <i class="bi bi-dash-circle me-1"></i> Remove Apparel
        </button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Products Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-white" style="background-color:#1a1a1a;">
            <thead class="table-dark">
                <tr>
                    <th width="50">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th class="text-center">Status & Actions</th>
                </tr>
            </thead>
            <tbody id="productsTable">
                @forelse($products as $product)
                    <tr style="background-color:#222;">
                        <td>
                            <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                        </td>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ Str::limit($product->description ?? '', 60) }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="rounded"
                                    style="max-width: 80px; height:auto; object-fit:cover; border:1px solid #444;">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($product->is_featured)
                                <span class="badge bg-dark text-white mb-2 d-block">⭐ Featured</span>
                            @endif
                            @if ($product->is_apparel)
                                <span class="badge bg-dark text-white mb-2 d-block">👕 Apparel</span>
                            @endif
                            @if (!$product->is_featured && !$product->is_apparel)
                                <span class="badge bg-secondary mb-2 d-block">Normal</span>
                            @endif

                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-dark">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
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
                        <td colspan="7" class="text-center py-4 text-muted">No products found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-end mt-4" id="paginationLinks">
            {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>



    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const selectAll = document.getElementById('selectAll');
            const addBtn = document.getElementById('addFeaturedBtn');
            const removeBtn = document.getElementById('removeFeaturedBtn');
            // Apparel buttons
            const addApparelBtn = document.getElementById('addApparelBtn');
            const removeApparelBtn = document.getElementById('removeApparelBtn');

            addApparelBtn.addEventListener('click', () => handleApparelAction('add'));
            removeApparelBtn.addEventListener('click', () => handleApparelAction('remove'));

            async function handleApparelAction(action) {
                const selected = Array.from(document.querySelectorAll('.product-checkbox:checked'))
                    .map(cb => cb.value);

                if (selected.length === 0) {
                    alert('Please select at least one product!');
                    return;
                }

                try {
                    const res = await fetch("{{ route('products.apparel') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_ids: selected,
                            action: action // 'add' or 'remove'
                        })
                    });

                    const data = await res.json();

                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Unknown'));
                    }
                } catch (err) {
                    console.error(err);
                    alert('Network error!');
                }
            }
            // Live Search
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                fetch(`{{ route('products.index') }}?search=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        document.getElementById('productsTable').innerHTML = doc.getElementById(
                            'productsTable').innerHTML;
                        document.getElementById('paginationLinks').innerHTML = doc.getElementById(
                            'paginationLinks').innerHTML;
                    });
            });

            // Select All
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.product-checkbox').forEach(cb => {
                    cb.checked = this.checked;
                });
            });

            // Add to Featured
            addBtn.addEventListener('click', () => handleFeaturedAction('add'));

            // Remove from Featured
            removeBtn.addEventListener('click', () => handleFeaturedAction('remove'));

            async function handleFeaturedAction(action) {
                const selected = Array.from(document.querySelectorAll('.product-checkbox:checked'))
                    .map(cb => cb.value);

                if (selected.length === 0) {
                    alert('Please select at least one product!');
                    return;
                }

                try {
                    const res = await fetch("{{ route('products.featured') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_ids: selected,
                            action: action // 'add' ya 'remove'
                        })
                    });

                    const data = await res.json();

                    if (data.success) {
                        alert(data.message ||
                            `Products ${action === 'add' ? 'added to' : 'removed from'} Featured!`);
                        location.reload(); // Simple refresh (sabse safe)
                    } else {
                        alert('Something went wrong: ' + (data.message || 'Unknown error'));
                    }
                } catch (err) {
                    console.error(err);
                    alert('Network error! Please try again.');
                }
            }
        });
    </script>
@endsection