@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2 class="text-black mb-0">Testimonials</h2>
        <a href="{{ route('testimonials.create') }}" class="btn btn-dark">+ Add Testimonial</a>
    </div>

    @if(session('success'))
    <div class="alert alert-dark border border-dark">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50"><input type="checkbox" id="selectAll"></th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Rating</th>
                    <th>Image</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="testimonialsTable">
                @forelse($testimonials as $testimonial)
                <tr>
                    <td><input type="checkbox" class="testimonial-checkbox" value="{{ $testimonial->id }}"></td>
                    <td>{{ $testimonial->id }}</td>
                    <td>{{ $testimonial->name }}</td>
                    <td>{{ $testimonial->position }}</td>
                    <td>
                        @for($i=1;$i<=5;$i++)
                            <i class="bi bi-star{{ $i <= $testimonial->rating ? '-fill' : '' }}" style="color:#000;"></i>
                        @endfor
                    </td>
                    <td>
                        @if($testimonial->image)
                            <img src="{{ asset('storage/'.$testimonial->image) }}" width="80" class="rounded border">
                        @else
                            <span class="text-muted">No Image</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('testimonials.edit', $testimonial->id) }}" class="btn btn-sm btn-dark">
                                <i class="bi bi-pencil"></i> Edit
                            </a>

                            <form action="{{ route('testimonials.destroy', $testimonial->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-dark" onclick="return confirm('Delete this testimonial?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No testimonials found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-end mt-4" id="paginationLinks">
            {{ $testimonials->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const selectAll = document.getElementById('selectAll');
    selectAll.addEventListener('change', function(){
        document.querySelectorAll('.testimonial-checkbox').forEach(cb=>cb.checked=this.checked);
    });
});
</script>

<style scoped>
.btn-dark { background-color:#000; color:#fff; border:1px solid #000; transition:0.3s; }
.btn-dark:hover { background-color:#fff; color:#000; }
.btn-outline-dark { border:1px solid #000; color:#000; transition:0.3s; }
.btn-outline-dark:hover { background-color:#000; color:#fff; }
img.border { border:1px solid #000; border-radius:8px; }
</style>
@endsection
