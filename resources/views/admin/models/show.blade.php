<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.MODEL_ID = {{ $model->id }};
        window.isUserMode = {{ isset($isUserMode) && $isUserMode ? 'true' : 'false' }};

        @if (isset($design) && $design)
            window.DESIGN_ID = {{ $design->id }};
            window.USER_DESIGN = {!! json_encode([
                'id' => $design->id,
                'color_changes' => $design->color_changes ?? [],
                'pattern_changes' => $design->pattern_changes ?? [],
                'mascot_changes' => $design->mascot_changes ?? [],
                'applications' => $design->applications ?? [],
            ]) !!};
        @else
            window.DESIGN_ID = null;
            window.USER_DESIGN = null;
        @endif

        window.API_URL = window.isUserMode ?
            '/user/model-api/{{ $model->id }}' + (window.DESIGN_ID ? '?customization_id=' + window.DESIGN_ID : '') :
            '{{ route('models.api.get', $model->id) }}';

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
                    src: url("{{ asset('storage/' . $font->file) }}") format("truetype");
                    font-display: swap;
                }
            </style>
        @endif
    @endforeach

    <div class="customize-container">
        @php
            $backUrl = isset($isUserMode) && $isUserMode ? url('/dashboard') : route('models.index');
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
    style="position:fixed;top:0;right:-100vw;width:100vw;height:100vh;transition:.4s;z-index:9999;overflow:hidden;">

    <!-- P Logo Texture Background -->
    <div style="position:absolute;inset:0;background-color:#eeeeee;z-index:0;">
        <div style="
            position:absolute;inset:0;
            background-image:url('/assets/images/P LOGO BLACK.png');
            background-size:70px 70px;
            background-repeat:repeat;
            opacity:0.06;
            pointer-events:none;">
        </div>
    </div>

    <!-- HEADER -->
    <div style="position:relative;z-index:2;background:#111;color:white;padding:14px 20px;display:flex;justify-content:space-between;align-items:center;">

        {{-- LEFT: Logo --}}
        <div style="display:flex;align-items:center;gap:10px;flex:1;">
            <img src="/assets/images/P LOGO WHITE.png" style="height:38px;object-fit:contain;">
            <img src="/assets/images/PROSIX SPORTS LOGO PNG WHITE.png" style="height:32px;object-fit:contain;">
        </div>

        {{-- CENTER: Preview Title --}}
        <div style="flex:1;text-align:center;">
            <span style="font-size:20px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:#fff;">Preview</span>
        </div>

        {{-- RIGHT: Buttons --}}
        <div style="display:flex;gap:10px;align-items:center;flex:1;justify-content:flex-end;">
            <button id="downloadAllViews"
                style="padding:8px 16px;background:#1a1a1a;color:white;border:1.5px solid #444;border-radius:6px;cursor:pointer;font-weight:600;font-size:13px;transition:all 0.2s;">
                <i class="fas fa-download" style="margin-right:6px;"></i>Download Draft
            </button>

            @if(!isset($isUserMode) || !$isUserMode)
            <button id="downloadClean"
                style="padding:8px 16px;background:#1a1a1a;color:white;border:1.5px solid #444;border-radius:6px;cursor:pointer;font-weight:600;font-size:13px;transition:all 0.2s;">
                <i class="fas fa-download" style="margin-right:6px;"></i>Download Clean
            </button>
            @endif

            <button onclick="closePreviewPanel()"
                style="width:36px;height:36px;background:#333;border:none;color:white;font-size:20px;border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;">
                ✕
            </button>
        </div>
    </div>

    {{-- GOLD LINE --}}
    <div style="position:relative;z-index:2;height:3px;background:#C9A84C;"></div>

    {{-- 4 VIEWS --}}
    <div style="position:relative;z-index:1;display:flex;gap:24px;justify-content:center;align-items:center;height:calc(100% - 70px);padding:30px;flex-wrap:wrap;">

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



                <!-- ROTATE POPUP -->
                <div id="rotateOverlay"
                    style="
  position:fixed; inset:0; background:rgba(0,0,0,0.75);
  display:flex; align-items:center; justify-content:center;
  z-index:99999; opacity:0; pointer-events:none;
  transition: opacity 0.3s ease;">

                    <div id="rotateCard"
                        style="
    background:#1a1a1a; border-radius:20px;
    padding:40px 48px; display:flex; flex-direction:column;
    align-items:center; gap:20px;
    transform: scale(0.7); transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1);">

                        <div id="phoneIcon" style="font-size:52px;">📱</div>
                        <div style="color:#fff; font-size:18px; font-weight:600;">Rotate your phone</div>
                        <div style="color:#999; font-size:13px; text-align:center;">
                            Best experience in<br>landscape mode
                        </div>

                    </div>
                </div>
                <style>
                    @keyframes phoneRotate {
                        0% {
                            transform: rotate(0deg);
                        }

                        40% {
                            transform: rotate(-15deg);
                        }

                        100% {
                            transform: rotate(90deg);
                        }
                    }

                    #phoneIcon.rotating {
                        animation: phoneRotate 1.2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
                    }
                </style>
                <script>
                    window.backendColors = @json(
                        $colors->map(fn($c) => [
                                'code' => $c->code,
                                'name' => $c->name,
                            ]));
                </script>

                <script>
                    function checkOrientation() {
                        const isPortrait = window.innerHeight > window.innerWidth;
                        const isMobile = window.innerWidth <= 1024 || window.innerHeight <= 600;
                        const overlay = document.getElementById('rotateOverlay');
                        const icon = document.getElementById('phoneIcon');

                        if (!overlay) return;

                        if (isMobile && isPortrait) {
                            overlay.style.opacity = '1';
                            overlay.style.pointerEvents = 'all';
                            document.getElementById('rotateCard').style.transform = 'scale(1)';
                            if (icon) icon.classList.add('rotating');
                        } else {
                            overlay.style.opacity = '0';
                            overlay.style.pointerEvents = 'none';
                            if (icon) icon.classList.remove('rotating');
                        }
                    }

                    window.addEventListener('resize', checkOrientation);
                    window.addEventListener('orientationchange', checkOrientation);
                    document.addEventListener('DOMContentLoaded', checkOrientation);
                </script>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

                {{-- <script src="{{ asset('js/customizer.js') }}"></script> --}}
                <script src="/js/customizer-core.js"></script>
                <script src="/js/customizer-color.js"></script>
                <script src="/js/customizer-pattern.js"></script>
                <script src="/js/customizer-application.js"></script>
                <div id="saveSuccessToast" class="save-success-toast">
                    <div class="save-success-toast-inner">
                        <div class="save-success-icon">✓</div>
                        <div>
                            <div class="save-success-title">Successfully Saved</div>
                            <div class="save-success-subtitle">Your design has been saved</div>
                        </div>
                    </div>
                </div>
</body>

</html>
<style>
    .save-success-toast {
        position: fixed;
        inset: 0;
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, .45);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: all .3s ease;
    }

    .save-success-toast.show {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .save-success-toast-inner {
        min-width: 320px;
        max-width: 420px;
        background: #111;
        color: #fff;
        border-radius: 18px;
        padding: 22px 24px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, .35);
        transform: scale(.88) translateY(20px);
        transition: all .3s ease;
    }

    .save-success-toast.show .save-success-toast-inner {
        transform: scale(1) translateY(0);
    }

    .save-success-icon {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #22c55e;
        color: #fff;
        font-size: 24px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .save-success-title {
        font-size: 18px;
        font-weight: 700;
        line-height: 1.2;
    }

    .save-success-subtitle {
        font-size: 13px;
        color: rgba(255, 255, 255, .72);
        margin-top: 4px;
    }
</style>
