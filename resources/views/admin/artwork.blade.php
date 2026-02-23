@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Artwork Requests</h4>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr> 
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
                        @php
                            $images = $req->artwork_file ? json_decode($req->artwork_file) : [];
                        @endphp
                        <tr>
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
                                    <span class="badge bg-dark">{{ count($images) }} image(s)</span>
                                    <br>
                                    <img src="{{ asset('uploads/artwork/'.$images[0]) }}" 
                                         style="width:50px; height:50px; object-fit:cover; border-radius:4px; margin-top:4px;">
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>{{ $req->created_at->format('d M Y') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-dark" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#viewArtworkModal{{ $req->id }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="viewArtworkModal{{ $req->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-dark text-white">
                                        <h5 class="modal-title">Artwork Request #{{ $req->id }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6"><strong>Name:</strong> {{ $req->full_name }}</div>
                                            <div class="col-md-6"><strong>Email:</strong> <a href="mailto:{{ $req->email }}">{{ $req->email }}</a></div>
                                            <div class="col-md-6"><strong>Team:</strong> {{ $req->team_name ?? 'N/A' }}</div>
                                            <div class="col-md-6"><strong>Qty:</strong> {{ $req->quantity }}</div>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <strong>Products:</strong>
                                            @foreach($req->products as $p)
                                                <span class="badge bg-dark me-1 mb-1">{{ $p }}</span>
                                            @endforeach
                                        </div>

                                        <!-- Images Section -->
                                        <div class="col-12">
                                            <strong>Uploaded Images:</strong>
                                            @if(count($images))
                                                <div class="row mt-2 g-3">
                                                    @foreach($images as $img)
                                                        <div class="col-md-3 col-sm-4 col-6">
                                                            <div class="border rounded overflow-hidden position-relative" style="height:120px;">
                                                                
                                                                <a href="{{ asset('uploads/artwork/'.$img) }}" target="_blank">
                                                                    <img src="{{ asset('uploads/artwork/'.$img) }}" 
                                                                         class="fly-source-img"
                                                                         style="width:100%; height:100%; object-fit:cover;">
                                                                </a>

                                                                <a href="{{ asset('uploads/artwork/'.$img) }}" 
                                                                   class="download-icon"
                                                                   title="Download">
                                                                    <i class="bi bi-download"></i>
                                                                </a>

                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted mt-1">No images uploaded.</p>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No Artwork Requests Found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $requests->links() }}
        </div>
    </div>
</div>

<!-- Styles -->
<style>
.download-icon {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(0,0,0,0.65);
    color: white;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    z-index: 10;
    transition: all 0.25s;
}

.download-icon:hover {
    background: rgba(0,0,0,0.9);
    transform: scale(1.15);
    color: #0d6efd;
}

.fly-clone {
    position: fixed !important;
    z-index: 9999;
    pointer-events: none;
    will-change: transform, opacity, left, top;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.4);
}

/* Circular path animation */
@keyframes circularFly {
    0% {
        transform: translate(0, 0) scale(1);
        opacity: 1;
    }
    25% {
        transform: translate(120px, -80px) scale(0.85);
        opacity: 0.9;
    }
    50% {
        transform: translate(180px, -160px) scale(0.65);
        opacity: 0.7;
    }
    75% {
        transform: translate(80px, -220px) scale(0.45);
        opacity: 0.4;
    }
    100% {
        transform: translate(0, -280px) scale(0.25);
        opacity: 0;
    }
}
</style>

<!-- JavaScript for Circular Fly -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.download-icon').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const imgSrc = this.getAttribute('href');
            const imgElement = this.previousElementSibling.querySelector('img'); // <img> inside the <a>

            if (!imgElement || !imgSrc) return;

            const rect = imgElement.getBoundingClientRect();

            const clone = imgElement.cloneNode(true);
            clone.classList.add('fly-clone');

            // Starting position (absolute on page)
            clone.style.position = 'fixed';
            clone.style.left = (rect.left + window.scrollX) + 'px';
            clone.style.top  = (rect.top  + window.scrollY) + 'px';
            clone.style.width  = rect.width  + 'px';
            clone.style.height = rect.height + 'px';

            document.body.appendChild(clone);

            // Small delay for smooth start
            setTimeout(() => {
                // Calculate target (top-right)
                const targetX = window.innerWidth - 100;
                const targetY = 40;

                // Distance to move
                const deltaX = targetX - rect.left;
                const deltaY = targetY - rect.top;

                // Apply circular animation with dynamic values
                clone.style.transition = 'all 1.1s cubic-bezier(0.22, 0.61, 0.36, 1)';
                clone.style.transform = `translate(${deltaX}px, ${deltaY}px) scale(0.25)`;
                clone.style.opacity = '0';

                // Optional: add slight rotation for fun
                // clone.style.transform += ' rotate(360deg)';

            }, 20);

            // Cleanup + real download
            setTimeout(() => {
                clone.remove();

                const realLink = document.createElement('a');
                realLink.href = imgSrc;
                realLink.download = '';
                document.body.appendChild(realLink);
                realLink.click();
                realLink.remove();
            }, 1200); // 1.1s animation + buffer
        });
    });
});
</script>
@endsection