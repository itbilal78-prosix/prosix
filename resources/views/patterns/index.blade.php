@extends('layouts.dashboard')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Patterns</h2>
        <a href="{{ route('patterns.create') }}" class="btn btn-dark">Add Pattern</a>
    </div>

    @if(session('success'))
        <div id="success-alert" class="alert alert-success">{{ session('success') }}</div>
        <script>
            setTimeout(function() {
                let alert = document.getElementById('success-alert');
                if(alert){
                    alert.style.transition = "opacity 0.5s ease";
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            }, 3000); 
        </script>
    @endif

    <div class="row">
        @foreach($patterns as $pattern)
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <img src="{{ asset('storage/' . $pattern->svg_path) }}" class="card-img-top p-3" style="height:150px; object-fit: contain;" alt="SVG">

                <div class="card-body text-center">
                    <h5 class="card-title">{{ $pattern->name }}</h5>

                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="{{ route('patterns.edit', $pattern->id) }}" class="btn btn-sm btn-dark">Edit</a>
                        <form action="{{ route('patterns.destroy', $pattern->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-dark" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-3">
        {{ $patterns->links() }}
    </div>
</div>
@endsection
