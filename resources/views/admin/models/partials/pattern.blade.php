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
    <div id="patternControls" style="display:none;  padding: 0px 20px 20px 20px; border-radius:12px;  margin-top:20px;">

        <!-- Pattern Preview -->
        <div style="display:flex; gap:15px; align-items:flex-start;">

            <div id="patternPreviewBox"
                style="width:120px;height:100px;border:1px solid #000;
border-radius:8px;overflow:hidden;">
                <span style="color:#999;">No pattern applied</span>
            </div>

            <div id="patternColorPalette" style="flex:1;display:flex;flex-direction:column;gap:12px;">
            </div>

        </div>

        <!-- Size Slider -->
        <div style="margin-bottom:2px;">
            <label style="font-weight:600; font-size:14px;">SIZE</label>
            <input type="range" min="10" max="200" value="50" id="patternSize"
                oninput="updatePatternSize(this.value)" style="width:100%;">
            <div style="text-align:right; font-size:12px;"><span id="sizeValue">50</span></div>
        </div>

        <!-- Opacity Slider -->
        <div style="margin-bottom:2px;">
            <label style="font-weight:600; font-size:14px;">OPACITY</label>
            <input type="range" min="0" max="100" value="100" id="patternOpacity"
                oninput="updatePatternOpacity(this.value)" style="width:100%;">
            <div style="text-align:right; font-size:12px;"><span id="opacityValue">100</span>%</div>
        </div>
        <div style="margin-bottom:2px;">
            <label style="font-weight:600;font-size:14px;">LEFT & Right</label>

            <input type="range" min="-800" max="800" value="0" id="patternLeft"
                oninput="movePattern('x', this.value)" style="width:100%;">
        </div>


        <div style="margin-bottom:2px;">
            <label style="font-weight:600;font-size:14px;">Top & Bottom</label>

            <input type="range" min="-800" max="800" value="0" id="patternRight"
                oninput="movePattern('y', this.value)" style="width:100%;">
        </div>

        <div class="rotate-wrapper">

            <label class="rotate-label">Rotate</label>

            <div class="circular-slider" id="circularSlider">
                <div id="rotateKnob"></div>

                <div class="circle-inner">
                    <input type="number" id="angleValue" value="0" min="0" max="360"
                        style="
width:55px;
height:40px;
text-align:center;
border-radius:6px;
border:1px solid #ccc;
font-weight:600;
"
                        oninput="updatePatternAngle(this.value)">
                </div>
            </div>

        </div>


    </div>






    <!-- Mascot Controls (Hidden by default) -->
    <div id="mascotControls" style="display:none;  padding:20px; margin-top:20px;">


        <!-- Mascot Preview -->
        <div style="display:flex; gap:15px; align-items:center;">

            <!-- Mascot Preview Box -->
            <div id="mascotPreviewBox"
                style="text-align:center; height:100px; width:150px; background:#f0f0f0; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                <span style="color:#999;">No mascot applied</span>
            </div>

            <!-- Mascot Color Palette -->
            <div id="mascotColorPalette" style="display:flex; flex-direction:column; gap:10px;">
            </div>

        </div>

        <!-- Size Slider -->
        <div style="margin-bottom:12px;">
            <label style="font-weight:600; font-size:14px;">SIZE</label>
            <input type="range" min="10" max="200" value="50" id="mascotSize"
                oninput="updateMascotSize(this.value)" style="width:100%;">
            <div style="text-align:right; font-size:12px;"><span id="mascotSizeValue">50</span></div>
        </div>

        <!-- Opacity Slider -->
        <div style="margin-bottom:12px;">
            <label style="font-weight:600; font-size:14px;">OPACITY</label>
            <input type="range" min="0" max="100" value="100" id="mascotOpacity"
                oninput="updateMascotOpacity(this.value)" style="width:100%;">
            <div style="text-align:right; font-size:12px;"><span id="mascotOpacityValue">100</span>%</div>
        </div>

        <!-- Count Slider (تعداد) -->
        <div style="margin-bottom:12px;">
            <label style="font-weight:600; font-size:14px;">COUNT (تعداد)</label>
            <input type="range" min="1" max="10" value="4" id="mascotCount"
                oninput="updateMascotCount(this.value)" style="width:100%;">
            <div style="text-align:right; font-size:12px;"><span id="mascotCountValue">4</span></div>
        </div>

    </div>
</div>

<!-- Pattern Library Modal -->
<div id="patternLibraryModal" class="color-modal"  style="display:none;">
    <div class="color-modal-content" style="width:1000px; max-height:80vh; overflow-y:auto;">
        <div class="color-modal-header">
            <h3>Select Pattern</h3>
            <span class="color-modal-close" onclick="closePatternLibrary()">✕</span>
        </div>

        <div style="padding:20px;">
            <div id="patternList"
                style="display:grid;
grid-template-columns:repeat(6,1fr);
gap:15px;
max-height:60vh;
overflow-y:auto;">

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


            <button onclick="document.getElementById('mascotFileInput').click()"
                style="padding:15px 30px; background:#007bff; color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer; font-size:16px;">
                📁 Choose SVG File
            </button>

            <div id="mascotPreview" style="margin-top:20px; min-height:100px; display:none;">
                <p style="font-weight:600; margin-bottom:10px;">Preview:</p>
                <div id="mascotPreviewImage"
                    style="max-height:200px; display:flex; align-items:center; justify-content:center; background:#f5f5f5; border-radius:8px; padding:20px;">
                </div>

                <button onclick="applyMascotPattern()"
                    style="margin-top:15px; padding:10px 25px; background:#28a745; color:white; border:none; border-radius:6px; font-weight:600; cursor:pointer;">
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

        <div style="padding:20px; max-height:65vh; overflow-y:auto;">
            <div id="mascotTemplateGrid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:15px;">
                Loading...
            </div>
        </div>
    </div>
</div>
