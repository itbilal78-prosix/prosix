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
                    src: url("{{ asset('storage/' . $font->file) }}") format("truetype");
                    font-display: swap;
                }
            </style>
        @endif
    @endforeach

    <div class="customize-container">
        @php
            $backUrl = isset($isUserMode) && $isUserMode ? url('/dashboard?tab=my-design') : route('models.index');
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
                            <i class="fas fa-arrows-alt"></i>
                            <span>Zoom</span>
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

                <!-- PATTERN MODAL -->
                <div id="patternLibraryModal" class="color-modal" style="display:none;">
                    <div class="color-modal-content">
                        <div id="patternList"></div>
                    </div>
                </div>

                <!-- ===================== PREVIEW PANEL ===================== -->
           <div id="previewPanel"
    style="position:fixed;top:0;right:-100vw;width:100vw;height:100vh;transition:.4s;z-index:9999;overflow:hidden;display:flex;">

    {{-- LEFT: Background Frame Image --}}
    <div style="position:relative;flex:1;overflow:hidden;background:#f1f1f1;display:flex;align-items:center;justify-content:center;">
        <img src="{{ asset('assets/images/frame.png') }}"
            style="width:100%;height:100%;object-fit:contain;pointer-events:none;opacity:1;">

        {{-- 4 VIEWS OVERLAY --}}
<div class="preview-views-row">
     <div class="preview-card-horizontal" style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                <div id="previewRight" class="preview-container-horizontal"></div>
                <div style="font-size:14px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:#111;">Right</div>
            </div>
            <div class="preview-card-horizontal" style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                <div id="previewFront" class="preview-container-horizontal"></div>
                <div style="font-size:14px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:#111;">Front</div>
            </div>

            <div class="preview-card-horizontal" style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                <div id="previewBack" class="preview-container-horizontal"></div>
                <div style="font-size:14px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:#111;">Back</div>
            </div>

            <div class="preview-card-horizontal" style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                <div id="previewLeft" class="preview-container-horizontal"></div>
                <div style="font-size:14px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:#111;">Left</div>
            </div>



        </div>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <div style="width:220px;background:#111;display:flex;flex-direction:column;align-items:stretch;padding:24px 16px;gap:16px;box-shadow:-4px 0 20px rgba(0,0,0,0.4);">

        {{-- Logos --}}
        <div style="display:flex;flex-direction:column;align-items:center;gap:8px;padding-bottom:16px;border-bottom:1px solid #333;">
            <img src="/assets/images/P LOGO WHITE.png" style="height:36px;object-fit:contain;">
            <img src="/assets/images/PROSIX SPORTS LOGO PNG WHITE.png" style="height:28px;object-fit:contain;">
            <span style="font-size:13px;font-weight:700;letter-spacing:3px;color:#C9A84C;text-transform:uppercase;margin-top:4px;">Preview</span>
        </div>

        {{-- Gold Divider --}}
        <div style="height:2px;background:#C9A84C;border-radius:2px;"></div>

        {{-- Download Draft --}}
        <button id="downloadAllViews"
            style="padding:12px 16px;background:#1a1a1a;color:white;border:1.5px solid #444;border-radius:8px;cursor:pointer;font-weight:600;font-size:13px;display:flex;align-items:center;gap:8px;transition:border-color .2s;"
            onmouseover="this.style.borderColor='#C9A84C'" onmouseout="this.style.borderColor='#444'">
            <i class="fas fa-download" style="color:#C9A84C;"></i> Download Draft
        </button>

        {{-- Download Clean (Admin only) --}}
        @if (!isset($isUserMode) || !$isUserMode)
        <button id="downloadClean"
            style="padding:12px 16px;background:#1a1a1a;color:white;border:1.5px solid #444;border-radius:8px;cursor:pointer;font-weight:600;font-size:13px;display:flex;align-items:center;gap:8px;transition:border-color .2s;"
            onmouseover="this.style.borderColor='#C9A84C'" onmouseout="this.style.borderColor='#444'">
            <i class="fas fa-download" style="color:#C9A84C;"></i> Download Clean
        </button>
        @endif

        {{-- Customize --}}
        <button onclick="closePreviewPanel()"
            style="padding:12px 16px;background:#1a1a1a;color:white;border:1.5px solid #444;border-radius:8px;cursor:pointer;font-weight:600;font-size:13px;display:flex;align-items:center;gap:8px;"
            onmouseover="this.style.borderColor='#C9A84C'" onmouseout="this.style.borderColor='#444'">
            <i class="fas fa-sliders-h" style="color:#C9A84C;"></i> Customize
        </button>

        {{-- Back to Website --}}
        <a href="/"
            style="padding:12px 16px;background:#1a1a1a;color:white;border:1.5px solid #444;border-radius:8px;cursor:pointer;font-weight:600;font-size:13px;display:flex;align-items:center;gap:8px;text-decoration:none;"
            onmouseover="this.style.borderColor='#C9A84C'" onmouseout="this.style.borderColor='#444'">
            <i class="fas fa-globe" style="color:#C9A84C;"></i> Back to Website
        </a>

        {{-- Spacer --}}
        <div style="flex:1;"></div>

        {{-- Gold Divider --}}
        <div style="height:2px;background:#C9A84C;border-radius:2px;"></div>

        {{-- Close Button --}}
        <button onclick="closePreviewPanel()"
            style="padding:12px 16px;background:#C9A84C;color:#111;border:none;border-radius:8px;cursor:pointer;font-weight:700;font-size:13px;display:flex;align-items:center;justify-content:center;gap:8px;">
            <i class="fas fa-times"></i> Close Preview
        </button>

    </div>
</div>
                {{-- ===================== END PREVIEW PANEL ===================== --}}

                <!-- APPLICATION MODAL -->
                <div id="applicationModal" class="color-modal" style="display:none;">
                    <div class="color-modal-content">
                        <button onclick="addNewApplication()">Add</button>
                    </div>
                </div>

                <!-- ROTATE POPUP -->
                <div id="rotateOverlay"
                    style="position:fixed;inset:0;background:rgba(0,0,0,0.75);display:flex;align-items:center;justify-content:center;z-index:99999;opacity:0;pointer-events:none;transition:opacity 0.3s ease;">
                    <div id="rotateCard"
                        style="background:#1a1a1a;border-radius:20px;padding:40px 48px;display:flex;flex-direction:column;align-items:center;gap:20px;transform:scale(0.7);transition:transform 0.4s cubic-bezier(0.34,1.56,0.64,1);">
                        <div id="phoneIcon" style="font-size:52px;">📱</div>
                        <div style="color:#fff;font-size:18px;font-weight:600;">Rotate your phone</div>
                        <div style="color:#999;font-size:13px;text-align:center;">
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
// =====================================================
// PROSIX SECURED — DEVTOOLS PROTECTION
// =====================================================

document.addEventListener('contextmenu', e => e.preventDefault());

document.addEventListener('keydown', e => {
    if (
        e.key === 'F12' ||
        (e.ctrlKey && e.shiftKey && ['I','J','C','K','S'].includes(e.key.toUpperCase())) ||
        (e.ctrlKey && e.key.toUpperCase() === 'U') ||
        (e.ctrlKey && e.key.toUpperCase() === 'P') ||
        (e.metaKey && e.altKey && ['I','J','C'].includes(e.key.toUpperCase()))
    ) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
});

//  DevTools size detection
const devToolsCheck = () => {
    const isRealMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    if (isRealMobile) return;

    const threshold = 160;
    const widthDiff = window.outerWidth - window.innerWidth;
    const heightDiff = window.outerHeight - window.innerHeight;

    if (widthDiff > threshold || heightDiff > threshold) {
        document.body.innerHTML = `
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;background:#000;color:#fff;font-family:sans-serif;">
                <h1 style="font-size:48px;margin:0;">😜</h1>
                <h2 style="margin:16px 0 8px;">Access Denied</h2>
                <p style="color:#aaa;font-size:14px;">PROSIX SECURED</p>
            </div>`;
        clearInterval(checkInterval);
    }
};

window.addEventListener('resize', devToolsCheck);
const checkInterval = setInterval(devToolsCheck, 1000);

// Console clear + warning
setInterval(() => { console.clear(); }, 100);
console.log('%cPROSIX SECURED', 'color:#C9A84C;font-size:40px;font-weight:900;');
console.log('%c⚠ Stop! This browser feature is for developers only.', 'color:red;font-size:14px;font-weight:bold;');
console.log('%cIf someone told you to paste something here, it is a scam.', 'color:red;font-size:13px;');

// Debugger trap
(function antiDebug() {
    function trap() {
        const start = performance.now();
        debugger;
        if (performance.now() - start > 100) {
            document.body.innerHTML = `
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;background:#000;color:#fff;font-family:sans-serif;">
                    <h1 style="font-size:48px;margin:0;">🔒</h1>
                    <h2 style="margin:16px 0 8px;">Access Denied</h2>
                    <p style="color:#aaa;font-size:14px;">PROSIX SECURED</p>
                </div>`;
        }
        setTimeout(trap, 1000);
    }
    trap();
})();
</script>
                <script>
                    // ============================================================
                    // ✅ BACKGROUND CANVAS
                    // Pattern: P [gap] Prosix [gap] P [gap] Prosix — ek hi row
                    // Brick offset: har doosri row half-tile shift
                    // ============================================================


                    // ✅ ORIENTATION CHECK
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
                <script src="/js/customizer-core.js"></script>
                <script src="/js/customizer-color.js"></script>
                <script src="/js/customizer-pattern.js"></script>
                <script src="/js/customizer-application.js"></script>
                <!-- ===================================================== -->
                <!-- USER DESIGN SAVE MODAL -->
                <!-- ===================================================== -->
                <div id="saveDesignPopup" class="save-design-popup" aria-hidden="true">
                    <div class="save-design-box" role="dialog" aria-modal="true"
                        aria-labelledby="saveDesignPopupTitle">

                        <button type="button" class="save-popup-close"
                            onclick="closeSaveDesignPopup()" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>

                        <div id="saveChoiceStep" class="save-popup-step active">
                            <div class="save-popup-badge">
                                <i class="fas fa-cloud-arrow-up"></i>
                            </div>

                            <div class="save-popup-heading">
                                <span class="save-popup-eyebrow">SAVE OPTIONS</span>
                                <h2 id="saveDesignPopupTitle">How would you like to save?</h2>
                                <p>
                                    Keep the current design and create a new copy, or update the
                                    design you currently have open.
                                </p>
                            </div>

                            <div class="save-popup-buttons">
                                <button type="button"
                                    class="save-option-btn save-as-new-btn"
                                    onclick="showSaveAsNewStep()">
                                    <span class="save-option-icon">
                                        <i class="fas fa-copy"></i>
                                    </span>

                                    <span class="save-option-copy">
                                        <strong>Save as New</strong>
                                        <small>Create a separate design and keep the current one unchanged.</small>
                                    </span>

                                    <span class="save-option-arrow">
                                        <i class="fas fa-arrow-right"></i>
                                    </span>
                                </button>

                                <button type="button"
                                    class="save-option-btn replace-design-btn"
                                    onclick="selectDesignSaveType('replace')">
                                    <span class="save-option-icon">
                                        <i class="fas fa-rotate"></i>
                                    </span>

                                    <span class="save-option-copy">
                                        <strong>Replace Current Design</strong>
                                        <small>Update only the design that is currently open.</small>
                                    </span>

                                    <span class="save-option-arrow">
                                        <i class="fas fa-arrow-right"></i>
                                    </span>
                                </button>
                            </div>

                            <button type="button" class="save-popup-cancel"
                                onclick="closeSaveDesignPopup()">
                                Cancel
                            </button>
                        </div>

                        <div id="saveNameStep" class="save-popup-step">
                            <button type="button" class="save-popup-back"
                                onclick="showSaveChoiceStep()">
                                <i class="fas fa-arrow-left"></i>
                                Back
                            </button>

                            <div class="save-popup-badge">
                                <i class="fas fa-pen-to-square"></i>
                            </div>

                            <div class="save-popup-heading">
                                <span class="save-popup-eyebrow">NEW DESIGN</span>
                                <h2>Name your design</h2>
                                <p>Choose a clear name so you can find this design easily later.</p>
                            </div>

                            <div class="save-name-field">
                                <label for="newDesignName">Design name</label>
                                <input type="text" id="newDesignName"
                                    maxlength="120"
                                    autocomplete="off"
                                    placeholder="Example: Blue Football Uniform">
                                <div id="saveNameError" class="save-name-error"></div>
                            </div>

                            <button type="button" id="confirmSaveAsNewBtn"
                                class="save-confirm-btn"
                                onclick="confirmSaveAsNew()">
                                <span>Save New Design</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>

                            <button type="button" class="save-popup-cancel"
                                onclick="closeSaveDesignPopup()">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                    // =====================================================
                    // USER DESIGN SAVE MODAL CONTROLS
                    // =====================================================
                    window.openSaveDesignPopup = function () {
                        const popup = document.getElementById('saveDesignPopup');

                        if (!popup) {
                            console.error('Save design modal was not found.');
                            return;
                        }

                        showSaveChoiceStep();
                        popup.classList.add('show');
                        popup.setAttribute('aria-hidden', 'false');
                        document.body.style.overflow = 'hidden';
                    };

                    window.closeSaveDesignPopup = function () {
                        const popup = document.getElementById('saveDesignPopup');

                        if (popup) {
                            popup.classList.remove('show');
                            popup.setAttribute('aria-hidden', 'true');
                        }

                        document.body.style.overflow = '';

                        const error = document.getElementById('saveNameError');
                        if (error) error.textContent = '';
                    };

                    window.showSaveChoiceStep = function () {
                        document.getElementById('saveChoiceStep')?.classList.add('active');
                        document.getElementById('saveNameStep')?.classList.remove('active');
                    };

                    window.showSaveAsNewStep = function () {
                        const choiceStep = document.getElementById('saveChoiceStep');
                        const nameStep = document.getElementById('saveNameStep');
                        const input = document.getElementById('newDesignName');
                        const error = document.getElementById('saveNameError');

                        choiceStep?.classList.remove('active');
                        nameStep?.classList.add('active');

                        if (error) error.textContent = '';

                        if (input) {
                            if (!input.value.trim()) {
                                input.value = 'My Design ' + new Date().toLocaleDateString('en-US');
                            }

                            setTimeout(() => {
                                input.focus();
                                input.select();
                            }, 120);
                        }
                    };

                    window.confirmSaveAsNew = async function () {
                        const input = document.getElementById('newDesignName');
                        const error = document.getElementById('saveNameError');
                        const name = input?.value.trim() || '';

                        if (!name) {
                            if (error) error.textContent = 'Please enter a design name.';
                            input?.focus();
                            return;
                        }

                        if (error) error.textContent = '';
                        closeSaveDesignPopup();

                        if (typeof window.saveUserDesign !== 'function') {
                            console.error('window.saveUserDesign function is missing.');
                            return;
                        }

                        await window.saveUserDesign('new', name);
                    };

                    window.selectDesignSaveType = async function (saveType) {
                        if (saveType !== 'replace') return;

                        closeSaveDesignPopup();

                        if (typeof window.saveUserDesign !== 'function') {
                            console.error('window.saveUserDesign function is missing.');
                            return;
                        }

                        await window.saveUserDesign('replace');
                    };

                    document.addEventListener('keydown', function (event) {
                        const popup = document.getElementById('saveDesignPopup');

                        if (event.key === 'Escape' && popup?.classList.contains('show')) {
                            closeSaveDesignPopup();
                        }

                        if (
                            event.key === 'Enter' &&
                            document.getElementById('saveNameStep')?.classList.contains('active')
                        ) {
                            event.preventDefault();
                            confirmSaveAsNew();
                        }
                    });

                    document.addEventListener('click', function (event) {
                        const popup = document.getElementById('saveDesignPopup');

                        if (popup && event.target === popup) {
                            closeSaveDesignPopup();
                        }
                    });
                </script>

                <!-- SAVE SUCCESS MODAL -->
                <div id="saveSuccessToast" class="save-success-toast" aria-hidden="true">
                    <div class="save-success-toast-inner">
                        <div class="save-success-ring">
                            <div class="save-success-icon">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>

                        <div class="save-success-copy">
                            <span class="save-success-eyebrow">PROSIX SPORTS</span>
                            <div class="save-success-title">Design Saved</div>
                            <div class="save-success-subtitle">Your changes were saved successfully.</div>
                        </div>

                        <div class="save-success-progress"></div>
                    </div>
                </div>
                </div>

            </div>{{-- end tools-bar --}}
        </div>{{-- end main-content --}}
    </div>{{-- end customize-container --}}

</body>

</html>



<style>
    /* =====================================================
       SAVE SUCCESS MODAL — CLEAN BLACK & WHITE
    ===================================================== */
    .save-success-toast {
        position: fixed;
        inset: 0;
        z-index: 1000002;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(0, 0, 0, .46);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity .22s ease, visibility .22s ease;
    }

    .save-success-toast.show {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .save-success-toast-inner {
        position: relative;
        width: min(100%, 320px);
        overflow: hidden;
        padding: 26px 24px 24px;
        border: 1px solid #e7e7e7;
        border-radius: 18px;
        background: #ffffff;
        color: #111111;
        text-align: center;
        box-shadow: 0 24px 70px rgba(0, 0, 0, .24);
        transform: translateY(14px) scale(.96);
        transition: transform .26s cubic-bezier(.2, .8, .2, 1);
    }

    .save-success-toast.show .save-success-toast-inner {
        transform: translateY(0) scale(1);
    }

    .save-success-ring {
        width: 62px;
        height: 62px;
        margin: 0 auto 15px;
        padding: 5px;
        border-radius: 50%;
        background: #f3f3f3;
        border: 1px solid #e5e5e5;
    }

    .save-success-icon {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #111111;
        color: #ffffff;
        font-size: 20px;
    }

    .save-success-eyebrow {
        display: inline-block;
        margin-bottom: 6px;
        color: #777777;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 1.8px;
    }

    .save-success-title {
        font-family: 'Poppins', sans-serif;
        font-size: 20px;
        font-weight: 700;
        line-height: 1.2;
    }

    .save-success-subtitle {
        max-width: 250px;
        margin: 7px auto 0;
        color: #777777;
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
        line-height: 1.55;
    }

    .save-success-progress {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 3px;
        background: #111111;
        transform-origin: left center;
    }

    .save-success-toast.show .save-success-progress {
        animation: saveProgress 2.35s linear forwards;
    }

    @keyframes saveProgress {
        from { transform: scaleX(1); }
        to { transform: scaleX(0); }
    }

    /* =====================================================
       SAVE OPTIONS MODAL — SMALL CLEAN BLACK & WHITE
    ===================================================== */
    .save-design-popup {
        position: fixed;
        inset: 0;
        z-index: 1000001;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(0, 0, 0, .52);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity .22s ease, visibility .22s ease;
    }

    .save-design-popup.show {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .save-design-box {
        position: relative;
        width: min(100%, 430px);
        min-height: auto;
        padding: 28px 24px 20px;
        overflow: hidden;
        border: 1px solid #e5e5e5;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 26px 80px rgba(0, 0, 0, .28);
        transform: translateY(16px) scale(.97);
        transition: transform .25s cubic-bezier(.2, .8, .2, 1);
    }

    .save-design-popup.show .save-design-box {
        transform: translateY(0) scale(1);
    }

    .save-popup-step {
        display: none;
        animation: saveStepIn .22s ease;
    }

    .save-popup-step.active {
        display: block;
    }

    @keyframes saveStepIn {
        from { opacity: 0; transform: translateX(8px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .save-popup-close {
        position: absolute;
        top: 13px;
        right: 13px;
        z-index: 3;
        width: 32px;
        height: 32px;
        border: 1px solid #e7e7e7;
        border-radius: 50%;
        background: #ffffff;
        color: #111111;
        font-size: 12px;
        cursor: pointer;
        transition: all .18s ease;
    }

    .save-popup-close:hover {
        background: #111111;
        color: #ffffff;
        border-color: #111111;
    }

    .save-popup-back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin: 0 0 10px;
        padding: 0;
        border: 0;
        background: transparent;
        color: #666666;
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }

    .save-popup-back:hover {
        color: #111111;
    }

    .save-popup-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 54px;
        height: 54px;
        margin: 0 auto 14px;
        border: 1px solid #e5e5e5;
        border-radius: 15px;
        background: #111111;
        color: #ffffff;
        font-size: 18px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, .12);
    }

    .save-popup-heading {
        text-align: center;
    }

    .save-popup-eyebrow {
        display: inline-block;
        margin-bottom: 6px;
        color: #777777;
        font-family: 'Poppins', sans-serif;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 1.8px;
    }

    .save-popup-heading h2 {
        margin: 0;
        color: #111111;
        font-family: 'Poppins', sans-serif;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -.3px;
    }

    .save-popup-heading p {
        max-width: 350px;
        margin: 8px auto 18px;
        color: #777777;
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
        line-height: 1.55;
    }

    .save-popup-buttons {
        display: grid;
        gap: 10px;
    }

    .save-option-btn {
        display: grid;
        grid-template-columns: 42px 1fr 20px;
        align-items: center;
        gap: 12px;
        width: 100%;
        padding: 12px 13px;
        border: 1px solid #e5e5e5;
        border-radius: 13px;
        background: #ffffff;
        color: #111111;
        text-align: left;
        cursor: pointer;
        box-shadow: 0 5px 14px rgba(0, 0, 0, .03);
        transition: all .18s ease;
    }

    .save-option-btn:hover {
        border-color: #111111;
        background: #fafafa;
        box-shadow: 0 9px 20px rgba(0, 0, 0, .07);
        transform: translateY(-1px);
    }

    .save-option-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 11px;
        background: #111111;
        color: #ffffff;
        font-size: 14px;
    }

    .replace-design-btn .save-option-icon {
        background: #f2f2f2;
        color: #111111;
        border: 1px solid #dddddd;
    }

    .save-option-copy {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: 3px;
    }

    .save-option-copy strong {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 700;
    }

    .save-option-copy small {
        color: #7a7a7a;
        font-family: 'Poppins', sans-serif;
        font-size: 10.5px;
        line-height: 1.45;
    }

    .save-option-arrow {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #555555;
        font-size: 11px;
        transition: transform .18s ease;
    }

    .save-option-btn:hover .save-option-arrow {
        transform: translateX(2px);
    }

    .save-name-field {
        margin-top: 4px;
    }

    .save-name-field label {
        display: block;
        margin-bottom: 7px;
        color: #222222;
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
        font-weight: 700;
    }

    .save-name-field input {
        width: 100%;
        height: 46px;
        padding: 0 13px;
        border: 1px solid #dcdcdc;
        border-radius: 11px;
        outline: none;
        background: #ffffff;
        color: #111111;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        box-sizing: border-box;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .save-name-field input:focus {
        border-color: #111111;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, .08);
    }

    .save-name-error {
        min-height: 18px;
        padding-top: 5px;
        color: #c53b3b;
        font-family: 'Poppins', sans-serif;
        font-size: 10px;
    }

    .save-confirm-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        width: 100%;
        height: 46px;
        margin-top: 5px;
        border: 0;
        border-radius: 11px;
        background: #111111;
        color: #ffffff;
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 10px 22px rgba(0, 0, 0, .14);
        transition: all .18s ease;
    }

    .save-confirm-btn:hover {
        background: #000000;
        transform: translateY(-1px);
    }

    .save-popup-cancel {
        display: block;
        width: 100%;
        margin-top: 10px;
        padding: 8px;
        border: 0;
        border-radius: 9px;
        background: transparent;
        color: #777777;
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: background .18s ease, color .18s ease;
    }

    .save-popup-cancel:hover {
        background: #f3f3f3;
        color: #111111;
    }

    @media (max-width: 576px) {
        .save-design-popup,
        .save-success-toast {
            padding: 12px;
        }

        .save-design-box {
            width: min(100%, 390px);
            padding: 25px 16px 17px;
            border-radius: 16px;
        }

        .save-popup-heading h2 {
            font-size: 20px;
        }

        .save-option-btn {
            grid-template-columns: 40px 1fr 18px;
            padding: 11px;
        }

        .save-option-icon {
            width: 40px;
            height: 40px;
        }

        .save-success-toast-inner {
            width: min(100%, 300px);
            padding: 24px 18px 22px;
        }
    }
</style>
