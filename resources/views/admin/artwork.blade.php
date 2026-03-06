@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Artwork Requests</h4>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="d-flex gap-2">
            <button id="downloadPdfBtn" class="btn btn-dark btn-sm">
                <i class="bi bi-file-pdf me-1"></i> Download Selected as PDF
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover mb-0" id="artworkTable">
                <thead class="table-dark">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Team</th>
                        <th>Products</th>
                        <th>Qty</th>
                        <th>Images</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        @php $images = $req->artwork_file ? json_decode($req->artwork_file) : []; @endphp
                        <tr class="artwork-row">
                            <td><input type="checkbox" class="row-checkbox" value="{{ $req->id }}"></td>
                            <td>{{ $req->id }}</td>
                            <td>{{ $req->full_name }}</td>
                            <td>{{ $req->email }}</td>
                            <td>{{ $req->team_name ?? '—' }}</td>
                            <td>
                                @foreach($req->products as $p)
                                    <span class="badge bg-dark">{{ $p }}</span>
                                @endforeach
                            </td>
                            <td>{{ $req->quantity }}</td>
                            <td>
                                @if(count($images))
                                    <span class="badge bg-dark">{{ count($images) }} image(s)</span><br>
                                    <img src="{{ asset('uploads/artwork/'.$images[0]) }}"
                                         style="width:50px;height:50px;object-fit:cover;border-radius:4px;margin-top:4px;">
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>{{ $req->created_at->format('d M Y') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-dark view-artwork-btn"
                                    data-id="{{ $req->id }}"
                                    data-name="{{ $req->full_name }}"
                                    data-email="{{ $req->email }}"
                                    data-phone="{{ $req->phone }}"
                                    data-instagram="{{ $req->instagram }}"
                                    data-address="{{ $req->address }}"
                                    data-team="{{ $req->team_name }}"
                                    data-role="{{ $req->role }}"
                                    data-qty="{{ $req->quantity }}"
                                    data-color="{{ $req->team_color }}"
                                    data-homeaway="{{ $req->home_away }}"
                                    data-style="{{ $req->design_style }}"
                                    data-material="{{ $req->material }}"
                                    data-sports="{{ is_array($req->products) ? implode(', ', $req->products) : $req->products }}"
                                    data-details="{{ $req->additional }}"
                                    data-source="{{ $req->source }}"
                                    data-submitted="{{ $req->created_at->format('d M Y - h:i A') }}"
                                    data-images="{{ json_encode($images) }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">No Artwork Requests Found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== SINGLE SHARED MODAL - outside table ===== --}}
<div class="modal fade" id="viewArtworkModal" tabindex="-1" aria-hidden="true">
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
                    ARTWORK REQUEST FORM
                </h5>

                <table width="100%" style="border-collapse:collapse;">
                    <tr><td class="modal-label">Full Name</td>        <td class="modal-value" id="aw-name"></td></tr>
                    <tr><td class="modal-label">Email</td>             <td class="modal-value" id="aw-email"></td></tr>
                    <tr><td class="modal-label">Phone</td>             <td class="modal-value" id="aw-phone"></td></tr>
                    <tr><td class="modal-label">Instagram</td>         <td class="modal-value" id="aw-instagram"></td></tr>
                    <tr><td class="modal-label">Address</td>           <td class="modal-value" id="aw-address"></td></tr>
                    <tr><td class="modal-label">Team / Org</td>        <td class="modal-value" id="aw-team"></td></tr>
                    <tr><td class="modal-label">Role</td>              <td class="modal-value" id="aw-role"></td></tr>
                    <tr><td class="modal-label">Order Quantity</td>    <td class="modal-value" id="aw-qty"></td></tr>
                    <tr><td class="modal-label">Team Color</td>        <td class="modal-value" id="aw-color"></td></tr>
                    <tr><td class="modal-label">Home / Away</td>       <td class="modal-value" id="aw-homeaway"></td></tr>
                    <tr><td class="modal-label">Design Style</td>      <td class="modal-value" id="aw-style"></td></tr>
                    <tr><td class="modal-label">Material</td>          <td class="modal-value" id="aw-material"></td></tr>
                    <tr><td class="modal-label">Sport(s)</td>          <td class="modal-value" id="aw-sports"></td></tr>
                    <tr><td class="modal-label">Mockup Details</td>    <td class="modal-value" id="aw-details"></td></tr>
                    <tr><td class="modal-label">How Heard</td>         <td class="modal-value" id="aw-source"></td></tr>
                    <tr><td class="modal-label">Submitted On</td>      <td class="modal-value" id="aw-submitted"></td></tr>
                </table>

                {{-- Images section --}}
                <div id="aw-images-section" style="margin-top:20px;display:none;">
                    <p style="font-weight:700;font-size:13px;border-bottom:1px solid #eee;padding-bottom:6px;">Uploaded Reference Images</p>
                    <div id="aw-images-grid" class="row g-2 mt-1"></div>
                </div>
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
.artwork-row.selected-row { background: #e8efff !important; }
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
.img-thumb-wrap {
    position: relative;
    height: 100px;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #eee;
}
.img-thumb-wrap img { width:100%; height:100%; object-fit:cover; }
.img-dl-btn {
    position: absolute; top: 5px; right: 5px;
    background: rgba(0,0,0,0.6); color: white;
    border-radius: 50%; width: 26px; height: 26px;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; text-decoration: none;
}
.img-dl-btn:hover { background: rgba(0,0,0,0.9); color: white; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const selectAll  = document.getElementById('selectAll');
    let lastChecked  = null;

    // ── View Modal ──
    document.querySelectorAll('.view-artwork-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const d = this.dataset;
            document.getElementById('aw-name').textContent      = d.name      || '—';
            document.getElementById('aw-email').textContent     = d.email     || '—';
            document.getElementById('aw-phone').textContent     = d.phone     || '—';
            document.getElementById('aw-instagram').textContent = d.instagram || '—';
            document.getElementById('aw-address').textContent   = d.address   || '—';
            document.getElementById('aw-team').textContent      = d.team      || '—';
            document.getElementById('aw-role').textContent      = d.role      || '—';
            document.getElementById('aw-qty').textContent       = d.qty       || '—';
            document.getElementById('aw-color').textContent     = d.color     || '—';
            document.getElementById('aw-homeaway').textContent  = d.homeaway  || '—';
            document.getElementById('aw-style').textContent     = d.style     || '—';
            document.getElementById('aw-material').textContent  = d.material  || '—';
            document.getElementById('aw-sports').textContent    = d.sports    || '—';
            document.getElementById('aw-details').textContent   = d.details   || '—';
            document.getElementById('aw-source').textContent    = d.source    || '—';
            document.getElementById('aw-submitted').textContent = d.submitted || '—';

            // Images
            const imagesSection = document.getElementById('aw-images-section');
            const imagesGrid    = document.getElementById('aw-images-grid');
            imagesGrid.innerHTML = '';

            try {
                const imgs = JSON.parse(d.images || '[]');
                if (imgs.length > 0) {
                    imagesSection.style.display = 'block';
                    imgs.forEach(function(img) {
                        const url = '/uploads/artwork/' + img;
                        imagesGrid.innerHTML += `
                            <div class="col-3">
                                <div class="img-thumb-wrap">
                                    <a href="${url}" target="_blank">
                                        <img src="${url}" alt="ref">
                                    </a>
                                    <a href="${url}" download class="img-dl-btn" title="Download">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </div>`;
                    });
                } else {
                    imagesSection.style.display = 'none';
                }
            } catch(e) {
                imagesSection.style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('viewArtworkModal')).show();
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
            updateRowHighlight();
        });
    });

    // ── Select All ──
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateRowHighlight();
    });

    function updateRowHighlight() {
        checkboxes.forEach(cb => {
            const row = cb.closest('tr');
            if (row) row.classList.toggle('selected-row', cb.checked);
        });
    }

    // ── Download PDF ──
    document.getElementById('downloadPdfBtn').addEventListener('click', function() {
        const selected = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
        if (selected.length === 0) { alert('Please select at least one request!'); return; }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("artwork.download.pdf") }}';
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
});
</script>
@endsection
