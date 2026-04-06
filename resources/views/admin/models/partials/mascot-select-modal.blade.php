{{-- =================== MASCOT SELECTION MODAL =================== --}}
<div id="mascotSelectModal"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center;">
    <div
        style="background:#fff; width:1100px; max-width:96vw; height:85vh; border-radius:12px; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,0.4);">

        {{-- ===== HEADER ===== --}}
        <div
            style="padding:18px 24px; background:#1a1a1a; color:#fff; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
            <h3 style="margin:0; font-size:17px; font-weight:700; letter-spacing:1px;">MASCOTS</h3>
            <span onclick="closeMascotSelectModal()"
                style="cursor:pointer; font-size:26px; line-height:1; opacity:.8; transition:opacity .2s;"
                onmouseenter="this.style.opacity=1" onmouseleave="this.style.opacity=.8">×</span>
        </div>

        {{-- ===== TABS ===== --}}
        <div style="display:flex; border-bottom:2px solid #e8e8e8; flex-shrink:0; background:#fafafa;">
            <button id="msTab1" onclick="switchMascotSelectTab('existing')"
                style="padding:14px 28px; border:none; background:transparent; font-weight:700; font-size:13px; cursor:pointer; border-bottom:3px solid #1a1a1a; color:#1a1a1a; transition:all .2s; letter-spacing:.5px;">
                Select Existing Mascot
            </button>
            <button id="msTab2" onclick="switchMascotSelectTab('create')"
                style="padding:14px 28px; border:none; background:transparent; font-weight:700; font-size:13px; cursor:pointer; border-bottom:3px solid transparent; color:#999; transition:all .2s; letter-spacing:.5px;">
                Create Custom Mascot
            </button>
            <button id="msTab3" onclick="switchMascotSelectTab('upload')"
                style="padding:14px 28px; border:none; background:transparent; font-weight:700; font-size:13px; cursor:pointer; border-bottom:3px solid transparent; color:#999; transition:all .2s; letter-spacing:.5px;">
                Upload Your Own Mascot File
            </button>
        </div>

        {{-- ===== BODY ===== --}}
        <div style="flex:1; overflow:hidden; display:flex;">

            {{-- ========== TAB 1: SELECT EXISTING ========== --}}
            <div id="msContent1" style="display:flex; width:100%; height:100%;">

                {{-- LEFT: Category sidebar --}}
                <div
                    style="width:220px; border-right:1px solid #e8e8e8; overflow-y:auto; flex-shrink:0; background:#fafafa;">
                    <div
                        style="padding:12px 16px; font-size:11px; font-weight:700; color:#999; letter-spacing:1px; border-bottom:1px solid #eee;">
                        CATEGORIES
                    </div>
                    <div id="mascotCategoryList" style="padding:8px 0;">
                        <div class="ms-cat-item ms-cat-active" data-category="all"
                            onclick="filterMascotCategory('all', this)"
                            style="padding:10px 16px; cursor:pointer; font-size:13px; font-weight:600; background:#1a1a1a; color:#fff;">
                            All Categories
                        </div>
                    </div>
                    <div style="padding:12px;">
                        <button onclick="toggleMascotDesignIdeas()"
                            style="width:100%; padding:10px; background:#fff; border:2px solid #1a1a1a; border-radius:6px; font-weight:700; font-size:12px; cursor:pointer; letter-spacing:.5px;">
                            Design Ideas
                        </button>
                    </div>
                </div>

                {{-- CENTER: Mascot grid --}}
                <div style="flex:1; display:flex; flex-direction:column; overflow:hidden;">
                    <div style="padding:12px 16px; border-bottom:1px solid #eee; flex-shrink:0;">
                        <div style="position:relative;">
                            <input type="text" id="mascotSearchInput" placeholder="Search mascot..."
                                oninput="searchMascots(this.value)"
                                style="width:100%; padding:9px 36px 9px 14px; border:1.5px solid #ddd; border-radius:8px; font-size:13px; outline:none; box-sizing:border-box; transition:border .2s;"
                                onfocus="this.style.borderColor='#1a1a1a'" onblur="this.style.borderColor='#ddd'">
                            <span
                                style="position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:16px;">⌕</span>
                        </div>
                    </div>
                    <div id="mascotSelectGrid"
                        style="flex:1; overflow-y:auto; padding:16px; display:grid; grid-template-columns:repeat(auto-fill,minmax(110px,1fr)); gap:12px; align-content:start;">
                        <div style="grid-column:1/-1; text-align:center; padding:40px; color:#aaa; font-size:13px;">
                            Loading mascots...
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Preview panel --}}
                <div
                    style="width:200px; border-left:1px solid #e8e8e8; display:flex; flex-direction:column; flex-shrink:0; background:#fafafa;">
                    <div
                        style="padding:12px 16px; font-size:11px; font-weight:700; color:#999; letter-spacing:1px; border-bottom:1px solid #eee;">
                        PREVIEW
                    </div>
                    <div
                        style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:16px; gap:12px;">
                        <div id="mascotSelectPreviewBox"
                            style="width:130px; height:130px; background:#f0f0f0; border-radius:10px; display:flex; align-items:center; justify-content:center; border:2px dashed #ccc; overflow:hidden;">
                            <span style="color:#ccc; font-size:12px; text-align:center; line-height:1.4;">Select
                                a<br>mascot</span>
                        </div>
                        <div id="mascotSelectPreviewName"
                            style="font-size:13px; font-weight:700; color:#333; text-align:center;"></div>
                        <button id="mascotEditBtn" onclick="editSelectedMascot()"
                            style="display:none; width:100%; padding:10px; background:#1a1a1a; color:#fff; border:none; border-radius:6px; font-weight:700; font-size:12px; cursor:pointer; letter-spacing:.5px;">
                            Edit Mascot
                        </button>
                    </div>
                </div>
            </div>

            {{-- ========== TAB 2: CREATE CUSTOM (inline) ========== --}}
            <div id="msContent2" style="display:none; width:100%; height:100%; overflow:hidden; flex-direction:column;">
                <div style="flex:1; overflow:hidden; position:relative;">
                    <iframe id="mascotCreatorFrame" src=""
                        style="width:100%; height:100%; border:none; display:block;" title="Mascot Creator">
                    </iframe>
                    <div id="mascotCreatorLoading"
                        style="position:absolute;inset:0;background:#fff;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:12px;">
                        <div
                            style="width:40px;height:40px;border:4px solid #eee;border-top-color:#1a1a1a;border-radius:50%;animation:msSpinAnim 0.8s linear infinite;">
                        </div>
                        <p style="color:#888;font-size:13px;margin:0;">Loading mascot creator...</p>
                    </div>
                </div>
                <div
                    style="padding:12px 20px;border-top:1px solid #eee;background:#fafafa;display:flex;gap:10px;align-items:center;flex-shrink:0;">
                    <span style="font-size:13px;color:#666;flex:1;">Create your mascot above, then click <strong>Use
                            This Mascot</strong> to apply it.</span>
                    <button onclick="importMascotFromCreator()"
                        style="padding:10px 22px;background:#1a1a1a;color:#fff;border:none;border-radius:6px;font-weight:700;font-size:13px;cursor:pointer;letter-spacing:.5px;">
                        Use This Mascot
                    </button>
                </div>
            </div>

            {{-- ========== TAB 3: UPLOAD ========== --}}
            <div id="msContent3" style="display:none; width:100%; height:100%; overflow-y:auto; padding:32px;">
                <div style="max-width:560px; margin:0 auto;">
                    <h4 style="margin:0 0 8px 0; font-size:18px; font-weight:700;">Upload Your Own Mascot</h4>
                    <p style="color:#777; font-size:13px; margin-bottom:28px;">Upload an SVG, PNG or JPG file from your
                        computer.</p>

                    <div id="mascotDropZone"
                        onclick="(function(){ var inp = document.getElementById('mascotFileInput'); inp.value=''; inp.click(); })()"
                        ondragover="mascotDragOver(event)" ondragleave="mascotDragLeave(event)"
                        ondrop="mascotDrop(event)"
                        style="border:2px dashed #ccc; border-radius:12px; padding:48px 24px; text-align:center; cursor:pointer; transition:all .2s; background:#fafafa;">
                        <div style="font-size:42px; margin-bottom:14px;">📁</div>
                        <div style="font-weight:700; font-size:15px; color:#333; margin-bottom:6px;">Drop your file here
                        </div>
                        <div style="font-size:12px; color:#999; margin-bottom:14px;">or click to browse</div>
                        <div
                            style="display:inline-block; padding:8px 20px; background:#1a1a1a; color:#fff; border-radius:6px; font-size:12px; font-weight:700; letter-spacing:.5px;">
                            Browse Files
                        </div>
                        <div style="margin-top:12px; font-size:11px; color:#bbb;">Supported: SVG, PNG, JPG (max 5MB)
                        </div>
                    </div>

                    <input type="file" id="mascotFileInput" accept="image/*,.svg" style="display:none;"
                        onchange="mascotFileSelected(this)">

                    <div id="mascotUploadPreview"
                        style="display:none; margin-top:24px; padding:20px; background:#f5f5f5; border-radius:10px;">
                        <div style="display:flex; align-items:center; gap:16px;">
                            <div id="mascotUploadThumb"
                                style="width:80px; height:80px; background:#e0e0e0; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden;">
                            </div>
                            <div style="flex:1;">
                                <div id="mascotUploadFileName"
                                    style="font-weight:700; font-size:13px; margin-bottom:4px; color:#333;"></div>
                                <div id="mascotUploadFileSize" style="font-size:11px; color:#999;"></div>
                            </div>
                            <button onclick="clearMascotUpload()"
                                style="background:none; border:none; font-size:20px; cursor:pointer; color:#999;">×</button>
                        </div>
                        <button onclick="applyUploadedMascotFile()"
                            style="width:100%; margin-top:16px; padding:12px; background:#1a1a1a; color:#fff; border:none; border-radius:6px; font-weight:700; cursor:pointer; font-size:13px; letter-spacing:.5px;">
                            Use This Mascot
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== FOOTER ===== --}}
        <div
            style="padding:16px 24px; border-top:1px solid #e8e8e8; display:flex; justify-content:flex-end; gap:12px; flex-shrink:0; background:#fafafa;">
            <button onclick="closeMascotSelectModal()"
                style="padding:12px 28px; background:#fff; border:2px solid #ccc; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; color:#555; letter-spacing:.5px;">
                CANCEL
            </button>
            <button onclick="applySelectedMascotToApplication()"
                style="padding:12px 28px; background:#1a1a1a; color:#fff; border:none; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; letter-spacing:.5px;">
                APPLY
            </button>
        </div>

    </div>
</div>

{{-- =================== MASCOT EDIT/CUSTOMIZE POPUP =================== --}}
{{-- Opens AFTER selecting a mascot — color change, bg erase, name, save --}}
<div id="mascotCustomizeModal"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:10000; align-items:center; justify-content:center;">
    <div
        style="background:#fff; width:900px; max-width:95vw; max-height:90vh; border-radius:12px; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 24px 80px rgba(0,0,0,0.5);">

        {{-- Header --}}
        <div
            style="padding:16px 24px; background:#1a1a1a; color:#fff; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
            <h3 style="margin:0; font-size:16px; font-weight:700; letter-spacing:1px;">CUSTOMIZE MASCOT</h3>
            <span onclick="closeMascotCustomizeModal()"
                style="cursor:pointer; font-size:26px; line-height:1; opacity:.8;" onmouseenter="this.style.opacity=1"
                onmouseleave="this.style.opacity=.8">×</span>
        </div>

        {{-- Body --}}
        <div style="display:flex; flex:1; overflow:hidden;">

            {{-- LEFT: Canvas area --}}
            <div style="flex:1; display:flex; flex-direction:column; border-right:1px solid #eee;">
                {{-- Toolbar --}}
                <div
                    style="padding:10px 16px; border-bottom:1px solid #eee; display:flex; gap:10px; align-items:center; flex-shrink:0; background:#fafafa;">
                    <button onclick="mcSetTool('select')" id="mcToolSelect"
                        style="padding:7px 14px; border:2px solid #1a1a1a; border-radius:6px; background:#1a1a1a; color:#fff; font-size:12px; font-weight:700; cursor:pointer;">
                        ↖ Select
                    </button>
                    <button onclick="mcSetTool('eraser')" id="mcToolEraser"
                        style="padding:7px 14px; border:2px solid #ccc; border-radius:6px; background:#fff; color:#333; font-size:12px; font-weight:700; cursor:pointer;">
                        ⌫ Eraser
                    </button>
                    <div style="display:flex;align-items:center;gap:6px;" id="mcEraserSizeWrap"
                        style="display:none;">
                        <label style="font-size:11px;color:#666;">Size:</label>
                        <input type="range" id="mcEraserSize" min="5" max="60" value="20"
                            style="width:80px;"
                            oninput="document.getElementById('mcEraserSizeVal').textContent=this.value">
                        <span id="mcEraserSizeVal" style="font-size:11px;width:24px;">20</span>
                    </div>
                    <button onclick="mcUndo()"
                        style="padding:7px 14px; border:2px solid #ccc; border-radius:6px; background:#fff; color:#333; font-size:12px; font-weight:700; cursor:pointer;">
                        ↩ Undo
                    </button>
                    <button onclick="mcResetCanvas()"
                        style="padding:7px 14px; border:2px solid #ccc; border-radius:6px; background:#fff; color:#333; font-size:12px; font-weight:700; cursor:pointer;">
                        ↺ Reset
                    </button>
                </div>
                {{-- Canvas --}}
                <div
                    style="flex:1; display:flex; align-items:center; justify-content:center; background:#e8e8e8; position:relative; overflow:hidden;">
                    <canvas id="mcCanvas"
                        style="background: repeating-conic-gradient(#ccc 0% 25%, #fff 0% 50%) 0 0 / 16px 16px; border-radius:4px; cursor:crosshair;"></canvas>
                </div>
            </div>

            {{-- RIGHT: Controls panel --}}
            <div style="width:260px; overflow-y:auto; flex-shrink:0;">

                {{-- Name --}}
                <div style="padding:16px; border-bottom:1px solid #eee;">
                    <label
                        style="font-size:11px; font-weight:700; color:#999; letter-spacing:1px; display:block; margin-bottom:6px;">MASCOT
                        NAME</label>
                    <input type="text" id="mcMascotName" placeholder="Enter mascot name..."
                        style="width:100%; padding:9px 12px; border:1.5px solid #ddd; border-radius:6px; font-size:13px; box-sizing:border-box; outline:none;"
                        onfocus="this.style.borderColor='#1a1a1a'" onblur="this.style.borderColor='#ddd'">
                </div>

                {{-- Color count selector --}}
                <div style="padding:16px; border-bottom:1px solid #eee;">
                    <label
                        style="font-size:11px; font-weight:700; color:#999; letter-spacing:1px; display:block; margin-bottom:8px;">COLORS
                        IN MASCOT</label>
                    <div style="display:flex; flex-wrap:wrap; gap:6px;" id="mcColorCountBtns">
                        <button onclick="mcSelectColorCount(1,this)"
                            style="padding:5px 10px;border:2px solid #ddd;border-radius:5px;font-size:12px;font-weight:700;cursor:pointer;background:#fff;">1</button>
                        <button onclick="mcSelectColorCount(2,this)"
                            style="padding:5px 10px;border:2px solid #1a1a1a;border-radius:5px;font-size:12px;font-weight:700;cursor:pointer;background:#1a1a1a;color:#fff;">2</button>
                        <button onclick="mcSelectColorCount(3,this)"
                            style="padding:5px 10px;border:2px solid #ddd;border-radius:5px;font-size:12px;font-weight:700;cursor:pointer;background:#fff;">3</button>
                        <button onclick="mcSelectColorCount(4,this)"
                            style="padding:5px 10px;border:2px solid #ddd;border-radius:5px;font-size:12px;font-weight:700;cursor:pointer;background:#fff;">4</button>
                        <button onclick="mcSelectColorCount(5,this)"
                            style="padding:5px 10px;border:2px solid #ddd;border-radius:5px;font-size:12px;font-weight:700;cursor:pointer;background:#fff;">5</button>
                        <button onclick="mcSelectColorCount(6,this)"
                            style="padding:5px 10px;border:2px solid #ddd;border-radius:5px;font-size:12px;font-weight:700;cursor:pointer;background:#fff;">6</button>
                        <button onclick="mcSelectColorCount(7,this)"
                            style="padding:5px 10px;border:2px solid #ddd;border-radius:5px;font-size:12px;font-weight:700;cursor:pointer;background:#fff;">7</button>
                        <button onclick="mcSelectColorCount(8,this)"
                            style="padding:5px 10px;border:2px solid #ddd;border-radius:5px;font-size:12px;font-weight:700;cursor:pointer;background:#fff;">8+</button>
                    </div>
                    <p id="mcColorCountInfo" style="font-size:11px;color:#888;margin:8px 0 0;">Detected: <strong
                            id="mcDetectedColors">0</strong> colors</p>
                </div>

                {{-- Color Replacement --}}
                <div style="padding:16px; border-bottom:1px solid #eee;">
                    <label
                        style="font-size:11px; font-weight:700; color:#999; letter-spacing:1px; display:block; margin-bottom:10px;">CHANGE
                        COLORS</label>
                    <div id="mcColorSwatches" style="display:flex;flex-direction:column;gap:10px;">
                        <p style="font-size:12px;color:#aaa;text-align:center;">Select a mascot to see colors</p>
                    </div>
                </div>

                {{-- Background --}}
                <div style="padding:16px; border-bottom:1px solid #eee;">
                    <label
                        style="font-size:11px; font-weight:700; color:#999; letter-spacing:1px; display:block; margin-bottom:8px;">BACKGROUND</label>
                    <button onclick="mcRemoveBackground()"
                        style="width:100%; padding:9px; background:#fff; border:2px solid #dc3545; color:#dc3545; border-radius:6px; font-weight:700; font-size:12px; cursor:pointer;">
                        🗑 Remove Background
                    </button>
                    <p style="font-size:11px;color:#999;margin:6px 0 0;">Or use Eraser tool above to manually erase
                        areas</p>
                </div>

                {{-- Opacity --}}
                <div style="padding:16px; border-bottom:1px solid #eee;">
                    <label
                        style="font-size:11px; font-weight:700; color:#999; letter-spacing:1px; display:block; margin-bottom:8px;">OPACITY</label>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <input type="range" id="mcOpacitySlider" min="0" max="100" value="100"
                            style="flex:1;" oninput="mcUpdateOpacity(this.value)">
                        <span id="mcOpacityVal" style="font-size:12px;font-weight:700;width:30px;">100</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- Footer --}}
        <div
            style="padding:14px 24px; border-top:1px solid #e8e8e8; display:flex; justify-content:space-between; align-items:center; flex-shrink:0; background:#fafafa;">
            <button onclick="closeMascotCustomizeModal()"
                style="padding:11px 24px; background:#fff; border:2px solid #ccc; border-radius:7px; font-weight:700; font-size:13px; cursor:pointer; color:#555;">
                BACK
            </button>
            <div style="display:flex;gap:10px;">
                <button onclick="mcSaveAndApply(false)"
                    style="padding:11px 24px; background:#fff; border:2px solid #1a1a1a; border-radius:7px; font-weight:700; font-size:13px; cursor:pointer; color:#1a1a1a;">
                    Apply Without Saving
                </button>
                <button onclick="mcSaveAndApply(true)"
                    style="padding:11px 24px; background:#1a1a1a; color:#fff; border:none; border-radius:7px; font-weight:700; font-size:13px; cursor:pointer;">
                    💾 Save & Apply
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes msSpinAnim {
        to {
            transform: rotate(360deg);
        }
    }

    .ms-cat-item {
        padding: 10px 16px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        color: #444;
        transition: background .15s;
        border-left: 3px solid transparent;
    }

    .ms-cat-item:hover {
        background: #f0f0f0;
    }

    .ms-cat-active {
        background: #1a1a1a !important;
        color: #fff !important;
        border-left-color: #1a1a1a !important;
    }

    .ms-mascot-card {
        border: 2px solid #e8e8e8;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
        cursor: pointer;
        transition: all .2s;
        background: #fff;
    }

    .ms-mascot-card:hover {
        border-color: #1a1a1a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
    }

    .ms-mascot-card.ms-selected {
        border-color: #1a1a1a;
        border-width: 3px;
        background: #f8f8f8;
    }

    .ms-mascot-card img {
        width: 100%;
        height: 80px;
        object-fit: contain;
        background: #f5f5f5;
        border-radius: 4px;
    }

    .ms-mascot-card p {
        margin: 6px 0 0;
        font-size: 11px;
        font-weight: 600;
        color: #444;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<script>
    // ============================================================
    // MASCOT SELECT MODAL — All functions
    // ============================================================

    window._mascotState = {
        allMascots: [],
        filteredMascots: [],
        selectedMascotData: null,
        currentTab: 'existing',
        pendingLayerId: null,
        uploadedFile: null
    };

    // ===== MC (Mascot Customize) State =====
    window._mc = {
        originalSvg: null, // original SVG string
        currentSvg: null, // working SVG string (after edits)
        canvas: null, // fabric canvas OR null
        undoStack: [],
        tool: 'select',
        eraserRadius: 20,
        colorMap: {}, // { detectedHex: replacedHex }
        colorCount: 2,
        opacity: 100,
        mascotName: ''
    };

    // ======= OPEN =======
    window.openMascotSelectModal = function(layerId) {
        layerId = layerId || window.currentApplicationLayer;
        const modal = document.getElementById('mascotSelectModal');
        if (!modal) {
            console.error('❌ mascotSelectModal not found');
            return;
        }

        window._mascotState.pendingLayerId = layerId;
        window._mascotState.selectedMascotData = null;

        modal.style.display = 'flex';
        modal.style.zIndex = '99999';

        switchMascotSelectTab('existing');
        _loadMascotTemplates();

        console.log('✅ Mascot modal opened for layer:', layerId);
    };

    // ======= CLOSE =======
    window.closeMascotSelectModal = function() {
        const modal = document.getElementById('mascotSelectModal');
        if (modal) modal.style.display = 'none';
        window._mascotState.selectedMascotData = null;
    };

    // ======= TABS =======
    window.switchMascotSelectTab = function(tab) {
        window._mascotState.currentTab = tab;

        var tabIds = {
            existing: 'msTab1',
            create: 'msTab2',
            upload: 'msTab3'
        };
        var contIds = {
            existing: 'msContent1',
            create: 'msContent2',
            upload: 'msContent3'
        };

        Object.keys(tabIds).forEach(function(t) {
            var btn = document.getElementById(tabIds[t]);
            var cont = document.getElementById(contIds[t]);
            if (btn) {
                btn.style.borderBottom = (t === tab) ? '3px solid #1a1a1a' : '3px solid transparent';
                btn.style.color = (t === tab) ? '#1a1a1a' : '#999';
            }
            if (cont) {
                if (t === 'existing') {
                    cont.style.display = (t === tab) ? 'flex' : 'none';
                } else if (t === 'create') {
                    cont.style.display = (t === tab) ? 'flex' : 'none';
                    cont.style.flexDirection = 'column';
                    if (t === tab) _loadMascotCreatorFrame();
                } else {
                    cont.style.display = (t === tab) ? 'block' : 'none';
                }
            }
        });
    };

    // ======= CREATE TAB: iframe loader =======
    function _loadMascotCreatorFrame() {
        var frame = document.getElementById('mascotCreatorFrame');
        var loading = document.getElementById('mascotCreatorLoading');
        if (!frame) return;

        // Only load once or if empty
        if (!frame.src || frame.src === window.location.href || frame.src === '') {
            if (loading) loading.style.display = 'flex';
            frame.src = '/admin/mascots/create?embed=1';
            frame.onload = function() {
                if (loading) loading.style.display = 'none';
            };
        }
    }

    // Import mascot from creator iframe
    window.importMascotFromCreator = function() {
        var frame = document.getElementById('mascotCreatorFrame');
        if (!frame || !frame.contentWindow) {
            alert('Creator not loaded');
            return;
        }

        try {
            // Try to get SVG from creator page
            var creatorDoc = frame.contentDocument || frame.contentWindow.document;
            var canvas = creatorDoc.getElementById('designCanvas');
            var svgEl = creatorDoc.querySelector('svg#mainSvg, svg.main-svg, svg');

            var svgStr = null;
            if (svgEl) {
                svgStr = new XMLSerializer().serializeToString(svgEl);
            } else if (canvas) {
                // fabric canvas → dataURL wrapped in SVG image
                var dataUrl = canvas.toDataURL('image/png');
                svgStr = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500">' +
                    '<image href="' + dataUrl + '" x="0" y="0" width="500" height="500"/>' +
                    '</svg>';
            }

            if (!svgStr) {
                alert('Could not get mascot from creator. Please save first.');
                return;
            }

            var name = 'Custom Mascot';
            var titleEl = creatorDoc.getElementById('designTitle') || creatorDoc.querySelector('[name="title"]');
            if (titleEl && titleEl.value) name = titleEl.value;

            window._mascotState.selectedMascotData = {
                svg: svgStr,
                title: name,
                source: 'creator',
                isImage: false
            };

            applySelectedMascotToApplication();
        } catch (e) {
            console.error('Creator import error:', e);
            alert('Could not import from creator. Please use Save & Apply instead.');
        }
    };

    // ======= LOAD MASCOTS =======
    function _loadMascotTemplates() {
        var grid = document.getElementById('mascotSelectGrid');
        if (grid) grid.innerHTML =
            '<div style="grid-column:1/-1;text-align:center;padding:40px;color:#aaa;font-size:13px;">Loading mascots...</div>';

        fetch('/api/mascot-templates')
            .then(function(r) {
                return r.json();
            })
            .then(function(data) {
                window._mascotState.allMascots = data;
                window._mascotState.filteredMascots = data;
                _buildCategoryList(data);
                _renderMascotGrid(data);
            })
            .catch(function() {
                var g = document.getElementById('mascotSelectGrid');
                if (g) g.innerHTML =
                    '<div style="grid-column:1/-1;text-align:center;padding:40px;color:#aaa;font-size:13px;">Could not load mascots.</div>';
            });
    }

    function _buildCategoryList(mascots) {
        var categories = [];
        mascots.forEach(function(m) {
            if (m.category && categories.indexOf(m.category) === -1) categories.push(m.category);
        });
        var list = document.getElementById('mascotCategoryList');
        if (!list) return;
        list.innerHTML =
            '<div class="ms-cat-item ms-cat-active" data-category="all" onclick="filterMascotCategory(\'all\',this)">All Categories</div>';
        categories.forEach(function(cat) {
            var div = document.createElement('div');
            div.className = 'ms-cat-item';
            div.dataset.category = cat;
            div.textContent = cat;
            div.onclick = function() {
                filterMascotCategory(cat, this);
            };
            list.appendChild(div);
        });
    }

    window.filterMascotCategory = function(category, el) {
        document.querySelectorAll('.ms-cat-item').forEach(function(i) {
            i.classList.remove('ms-cat-active');
        });
        if (el) el.classList.add('ms-cat-active');
        var state = window._mascotState;
        state.filteredMascots = (category === 'all') ? state.allMascots : state.allMascots.filter(function(m) {
            return m.category === category;
        });
        _renderMascotGrid(state.filteredMascots);
    };

    window.searchMascots = function(query) {
        var q = query.toLowerCase().trim();
        var state = window._mascotState;
        var base = state.filteredMascots.length ? state.filteredMascots : state.allMascots;
        _renderMascotGrid(q ? base.filter(function(m) {
            return m.title && m.title.toLowerCase().includes(q);
        }) : base);
    };

    function _renderMascotGrid(mascots) {
        var grid = document.getElementById('mascotSelectGrid');
        if (!grid) return;
        if (!mascots.length) {
            grid.innerHTML =
                '<div style="grid-column:1/-1;text-align:center;padding:40px;color:#aaa;font-size:13px;">No mascots found.</div>';
            return;
        }
        grid.innerHTML = '';
        mascots.forEach(function(m) {
            var card = document.createElement('div');
            card.className = 'ms-mascot-card';
            card.dataset.id = m.id;
            card.innerHTML = (m.image_data ?
                    '<img src="' + m.image_data + '" style="width:100%;height:80px;object-fit:contain;">' :
                    '<div style="width:100%;height:80px;overflow:hidden;">' + (m.svg_data || '') + '</div>') +
                '<p>' + (m.title || 'Untitled') + '</p>';
            card.onclick = function() {
                _selectMascotCard(m, card);
            };
            grid.appendChild(card);
        });
    }

    function _selectMascotCard(mascot, cardEl) {
        document.querySelectorAll('.ms-mascot-card').forEach(function(c) {
            c.classList.remove('ms-selected');
        });
        cardEl.classList.add('ms-selected');

        window._mascotState.selectedMascotData = {
            svg: mascot.svg_data || '',
            imageUrl: mascot.image_data || '',
            title: mascot.title,
            source: 'existing',
            mascotDbId: mascot.id
        };

        var previewBox = document.getElementById('mascotSelectPreviewBox');
        if (previewBox) {
            previewBox.innerHTML = '';
            previewBox.style.border = '2px solid #1a1a1a';
            if (mascot.svg_data && mascot.svg_data.trim().startsWith('<')) {
                previewBox.innerHTML = mascot.svg_data;
                var svg = previewBox.querySelector('svg');
                if (svg) {
                    svg.style.width = '110px';
                    svg.style.height = '110px';
                    svg.style.display = 'block';
                }
            } else if (mascot.image_data) {
                previewBox.innerHTML = '<img src="' + mascot.image_data +
                    '" style="width:110px;height:110px;object-fit:contain;">';
            } else {
                previewBox.innerHTML = '<span style="color:#ccc;font-size:11px;">No preview</span>';
            }
        }
        var nameEl = document.getElementById('mascotSelectPreviewName');
        if (nameEl) nameEl.textContent = mascot.title || '';
        var editBtn = document.getElementById('mascotEditBtn');
        if (editBtn) editBtn.style.display = 'block';
    }

    window.editSelectedMascot = function() {
        var data = window._mascotState.selectedMascotData;
        if (!data) {
            alert('Please select a mascot first.');
            return;
        }
        var card = document.querySelector('.ms-mascot-card.ms-selected');
        var mascotId = card ? card.dataset.id : null;
        if (!mascotId) {
            alert('Mascot ID not found.');
            return;
        }
        closeMascotSelectModal();
        window.location.href = '/admin/mascots/' + mascotId + '/edit';
    };

    window.toggleMascotDesignIdeas = function() {
        if (window.openDesignIdeas) window.openDesignIdeas();
    };

    // ======= APPLY BUTTON in main modal =======
    window.applySelectedMascotToApplication = function() {
        var state = window._mascotState;
        var mascotData = state.selectedMascotData;

        if (!mascotData) {
            alert('Please select a mascot first.');
            return;
        }

        var layerId = state.pendingLayerId || window.currentApplicationLayer;
        if (!layerId) {
            alert('No application layer found.');
            return;
        }

        // Open customize modal BEFORE applying
        closeMascotSelectModal();
        openMascotCustomizeModal(mascotData, layerId);
    };

    // ============================================================
    // =================== MASCOT CUSTOMIZE MODAL =================
    // ============================================================

    window.openMascotCustomizeModal = function(mascotData, layerId) {
        var modal = document.getElementById('mascotCustomizeModal');
        if (!modal) return;

        // Reset mc state
        window._mc = {
            originalSvg: mascotData.svg || '',
            currentSvg: mascotData.svg || '',
            imageUrl: mascotData.imageUrl || mascotData.imageData || '',
            undoStack: [],
            tool: 'select',
            eraserRadius: 20,
            colorMap: {},
            colorCount: 2,
            opacity: 100,
            mascotName: mascotData.title || '',
            layerId: layerId,
            source: mascotData.source || 'existing',
            mascotDbId: mascotData.mascotDbId || null,
            isImage: mascotData.isImage || false
        };

        modal.style.display = 'flex';
        modal.style.zIndex = '10000';

        // Set name
        var nameEl = document.getElementById('mcMascotName');
        if (nameEl) nameEl.value = window._mc.mascotName;

        // Reset opacity
        var opEl = document.getElementById('mcOpacitySlider');
        if (opEl) opEl.value = 100;
        var opVal = document.getElementById('mcOpacityVal');
        if (opVal) opVal.textContent = '100';

        // Init canvas after short delay
        setTimeout(function() {
            mcInitCanvas();
        }, 100);
    };

    window.closeMascotCustomizeModal = function() {
        var modal = document.getElementById('mascotCustomizeModal');
        if (modal) modal.style.display = 'none';
        // Re-open mascot select modal so user can pick another
        // (only if no mascot was applied)
    };

    // ------- Canvas init -------
    // ✅ FIXED mcInitCanvas — original size maintain karo
    function mcInitCanvas() {
        var canvasEl = document.getElementById('mcCanvas');
        if (!canvasEl) return;

        var svg = window._mc.currentSvg || '';
        var imageUrl = window._mc.imageUrl || '';

        var ctx;

        function _drawImageOnCanvas(src) {
            var img = new Image();
            img.crossOrigin = 'anonymous';

            img.onload = function() {
                // ✅ FIX: original size use karo — 460 nahi
                var MAX = 800;
                var ratio = Math.min(MAX / img.naturalWidth, MAX / img.naturalHeight, 1);
                var W = Math.round(img.naturalWidth * ratio);
                var H = Math.round(img.naturalHeight * ratio);

                canvasEl.width = W;
                canvasEl.height = H;

                // Canvas display size fix
                canvasEl.style.maxWidth = '460px';
                canvasEl.style.maxHeight = '460px';
                canvasEl.style.width = 'auto';
                canvasEl.style.height = 'auto';

                ctx = canvasEl.getContext('2d');
                ctx.clearRect(0, 0, W, H);
                ctx.drawImage(img, 0, 0, W, H);

                mcDetectColors(ctx, W, H);
                window._mc.undoStack = [];
                mcPushUndo();
                mcSetupEraserEvents(canvasEl);
            };

            img.onerror = function() {
                console.error('mcInitCanvas: image load failed');
                canvasEl.width = 460;
                canvasEl.height = 460;
                ctx = canvasEl.getContext('2d');
                ctx.fillStyle = '#eee';
                ctx.fillRect(0, 0, 460, 460);
                ctx.fillStyle = '#999';
                ctx.font = '16px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('Image could not load', 230, 230);
            };

            img.src = src;
        }

        // CASE 1: base64 PNG directly
        if (imageUrl && imageUrl.startsWith('data:')) {
            _drawImageOnCanvas(imageUrl);
            return;
        }

        // CASE 2: SVG string
        if (svg && svg.trim().startsWith('<')) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(svg, 'image/svg+xml');
            var imgTag = doc.querySelector('image');

            if (imgTag) {
                var href = imgTag.getAttribute('href') || imgTag.getAttribute('xlink:href') || '';
                if (href.startsWith('data:')) {
                    _drawImageOnCanvas(href);
                    return;
                }
            }

            var encoded = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
            _drawImageOnCanvas(encoded);
            return;
        }

        // CASE 3: regular URL
        if (imageUrl) {
            _drawImageOnCanvas(imageUrl);
            return;
        }

        canvasEl.width = 460;
        canvasEl.height = 460;
        ctx = canvasEl.getContext('2d');
        ctx.fillStyle = '#f0f0f0';
        ctx.fillRect(0, 0, 460, 460);
        ctx.fillStyle = '#aaa';
        ctx.font = '14px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('No mascot data', 230, 230);
    }

    function mcSetupEraserEvents(canvas) {
        // Click = flood fill erase (us color ka poora area)
        // Drag  = brush erase (manual fine control)
        var drawing = false;
        var didErase = false;

        canvas.onmousedown = function(e) {
            if (window._mc.tool !== 'eraser') return;
            e.preventDefault();
            drawing = true;
            didErase = false;
            // Flood fill on click
            mcFloodErase(canvas, e);
            didErase = true;
        };

        canvas.onmousemove = function(e) {
            if (!drawing || window._mc.tool !== 'eraser') return;
            // Drag = brush erase for fine details
            mcBrushErase(canvas, e);
            didErase = true;
        };

        canvas.onmouseup = function() {
            if (drawing && didErase) mcPushUndo();
            drawing = false;
        };

        canvas.onmouseleave = function() {
            if (drawing && didErase) mcPushUndo();
            drawing = false;
        };
    }

    // ---- FLOOD FILL ERASE — click karo, us color ka connected area erase ----
    function mcFloodErase(canvas, e) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        var clickX = Math.floor((e.clientX - rect.left) * scaleX);
        var clickY = Math.floor((e.clientY - rect.top) * scaleY);

        clickX = Math.max(0, Math.min(canvas.width - 1, clickX));
        clickY = Math.max(0, Math.min(canvas.height - 1, clickY));

        var ctx = canvas.getContext('2d');
        var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var data = imageData.data;
        var w = canvas.width,
            h = canvas.height;

        var idx = (clickY * w + clickX) * 4;
        var targetR = data[idx];
        var targetG = data[idx + 1];
        var targetB = data[idx + 2];
        var targetA = data[idx + 3];

        if (targetA < 20) return; // already transparent

        var tolerance = 40; // color similarity (0=exact, higher=more forgiving)

        function colorMatch(i) {
            if (data[i + 3] < 20) return false;
            return (
                Math.abs(data[i] - targetR) +
                Math.abs(data[i + 1] - targetG) +
                Math.abs(data[i + 2] - targetB)
            ) <= tolerance * 3;
        }

        // BFS flood fill
        var visited = new Uint8Array(w * h);
        var queue = [clickY * w + clickX];
        visited[queue[0]] = 1;

        while (queue.length) {
            var pos = queue.shift();
            var px = pos % w;
            var py = Math.floor(pos / w);
            data[pos * 4 + 3] = 0; // transparent

            var neighbors = [
                py > 0 ? (py - 1) * w + px : -1,
                py < h - 1 ? (py + 1) * w + px : -1,
                px > 0 ? py * w + (px - 1) : -1,
                px < w - 1 ? py * w + (px + 1) : -1
            ];
            for (var n = 0; n < 4; n++) {
                var np = neighbors[n];
                if (np < 0 || visited[np]) continue;
                visited[np] = 1;
                if (colorMatch(np * 4)) queue.push(np);
            }
        }

        ctx.putImageData(imageData, 0, 0);
    }

    // ---- BRUSH ERASE — drag karo for manual fine erasing ----
    function mcBrushErase(canvas, e) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        var x = (e.clientX - rect.left) * scaleX;
        var y = (e.clientY - rect.top) * scaleY;
        var ctx = canvas.getContext('2d');
        ctx.save();
        ctx.globalCompositeOperation = 'destination-out';
        ctx.beginPath();
        ctx.arc(x, y, window._mc.eraserRadius || 20, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
    }

    // Legacy mcErase (for compatibility)
    function mcErase(canvas, e) {
        mcBrushErase(canvas, e);
    }

    function mcPushUndo() {
        var canvas = document.getElementById('mcCanvas');
        if (!canvas) return;
        window._mc.undoStack.push(canvas.toDataURL());
        if (window._mc.undoStack.length > 30) window._mc.undoStack.shift();
    }

    window.mcUndo = function() {
        if (window._mc.undoStack.length < 2) return;
        window._mc.undoStack.pop(); // remove current
        var prev = window._mc.undoStack[window._mc.undoStack.length - 1];
        var canvas = document.getElementById('mcCanvas');
        if (!canvas) return;
        var img = new Image();
        img.onload = function() {
            var ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0);
        };
        img.src = prev;
    };

    window.mcResetCanvas = function() {
        window._mc.colorMap = {};
        window._mc.currentSvg = window._mc.originalSvg;
        mcInitCanvas();
    };

    // ------- Tools -------
    window.mcSetTool = function(tool) {
        window._mc.tool = tool;
        var canvas = document.getElementById('mcCanvas');

        var selBtn = document.getElementById('mcToolSelect');
        var erBtn = document.getElementById('mcToolEraser');
        var erWrap = document.getElementById('mcEraserSizeWrap');

        if (tool === 'select') {
            if (selBtn) {
                selBtn.style.background = '#1a1a1a';
                selBtn.style.color = '#fff';
                selBtn.style.borderColor = '#1a1a1a';
            }
            if (erBtn) {
                erBtn.style.background = '#fff';
                erBtn.style.color = '#333';
                erBtn.style.borderColor = '#ccc';
            }
            if (erWrap) erWrap.style.display = 'none';
            if (canvas) canvas.style.cursor = 'default';
        } else {
            if (erBtn) {
                erBtn.style.background = '#1a1a1a';
                erBtn.style.color = '#fff';
                erBtn.style.borderColor = '#1a1a1a';
            }
            if (selBtn) {
                selBtn.style.background = '#fff';
                selBtn.style.color = '#333';
                selBtn.style.borderColor = '#ccc';
            }
            if (erWrap) erWrap.style.display = 'flex';
            if (canvas) canvas.style.cursor = 'crosshair';
        }

        var erSize = document.getElementById('mcEraserSize');
        if (erSize) window._mc.eraserRadius = parseInt(erSize.value) || 20;
    };

    // Sync eraser size
    document.addEventListener('DOMContentLoaded', function() {
        var erSize = document.getElementById('mcEraserSize');
        if (erSize) erSize.addEventListener('input', function() {
            window._mc.eraserRadius = parseInt(this.value) || 20;
        });
    });

    // ------- Color detection -------
    // ------- Color detection — K-means style clustering -------
    function mcDetectColors(ctx, w, h) {
        var imageData = ctx.getImageData(0, 0, w, h);
        var data = imageData.data;
        var colorCounts = {};

        // Sample pixels
        for (var i = 0; i < data.length; i += 16) {
            var a = data[i + 3];
            if (a < 30) continue; // skip transparent

            // Quantize to reduce noise (32-step buckets)
            var r = Math.round(data[i] / 32) * 32;
            var g = Math.round(data[i + 1] / 32) * 32;
            var b = Math.round(data[i + 2] / 32) * 32;
            r = Math.min(r, 255);
            g = Math.min(g, 255);
            b = Math.min(b, 255);

            var hex = rgbToHex(r, g, b);
            colorCounts[hex] = (colorCounts[hex] || 0) + 1;
        }

        // Sort by frequency
        var sorted = Object.entries(colorCounts).sort(function(a, b) {
            return b[1] - a[1];
        });

        var detEl = document.getElementById('mcDetectedColors');
        if (detEl) detEl.textContent = sorted.length;

        // ✅ K-means style: colors ko cluster karo
        // Sirf colorCount (2,3,4...) top clusters nikalo
        var topColors = _clusterColors(sorted, window._mc.colorCount || 2);

        mcRenderColorSwatches(topColors);
    }

    // ✅ Color clustering — similar colors ko merge karo, top N clusters nikalo
    function _clusterColors(sortedEntries, n) {
        if (!sortedEntries.length) return [];

        var clusters = [];

        sortedEntries.forEach(function(entry) {
            var hex = entry[0];
            var count = entry[1];
            var rgb = hexToRgb(hex);
            if (!rgb) return;

            // Check if fits in existing cluster
            var merged = false;
            for (var c = 0; c < clusters.length; c++) {
                var cRgb = hexToRgb(clusters[c].hex);
                var dist = Math.sqrt(
                    Math.pow(rgb.r - cRgb.r, 2) +
                    Math.pow(rgb.g - cRgb.g, 2) +
                    Math.pow(rgb.b - cRgb.b, 2)
                );
                // Threshold: 80 = similar colors merge ho jayein
                if (dist < 80) {
                    clusters[c].count += count;
                    // Weighted average center update
                    clusters[c].r = Math.round((clusters[c].r * (clusters[c].count - count) + rgb.r * count) /
                        clusters[c].count);
                    clusters[c].g = Math.round((clusters[c].g * (clusters[c].count - count) + rgb.g * count) /
                        clusters[c].count);
                    clusters[c].b = Math.round((clusters[c].b * (clusters[c].count - count) + rgb.b * count) /
                        clusters[c].count);
                    clusters[c].hex = rgbToHex(clusters[c].r, clusters[c].g, clusters[c].b);
                    merged = true;
                    break;
                }
            }

            if (!merged) {
                clusters.push({
                    hex: hex,
                    r: rgb.r,
                    g: rgb.g,
                    b: rgb.b,
                    count: count
                });
            }
        });

        // Sort by count, take top N
        clusters.sort(function(a, b) {
            return b.count - a.count;
        });
        return clusters.slice(0, n).map(function(c) {
            return c.hex;
        });
    }

   window.mcSelectColorCount = function(n, btn) {
    window._mc.colorCount = n;
    console.log('🔢 User ne select kiya:', n); // ← add karo

    document.querySelectorAll('#mcColorCountBtns button').forEach(function(b) {
        b.style.background = '#fff';
        b.style.color = '#333';
        b.style.borderColor = '#ddd';
    });
    if (btn) {
        btn.style.background = '#1a1a1a';
        btn.style.color = '#fff';
        btn.style.borderColor = '#1a1a1a';
    }
    var canvas = document.getElementById('mcCanvas');
    if (canvas) {
        var ctx = canvas.getContext('2d');
        mcDetectColors(ctx, canvas.width, canvas.height);
    }
};

    // ✅ NAYA CODE — selectedColors (color wheel) use karega
    function mcRenderColorSwatches(detectedColors) {
        var container = document.getElementById('mcColorSwatches');
        if (!container) return;
        container.innerHTML = '';

        // FIX: sirf colorCount tak rows dikhao
        detectedColors = detectedColors.slice(0, window._mc.colorCount || 2);

        var backendColors = (window.selectedColors && window.selectedColors.length) ?
            window.selectedColors :
            (window.backendColors || []).map(function(c) {
                return c.code || c.hex || c.color || c;
            });

        if (!backendColors.length) backendColors = [
            '#FF0000', '#00FF00', '#0000FF', '#FFFFFF', '#000000', '#FFFF00', '#FF00FF', '#00FFFF'
        ];

        detectedColors.forEach(function(detectedHex) {
            var row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;gap:8px;margin-bottom:4px;';

            var fromBox = document.createElement('div');
            fromBox.style.cssText =
                'width:28px;height:28px;border-radius:5px;border:2px solid #ccc;flex-shrink:0;';
            fromBox.style.background = detectedHex;
            fromBox.title = 'Original: ' + detectedHex;

            var arrow = document.createElement('span');
            arrow.textContent = '→';
            arrow.style.cssText = 'font-size:14px;color:#888;flex-shrink:0;';

            var swatchRow = document.createElement('div');
            swatchRow.style.cssText = 'display:flex;flex-wrap:wrap;gap:4px;';

            var currentReplacement = window._mc.colorMap[detectedHex.toLowerCase()] || null;

            backendColors.slice(0, 12).forEach(function(hex) {
                var isSelected = hex.toLowerCase() === (currentReplacement || '').toLowerCase();
                var box = document.createElement('div');
                box.className = 'mc-swatch-box';
                box.dataset.detected = detectedHex.toLowerCase();
                box.dataset.replace = hex.toLowerCase();
                box.style.cssText = 'width:22px;height:22px;border-radius:4px;cursor:pointer;border:' +
                    (isSelected ? '3px solid #1a1a1a' : '2px solid #ddd') +
                    ';box-sizing:border-box;transition:border .1s;';
                box.style.background = hex;
                box.title = hex;

                box.onclick = (function(dHex, nHex, b, swRow) {
                    return function() {
                        window._mc.colorMap[dHex.toLowerCase()] = nHex;
                        swRow.querySelectorAll('.mc-swatch-box').forEach(function(x) {
                            x.style.border = '2px solid #ddd';
                        });
                        b.style.border = '3px solid #1a1a1a';
                        mcApplyColorReplacements();
                    };
                })(detectedHex, hex, box, swatchRow);

                swatchRow.appendChild(box);
            });

            row.appendChild(fromBox);
            row.appendChild(arrow);
            row.appendChild(swatchRow);
            container.appendChild(row);
        });
    }

    function mcApplyColorReplacements() {
        var canvas = document.getElementById('mcCanvas');
        if (!canvas) return;

        // ✅ KEY FIX: Hamesha original image se start karo
        // Taake pehli replacement ki wajah se colors lost na hon
        var originalBase = window._mc.undoStack && window._mc.undoStack.length > 0 ?
            window._mc.undoStack[0] // very first state = original
            :
            null;

        function _doReplace(imageData) {
            var data = imageData.data;
            var replacements = [];
            Object.entries(window._mc.colorMap).forEach(function(entry) {
                var from = hexToRgb(entry[0]);
                var to = hexToRgb(entry[1]);
                if (from && to) replacements.push({
                    from: from,
                    to: to
                });
            });
            if (!replacements.length) return imageData;

            var threshold = 45;
            for (var i = 0; i < data.length; i += 4) {
                if (data[i + 3] < 20) continue;
                var r = data[i],
                    g = data[i + 1],
                    b = data[i + 2];
                for (var ri = 0; ri < replacements.length; ri++) {
                    var fr = replacements[ri].from.r;
                    var fg = replacements[ri].from.g;
                    var fb = replacements[ri].from.b;
                    var dist = Math.abs(r - fr) + Math.abs(g - fg) + Math.abs(b - fb);
                    if (dist < threshold * 3) {
                        data[i] = replacements[ri].to.r;
                        data[i + 1] = replacements[ri].to.g;
                        data[i + 2] = replacements[ri].to.b;
                        break; // ek pixel pe ek hi replacement
                    }
                }
            }
            return imageData;
        }

        var ctx = canvas.getContext('2d');

        if (originalBase) {
            // Original image load karo, phir replacements apply karo
            var img = new Image();
            img.onload = function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);
                var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                _doReplace(imageData);
                ctx.putImageData(imageData, 0, 0);
                mcPushUndo();
                // Swatches update karo — selected colors maintain karo
                mcRefreshSwatchSelections();
            };
            img.src = originalBase;
        } else {
            // No original saved — current canvas use karo
            var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            _doReplace(imageData);
            ctx.putImageData(imageData, 0, 0);
            mcPushUndo();
            mcRefreshSwatchSelections();
        }
    }

    // Swatches par selected borders update karo (bina re-render kiye)
    function mcRefreshSwatchSelections() {
        var container = document.getElementById('mcColorSwatches');
        if (!container) return;
        var rows = container.querySelectorAll('div[data-detected]');
        // Simpler: bas swatch borders update karo
        container.querySelectorAll('.mc-swatch-box').forEach(function(box) {
            var dHex = box.dataset.detected;
            var nHex = box.dataset.replace;
            var current = window._mc.colorMap[dHex] || '';
            box.style.borderColor = (current.toLowerCase() === nHex.toLowerCase()) ? '#1a1a1a' : '#ddd';
            box.style.borderWidth = (current.toLowerCase() === nHex.toLowerCase()) ? '3px' : '2px';
        });
    }

    // ------- Remove Background -------
    window.mcRemoveBackground = function() {
        var canvas = document.getElementById('mcCanvas');
        if (!canvas) return;
        var ctx = canvas.getContext('2d');
        var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var data = imageData.data;

        // Sample corners for background color
        var corners = [0, (canvas.width - 1) * 4, (canvas.height - 1) * canvas.width * 4, ((canvas.height - 1) *
            canvas.width + canvas.width - 1) * 4];
        var bgSamples = corners.map(function(i) {
            return {
                r: data[i],
                g: data[i + 1],
                b: data[i + 2]
            };
        });
        var bgR = Math.round(bgSamples.reduce(function(s, c) {
            return s + c.r;
        }, 0) / 4);
        var bgG = Math.round(bgSamples.reduce(function(s, c) {
            return s + c.g;
        }, 0) / 4);
        var bgB = Math.round(bgSamples.reduce(function(s, c) {
            return s + c.b;
        }, 0) / 4);

        var threshold = 50;
        for (var i = 0; i < data.length; i += 4) {
            var dist = Math.abs(data[i] - bgR) + Math.abs(data[i + 1] - bgG) + Math.abs(data[i + 2] - bgB);
            if (dist < threshold) data[i + 3] = 0;
        }

        ctx.putImageData(imageData, 0, 0);
        mcPushUndo();
    };

    // ------- Opacity -------
    window.mcUpdateOpacity = function(val) {
        window._mc.opacity = parseInt(val);
        var valEl = document.getElementById('mcOpacityVal');
        if (valEl) valEl.textContent = val;
    };

    // ------- Save & Apply -------
 // ✅ FIXED mcSaveAndApply — full original quality
window.mcSaveAndApply = function (doSave) {
    var canvas = document.getElementById('mcCanvas');
    if (!canvas) return;

    // ✅ Button se colorCount lo
    var selectedBtn = document.querySelector('#mcColorCountBtns button[style*="background: rgb(26, 26, 26)"], #mcColorCountBtns button[style*="background:#1a1a1a"], #mcColorCountBtns button[style*="background: #1a1a1a"]');
    if (selectedBtn) {
        var btnText = selectedBtn.textContent.trim();
        window._mc.colorCount = btnText === '8+' ? 8 : parseInt(btnText) || 2;
    }

    // ✅ colorCount global variable mein bhi save karo
    var savedColorCount = window._mc.colorCount;
    console.log('✅ savedColorCount:', savedColorCount);

    var nameEl = document.getElementById('mcMascotName');
    var name = (nameEl && nameEl.value.trim()) ? nameEl.value.trim() : (window._mc.mascotName || 'My Mascot');
    window._mc.mascotName = name;

    var alphaMap = [];
    try {
        var maskImageData = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
        for (var i = 3; i < maskImageData.data.length; i += 4) {
            alphaMap.push(maskImageData.data[i]);
        }
    } catch (e) {}

    var offscreen = document.createElement('canvas');
    offscreen.width  = canvas.width;
    offscreen.height = canvas.height;
    var offCtx = offscreen.getContext('2d');
    offCtx.imageSmoothingEnabled = true;
    offCtx.imageSmoothingQuality = 'high';
    offCtx.drawImage(canvas, 0, 0);
    var dataUrl = offscreen.toDataURL('image/png', 1.0);

    var finalSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' +
        canvas.width + ' ' + canvas.height + '">' +
        '<image href="' + dataUrl + '" x="0" y="0" width="' + canvas.width +
        '" height="' + canvas.height +
        '" preserveAspectRatio="xMidYMid meet" opacity="' + (window._mc.opacity / 100) + '"/>' +
        '</svg>';

    var layerId = window._mc.layerId || window.currentApplicationLayer;

    document.getElementById('mascotCustomizeModal').style.display = 'none';

    window.currentApplicationLayer = layerId;

    // ✅ Pehle apply karo
    if (window.applyDirectMascotToLayer) {
        window.applyDirectMascotToLayer(finalSvg, layerId, true);
    }

    // ✅ BAAD MEIN layer update karo — applyDirectMascotToLayer ke baad
    var layer = window.findLayerById ? window.findLayerById(layerId) : null;
    if (layer) {
        layer.mascotTitle = name;
        layer._selectedColorCount = savedColorCount; // ✅ override karo
        console.log('✅ layer._selectedColorCount SET TO:', layer._selectedColorCount);

        if (alphaMap.length > 0) {
            layer._alphaMask  = alphaMap;
            layer._alphaMaskW = canvas.width;
            layer._alphaMaskH = canvas.height;
        }
    }

    // ✅ updateApplicationLayersList ke baad showDirectMascotControls dobara call karo
    if (window.updateApplicationLayersList) window.updateApplicationLayersList();


setTimeout(function() {
    var updatedLayer = window.findLayerById ? window.findLayerById(layerId) : null;
    if (updatedLayer) {
        updatedLayer._selectedColorCount = savedColorCount;
        console.log('🎨 Rendering colors with count:', updatedLayer._selectedColorCount);
        // ✅ FIX: _renderDirectMascotColors ki jagah showDirectMascotControls use karo
        if (window.selectApplicationLayer) {
            window.selectApplicationLayer(layerId);
        }
    }
}, 500);

    if (doSave) _mcSaveToBackend(name, dataUrl, finalSvg);
};

// function _renderDirectMascotColors ke saath saath:
window._renderDirectMascotColors = function(layer) {
    const container = document.getElementById('directMascotColorSwatches');
    if (!container) return;

    if (!layer.mascotSvg) {
        container.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;">No mascot selected</p>';
        return;
    }

    container.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;">Detecting colors...</p>';

    const maxColors = (layer._selectedColorCount && layer._selectedColorCount > 0)
                      ? layer._selectedColorCount
                      : (window.selectedColors ? window.selectedColors.length : 3);

    console.log('🎨 maxColors:', maxColors, '| _selectedColorCount:', layer._selectedColorCount);

    const parser = new DOMParser();
    const doc = parser.parseFromString(layer.mascotSvg, 'image/svg+xml');

    const imgTag = doc.querySelector('image');
    if (imgTag) {
        const href = imgTag.getAttribute('href') || imgTag.getAttribute('xlink:href') || '';
        if (href.startsWith('data:image')) {
            _detectColorsFromPng(href, layer, container);
            return;
        }
    }

    const colorCounts = {};
    doc.querySelectorAll('[fill]').forEach(el => {
        const hex = _normalizeColor(el.getAttribute('fill') || '');
        if (hex && hex !== '#ffffff') colorCounts[hex] = (colorCounts[hex] || 0) + 1;
    });

    const detected = Object.keys(colorCounts)
                           .sort((a, b) => colorCounts[b] - colorCounts[a])
                           .slice(0, maxColors);

    layer._detectedColors = detected;
    _buildColorSwatches(detected, layer, container);
};

// Private reference bhi rakho
function _renderDirectMascotColors(layer) {
    window._renderDirectMascotColors(layer);
}
// showDirectMascotControls function ko window pe lagao:
window.showDirectMascotControls = function(layer) {
    const appControls = document.getElementById('applicationLayerControls');
    if (appControls) {
        Array.from(appControls.children).forEach(child => {
            child.style.display = child.id !== 'directMascotControls' ? 'none' : 'block';
        });
    }

    const preview = document.getElementById('directMascotPreview');
    if (preview && layer.mascotSvg) {
        preview.innerHTML = layer.mascotSvg;
        const svg = preview.querySelector('svg');
        if (svg) {
            svg.style.maxWidth = '80px';
            svg.style.maxHeight = '80px';
            svg.style.transform = layer.flipX === -1 ? 'scaleX(-1)' : '';
        }
    } else if (preview) {
        preview.innerHTML = '<span style="color:#aaa;font-size:12px;">No mascot selected</span>';
    }

    const sync = (id, valId, val) => {
        const el = document.getElementById(id);
        const ve = document.getElementById(valId);
        if (el) el.value = val;
        if (ve) ve.textContent = val;
    };

    sync('directMascotScale',    'directMascotScaleValue',    Math.round((layer.mascotScaleX || 1) * 100));
    sync('directMascotOpacity',  'directMascotOpacityValue',  layer.mascotOpacity ?? 100);
    sync('directMascotRotation', 'directMascotRotationValue', layer.rotation || 0);
    sync('mascotDirectPosX',     'mascotDirectPosXValue',     layer.x || 0);
    sync('mascotDirectPosY',     'mascotDirectPosYValue',     layer.y || 0);

    setTimeout(() => {
        // ✅ window pe expose karo
        if (window._renderDirectMascotColors) {
            window._renderDirectMascotColors(layer);
        }
    }, 100);

    setTimeout(() => _showMascotBox(layer.id), 80);
};
    function _mcSaveToBackend(name, dataUrl, svgData) {
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var csrf = csrfMeta ? csrfMeta.content : '';

        fetch('/api/mascot-templates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({
                    title: name,
                    svg_data: svgData,
                    image_data: dataUrl,
                    category: 'Custom'
                })
            })
            .then(function(r) {
                return r.json();
            })
            .then(function(res) {
                console.log('✅ Mascot saved:', res);
                // Reload mascot list silently
                fetch('/api/mascot-templates').then(function(r) {
                    return r.json();
                }).then(function(data) {
                    window._mascotState.allMascots = data;
                    window._mascotState.filteredMascots = data;
                });
            })
            .catch(function(err) {
                console.error('Save error:', err);
            });
    }

    // ------- Helpers -------
    function rgbToHex(r, g, b) {
        return '#' + [r, g, b].map(function(v) {
            return v.toString(16).padStart(2, '0');
        }).join('');
    }

    function hexToRgb(hex) {
        var r = parseInt(hex.slice(1, 3), 16);
        var g = parseInt(hex.slice(3, 5), 16);
        var b = parseInt(hex.slice(5, 7), 16);
        if (isNaN(r) || isNaN(g) || isNaN(b)) return null;
        return {
            r: r,
            g: g,
            b: b
        };
    }

    // ======= UPLOAD TAB =======
    window.mascotDragOver = function(e) {
        e.preventDefault();
        var zone = document.getElementById('mascotDropZone');
        if (zone) {
            zone.style.borderColor = '#1a1a1a';
            zone.style.background = '#f0f0f0';
        }
    };
    window.mascotDragLeave = function(e) {
        var zone = document.getElementById('mascotDropZone');
        if (zone) {
            zone.style.borderColor = '#ccc';
            zone.style.background = '#fafafa';
        }
    };
    window.mascotDrop = function(e) {
        e.preventDefault();
        mascotDragLeave(e);
        var file = e.dataTransfer.files[0];
        if (file) _handleMascotFile(file);
    };
    window.mascotFileSelected = function(input) {
        var file = input.files && input.files[0];
        if (file) _handleMascotFile(file);
        input.value = '';
    };

    function _handleMascotFile(file) {
        if (file.size > 5 * 1024 * 1024) {
            alert('File too large (max 5MB)');
            return;
        }
        var allowed = ['image/svg+xml', 'image/png', 'image/jpeg', 'image/jpg'];
        if (!allowed.includes(file.type)) {
            alert('Please upload SVG, PNG or JPG');
            return;
        }
        window._mascotState.uploadedFile = file;

        var reader = new FileReader();
        reader.onload = function(e) {
            var result = e.target.result;
            var thumb = document.getElementById('mascotUploadThumb');
            var svgStr;

            if (file.type === 'image/svg+xml') {
                if (thumb) {
                    thumb.innerHTML = result;
                    var s = thumb.querySelector('svg');
                    if (s) {
                        s.style.width = '100%';
                        s.style.height = '100%';
                    }
                }
                svgStr = result;
            } else {
                if (thumb) thumb.innerHTML = '<img src="' + result +
                    '" style="width:100%;height:100%;object-fit:contain;border-radius:4px;">';
                svgStr = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">' +
                    '<image href="' + result +
                    '" x="0" y="0" width="100" height="100" preserveAspectRatio="xMidYMid meet"/>' +
                    '</svg>';
            }

            window._mascotState.selectedMascotData = {
                svg: svgStr,
                title: file.name.replace(/\.[^.]+$/, ''),
                source: 'upload',
                isImage: file.type !== 'image/svg+xml'
            };

            var fn = document.getElementById('mascotUploadFileName');
            var fs = document.getElementById('mascotUploadFileSize');
            var pr = document.getElementById('mascotUploadPreview');
            if (fn) fn.textContent = file.name;
            if (fs) fs.textContent = (file.size / 1024).toFixed(1) + ' KB';
            if (pr) pr.style.display = 'block';
        };

        if (file.type === 'image/svg+xml') reader.readAsText(file);
        else reader.readAsDataURL(file);
    }

    window.clearMascotUpload = function() {
        window._mascotState.uploadedFile = null;
        window._mascotState.selectedMascotData = null;
        var pr = document.getElementById('mascotUploadPreview');
        var inp = document.getElementById('mascotFileInput');
        var zn = document.getElementById('mascotDropZone');
        if (pr) pr.style.display = 'none';
        if (inp) inp.value = '';
        if (zn) {
            zn.style.borderColor = '#ccc';
            zn.style.background = '#fafafa';
        }
    };

    window.applyUploadedMascotFile = function() {
        var data = window._mascotState.selectedMascotData;
        if (!data) {
            alert('Please select a file first.');
            return;
        }
        var layerId = window._mascotState.pendingLayerId || window.currentApplicationLayer;
        closeMascotSelectModal();
        openMascotCustomizeModal(data, layerId);
    };
</script>
