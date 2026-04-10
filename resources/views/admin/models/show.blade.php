<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<script>
window.MODEL_ID = {{ $model->id }};
window.isUserMode = {{ isset($isUserMode) && $isUserMode ? 'true' : 'false' }};

@if(isset($design) && $design)
window.DESIGN_ID = {{ $design->id }};
window.USER_DESIGN = {!! json_encode([
    'id'              => $design->id,
    'color_changes'   => $design->color_changes   ?? [],
    'pattern_changes' => $design->pattern_changes ?? [],
    'mascot_changes'  => $design->mascot_changes  ?? [],
    'applications'    => $design->applications    ?? [],
]) !!};
@else
window.DESIGN_ID = null;
window.USER_DESIGN = null;
@endif

window.API_URL = window.isUserMode
    ? '/user/model-api/{{ $model->id }}' + (window.DESIGN_ID ? '?customization_id=' + window.DESIGN_ID : '')
    : '{{ route("models.api.get", $model->id) }}';

console.log('API_URL:', window.API_URL);
console.log('USER_DESIGN:', window.USER_DESIGN);
</script>


    <title>Customize Model</title>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/customizer.css') }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>

<body>
    @foreach (\App\Models\Font::all() as $font)
        @if ($font->file)
            <style>
                @font-face {
                    font-family: "font_{{ $font->id }}";
                    // ✅ SAHI
                   src: url("{{ asset('storage/' . $font->file) }}")
 format("truetype");
                    font-display: swap;
                }
            </style>
        @endif
    @endforeach

    <div class="customize-container">
@php
$backUrl = isset($isUserMode) && $isUserMode
    ? url('/dashboard')
    : route('models.index');
    @endphp
        <!-- HEADER -->
        <div class="header-bar">
<a href="/" class="logo">
                    <img src="{{ asset('assets/images/PROSIX SPORTS LOGO PNG WHITE.png') }}" class="logo-img">
            </a>
            <div class="model-title">Customize Model</div>
<a href="{{ $backUrl }}" class="close-btn">✕ Close</a>
        </div>

        <div class="main-content">

            <!-- LEFT MODEL -->
            <div class="model-view-area">
                <div class="model-display">
                    <div id="modelDisplay" class="model-view-container">Loading...</div>
                </div>
            </div>







            <!-- RIGHT PANEL -->
            <div class="tools-bar">







                <!-- PART SELECT -->
                <div class="part-selection">

                    <button class="nav-arrow" onclick="navigatePart(-1)">
                        <i class="fa fa-chevron-left"></i>
                    </button>

                    <button id="partDropdownBtn">
                        <span id="partName">Select Part</span>
                        <i class="fa fa-chevron-down partArrow"></i>
                    </button>



                    <ul id="partDropdown"></ul>

                    <button class="nav-arrow" onclick="navigatePart(1)">
                        <i class="fa fa-chevron-right"></i>
                    </button>

                </div>












                <!-- TOOL PAGES -->
                <div class="color-wheel-section">

                    @include('admin.models.partials.color')

                    @include('admin.models.partials.pattern')

                    @include('admin.models.partials.application')

                </div>


                <!-- TABS -->
                <div class="tabs-container">
                    <div class="tab-row1">
                        <div class="tab-btn active" id="colorBtn" onclick="activateTab('colorBtn')">
                            <i class="fa fa-palette"></i>
                            <span>Color</span>
                        </div>

                        <div class="tab-btn" id="patternBtn" onclick="activateTab('patternBtn')">
                            <i class="fa fa-shapes"></i>
                            <span>Pattern</span>
                        </div>

                        <div class="tab-btn" id="applicationBtn" onclick="activateTab('applicationBtn')">
                            <i class="fa fa-layer-group"></i>
                            <span>Application</span>
                        </div>

                        <div class="tab-btn" id="saveBtn" onclick="activateTab('saveBtn')">
                            <i class="fa fa-save"></i>
                            <span>Save</span>
                        </div>

                        <div class="tab-btn" id="previewBtn" onclick="activateTab('previewBtn')">
                            <i class="fa fa-eye"></i>
                            <span>Preview</span>
                        </div>

                        <div class="tab-btn" id="zoomBtn" onclick="activateTab('zoomBtn')">
                            <i class="fas fa-arrows-alt"></i> <span>Zoom</span>
                        </div>

                    </div>

                    <div class="tab-row2">
                        <div class="tab-btn" id="frontBtn" onclick="activateTab('frontBtn')">
                            <i class="fas fa-arrow-up"></i>
                            <span>Front</span>
                        </div>

                        <div class="tab-btn" id="backBtn" onclick="activateTab('backBtn')">
                            <i class="fas fa-arrow-down"></i>
                            <span>Back</span>
                        </div>

                        <div class="tab-btn" id="leftBtn" onclick="activateTab('leftBtn')">
                            <i class="fas fa-arrow-left"></i>
                            <span>Left</span>
                        </div>

                        <div class="tab-btn" id="rightBtn" onclick="activateTab('rightBtn')">
                            <i class="fas fa-arrow-right"></i>
                            <span>Right</span>
                        </div>

                        <div class="tab-btn" id="resetBtn" onclick="activateTab('resetBtn')">
                            <i class="fas fa-undo-alt"></i>
                            <span>Reset</span>
                        </div>

                    </div>

                </div>






                <!-- COLOR MODAL -->


                <!-- PATTERN MODAL -->
                <div id="patternLibraryModal" class="color-modal" style="display:none;">
                    <div class="color-modal-content">
                        <div id="patternList"></div>
                    </div>
                </div>
                <!-- PREVIEW PANEL -->
                <div id="previewPanel"
                    style="position:fixed;top:0;right:-100vw;width:100vw;height:100vh;background:#fff;transition:.4s;z-index:9999;">

                    {{-- <div style="background:#111;color:white;padding:15px;display:flex;justify-content:space-between;align-items:center;">
<h3>Preview</h3>
<button onclick="closePreviewPanel()" style="background:none;border:none;color:white;font-size:26px;">×</button>
</div> --}}
                    <!-- PREVIEW PANEL HEADER (updated with Download button) -->
                    <div
                        style="background:#111;color:white;padding:15px;display:flex;justify-content:space-between;align-items:center;">
                        <h3>Preview</h3>

                        <div style="display:flex; gap:15px; align-items:center;">
                            <!-- Download Button -->
                            <button id="downloadAllViews"
                                style="padding:8px 16px; background:#000000; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:500; font-size:14px;">
                                Download All (PNG)
                            </button>

                            <!-- Close Button -->
                            <button onclick="closePreviewPanel()"
                                style="background:none;border:none;color:white;font-size:26px; cursor:pointer;">
                                ×
                            </button>
                        </div>
                    </div>
                    <div
                        style="display:flex;gap:20px;justify-content:center;align-items:center;height:100%;padding:30px;flex-wrap:wrap">

                        <div class="preview-card-horizontal">
                            <h4>Front</h4>
                            <div id="previewFront" class="preview-container-horizontal"></div>
                        </div>

                        <div class="preview-card-horizontal">
                            <h4>Back</h4>
                            <div id="previewBack" class="preview-container-horizontal"></div>
                        </div>

                        <div class="preview-card-horizontal">
                            <h4>Left</h4>
                            <div id="previewLeft" class="preview-container-horizontal"></div>
                        </div>

                        <div class="preview-card-horizontal">
                            <h4>Right</h4>
                            <div id="previewRight" class="preview-container-horizontal"></div>
                        </div>

                    </div>
                </div>

                <!-- APPLICATION MODAL -->
                <div id="applicationModal" class="color-modal" style="display:none;">
                    <div class="color-modal-content">
                        <button onclick="addNewApplication()">Add</button>
                    </div>
                </div>
                <script>
                    window.backendColors = @json(
                        $colors->map(fn($c) => [
                                'code' => $c->code,
                                'name' => $c->name,
                            ]));
                </script>



                <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

                {{-- <script src="{{ asset('js/customizer.js') }}"></script> --}}
                <script src="/js/customizer-core.js"></script>
                <script src="/js/customizer-color.js"></script>
                <script src="/js/customizer-pattern.js"></script>
                <script src="/js/customizer-application.js"></script>
</body>

</html>
