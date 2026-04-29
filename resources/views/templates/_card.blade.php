<div class="col-lg-2 col-md-3 col-sm-4 col-6">
    <div class="card template-card shadow-sm h-100">
        @if($template->image_data)
            <img src="{{ $template->image_data }}"
                 class="card-img-top"
                 style="height:160px;object-fit:contain;background:#f9f9f9;padding:8px;">
        @else
            <div class="d-flex align-items-center justify-content-center"
                 style="height:160px;background:#f0f0f0;color:#aaa;">
                <i class="bi bi-image" style="font-size:40px;"></i>
            </div>
        @endif
        <div class="card-body p-2 text-center">
            <p class="fw-semibold mb-0" style="font-size:13px;">{{ $template->title }}</p>
        </div>
        <div class="card-footer p-2">
            <div class="d-flex gap-2">
                <a href="{{ route('mascots.edit', $template->id) }}"
                   class="btn btn-dark btn-sm flex-fill" style="font-size:12px;">Edit</a>
                <form action="{{ route('templates.destroy', $template) }}"
                      method="POST" class="flex-fill"
                      onsubmit="return confirmDelete(event, this)">
                    @csrf @method('DELETE')
                    <button class="btn btn-dark btn-sm w-100" style="font-size:12px;">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
