<!-- Pattern Page -->
<div id="patternPage" class="tool-page" style="display:none;">
    <h1 style="margin-bottom:15px; text-align:center; font-size:20px"> Pattern Section</h1>

    <!-- Top Buttons -->
    <div style="display:flex; gap:12px; margin-bottom:20px;">
 <div class="pattern-top-buttons">

<button class="custom-btn" data-text="SELECT PATTERN" onclick="openPatternLibrary()">
    <img src="/assets/images/pattern logo.avif" class="btn-logo">
</button>

<button class="custom-btn" data-text="SELECT MASCOT" onclick="openMascotTemplateModal()">
    <img src="/assets/images/bulldog.png" class="btn-logo">
</button>

</div>


    </div>

    <!-- Pattern Controls (Hidden by default) -->
    <div id="patternControls" style="display:none; background:#fff; padding:20px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.25); margin-top:20px;">

        <!-- Pattern Preview -->
        <div id="patternPreviewBox" style="text-align:center; margin-bottom:20px; height:100px; background:#f0f0f0; border-radius:8px; display:flex; align-items:center; justify-content:center;">
            <span style="color:#999;">No pattern applied</span>
        </div>

        <!-- Color Palette for Pattern Colors -->
        <div id="patternColorPalette" style="margin: 15px auto; max-width: 360px; display: flex; flex-direction: column; gap: 12px; padding: 10px;"></div>

        <!-- Size Slider -->
        <div style="margin-bottom:12px;">
            <label style="font-weight:600; font-size:14px;">SIZE</label>
            <input type="range" min="10" max="200" value="50" id="patternSize" oninput="updatePatternSize(this.value)" style="width:100%;">
            <div style="text-align:right; font-size:12px;"><span id="sizeValue">50</span></div>
        </div>

        <!-- Opacity Slider -->
        <div style="margin-bottom:12px;">
            <label style="font-weight:600; font-size:14px;">OPACITY</label>
            <input type="range" min="0" max="100" value="100" id="patternOpacity" oninput="updatePatternOpacity(this.value)" style="width:100%;">
            <div style="text-align:right; font-size:12px;"><span id="opacityValue">100</span>%</div>
        </div>



        <!-- Remove Button -->

    </div>






    <!-- Mascot Controls (Hidden by default) -->
<div id="mascotControls" style="display:none; background:#fff; padding:20px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.25); margin-top:20px;">

    <!-- Mascot Preview -->
    <div id="mascotPreviewBox" style="text-align:center; margin-bottom:20px; height:100px; background:#f0f0f0; border-radius:8px; display:flex; align-items:center; justify-content:center;">
        <span style="color:#999;">No mascot applied</span>
    </div>

    <!-- Size Slider -->
    <div style="margin-bottom:12px;">
        <label style="font-weight:600; font-size:14px;">SIZE</label>
        <input type="range" min="10" max="200" value="50" id="mascotSize" oninput="updateMascotSize(this.value)" style="width:100%;">
        <div style="text-align:right; font-size:12px;"><span id="mascotSizeValue">50</span></div>
    </div>

    <!-- Opacity Slider -->
    <div style="margin-bottom:12px;">
        <label style="font-weight:600; font-size:14px;">OPACITY</label>
        <input type="range" min="0" max="100" value="100" id="mascotOpacity" oninput="updateMascotOpacity(this.value)" style="width:100%;">
        <div style="text-align:right; font-size:12px;"><span id="mascotOpacityValue">100</span>%</div>
    </div>

    <!-- Count Slider (تعداد) -->
    <div style="margin-bottom:12px;">
        <label style="font-weight:600; font-size:14px;">COUNT (تعداد)</label>
        <input type="range" min="1" max="10" value="4" id="mascotCount" oninput="updateMascotCount(this.value)" style="width:100%;">
        <div style="text-align:right; font-size:12px;"><span id="mascotCountValue">4</span></div>
    </div>

</div>
</div>

<!-- Pattern Library Modal -->
<div id="patternLibraryModal" class="color-modal" style="display:none;">
    <div class="color-modal-content" style="width:700px;">
        <div class="color-modal-header">
            <h3>Select Pattern</h3>
            <span class="color-modal-close" onclick="closePatternLibrary()">✕</span>
        </div>

        <div style="padding:20px;">
            <div id="patternList" style="display:grid; grid-template-columns:repeat(4,1fr); gap:15px;">
                <!-- Patterns will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>
<!-- Mascot/Custom Pattern Upload Modal -->
<div id="mascotUploaderModal" class="color-modal" style="display:none;">
    <div class="color-modal-content" style="width:500px;">
        <div class="color-modal-header">
            <h3>Upload Custom Pattern / Mascot</h3>
            <span class="color-modal-close" onclick="closeMascotUploader()">✕</span>
        </div>

        <div style="padding:30px; text-align:center;">
            <p style="margin-bottom:20px; color:#666;">Upload your own SVG pattern or mascot logo</p>

            {{-- <input type="file" id="mascotFileInput" accept=".svg,image/svg+xml" style="display:none;" onchange="handleMascotUpload(event)"> --}}
            <input type="file" id="mascotFileInput" accept="image/*,.svg" onchange="mascotFileSelected(this)">


            <button onclick="document.getElementById('mascotFileInput').click()" style="padding:15px 30px; background:#007bff; color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer; font-size:16px;">
                📁 Choose SVG File
            </button>

            <div id="mascotPreview" style="margin-top:20px; min-height:100px; display:none;">
                <p style="font-weight:600; margin-bottom:10px;">Preview:</p>
                <div id="mascotPreviewImage" style="max-height:200px; display:flex; align-items:center; justify-content:center; background:#f5f5f5; border-radius:8px; padding:20px;"></div>

                <button onclick="applyMascotPattern()" style="margin-top:15px; padding:10px 25px; background:#28a745; color:white; border:none; border-radius:6px; font-weight:600; cursor:pointer;">
                    ✓ Apply to Selected Part
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Mascot Template Modal -->
<div id="mascotTemplateModal" class="color-modal" style="display:none;">
    <div class="color-modal-content" style="width:900px; max-height:80vh; overflow:auto;">
        <div class="color-modal-header">
            <h3>Select Mascot Template</h3>
            <span class="color-modal-close" onclick="closeMascotTemplateModal()">✕</span>
        </div>

        <div style="padding:20px;">
            <div id="mascotTemplateGrid"
                 style="display:grid;grid-template-columns:repeat(4,1fr);gap:15px;">
                Loading...
            </div>
        </div>
    </div>
</div>
