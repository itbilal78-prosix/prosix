@extends('layouts.dashboard')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold mb-1">Website Information</h3>
            <p class="text-muted mb-0">
                Manage footer, contact details, schedule and social media links.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('admin.website-info.update') }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf
        @method('PUT')


        {{-- CONTACT --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    Contact Information
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Phone Number
                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="{{ old('phone', $setting->phone) }}"
                            placeholder="+1 929 210 4402"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            WhatsApp Number
                        </label>

                        <input
                            type="text"
                            name="whatsapp"
                            class="form-control"
                            value="{{ old('whatsapp', $setting->whatsapp) }}"
                            placeholder="+1 929 210 4402"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $setting->email) }}"
                            placeholder="sales@prosix.com"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Address 1
                        </label>

                        <textarea
                            name="address_one"
                            rows="3"
                            class="form-control"
                        >{{ old('address_one', $setting->address_one) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Address 2
                        </label>

                        <textarea
                            name="address_two"
                            rows="3"
                            class="form-control"
                        >{{ old('address_two', $setting->address_two) }}</textarea>
                    </div>

                </div>

            </div>
        </div>


        {{-- OPENING --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="bi bi-clock me-2"></i>
                    Opening Schedule
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Opening Days
                        </label>

                        <input
                            type="text"
                            name="opening_days"
                            class="form-control"
                            value="{{ old('opening_days', $setting->opening_days) }}"
                            placeholder="Mon – Sat"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <input
                            type="text"
                            name="opening_status"
                            class="form-control"
                            value="{{ old('opening_status', $setting->opening_status) }}"
                            placeholder="Open"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Opening Time
                        </label>

                        <input
                            type="text"
                            name="opening_time"
                            class="form-control"
                            value="{{ old('opening_time', $setting->opening_time) }}"
                            placeholder="08:00 – 18:00"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Sunday Label
                        </label>

                        <input
                            type="text"
                            name="sunday_label"
                            class="form-control"
                            value="{{ old('sunday_label', $setting->sunday_label) }}"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Sunday Status
                        </label>

                        <input
                            type="text"
                            name="sunday_status"
                            class="form-control"
                            value="{{ old('sunday_status', $setting->sunday_status) }}"
                            placeholder="Closed"
                        >
                    </div>

                </div>

            </div>
        </div>


        {{-- SUBSCRIBE --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="bi bi-envelope-paper me-2"></i>
                    Subscribe Section
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Subscribe Heading
                        </label>

                        <input
                            type="text"
                            name="subscribe_title"
                            class="form-control"
                            value="{{ old('subscribe_title', $setting->subscribe_title) }}"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Subscribe Subtitle
                        </label>

                        <input
                            type="text"
                            name="subscribe_subtitle"
                            class="form-control"
                            value="{{ old('subscribe_subtitle', $setting->subscribe_subtitle) }}"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Website Badge Text
                        </label>

                        <input
                            type="text"
                            name="website_badge_text"
                            class="form-control"
                            value="{{ old('website_badge_text', $setting->website_badge_text) }}"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Website Badge Link
                        </label>

                        <input
                            type="text"
                            name="website_badge_link"
                            class="form-control"
                            value="{{ old('website_badge_link', $setting->website_badge_link) }}"
                        >
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="show_website_badge"
                                value="1"
                                id="showWebsiteBadge"
                                {{ $setting->show_website_badge ? 'checked' : '' }}
                            >

                            <label
                                class="form-check-label"
                                for="showWebsiteBadge"
                            >
                                Show Website Badge
                            </label>

                        </div>
                    </div>

                </div>

            </div>
        </div>


        {{-- SOCIAL --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="bi bi-share me-2"></i>
                    Social Media
                </h5>
            </div>

            <div class="card-body">

                @php
                    $socials = [
                        [
                            'field' => 'facebook_url',
                            'show' => 'show_facebook',
                            'name' => 'Facebook',
                            'icon' => 'bi-facebook'
                        ],
                        [
                            'field' => 'instagram_url',
                            'show' => 'show_instagram',
                            'name' => 'Instagram',
                            'icon' => 'bi-instagram'
                        ],
                        [
                            'field' => 'youtube_url',
                            'show' => 'show_youtube',
                            'name' => 'YouTube',
                            'icon' => 'bi-youtube'
                        ],
                        [
                            'field' => 'twitter_url',
                            'show' => 'show_twitter',
                            'name' => 'X / Twitter',
                            'icon' => 'bi-twitter'
                        ],
                        [
                            'field' => 'pinterest_url',
                            'show' => 'show_pinterest',
                            'name' => 'Pinterest',
                            'icon' => 'bi-pinterest'
                        ],
                    ];
                @endphp

                @foreach($socials as $social)

                    <div class="row align-items-center g-3 mb-3">

                        <div class="col-md-2">

                            <strong>
                                <i class="bi {{ $social['icon'] }} me-1"></i>
                                {{ $social['name'] }}
                            </strong>

                        </div>

                        <div class="col-md-8">

                            <input
                                type="text"
                                name="{{ $social['field'] }}"
                                class="form-control"
                                value="{{ old($social['field'], $setting->{$social['field']}) }}"
                                placeholder="https://..."
                            >

                        </div>

                        <div class="col-md-2">

                            <div class="form-check form-switch">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="{{ $social['show'] }}"
                                    value="1"
                                    {{ $setting->{$social['show']} ? 'checked' : '' }}
                                >

                                <label class="form-check-label">
                                    Show
                                </label>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>
        </div>


        {{-- FOOTER MEDIA --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="bi bi-images me-2"></i>
                    Footer Images
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Footer Logo 1
                        </label>

                        @if($setting->footer_logo_one)
                            <div class="mb-2 p-3 bg-dark rounded text-center">
                                <img
                                    src="{{ asset('storage/' . $setting->footer_logo_one) }}"
                                    style="max-height:70px;max-width:100%;"
                                >
                            </div>
                        @endif

                        <input
                            type="file"
                            name="footer_logo_one"
                            class="form-control"
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Footer Logo 2
                        </label>

                        @if($setting->footer_logo_two)
                            <div class="mb-2 p-3 bg-dark rounded text-center">
                                <img
                                    src="{{ asset('storage/' . $setting->footer_logo_two) }}"
                                    style="max-height:70px;max-width:100%;"
                                >
                            </div>
                        @endif

                        <input
                            type="file"
                            name="footer_logo_two"
                            class="form-control"
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Footer Background
                        </label>

                        @if($setting->footer_background)
                            <div class="mb-2">
                                <img
                                    src="{{ asset('storage/' . $setting->footer_background) }}"
                                    style="max-height:90px;width:100%;object-fit:cover;"
                                    class="rounded"
                                >
                            </div>
                        @endif

                        <input
                            type="file"
                            name="footer_background"
                            class="form-control"
                        >

                    </div>

                    <!-- Footer Texture Darkness -->
                    <div class="col-12">
                        <div class="texture-opacity-box">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                <div>
                                    <label for="footerTextureOpacity" class="form-label fw-semibold mb-1">
                                        Footer Texture Darkness
                                    </label>
                                    <div class="text-muted small">
                                        0% = texture fully visible, 100% = texture almost hidden
                                    </div>
                                </div>

                                <div class="texture-opacity-value">
                                    <span id="textureOpacityValue">
                                        {{ old('footer_texture_opacity', $setting->footer_texture_opacity ?? 48) }}
                                    </span>%
                                </div>
                            </div>

                            <input
                                type="range"
                                class="form-range"
                                id="footerTextureOpacity"
                                name="footer_texture_opacity"
                                min="0"
                                max="100"
                                step="1"
                                value="{{ old('footer_texture_opacity', $setting->footer_texture_opacity ?? 48) }}"
                            >

                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">0% — Clear Texture</small>
                                <small class="text-muted">100% — Hidden Texture</small>
                            </div>

                            <div class="texture-preview mt-3">
                                <div
                                    id="texturePreviewOverlay"
                                    class="texture-preview-overlay"
                                    style="background: rgba(0, 0, 0, {{ (old('footer_texture_opacity', $setting->footer_texture_opacity ?? 48)) / 100 }});"
                                ></div>

                                <div class="texture-preview-content">
                                    <strong>Footer Texture Preview</strong>
                                    <small>Move the slider to preview darkness.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>


        <div class="d-flex justify-content-end">

            <button
                type="submit"
                class="btn btn-dark px-4 py-2"
            >
                <i class="bi bi-floppy me-1"></i>
                Save Website Information
            </button>

        </div>

    </form>

</div>


<style>
.texture-opacity-box {
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 18px;
    background: #f8f9fa;
}

.texture-opacity-value {
    min-width: 70px;
    padding: 7px 12px;
    border-radius: 8px;
    background: #000;
    color: #fff;
    font-weight: 700;
    text-align: center;
}

.texture-preview {
    position: relative;
    min-height: 120px;
    overflow: hidden;
    border-radius: 10px;
    background:
        #000
        url('/public/assets/images/footer texture.svg')
        no-repeat center center;
    background-size: cover;
    border: 1px solid #222;
}

.texture-preview-overlay {
    position: absolute;
    inset: 0;
    z-index: 1;
    pointer-events: none;
}

.texture-preview-content {
    position: relative;
    z-index: 2;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #fff;
    text-align: center;
    padding: 15px;
}

.texture-preview-content strong {
    font-size: 16px;
}

.texture-preview-content small {
    margin-top: 4px;
    color: rgba(255,255,255,.7);
}

#footerTextureOpacity {
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const slider = document.getElementById('footerTextureOpacity');
    const value = document.getElementById('textureOpacityValue');
    const previewOverlay = document.getElementById('texturePreviewOverlay');

    if (!slider || !value) {
        return;
    }

    function updateTexturePreview() {
        const opacity = Math.min(
            100,
            Math.max(0, Number(slider.value || 0))
        );

        value.textContent = opacity;

        if (previewOverlay) {
            previewOverlay.style.background =
                `rgba(0, 0, 0, ${opacity / 100})`;
        }
    }

    slider.addEventListener('input', updateTexturePreview);

    updateTexturePreview();
});
</script>

@endsection
