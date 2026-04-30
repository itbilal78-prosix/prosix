{{-- templates/_card.blade.php --}}
<div class="col-md-3 col-sm-6 mb-3">
    <div class="card template-card shadow-sm h-100 position-relative">

        {{-- ===== CHECKBOX ===== --}}
        <div class="tpl-cb-wrap">
            <input type="checkbox" class="tpl-checkbox" value="{{ $template->id }}">
        </div>

        {{-- Template preview image --}}
        @if($template->image_data)
            <img src="{{ $template->image_data }}"
                 class="card-img-top"
                 style="height:160px;object-fit:contain;padding:10px;">
        @else
            <div class="d-flex align-items-center justify-content-center"
                 style="height:160px;background:#f5f5f5;">
                <i class="bi bi-image text-muted" style="font-size:36px;"></i>
            </div>
        @endif

        <div class="card-body p-2">
            <strong style="font-size:13px;">{{ $template->title }}</strong>
            @if($template->category)
                {{-- <span class="badge bg-dark d-block mt-1" style="font-size:10px;">
                    {{ $template->category->name }}
                </span> --}}
            @endif
        </div>

        <div class="card-footer p-2">
            <div class="d-flex gap-2">
                <a href="{{ route('templates.show', $template->id) }}"
                   class="btn btn-dark btn-sm flex-fill">View</a>
                <form action="{{ route('templates.destroy', $template->id) }}" method="POST"
                      class="flex-fill" onsubmit="return confirmDelete(event, this)">
                    @csrf @method('DELETE')
                    <button class="btn btn-dark btn-sm w-100">Delete</button>
                </form>
            </div>
        </div>

    </div>
</div>
