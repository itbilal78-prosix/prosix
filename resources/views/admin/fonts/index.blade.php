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

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
        @forelse($fonts as $font)
            <div class="col">
                @if($font->file)
                <style>
                    @font-face {
                        font-family: 'preview_font_{{ $font->id }}';
                        src: url('{{ asset('storage/' . $font->file) }}');
                    }
                    #preview_{{ $font->id }} {
                        font-family: 'preview_font_{{ $font->id }}' !important;
                        font-size: 24px;
                    }
                </style>
                @endif

                <div class="card shadow-sm text-center p-2" style="min-height: 150px;">
                    @if($font->file)
                        <p id="preview_{{ $font->id }}" style="margin: 0;">ABC</p>
                    @else
                        <p style="margin: 0;">N/A</p>
                    @endif

                    <small class="d-block mt-2">{{ $font->name }}</small>

                    <div class="mt-2 d-flex justify-content-between">
                        <a href="{{ route('fonts.edit', $font->id) }}"
                           class="btn btn-sm w-45 font-btn">Edit</a>
                        <form action="{{ route('fonts.destroy', $font->id) }}" method="POST" class="w-45">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm w-100 font-btn"
                                    onclick="return confirm('Delete this font?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p>No fonts added yet.</p>
        @endforelse
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const msg = document.getElementById('success-msg');
    if(msg){ setTimeout(() => { msg.style.display = 'none'; }, 2000); }
});
</script>

<style>
.font-btn {
    border: 1px solid rgb(140,133,133);
    font-weight: 500;
    font-size: 0.85rem;
    border-radius: 4px;
    transition: all 0.2s;
    background: transparent;
    color: rgb(140,133,133);
}
.font-btn:hover { background-color: #000; color: #fff; }
.w-45 { width: 45%; }
</style>
@endsection
