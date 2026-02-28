@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('admin.admins.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Edit Admin: {{ $adminUser->name }}</h4>
            <small class="text-muted">Update permissions and password</small>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form action="{{ route('admin.admins.update', $adminUser->id) }}" method="POST">
                        @csrf @method('PUT')

                        {{-- Info (Read only) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Name</label>
                            <input type="text" class="form-control bg-light"
                                   value="{{ $adminUser->name }}" disabled>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control bg-light"
                                   value="{{ $adminUser->email }}" disabled>
                        </div>

                        {{-- New Password (Optional) --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                New Password
                                <small class="text-muted fw-normal">(leave blank to keep current)</small>
                            </label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Enter new password (optional)">
                        </div>

                        {{-- Permissions --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-3">
                                <i class="bi bi-shield-check me-1"></i> Permissions
                            </label>

                            <div class="row g-3">

                                <div class="col-6">
                                    <div class="permission-card {{ $adminUser->can_products ? 'selected' : '' }}"
                                         onclick="togglePermission(this, 'can_products')">
                                        <input type="checkbox" name="can_products" id="can_products"
                                               class="d-none" {{ $adminUser->can_products ? 'checked' : '' }}>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="perm-icon">
                                                <i class="bi bi-box-seam fs-4"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Products</div>
                                                <small class="text-muted">Add, edit, delete products</small>
                                            </div>
                                            <i class="bi bi-check-circle-fill ms-auto check-icon"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="permission-card {{ $adminUser->can_categories ? 'selected' : '' }}"
                                         onclick="togglePermission(this, 'can_categories')">
                                        <input type="checkbox" name="can_categories" id="can_categories"
                                               class="d-none" {{ $adminUser->can_categories ? 'checked' : '' }}>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="perm-icon">
                                                <i class="bi bi-tags fs-4"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Categories</div>
                                                <small class="text-muted">Manage categories</small>
                                            </div>
                                            <i class="bi bi-check-circle-fill ms-auto check-icon"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="permission-card {{ $adminUser->can_customizer ? 'selected' : '' }}"
                                         onclick="togglePermission(this, 'can_customizer')">
                                        <input type="checkbox" name="can_customizer" id="can_customizer"
                                               class="d-none" {{ $adminUser->can_customizer ? 'checked' : '' }}>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="perm-icon">
                                                <i class="bi bi-paint-bucket fs-4"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Customizer</div>
                                                <small class="text-muted">Models, colors, fonts</small>
                                            </div>
                                            <i class="bi bi-check-circle-fill ms-auto check-icon"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="permission-card {{ $adminUser->can_orders ? 'selected' : '' }}"
                                         onclick="togglePermission(this, 'can_orders')">
                                        <input type="checkbox" name="can_orders" id="can_orders"
                                               class="d-none" {{ $adminUser->can_orders ? 'checked' : '' }}>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="perm-icon">
                                                <i class="bi bi-bag fs-4"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Orders</div>
                                                <small class="text-muted">View and manage orders</small>
                                            </div>
                                            <i class="bi bi-check-circle-fill ms-auto check-icon"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-dark flex-grow-1 py-2">
                                <i class="bi bi-save me-2"></i> Save Changes
                            </button>
                            <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary px-4">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.permission-card {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 14px;
    cursor: pointer;
    transition: all 0.2s;
    background: #fff;
    user-select: none;
}
.permission-card:hover { border-color: #aaa; background: #f9f9f9; }
.permission-card.selected { border-color: #000; background: #f0f0f0; }
.permission-card .check-icon { color: #ccc; font-size: 1.2rem; transition: color 0.2s; }
.permission-card.selected .check-icon { color: #000; }
.perm-icon {
    width: 42px; height: 42px;
    background: #f0f0f0; border-radius: 8px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.permission-card.selected .perm-icon { background: #000; color: #fff; }
</style>

<script>
function togglePermission(card, name) {
    card.classList.toggle('selected');
    const checkbox = document.getElementById(name);
    checkbox.checked = card.classList.contains('selected');
}
</script>
@endsection
