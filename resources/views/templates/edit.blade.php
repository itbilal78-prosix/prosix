@extends('layouts.dashboard')

@section('content')
    <div class="mascot-wrapper">

        <link rel="stylesheet" href="{{ asset('css/mascot.css') }}">

        @foreach (\App\Models\Font::all() as $font)
            @if ($font->file)
                <style>
                    @font-face {
                        font-family: 'font_{{ $font->id }}';
                        src: url('{{ asset('storage/' . $font->file) }}');
                    }
                </style>
            @endif
        @endforeach
        <div class="app-container">
            <!-- Header -->
            <header class="app-header">
                <h1>Welcome to Mascot Customizer</h1>
            </header>
<input type="hidden" id="editingTemplateId" value="{{ $template->id ?? '' }}">

            <div class="main-content">
                <!-- Left Sidebar - EDIT DESIGN -->
                <aside class="left-sidebar">
                    <h2 class="sidebar-title">EDIT DESIGN</h2>
                    <div class="sidebar-options">
                        <input type="file" id="imageInput" accept="image/*,.svg,image/svg+xml" hidden>
                        <button class="sidebar-option" onclick="document.getElementById('imageInput').click()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span>Upload Image</span>
                        </button>
                        <button class="sidebar-option" onclick="addText()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M4 7h16M4 12h16M4 17h10"></path>
                            </svg>
                            <span>Add Text</span>
                        </button>
                        <button class="sidebar-option" id="eraserToolBtn" onclick="toggleEraser()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <path d="M7 9l5-5 5 5"></path>
                                <path d="M12 4v12"></path>
                            </svg>
                            <span>Eraser Tool</span>
                        </button>
                        <button class="sidebar-option active" id="selectToolBtn" onclick="activateSelectTool()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M3 3l7 18 2-7 7-2z"></path>
                            </svg>
                            <span>Select Tool</span>
                        </button>


                    </div>
                </aside>

                <!-- Central Canvas Area -->
                <main class="canvas-area">
                    <!-- Toolbar -->
                    <div class="canvas-toolbar">
                        <button class="toolbar-btn" onclick="selectAll()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" stroke-dasharray="3 3">
                                </rect>
                            </svg>
                            <span>Select All</span>
                        </button>
                        <button class="toolbar-btn" onclick="duplicate()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                            <span>Duplicate</span>
                        </button>
                        <button class="toolbar-btn" onclick="toggleLayers()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                <polyline points="2 17 12 22 22 17"></polyline>
                                <polyline points="2 12 12 17 22 12"></polyline>
                            </svg>
                            <span>Layers</span>
                        </button>
                        <button class="toolbar-btn" onclick="undo()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M3 7v6h6"></path>
                                <path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"></path>
                            </svg>
                            <span>Undo</span>
                        </button>
                        <button class="toolbar-btn" onclick="redo()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 7v6h-6"></path>
                                <path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3L21 13"></path>
                            </svg>
                            <span>Redo</span>
                        </button>
                    </div>

                    <!-- Canvas -->
                    <div class="canvas-wrapper" id="canvasWrapper">
                        <!-- Layer controls: 3 icons at corners/middle - move with object in real-time -->
                        <button type="button" class="layer-ctrl-icon layer-ctrl-left" id="layerCtrlRemove"
                            style="display: none;" title="Remove layer" data-action="remove">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>
                        <button type="button" class="layer-ctrl-icon layer-ctrl-center" id="layerCtrlAngle"
                            style="display: none;" title="Rotate" data-action="angle">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 12a9 9 0 1 1-9-9c2.5 0 4.9 1 6.7 2.6" />
                                <path d="M21 3v6h-6" />
                            </svg>
                        </button>
                        <button type="button" class="layer-ctrl-icon layer-ctrl-right" id="layerCtrlGear"
                            style="display: none;" title="Options" data-action="gear">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="3" />
                                <path
                                    d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                            </svg>
                        </button>
                        <div class="loading-overlay-mascot" id="loadingOverlayMascot" style="display: none;">
                            <div class="loading-spinner-mascot"></div>
                            <p class="loading-text-mascot">Processing...</p>
                        </div>
                        <div class="canvas-center-guide" id="canvasCenterGuide" style="display: none;"
                            aria-hidden="true">
                            <div class="center-guide-line center-guide-vertical"></div>
                            <div class="center-guide-line center-guide-horizontal"></div>
                        </div>
                        <canvas id="designCanvas"></canvas>
                        <canvas id="hiddenCanvasMascot" style="display: none;"></canvas>
                        <!-- Save Button -->
                        <div class="save-section">
<button class="btn btn-dark" onclick="openSaveDesignDialog()">Save</button>
                        </div>
                    </div>
                </main>

                <!-- Right Sidebar: Layers panel (when Layers clicked) OR Object settings (when object selected) -->
                <aside class="right-sidebar" id="rightSidebar">
                    <div id="layersPanelSection">
                        <h2 class="sidebar-title">MANAGE LAYERS</h2>
                        <p class="layers-hint">Drag karke up/neechay le jayein — Top = front, Bottom = back</p>
                        <div class="layers-list" id="layersList">
                            <p class="empty-layers-text">No layers yet</p>
                        </div>
                    </div>
                    <div id="objectSettingsSection" style="display: none;">
                        <!-- Text settings - shown when a text layer is selected -->
                        <div class="text-settings-panel" id="textSettingsPanel" style="display: none;">
                            <h3 class="text-settings-title">Text Settings</h3>
                            <div class="text-settings-form">
                                <label>Edit text (text change karein)</label>
                                <textarea id="textContent" rows="3" placeholder="Yahan text likhein..."></textarea>
                                <label>Font style</label>
                                <select id="textFontStyle">
                                    <option value="normal">Normal</option>
                                    <option value="italic">Italic</option>
                                    <option value="oblique">Oblique</option>
                                </select>
                                <label>Text shape</label>
                                <div class="text-shape-dropdown" id="textShapeDropdown">
                                    <button type="button" class="text-shape-trigger" id="textShapeTrigger"
                                        aria-haspopup="listbox" aria-expanded="false">
                                        <span class="text-shape-trigger-icon" id="textShapeTriggerIcon"></span>
                                        <span class="text-shape-trigger-label" id="textShapeTriggerLabel">Normal</span>
                                    </button>
                                    <select id="textShape" class="text-shape-hidden-select" aria-hidden="true">
                                        <option value="normal">Normal</option>
                                        <option value="arch">Arch</option>
                                        <option value="arcDown">Arc Down</option>
                                        <option value="circle">Circle</option>
                                        <option value="wave">Wave</option>
                                        <option value="zigzag">Zigzag</option>
                                        <option value="curveUp">Curve Up</option>
                                        <option value="curveDown">Curve Down</option>
                                        <option value="semicircleTop">Semicircle Top</option>
                                        <option value="semicircleBottom">Semicircle Bottom</option>
                                        <option value="ellipse">Ellipse</option>
                                        <option value="fan">Fan</option>
                                        <option value="bulge">Bulge</option>
                                        <option value="pinch">Pinch</option>
                                        <option value="spiral">Spiral</option>
                                        <option value="rainbow">Rainbow</option>
                                    </select>
                                    <div class="text-shape-options" id="textShapeOptions" role="listbox">
                                        <div class="text-shape-option" data-value="normal" role="option"><span
                                                class="shape-icon">—</span> Normal</div>
                                        <div class="text-shape-option" data-value="arch" role="option"><span
                                                class="shape-icon">⌒</span> Arch</div>
                                        <div class="text-shape-option" data-value="arcDown" role="option"><span
                                                class="shape-icon">⌣</span> Arc Down</div>
                                        <div class="text-shape-option" data-value="circle" role="option"><span
                                                class="shape-icon">○</span> Circle</div>
                                        <div class="text-shape-option" data-value="wave" role="option"><span
                                                class="shape-icon">∿</span> Wave</div>
                                        <div class="text-shape-option" data-value="zigzag" role="option"><span
                                                class="shape-icon">⚡</span> Zigzag</div>
                                        <div class="text-shape-option" data-value="curveUp" role="option"><span
                                                class="shape-icon">⌒</span> Curve Up</div>
                                        <div class="text-shape-option" data-value="curveDown" role="option"><span
                                                class="shape-icon">⌣</span> Curve Down</div>
                                        <div class="text-shape-option" data-value="semicircleTop" role="option"><span
                                                class="shape-icon">◠</span> Semicircle Top</div>
                                        <div class="text-shape-option" data-value="semicircleBottom" role="option"><span
                                                class="shape-icon">◡</span> Semicircle Bottom</div>
                                        <div class="text-shape-option" data-value="ellipse" role="option"><span
                                                class="shape-icon">⬭</span> Ellipse</div>
                                        <div class="text-shape-option" data-value="fan" role="option"><span
                                                class="shape-icon">🪭</span> Fan</div>
                                        <div class="text-shape-option" data-value="bulge" role="option"><span
                                                class="shape-icon">◉</span> Bulge</div>
                                        <div class="text-shape-option" data-value="pinch" role="option"><span
                                                class="shape-icon">◎</span> Pinch</div>
                                        <div class="text-shape-option" data-value="spiral" role="option"><span
                                                class="shape-icon"> spiral</span> Spiral</div>
                                        <div class="text-shape-option" data-value="rainbow" role="option"><span
                                                class="shape-icon">🌈</span> Rainbow</div>
                                    </div>
                                </div>
                                <div class="text-settings-row">
                                    <div class="text-settings-field">
                                        <label>Text color</label>
                                        <input type="color" id="textFillColor" value="#000000" title="Text color">
                                    </div>
                                    <div class="text-settings-field">
                                        <label>Spacing</label>
                                        <input type="number" id="textCharSpacing" value="0" step="10"
                                            min="-500" max="500">
                                    </div>
                                </div>
                                <label>Font</label>

                                <button type="button" class="btn btn-dark" onclick="openBackendFontModal()">
                                    Select Font
                                </button>

                                <label>Size</label>
                                <input type="number" id="textFontSize" min="8" max="200" value="40">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="textBold"> Bold
                                </label>
                                <div class="text-settings-row">
                                    <div class="text-settings-field">
                                        <label>Outline</label>
                                        <input type="number" id="textStrokeWidth" min="0" max="20"
                                            value="0" placeholder="Width">
                                    </div>
                                    <div class="text-settings-field">
                                        <label>Outline color</label>
                                        <input type="color" id="textStrokeColor" value="#000000"
                                            title="Outline color">
                                    </div>
                                </div>
                                <label>Outline position</label>
                                <select id="textPaintFirst">
                                    <option value="fill">Outside</option>
                                    <option value="stroke">Inside</option>
                                </select>
                                <label>Line height</label>
                                <input type="number" id="textLineHeight" value="1.2" step="0.1" min="0.5"
                                    max="3">
                                <label>Text align</label>
                                <select id="textAlign">
                                    <option value="left">Left</option>
                                    <option value="center">Center</option>
                                    <option value="right">Right</option>
                                    <option value="justify">Justify</option>
                                </select>
                            </div>
                        </div>
                        <!-- Object/Image settings - shown when an image or vector layer is selected -->
                        <div class="object-settings-panel" id="objectSettingsPanel" style="display: none;">
                            <h3 class="object-settings-title">Object / Image Settings</h3>
                            <div class="object-settings-form">
                                <label>Move</label>
                                <div class="move-buttons-row">
                                    <button type="button" class="move-btn move-btn-icon" id="moveLeftBtn"
                                        title="Left" aria-label="Move left">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <polyline points="15 18 9 12 15 6" />
                                        </svg>
                                    </button>
                                    <button type="button" class="move-btn move-btn-icon" id="moveRightBtn"
                                        title="Right" aria-label="Move right">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <polyline points="9 18 15 12 9 6" />
                                        </svg>
                                    </button>
                                    <button type="button" class="move-btn move-btn-icon" id="moveUpBtn" title="Up"
                                        aria-label="Move up">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <polyline points="18 15 12 9 6 15" />
                                        </svg>
                                    </button>
                                    <button type="button" class="move-btn move-btn-icon" id="moveDownBtn"
                                        title="Down" aria-label="Move down">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <polyline points="6 9 12 15 18 9" />
                                        </svg>
                                    </button>
                                </div>
                                <label>Opacity</label>
                                <input type="range" id="objectOpacity" min="0" max="100" value="100"
                                    step="5">
                                <span class="object-opacity-value" id="objectOpacityValue">100%</span>
                                <h4 class="object-colors-title">Colors</h4>
                                <div class="colors-grid-mascot" id="objectColorsGrid"></div>
                            </div>
                        </div>
                        <!-- Shown when sidebar is open but no object selected -->
                        <p class="right-panel-hint" id="rightPanelHint" style="display: none;">Select a layer to edit its
                            settings.</p>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Upload Image Module: LEFT = Original image, RIGHT = Edited image -->
        <div class="modal-overlay" id="uploadImageModal" style="display: none;">
            <div class="modal-content upload-modal-wide upload-module">
                <button type="button" class="upload-modal-close" id="uploadModalClose" onclick="closeUploadModule()"
                    title="Module band karein — image remove">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
                <h3 class="upload-module-title">Preview image</h3>
                <div class="upload-modal-two-col">
                    <!-- LEFT: Original image (humari uploaded image) -->
                    <div class="upload-modal-left">
                        <h4 class="upload-module-side-label">Original</h4>
                        <div class="upload-modal-original-wrap">
                            <img id="uploadModalOriginal" alt="Original" class="upload-modal-thumb">
                        </div>
                        <p class="upload-modal-color-count" id="uploadModalColorCount">Detected: <strong>0</strong>
                            colors.</p>
                        <div class="color-count-buttons-mascot upload-modal-color-count-btns"
                            id="uploadModalColorCountButtons">
                            <button class="color-count-btn-small" onclick="selectColorCount(1, this)">1</button>
                            <button class="color-count-btn-small" onclick="selectColorCount(2, this)">2</button>
                            <button class="color-count-btn-small" onclick="selectColorCount(3, this)">3</button>
                            <button class="color-count-btn-small" onclick="selectColorCount(4, this)">4</button>
                            <button class="color-count-btn-small" onclick="selectColorCount(5, this)">5</button>
                            <button class="color-count-btn-small" onclick="selectColorCount(6, this)">6</button>
                            <button class="color-count-btn-small" onclick="selectColorCount(7, this)">7</button>
                            <button class="color-count-btn-small" onclick="selectColorCount(8, this)">8+</button>
                        </div>
                    </div>
                    <!-- RIGHT: Edited image (jo hum edit karenge) -->
                    <div class="upload-modal-right">
                        <h4 class="upload-module-side-label">Edited</h4>
                        <div class="upload-modal-eraser-row">
                            <button type="button" class="btn btn-dark upload-modal-eraser-btn" id="uploadModalEraserBtn"
                                onclick="toggleUploadModalEraser()">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M20 20H7L2 15l8-8 5 5-5 5 5 5z" />
                                </svg>
                                <span>Eraser Tool</span>
                            </button>
                            <label class="upload-modal-eraser-size" id="uploadModalEraserSizeWrap"
                                style="display: none;">
                                Size: <input type="range" id="uploadModalEraserSize" min="5" max="80"
                                    value="30" onchange="setUploadModalEraserRadius(this.value)">
                                <span id="uploadModalEraserSizeVal">30</span>
                            </label>
                        </div>
                        <div class="upload-modal-preview-wrap upload-modal-preview-clickable" id="uploadModalPreviewWrap">
                            <img id="uploadModalPreview" alt="Edited Preview" class="upload-modal-preview-img">
                        </div>
                        <h4 class="upload-modal-colors-title">colors</h4>
                        <div class="colors-grid-mascot upload-modal-colors-grid upload-modal-colors-straight"
                            id="uploadModalColorsGrid"></div>
                    </div>
                </div>
                <div class="modal-buttons">
                    <button class="btn btn-dark" id="uploadModalDone">Done</button>
                </div>
            </div>
        </div>
        <div id="backendColorModal" class="backend-color-modal">

            <div class="backend-color-box">

                <div class="backend-color-header">
                    <h3>Select Color</h3>
                    <span onclick="closeBackendColorModal()">✕</span>
                </div>

                <input type="text" id="backendColorSearch" placeholder="Search color..."
                    oninput="filterBackendColors(this.value)">

                <div id="backendColorGrid" class="backend-color-grid"></div>

            </div>

        </div>


        <!-- Save Design Modal -->
        <div class="modal-overlay" id="saveDesignModal" style="display: none;">
            <div class="modal-content">
                <h3>Save Design</h3>
                <div class="modal-body">
                    <label for="designTitle">Design Title:</label>
                    <input type="text" id="designTitle" placeholder="Enter design title..." maxlength="50">
                  <div class="modal-buttons">
    <button class="btn btn-secondary" onclick="closeSaveDesignDialog()">Cancel</button>
<button class="btn btn-dark" id="saveDesignBtn" onclick="saveDesignToSVG()">
    <span id="saveBtnText">Save</span>
    <span id="saveBtnSpinner" class="btn-spinner"></span>
</button>
</div>

                </div>
            </div>
        </div>
        <div id="backendFontModal" class="backend-color-modal">

            <div class="backend-color-box">

                <div class="backend-color-header">
                    <h3>Select Font</h3>
                    <span onclick="closeBackendFontModal()">✕</span>
                </div>

                <input type="text" placeholder="Search font..." oninput="filterBackendFonts(this.value)">

                <div id="backendFontGrid" class="backend-color-grid"></div>

            </div>

        </div>

        <!-- Right-click context menu (with icons) -->
        <div id="contextMenu" class="context-menu" style="display: none;">
            <button type="button" data-action="copy"><span class="ctx-icon"><svg width="16" height="16"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" />
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                    </svg></span> Copy</button>
            <button type="button" data-action="paste"><span class="ctx-icon"><svg width="16" height="16"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                        <rect x="8" y="2" width="8" height="4" rx="1" />
                    </svg></span> Paste</button>
            <button type="button" data-action="cut"><span class="ctx-icon"><svg width="16" height="16"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                        <path d="M15 2H9a1 1 0 0 0-1 1v2" />
                        <line x1="9" y1="12" x2="15" y2="12" />
                    </svg></span> Cut</button>
            <div class="context-menu-sep"></div>
            <button type="button" data-action="flipH"><span class="ctx-icon"><svg width="16" height="16"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3" />
                        <path d="M16 3h3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-3" />
                        <line x1="12" y1="8" x2="12" y2="16" />
                    </svg></span> Flip left to right</button>
            <button type="button" data-action="flipV"><span class="ctx-icon"><svg width="16" height="16"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 8V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v3" />
                        <path d="M3 16v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3" />
                        <line x1="8" y1="12" x2="16" y2="12" />
                    </svg></span> Flip up to down</button>
            <div class="context-menu-sep"></div>
            <button type="button" data-action="bringToFront"><span class="ctx-icon"><svg width="16" height="16"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="8" y="8" width="12" height="12" rx="1" />
                        <rect x="4" y="4" width="12" height="12" rx="1" />
                    </svg></span> Bring to front</button>
            <button type="button" data-action="sendToBack"><span class="ctx-icon"><svg width="16" height="16"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="4" y="4" width="12" height="12" rx="1" />
                        <rect x="8" y="8" width="12" height="12" rx="1" />
                    </svg></span> Send to back</button>
            <button type="button" data-action="bringForward"><span class="ctx-icon"><svg width="16" height="16"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 2 12 12 22 22 12 12 2" />
                    </svg></span> Bring forward</button>
            <button type="button" data-action="sendBackward"><span class="ctx-icon"><svg width="16" height="16"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 22 2 12 12 2 22 12 12 22" />
                    </svg></span> Send backward</button>
        </div>

    </div>


    <script>
        window.backendColors = @json(\App\Models\Color::all());
        window.backendFonts = @json(\App\Models\Font::all());
    </script>

    <script src="https://cdn.jsdelivr.net/npm/fabric@5.3.0/dist/fabric.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/imagetracerjs@1.2.6/imagetracer_v1.2.6.js"></script>
    <script src="{{ asset('js/mascot.js') }}"></script>


@if(isset($template))
<script>
window.addEventListener('load', function() {

    var svgRaw = {!! json_encode($template->svg_data) !!};
    var imageData = {!! json_encode($template->image_data ?? '') !!};
    var templateTitle = {!! json_encode($template->title ?? '') !!};

    // Title set karo
    var titleInput = document.getElementById('designTitle');
    if (titleInput && templateTitle) titleInput.value = templateTitle;

    // ✅ CASE 1: SVG data hai
    if (svgRaw && svgRaw.trim().startsWith('<')) {

        fabric.loadSVGFromString(svgRaw, function(objects, options) {
            fabricCanvas.clear();

            var allColors = new Set();

            objects.forEach(function(obj) {
                obj.set({
                    selectable: true,
                    hasControls: true,
                    hasBorders: true,
                    objectCaching: false
                });

                // SVG paths se colors nikalo
                if (['path','rect','circle','polygon','ellipse'].indexOf(obj.type) !== -1) {
                    if (obj.fill && obj.fill !== 'transparent' && obj.fill !== '' && obj.fill !== 'none') {
                        allColors.add(obj.fill);
                    }
                    if (obj.stroke && obj.stroke !== 'transparent' && obj.stroke !== '' && obj.stroke !== 'none') {
                        allColors.add(obj.stroke);
                    }
                }

                // Group ke andar se colors nikalo
                if (obj.type === 'group') {
                    obj.subTargetCheck = false;
                    obj.interactive = false;

                    if (obj._objects && obj._objects.length) {
                        obj.forEachObject(function(child) {
                            child.selectable = false;
                            child.evented = false;
                            child.hasControls = false;
                            child.hasBorders = false;

                            if (child.fill && child.fill !== 'transparent' && child.fill !== '' && child.fill !== 'none') {
                                allColors.add(child.fill);
                            }
                            if (child.stroke && child.stroke !== 'transparent' && child.stroke !== '' && child.stroke !== 'none') {
                                allColors.add(child.stroke);
                            }
                        });
                    }

                    if (obj.colorMappings) {
                        Object.keys(obj.colorMappings).forEach(function(k) { allColors.add(k); });
                    }
                }

                if (obj.type === 'image' && obj.colorMappings) {
                    Object.keys(obj.colorMappings).forEach(function(k) { allColors.add(k); });
                }

                fabricCanvas.add(obj);
            });

            fabricCanvas.renderAll();

            // Colors set karo
            if (allColors.size > 0) {
                detectedColors = Array.from(allColors).map(function(hex) {
                    return {
                        hex: hex,
                        r: parseInt(hex.slice(1,3), 16) || 0,
                        g: parseInt(hex.slice(3,5), 16) || 0,
                        b: parseInt(hex.slice(5,7), 16) || 0,
                        count: 1
                    };
                });
                selectedColorsForVector = Array.from(allColors);
                allColors.forEach(function(hex) { colorMappings[hex] = hex; });
            }

            setTimeout(function() {
                fabricCanvas.getObjects().forEach(function(obj) {
                    if (obj.type === 'group') {
                        obj.subTargetCheck = false;
                        obj.set({ selectable: true, objectCaching: false });
                    }
                });
                fabricCanvas.renderAll();

                // Pehla object select karo — colors panel khulega
                var firstObj = fabricCanvas.getObjects()[0];
                if (firstObj) {
                    fabricCanvas.setActiveObject(firstObj);
                    fabricCanvas.fire('selection:created', { selected: [firstObj] });
                    fabricCanvas.renderAll();
                }
            }, 200);

            undoHistory = [];
            redoHistory = [];
            saveStateForUndo();
            updateLayers();
            updateLayerSelection();
        });
    }

    // ✅ CASE 2: Sirf image hai
    else if (imageData && imageData.length > 10) {
        fabric.Image.fromURL(imageData, function(img) {
            fabricCanvas.clear();
            img.set({ selectable: true, hasControls: true, hasBorders: true });
            fabricCanvas.add(img);
            fabricCanvas.setActiveObject(img);
            fabricCanvas.renderAll();
            undoHistory = [];
            redoHistory = [];
            saveStateForUndo();
            updateLayers();
        }, { crossOrigin: 'anonymous' });
    }

});
</script>
@endif


@endsection
