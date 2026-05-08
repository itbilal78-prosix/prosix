@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
            <h4 class="mb-0"> Pinned Customers</h4>
            <input type="text" id="searchInput" class="form-control form-control-sm w-auto"
                   placeholder=" Search customer..." style="min-width:240px;">
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Customer Label</th>
                        <th>Real Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Pinned On</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="customersTable">
                    @forelse($customers as $i => $user)
                        <tr data-name="{{ strtolower($user->name) }}"
                            data-label="{{ strtolower($user->customer_label ?? '') }}">

                            <td>{{ $i + 1 }}</td>

                            <td>
                                <span class="badge bg-dark text-white fs-6 px-3 py-2">
                                    {{ $user->customer_label ?? $user->name }}
                                </span>
                            </td>

                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '-' }}</td>

                            <td>
                                @if($user->status == 'approved')
                                    <span class="badge bg-success">Verified</span>
                                @elseif($user->status == 'blocked')
                                    <span class="badge bg-danger">Blocked</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($user->status) }}</span>
                                @endif
                            </td>

                            <td>{{ $user->updated_at->format('d M Y') }}</td>

                            <td class="text-center">
                                {{-- View Dashboard --}}
                                <a href="{{ route('admin.users.loginAsUser', $user->id) }}"
                                   class="btn btn-dark btn-sm" title="View Customer Dashboard">
                                    <i class="bi bi-eye"></i> View
                                </a>

                                {{-- Unpin --}}
                                <button class="btn btn-outline-dark btn-sm unpin-btn"
                                        data-id="{{ $user->id }}"
                                        title="Remove from Customers">
                                    <i class="bi bi-pin-angle"></i> Unpin
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-pin-angle" style="font-size:2rem;"></i>
                                <p class="mt-2">Koi pinned customer nahi. Users page se pin karein.</p>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-dark btn-sm">
                                    Go to Users
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Search
document.getElementById('searchInput').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#customersTable tr').forEach(row => {
        const name  = row.dataset.name  || '';
        const label = row.dataset.label || '';
        row.style.display = (name.includes(q) || label.includes(q)) ? '' : 'none';
    });
});

// Unpin
document.querySelectorAll('.unpin-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        if (!confirm('Is customer ko unpin karna chahte hain?')) return;
        const id = this.dataset.id;
        const row = this.closest('tr');

        fetch('/users/' + id + '/pin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ customer_label: '' })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                row.remove();
                // Agar koi na bache toh reload
                if (!document.querySelector('#customersTable tr[data-name]')) {
                    location.reload();
                }
            }
        });
    });
});
</script>
@endsection
