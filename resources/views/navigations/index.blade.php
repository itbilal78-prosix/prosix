@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">All Navigations</h2>

        <a href="{{ route('navigations.create') }}"
           class="btn btn-dark d-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i> Add Navigation
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div id="success-msg" class="alert alert-dark border border-dark">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        @forelse($navigations as $nav)

            <div class="col-md-4 mb-4">
                <div class="card border-dark shadow-sm h-100">

                    {{-- Card Header --}}
                    <div class="card-header bg-white border-bottom border-dark
                                d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">{{ $nav->title }}</span>

                        <span class="badge {{ $nav->status ? 'bg-dark' : 'bg-secondary' }}">
                            {{ $nav->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body text-dark">
                        <p class="mb-1">
                            <strong>Route:</strong> {{ $nav->route ?? '-' }}
                        </p>
                        <p class="mb-1">
                            <strong>Dropdown:</strong> {{ $nav->has_dropdown ? 'Yes' : 'No' }}
                        </p>
                        <p class="mb-0">
                            <strong>Position:</strong> {{ $nav->position }}
                        </p>
                    </div>

                    {{-- Action Buttons --}}
                 <div class="card-footer bg-white border-top-0 p-2">
    <div class="d-flex justify-content-between gap-2">

        {{-- Edit --}}
        <a href="{{ route('navigations.edit', $nav->id) }}"
           class="btn btn-sm btn-dark w-50 d-flex justify-content-center align-items-center">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>

        {{-- Delete --}}
        <form action="{{ route('navigations.destroy', $nav->id) }}"
              method="POST" class="w-50">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="btn btn-sm btn-dark w-100 d-flex justify-content-center align-items-center"
                    onclick="return confirm('Are you sure?')">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </form>

    </div>
</div>


                    {{-- Toggle Status --}}
                    <div class="card-footer bg-white border-top-0 p-2">
                        <form action="{{ route('navigations.toggle-status', $nav->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="btn btn-sm btn-dark w-100">
                                {{ $nav->status ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        @empty
            <div class="col-12">
                <p class="text-center text-muted">
                    No navigations added yet. Click "Add Navigation" to start.
                </p>
            </div>
        @endforelse
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const msg = document.getElementById('success-msg');
    if (msg) {
        setTimeout(() => {
            msg.style.display = 'none';
        }, 2000);
    }
});
</script>

@endsection

