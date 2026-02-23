@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <h2>Add Social Media</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('socials.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Social Media Name</label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Facebook, Twitter..." required>
        </div>
        <div class="mb-3">
            <label for="logo" class="form-label">Upload Logo</label>
            <input type="file" name="logo" class="form-control" id="logo" accept="image/*" required>
            <small class="text-muted">Upload your social media logo (png, jpg, jpeg).</small>
        </div>
        <div class="mb-3">
            <label for="link" class="form-label">URL</label>
            <input type="url" name="link" class="form-control" id="link" placeholder="https://facebook.com" required>
        </div>
        <button type="submit" class="btn btn-success">Add Social Media</button>
        <a href="{{ route('socials.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
