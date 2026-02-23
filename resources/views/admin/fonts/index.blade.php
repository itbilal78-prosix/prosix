@extends('layouts.dashboard')
@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>All Fonts</h2>
        <a href="{{ route('fonts.create') }}" class="btn btn-dark">Add Font</a>
    </div>

    @if(session('success'))
        <div id="success-msg" class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Load fonts for preview --}}
    @foreach($fonts as $font)
        @if($font->file)
            <style>
                @font-face {
                    font-family: '{{ pathinfo($font->file, PATHINFO_FILENAME) }}';
                    src: url('{{ asset('storage/' . $font->file) }}') format('truetype');
                }
            </style>
        @endif
    @endforeach

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
        @forelse($fonts as $font)
            <div class="col">
                <div class="card shadow-sm text-center p-2" style="height: 120px;">
                    
                    {{-- Font preview --}}
                    @if($font->file)
                        <p style="font-family: '{{ pathinfo($font->file, PATHINFO_FILENAME) }}', sans-serif; font-size: 20px; margin: 0;">
                            ABC
                        </p>
                    @else
                        <p style="margin: 0;">N/A</p>
                    @endif

                    {{-- Font name --}}
                    <small class="d-block mt-2">{{ $font->name }}</small>

                    {{-- Actions --}}
                    <div class="mt-2 d-flex justify-content-between">
                        {{-- Edit --}}
                        <a href="{{ route('fonts.edit', $font->id) }}" 
                           class="btn btn-sm w-45 font-btn" title="Edit">
                            Edit
                        </a>

                        {{-- Delete --}}
                        <form action="{{ route('fonts.destroy', $font->id) }}" method="POST" class="w-45">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm w-100 font-btn" 
                                    onclick="return confirm('Delete this font?')">
                                Delete
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        @empty
            <p>No fonts added yet.</p>
        @endforelse
    </div>

    {{-- Pagination --}}
    {{ $fonts->links() }}
</div>

{{-- Auto-hide success message --}}
<script>
document.addEventListener('DOMContentLoaded', function(){
    const msg = document.getElementById('success-msg');
    if(msg){
        setTimeout(() => { msg.style.display = 'none'; }, 2000);
    }
});
</script>

{{-- Button styles for light/dark mode --}}
<style>
.font-btn {
    border: 1px solid;
    font-weight: 500;
    font-size: 0.85rem;
    border-radius: 4px;
    transition: all 0.2s;
    background: transparent;
}

/* Light mode: black text */
@media (prefers-color-scheme: light) {
    .font-btn {
        color:  rgb(140, 133, 133);
        border-color: rgb(140, 133, 133);
    }

    .font-btn:hover {
        background-color: #000;
        color: #fff;
    }
}

/* Dark mode: white text */
@media (prefers-color-scheme: dark) {
    .font-btn {
        color: #fff;
        border-color: #fff;
    }

    .font-btn:hover {
        background-color: #fff;
        color:  rgb(140, 133, 133);
    }
}

/* Width tweaks */
.w-45 { width: 45%; }
</style>
@endsection
