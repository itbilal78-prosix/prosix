@extends('layouts.frontend')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Available Models (Demo)</h2>

    <div class="row">
        @forelse($models as $model)
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-image-wrapper mb-3 text-center" style="position:relative; height:180px;">
                    @if($model->front_black)
                        <img src="{{ asset('uploads/models/'.$model->front_black) }}" 
                             style="position:absolute; top:0; left:50%; transform:translateX(-50%); height:100%; object-fit:contain; mix-blend-mode:screen;">
                    @endif
                    @if($model->front_white)
                        <img src="{{ asset('uploads/models/'.$model->front_white) }}" 
                             style="position:absolute; top:0; left:50%; transform:translateX(-50%); height:100%; object-fit:contain; mix-blend-mode:multiply;">
                    @endif
                    @if($model->front_svg)
                        <img src="{{ asset('uploads/models/'.$model->front_svg) }}" 
                             style="position:absolute; top:0; left:50%; transform:translateX(-50%); height:100%; object-fit:contain;">
                    @endif
                    @if(!($model->front_black || $model->front_white || $model->front_svg))
                        <img src="{{ asset('uploads/models/placeholder.png') }}" style="height:100%; object-fit:contain;">
                    @endif
                </div>

                <div class="card-body p-2 text-start">
                    <h5 class="card-title mb-1">{{ $model->title }}</h5>
                    <p class="card-text small mb-0">{{ \Illuminate\Support\Str::limit($model->description, 50) }}</p>
                </div>

                <div class="card-footer p-2 text-center">
                    <a href="{{ route('frontend.models.show', $model->id) }}" class="btn btn-primary btn-sm">
                        View Model
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">No models available.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection
