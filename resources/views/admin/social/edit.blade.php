@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <h2>Edit Social Media</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('socials.update', $social->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Social Media Name</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ $social->name }}" required>
        </div>
        <div class="mb-3">
            <label for="logo" class="form-label">Icon Class</label>
            <input type="text" name="logo" class="form-control" id="logo" value="{{ $social->logo }}" required>
            <small class="text-muted">Use Bootstrap Icons class.</small>
        </div>
        <div class="mb-3">
            <label for="link" class="form-label">URL</label>
            <input type="url" name="link" class="form-control" id="link" value="{{ $social->link }}" required>
        </div>
        <button type="submit" class="btn btn-success">Update Social Media</button>
        <a href="{{ route('socials.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
