@extends('layouts.dashboard')

@section('content')

<div class="container-fluid">
    <h4 class="mb-4">Place Orders</h4>

    <button id="downloadPdfBtn" class="btn btn-dark btn-sm mb-3">
        <i class="bi bi-file-pdf"></i> Download Selected as PDF
    </button>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Files</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($orders as $order)
                    @php
                        $mockup = $order->mockup_files ?? [];
                        $roster = $order->roster_files ?? [];
                        $quote  = $order->quote_files  ?? [];
                        $total  = count($mockup) + count($roster) + count($quote);
                    @endphp

                    <tr>
                        <td><input type="checkbox" class="row-checkbox" value="{{ $order->id }}"></td>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->full_name }}</td>
                        <td>{{ $order->email }}</td>
                        <td>{{ $order->phone ?? '-' }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>
                            @php
                                $statusColors = ['pending'=>'warning','processing'=>'info','completed'=>'success','cancelled'=>'danger'];
                                $color = $statusColors[$order->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }} status-badge" id="status-badge-{{ $order->id }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td>
                            @if($total)
                                <span class="badge bg-dark">{{ $total }} files</span>
                            @else
                                0
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-dark view-order-btn"
                                data-id="{{ $order->id }}"
                                data-name="{{ $order->full_name }}"
                                data-email="{{ $order->email }}"
                                data-phone="{{ $order->phone ?? '' }}"
                                data-order="{{ $order->order_number }}"
                                data-date="{{ $order->order_date }}"
                                data-delivery="{{ $order->delivery_date }}"
                                data-sales="{{ $order->sales_rep }}"
                                data-colors="{{ $order->team_colors }}"
                                data-notes="{{ $order->notes }}"
                                data-status="{{ $order->status }}"
                                data-mockup="{{ json_encode($mockup) }}"
                                data-roster="{{ json_encode($roster) }}"
                                data-quote="{{ json_encode($quote) }}">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $orders->links() }}</div>
</div>


<!-- ===== MODAL ===== -->
<div class="modal fade" id="viewOrderModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;">

            <!-- Modal Header -->
            <div style="background:#000;padding:16px 24px;display:flex;align-items:center;justify-content:space-between;">
                <span style="color:#fff;font-weight:800;font-size:15px;letter-spacing:2px;">PROSIX SPORTS</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">

                <!-- Form Header (same as customer form) -->
                <div style="background:#f8f8f8;border-bottom:1px solid #e5e5e5;padding:16px 28px;text-align:center;">
                    <h5 style="margin:0;font-weight:800;font-size:20px;font-style:italic;letter-spacing:1px;">THANKS FOR CHOOSING US!</h5>
                    <p style="margin:4px 0 0;font-size:11px;color:#888;letter-spacing:3px;text-transform:uppercase;">WE REALLY APPRECIATE & VALUE YOUR BUSINESS</p>
                </div>

                <div style="padding:28px;">

                    <!-- ── ROW 1: All form fields (same layout as customer form) ── -->
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr 1fr 1.5fr 1fr;gap:16px;margin-bottom:24px;">

                        <div class="po-field">
                            <label class="po-lbl">Full Name</label>
                            <div class="po-val" id="o-name"></div>
                        </div>

                        <div class="po-field">
                            <label class="po-lbl">Email</label>
                            <div class="po-val" id="o-email"></div>
                        </div>

                        <div class="po-field">
                            <label class="po-lbl">Phone</label>
                            <div class="po-val" id="o-phone"></div>
                        </div>

                        <div class="po-field">
                            <label class="po-lbl">Order Place Date</label>
                            <div class="po-val" id="o-date"></div>
                        </div>

                        <div class="po-field">
                            <label class="po-lbl">Delivery Date</label>
                            <div class="po-val" id="o-delivery"></div>
                        </div>

                        <div class="po-field">
                            <label class="po-lbl">Sales Rep</label>
                            <div class="po-val" id="o-sales"></div>
                        </div>

                        <div class="po-field">
                            <label class="po-lbl">Order #</label>
                            <div class="po-val po-val-bold" id="o-order"></div>
                        </div>

                    </div>

                    <!-- Team Colors full width -->
                    <div class="po-field" style="margin-bottom:24px;">
                        <label class="po-lbl">Team Colors</label>
                        <div class="po-val" id="o-colors" style="height:auto;min-height:38px;padding:8px 12px;"></div>
                    </div>

                    <div style="height:1px;background:#e5e5e5;margin-bottom:24px;"></div>

                    <!-- ── ROW 2: Upload Cards + Notes (same as customer form) ── -->
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1.1fr;gap:20px;margin-bottom:24px;">

                        <!-- Mockup -->
                        <div class="po-upload-card">
                            <div class="po-upload-title">📎 Final Mockup Files</div>
                            <div id="mockup-grid" class="files-grid"></div>
                            <div id="mockup-empty" class="po-empty">No files uploaded</div>
                        </div>

                        <!-- Roster -->
                        <div class="po-upload-card">
                            <div class="po-upload-title">📋 Team Roster Files</div>
                            <div id="roster-grid" class="files-grid"></div>
                            <div id="roster-empty" class="po-empty">No files uploaded</div>
                        </div>

                        <!-- Quote -->
                        <div class="po-upload-card">
                            <div class="po-upload-title">📄 Quote / Invoice</div>
                            <div id="quote-grid" class="files-grid"></div>
                            <div id="quote-empty" class="po-empty">No files uploaded</div>
                        </div>

                        <!-- Notes -->
                        <div class="po-upload-card" style="display:flex;flex-direction:column;">
                            <div class="po-upload-title">📝 Notes</div>
                            <div id="o-notes"
                                 style="flex:1;border:1px dashed #ccc;border-radius:6px;padding:10px 12px;
                                        font-size:13px;color:#333;line-height:1.7;min-height:160px;
                                        background:#fafafa;overflow-y:auto;">
                            </div>
                        </div>

                    </div>

                    <div style="height:1px;background:#e5e5e5;margin-bottom:24px;"></div>

                    <!-- ── Status Update ── -->
                    <div class="status-change-box">
                        <h6 style="font-weight:700;margin:0 0 12px;font-size:14px;">Update Order Status</h6>
                        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                            <select id="statusSelect" class="form-select form-select-sm" style="max-width:200px;">
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <button id="updateStatusBtn" class="btn btn-dark btn-sm px-4" onclick="updateStatus()">
                                Update Status
                            </button>
                            <span id="statusMsg" class="text-success small fw-bold" style="display:none;">
                                ✓ Status updated & email sent to customer!
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer" style="border-top:1px solid #e5e5e5;">
                <a id="downloadSinglePdf" href="#" class="btn btn-outline-dark btn-sm" target="_blank">
                    <i class="bi bi-file-pdf"></i> Download PDF
                </a>
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<style>
.po-field { display:flex;flex-direction:column;gap:5px; }
.po-lbl { font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:1px; }
.po-val {
    min-height: 38px;
    border: 1px solid #d0d0d0;
    border-radius: 6px;
    padding: 0 12px;
    font-size: 13px;
    background: #f5f5f5;
    color: #222;
    display: flex;
    align-items: center;
    overflow: hidden;
    text-overflow: ellipsis;
}
.po-val-bold { font-weight:700; }

.po-upload-card { border:1px solid #d0d0d0;border-radius:10px;padding:14px;background:#fff; }
.po-upload-title { font-size:13px;font-weight:700;color:#000;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid #ececec; }
.po-empty { font-size:12px;color:#aaa;text-align:center;padding:20px 0; }

.files-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px; }
.file-card { border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;background:#fff;transition:box-shadow 0.2s; }
.file-card:hover { box-shadow:0 4px 12px rgba(0,0,0,0.1); }
.file-thumb { height:75px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#fafafa;border-bottom:1px solid #eee; }
.file-thumb img { width:100%;height:100%;object-fit:cover; }
.file-icon-box { width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px; }
.ext-label { font-size:10px;font-weight:800;padding:3px 7px;border-radius:4px; }
.ext-pdf  { background:#fff0f0;color:#d32f2f; }
.ext-xls  { background:#f0fff4;color:#2e7d32; }
.ext-doc  { background:#e8f0fe;color:#1565c0; }
.ext-other{ background:#f5f5f5;color:#555; }
.file-name-row { padding:4px 6px;font-size:9px;font-weight:600;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.file-actions { display:flex;gap:3px;padding:3px 5px 6px; }
.file-actions a { flex:1;text-align:center;font-size:10px;padding:3px 4px;border-radius:4px;text-decoration:none;font-weight:600; }
.btn-view-file { background:#f0f0f0;color:#333; }
.btn-view-file:hover { background:#e0e0e0;color:#000; }
.btn-dl-file { background:#000;color:#fff; }
.btn-dl-file:hover { background:#333;color:#fff; }

.status-change-box { background:#f8f8f8;border:1px solid #e5e5e5;border-radius:10px;padding:16px 20px; }
</style>


<script>
let currentOrderId = null;

document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
    });

    document.querySelectorAll('.view-order-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            currentOrderId = d.id;

            document.getElementById('o-name').innerText     = d.name     || '-';
            document.getElementById('o-email').innerText    = d.email    || '-';
            document.getElementById('o-phone').innerText    = d.phone    || '-';
            document.getElementById('o-order').innerText    = d.order    || '-';
            document.getElementById('o-date').innerText     = d.date     || '-';
            document.getElementById('o-delivery').innerText = d.delivery || '-';
            document.getElementById('o-sales').innerText    = d.sales    || '-';
            document.getElementById('o-colors').innerText   = d.colors   || '-';
            document.getElementById('o-notes').innerHTML    = d.notes    || '-';

            document.getElementById('statusSelect').value      = d.status;
            document.getElementById('statusMsg').style.display = 'none';
            document.getElementById('downloadSinglePdf').href  = '/order/download/' + d.id;

            renderFiles('mockup', JSON.parse(d.mockup || '[]'), 'orders/mockup');
            renderFiles('roster', JSON.parse(d.roster || '[]'), 'orders/roster');
            renderFiles('quote',  JSON.parse(d.quote  || '[]'), 'orders/quote');

            new bootstrap.Modal(document.getElementById('viewOrderModal')).show();
        });
    });

    document.getElementById('downloadPdfBtn').addEventListener('click', function () {
        const selected = Array.from(document.querySelectorAll('.row-checkbox'))
            .filter(cb => cb.checked).map(cb => cb.value);
        if (!selected.length) { alert('Select at least one order'); return; }
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/place-orders-download';
        form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="ids" value="${selected.join(',')}">`;
        document.body.appendChild(form);
        form.submit();
    });
});

function renderFiles(type, files, folder) {
    const grid     = document.getElementById(type + '-grid');
    const emptyMsg = document.getElementById(type + '-empty');
    grid.innerHTML = '';

    if (!files || files.length === 0) {
        emptyMsg.style.display = 'block';
        grid.style.display     = 'none';
        return;
    }

    emptyMsg.style.display = 'none';
    grid.style.display     = 'grid';

    files.forEach(function (file) {
        let filename, original, ext;

        if (typeof file === 'object' && file.filename) {
            filename = file.filename;
            original = file.original || file.filename;
            ext      = (file.ext || filename.split('.').pop()).toLowerCase();
        } else {
            filename = file;
            original = file;
            ext      = filename.split('.').pop().toLowerCase();
        }

        const url       = '/uploads/' + folder + '/' + filename;
        const isImage   = ['jpg','jpeg','png','gif','webp','svg'].includes(ext);
        const shortName = original.length > 12 ? original.substring(0,10) + '..' : original;
        const thumbHtml = isImage
            ? `<img src="${url}" alt="${original}" onerror="this.parentElement.innerHTML=getIconHtml('img')">`
            : getIconHtml(ext);

        grid.innerHTML += `
            <div class="file-card">
                <div class="file-thumb">${thumbHtml}</div>
                <div class="file-name-row" title="${original}">${shortName}</div>
                <div class="file-actions">
                    <a href="${url}" target="_blank" class="btn-view-file">View</a>
                    <a href="${url}" download="${original}" class="btn-dl-file">↓ DL</a>
                </div>
            </div>
        `;
    });
}

function getIconHtml(ext) {
    const m = {
        pdf:{ label:'PDF', cls:'ext-pdf', icon:'📕' },
        xls:{ label:'XLS', cls:'ext-xls', icon:'📗' },
        xlsx:{ label:'XLSX', cls:'ext-xls', icon:'📗' },
        doc:{ label:'DOC', cls:'ext-doc', icon:'📘' },
        docx:{ label:'DOCX', cls:'ext-doc', icon:'📘' },
        img:{ label:'IMG', cls:'ext-other', icon:'🖼️' },
    };
    const i = m[ext] || { label: ext.toUpperCase().slice(0,4), cls:'ext-other', icon:'📄' };
    return `<div class="file-icon-box"><div style="font-size:22px;">${i.icon}</div><span class="ext-label ${i.cls}">${i.label}</span></div>`;
}

function updateStatus() {
    if (!currentOrderId) return;
    const status = document.getElementById('statusSelect').value;
    const btn    = document.getElementById('updateStatusBtn');
    btn.disabled  = true;
    btn.innerText = 'Updating...';

    fetch('/api/place-order/' + currentOrderId + '/status', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
        body: JSON.stringify({ status })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const badge    = document.getElementById('status-badge-' + currentOrderId);
            const colorMap = { pending:'warning', processing:'info', completed:'success', cancelled:'danger' };
            if (badge) {
                badge.className = 'badge bg-' + (colorMap[status] || 'secondary') + ' status-badge';
                badge.innerText = status;
            }
            document.getElementById('statusMsg').style.display = 'inline';
        } else {
            alert('Failed to update status.');
        }
    })
    .catch(() => alert('Server error. Try again.'))
    .finally(() => { btn.disabled = false; btn.innerText = 'Update Status'; });
}
</script>

@endsection
