@extends('layouts.frontend')

@section('content')
<div class="container py-5">
    <h2>{{ $model->title }}</h2>
    <p>{{ $model->description }}</p>

    <div class="row mt-4">
        @php
            $views = ['front','back','left','right'];
            $types = ['black','white','svg'];
        @endphp

        @foreach($views as $view)
        <div class="col-md-6 mb-3">
            <h5 class="text-capitalize">{{ $view }} View</h5>
            @foreach($types as $type)
                @php $field = $view.'_'.$type; @endphp
                @if($model->$field)
                    <img src="{{ asset('uploads/models/'.$model->$field) }}" 
                         class="img-fluid mb-2" style="max-height:150px; object-fit:contain;">
                @endif
            @endforeach
        </div>
        @endforeach
    </div>

    <a href="{{ route('frontend.models') }}" class="btn btn-secondary mt-3">Back to Models</a>
</div>
@endsection
