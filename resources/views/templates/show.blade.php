@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h2>Template Details</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Templates</a></li>
                <li class="breadcrumb-item active">{{ $template->title }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">{{ $template->title }}</h5>
                </div>
                <div class="card-body">
                    @if($template->image_data)
                    <div class="text-center mb-4">
                        <img src="{{ $template->image_data }}" alt="{{ $template->title }}"
                             style="max-width: 100%; height: auto; border-radius: 8px;">
                    </div>
                    @endif

                    @if($template->description)
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p class="mt-2">{{ $template->description }}</p>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Source:</strong> <span class="badge bg-info">{{ $template->source }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Created:</strong> {{ $template->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex gap-2">
                        <a href="{{ route('templates.edit', $template) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                        <form action="{{ route('templates.destroy', $template) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this template?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-2"></i>Delete
                            </button>
                        </form>
                        <a href="{{ route('templates.index') }}" class="btn btn-secondary ms-auto">
                            <i class="bi bi-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
