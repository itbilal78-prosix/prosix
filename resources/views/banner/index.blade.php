@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h2>Banners</h2>

    <a href="{{ route('admin.banners.create') }}" class="btn add_banner mb-3">
        Add New Banner
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Position</th>
                <th>Title</th>
                <th>Button</th>
                <th>Background</th>
                <th>PNG</th>
                <th width="160">Actions</th>
            </tr>
        </thead>
        <tbody id="sortable-banners">
            @forelse($banners as $banner)
            <tr data-id="{{ $banner->id }}">
                <td>{{ $banner->id }}</td>
                <td><strong>#{{ $banner->position }}</strong></td>
                <td>{{ $banner->title }}</td>
                <td>{{ $banner->button_text ?? '—' }}</td>
                <td>
                    <img src="{{ asset('storage/' . $banner->background_image) }}" width="120" alt="Background">
                </td>
                <td>
                    @if($banner->png_image)
                        <img src="{{ asset('storage/' . $banner->png_image) }}" width="80" alt="PNG Image">
                    @else
                        —
                    @endif
                </td>
              <td class="d-flex gap-2">
    <!-- Edit Button -->
    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-sm banner-btn">
        <i class="bi bi-pencil-fill"></i> <!-- Bootstrap icon -->
    </a>

    <!-- Delete Button -->
    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm banner-btn" onclick="return confirm('Are you sure you want to delete this banner?')">
            <i class="bi bi-trash-fill"></i>
        </button>
    </form>
</td>

            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">No banners found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">

<script>
$(function() {
    $("#sortable-banners").sortable({
        placeholder: "ui-state-highlight",
        update: function(event, ui) {
            let order = [];
            $("#sortable-banners tr").each(function(index) {
                if ($(this).data("id")) {
                    order.push({
                        id: $(this).data("id"),
                        position: index + 1
                    });
                }
            });

            $.ajax({
                url: '{{ route("admin.banners.reorder") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order: order
                },
                success: function(response) {
                    console.log("Order updated successfully!", response);
                },
                error: function(err) {
                    console.error("Error saving order", err);
                    alert("Something went wrong while saving order. Please try again.");
                }
            });
        }
    });

    $("#sortable-banners").disableSelection();
});
</script>

<style>
.ui-state-highlight {
    height: 70px;
    background: #f1f2f3;
    border: 2px dashed #6c757d;
    border-radius: 6px;
}
.add_banner {
    background-color: black;
    color: white;
    border: 1px solid black; /* initial border */
    transition: all 0.3s ease; /* smooth hover */
}

.add_banner:hover {
    background-color: white;
    color: black;
    border: 1px solid black; /* correct syntax */
}

td strong {
    color: #000000;
}
.banner-btn {
    background-color: white; /* button background */
    color: black;            /* icon & text color */
    border: 1px solid black; /* black border */
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px 8px;
    transition: all 0.25s ease;
    border-radius: 4px;
}

.banner-btn i {
    font-size: 0.9rem;
}

.banner-btn:hover {
    background-color: black;
    color: white;
    border: 1px solid black;
    transform: translateY(-2px);
}

</style>
@endsection
