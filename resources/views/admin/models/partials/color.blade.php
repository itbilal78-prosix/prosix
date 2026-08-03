<div id="colorPage" class="tool-page">

    <!-- SOLID / GRADIENT SWITCH -->
    <div id="fillSwitch" style="display:flex;gap:10px;margin-bottom:15px;">
        <button id="solidBtn" class="fill-btn active" onclick="setFillType('solid')">
            <i class="fas fa-fill-drip"></i> Solid
        </button>
        <button id="gradientBtn" class="fill-btn" onclick="setFillType('gradient')">
            <i class="fas fa-circle-half-stroke"></i> Gradient
        </button>
    </div>

    <!-- SOLID VIEW -->
    <div id="solidPanel">
        <!-- COLOR WHEEL -->
        <div class="color-wheel-container" id="colorWheelPanel">
            <div class="color-wheel-outer">
                <div class="color-wheel-ring" id="colorWheelRing"></div>
                <div class="color-wheel-white-ring"></div>
                <div class="color-wheel-center" id="selectedColorBtn" onclick="openColorPalette()"
                    style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:8px;">
                    SELECT <br> CUSTOM<br>COLORS
                </div>
            </div>
        </div>

        <!-- INLINE COLOR SELECTOR -->
        <div id="inlineColorSelector" class="inline-color-selector">
            <div class="inline-color-selector-header">
                <div class="inline-color-selector-title">Select Colors</div>
                {{-- <button type="button" class="inline-color-selector-close" onclick="closeColorPalette()">×</button> --}}
            </div>

            <div class="inline-color-grid">
                @foreach ($colors as $c)
                    <div class="inline-color-item">
                        <div class="color-box modern-box"
                            style="background:{{ $c->code }};"
                            onclick="togglePaletteColor(this,'{{ $c->code }}')">
                        </div>
                        <div class="color-name">{{ $c->name }}</div>
                    </div>
                @endforeach
             {{-- <div class="custom-color-panel">
    <div class="custom-title">Other / Custom Color</div>

    <div class="custom-picker-row">
        <input type="color" id="customColorPicker" value="#00c853">

        <input type="text"
               id="customColorName"
               placeholder="Color name"
               class="custom-color-name">
    </div>

    <button type="button" class="add-custom-color-btn" onclick="addCustomColorToPalette()">
        + Add This Color
    </button>

    <div id="customAddedColors" class="custom-added-colors"></div>
</div> --}}
            </div>

            <div class="inline-color-selector-footer">
                <button class="apply-btn" onclick="applySelectedColors()">
                    <i class="fas fa-check"></i> Apply
                </button>
                <button class="cancel-btn" onclick="closeColorPalette()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- GRADIENT PANEL -->
    <div id="gradientPanel" class="tool-page" style="display:none;padding:20px;">

        <div id="gradientPreview" class="gradient-preview-box">
            <div class="gradient-display" id="gradientDisplay"></div>
            <div class="gradient-slider-track">
                <div id="stopMarkers" class="stop-markers-container"></div>
            </div>
        </div>

        <div class="grad-row">
            <button id="linearBtn" class="fill-btn active" onclick="setGradientType('linear')">
                <i class="fas fa-arrows-alt-h"></i> Linear
            </button>
            <button id="radialBtn" class="fill-btn" onclick="setGradientType('radial')">
                <i class="fas fa-circle"></i> Radial
            </button>
        </div>

        <div class="gradient-main-row">

            <div class="angle-section">
                <div id="angleControls">
                    <label style="font-weight:600;display:block;margin-bottom:10px;">
                        Angle: <span id="angleDisplay">90°</span>
                    </label>

                    <div class="angle-wheel-wrap">
                        <div class="angle-wheel" id="angleWheel">
                            <div class="angle-wheel-knob" id="angleWheelKnob"></div>

                            <div class="angle-wheel-center">
                                <input type="number" id="gradAngle" min="0" max="360" value="0"
                                    oninput="updateGradientAngle(this.value)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stops-section">
                <div class="stops-header-row">
                    <label style="font-weight:600;">Color Stops</label>

                    <div class="stops-header-actions">
                        <button onclick="addGradientStop()" class="add-stop-btn">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>
                </div>

                <div id="gradientStopsContainer"></div>
            </div>

        </div>
    </div>
</div>


<style>
    .fill-btn {
        flex: 1;
        padding: 12px;
        border: 2px solid #ddd;
        background: #fff;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .fill-btn.active {
        background: #000;
        color: #fff;
        border-color: #000;
    }

    .fill-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .grad-row {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    /* SOLID PANEL */
    #solidPanel {
        position: relative;
        /* min-height: 520px; */
    }

    .color-wheel-container {
        display: block;
    }

    .inline-color-selector {
        display: none;
        height: 100%;
        min-height: 520px;
        animation: fadeInRight .22s ease;
    }

    .inline-color-selector.open {
        display: flex;
        flex-direction: column;
    }

    .inline-color-selector-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 14px 10px;
    }

    .inline-color-selector-title {
        font-size: 16px;
        font-weight: 700;
        color: #111;
    }

    .inline-color-selector-close {
        width: 30px;
        height: 30px;
        border: none;
        background: #f1f1f1;
        border-radius: 6px;
        cursor: pointer;
        font-size: 20px;
        line-height: 1;
    }

    .inline-color-grid {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 10px 8px;
        align-content: start;
    }

    .inline-color-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .color-name {
        font-size: 10px;
        margin-top: 4px;
        text-align: center;
        line-height: 1.2;
        font-weight: 500;
    }

    .modern-box {
        width: 34px;
        height: 34px;
        border-radius: 4px;
        cursor: pointer;
        transition: .2s;
        border: 1px solid #cfcfcf;
        box-sizing: border-box;
    }

    .modern-box:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
    }

    .modern-box.selected {
        border: 2px solid #000;
        box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.08);
    }

    .inline-color-selector-footer {
        display: flex;
        gap: 10px;
        padding: 10px 12px 12px;
    }

    .apply-btn,
    .cancel-btn {
        flex: 1;
        padding: 11px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all .2s ease;
    }

    .apply-btn {
        background: #000;
        color: #fff;
    }

    .cancel-btn {
        background: #f2f2f2;
        color: #111;
    }

    .apply-btn:hover,
    .cancel-btn:hover {
        transform: translateY(-1px);
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(16px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* GRADIENT LAYOUT */
    .gradient-main-row {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .angle-section {
        width: 130px;
        flex-shrink: 0;
    }

    .stops-section {
        flex: 1;
        min-width: 0;
    }

    #gradientStopsContainer {
        max-height: 220px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .stop-position-input {
        width: 50px;
        padding: 6px;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-align: center;
        font-weight: 600;
    }

    /* PREVIEW */
    .gradient-preview-box {
        width: 100%;
        margin-bottom: 20px;
        overflow: hidden;
        background: #fff;
    }

    .gradient-display {
        width: 100%;
        height: 50px;
        position: relative;
    }

    .gradient-slider-track {
        width: 100%;
        height: 40px;
        background: #f5f5f5;
        position: relative;
    }

    .stop-markers-container {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .stop-marker {
        position: absolute;
        top: 50%;
        width: 22px;
        height: 22px;
        transform: translate(-50%, -50%);
        cursor: grab;
        z-index: 10;
    }

    .stop-marker:active {
        cursor: grabbing;
        transform: translate(-50%, -50%) scale(1.15);
    }

    .stop-marker::before {
        content: '';
        position: absolute;
        inset: 0;
        background: inherit;
        border: 3px solid #ffffff;
        border-radius: 4px;
        transform: rotate(45deg);
        transition: all 0.18s ease;
    }

    .stop-marker::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -20px;
        width: 2px;
        height: 20px;
        background: #000000;
        transform: translateX(-50%);
        border-radius: 1px;
        pointer-events: none;
    }

    .stop-marker:hover::before {
        transform: rotate(45deg) scale(1.1);
    }

    .stop-marker.active::before,
    .stop-marker.selected::before {
        border-color: #000000;
        border-width: 4px;
        box-shadow:
            0 0 0 4px rgba(0, 0, 0, 0.2),
            0 6px 16px rgba(0, 0, 0, 0.5);
    }

    /* STOPS */
    .stops-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        gap: 8px;
    }

    .stops-header-actions {
        display: flex;
        gap: 6px;
    }

    .gradient-stop-item {
        padding: 4px 0;
        margin-bottom: 6px;
        background: transparent;
        border: none;
    }

    .gradient-stop-single-row {
        display: flex;
        align-items: center;
        gap: 6px;
        width: 100%;
        flex-wrap: nowrap;
    }

    .manual-stop-color-box.compact {
        width: 18px;
        height: 18px;
        min-width: 18px;
        border-radius: 3px;
        border: 1px solid #000;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    .stop-wheel-swatch-row.compact {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-wrap: nowrap;
    }

    .wheel-stop-swatch.compact {
        width: 18px;
        height: 18px;
        min-width: 18px;
        border-radius: 3px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        box-sizing: border-box;
        line-height: 1;
    }

    .stop-position-input.compact {
        width: 40px;
        height: 22px;
        padding: 2px 4px;
        font-size: 12px;
        border: 1px solid #ccc;
        border-radius: 3px;
        text-align: center;
        font-weight: 600;
    }

    .pct-label {
        font-size: 11px;
        color: #666;
        min-width: 10px;
    }

    .remove-stop-btn.compact {
        width: 18px;
        height: 18px;
        min-width: 18px;
        border: none;
        background: none;
        color: #000;
        font-size: 14px;
        font-weight: bold;
        line-height: 1;
        cursor: pointer;
        padding: 0;
    }

    .remove-stop-btn {
        color: rgb(0, 0, 0);
        border: none;
        background: none;
        width: 28px;
        height: 28px;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        line-height: 1;
        flex-shrink: 0;
    }

    .add-stop-btn {
        background: #000000;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 12px;
        transition: all 0.3s;
    }

    .add-stop-btn:hover {
        background: #3a3a3a;
        transform: translateY(-2px);
    }

    /* ANGLE WHEEL */
    .angle-wheel-wrap {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 14px 0 18px;
    }

    .angle-wheel {
        position: relative;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: black;
        padding: 12px;
        box-sizing: border-box;
        cursor: pointer;
    }

    .angle-wheel::before {
        content: '';
        position: absolute;
        inset: 12px;
        border-radius: 50%;
        background: #bfbfbf;
    }

    .angle-wheel-center {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 60px;
        height: 30px;
        transform: translate(-50%, -50%);
        background: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 3;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .18);
    }

    .angle-wheel-center input {
        width: 100%;
        border: none;
        outline: none;
        text-align: center;
        font-size: 20px;
        font-weight: 700;
        background: transparent;
        padding: 0 6px;
        box-sizing: border-box;
    }

    .angle-wheel-knob {
        position: absolute;
        width: 16px;
        height: 16px;
        background: #000;
        border: 3px solid #fff;
        border-radius: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .25);
        z-index: 4;
        left: 50%;
        top: 8px;
        transform: translate(-50%, 0);
    }
.custom-color-panel {
    grid-column: 1 / -1;
    border: 1px dashed #999;
    border-radius: 8px;
    padding: 10px;
    margin-top: 8px;
    background: #f7f7f7;
}

.custom-title {
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 8px;
}

.custom-picker-row {
    display: flex;
    gap: 8px;
    align-items: center;
}

#customColorPicker {
    width: 58px;
    height: 38px;
    border: none;
    background: transparent;
    cursor: pointer;
}

.custom-color-name {
    flex: 1;
    height: 36px;
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 0 8px;
    font-size: 12px;
}

.add-custom-color-btn {
    width: 100%;
    margin-top: 8px;
    padding: 9px;
    border: none;
    border-radius: 6px;
    background: #000;
    color: #fff;
    font-weight: 700;
    cursor: pointer;
}

.custom-added-colors {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.custom-added-color {
    text-align: center;
    font-size: 10px;
}

.custom-added-color .color-box {
    margin: 0 auto 3px;
}
    @media (max-width: 600px) {
        .gradient-main-row {
            flex-direction: column;
        }

        .angle-section {
            width: 100%;
            text-align: center;
        }

        .gradient-stop-single-row {
            flex-wrap: wrap;
        }

        .inline-color-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media screen and (orientation: landscape) and (max-height: 500px) {
        .color-wheel-outer {
            position: relative !important;
            overflow: hidden !important;
            width: 160px !important;
            height: 160px !important;
        }

        .color-wheel-ring {
            position: relative !important;
            overflow: hidden !important;
            width: 160px !important;
            height: 160px !important;
            margin-top: 0 !important;
        }
    }


    /* =====================================================
       MOBILE / SHORT LANDSCAPE RESPONSIVE FIXES
       - Right panel parent handles vertical scrolling
       - Gradient color stops appear first
       - Angle wheel appears underneath
    ====================================================== */
    @media screen and (max-width: 1024px),
           screen and (orientation: landscape) and (max-height: 600px) {
        #gradientPanel {
            padding: 10px 10px 36px !important;
            overflow: visible !important;
        }

        .gradient-preview-box {
            margin-bottom: 10px !important;
        }

        .gradient-display {
            height: 38px !important;
        }

        .gradient-slider-track {
            height: 34px !important;
        }

        .grad-row {
            margin-bottom: 10px !important;
            gap: 6px !important;
        }

        .gradient-main-row {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }

        /* Add/Color Stops controls top par */
        .stops-section {
            order: 1 !important;
            width: 100% !important;
            min-width: 0 !important;
        }

        /* Angle wheel color stops ke neeche */
        .angle-section {
            order: 2 !important;
            width: 100% !important;
            text-align: center !important;
        }

        #gradientStopsContainer {
            max-height: none !important;
            overflow: visible !important;
            padding-right: 0 !important;
        }

        .gradient-stop-single-row {
            flex-wrap: wrap !important;
            row-gap: 7px !important;
        }

        .angle-wheel-wrap {
            margin: 8px 0 12px !important;
        }

        .inline-color-selector {
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow: visible !important;
        }

        .inline-color-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            overflow: visible !important;
            padding: 10px !important;
        }

        .inline-color-selector-footer {
            padding-bottom: 34px !important;
        }
    }

    /* =====================================================
       FINAL MOBILE FIX
       1) Apply / Cancel color list ke bilkul end mein normal flow mein aate hain
       2) Footer sticky/fixed nahi rehta
       3) Gradient angle wheel supports touch/pointer dragging
       Desktop layout remains unchanged.
    ====================================================== */
    @media screen and (max-width: 1024px),
           screen and (orientation: landscape) and (max-height: 600px) {

        #colorPage,
        #solidPanel {
            min-height: 0 !important;
        }

        .inline-color-selector.open {
            display: flex !important;
            flex-direction: column !important;
            min-height: 100% !important;
            height: auto !important;
            overflow: visible !important;
        }

        .inline-color-selector-header {
            flex: 0 0 auto !important;
            position: relative !important;
            z-index: 2 !important;
            background: #eeeeee !important;
        }

        .inline-color-grid {
            flex: 0 0 auto !important;
            min-height: 0 !important;
            overflow: visible !important;
            padding: 10px !important;
            align-content: start !important;
        }

        /*
         * Apply / Cancel sticky nahi rahenge.
         * Tamam colors ke baad normal flow mein aayenge, is liye user
         * scroll karke bilkul end par ja kar buttons click karega.
         */
        .inline-color-selector-footer {
            position: static !important;
            left: auto !important;
            right: auto !important;
            bottom: auto !important;
            z-index: auto !important;
            flex: 0 0 auto !important;
            width: 100% !important;
            box-sizing: border-box !important;
            margin: 4px 0 34px !important;
            padding: 4px 6px !important;
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .inline-color-selector-footer .apply-btn,
        .inline-color-selector-footer .cancel-btn {
            min-height: 38px !important;
            padding: 4px 10px !important;
        }

        /* Mobile gradient layout */
        #gradientPanel {
            touch-action: pan-y !important;
        }

        .angle-wheel {
            touch-action: none !important;
            -webkit-user-select: none !important;
            user-select: none !important;
        }

        .angle-wheel-knob {
            pointer-events: none !important;
        }

        .angle-wheel-center {
            z-index: 6 !important;
        }

        .angle-wheel-center input {
            touch-action: manipulation !important;
        }
    }
</style>


<script>
/* =========================================================
   MOBILE GRADIENT ANGLE TOUCH FIX
   Pointer drag se angle update hota hai. Existing desktop
   mouse behavior ko replace nahi karta.
========================================================= */
(function () {
    function normalizeAngle(value) {
        value = Math.round(Number(value) || 0) % 360;
        return value < 0 ? value + 360 : value;
    }

    function applyMobileGradientAngle(angle) {
        angle = normalizeAngle(angle);

        const input = document.getElementById('gradAngle');
        const display = document.getElementById('angleDisplay');

        if (input) input.value = angle;
        if (display) display.textContent = angle + '°';

        if (typeof window.updateGradientAngle === 'function') {
            window.updateGradientAngle(angle);
        } else if (input) {
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function angleFromPointer(wheel, clientX, clientY) {
        const rect = wheel.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;

        /*
         * Top = 0°, right = 90°, bottom = 180°, left = 270°
         * Existing wheel knob bhi top se start hota hai.
         */
        const radians = Math.atan2(clientY - centerY, clientX - centerX);
        return normalizeAngle((radians * 180 / Math.PI) + 90);
    }

    function bindMobileAngleWheel() {
        const wheel = document.getElementById('angleWheel');
        const input = document.getElementById('gradAngle');

        if (!wheel || wheel.dataset.mobileTouchBound === '1') return;
        wheel.dataset.mobileTouchBound = '1';

        let dragging = false;
        let activePointerId = null;

        const updateFromEvent = function (event) {
            if (!dragging) return;
            event.preventDefault();
            applyMobileGradientAngle(
                angleFromPointer(wheel, event.clientX, event.clientY)
            );
        };

        wheel.addEventListener('pointerdown', function (event) {
            /*
             * Number input par tap ho to keyboard/input ko normal kaam karne dein.
             */
            if (event.target.closest('.angle-wheel-center')) return;

            dragging = true;
            activePointerId = event.pointerId;

            try {
                wheel.setPointerCapture(activePointerId);
            } catch (error) {}

            event.preventDefault();
            applyMobileGradientAngle(
                angleFromPointer(wheel, event.clientX, event.clientY)
            );
        }, { passive: false });

        wheel.addEventListener('pointermove', updateFromEvent, { passive: false });

        const stopDragging = function (event) {
            if (!dragging) return;

            dragging = false;

            try {
                if (
                    activePointerId !== null &&
                    wheel.hasPointerCapture(activePointerId)
                ) {
                    wheel.releasePointerCapture(activePointerId);
                }
            } catch (error) {}

            activePointerId = null;

            if (event) event.preventDefault();
        };

        wheel.addEventListener('pointerup', stopDragging, { passive: false });
        wheel.addEventListener('pointercancel', stopDragging, { passive: false });
        wheel.addEventListener('lostpointercapture', function () {
            dragging = false;
            activePointerId = null;
        });

        if (input && input.dataset.mobileAngleBound !== '1') {
            input.dataset.mobileAngleBound = '1';

            const updateFromInput = function () {
                applyMobileGradientAngle(input.value);
            };

            input.addEventListener('input', updateFromInput);
            input.addEventListener('change', updateFromInput);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindMobileAngleWheel);
    } else {
        bindMobileAngleWheel();
    }

    /*
     * Gradient panel dynamic tab switching ke baad bhi binding confirm kar dein.
     */
    document.addEventListener('click', function (event) {
        if (
            event.target.closest('#gradientBtn') ||
            event.target.closest('#linearBtn') ||
            event.target.closest('#radialBtn')
        ) {
            setTimeout(bindMobileAngleWheel, 50);
        }
    });
})();
</script>

