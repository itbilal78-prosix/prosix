@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Activity Logs</h4>
            <small class="text-muted">Har admin ka kaam dekho</small>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Admin</label>
                    <select name="admin_id" class="form-select">
                        <option value="">All Admins</option>
                        @foreach($admins as $a)
                            <option value="{{ $a->id }}" {{ request('admin_id') == $a->id ? 'selected' : '' }}>
                                {{ $a->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Module</label>
                    <select name="module" class="form-select">
                        <option value="">All</option>
                        <option value="Admin"    {{ request('module') == 'Admin'    ? 'selected' : '' }}>Admin</option>
                        <option value="Order"    {{ request('module') == 'Order'    ? 'selected' : '' }}>Order</option>
                        <option value="Product"  {{ request('module') == 'Product'  ? 'selected' : '' }}>Product</option>
                        <option value="Category" {{ request('module') == 'Category' ? 'selected' : '' }}>Category</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Action</label>
                    <select name="action" class="form-select">
                        <option value="">All</option>
                        <option value="created"        {{ request('action') == 'created'        ? 'selected' : '' }}>Created</option>
                        <option value="updated"        {{ request('action') == 'updated'        ? 'selected' : '' }}>Updated</option>
                        <option value="deleted"        {{ request('action') == 'deleted'        ? 'selected' : '' }}>Deleted</option>
                        <option value="status_changed" {{ request('action') == 'status_changed' ? 'selected' : '' }}>Status Changed</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-1 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x"></i>
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- Logs Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="py-3">Admin</th>
                        <th class="py-3">Action</th>
                        <th class="py-3">Module</th>
                        <th class="py-3">Target</th>
                        <th class="py-3">Changes</th>
                        <th class="py-3">IP</th>
                        <th class="py-3">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $i => $log)
                    <tr>
                        <td class="px-4">{{ $logs->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-semibold">{{ $log->admin_name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $log->action_color }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-dark">{{ $log->module }}</span>
                        </td>
                        <td class="text-muted">{{ $log->target_name }}</td>
                        <td>
                            @if($log->changes)
                                <button class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#changes-{{ $log->id }}">
                                    <i class="bi bi-eye"></i> View
                                </button>

                                {{-- Modal --}}
                                <div class="modal fade" id="changes-{{ $log->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h6 class="modal-title">
                                                    {{ $log->admin_name }} — {{ $log->target_name }}
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if(isset($log->changes['old']) && isset($log->changes['new']))
                                                    <table class="table table-sm">
                                                        <thead><tr><th>Permission</th><th>Old</th><th>New</th></tr></thead>
                                                        <tbody>
                                                            @foreach($log->changes['old'] as $key => $oldVal)
                                                                @php $newVal = $log->changes['new'][$key] ?? null; @endphp
                                                                <tr class="{{ $oldVal != $newVal ? 'table-warning' : '' }}">
                                                                    <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                                                    <td>
                                                                        @if(is_bool($oldVal) || in_array($oldVal, [0,1]))
                                                                            <i class="bi bi-{{ $oldVal ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }}"></i>
                                                                        @else
                                                                            {{ $oldVal }}
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if(is_bool($newVal) || in_array($newVal, [0,1]))
                                                                            <i class="bi bi-{{ $newVal ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }}"></i>
                                                                        @else
                                                                            {{ $newVal }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            @if(!empty($log->changes['password_changed']))
                                                                <tr class="table-warning">
                                                                    <td>Password</td>
                                                                    <td>—</td>
                                                                    <td><span class="badge bg-warning text-dark">Changed</span></td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                            @else
    {{-- Product image show karo agar hai --}}
    @if(isset($log->changes['image']) && $log->changes['image'])
        <div class="text-center mb-3">
            <img src="{{ $log->changes['image'] }}"
                 style="max-width: 150px; max-height: 150px; border-radius: 8px; border: 1px solid #eee; object-fit: cover;">
            <div class="text-muted small mt-1">Product Image</div>
        </div>
    @endif

    {{-- Baaki details table mein --}}
    <table class="table table-sm">
        <tbody>
            @foreach($log->changes as $key => $val)
                @if($key === 'image') @continue @endif
                <tr>
                    <td class="text-muted fw-semibold" style="width:40%">
                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                    </td>
                    <td>{{ $val ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $log->ip_address }}</td>
                        <td class="text-muted small">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                            Koi activity nahi mili
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3 d-flex justify-content-end">
        {{ $logs->links() }}
    </div>

</div>
@endsection
