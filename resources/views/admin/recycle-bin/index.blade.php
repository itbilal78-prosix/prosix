@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h3>Recycle Bin</h3>
    <p class="text-muted">Deleted data will appear here.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Artwork Requests --}}
    <div class="card">
        <div class="card-header">
            Artwork Requests
        </div>

        <div class="card-body">
            @if($artworks->count() == 0)
                <p>No deleted artwork requests found.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Team</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($artworks as $item)
                            <tr>
                                <td>{{ $item->full_name }}</td>
                                <td>{{ $item->team_name ?? '—' }}</td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.artwork.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>

                                    <form action="{{ route('recycle-bin.artwork.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Delete Permanently</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

 {{-- Banners --}}
<div class="card mt-4">
    <div class="card-header">
        Banners
    </div>

    <div class="card-body">
        @if($banners->count() == 0)
            <p>No deleted banners found.</p>
        @else
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th width="80">ID</th>
                        <th width="120">Background</th>
                        <th width="120">Mobile</th>
                        <th width="120">PNG</th>
                        <th>Title</th>
                        <th>Deleted At</th>
                        <th width="260">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($banners as $item)
                        <tr>
                            <td>{{ $item->id }}</td>

                            {{-- Background --}}
                            <td>
                                @if($item->background_image)
                                    <img
                                        src="{{ asset('storage/' . $item->background_image) }}"
                                        style="width:90px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #ddd;"
                                    >
                                @else
                                    —
                                @endif
                            </td>

                            {{-- Mobile Background --}}
                            <td>
                                @if($item->mobile_background_image)
                                    <img
                                        src="{{ asset('storage/' . $item->mobile_background_image) }}"
                                        style="width:90px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #ddd;"
                                    >
                                @else
                                    —
                                @endif
                            </td>

                            {{-- PNG --}}
                            <td>
                                @if($item->png_image)
                                    <img
                                        src="{{ asset('storage/' . $item->png_image) }}"
                                        style="width:90px;height:60px;object-fit:contain;border-radius:8px;border:1px solid #ddd;background:#fff;"
                                    >
                                @else
                                    —
                                @endif
                            </td>

                            <td>{{ $item->title }}</td>

                            <td>{{ $item->deleted_at }}</td>

                            <td>
                                <form action="{{ route('recycle-bin.banner.restore', $item->id) }}"
                                      method="POST"
                                      style="display:inline-block;">
                                    @csrf

                                    <button class="btn btn-success btn-sm">
                                        Restore
                                    </button>
                                </form>

                                <form action="{{ route('recycle-bin.banner.delete', $item->id) }}"
                                      method="POST"
                                      style="display:inline-block;"
                                      onsubmit="return confirm('Permanent delete?')">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm">
                                        Delete Permanently
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
</div>
@endsection
