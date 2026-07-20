@extends('layouts.dashboard')

@section('content')

<div class="fonts-page">

    {{-- Header --}}
    <div class="fonts-page-header">
        <div>
            <h2>All Fonts</h2>
            <p>Manage and preview all uploaded fonts</p>
        </div>

        <a href="{{ route('fonts.create') }}" class="add-font-btn">
            Add Font
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div id="success-msg" class="font-alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Fonts Grid --}}
    <div class="fonts-grid">

        @forelse($fonts as $font)

            @if($font->file)
                <style>
                    @font-face {
                        font-family: 'preview_font_{{ $font->id }}';
                        src: url('{{ asset('storage/' . $font->file) }}');
                        font-display: swap;
                    }

                    #preview_{{ $font->id }} {
                        font-family: 'preview_font_{{ $font->id }}', sans-serif !important;
                    }
                </style>
            @endif

            <div class="font-card">

                {{-- Font Name --}}
                <div class="font-card-name">
                    {{ strtoupper($font->name) }}
                </div>

                {{-- Preview --}}
                <div class="font-preview-box">

                    @if($font->file)
                        <div
                            id="preview_{{ $font->id }}"
                            class="font-preview-text"
                        >
                            2
                        </div>
                    @else
                        <div class="font-preview-empty">
                            N/A
                        </div>
                    @endif

                </div>

                {{-- Actions --}}
                <div class="font-card-actions">

                    <a
                        href="{{ route('fonts.edit', $font->id) }}"
                        class="font-action-btn"
                    >
                        Edit
                    </a>

                    <form
                        action="{{ route('fonts.destroy', $font->id) }}"
                        method="POST"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="font-action-btn"
                            onclick="return confirm('Delete this font?')"
                        >
                            Delete
                        </button>
                    </form>

                </div>

            </div>

        @empty

            <div class="fonts-empty">
                <h4>No fonts added yet</h4>
                <p>Click the Add Font button to upload your first font.</p>
            </div>

        @endforelse

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const successMessage = document.getElementById('success-msg');

    if (successMessage) {
        setTimeout(function () {
            successMessage.style.opacity = '0';

            setTimeout(function () {
                successMessage.style.display = 'none';
            }, 300);
        }, 2000);
    }
});
</script>

<style>
.fonts-page {
    width: 100%;
    padding: 22px;
    background: #e9e9e9;
    min-height: calc(100vh - 70px);
}

.fonts-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 20px;
}

.fonts-page-header h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #222;
}

.fonts-page-header p {
    margin: 4px 0 0;
    font-size: 13px;
    color: #777;
}

.add-font-btn {
    padding: 11px 20px;
    border: 1px solid #111;
    border-radius: 5px;
    background: #111;
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 700;
    transition: 0.2s ease;
}

.add-font-btn:hover {
    background: #fff;
    color: #111;
}

.font-alert {
    padding: 12px 15px;
    margin-bottom: 18px;
    border: 1px solid #b9d6bd;
    border-radius: 5px;
    background: #dff3e2;
    color: #24612c;
    font-size: 13px;
    font-weight: 600;
    transition: opacity 0.3s ease;
}

.fonts-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}

.font-card {
    padding: 12px;
    border: 1px solid #d6d6d6;
    border-radius: 4px;
    background: #fff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.font-card:hover {
    border-color: #999;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
}

.font-card-name {
    min-height: 26px;
    padding-bottom: 8px;
    color: #414141;
    font-size: 16px;
    font-weight: 800;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.font-preview-box {
    width: 100%;
    height: 93px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9e9e9;
    overflow: hidden;
}

.font-preview-text {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #181818;
    font-size: 42px;
    line-height: 1;
    text-align: center;
}

.font-preview-empty {
    color: #999;
    font-size: 14px;
    font-weight: 600;
}

.font-card-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
}

.font-card-actions form {
    flex: 1;
    margin: 0;
}

.font-action-btn {
    width: 100%;
    min-height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 7px 12px;
    border: 1px solid #aaa;
    border-radius: 4px;
    background: #fff;
    color: #555;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s ease;
}

.font-card-actions > .font-action-btn {
    flex: 1;
}

.font-action-btn:hover {
    border-color: #111;
    background: #111;
    color: #fff;
}

.fonts-empty {
    grid-column: 1 / -1;
    padding: 60px 20px;
    border: 1px dashed #bbb;
    background: #fff;
    text-align: center;
}

.fonts-empty h4 {
    margin: 0 0 7px;
    color: #333;
    font-size: 18px;
}

.fonts-empty p {
    margin: 0;
    color: #888;
    font-size: 13px;
}

/* Tablet */
@media (max-width: 991px) {
     .fonts-grid{
        grid-template-columns:repeat(2,1fr);
    }

}

/* Mobile */
@media (max-width: 575px) {
    .fonts-page {
        padding: 15px;
    }

    .fonts-page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .add-font-btn {
        width: 100%;
        text-align: center;
    }

    .fonts-grid{
        grid-template-columns:1fr;
    }
}
</style>

@endsection
