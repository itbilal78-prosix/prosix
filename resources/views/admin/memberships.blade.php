@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h1 class="mb-0">Membership & Special Deal Requests</h1>

            <!-- Hidden form for download -->
            <form id="downloadForm" method="GET" action="{{ route('membership.download') }}" style="display:none;">
                <input type="hidden" name="ids" id="downloadIds">
            </form>

            <button id="downloadBtn" class="btn btn-dark">
                <i class="bi bi-download me-1"></i> Download Selected
            </button>
        </div>

        <!-- Requests Table -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if ($requests->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-3">No membership requests yet.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>

                                    <th>State</th> <!-- ← Naya column add kiya -->
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $key => $m)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="request-checkbox" value="{{ $m->id }}">
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $m->name }}</td>
                                        <td><a href="mailto:{{ $m->email }}">{{ $m->email }}</a></td>
                                        <td>{{ $m->phone }}</td>
                                        <td>{{ $m->role }}</td>

                                        <td>{{ $m->state ?? 'N/A' }}</td> <!-- ← State yahan show ho raha -->
                                        <td>{{ $m->created_at->format('d M Y, H:i') }}</td>
                                        <td>
                                            <!-- View Button -->
                                            <button type="button" class="btn btn-sm btn-dark view-btn"
                                                data-bs-toggle="modal" data-bs-target="#viewModal{{ $m->id }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal (same rakha hai, sirf state already table mein hai) -->
                                    <div class="modal fade" id="viewModal{{ $m->id }}" tabindex="-1"
                                        aria-labelledby="viewModalLabel{{ $m->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title" id="viewModalLabel{{ $m->id }}">
                                                        Membership Request Details - #{{ $m->id }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <strong>Name:</strong> {{ $m->name }}
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Email:</strong> <a
                                                                href="mailto:{{ $m->email }}">{{ $m->email }}</a>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Phone:</strong> {{ $m->phone }}
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Role:</strong> {{ $m->role }}
                                                        </div>
                                                        <div class="col-12">
                                                            <strong>Organization / School:</strong> {{ $m->organization }}
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Mailing Address:</strong> {{ $m->address }}
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>State / Province:</strong> {{ $m->state ?? 'N/A' }}
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Zip / Postal Code:</strong> {{ $m->zip ?? 'N/A' }}
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Apparel Level:</strong> {{ $m->level }}
                                                        </div>
                                                        <div class="col-12">
                                                            <strong>Sport(s):</strong>
                                                            {{ implode(', ', $m->sports ?? []) ?: 'None' }}
                                                        </div>
                                                        <div class="col-12">
                                                            <strong>Submitted On:</strong>
                                                            {{ $m->created_at->format('d M Y, H:i') }}
                                                        </div>
                                                        @if ($m->updated_at != $m->created_at)
                                                            <div class="col-12">
                                                                <strong>Last Updated:</strong>
                                                                {{ $m->updated_at->format('d M Y, H:i') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- JS for Select & Download (same rakha) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.request-checkbox');
            const downloadBtn = document.getElementById('downloadBtn');

            // Select/Deselect All
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            }

            // Download logic
            if (downloadBtn) {
               downloadBtn.addEventListener('click', function() {
    let selectedIds = Array.from(checkboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value);

    if (selectedIds.length === 0) {
        alert('Please select at least one request!');
        return;
    }

    const url = "{{ route('membership.download') }}?ids=" + selectedIds.join(',');

    // Trigger download via invisible <a>
    const a = document.createElement('a');
    a.href = url;
    a.download = ''; // browser will auto name
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
});
            }
        });
    </script>
@endsection