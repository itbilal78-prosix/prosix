@extends('layouts.dashboard')

@section('content')

<div class="container-fluid">
    <h4 class="mb-4">Place Orders</h4>

    <!-- PDF Button -->
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
                        <th>Order</th>
                        <th>Status</th>
                        <th>Files</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($orders as $order)
                    @php $files = $order->mockup_files ?? []; @endphp

                    <tr>
                        <td><input type="checkbox" class="row-checkbox" value="{{ $order->id }}"></td>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->full_name }}</td>
                        <td>{{ $order->email }}</td>
                        <td>{{ $order->order_number }}</td>

                        <td>
                            <span class="badge bg-warning">{{ $order->status }}</span>
                        </td>

                        <td>
                            @if(count($files))
                                <span class="badge bg-dark">{{ count($files) }} files</span>
                            @else
                                0
                            @endif
                        </td>

                        <td>
                            <button class="btn btn-sm btn-dark view-order-btn"
                                data-name="{{ $order->full_name }}"
                                data-email="{{ $order->email }}"
                                data-order="{{ $order->order_number }}"
                                data-date="{{ $order->order_date }}"
                                data-delivery="{{ $order->delivery_date }}"
                                data-sales="{{ $order->sales_rep }}"
                                data-colors="{{ $order->team_colors }}"
                                data-notes="{{ $order->notes }}"
                                data-status="{{ $order->status }}"
                                data-files="{{ json_encode($files) }}">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>

<!-- ===== MODAL ===== -->
<div class="modal fade" id="viewOrderModal">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div style="background:#000;padding:15px;text-align:center;color:#fff;">
                PROSIX SPORTS
            </div>

            <div class="modal-body p-4">

                <h5 class="text-center fw-bold border-bottom pb-2 mb-3">
                    PLACE ORDER DETAILS
                </h5>

                <table width="100%">
                    <tr><td class="modal-label">Full Name</td><td id="o-name"></td></tr>
                    <tr><td class="modal-label">Email</td><td id="o-email"></td></tr>
                    <tr><td class="modal-label">Order No</td><td id="o-order"></td></tr>
                    <tr><td class="modal-label">Order Date</td><td id="o-date"></td></tr>
                    <tr><td class="modal-label">Delivery</td><td id="o-delivery"></td></tr>
                    <tr><td class="modal-label">Sales Rep</td><td id="o-sales"></td></tr>
                    <tr><td class="modal-label">Colors</td><td id="o-colors"></td></tr>
                    <tr><td class="modal-label">Notes</td><td id="o-notes"></td></tr>
                    <tr><td class="modal-label">Status</td><td id="o-status"></td></tr>
                </table>

                <!-- FILES -->
                <div id="files-section" style="margin-top:20px;display:none;">
                    <h6>Files</h6>
                    <div id="files-grid" class="row"></div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<style>
.modal-label {
    font-weight:600;
    background:#f5f5f5;
    padding:8px;
    width:35%;
}
.img-box {
    height:100px;
    border:1px solid #ddd;
    overflow:hidden;
    border-radius:6px;
    position:relative;
}
.img-box img {
    width:100%;
    height:100%;
    object-fit:cover;
}
.download-btn {
    position:absolute;
    top:5px;
    right:5px;
    background:#000;
    color:#fff;
    padding:4px 6px;
    font-size:10px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){

    const checkboxes = document.querySelectorAll('.row-checkbox');
    const selectAll = document.getElementById('selectAll');

    // SELECT ALL
    selectAll.addEventListener('change', function(){
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // VIEW MODAL
    document.querySelectorAll('.view-order-btn').forEach(btn => {
        btn.addEventListener('click', function(){

            const d = this.dataset;

            document.getElementById('o-name').innerText = d.name;
            document.getElementById('o-email').innerText = d.email;
            document.getElementById('o-order').innerText = d.order;
            document.getElementById('o-date').innerText = d.date;
            document.getElementById('o-delivery').innerText = d.delivery || '-';
            document.getElementById('o-sales').innerText = d.sales || '-';
            document.getElementById('o-colors').innerText = d.colors || '-';
            document.getElementById('o-notes').innerText = d.notes || '-';
            document.getElementById('o-status').innerText = d.status;

            const files = JSON.parse(d.files || '[]');
            const grid = document.getElementById('files-grid');
            const section = document.getElementById('files-section');

            grid.innerHTML = '';

            if(files.length){
                section.style.display = 'block';

                files.forEach(file => {
                    let url = '/uploads/orders/mockup/' + file;

                    grid.innerHTML += `
                        <div class="col-3 mb-2">
                            <div class="img-box">
                                <a href="${url}" target="_blank">
                                    <img src="${url}">
                                </a>
                                <a href="${url}" download class="download-btn">DL</a>
                            </div>
                        </div>
                    `;
                });

            } else {
                section.style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('viewOrderModal')).show();
        });
    });

    // PDF DOWNLOAD
    document.getElementById('downloadPdfBtn').addEventListener('click', function(){

        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if(selected.length === 0){
            alert('Select at least one order');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/place-orders-download';

        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="ids" value="${selected.join(',')}">
        `;

        document.body.appendChild(form);
        form.submit();
    });

});
</script>

@endsection
