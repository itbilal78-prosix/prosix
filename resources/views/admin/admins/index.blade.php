@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Manage Admins</h4>
            <small class="text-muted">Create and manage admin access permissions</small>
        </div>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-dark">
            <i class="bi bi-plus-circle me-2"></i> Add New Admin
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-x-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Type</th>
                        <th class="py-3 text-center">Products</th>
                        <th class="py-3 text-center">Categories</th>
                        <th class="py-3 text-center">Customizer</th>
                        <th class="py-3 text-center">Orders</th>
                        <th class="py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $i => $adminUser)
                    <tr>
                        <td class="px-4">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-semibold">{{ $adminUser->name }}</div>
                        </td>
                        <td class="text-muted">{{ $adminUser->email }}</td>
                        <td>
                            @if($adminUser->is_super_admin)
                                <span class="badge bg-dark">Super Admin</span>
                            @else
                                <span class="badge bg-secondary">Sub Admin</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($adminUser->is_super_admin || $adminUser->can_products)
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($adminUser->is_super_admin || $adminUser->can_categories)
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($adminUser->is_super_admin || $adminUser->can_customizer)
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($adminUser->is_super_admin || $adminUser->can_orders)
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                @if(!$adminUser->is_super_admin)
                                <a href="{{ route('admin.admins.edit', $adminUser->id) }}"
                                   class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.admins.destroy', $adminUser->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this admin?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2"></i>
                            No admins found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
