@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3>Recycle Bin</h3>
            <p class="text-muted mb-0">Deleted data will appear here.</p>
        </div>
        <a href="{{ route('recycle-bin.download-images') }}" class="btn btn-dark">
            <i class="bi bi-download"></i> Download All Images
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Artwork Requests --}}
    <div class="card">
        <div class="card-header">Artwork Requests</div>
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
        <div class="card-header">Banners</div>
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
                                <td>
                                    @if($item->background_image)
                                        <img src="{{ asset('storage/' . $item->background_image) }}" style="width:90px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                    @else —
                                    @endif
                                </td>
                                <td>
                                    @if($item->mobile_background_image)
                                        <img src="{{ asset('storage/' . $item->mobile_background_image) }}" style="width:90px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                    @else —
                                    @endif
                                </td>
                                <td>
                                    @if($item->png_image)
                                        <img src="{{ asset('storage/' . $item->png_image) }}" style="width:90px;height:60px;object-fit:contain;border-radius:8px;border:1px solid #ddd;background:#fff;">
                                    @else —
                                    @endif
                                </td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.banner.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.banner.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
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

    {{-- Blogs --}}
    <div class="card mt-4">
        <div class="card-header">Blogs</div>
        <div class="card-body">
            @if($blogs->count() == 0)
                <p>No deleted blogs found.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th width="120">Image</th>
                            <th width="150">Video</th>
                            <th>Title</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blogs as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" style="width:90px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                    @else —
                                    @endif
                                </td>
                                <td>
                                    @if($item->video)
                                        <video width="120" height="70" controls style="border-radius:8px;border:1px solid #ddd;">
                                            <source src="{{ asset('storage/' . $item->video) }}" type="video/mp4">
                                        </video>
                                    @else —
                                    @endif
                                </td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.blog.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.blog.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
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

    {{-- Testimonials --}}
    <div class="card mt-4">
        <div class="card-header">Testimonials</div>
        <div class="card-body">
            @if($testimonials->count() == 0)
                <p>No deleted testimonials found.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th width="120">Image</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Rating</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($testimonials as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" style="width:60px;height:60px;object-fit:cover;border-radius:50%;border:1px solid #ddd;">
                                    @else —
                                    @endif
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->position ?? '—' }}</td>
                                <td>{{ $item->rating ?? '—' }}</td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.testimonial.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.testimonial.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
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

    {{-- Flipbooks --}}
    <div class="card mt-4">
        <div class="card-header">Flipbooks</div>
        <div class="card-body">
            @if($flipbooks->count() == 0)
                <p>No deleted flipbooks found.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th>Title</th>
                            <th>File</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($flipbooks as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->title }}</td>
                                <td>
                                    @if($item->file_path)
                                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-file-earmark-pdf"></i> View
                                        </a>
                                    @else —
                                    @endif
                                </td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.flipbook.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.flipbook.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
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

    {{-- Products --}}
    <div class="card mt-4">
        <div class="card-header">Products</div>
        <div class="card-body">
            @if($products->count() == 0)
                <p>No deleted products found.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th width="120">Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" style="width:90px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                    @else —
                                    @endif
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>${{ $item->price }}</td>
                                <td>{{ $item->category->name ?? '—' }}</td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.product.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.product.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
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

    {{-- Deals --}}
    <div class="card mt-4">
        <div class="card-header">Deals</div>
        <div class="card-body">
            @if($deals->count() == 0)
                <p>No deleted deals found.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th>Title</th>
                            <th>Subtitle</th>
                            <th width="200">Images</th>
                            <th width="200">Banners</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deals as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->subtitle ?? '—' }}</td>
                                <td>
                                    @php
                                        $dealImages = \App\Models\DealImage::withTrashed()->where('deal_id', $item->id)->get();
                                    @endphp
                                    @if($dealImages->count())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($dealImages as $img)
                                                <img src="{{ $img->image_path }}" style="width:60px;height:45px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                                            @endforeach
                                        </div>
                                    @else —
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $dealBanners = \App\Models\DealBanner::withTrashed()->where('deal_id', $item->id)->get();
                                    @endphp
                                    @if($dealBanners->count())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($dealBanners as $banner)
                                                <img src="{{ $banner->image_path }}" style="width:60px;height:45px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                                            @endforeach
                                        </div>
                                    @else —
                                    @endif
                                </td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.deal.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.deal.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
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

    {{-- Videos --}}
    <div class="card mt-4">
        <div class="card-header">Videos</div>
        <div class="card-body">
            @if($videos->count() == 0)
                <p>No deleted videos found.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th width="120">Thumbnail</th>
                            <th>Title</th>
                            <th>Video URL</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($videos as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    @if($item->thumbnail)
                                        <img src="{{ asset('storage/' . $item->thumbnail) }}" style="width:90px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                    @else —
                                    @endif
                                </td>
                                <td>{{ $item->title }}</td>
                                <td>
                                    @if($item->video_url)
                                        <a href="{{ $item->video_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-play-circle"></i> View
                                        </a>
                                    @else —
                                    @endif
                                </td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.video.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.video.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
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

    {{-- Categories --}}
    <div class="card mt-4">
        <div class="card-header">Categories</div>
        <div class="card-body">
            @if($categories->count() == 0)
                <p>No deleted categories found.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th width="120">Icon</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    @if($item->icon_image)
                                        <img src="{{ url($item->icon_image) }}" style="width:60px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #ddd;" onerror="this.src=''; this.style.display='none';">
                                    @else —
                                    @endif
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    @if($item->parent_id)
                                        <span class="badge bg-secondary">Subcategory</span>
                                    @else
                                        <span class="badge bg-dark">Main Category</span>
                                    @endif
                                </td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.category.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.category.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
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

    {{-- Navigations --}}
    <div class="card mt-4">
        <div class="card-header">Navigations</div>
        <div class="card-body">
            @if($navigations->count() == 0)
                <p>No deleted navigations found.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($navigations as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->slug ?? '—' }}</td>
                                <td>
                                    @if($item->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.navigation.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.navigation.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
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

    {{-- Customizer Models --}}
    <div class="card mt-4">
        <div class="card-header">Customizer Models</div>
        <div class="card-body">
            @if($customizerModels->count() == 0)
                <p>No deleted customizer models found.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th width="120">Thumbnail</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customizerModels as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    @if($item->thumbnail)
                                        <img src="{{ asset('uploads/models/' . $item->thumbnail) }}" style="width:90px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                    @else —
                                    @endif
                                </td>
                                <td>{{ $item->title }}</td>
                                <td>${{ $item->price ?? '—' }}</td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.customizer-model.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.customizer-model.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
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

    {{-- Patterns --}}
    <div class="card mt-4">
        <div class="card-header">Patterns</div>
        <div class="card-body">
            @if($patterns->count() == 0)
                <p>No deleted patterns found.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th width="120">Preview</th>
                            <th>Name</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patterns as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    @if($item->svg_path)
                                        <img src="{{ asset('storage/' . $item->svg_path) }}" style="width:60px;height:60px;object-fit:contain;border-radius:8px;border:1px solid #ddd;background:#f5f5f5;">
                                    @else —
                                    @endif
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.pattern.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.pattern.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
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

    {{-- Colors --}}
    <div class="card mt-4">
        <div class="card-header">Colors</div>
        <div class="card-body">
            @if($colors->count() == 0)
                <p>No deleted colors found.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th width="80">Color</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($colors as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    <div style="width:40px;height:40px;border-radius:50%;background:{{ $item->code }};border:1px solid #ddd;"></div>
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.color.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.color.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
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

    {{-- Templates --}}
    <div class="card mt-4">
        <div class="card-header">Templates</div>
        <div class="card-body">
            @if($templates->count() == 0)
                <p>No deleted templates found.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th width="120">Preview</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    @if($item->image_data)
                                        <img src="{{ $item->image_data }}" style="width:90px;height:60px;object-fit:contain;border-radius:8px;border:1px solid #ddd;background:#f5f5f5;">
                                    @elseif($item->svg_data)
                                        <div style="width:90px;height:60px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:1px solid #ddd;background:#f5f5f5;">
                                            {!! $item->svg_data !!}
                                        </div>
                                    @else —
                                    @endif
                                </td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->category->name ?? '—' }}</td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.template.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.template.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
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

    {{-- Fonts --}}
    <div class="card mt-4">
        <div class="card-header">Fonts</div>
        <div class="card-body">
            @if($fonts->count() == 0)
                <p>No deleted fonts found.</p>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th>Name</th>
                            <th>Preview</th>
                            <th>Deleted At</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fonts as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    @if($item->file)
                                        <span
                                            class="font-preview"
                                            data-font-url="{{ asset('storage/' . $item->file) }}"
                                            data-font-id="recycleFont{{ $item->id }}"
                                            style="font-size: 20px;">
                                            Aa Bb Cc 123
                                        </span>
                                    @else —
                                    @endif
                                </td>
                                <td>{{ $item->deleted_at }}</td>
                                <td>
                                    <form action="{{ route('recycle-bin.font.restore', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Restore</button>
                                    </form>
                                    <form action="{{ route('recycle-bin.font.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Permanent delete?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Delete Permanently</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.querySelectorAll('.font-preview').forEach(function(el) {
                            var fontUrl = el.getAttribute('data-font-url');
                            var fontId  = el.getAttribute('data-font-id');
                            var style   = document.createElement('style');
                            style.innerHTML = '@font-face { font-family: "' + fontId + '"; src: url("' + fontUrl + '"); }';
                            document.head.appendChild(style);
                            el.style.fontFamily = '"' + fontId + '"';
                        });
                    });
                </script>
            @endif
        </div>
    </div>

</div>
@endsection
