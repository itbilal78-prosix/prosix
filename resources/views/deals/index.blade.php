@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-black h3 mb-0">Deals & Offers</h1>
            <a href="{{ route('deals.create') }}" class="btn btn-dark">
                <i class="bi bi-plus-lg me-2"></i> Add New Deal
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($deals->isEmpty())
            <div class="alert alert-light text-center py-5 border">
                <i class="bi bi-info-circle-fill fs-1 text-black"></i>
                <p class="mt-3 text-black">No deals added yet. Add your first deal now!</p>
                <a href="{{ route('deals.create') }}" class="btn btn-dark mt-2">Add Deal</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark text-white">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Subtitle</th>
                            <th>Images</th>
                            <th>Banners</th> {{-- ADD THIS --}}
                            <th>Button</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deals as $deal)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td class="text-black">{{ $deal->title }}</td>

                                <td class="text-black">
                                    {{ $deal->subtitle ?? '-' }}
                                </td>

                                {{-- DEAL IMAGES --}}
                                <td>
                                    @if ($deal->images && $deal->images->count() > 0)
                                        <div class="d-flex flex-wrap gap-2">

                                            @foreach ($deal->images->take(3) as $img)
                                                <div class="position-relative">

                                                    <img src="{{ asset($img->image_path) }}" class="img-thumbnail"
                                                        style="width:60px; height:60px; object-fit:cover;">

                                                    @if (!empty($img->label))
                                                        <span class="deal-ribbon">
                                                            {{ $img->label }}
                                                        </span>
                                                    @endif

                                                </div>
                                            @endforeach

                                            @if ($deal->images->count() > 3)
                                                <span class="badge bg-secondary">
                                                    +{{ $deal->images->count() - 3 }}
                                                </span>
                                            @endif

                                        </div>
                                    @else
                                        <span class="text-muted">No images</span>
                                    @endif
                                </td>

                                {{-- BANNER IMAGES --}}
                                <td>
                                    @if ($deal->banners && $deal->banners->count())
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($deal->banners->take(2) as $banner)
                                                <img src="{{ $banner->image_path }}" class="img-thumbnail"
                                                    style="width:90px; height:50px; object-fit:cover;">
                                            @endforeach

                                            @if ($deal->banners->count() > 2)
                                                <span class="badge bg-dark">
                                                    +{{ $deal->banners->count() - 2 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No banners</span>
                                    @endif
                                </td>

                                <td class="text-black">
                                    {{ $deal->button_text ?? '-' }}
                                </td>

                                <td>
                                    <a href="{{ route('deals.edit', $deal) }}" class="btn btn-sm btn-outline-dark">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>

                                    <form action="{{ route('deals.destroy', $deal) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-dark"
                                            onclick="return confirm('Delete this deal?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <style scoped>
        .deal-ribbon {
            position: absolute;
            top: 5px;
            left: -5px;
            background: #000;
            color: #fff;
            font-size: 9px;
            padding: 3px 10px;
            font-weight: 600;
            border-radius: 0 4px 4px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .text-black {
            color: #000 !important;
        }

        .btn-dark {
            background-color: #000;
            color: #fff;
            border: 1px solid #000;
            transition: all 0.3s ease;
        }

        .btn-dark:hover {
            background-color: #fff;
            color: #000;
            border-color: #000;
        }

        .btn-outline-dark {
            color: #000;
            border: 1px solid #000;
            transition: all 0.3s ease;
        }

        .btn-outline-dark:hover {
            background-color: #000;
            color: #fff;
        }

        .img-thumbnail {
            border: 1px solid #000;
            border-radius: 8px;
        }

        .table-dark {
            background-color: #000 !important;
            color: #fff !important;
        }
    </style>
@endsection
