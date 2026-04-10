@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Templates</h2>

        <a href="{{ route('templates.create') }}" class="btn btn-dark">
            + Create Template
        </a>
    </div>

    <div class="row g-4">

        @forelse($templates as $template)

        <div class="col-lg-3 col-md-4 col-sm-6">

            <div class="card shadow-sm h-100">

                {{-- IMAGE --}}
            @if($template->image_data)
<img src="{{ $template->image_data }}"
     class="card-img-top"
     style="height:200px;object-fit:contain;background:#ffffff;">
@endif


                <div class="card-body text-center">

                    {{-- TITLE --}}
                    <h6 class="mb-3">{{ $template->title }}</h6>

                    {{-- BUTTONS --}}
                    <div class="d-flex justify-content-center gap-2">

                        <a href="{{ route('templates.edit',$template) }}"
                           class="btn btn-sm btn-dark">
                            Edit
                        </a>

                        <form action="{{ route('templates.destroy',$template) }}"
                              method="POST"
                              onsubmit="return confirm('Delete this template?')">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-sm btn-dark">
                                Delete
                            </button>

                        </form>

                    </div>

                </div>
            </div>

        </div>

        @empty

        <div class="col-12 text-center py-5">
            <h5>No Templates Found</h5>
        </div>

        @endforelse

    </div>

</div>
@endsection
