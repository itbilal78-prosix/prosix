@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Users Management</h4>
            <input type="text" id="searchInput" class="form-control form-control-sm w-auto"
                   placeholder="Search by name or label..." style="min-width:240px;">
        </div>

        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Customer Label</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Registered At</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="usersTable">
                    @forelse($users as $user)
                        <tr class="{{ $user->is_pinned ? 'table-warning pinned-user-row' : '' }}"
                            data-name="{{ strtolower($user->name) }}"
                            data-label="{{ strtolower($user->customer_label ?? '') }}">

                            <td>{{ $loop->iteration }}</td>

                            <td>

                                {{ $user->name }}
                            </td>

                            <td>
                                @if($user->is_pinned && $user->customer_label)
                                    <span class="badge bg-dark text-white" id="label-{{ $user->id }}">
                                        {{ $user->customer_label }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary" id="label-{{ $user->id }}">
                                        {{ $user->customer_label ?? '-' }}
                                    </span>
                                @endif
                            </td>

                            <td>{{ $user->email }}</td>

                            <td>
                                @if($user->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending OTP</span>
                                @elseif($user->status == 'approved')
                                    <span class="badge bg-success">Verified</span>
                                @elseif($user->status == 'blocked')
                                    <span class="badge bg-dark">Blocked</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($user->status) }}</span>
                                @endif
                            </td>

                            <td>{{ $user->created_at->format('d M Y, h:i A') }}</td>

                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">

                                    {{-- View Dashboard --}}
                                    <a href="{{ route('admin.users.loginAsUser', $user->id) }}"
                                       class="btn btn-dark btn-sm" title="View Dashboard">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Pin / Unpin --}}
                                    <button class="btn btn-sm {{ $user->is_pinned ?  : 'btn-outline-dark' }} pin-btn"
                                            data-id="{{ $user->id }}"
                                            data-pinned="{{ $user->is_pinned ? '1' : '0' }}"
                                            data-name="{{ $user->name }}"
                                            title="{{ $user->is_pinned ? 'Unpin' : 'Pin as Customer' }}">
                                        <i class="bi {{ $user->is_pinned ? 'bi-pin-angle-fill' : 'bi-pin-angle' }}"></i>
                                    </button>

                                    {{-- Block / Unblock --}}
                                    <form action="{{ route('admin.users.toggle', $user->id) }}"
                                          method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        @if($user->status == 'blocked')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Unblock">
                                                <i class="bi bi-unlock"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-dark btn-sm" title="Block">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        @endif
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== Pin Label Modal ===== --}}
<div class="modal fade" id="pinModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"> Pin as Customer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-bold mb-1">Customer Label</label>
                <input type="text" id="customerLabelInput" class="form-control"
                       placeholder="e.g. Ali Bhai, VIP Client, Team Nike...">
              <small class="text-muted mt-1 d-block">
    This name will appear on the <strong>Customers</strong> page — visible to admins only.
</small>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-dark btn-sm" id="savePinBtn">
                     Save & Add to Customers
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.pinned-user-row { background: #fff8db !important; }
.pinned-user-row td { font-weight: 600; }
</style>

<script>
let pinUserId   = null;
let pinUserName = '';
let pinModal    = null;

document.addEventListener('DOMContentLoaded', function () {

    pinModal = new bootstrap.Modal(document.getElementById('pinModal'));

    // ── Pin button click ──────────────────────────────
    document.querySelectorAll('.pin-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const isPinned = this.dataset.pinned === '1';
            pinUserId   = this.dataset.id;
            pinUserName = this.dataset.name;

            if (isPinned) {
                // Seedha unpin — no popup
                if (!confirm('Are you sure you want to remove this user from the Customers list?')) return;
                doPinRequest(pinUserId, '', true);
            } else {
                // Popup kholo with default name
                document.getElementById('customerLabelInput').value = pinUserName;
                pinModal.show();
            }
        });
    });

    // ── Save pin with label ───────────────────────────
    document.getElementById('savePinBtn').addEventListener('click', function () {
        const label = document.getElementById('customerLabelInput').value.trim() || pinUserName;
        pinModal.hide();
        doPinRequest(pinUserId, label, false);
    });

    // ── Enter key submit ──────────────────────────────
    document.getElementById('customerLabelInput').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            document.getElementById('savePinBtn').click();
        }
    });

    // ── Search filter ─────────────────────────────────
    document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#usersTable tr').forEach(row => {
            const name  = row.dataset.name  || '';
            const label = row.dataset.label || '';
            row.style.display = (name.includes(q) || label.includes(q)) ? '' : 'none';
        });
    });

});

// ── AJAX Pin/Unpin ────────────────────────────────────
function doPinRequest(userId, label, isUnpin) {
    fetch('/users/' + userId + '/pin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ customer_label: label })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            setTimeout(() => location.reload(), 200);
        } else {
            alert('Kuch masla hua. Dobara try karein.');
        }
    })
    .catch(() => alert('Server error. Try again.'));
}
</script>
@endsection
