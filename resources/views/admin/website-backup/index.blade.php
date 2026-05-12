@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3><i class="bi bi-shield-check me-2"></i>Website Backup</h3>
            <p class="text-muted mb-0">Full website backup — Database + Files</p>
        </div>
        <form action="{{ route('admin.website-backup.create') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary" id="createBackupBtn"
                onclick="return confirm('Create a new backup now? This may take a few minutes.')">
                <i class="bi bi-plus-circle me-1"></i> Create Backup Now
            </button>
        </form>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-x-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Info Cards --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-database fs-2 text-primary"></i>
                        <div>
                            <div class="fw-bold">Database</div>
                            <div class="text-muted small">prosix — Full MySQL dump</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-images fs-2 text-success"></i>
                        <div>
                            <div class="fw-bold">Files & Images</div>
                            <div class="text-muted small">Storage + Uploads folder</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-clock-history fs-2 text-warning"></i>
                        <div>
                            <div class="fw-bold">Auto Backup</div>
                            <div class="text-muted small">Every week — kept for 3 months</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Restore Section --}}
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning bg-opacity-10">
            <i class="bi bi-arrow-counterclockwise me-1"></i>
            <strong>Restore from Backup</strong>
            <span class="text-muted small ms-2">— Upload a ZIP from your PC to restore the website</span>
        </div>
        <div class="card-body">

            {{-- Database Only Restore --}}
            <div class="mb-4">
                <h6 class="fw-bold text-primary"><i class="bi bi-database me-1"></i> Restore Database Only <span class="badge bg-success ms-1">Fast — recommended</span></h6>
                <p class="text-muted small mb-2">Restores only the database (products, orders, users etc). Files stay as-is on server. Takes ~30 seconds.</p>
                <form action="{{ route('admin.website-backup.restore-db') }}" method="POST"
                      enctype="multipart/form-data" id="restoreDbForm">
                    @csrf
                    <div class="d-flex align-items-end gap-3 flex-wrap">
                        <div>
                            <input type="file" class="form-control" name="backup_zip" accept=".zip" required>
                            <div class="form-text">Select the backup ZIP file from your PC</div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary" id="restoreDbBtn"
                                onclick="return confirm('WARNING: This will replace the entire database. Are you sure?')">
                                <i class="bi bi-database me-1"></i> Restore Database
                            </button>
                        </div>
                    </div>
                    <div id="restoreDbProgress" class="d-none mt-2">
                        <div class="d-flex align-items-center gap-2 text-primary">
                            <div class="spinner-border spinner-border-sm"></div>
                            <span>Restoring database — please wait...</span>
                        </div>
                    </div>
                </form>
            </div>

            <hr>

            {{-- Full Restore --}}
            <div>
                <h6 class="fw-bold text-warning"><i class="bi bi-arrow-counterclockwise me-1"></i> Full Restore (Database + Files) <span class="badge bg-warning text-dark ms-1">Slow — large files</span></h6>
                <p class="text-muted small mb-2">Restores everything — database + all images and files. Use only when server files are lost. May take several minutes.</p>
                <form action="{{ route('admin.website-backup.restore') }}" method="POST"
                      enctype="multipart/form-data" id="restoreFullForm">
                    @csrf
                    <div class="d-flex align-items-end gap-3 flex-wrap">
                        <div>
                            <input type="file" class="form-control" name="backup_zip" accept=".zip" required>
                            <div class="form-text">Select the backup ZIP file from your PC</div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-warning" id="restoreFullBtn"
                                onclick="return confirm('WARNING: This will replace ALL database and files. Are you sure?')">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Full Restore
                            </button>
                        </div>
                    </div>
                    <div id="restoreFullProgress" class="d-none mt-2">
                        <div class="d-flex align-items-center gap-2 text-warning">
                            <div class="spinner-border spinner-border-sm"></div>
                            <span>Full restore in progress — do NOT reload the page...</span>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- Backups List --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-archive me-1"></i> Available Backups (On Server)</span>
            <span class="badge bg-secondary">{{ count($backups) }} backups</span>
        </div>
        <div class="card-body">
            @if(count($backups) === 0)
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No backups found.</p>
                    <p class="text-muted small">Click "Create Backup Now" to create one.</p>
                </div>
            @else
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Backup File</th>
                            <th>Size</th>
                            <th>Created</th>
                            <th width="220">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                            <tr>
                                <td>
                                    <i class="bi bi-file-zip text-warning me-2"></i>
                                    {{ $backup['name'] }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $backup['size'] }}</span>
                                </td>
                                <td>{{ $backup['created_at'] }}</td>
                                <td>
                                    <a href="{{ route('admin.website-backup.download', ['file' => $backup['name']]) }}"
                                       class="btn btn-success btn-sm">
                                        <i class="bi bi-download me-1"></i> Save to PC
                                    </a>
                                    <form action="{{ route('admin.website-backup.delete') }}"
                                          method="POST" style="display:inline-block;"
                                          onsubmit="return confirm('Delete this backup?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="file" value="{{ $backup['name'] }}">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="alert alert-info mt-3">
        <i class="bi bi-info-circle me-1"></i>
        <strong>Note:</strong> The backup contains the full database and all files.
        Save it to your PC — if the server crashes or data is lost, you can restore the entire website from this backup.
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Create backup spinner
    var createForm = document.querySelector('form[action*="create"]');
    if (createForm) {
        createForm.addEventListener('submit', function () {
            var btn = document.getElementById('createBackupBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating...';
            }
        });
    }

    // DB restore spinner
    var restoreDbForm = document.getElementById('restoreDbForm');
    if (restoreDbForm) {
        restoreDbForm.addEventListener('submit', function () {
            document.getElementById('restoreDbProgress').classList.remove('d-none');
            var btn = document.getElementById('restoreDbBtn');
            if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Restoring...'; }
        });
    }

    // Full restore spinner
    var restoreFullForm = document.getElementById('restoreFullForm');
    if (restoreFullForm) {
        restoreFullForm.addEventListener('submit', function () {
            document.getElementById('restoreFullProgress').classList.remove('d-none');
            var btn = document.getElementById('restoreFullBtn');
            if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Restoring...'; }
        });
    }

});
</script>

@endsection
