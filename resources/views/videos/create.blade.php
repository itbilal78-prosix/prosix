@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Add New Video</h2>
    <a href="{{ route('videos.index') }}" class="btn btn-secondary mb-3">Back</a>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control bg-light text-dark" required>
        </div>

        <div class="mb-3">
            <label>Thumbnail</label>
            <input type="file" name="thumbnail" class="form-control" accept="image/*" id="thumbnailInput" required>
            <div class="mt-2">
                <img id="thumbnailPreview" src="#" style="display:none; max-width:150px;" class="img-thumbnail">
            </div>
        </div>

        <div class="mb-3">
            <label>Video</label>
            <input type="file" name="video_url" class="form-control" accept="video/*" id="videoInput" required>
            <div class="mt-2">
                <video id="videoPreview" width="200" controls style="display:none;" class="border"></video>
            </div>
        </div>

        <button type="submit" class="btn btn-dark">Add Video</button>
    </form>
</div>

<script>
    // Thumbnail Preview
    document.getElementById('thumbnailInput').addEventListener('change', function(e){
        const file = e.target.files[0];
        const preview = document.getElementById('thumbnailPreview');
        if(file){
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });

    // Video Preview
    document.getElementById('videoInput').addEventListener('change', function(e){
        const file = e.target.files[0];
        const preview = document.getElementById('videoPreview');
        if(file){
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });
</script>
@endsection
