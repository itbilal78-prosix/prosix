@extends('layouts.dashboard')

@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold">Place Orders</h4>
            <p class="text-muted small mb-0">
                Manage customer orders, files and order statuses.
            </p>
        </div>

        <button id="downloadPdfBtn" class="btn btn-dark btn-sm">
            <i class="bi bi-file-pdf"></i>
            Download Selected as PDF
        </button>
    </div>

    <div class="card shadow-sm border-0 place-orders-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 place-orders-table">
                <thead class="table-dark">
                    <tr>
                        <th>
                            <input
                                type="checkbox"
                                id="selectAll"
                                class="form-check-input"
                            >
                        </th>

                        <th>#</th>
                        <th>Thumbnail</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Order #</th>
                        <th>Status</th>
                        <th>Files</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $order)
                        @php
                            /*
                            |--------------------------------------------------------------------------
                            | Files
                            |--------------------------------------------------------------------------
                            */

                            $mockup = $order->mockup_files ?? [];
                            $roster = $order->roster_files ?? [];
                            $quote  = $order->quote_files ?? [];

                            if (is_string($mockup)) {
                                $mockup = json_decode($mockup, true) ?: [];
                            }

                            if (is_string($roster)) {
                                $roster = json_decode($roster, true) ?: [];
                            }

                            if (is_string($quote)) {
                                $quote = json_decode($quote, true) ?: [];
                            }

                            $mockup = is_array($mockup) ? $mockup : [];
                            $roster = is_array($roster) ? $roster : [];
                            $quote  = is_array($quote) ? $quote : [];

                            $total = count($mockup) + count($roster) + count($quote);

                            /*
                            |--------------------------------------------------------------------------
                            | Order Name
                            |--------------------------------------------------------------------------
                            */

                            $orderName =
                                $order->order_name
                                ?? $order->product_name
                                ?? $order->design_name
                                ?? $order->model_name
                                ?? $order->title
                                ?? $order->name
                                ?? 'Custom Order';

                            /*
                            |--------------------------------------------------------------------------
                            | Order Thumbnail
                            |--------------------------------------------------------------------------
                            | First valid image from mockup files.
                            */

                            $thumbnailUrl = null;

                            foreach ($mockup as $mockupFile) {
                                $fileName = null;

                                if (is_array($mockupFile)) {
                                    $fileName =
                                        $mockupFile['filename']
                                        ?? $mockupFile['file_name']
                                        ?? $mockupFile['name']
                                        ?? $mockupFile['path']
                                        ?? null;
                                } elseif (is_object($mockupFile)) {
                                    $fileName =
                                        $mockupFile->filename
                                        ?? $mockupFile->file_name
                                        ?? $mockupFile->name
                                        ?? $mockupFile->path
                                        ?? null;
                                } else {
                                    $fileName = $mockupFile;
                                }

                                if (!$fileName) {
                                    continue;
                                }

                                $cleanFileName = strtok($fileName, '?');
                                $extension = strtolower(pathinfo($cleanFileName, PATHINFO_EXTENSION));

                                if (!in_array($extension, [
                                    'jpg',
                                    'jpeg',
                                    'png',
                                    'gif',
                                    'webp',
                                    'svg',
                                    'bmp'
                                ])) {
                                    continue;
                                }

                                if (
                                    str_starts_with($fileName, 'http://') ||
                                    str_starts_with($fileName, 'https://')
                                ) {
                                    $thumbnailUrl = $fileName;
                                } elseif (str_starts_with($fileName, '/uploads/')) {
                                    $thumbnailUrl = $fileName;
                                } elseif (str_starts_with($fileName, 'uploads/')) {
                                    $thumbnailUrl = '/' . $fileName;
                                } else {
                                    $thumbnailUrl =
                                        '/uploads/orders/mockup/' . basename($fileName);
                                }

                                break;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Status Color
                            |--------------------------------------------------------------------------
                            */

                            $statusColors = [
                                'pending'    => 'warning',
                                'processing' => 'info',
                                'completed'  => 'success',
                                'cancelled'  => 'danger',
                                'canceled'   => 'danger',
                                'shipped'    => 'primary'
                            ];

                            $color = $statusColors[strtolower($order->status ?? '')]
                                ?? 'secondary';
                        @endphp

                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    class="row-checkbox form-check-input"
                                    value="{{ $order->id }}"
                                >
                            </td>

                            <td>
                                <span class="order-id">
                                    {{ $order->id }}
                                </span>
                            </td>

                            <!-- Order Thumbnail -->
                            <td>
                                @if($thumbnailUrl)
                                    <a
                                        href="{{ $thumbnailUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="admin-order-thumbnail"
                                        title="Open full image"
                                    >
                                        <img
                                            src="{{ $thumbnailUrl }}"
                                            alt="{{ $orderName }}"
                                            loading="lazy"
                                            onerror="handleTableThumbnailError(this)"
                                        >
                                    </a>
                                @else
                                    <div class="admin-no-thumbnail">
                                        <i class="bi bi-image"></i>
                                        <span>No Image</span>
                                    </div>
                                @endif
                            </td>


                            <!-- Customer -->
                            <td>
                                <div class="customer-name">
                                    {{ $order->full_name ?? '-' }}
                                </div>
                            </td>

                            <td>
                                <span class="table-email">
                                    {{ $order->email ?? '-' }}
                                </span>
                            </td>

                            <td>
                                {{ $order->phone ?? '-' }}
                            </td>

                            <td>
                                <strong>
                                    {{ $order->order_number ?? '-' }}
                                </strong>
                            </td>

                            <td>
                                <span
                                    class="badge bg-{{ $color }} status-badge"
                                    id="status-badge-{{ $order->id }}"
                                >
                                    {{ ucfirst($order->status ?? 'pending') }}
                                </span>
                            </td>

                            <td>
                                @if($total > 0)
                                    <span class="badge bg-dark file-count-badge">
                                        {{ $total }}
                                        {{ $total === 1 ? 'file' : 'files' }}
                                    </span>
                                @else
                                    <span class="text-muted small">
                                        No files
                                    </span>
                                @endif
                            </td>

                            <td>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-dark view-order-btn"

                                    data-id="{{ $order->id }}"
                                    data-name="{{ $order->full_name ?? '' }}"
                                    data-order-name="{{ $orderName }}"
                                    data-thumbnail="{{ $thumbnailUrl ?? '' }}"
                                    data-email="{{ $order->email ?? '' }}"
                                    data-phone="{{ $order->phone ?? '' }}"
                                    data-order="{{ $order->order_number ?? '' }}"
                                    data-date="{{ $order->order_date ?? '' }}"
                                    data-delivery="{{ $order->delivery_date ?? '' }}"
                                    data-sales="{{ $order->sales_rep ?? '' }}"
                                    data-colors="{{ $order->team_colors ?? '' }}"
                                    data-status="{{ $order->status ?? 'pending' }}"

                                    data-notes="{{ base64_encode($order->notes ?? '') }}"
                                    data-mockup="{{ base64_encode(json_encode($mockup)) }}"
                                    data-roster="{{ base64_encode(json_encode($roster)) }}"
                                    data-quote="{{ base64_encode(json_encode($quote)) }}"
                                >
                                    <i class="bi bi-eye"></i>
                                    View
                                </button>
                                <button
    type="button"
    class="btn btn-sm btn-danger delete-order-btn"
    onclick="deleteOrder({{ $order->id }})">
    <i class="bi bi-trash"></i>
    Delete
</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <div class="empty-orders-state">
                                    <i class="bi bi-inbox"></i>
                                    <h6>No place orders found</h6>
                                    <p>Customer orders will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- =========================================================
     VIEW ORDER MODAL
========================================================= -->
<div
    class="modal fade"
    id="viewOrderModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content order-modal-content">

            <!-- Modal Header -->
            <div class="order-modal-black-header">
                <span class="order-modal-brand">
                    PROSIX SPORTS
                </span>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <div class="modal-body p-0">

                <!-- Welcome Header -->
                <div class="order-modal-welcome">
                    <h5>THANKS FOR CHOOSING US!</h5>

                    <p>
                        WE REALLY APPRECIATE & VALUE YOUR BUSINESS
                    </p>
                </div>

                <div class="order-modal-body">

                    <!-- Order Name & Thumbnail -->
                    <div class="modal-order-heading">
                        <div
                            class="modal-order-thumbnail"
                            id="modal-thumbnail-box"
                        >
                            <img
                                id="o-thumbnail"
                                src=""
                                alt="Order thumbnail"
                            >

                            <div
                                id="o-thumbnail-empty"
                                class="modal-thumbnail-empty"
                            >
                                <i class="bi bi-image"></i>
                                <span>No Image</span>
                            </div>
                        </div>

                        <div class="modal-order-title-info">
                            <span class="modal-order-small-label">
                                Order Name
                            </span>

                            <h3 id="o-order-name">
                                Custom Order
                            </h3>

                            <span id="o-order-number-preview">
                                Order #
                            </span>
                        </div>
                    </div>

                    <!-- Customer and Order Fields -->
                    <div class="order-details-grid">

                        <div class="po-field">
                            <label class="po-lbl">
                                Full Name
                            </label>

                            <div
                                class="po-val"
                                id="o-name"
                            ></div>
                        </div>

                        <div class="po-field">
                            <label class="po-lbl">
                                Email
                            </label>

                            <div
                                class="po-val"
                                id="o-email"
                            ></div>
                        </div>

                        <div class="po-field">
                            <label class="po-lbl">
                                Phone
                            </label>

                            <div
                                class="po-val"
                                id="o-phone"
                            ></div>
                        </div>

                        <div class="po-field">
                            <label class="po-lbl">
                                Order Place Date
                            </label>

                            <div
                                class="po-val"
                                id="o-date"
                            ></div>
                        </div>

                        <div class="po-field">
                            <label class="po-lbl">
                                Delivery Date
                            </label>

                            <div
                                class="po-val"
                                id="o-delivery"
                            ></div>
                        </div>

                        <div class="po-field">
                            <label class="po-lbl">
                                Sales Rep
                            </label>

                            <div
                                class="po-val"
                                id="o-sales"
                            ></div>
                        </div>

                        <div class="po-field">
                            <label class="po-lbl">
                                Order #
                            </label>

                            <div
                                class="po-val po-val-bold"
                                id="o-order"
                            ></div>
                        </div>

                    </div>

                    <!-- Team Colors -->
                    <div class="po-field team-colors-field">
                        <label class="po-lbl">
                            Team Colors
                        </label>

                        <div
                            class="po-val po-val-multiline"
                            id="o-colors"
                        ></div>
                    </div>

                    <div class="order-divider"></div>

                    <!-- Files and Notes -->
                    <div class="order-files-notes-grid">

                        <!-- Mockup -->
                        <div class="po-upload-card">
                            <div class="po-upload-title">
                                <i class="bi bi-paperclip"></i>
                                Final Mockup Files
                            </div>

                            <div
                                id="mockup-grid"
                                class="files-grid"
                            ></div>

                            <div
                                id="mockup-empty"
                                class="po-empty"
                            >
                                No files uploaded
                            </div>
                        </div>

                        <!-- Roster -->
                        <div class="po-upload-card">
                            <div class="po-upload-title">
                                <i class="bi bi-people"></i>
                                Team Roster Files
                            </div>

                            <div
                                id="roster-grid"
                                class="files-grid"
                            ></div>

                            <div
                                id="roster-empty"
                                class="po-empty"
                            >
                                No files uploaded
                            </div>
                        </div>

                        <!-- Quote -->
                        <div class="po-upload-card">
                            <div class="po-upload-title">
                                <i class="bi bi-file-earmark-text"></i>
                                Quote / Invoice
                            </div>

                            <div
                                id="quote-grid"
                                class="files-grid"
                            ></div>

                            <div
                                id="quote-empty"
                                class="po-empty"
                            >
                                No files uploaded
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="po-upload-card notes-card">
                            <div class="po-upload-title">
                                <i class="bi bi-journal-text"></i>
                                Notes
                            </div>

                            <div
                                id="o-notes"
                                class="order-notes-box"
                            ></div>
                        </div>

                    </div>

                    <div class="order-divider"></div>

                    <!-- Status Update -->
                    <div class="status-change-box">
                        <h6>Update Order Status</h6>

                        <div class="status-update-row">
                            <select
                                id="statusSelect"
                                class="form-select form-select-sm"
                            >
                                <option value="pending">
                                    Pending
                                </option>

                                <option value="processing">
                                    Processing
                                </option>

                                <option value="completed">
                                    Completed
                                </option>

                                <option value="cancelled">
                                    Cancelled
                                </option>
                            </select>

                            <button
                                id="updateStatusBtn"
                                type="button"
                                class="btn btn-dark btn-sm px-4"
                                onclick="updateStatus()"
                            >
                                Update Status
                            </button>

                            <span
                                id="statusMsg"
                                class="text-success small fw-bold"
                            >
                                <i class="bi bi-check-circle-fill"></i>
                                Status updated and email sent!
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer order-modal-footer">
                <a
                    id="downloadSinglePdf"
                    href="#"
                    class="btn btn-outline-dark btn-sm"
                    target="_blank"
                >
                    <i class="bi bi-file-pdf"></i>
                    Download PDF
                </a>

                <button
                    type="button"
                    class="btn btn-secondary btn-sm"
                    data-bs-dismiss="modal"
                >
                    Close
                </button>
            </div>

        </div>
    </div>
</div>


<style>
/* =========================================================
   PAGE
========================================================= */

.place-orders-card {
    border-radius: 12px;
    overflow: hidden;
}

.place-orders-table th {
    white-space: nowrap;
    font-size: 12px;
    font-weight: 700;
    vertical-align: middle;
}

.place-orders-table td {
    font-size: 15x;
    vertical-align: middle;
}

.order-id {
    font-weight: 700;
    color: #555;
}

.customer-name {
    font-weight: 650;
    color: #111;
}

.table-email {
    display: inline-block;
    max-width: 190px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.file-count-badge {
    font-size: 10px;
    padding: 6px 9px;
}

.status-badge {
    font-size: 10px;
    padding: 6px 9px;
    text-transform: capitalize;
}

/* =========================================================
   ADMIN TABLE THUMBNAIL
========================================================= */

.admin-order-thumbnail {
    display: block;
    width: 76px;
    height: 62px;
    overflow: hidden;
    border: 1px solid #dedede;
    border-radius: 8px;
    background: #f6f6f6;
    text-decoration: none;
}

.admin-order-thumbnail img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.2s ease;
}

.admin-order-thumbnail:hover img {
    transform: scale(1.08);
}

.admin-no-thumbnail {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 3px;
    width: 76px;
    height: 62px;
    border: 1px dashed #cfcfcf;
    border-radius: 8px;
    background: #fafafa;
    color: #999;
    font-size: 9px;
    font-weight: 700;
    text-align: center;
}

.admin-no-thumbnail i {
    font-size: 16px;
}

.admin-order-name {
    max-width: 220px;
    color: #111;
    font-size: 13px;
    font-weight: 750;
    line-height: 1.4;
    overflow-wrap: anywhere;
}

/* =========================================================
   EMPTY TABLE
========================================================= */

.empty-orders-state {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    min-height: 260px;
    text-align: center;
    color: #999;
}

.empty-orders-state i {
    margin-bottom: 10px;
    font-size: 38px;
}

.empty-orders-state h6 {
    margin: 0 0 5px;
    color: #444;
    font-weight: 700;
}

.empty-orders-state p {
    margin: 0;
    font-size: 12px;
}

/* =========================================================
   MODAL
========================================================= */

.order-modal-content {
    overflow: hidden;
    border: 0;
    border-radius: 14px;
}

.order-modal-black-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    background: #000;
}

.order-modal-brand {
    color: #fff;
    font-size: 15px;
    font-weight: 800;
    letter-spacing: 2px;
}

.order-modal-welcome {
    padding: 17px 28px;
    border-bottom: 1px solid #e5e5e5;
    background: #f8f8f8;
    text-align: center;
}

.order-modal-welcome h5 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    font-style: italic;
    letter-spacing: 1px;
}

.order-modal-welcome p {
    margin: 4px 0 0;
    color: #888;
    font-size: 11px;
    letter-spacing: 3px;
    text-transform: uppercase;
}

.order-modal-body {
    padding: 28px;
}

.order-modal-footer {
    border-top: 1px solid #e5e5e5;
}

/* =========================================================
   MODAL ORDER THUMBNAIL AND NAME
========================================================= */

.modal-order-heading {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
    padding: 16px;
    border: 1px solid #e2e2e2;
    border-radius: 11px;
    background: #fafafa;
}

.modal-order-thumbnail {
    position: relative;
    flex-shrink: 0;
    width: 145px;
    height: 115px;
    overflow: hidden;
    border: 1px solid #d8d8d8;
    border-radius: 10px;
    background: #fff;
}

.modal-order-thumbnail img {
    display: none;
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.modal-order-thumbnail img:hover {
    transform: scale(1.05);
}

.modal-thumbnail-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 5px;
    width: 100%;
    height: 100%;
    color: #aaa;
    font-size: 11px;
    font-weight: 700;
}

.modal-thumbnail-empty i {
    font-size: 25px;
}

.modal-order-title-info {
    min-width: 0;
}

.modal-order-small-label {
    display: block;
    margin-bottom: 5px;
    color: #888;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.modal-order-title-info h3 {
    margin: 0 0 6px;
    color: #111;
    font-size: 23px;
    font-weight: 800;
    overflow-wrap: anywhere;
}

#o-order-number-preview {
    color: #777;
    font-size: 12px;
    font-weight: 600;
}

/* =========================================================
   ORDER FIELDS
========================================================= */

.order-details-grid {
    display: grid;
    grid-template-columns:
        minmax(130px, 1fr)
        minmax(170px, 1.2fr)
        minmax(120px, 1fr)
        minmax(130px, 1fr)
        minmax(130px, 1fr)
        minmax(130px, 1fr)
        minmax(120px, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.po-field {
    display: flex;
    min-width: 0;
    flex-direction: column;
    gap: 5px;
}

.po-lbl {
    color: #888;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

.po-val {
    display: flex;
    align-items: center;
    min-height: 39px;
    overflow: hidden;
    padding: 7px 11px;
    border: 1px solid #d0d0d0;
    border-radius: 6px;
    background: #f5f5f5;
    color: #222;
    font-size: 12px;
    line-height: 1.4;
    text-overflow: ellipsis;
    overflow-wrap: anywhere;
}

.po-val-bold {
    font-weight: 700;
}

.po-val-multiline {
    align-items: flex-start;
    height: auto;
    min-height: 42px;
}

.team-colors-field {
    margin-bottom: 24px;
}

.order-divider {
    height: 1px;
    margin-bottom: 24px;
    background: #e5e5e5;
}

/* =========================================================
   FILES AND NOTES
========================================================= */

.order-files-notes-grid {
    display: grid;
    grid-template-columns:
        minmax(0, 1fr)
        minmax(0, 1fr)
        minmax(0, 1fr)
        minmax(0, 1.1fr);
    gap: 20px;
    margin-bottom: 24px;
}

.po-upload-card {
    min-width: 0;
    padding: 14px;
    border: 1px solid #d0d0d0;
    border-radius: 10px;
    background: #fff;
}

.po-upload-title {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 11px;
    padding-bottom: 9px;
    border-bottom: 1px solid #ececec;
    color: #000;
    font-size: 12px;
    font-weight: 700;
}

.po-empty {
    padding: 22px 0;
    color: #aaa;
    font-size: 11px;
    text-align: center;
}

.files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(88px, 1fr));
    gap: 9px;
}

.file-card {
    min-width: 0;
    overflow: hidden;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: #fff;
    transition:
        box-shadow 0.2s,
        transform 0.2s;
}

.file-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 14px rgba(0, 0, 0, 0.1);
}

.file-thumb {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 82px;
    overflow: hidden;
    border-bottom: 1px solid #eee;
    background: #fafafa;
}

.file-thumb img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.file-image-link {
    display: block;
    width: 100%;
    height: 100%;
}

.file-icon-box {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 5px;
    width: 100%;
    height: 100%;
}

.file-icon-emoji {
    font-size: 22px;
}

.ext-label {
    padding: 3px 7px;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 800;
}

.ext-pdf {
    background: #fff0f0;
    color: #d32f2f;
}

.ext-xls {
    background: #f0fff4;
    color: #2e7d32;
}

.ext-doc {
    background: #e8f0fe;
    color: #1565c0;
}

.ext-img,
.ext-other {
    background: #f1f1f1;
    color: #555;
}

.file-name-row {
    overflow: hidden;
    padding: 5px 6px;
    color: #333;
    font-size: 9px;
    font-weight: 600;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.file-actions {
    display: flex;
    gap: 4px;
    padding: 3px 5px 7px;
}

.file-actions a {
    flex: 1;
    padding: 4px;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 700;
    text-align: center;
    text-decoration: none;
}

.btn-view-file {
    background: #f0f0f0;
    color: #333;
}

.btn-view-file:hover {
    background: #ddd;
    color: #000;
}

.btn-dl-file {
    background: #000;
    color: #fff;
}

.btn-dl-file:hover {
    background: #333;
    color: #fff;
}

.notes-card {
    display: flex;
    flex-direction: column;
}

.order-notes-box {
    flex: 1;
    min-height: 180px;
    overflow-y: auto;
    padding: 11px 12px;
    border: 1px dashed #ccc;
    border-radius: 6px;
    background: #fafafa;
    color: #333;
    font-size: 12px;
    line-height: 1.7;
    overflow-wrap: anywhere;
}

/* =========================================================
   STATUS
========================================================= */

.status-change-box {
    padding: 17px 20px;
    border: 1px solid #e5e5e5;
    border-radius: 10px;
    background: #f8f8f8;
}

.status-change-box h6 {
    margin: 0 0 12px;
    font-size: 13px;
    font-weight: 700;
}

.status-update-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

#statusSelect {
    max-width: 210px;
}

#statusMsg {
    display: none;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1200px) {
    .order-details-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .order-files-notes-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767px) {
    .order-modal-body {
        padding: 18px;
    }

    .order-modal-welcome {
        padding: 15px;
    }

    .order-modal-welcome h5 {
        font-size: 16px;
    }

    .order-modal-welcome p {
        font-size: 8px;
        letter-spacing: 2px;
    }

    .modal-order-heading {
        align-items: flex-start;
        gap: 13px;
        padding: 12px;
    }

    .modal-order-thumbnail {
        width: 100px;
        height: 82px;
    }

    .modal-order-title-info h3 {
        font-size: 17px;
    }

    .order-details-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .order-files-notes-grid {
        grid-template-columns: 1fr;
    }

    .status-update-row {
        align-items: stretch;
        flex-direction: column;
    }

    #statusSelect {
        width: 100%;
        max-width: none;
    }

    .status-update-row .btn {
        width: 100%;
    }
}

@media (max-width: 420px) {
    .order-details-grid {
        grid-template-columns: 1fr;
    }

    .modal-order-heading {
        flex-direction: column;
    }

    .modal-order-thumbnail {
        width: 100%;
        height: 170px;
    }
}
</style>


<script>
let currentOrderId = null;

/*
|--------------------------------------------------------------------------
| Page Ready
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const downloadPdfBtn = document.getElementById('downloadPdfBtn');

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document
                .querySelectorAll('.row-checkbox')
                .forEach(function (checkbox) {
                    checkbox.checked = selectAll.checked;
                });
        });
    }

    document
        .querySelectorAll('.view-order-btn')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                openOrderModal(this.dataset);
            });
        });

    if (downloadPdfBtn) {
        downloadPdfBtn.addEventListener('click', function () {
            downloadSelectedOrders();
        });
    }
});

/*
|--------------------------------------------------------------------------
| Open Order Modal
|--------------------------------------------------------------------------
*/

function openOrderModal(data) {
    currentOrderId = data.id;

    setText('o-name', data.name || '-');
    setText('o-email', data.email || '-');
    setText('o-phone', data.phone || '-');
    setText('o-order', data.order || '-');
    setText('o-date', data.date || '-');
    setText('o-delivery', data.delivery || '-');
    setText('o-sales', data.sales || '-');
    setText('o-colors', data.colors || '-');

    setText(
        'o-order-name',
        data.orderName || 'Custom Order'
    );

    setText(
        'o-order-number-preview',
        data.order
            ? 'Order #' + data.order
            : 'Order # -'
    );

    const notes = decodeBase64(data.notes);

    document.getElementById('o-notes').innerHTML =
        notes || '<span class="text-muted">No notes added.</span>';

    const statusSelect =
        document.getElementById('statusSelect');

    if (statusSelect) {
        statusSelect.value = data.status || 'pending';
    }

    const statusMsg =
        document.getElementById('statusMsg');

    if (statusMsg) {
        statusMsg.style.display = 'none';
    }

    const downloadSinglePdf =
        document.getElementById('downloadSinglePdf');

    if (downloadSinglePdf) {
        downloadSinglePdf.href =
            '/order/download/' + data.id;
    }

    setModalThumbnail(
        data.thumbnail || '',
        data.orderName || 'Order'
    );

    renderFiles(
        'mockup',
        parseBase64Json(data.mockup),
        'orders/mockup'
    );

    renderFiles(
        'roster',
        parseBase64Json(data.roster),
        'orders/roster'
    );

    renderFiles(
        'quote',
        parseBase64Json(data.quote),
        'orders/quote'
    );

    const modalElement =
        document.getElementById('viewOrderModal');

    const modal =
        bootstrap.Modal.getOrCreateInstance(modalElement);

    modal.show();
}

/*
|--------------------------------------------------------------------------
| Set Text
|--------------------------------------------------------------------------
*/

function setText(id, value) {
    const element = document.getElementById(id);

    if (element) {
        element.innerText = value;
    }
}

/*
|--------------------------------------------------------------------------
| Base64 Helpers
|--------------------------------------------------------------------------
*/

function decodeBase64(value) {
    if (!value) {
        return '';
    }

    try {
        const binary = atob(value);

        const bytes = Uint8Array.from(
            binary,
            function (character) {
                return character.charCodeAt(0);
            }
        );

        return new TextDecoder('utf-8').decode(bytes);
    } catch (error) {
        console.error('Base64 decode error:', error);
        return '';
    }
}

function parseBase64Json(value) {
    if (!value) {
        return [];
    }

    try {
        const decoded = decodeBase64(value);
        const parsed = JSON.parse(decoded);

        return Array.isArray(parsed)
            ? parsed
            : [];
    } catch (error) {
        console.error('File JSON parse error:', error);
        return [];
    }
}

/*
|--------------------------------------------------------------------------
| Modal Thumbnail
|--------------------------------------------------------------------------
*/

function setModalThumbnail(url, orderName) {
    const image =
        document.getElementById('o-thumbnail');

    const empty =
        document.getElementById('o-thumbnail-empty');

    if (!image || !empty) {
        return;
    }

    image.onclick = null;
    image.onerror = null;

    if (!url) {
        image.removeAttribute('src');
        image.style.display = 'none';
        empty.style.display = 'flex';
        return;
    }

    image.src = url;
    image.alt = orderName || 'Order thumbnail';
    image.style.display = 'block';
    empty.style.display = 'none';

    image.onclick = function () {
        window.open(
            url,
            '_blank',
            'noopener,noreferrer'
        );
    };

    image.onerror = function () {
        image.style.display = 'none';
        empty.style.display = 'flex';
    };
}

/*
|--------------------------------------------------------------------------
| Table Thumbnail Error
|--------------------------------------------------------------------------
*/

function handleTableThumbnailError(image) {
    const link = image.closest(
        '.admin-order-thumbnail'
    );

    if (!link) {
        return;
    }

    link.outerHTML = `
        <div class="admin-no-thumbnail">
            <i class="bi bi-image"></i>
            <span>No Image</span>
        </div>
    `;
}

/*
|--------------------------------------------------------------------------
| File Helpers
|--------------------------------------------------------------------------
*/

function getFileInformation(file) {
    if (!file) {
        return {
            filename: '',
            original: 'File',
            extension: ''
        };
    }

    if (typeof file === 'object') {
        const filename =
            file.filename ||
            file.file_name ||
            file.name ||
            file.path ||
            '';

        const original =
            file.original ||
            file.original_name ||
            file.name ||
            filename ||
            'File';

        const extension =
            String(
                file.ext ||
                getExtension(filename || original)
            )
            .replace('.', '')
            .toLowerCase();

        return {
            filename,
            original,
            extension
        };
    }

    const filename = String(file);

    return {
        filename,
        original:
            filename.split('/').pop() || 'File',
        extension: getExtension(filename)
    };
}

function getExtension(filename) {
    if (!filename || !filename.includes('.')) {
        return '';
    }

    return filename
        .split('?')[0]
        .split('.')
        .pop()
        .toLowerCase();
}

function buildFileUrl(filename, folder) {
    if (!filename) {
        return '#';
    }

    if (
        filename.startsWith('http://') ||
        filename.startsWith('https://')
    ) {
        return filename;
    }

    if (filename.startsWith('/uploads/')) {
        return filename;
    }

    if (filename.startsWith('uploads/')) {
        return '/' + filename;
    }

    const cleanFilename =
        filename.split('/').pop();

    return (
        '/uploads/' +
        folder +
        '/' +
        encodeURIComponent(cleanFilename)
    );
}

function isImageExtension(extension) {
    return [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp',
        'svg',
        'bmp'
    ].includes(extension);
}

function escapeHtml(value) {
    return String(value || '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function shortFileName(name) {
    if (!name) {
        return 'File';
    }

    if (name.length <= 18) {
        return name;
    }

    const extension = name.includes('.')
        ? '.' + name.split('.').pop()
        : '';

    const withoutExtension = extension
        ? name.slice(0, -extension.length)
        : name;

    return (
        withoutExtension.substring(0, 11) +
        '...' +
        extension
    );
}

/*
|--------------------------------------------------------------------------
| Render Files
|--------------------------------------------------------------------------
*/

function renderFiles(type, files, folder) {
    const grid =
        document.getElementById(type + '-grid');

    const emptyMessage =
        document.getElementById(type + '-empty');

    if (!grid || !emptyMessage) {
        return;
    }

    grid.innerHTML = '';

    if (!Array.isArray(files) || files.length === 0) {
        emptyMessage.style.display = 'block';
        grid.style.display = 'none';
        return;
    }

    emptyMessage.style.display = 'none';
    grid.style.display = 'grid';

    files.forEach(function (file) {
        const info = getFileInformation(file);

        if (!info.filename) {
            return;
        }

        const url =
            buildFileUrl(info.filename, folder);

        const safeUrl =
            escapeHtml(url);

        const safeOriginal =
            escapeHtml(info.original);

        const displayName =
            escapeHtml(shortFileName(info.original));

        const imageFile =
            isImageExtension(info.extension);

        const thumbnailHtml = imageFile
            ? `
                <a
                    href="${safeUrl}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="file-image-link"
                    title="Open full image"
                >
                    <img
                        src="${safeUrl}"
                        alt="${safeOriginal}"
                        loading="lazy"
                        onerror="replaceBrokenFileImage(this)"
                    >
                </a>
            `
            : getIconHtml(info.extension);

        const card = document.createElement('div');

        card.className = 'file-card';

        card.innerHTML = `
            <div class="file-thumb">
                ${thumbnailHtml}
            </div>

            <div
                class="file-name-row"
                title="${safeOriginal}"
            >
                ${displayName}
            </div>

            <div class="file-actions">
                <a
                    href="${safeUrl}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn-view-file"
                >
                    View
                </a>

                <a
                    href="${safeUrl}"
                    download="${safeOriginal}"
                    class="btn-dl-file"
                >
                    ↓ DL
                </a>
            </div>
        `;

        grid.appendChild(card);
    });
}

/*
|--------------------------------------------------------------------------
| Broken File Image
|--------------------------------------------------------------------------
*/

function replaceBrokenFileImage(image) {
    const container =
        image.closest('.file-thumb');

    if (container) {
        container.innerHTML =
            getIconHtml('img');
    }
}

/*
|--------------------------------------------------------------------------
| File Icon
|--------------------------------------------------------------------------
*/

function getIconHtml(extension) {
    const icons = {
        pdf: {
            label: 'PDF',
            className: 'ext-pdf',
            icon: '📕'
        },

        xls: {
            label: 'XLS',
            className: 'ext-xls',
            icon: '📗'
        },

        xlsx: {
            label: 'XLSX',
            className: 'ext-xls',
            icon: '📗'
        },

        csv: {
            label: 'CSV',
            className: 'ext-xls',
            icon: '📗'
        },

        doc: {
            label: 'DOC',
            className: 'ext-doc',
            icon: '📘'
        },

        docx: {
            label: 'DOCX',
            className: 'ext-doc',
            icon: '📘'
        },

        img: {
            label: 'IMG',
            className: 'ext-img',
            icon: '🖼️'
        }
    };

    const fallbackLabel =
        extension
            ? extension.toUpperCase().slice(0, 5)
            : 'FILE';

    const item =
        icons[extension] || {
            label: fallbackLabel,
            className: 'ext-other',
            icon: '📄'
        };

    return `
        <div class="file-icon-box">
            <div class="file-icon-emoji">
                ${item.icon}
            </div>

            <span class="ext-label ${item.className}">
                ${item.label}
            </span>
        </div>
    `;
}

/*
|--------------------------------------------------------------------------
| Download Selected Orders
|--------------------------------------------------------------------------
*/

function downloadSelectedOrders() {
    const selected = Array.from(
        document.querySelectorAll('.row-checkbox')
    )
    .filter(function (checkbox) {
        return checkbox.checked;
    })
    .map(function (checkbox) {
        return checkbox.value;
    });

    if (!selected.length) {
        alert('Select at least one order.');
        return;
    }

    const form =
        document.createElement('form');

    form.method = 'POST';
    form.action = '/admin/place-orders-download';
    form.style.display = 'none';

    const csrfInput =
        document.createElement('input');

    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';

    const idsInput =
        document.createElement('input');

    idsInput.type = 'hidden';
    idsInput.name = 'ids';
    idsInput.value = selected.join(',');

    form.appendChild(csrfInput);
    form.appendChild(idsInput);

    document.body.appendChild(form);
    form.submit();
}

/*
|--------------------------------------------------------------------------
| Update Order Status
|--------------------------------------------------------------------------
*/

function updateStatus() {
    if (!currentOrderId) {
        return;
    }

    const statusSelect =
        document.getElementById('statusSelect');

    const button =
        document.getElementById('updateStatusBtn');

    const statusMessage =
        document.getElementById('statusMsg');

    const status =
        statusSelect.value;

    button.disabled = true;
    button.innerHTML =
        '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

    if (statusMessage) {
        statusMessage.style.display = 'none';
    }

    fetch(
        '/api/place-order/' +
        currentOrderId +
        '/status',
        {
            method: 'POST',

            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },

            body: JSON.stringify({
                status: status
            })
        }
    )
    .then(function (response) {
        if (!response.ok) {
            throw new Error(
                'Status update request failed.'
            );
        }

        return response.json();
    })
    .then(function (data) {
        if (!data.success) {
            throw new Error(
                data.message ||
                'Failed to update status.'
            );
        }

        const badge =
            document.getElementById(
                'status-badge-' +
                currentOrderId
            );

        const colorMap = {
            pending: 'warning',
            processing: 'info',
            completed: 'success',
            cancelled: 'danger',
            canceled: 'danger',
            shipped: 'primary'
        };

        if (badge) {
            badge.className =
                'badge bg-' +
                (colorMap[status] || 'secondary') +
                ' status-badge';

            badge.innerText =
                status.charAt(0).toUpperCase() +
                status.slice(1);
        }

        if (statusMessage) {
            statusMessage.style.display =
                'inline-flex';
        }
    })
    .catch(function (error) {
        console.error(error);

        alert(
            error.message ||
            'Server error. Please try again.'
        );
    })
    .finally(function () {
        button.disabled = false;

        button.innerHTML =
            'Update Status';
    });
    function deleteOrder(id)
{
    if (!confirm('Are you sure you want to delete this order?')) {
        return;
    }

    fetch('/admin/place-orders/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {

        if (!data.success) {
            throw new Error(data.message || 'Delete failed.');
        }

        alert(data.message);

        location.reload();

    })
    .catch(error => {
        alert(error.message);
    });
}
}
</script>


<script>
/*
|--------------------------------------------------------------------------
| Mark Place Orders as Read
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {
        if (
            window.location.pathname !==
            '/admin/place-orders'
        ) {
            return;
        }

        setTimeout(async function () {
            try {
                await fetch(
                    '/admin/place-orders-mark-read',
                    {
                        method: 'POST',

                        headers: {
                            'X-CSRF-TOKEN':
                                '{{ csrf_token() }}',

                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'application/json'
                        }
                    }
                );

                const badge =
                    document.getElementById(
                        'placeOrderBadge'
                    );

                if (badge) {
                    badge.style.display = 'none';
                }
            } catch (error) {
                console.error(
                    'Unable to mark place orders as read:',
                    error
                );
            }
        }, 1000);
    }
);
</script>

@endsection
