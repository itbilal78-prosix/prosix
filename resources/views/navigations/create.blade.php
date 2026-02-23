@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">

    <h2 class="fw-bold mb-3 text-dark">Add New Navigation</h2>

    <a href="{{ route('navigations.index') }}" class="btn btn-outline-dark mb-3">
        ← Back to List
    </a>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="alert alert-dark border border-dark">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li class="text-danger">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border border-dark">
        <div class="card-body">

            <form action="{{ route('navigations.store') }}" method="POST">
                @csrf

                {{-- Title --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">Title</label>
                    <input type="text" name="title"
                        class="form-control border-dark"
                        placeholder="Home"
                        required>
                </div>

                {{-- Slug --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">Slug</label>
                    <input type="text" name="slug"
                        class="form-control border-dark"
                        placeholder="home"
                        required>
                </div>

                {{-- Route --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">Route</label>
                    <input type="text" name="route"
                        class="form-control border-dark"
                        placeholder="/home">
                </div>

                {{-- Position --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">Position</label>
                    <input type="number" name="position"
                        class="form-control border-dark"
                        value="0">
                </div>

                {{-- Has Dropdown --}}
                <div class="form-check mb-3">
                    <input class="form-check-input border-dark"
                        type="checkbox"
                        name="has_dropdown"
                        id="hasDropdown">
                    <label class="form-check-label text-dark fw-semibold" for="hasDropdown">
                        Has Dropdown
                    </label>
                </div>

                {{-- Status --}}
                <div class="form-check mb-4">
                    <input class="form-check-input border-dark"
                        type="checkbox"
                        name="status"
                        id="status"
                        checked>
                    <label class="form-check-label text-dark fw-semibold" for="status">
                        Active
                    </label>
                </div>

                {{-- Buttons --}}
                <div class="d-flex gap-2">
                    <button class="btn btn-dark px-4">
                        💾 Save Navigation
                    </button>

                    <a href="{{ route('navigations.index') }}"
                        class="btn btn-outline-dark px-4">
                        Cancel
                    </a>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
<style>
/* FORCE BLACK CHECKBOX (Bootstrap override) */
.form-check-input {
    border: 2px solid #000;
    cursor: pointer;
}

/* Checked state */
.form-check-input:checked {
    background-color: #000 !important;
    border-color: #000 !important;
}

/* Remove blue focus shadow */
.form-check-input:focus {
    box-shadow: none !important;
    border-color: #000 !important;
}

/* Tick color white for contrast */
.form-check-input:checked[type="checkbox"] {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='white' d='M12.97 4.97a.75.75 0 0 1 0 1.06L7.477 11.53a.75.75 0 0 1-1.06 0L3.03 8.03a.75.75 0 1 1 1.06-1.06L6.94 9.82l4.97-4.97a.75.75 0 0 1 1.06 0z'/%3E%3C/svg%3E");
}
</style>

