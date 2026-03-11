@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>All Colors</h2>
        <a href="{{ route('colors.create') }}" class="btn btn-dark">
            <i class="bi bi-plus-lg"></i> Add Color
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div id="success-msg" class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @forelse($colors as $color)
<div class="col-md-2 mb-4">
                    <div class="card text-center shadow-sm" style="position: relative; overflow: hidden;">

                    {{-- Color Block --}}
                    <div style="width: 100%; height: 100px; background-color: {{ $color->code }};"></div>

                    {{-- Name & Code --}}
                    <div class="card-body p-2 d-flex justify-content-between">
                        <span class="fw-bold">{{ $color->name }}</span>
                        <span class="text-muted">{{ strtoupper($color->code) }}</span>
                    </div>

                    {{-- Edit & Delete Buttons --}}
                    <div class="card-footer d-flex justify-content-between bg-transparent border-top-0 p-2">
                        <!-- Edit -->
                        <a href="{{ route('colors.edit', $color->id) }}"
                           class="btn btn-sm d-flex align-items-center justify-content-center w-45 p-1 edit-btn"
                           title="Edit">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>

                        <!-- Delete -->
                        <form action="{{ route('colors.destroy', $color->id) }}" method="POST" class="w-45">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm d-flex align-items-center justify-content-center w-100 p-1 delete-btn"
                                    title="Delete">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center">No colors added yet. Click "Add Color" to start.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Auto-hide Success Message after 2 seconds --}}
<script>
document.addEventListener('DOMContentLoaded', function(){
    const msg = document.getElementById('success-msg');
    if(msg){
        setTimeout(() => {
            msg.style.display = 'none';
        }, 2000); // 2 seconds
    }
});
</script>

{{-- Button color based on background --}}
<style>
/* Base styles for buttons */
.card-footer .edit-btn,
.card-footer .delete-btn {
    border: 1px solid;
    font-weight: 500;

}

/* Detect dark/light mode using prefers-color-scheme */
@media (prefers-color-scheme: dark) {
    .card-footer .edit-btn,
    .card-footer .delete-btn {
        color: rgb(0, 0, 0);
        border-color: rgb(0, 0, 0);
        background: transparent;
    }
}

@media (prefers-color-scheme: light) {
    .card-footer .edit-btn,
    .card-footer .delete-btn {
        color: rgb(140, 133, 133);
        border-color:  rgb(140, 133, 133);
        background: transparent;
    }
}

/* Small width tweaks */
.w-45 { width: 45%; }

/* Icon spacing */
.me-1 { margin-right: 4px; }
</style>
@endsection
