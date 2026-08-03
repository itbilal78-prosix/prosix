<!-- Pattern Page -->
<div id="patternPage" class="tool-page" style="display:none;">
    <h1 class="pattern-section-title">Pattern Section</h1>

    <div class="pattern-action-row">
        <div class="pattern-top-buttons">
            <button class="custom-btn" data-text="SELECT PATTERN" onclick="openPatternLibrary()">
                <img src="/assets/images/pattern logo.avif" class="btn-logo" alt="Pattern">
            </button>

            <button class="custom-btn" data-text="SELECT MASCOT" onclick="openMascotTemplateModal()">
                <img src="/assets/images/bulldog.png" class="btn-logo" alt="Mascot">
            </button>
        </div>
    </div>

    <!-- Pattern Controls -->
    <div id="patternControls" class="pattern-settings-panel" style="display:none;">
        <div class="pattern-preview-row">
            <div id="patternPreviewBox" class="pattern-preview-box">
                <span style="color:#999;">No pattern applied</span>
            </div>

            <div id="patternColorPalette" class="pattern-color-palette"></div>
        </div>

        <div class="pattern-control-group">
            <label>SIZE</label>
            <input type="range" min="10" max="200" value="50" id="patternSize"
                oninput="updatePatternSize(this.value)">
            <div class="pattern-control-value"><span id="sizeValue">50</span></div>
        </div>

        <div class="pattern-control-group">
            <label>OPACITY</label>
            <input type="range" min="0" max="100" value="100" id="patternOpacity"
                oninput="updatePatternOpacity(this.value)">
            <div class="pattern-control-value"><span id="opacityValue">100</span>%</div>
        </div>

        <div class="pattern-control-group">
            <label>LEFT &amp; RIGHT</label>
            <input type="range" min="-800" max="800" value="0" id="patternLeft"
                oninput="movePattern('x', this.value)">
        </div>

        <div class="pattern-control-group">
            <label>TOP &amp; BOTTOM</label>
            <input type="range" min="-800" max="800" value="0" id="patternRight"
                oninput="movePattern('y', this.value)">
        </div>

        <div class="rotate-wrapper">
            <label class="rotate-label">Rotate</label>

            <div class="circular-slider" id="circularSlider">
                <div id="rotateKnob"></div>
                <div class="circle-inner">
                    <input type="number" id="angleValue" value="0" min="0" max="360"
                        oninput="updatePatternAngle(this.value)">
                </div>
            </div>
        </div>
    </div>

    <!-- Mascot Controls -->
    <div id="mascotControls" class="mascot-settings-panel" style="display:none;">
        <div class="mascot-preview-row">
            <div id="mascotPreviewBox" class="mascot-preview-box">
                <span style="color:#999;">No mascot applied</span>
            </div>

            <div id="mascotColorPalette" class="mascot-color-palette"></div>
        </div>

        <div class="pattern-control-group">
            <label>SIZE</label>
            <input type="range" min="10" max="200" value="50" id="mascotSize"
                oninput="updateMascotSize(this.value)">
            <div class="pattern-control-value"><span id="mascotSizeValue">50</span></div>
        </div>

        <div class="pattern-control-group">
            <label>OPACITY</label>
            <input type="range" min="0" max="100" value="100" id="mascotOpacity"
                oninput="updateMascotOpacity(this.value)">
            <div class="pattern-control-value"><span id="mascotOpacityValue">100</span>%</div>
        </div>

        <div class="pattern-control-group">
            <label>COUNT</label>
            <input type="range" min="1" max="10" value="4" id="mascotCount"
                oninput="updateMascotCount(this.value)">
            <div class="pattern-control-value"><span id="mascotCountValue">4</span></div>
        </div>
    </div>
</div>

<!-- Pattern Library Modal -->
<div id="patternLibraryModal" class="color-modal" style="display:none;">
    <div class="color-modal-content pattern-library-content">
        <div class="color-modal-header">
            <h3>Select Pattern</h3>
            <span class="color-modal-close" onclick="closePatternLibrary()">✕</span>
        </div>

        <div class="pattern-modal-body">
            <div id="patternList" class="pattern-library-grid">
                <!-- Patterns loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Mascot / Custom Pattern Upload Modal -->
<div id="mascotUploaderModal" class="color-modal" style="display:none;">
    <div class="color-modal-content mascot-uploader-content">
        <div class="color-modal-header">
            <h3>Upload Custom Pattern / Mascot</h3>
            <span class="color-modal-close" onclick="closeMascotUploader()">✕</span>
        </div>

        <div class="mascot-uploader-body">
            <p class="mascot-upload-copy">Upload your own SVG pattern or mascot logo</p>

            <input type="file" id="mascotFileInput" accept="image/*,.svg"
                onchange="mascotFileSelected(this)">

            <button type="button" class="mascot-file-button"
                onclick="document.getElementById('mascotFileInput').click()">
                📁 Choose SVG File
            </button>

            <div id="mascotPreview" class="mascot-upload-preview" style="display:none;">
                <p>Preview:</p>
                <div id="mascotPreviewImage" class="mascot-preview-image"></div>

                <button type="button" class="mascot-apply-button" onclick="applyMascotPattern()">
                    ✓ Apply to Selected Part
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mascot Template Modal -->
<div id="mascotTemplateModal" class="color-modal" style="display:none;">
    <div class="color-modal-content mascot-template-content">
        <div class="color-modal-header">
            <h3>Select Mascot Template</h3>
            <span class="color-modal-close" onclick="closeMascotTemplateModal()">✕</span>
        </div>

        <div class="mascot-template-body">
            <div id="mascotTemplateGrid" class="mascot-template-grid">Loading...</div>
        </div>
    </div>
</div>

<style>
.pattern-section-title {
    margin: 0 0 15px;
    text-align: center;
    font-size: 20px;
}

.pattern-action-row {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
}

.pattern-settings-panel,
.mascot-settings-panel {
    padding: 0 20px 20px;
    margin-top: 20px;
    border-radius: 12px;
    box-sizing: border-box;
}

.mascot-settings-panel {
    padding-top: 20px;
}

.pattern-preview-row,
.mascot-preview-row {
    display: flex;
    gap: 15px;
    align-items: flex-start;
    margin-bottom: 14px;
}

.pattern-preview-box {
    width: 120px;
    height: 100px;
    flex: 0 0 120px;
    border: 1px solid #000;
    border-radius: 8px;
    overflow: hidden;
}

.pattern-color-palette {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.mascot-preview-box {
    width: 150px;
    height: 100px;
    flex: 0 0 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    text-align: center;
    border-radius: 8px;
    background: #f0f0f0;
}

.mascot-color-palette {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.pattern-control-group {
    margin-bottom: 10px;
}

.pattern-control-group label {
    display: block;
    margin-bottom: 4px;
    font-size: 14px;
    font-weight: 600;
}

.pattern-control-group input[type="range"] {
    width: 100%;
}

.pattern-control-value {
    text-align: right;
    font-size: 12px;
}

.circle-inner input {
    width: 55px;
    height: 40px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 6px;
    text-align: center;
    font-weight: 600;
}

.pattern-library-content {
    width: 1000px;
    max-height: 80vh;
    overflow: hidden;
}

.pattern-modal-body {
    padding: 20px;
    overflow-y: auto;
}

.pattern-library-grid {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 15px;
}

.mascot-uploader-content {
    width: 500px;
    overflow: hidden;
}

.mascot-uploader-body {
    padding: 30px;
    overflow-y: auto;
    text-align: center;
}

.mascot-upload-copy {
    margin-bottom: 20px;
    color: #666;
}

#mascotFileInput {
    max-width: 100%;
}

.mascot-file-button {
    padding: 15px 30px;
    border: 0;
    border-radius: 8px;
    background: #007bff;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
}

.mascot-upload-preview {
    min-height: 100px;
    margin-top: 20px;
}

.mascot-upload-preview > p {
    margin-bottom: 10px;
    font-weight: 600;
}

.mascot-preview-image {
    display: flex;
    align-items: center;
    justify-content: center;
    max-height: 200px;
    padding: 20px;
    overflow: auto;
    border-radius: 8px;
    background: #f5f5f5;
}

.mascot-apply-button {
    margin-top: 15px;
    padding: 10px 25px;
    border: 0;
    border-radius: 6px;
    background: #28a745;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
}

.mascot-template-content {
    width: 900px;
    max-height: 80vh;
    overflow: hidden;
}

.mascot-template-body {
    padding: 20px;
    overflow-y: auto;
}

.mascot-template-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 15px;
}

/* =============================================================
   MOBILE / TABLET LANDSCAPE ONLY
   Desktop remains unchanged.
============================================================= */
@media screen and (max-width: 1024px) {
    #patternPage {
        min-height: 0 !important;
        padding-bottom: 44px !important;
    }

    .pattern-section-title {
        margin-bottom: 8px;
        font-size: 15px;
    }

    .pattern-action-row {
        margin-bottom: 10px;
    }

    .pattern-top-buttons {
        width: 100%;
    }

    .pattern-settings-panel,
    .mascot-settings-panel {
        min-height: 0 !important;
        margin-top: 8px !important;
        padding: 8px 10px 52px !important;
        overflow: visible !important;
    }

    .pattern-preview-row,
    .mascot-preview-row {
        gap: 8px;
        margin-bottom: 8px;
    }

    .pattern-preview-box,
    .mascot-preview-box {
        width: 90px;
        height: 72px;
        flex-basis: 90px;
    }

    .pattern-control-group {
        margin-bottom: 6px;
    }

    .pattern-control-group label {
        font-size: 11px;
    }

    .rotate-wrapper {
        margin-bottom: 12px;
    }

    /* Modal is smaller, centered, and fully scrollable */
    #patternLibraryModal,
    #mascotTemplateModal,
    #mascotUploaderModal {
        position: fixed !important;
        inset: 8px !important;
        z-index: 10050 !important;
        padding: 8px !important;
        overflow: hidden !important;
        align-items: center !important;
        justify-content: center !important;
    }

    #patternLibraryModal .color-modal-content,
    #mascotTemplateModal .color-modal-content,
    #mascotUploaderModal .color-modal-content {
        width: min(88%, 650px) !important;
        max-width: 650px !important;
        height: min(82%, 310px) !important;
        max-height: calc(100% - 12px) !important;
        display: flex !important;
        flex-direction: column !important;
        margin: auto !important;
        overflow: hidden !important;
        border-radius: 10px !important;
    }

    #mascotUploaderModal .color-modal-content {
        width: min(78%, 500px) !important;
    }

    #patternLibraryModal .color-modal-header,
    #mascotTemplateModal .color-modal-header,
    #mascotUploaderModal .color-modal-header {
        flex: 0 0 auto !important;
        min-height: 38px !important;
        padding: 8px 12px !important;
    }

    #patternLibraryModal .color-modal-header h3,
    #mascotTemplateModal .color-modal-header h3,
    #mascotUploaderModal .color-modal-header h3 {
        margin: 0 !important;
        font-size: 15px !important;
    }

    .pattern-modal-body,
    .mascot-template-body,
    .mascot-uploader-body {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        padding: 10px !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }

    .pattern-library-grid {
        grid-template-columns: repeat(5, minmax(82px, 1fr)) !important;
        gap: 8px !important;
        max-height: none !important;
        overflow: visible !important;
    }

    .mascot-template-grid {
        grid-template-columns: repeat(4, minmax(86px, 1fr)) !important;
        gap: 8px !important;
        max-height: none !important;
        overflow: visible !important;
    }

    .mascot-file-button {
        padding: 10px 18px;
        font-size: 13px;
    }

    .mascot-preview-image {
        max-height: 110px;
        padding: 10px;
    }

    .mascot-apply-button {
        margin-bottom: 12px;
    }
}

@media screen and (max-width: 720px) {
    .pattern-library-grid {
        grid-template-columns: repeat(4, minmax(76px, 1fr)) !important;
    }

    .mascot-template-grid {
        grid-template-columns: repeat(3, minmax(78px, 1fr)) !important;
    }
}
</style>
