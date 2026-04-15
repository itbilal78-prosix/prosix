<div id="colorPage" class="tool-page">

    <!-- SOLID / GRADIENT SWITCH (NOW LOCAL TO COLOR PAGE) -->
    <div id="fillSwitch" style="display:flex;gap:10px;margin-bottom:15px;">
        <button id="solidBtn" class="fill-btn active" onclick="setFillType('solid')">
            <i class="fas fa-fill-drip"></i> Solid
        </button>
        <button id="gradientBtn" class="fill-btn" onclick="setFillType('gradient')">
            <i class="fas fa-circle-half-stroke"></i> Gradient

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

     <div class="gradient-main-row">

    <!-- LEFT: ANGLE -->
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

    <!-- RIGHT: COLOR STOPS -->
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

<!-- COLOR BOTTOM BAR -->
<div id="colorBarBackdrop" onclick="closeColorPalette()"
     style="display:none; position:fixed; inset:0; z-index:299; background:rgba(0,0,0,0.3);"></div>

<div id="colorBottomBar" style="
    position: fixed;
    bottom: 0; left: 0; right: 0;
    background: #1a1a1a;
    border-radius: 20px 20px 0 0;
    padding: 16px 16px 24px;
    z-index: 300;
    transform: translateY(100%);
    transition: transform 0.4s cubic-bezier(0.32, 0.72, 0, 1);
    max-height: 60vh;
    overflow-y: auto;
    box-shadow: 0 -8px 40px rgba(0,0,0,0.5);
">
    <!-- Handle -->
    <div style="width:40px;height:4px;background:#444;border-radius:2px;margin:0 auto 16px;"></div>

    <div style="font-size:13px;color:#888;font-weight:600;margin-bottom:12px;">SELECT COLORS</div>

    <!-- Color Grid -->
    <div id="bottomBarColorGrid" style="
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 10px;
        margin-bottom: 16px;
    "></div>

    <!-- Buttons -->
    <div style="display:flex;gap:10px;">
        <button onclick="applySelectedColors()" style="
            flex:1; padding:13px; background:#fff; color:#000;
            border:none; border-radius:10px; font-weight:700;
            font-size:14px; cursor:pointer;">
            ✓ Apply
        </button>
        <button onclick="closeColorPalette()" style="
            flex:1; padding:13px; background:#333; color:#fff;
            border:none; border-radius:10px; font-weight:600;
            font-size:14px; cursor:pointer;">
            Cancel
        </button>
    </div>
</div>


<div id="colorPaletteModal" class="color-modal" style="display:none;">
    <div class="color-modal-content modern-color-modal">

        <h3 class="modal-title"> Pick Your Colors</h3>

        <div class="color-grid modern-grid">
            @foreach ($colors as $c)
                <div class="color-item">
                    <div class="color-box modern-box" style="background:{{ $c->code }};"
                        onclick="togglePaletteColor(this,'{{ $c->code }}')">
                    </div>

                    <div class="color-name">
                        {{ $c->name }}
                    </div>
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

.gradient-main-row {
    display: flex;
    gap: 10px;
    align-items: flex-start;
}

/* LEFT side */
.angle-section {
    width: 130px;
    flex-shrink: 0;
}

/* RIGHT side */
.stops-section {
    flex: 1;
    min-width: 0;
}

/* Stops scrollable bana do */
#gradientStopsContainer {
    max-height: 220px;
    overflow-y: auto;
    padding-right: 4px;
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
        width: 620px;
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
        grid-template-columns: repeat(8, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    .color-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .color-name {
        font-size: 12px;
        margin-top: 4px;
        text-align: center;
        font-weight: 500;
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
        gap: 8px;
        padding: 8px 10px;
        border-radius: 6px;
        margin-bottom: 8px;
        transition: all 0.2s;

        /* border: 1px solid #000; */
    }


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

.manual-btn {
    background: #444 !important;
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

#gradientStopsContainer {
    max-height: 220px;
    overflow-y: auto;
    padding-right: 2px;
}

@media (max-width: 600px) {
    .gradient-stop-single-row {
        flex-wrap: wrap;
    }
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
        color: rgb(0, 0, 0);
        border: none;
        background: none;
        /* background remove */
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

@media (max-width: 600px) {
    .gradient-main-row {
        flex-direction: column;
    }

    .angle-section {
        width: 100%;
        text-align: center;
    }
}
    @media screen and (orientation: landscape) and (max-height: 500px) {

        .color-wheel-outer {
            position: relative !important;
            overflow: hidden !important;
            /* ← YEH ZAROORI HAI */
            width: 160px !important;
            height: 160px !important;
        }

        .color-wheel-ring {
            position: relative !important;
            overflow: hidden !important;
            /* ← YEH BHI */
            width: 160px !important;
            height: 160px !important;
            margin-top: 0 !important;
        }

    }
</style>
