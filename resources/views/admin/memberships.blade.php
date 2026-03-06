@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="mb-0">Membership & Special Deal Requests</h1>
        <div class="d-flex gap-2">
            <button id="downloadPdfBtn" class="btn btn-dark btn-sm">
                <i class="bi bi-file-pdf me-1"></i> Download Selected as PDF
            </button>
            <form id="downloadForm" method="GET" action="{{ route('membership.download') }}" style="display:none;">
                <input type="hidden" name="ids" id="downloadIds">
            </form>
          
        </div>
    </div>

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
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>State</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $m)
                        <tr class="member-row">
                            <td><input type="checkbox" class="row-checkbox" value="{{ $m->id }}"></td>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $m->name }}</td>
                            <td><a href="mailto:{{ $m->email }}">{{ $m->email }}</a></td>
                            <td>{{ $m->phone }}</td>
                            <td>{{ $m->role }}</td>
                            <td>{{ $m->state ?? 'N/A' }}</td>
                            <td>{{ $m->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <button type="button"
                                    class="btn btn-sm btn-dark view-btn"
                                    data-id="{{ $m->id }}"
                                    data-name="{{ $m->name }}"
                                    data-email="{{ $m->email }}"
                                    data-phone="{{ $m->phone }}"
                                    data-org="{{ $m->organization }}"
                                    data-role="{{ $m->role }}"
                                    data-sports="{{ implode(', ', $m->sports ?? []) }}"
                                    data-level="{{ $m->level }}"
                                    data-address="{{ $m->address }}"
                                    data-state="{{ $m->state ?? 'Not provided' }}"
                                    data-zip="{{ $m->zip ?? 'Not provided' }}"
                                    data-submitted="{{ $m->created_at->format('d M Y - h:i A') }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== SINGLE SHARED MODAL ===== --}}
<div class="modal fade" id="viewMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:0;overflow:hidden;">

            {{-- Black Header --}}
            <div style="background:#000;padding:16px 24px;text-align:center;">
                <img src="{{ asset('assets/images/P LOGO WHITE.png') }}"
                     style="height:38px;vertical-align:middle;" alt="P">
                <span style="display:inline-block;width:1px;height:34px;background:rgba(255,255,255,0.3);vertical-align:middle;margin:0 14px;"></span>
                <img src="{{ asset('assets/images/PROSIX SPORTS LOGO PNG WHITE.png') }}"
                     style="height:32px;vertical-align:middle;" alt="Prosix">
            </div>

            <div class="modal-body p-4">
                <h5 style="text-align:center;font-weight:800;letter-spacing:1px;text-transform:uppercase;border-bottom:2px solid #000;padding-bottom:12px;margin-bottom:20px;">
                    MEMBERSHIP REQUEST FORM
                </h5>

                <table width="100%" style="border-collapse:collapse;" id="modalDataTable">
                    <tr><td class="modal-label">Full Name</td>         <td class="modal-value" id="md-name"></td></tr>
                    <tr><td class="modal-label">Email</td>             <td class="modal-value" id="md-email"></td></tr>
                    <tr><td class="modal-label">Phone</td>             <td class="modal-value" id="md-phone"></td></tr>
                    <tr><td class="modal-label">Organization/School</td><td class="modal-value" id="md-org"></td></tr>
                    <tr><td class="modal-label">Role</td>              <td class="modal-value" id="md-role"></td></tr>
                    <tr><td class="modal-label">Sports (max 2)</td>    <td class="modal-value" id="md-sports"></td></tr>
                    <tr><td class="modal-label">Apparel Level</td>     <td class="modal-value" id="md-level"></td></tr>
                    <tr><td class="modal-label">Address</td>           <td class="modal-value" id="md-address"></td></tr>
                    <tr><td class="modal-label">State</td>             <td class="modal-value" id="md-state"></td></tr>
                    <tr><td class="modal-label">ZIP / Postal Code</td> <td class="modal-value" id="md-zip"></td></tr>
                    <tr><td class="modal-label">Submitted On</td>      <td class="modal-value" id="md-submitted"></td></tr>
                </table>
            </div>

            <div style="background:#f4f4f4;padding:12px;text-align:center;font-size:11px;color:#999;border-top:1px solid #ddd;">
                Copyright &copy; 2009 – 2024, All Rights Reserved Design by: Prosix Sports LLC
            </div>

            <div class="modal-footer border-0 pt-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.member-row.selected-row { background: #e8efff !important; }
.modal-label {
    padding: 9px 12px;
    background: #f9f9f9;
    font-weight: 600;
    font-size: 13px;
    width: 38%;
    vertical-align: top;
}
.modal-value {
    padding: 9px 12px;
    font-size: 13px;
    vertical-align: top;
    border-bottom: 1px solid #f0f0f0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const selectAll  = document.getElementById('selectAll');
    let lastChecked  = null;

    // ── View Modal - single shared modal ──
    document.querySelectorAll('.view-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('md-name').textContent      = this.dataset.name;
            document.getElementById('md-email').textContent     = this.dataset.email;
            document.getElementById('md-phone').textContent     = this.dataset.phone;
            document.getElementById('md-org').textContent       = this.dataset.org;
            document.getElementById('md-role').textContent      = this.dataset.role;
            document.getElementById('md-sports').textContent    = this.dataset.sports;
            document.getElementById('md-level').textContent     = this.dataset.level;
            document.getElementById('md-address').textContent   = this.dataset.address;
            document.getElementById('md-state').textContent     = this.dataset.state;
            document.getElementById('md-zip').textContent       = this.dataset.zip;
            document.getElementById('md-submitted').textContent = this.dataset.submitted;

            new bootstrap.Modal(document.getElementById('viewMemberModal')).show();
        });
    });

    // ── Shift+Click multi-select ──
    checkboxes.forEach(function(cb) {
        cb.addEventListener('click', function(e) {
            if (e.shiftKey && lastChecked) {
                const all   = Array.from(checkboxes);
                const start = all.indexOf(lastChecked);
                const end   = all.indexOf(this);
                const range = all.slice(Math.min(start, end), Math.max(start, end) + 1);
                range.forEach(c => { c.checked = lastChecked.checked; });
            }
            lastChecked = this;
            updateHighlight();
        });
    });

    // ── Select All ──
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateHighlight();
    });

    function updateHighlight() {
        checkboxes.forEach(cb => {
            const row = cb.closest('tr');
            if (row) row.classList.toggle('selected-row', cb.checked);
        });
    }

    function getSelected() {
        return Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
    }

    // ── Download PDF ──
    document.getElementById('downloadPdfBtn').addEventListener('click', function() {
        const selected = getSelected();
        if (selected.length === 0) { alert('Please select at least one request!'); return; }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("membership.download.pdf") }}';
        form.style.display = 'none';

        const csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const ids = document.createElement('input');
        ids.type = 'hidden'; ids.name = 'ids'; ids.value = selected.join(',');
        form.appendChild(ids);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    });

    // ── Download Excel ──
    document.getElementById('downloadXlsxBtn').addEventListener('click', function() {
        const selected = getSelected();
        if (selected.length === 0) { alert('Please select at least one request!'); return; }
        document.getElementById('downloadIds').value = selected.join(',');
        document.getElementById('downloadForm').submit();
    });
});
</script>
@endsection
