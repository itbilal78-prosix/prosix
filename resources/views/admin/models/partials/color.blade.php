<div id="colorPage" class="tool-page">

    <!-- SOLID / GRADIENT SWITCH (NOW LOCAL TO COLOR PAGE) -->
    <div id="fillSwitch" style="display:flex;gap:10px;margin-bottom:15px;">
        <button id="solidBtn" class="fill-btn active" onclick="setFillType('solid')">
            <i class="fas fa-fill-drip"></i> Solid
        </button>
        <button id="gradientBtn" class="fill-btn" onclick="setFillType('gradient')">
            <i class="fas fa-fill"></i> Gradient
        </button>
    </div>

    <!-- COLOR WHEEL -->
    <div class="color-wheel-container">
        <div class="color-wheel-outer">
            <div class="color-wheel-ring" id="colorWheelRing"></div>
            <div class="color-wheel-white-ring"></div>
            <div class="color-wheel-center" id="selectedColorBtn" onclick="openColorPalette()"
                style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:8px;">
                SELECT <br> CUSTOM<br>COLORS
            </div>
        </div>
    </div>
    <div id="gradientPanel" class="tool-page" style="display:none;padding:20px;">

        <!-- GRADIENT PREVIEW WITH DRAGGABLE MARKERS -->
        <div id="gradientPreview" class="gradient-preview-box">
            <div class="gradient-display" id="gradientDisplay"></div>
            <div class="gradient-slider-track">
                <div id="stopMarkers" class="stop-markers-container"></div>
            </div>
        </div>

        <!-- TYPE -->
        <div class="grad-row">
            <button id="linearBtn" class="fill-btn active" onclick="setGradientType('linear')">
                <i class="fas fa-arrows-alt-h"></i> Linear
            </button>
            <button id="radialBtn" class="fill-btn" onclick="setGradientType('radial')">
                <i class="fas fa-circle"></i> Radial
            </button>
        </div>

        <!-- ANGLE (Linear only) -->
        <div id="angleControls">
            <label style="font-weight:600;display:block;margin-bottom:8px;">
                Angle: <span id="angleDisplay">90°</span>
            </label>
            <div class="angle-slider-container">
                <input type="range" min="0" max="360" value="90" id="gradAngle"
                    class="modern-angle-slider" oninput="updateGradientAngle(this.value)">
            </div>
        </div>

        <!-- COLOR STOPS LIST -->
        <div style="margin-bottom:15px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                <label style="font-weight:600;">Color Stops</label>
                <button onclick="addGradientStop()" class="add-stop-btn">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>

            <div id="gradientStopsContainer"></div>
        </div>

    </div>
</div>




<div id="colorPaletteModal" class="color-modal" style="display:none;">
    <div class="color-modal-content modern-color-modal">

        <h3 class="modal-title">🎨 Pick Your Colors</h3>

        <div class="color-grid modern-grid">
            @foreach ($colors as $c)
                <div class="color-box modern-box"
                    style="background:{{ $c->code }};@if (strtoupper($c->code) == '#FFFFFF') border:1px solid #ccc; @endif"
                    onclick="togglePaletteColor(this,'{{ $c->code }}')">
                </div>
            @endforeach
        </div>

        <div class="modal-footer">
            <button class="apply-btn" onclick="applySelectedColors()">
                <i class="fas fa-check"></i> Apply
            </button>
            <button class="cancel-btn" onclick="closeColorPalette()">
                <i class="fas fa-times"></i> Cancel
            </button>
        </div>

    </div>
</div>

<style>
    /* BUTTONS */
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

    /* GRADIENT STOPS - HORIZONTAL LAYOUT */

    .gradient-stop-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 8px;
    }

    .stop-color {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        cursor: pointer;
        border: 2px solid #ddd;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .stop-color:hover {
        transform: scale(1.1);
        border-color: #000;
    }

    .stop-position-input {
        width: 50px;
        padding: 6px;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-align: center;
        font-weight: 600;
    }




    /* STOP MARKERS ON PREVIEW */
    .stop-marker {
        position: absolute;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
        bottom: 4px;
        transform: translateX(-6px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        cursor: pointer;
    }

    /* MODAL */
    .modern-color-modal {
        width: 420px;
        padding: 25px;
        border-radius: 18px;
        background: white;
        box-shadow: 0 25px 60px rgba(0, 0, 0, .45);
        animation: fadeUp .3s ease;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-title {
        text-align: center;
        font-weight: 700;
        margin-bottom: 20px;
        font-size: 18px;
    }

    .modern-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .modern-box {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        cursor: pointer;
        transition: .2s;
        border: 2px solid transparent;
    }

    .modern-box:hover {
        transform: scale(1.15);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .modern-box.selected {
        border-color: #000;
        box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.1);
    }

    .modal-footer {
        display: flex;
        gap: 10px;
    }

    .apply-btn {
        flex: 1;
        background: #111;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .apply-btn:hover {
        background: #000;
        transform: translateY(-2px);
    }

    .cancel-btn {
        flex: 1;
        background: #eee;
        padding: 12px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }

    .cancel-btn:hover {
        background: #ddd;
    }

    /* GRADIENT PREVIEW BOX */
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

    /* ─── DIAMOND (BOX TYPE) STOP MARKER + BLACK CONNECTOR LINE ─── */
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

    /* Diamond shape (rotated square) */
    .stop-marker::before {
        content: '';
        position: absolute;
        inset: 0;
        background: inherit;
        /* color from JS */
        border: 3px solid #ffffff;
        border-radius: 4px;
        transform: rotate(45deg);

        transition: all 0.18s ease;
    }

    /* BLACK CONNECTOR LINE (from diamond center down to track) */
    .stop-marker::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -20px;
        /* line length */
        width: 2px;
        height: 20px;
        /* adjust if needed */
        background: #000000;
        transform: translateX(-50%);
        border-radius: 1px;
        pointer-events: none;
    }

    /* Hover effect */
    .stop-marker:hover::before {
        transform: rotate(45deg) scale(1.1);
    }

    /* Active / Selected state */
    .stop-marker.active::before,
    .stop-marker.selected::before {
        border-color: #000000;
        border-width: 4px;
        box-shadow:
            0 0 0 4px rgba(0, 0, 0, 0.2),
            0 6px 16px rgba(0, 0, 0, 0.5);
    }



    /* ANGLE SLIDER */
    .angle-slider-container {
        padding: 15px 0;
        margin-bottom: 15px;
    }

    .modern-angle-slider {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        height: 6px;
        border-radius: 3px;
        background: #e0e0e0;
        outline: none;
    }

    .modern-angle-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 20px;
        height: 20px;
        background: #000;
        border: 3px solid #fff;
        border-radius: 4px;
        cursor: grab;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
        transition: all 0.15s;
    }

    .modern-angle-slider::-moz-range-thumb {
        width: 20px;
        height: 20px;
        background: #000;
        border: 3px solid #fff;
        border-radius: 4px;
        cursor: grab;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
    }

    .modern-angle-slider::-webkit-slider-thumb:hover {
        transform: scale(1.15);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.35);
    }

    .modern-angle-slider:active::-webkit-slider-thumb {
        cursor: grabbing;
    }

    /* COLOR STOPS LIST */
    .gradient-stop-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 8px;
        transition: all 0.2s;
    }

    .gradient-stop-item:hover {
        background: #e9ecef;
    }

    .gradient-stop-item.active {
        background: #dee2e6;
        border: 2px solid #000;
    }

    .stop-color {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        cursor: pointer;
        border: 2px solid #ddd;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .stop-color:hover {
        transform: scale(1.1);
        border-color: #000;
    }

    .stop-position-display {
        font-weight: 600;
        font-size: 13px;
        color: #333;
        min-width: 40px;
    }

    .remove-stop-btn {
        background: #000000;
        color: white;
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        line-height: 1;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .remove-stop-btn:hover {
        background: #272727;
        transform: scale(1.1);
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
</style>
