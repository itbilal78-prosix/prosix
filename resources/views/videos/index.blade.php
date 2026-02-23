@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Videos</h2>
        <a href="{{ route('videos.create') }}" class="btn btn-dark">Add New Video</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($videos->isEmpty())
        <div class="alert alert-info text-center">
            No videos uploaded yet.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark text-white">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Thumbnail</th>
                        <th>Video</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($videos as $video)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $video->title }}</td>
                            <td>
                                <img src="{{ asset('storage/' . $video->thumbnail) }}" width="100" class="img-thumbnail">
                            </td>
                            <td>
                                <video width="150" controls class="border">
                                    <source src="{{ asset('storage/' . $video->video_url) }}" type="video/mp4">
                                </video>
                            </td>
                            <td>
                                <a href="{{ route('videos.edit', $video->id) }}" class="btn btn-sm btn-dark">Edit</a>
                                <form action="{{ route('videos.destroy', $video->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete this video?')" class="btn btn-sm btn-dark">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
