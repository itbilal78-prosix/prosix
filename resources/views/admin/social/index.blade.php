@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Social Media</h2>
        <a href="{{ route('socials.create') }}" class="btn btn-primary">Add New</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Logo</th>
                <th>Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($socials as $social)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $social->name }}</td>
                <td><i class="{{ $social->logo }}"></i> ({{ $social->logo }})</td>
                <td><a href="{{ $social->link }}" target="_blank">{{ $social->link }}</a></td>
                <td>
                    <a href="{{ route('socials.edit', $social->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('socials.destroy', $social->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
