{{-- =================== APPLICATIONS SIDEBAR =================== --}}
<div id="applicationsSidebar" class="applications-sidebar">

    {{-- Sidebar Header --}}
    <div class="sidebar-header">
        <h3 style="margin:0; font-size:16px; font-weight:600; color:#333;">
            APPLICATIONS
        </h3>
        <button onclick="toggleApplicationsSidebar()" class="sidebar-close-btn">
            ×
        </button>
    </div>
    <div style="
padding:0px 15px 0px;
font-size:15px;
color:#777;
text-align:center;
line-height:1.4;
">

        Drag the layers to adjust which Application is below or above another Application

    </div>
    {{-- Layers List --}}
    <div id="applicationLayersList" class="sidebar-layers-list">
    </div>

    <div style="
display:flex;
align-items:center;
justify-content:space-between;
gap:10px;
padding:12px 15px;
">

        <!-- LEFT SIDE -->
        <label onclick="toggleLocationMarkers()"
            style="
display:flex;
align-items:center;
gap:8px;
cursor:pointer;
font-size:13px;
color:#666;
white-space:nowrap;
">

            <i class="fas fa-map-marker-alt"></i>
            Show Location Markers

        </label>


        <!-- RIGHT SIDE BUTTON -->
        <button onclick="openApplicationModal()"
            style="
padding:10px 14px;
background:#888888;
color:white;
border:none;
border-radius:6px;
font-weight:600;
cursor:pointer;
font-size:13px;
display:flex;
align-items:center;
gap:6px;
">

            <i class="fas fa-plus-circle"></i>
            Add New free form Application

        </button>

    </div>

</div>

{{-- =================== APPLICATION TOOL PAGE (RIGHT PANEL) =================== --}}
<div id="applicationPage" class="tool-page" style="display:none;">

    {{-- Layer Controls (shown when layer is selected) --}}
    <div id="applicationLayerControls" style="display:none; margin-top:20px;">

        <h4 style="margin:0 0 15px 0; font-size:20px; font-weight:600; color:#333; text-align:center;">
            Edit Application
        </h4>

        {{-- ============================================================ --}}
        {{-- 🦅 DIRECT MASCOT CONTROLS (shown only for mascot-type layers) --}}
        {{-- ============================================================ --}}
        <div id="directMascotControls" style="display:none;">

            {{-- Mascot Preview --}}
            <div style="display:flex; gap:16px; margin-bottom:20px; align-items:flex-start;">

                <!-- LEFT: Mascot Preview -->
                <div style="padding:16px;  border-radius:10px; text-align:center;">

                    <div style="font-size:11px; font-weight:700; color:#999; letter-spacing:1px; margin-bottom:10px;">
                        MASCOT PREVIEW
                    </div>

                    <div id="directMascotPreview"
                        style="width:100px; height:100px; margin:0 auto 12px; background:#e8e8e8; border-radius:8px;
            display:flex; align-items:center; justify-content:center; overflow:hidden;">

                        <!-- mascot image inject hogi -->
                        <img id="directMascotPreviewImg" src=""
                            style="max-width:100%; max-height:100%; object-fit:contain; display:none;" />

                        <span id="directMascotPlaceholder" style="color:#aaa; font-size:11px;">
                            No mascot
                        </span>

                    </div>

                    <button onclick="changeDirectMascot()"
                        style="width:100%; padding:10px; background:#1a1a1a; color:#fff;
            border:none; border-radius:6px; font-weight:700; font-size:12px;
            cursor:pointer; letter-spacing:.5px;">
                        Change Mascot
                    </button>

                </div>


                <!-- RIGHT: Mascot Colors -->
                <div id="directMascotColorSection" style="flex:1; padding:16px;  border-radius:10px;">

                    <div style="font-size:11px; font-weight:700; color:#999; letter-spacing:1px; margin-bottom:10px;">
                        MASCOT COLORS
                    </div>

                    <div id="directMascotColorSwatches" style="display:flex; flex-direction:column; gap:12px;">

                        <!-- dynamic rows here -->

                    </div>

                </div>

            </div>

            {{-- Scale --}}
            <div class="control-group" style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; font-size:14px; margin-bottom:8px; color:#333;">
                    Scale: <span id="directMascotScaleValue">100</span>%
                </label>
                <input type="range" id="directMascotScale" min="10" max="200" value="100"
                    oninput="updateDirectMascotScale(this.value); document.getElementById('directMascotScaleValue').textContent=this.value;"
                    class="app-slider" style="width:100%; cursor:pointer;">
            </div>

            {{-- Opacity --}}
            <div class="control-group" style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; font-size:14px; margin-bottom:8px; color:#333;">
                    Opacity: <span id="directMascotOpacityValue">100</span>%
                </label>
                <input type="range" id="directMascotOpacity" min="0" max="100" value="100"
                    oninput="updateDirectMascotOpacity(this.value); document.getElementById('directMascotOpacityValue').textContent=this.value;"
                    class="app-slider" style="width:100%; cursor:pointer;">
            </div>

            {{-- Rotation --}}
            <div class="control-group" style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; font-size:14px; margin-bottom:8px; color:#333;">
                    Rotation: <span id="directMascotRotationValue">0</span>°
                </label>
                <input type="range" id="directMascotRotation" min="0" max="360" value="0"
                    oninput="updateDirectMascotRotation(this.value); document.getElementById('directMascotRotationValue').textContent=this.value;"
                    class="app-slider" style="width:100%; cursor:pointer;">
            </div>

            {{-- Position --}}
            <div class="control-group" style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; font-size:14px; margin-bottom:8px; color:#333;">
                    Position
                </label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label style="font-size:12px; color:#666;">X: <span id="mascotDirectPosXValue">0</span></label>
                        <input type="range" id="mascotDirectPosX" min="-500" max="500" value="0"
                            oninput="updateDirectMascotPosition('x', this.value)" class="app-slider"
                            style="width:100%; cursor:pointer;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:#666;">Y: <span id="mascotDirectPosYValue">0</span></label>
                        <input type="range" id="mascotDirectPosY" min="-500" max="500" value="0"
                            oninput="updateDirectMascotPosition('y', this.value)" class="app-slider"
                            style="width:100%; cursor:pointer;">
                    </div>
                </div>
            </div>

            {{-- Delete --}}
            <button onclick="deleteCurrentApplicationLayer()"
                style="width:100%; padding:12px; background:#000000; color:white; border:none; border-radius:6px; font-weight:600; cursor:pointer; margin-top:10px;">
                Delete Mascot
            </button>

        </div>

        {{-- ============================================================ --}}
        {{-- 📝 TEXT LAYER CONTROLS (number / teamname / playername)       --}}
        {{-- ============================================================ --}}
        <div id="textLayerControls" style="display:block;">


            <!-- ===== ROW 1: Text label + Select Font button ===== -->
            <div class="control-group" style="margin-bottom:15px;">

                <!-- LABEL ROW -->
                <div style="display:flex; gap:45px; margin-bottom:6px;">

                    <label style="flex:1; font-weight:600; font-size:14px; color:#333;">
                        Text
                    </label>

                    <label style="flex:1; font-weight:600; font-size:14px;  color:#333;">
                        Font
                    </label>

                </div>

                <!-- INPUT ROW -->
                <div style="display:flex; gap:30px;">

                    <!-- TEXT INPUT -->
                    <input type="text" id="applicationText" placeholder="23"
                        oninput="updateApplicationText(this.value)"
                        style="
                    flex:1;
                    height:42px;
                    padding:10px;
                    font-size:14px;
                ">

                    <!-- FONT BUTTON -->
                    <button id="selectFontBtn" onclick="openFontModal()"
                        style="
                    flex:1;
                    height:42px;
                    border:1px solid #ddd;
                    background:#f5f5f5;
                    font-size:22px;
                    font-weight:500;
                    cursor:pointer;
                    font-family:inherit;
                ">
                        Select Font
                    </button>

                </div>

            </div>






            {{-- ===== ROW 3: Position Controls ===== --}}
            <div class="control-group" style="margin-bottom:15px;">

                {{-- SIZE ROW --}}
                <div style="display:flex; align-items:center; gap:30%; margin-bottom:14px;">
                    <label style="font-weight:600; font-size:13px; color:#333; width:60px; flex-shrink:0;">Size</label>
                    <div style="flex:1; min-width:0; position:relative; padding-top:26px;">
                        <div id="fontSizeBubble"
                            style="
            position:absolute; top:0; left:0%;
            transform:translateX(-50%);
            background:#fff; border:1.5px solid #ccc;
            border-radius:5px; padding:1px 6px;
            font-size:11px; font-weight:600; color:#333;
            display:none; pointer-events:none; white-space:nowrap;">
                            50</div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:3px;">
                            <span style="font-size:10px; color:#aaa;">Small</span>
                            <span style="font-size:10px; color:#aaa;">Large</span>
                        </div>
                        <input type="range" id="fontSize" min="50" max="5500" value="50"
                            class="app-slider"
                            style="width:100%; cursor:pointer; box-sizing:border-box;
                   background:linear-gradient(to right, #000 0%, #ddd 0%);"
                            oninput="
                updateFontSize(this.value);
                document.getElementById('fontSizeValue').textContent = this.value;
                appMoveBub(this, 'fontSizeBubble');
                appFillSlider(this);
                appHideBub('posXBubble');
                appHideBub('posYBubble');
            ">
                        <span id="fontSizeValue" style="display:none;">50</span>
                    </div>
                </div>

                {{-- POSITION ROW --}}
                <div style="display:flex; align-items:flex-start; gap:30%; margin-bottom:12px;">
                    <label
                        style="font-weight:600; font-size:13px; color:#333; width:60px; flex-shrink:0; padding-top:4px;">Position</label>
                    <div style="flex:1; min-width:0; display:flex; flex-direction:column; gap:10px;">

                        {{-- Left / Right --}}
                        <div style="position:relative; padding-top:26px;">
                            <div id="posXBubble"
                                style="
                position:absolute; top:0; left:50%;
                transform:translateX(-50%);
                background:#fff; border:1.5px solid #ccc;
                border-radius:5px; padding:1px 6px;
                font-size:11px; font-weight:600; color:#333;
                display:none; pointer-events:none; white-space:nowrap;">
                                0</div>
                            <div style="display:flex; justify-content:space-between; margin-bottom:3px;">
                                <span style="font-size:10px; color:#aaa;">Left</span>
                                <span style="font-size:10px; color:#aaa;">Right</span>
                            </div>
                            <input type="range" id="posX" min="-200" max="200" value="0"
                                class="app-slider"
                                style="width:100%; cursor:pointer; box-sizing:border-box;
                       background:linear-gradient(to right, #000 50%, #ddd 50%);"
                                oninput="
                    updatePosition(this.value, null);
                    document.getElementById('posXValue').textContent = this.value;
                    appMoveBub(this, 'posXBubble');
                    appFillSlider(this);
                    appHideBub('posYBubble');
                    appHideBub('fontSizeBubble');
                ">
                            <span id="posXValue" style="display:none;">0</span>
                        </div>

                        {{-- Up / Down --}}
                        <div style="position:relative; padding-top:26px;">
                            <div id="posYBubble"
                                style="
                position:absolute; top:0; left:50%;
                transform:translateX(-50%);
                background:#fff; border:1.5px solid #ccc;
                border-radius:5px; padding:1px 6px;
                font-size:11px; font-weight:600; color:#333;
                display:none; pointer-events:none; white-space:nowrap;">
                                0</div>
                            <div style="display:flex; justify-content:space-between; margin-bottom:3px;">
                                <span style="font-size:10px; color:#aaa;">Up</span>
                                <span style="font-size:10px; color:#aaa;">Down</span>
                            </div>
                            <input type="range" id="posY" min="-200" max="200" value="0"
                                class="app-slider"
                                style="width:100%; cursor:pointer; box-sizing:border-box;
                       background:linear-gradient(to right, #000 50%, #ddd 50%);"
                                oninput="
                    updatePosition(null, this.value);
                    document.getElementById('posYValue').textContent = this.value;
                    appMoveBub(this, 'posYBubble');
                    appFillSlider(this);
                    appHideBub('posXBubble');
                    appHideBub('fontSizeBubble');
                ">
                            <span id="posYValue" style="display:none;">0</span>
                        </div>
                    </div>
                </div>







            </div>

            {{-- ===== ROW 2 + Rotation: Side by Side ===== --}}
            <div
                style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px; align-items:center;">

                {{-- LEFT: Letter Spacing + Width + Height --}}
                <div style="display:flex; flex-direction:column; gap:10px;">

                    <!-- Letter Spacing -->
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                        <label style="font-weight:600; font-size:12px; color:#333; white-space:nowrap;">Letter
                            Spacing</label>
                        <input type="number" value="0" oninput="updateLetterSpacing(this.value)"
                            style="width:70px; padding:6px 8px; border:2px solid #ddd; border-radius:6px; font-size:13px; text-align:center;">
                    </div>

                    <!-- Width -->
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                        <label style="font-weight:600; font-size:12px; color:#333; white-space:nowrap;">Width %</label>
                        <input type="number" value="100" min="10" max="300"
                            oninput="updateTextScale('x', this.value)"
                            style="width:70px; padding:6px 8px; border:2px solid #ddd; border-radius:6px; font-size:13px; text-align:center;">
                    </div>

                    <!-- Height -->
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                        <label style="font-weight:600; font-size:12px; color:#333; white-space:nowrap;">Height
                            %</label>
                        <input type="number" value="100" min="10" max="300"
                            oninput="updateTextScale('y', this.value)"
                            style="width:70px; padding:6px 8px; border:2px solid #ddd; border-radius:6px; font-size:13px; text-align:center;">
                    </div>

                </div>

                {{-- RIGHT: Rotation Wheel --}}
                {{-- Rotation Wheel --}}
                <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px;">
                    <label style="font-weight:600; font-size:13px; color:#333;">Rotation</label>
                    <input type="hidden" id="rotation" value="0">

                    <div style="position:relative; width:160px; height:160px; flex-shrink:0;">

                        <svg id="rotationSvg" width="160" height="160"
                            style="position:absolute; top:0; left:0; cursor:grab;">
                            {{-- Background circle --}}
                            <circle cx="80" cy="80" r="68" fill="#e0e0e0" stroke="#bbb"
                                stroke-width="3" />
                            {{-- Progress arc --}}
                            <circle id="rotationArc" cx="80" cy="80" r="68" fill="none"
                                stroke="#333" stroke-width="14" stroke-linecap="butt" stroke-dasharray="0 427.26"
                                transform="rotate(-90 80 80)" style="transition:none;" />
                            {{-- Inner circle --}}
                            <circle cx="80" cy="80" r="52" fill="#888" />
                            {{-- Dot at top (cy = 80 - 68 = 12) --}}
                            <circle id="rotationDot" cx="80" cy="12" r="9" fill="#222"
                                stroke="#fff" stroke-width="2.5" />
                        </svg>

                        {{-- Center input --}}
                        <div
                            style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none;">
                            <input type="number" id="rotationManual" min="0" max="360" value="0"
                                style="width:56px; height:40px; text-align:center;
                       border-radius:8px; border:2px solid #ccc;
                       font-weight:700; font-size:14px;
                       background:#fff; color:#222;
                       -moz-appearance:textfield;
                       pointer-events:all;"
                                oninput="
                    var v = Math.min(360, Math.max(0, parseInt(this.value) || 0));
                    setWheelAngle(v);
                    updateRotation(v);
                ">
                        </div>
                    </div>

                    <span style="font-size:13px; color:#666; font-weight:600;">
                        <span id="rotationValue">0</span>°
                    </span>
                </div>

            </div>

            {{-- ========== COLORS/PATTERN/MASCOT TABS ========== --}}
            <div style="margin-top:25px; margin-bottom:15px;">

                {{-- Tab Headers --}}
                <div style="display:flex; gap:15px; border-bottom:2px solid #e0e0e0; margin-bottom:20px;">
                    <button onclick="switchTextCustomizationTab('colors')" id="tabColors" class="text-custom-tab"
                        style="flex:1; padding:12px; background:#fff; border:none; border-bottom:13px solid #000; font-weight:600; cursor:pointer; transition:all 0.3s; color:#333;">
                        Colors
                    </button>
                    <button onclick="switchTextCustomizationTab('pattern')" id="tabPattern" class="text-custom-tab"
                        style="flex:1; padding:12px; background:#fff; border:none; border-bottom:13px solid transparent; font-weight:600; cursor:pointer; transition:all 0.3s; color:#999;">
                        Pattern
                    </button>
                    <button onclick="switchTextCustomizationTab('mascot')" id="tabMascot" class="text-custom-tab"
                        style="flex:1; padding:12px; background:#fff; border:none; border-bottom:13px solid transparent; font-weight:600; cursor:pointer; transition:all 0.3s; color:#999;">
                        Mascot
                    </button>
                </div>

                {{-- Tab Content: COLORS --}}
                <div id="colorsTabContent" class="tab-content" style="display:block;">
                    <div style="display:flex; gap:5px; align-items:flex-start;">

                        <div id="currentOutlineDisplay" onclick="openAccentsModal()"
                            style="cursor:pointer; text-align:center; margin-bottom:15px; display:none; width:30%;">
                            <div id="outlineStylePreview"
                                style="font-size:65px; font-weight:900; margin-bottom:8px; line-height:1;"></div>
                            <div id="outlineStyleName" style="font-size:13px; color:#333; font-weight:600;"></div>
                        </div>
                        <div id="outlineColorsSection" style="display:none; width:100%;">

                            <!-- Text-Color -->
                            <div class="control-group"
                                style="display:flex; align-items:center; gap:10px; margin-bottom:12px; flex-wrap:nowrap;">
                                <label
                                    style="font-weight:500; font-size:12px; color:#333; width:75px; flex-shrink:0;">Text-Color</label>
                                <div id="baseColorPicker"
                                    style="display:flex; gap:5px; flex-wrap:nowrap; align-items:center;"></div>
                            </div>

                            <!-- Outline 1 -->
                            <div id="outline1Section" class="control-group"
                                style="display:flex; align-items:center; gap:10px; margin-bottom:12px; flex-wrap:nowrap;">
                                <label
                                    style="font-weight:500; font-size:12px; color:#333; width:75px; flex-shrink:0;">Outline
                                    1</label>
                                <div id="outline1ColorPicker"
                                    style="display:flex; gap:5px; flex-wrap:nowrap; align-items:center; flex-shrink:0;">
                                </div>
                                <input type="number" min="0" value="3"
                                    onchange="updateOutlineStroke('outline1', this.value)"
                                    style="width:55px; padding:4px 6px; border:1px solid #ccc; border-radius:4px; font-size:12px; text-align:center; flex-shrink:0;">
                            </div>

                            <!-- Outline 2 -->
                            <div id="outline2Section" class="control-group"
                                style="display:none; align-items:center; gap:10px; margin-bottom:12px; flex-wrap:nowrap;">
                                <label
                                    style="font-weight:500; font-size:12px; color:#333; width:75px; flex-shrink:0;">Outline
                                    2</label>
                                <div id="outline2ColorPicker"
                                    style="display:flex; gap:5px; flex-wrap:nowrap; align-items:center; flex-shrink:0;">
                                </div>
                                <input type="number" min="0" value="3"
                                    onchange="updateOutlineStroke('outline2', this.value)"
                                    style="width:55px; padding:4px 6px; border:1px solid #ccc; border-radius:4px; font-size:12px; text-align:center; flex-shrink:0;">
                            </div>

                            <!-- Shadow -->
                            <div id="shadowSection" class="control-group"
                                style="display:none; align-items:center; gap:10px; margin-bottom:12px; flex-wrap:nowrap;">
                                <label
                                    style="font-weight:500; font-size:12px; color:#333; width:75px; flex-shrink:0;">Shadow</label>
                                <div id="shadowColorPicker"
                                    style="display:flex; gap:5px; flex-wrap:nowrap; align-items:center; flex-shrink:0;">
                                </div>
                                <input type="number" min="0" value="3"
                                    onchange="updateShadowOffset(this.value)"
                                    style="width:55px; padding:4px 6px; border:1px solid #ccc; border-radius:4px; font-size:12px; text-align:center; flex-shrink:0;">
                            </div>

                            <!-- ✅ Corners — ab yahan andar hai, same column mein -->
                            <div id="cornersSection" class="control-group"
                                style="display:flex; align-items:center; gap:10px; margin-bottom:12px; flex-wrap:nowrap;">
                                <label
                                    style="font-weight:500; font-size:12px; color:#333; width:75px; flex-shrink:0;">Corners</label>

                                <!-- Miter -->
                                <button class="stroke-shape-btn selected" id="join-miter"
                                    onclick="selectStrokeLinejoin('miter', this)">
                                    <i class="fa-solid fa-angle-up" style="font-size:16px;"></i>
                                    <span style="font-size:10px;">Miter</span>
                                </button>

                                <!-- Round -->
                                <button class="stroke-shape-btn" id="join-round"
                                    onclick="selectStrokeLinejoin('round', this)">
                                    <i class="fa-solid fa-circle-notch" style="font-size:16px;"></i>
                                    <span style="font-size:10px;">Round</span>
                                </button>

                                <!-- Bevel -->
                                <button class="stroke-shape-btn" id="join-bevel"
                                    onclick="selectStrokeLinejoin('bevel', this)">
                                    <i class="fa-solid fa-diamond" style="font-size:16px;"></i>
                                    <span style="font-size:10px;">Bevel</span>
                                </button>


                            </div>

                        </div>
                    </div>

                </div>

                {{-- Tab Content: PATTERN --}}
                {{-- Tab Content: PATTERN --}}
                <div id="patternTabContent" class="tab-content" style="display:none;">

                    {{-- Preview Button (image thumbnail) --}}
                    <div style="display:flex; align-items:flex-start; gap:16px; margin-bottom:18px;">
                        <div onclick="openTextPatternLibrary()"
                            style="cursor:pointer; flex-shrink:0; text-align:center;">
                            <div style="width:70px; height:70px; border-radius:8px; border:2px solid #ddd; background:#f5f5f5;
                        display:flex; align-items:center; justify-content:center; overflow:hidden; transition:border-color 0.2s;"
                                onmouseover="this.style.borderColor='#000'"
                                onmouseout="this.style.borderColor='#ddd'">
                                <img id="textPatternThumbnail" src="/assets/images/pattern logo.avif"
                                    style="width:100%; height:100%; object-fit:cover; border-radius:6px;">
                            </div>
                            <div style="font-size:11px; color:#333; font-weight:600; margin-top:5px;">Pattern Fill
                            </div>
                        </div>

                        {{-- Colors inline with the button --}}
                        <div id="textPatternColorControls" style="display:none; flex:1;">
                            <div
                                style="font-size:11px; font-weight:700; color:#999; letter-spacing:1px; margin-bottom:8px;">
                                PATTERN COLORS</div>
                            <div id="patternColorPaletteInTab"></div>
                        </div>
                    </div>

                    {{-- Size + Opacity sliders --}}
                    <div id="textPatternSizeOpacityControls" style="display:none;">
                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; font-size:13px;">Pattern Size: <span
                                    id="patternSizeValueTab">100</span>%</label>
                            <input type="range" min="10" max="200" value="100" id="patternSizeTab"
                                oninput="updateTextPatternSize(this.value); document.getElementById('patternSizeValueTab').textContent=this.value;"
                                class="app-slider" style="width:100%; cursor:pointer;">
                        </div>
                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; font-size:13px;">Pattern Opacity: <span
                                    id="patternOpacityValueTab">100</span>%</label>
                            <input type="range" min="0" max="100" value="100"
                                id="patternOpacityTab"
                                oninput="updateTextPatternOpacity(this.value); document.getElementById('patternOpacityValueTab').textContent=this.value;"
                                class="app-slider" style="width:100%; cursor:pointer;">
                        </div>
                        <button onclick="clearTextPattern()"
                            style="width:100%; padding:10px; background:#000; color:white; border:none; border-radius:6px; font-weight:600; cursor:pointer;">
                            Clear Pattern
                        </button>
                    </div>

                    <p id="patternPlaceholder"
                        style="color:#999; text-align:center; padding:30px 20px; font-size:13px;">
                        Click the pattern button to select a pattern fill
                    </p>
                </div>

                {{-- Tab Content: MASCOT (text fill) --}}
                {{-- Tab Content: MASCOT (text fill) --}}
                <div id="mascotTabContent" class="tab-content" style="display:none;">

                    {{-- Preview Button + Colors side by side --}}
                    {{-- Preview Button + Controls side by side --}}
                    <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:18px;">

                        {{-- Mascot thumbnail button --}}
                        <div onclick="openTextMascotLibrary()"
                            style="cursor:pointer; flex-shrink:0; text-align:center;">
                            <div style="width:70px; height:70px; border-radius:8px; border:2px solid #ddd;
                    background:#f5f5f5; display:flex; align-items:center; justify-content:center;
                    overflow:hidden; transition:border-color 0.2s;"
                                onmouseover="this.style.borderColor='#000'"
                                onmouseout="this.style.borderColor='#ddd'">
                                <div id="textMascotPreview"
                                    style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                                    <span style="font-size:26px; opacity:0.3;">🦅</span>
                                </div>
                            </div>
                            <div
                                style="font-size:10px; color:#666; font-weight:600; margin-top:4px; letter-spacing:.3px;">
                                MASCOT FILL</div>
                        </div>

                        {{-- Right side info (shown after mascot selected) --}}
                        <div id="textMascotColorControls" style="display:none; flex:1;">
                            <div
                                style="font-size:10px; font-weight:700; color:#999; letter-spacing:1px; margin-bottom:6px;">
                                MASCOT APPLIED</div>
                            <button onclick="openTextMascotLibrary()"
                                style="width:100%; padding:7px 10px; background:#f0f0f0; color:#000; border:2px solid #ddd;
                   border-radius:6px; font-weight:600; font-size:11px; cursor:pointer;">
                                Change Mascot
                            </button>
                            <button onclick="clearTextMascot()"
                                style="width:100%; padding:7px 10px; background:#000; color:#fff; border:none;
                   border-radius:6px; font-weight:600; font-size:11px; cursor:pointer; margin-top:6px;">
                                Clear Mascot
                            </button>
                        </div>
                    </div>

                    {{-- Sliders (shown after mascot selected) --}}
                    <div id="textMascotSizeOpacityControls" style="display:none;">
                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; font-size:13px; color:#333;">
                                Size: <span id="mascotSizeValueTab">100</span>%
                            </label>
                            <input type="range" min="10" max="200" value="100"
                                id="mascotSizeTabSlider"
                                oninput="updateTextMascotSize(this.value); document.getElementById('mascotSizeValueTab').textContent=this.value;"
                                class="app-slider" style="width:100%; cursor:pointer;">
                        </div>
                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; font-size:13px; color:#333;">
                                Opacity: <span id="mascotOpacityValueTab">100</span>%
                            </label>
                            <input type="range" min="0" max="100" value="100"
                                id="mascotOpacityTabSlider"
                                oninput="updateTextMascotOpacity(this.value); document.getElementById('mascotOpacityValueTab').textContent=this.value;"
                                class="app-slider" style="width:100%; cursor:pointer;">
                        </div>
                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; font-size:13px; color:#333;">
                                Count: <span id="mascotCountValueTab">4</span>
                            </label>
                            <input type="range" min="1" max="12" value="4"
                                id="mascotCountTabSlider"
                                oninput="updateTextMascotCount(this.value); document.getElementById('mascotCountValueTab').textContent=this.value;"
                                class="app-slider" style="width:100%; cursor:pointer;">
                        </div>
                    </div>

                    <p id="mascotPlaceholder"
                        style="color:#bbb; text-align:center; padding:20px 20px 10px; font-size:12px; font-style:italic;">
                        Click above to fill your text with a tiled mascot
                    </p>

                    {{-- Sliders --}}
                    <div id="textMascotSizeOpacityControls" style="display:none;">
                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; font-size:13px;">Mascot Size: <span
                                    id="mascotSizeValueTab">100</span>%</label>
                            <input type="range" min="10" max="200" value="100"
                                id="mascotSizeTabSlider"
                                oninput="updateTextMascotSize(this.value); document.getElementById('mascotSizeValueTab').textContent=this.value;"
                                class="app-slider" style="width:100%; cursor:pointer;">
                        </div>
                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; font-size:13px;">Mascot Opacity: <span
                                    id="mascotOpacityValueTab">100</span>%</label>
                            <input type="range" min="0" max="100" value="100"
                                id="mascotOpacityTabSlider"
                                oninput="updateTextMascotOpacity(this.value); document.getElementById('mascotOpacityValueTab').textContent=this.value;"
                                class="app-slider" style="width:100%; cursor:pointer;">
                        </div>
                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; font-size:13px;">Mascot Count: <span
                                    id="mascotCountValueTab">4</span></label>
                            <input type="range" min="1" max="12" value="4"
                                id="mascotCountTabSlider"
                                oninput="updateTextMascotCount(this.value); document.getElementById('mascotCountValueTab').textContent=this.value;"
                                class="app-slider" style="width:100%; cursor:pointer;">
                        </div>
                        <button onclick="clearTextMascot()"
                            style="width:100%; padding:10px; background:#000; color:white; border:none; border-radius:6px; font-weight:600; cursor:pointer;">
                            Clear Mascot
                        </button>
                    </div>

                    <p id="mascotPlaceholder"
                        style="color:#999; text-align:center; padding:30px 20px; font-size:13px;">
                        Click the mascot button to fill your text with a mascot
                    </p>
                </div>

            </div>

        </div>

    </div>

</div>

{{-- =================== ACCENTS MODAL (OUTLINE STYLES) =================== --}}
<div id="accentsModal" class="color-modal" style="display:none;">
    <div class="color-modal-content" style="width:800px; max-width:95%;">

        <div
            style="padding:20px; background:#2a2a2a; color:white; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:18px; font-weight:600;">ACCENTS</h3>
            <span onclick="closeAccentsModal()"
                style="cursor:pointer; font-size:28px; width:36px; height:36px; display:flex; align-items:center; justify-content:center; border-radius:50%;">×</span>
        </div>

        <div style="padding:30px;">
            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:20px;">

                <div class="accent-card" onclick="selectAccentStyle('single')"
                    style="cursor:pointer; border:3px solid #e0e0e0; border-radius:8px; padding:20px; text-align:center; transition:all 0.3s; background:white;">
                    <div style="font-size:48px; font-weight:900; margin-bottom:10px; color:#000;">T</div>
                    <div
                        style="font-weight:600; font-size:14px; padding:8px; background:#000; color:white; border-radius:4px;">
                        Single Color</div>
                </div>

                <div class="accent-card" onclick="selectAccentStyle('two-color')"
                    style="cursor:pointer; border:3px solid #e0e0e0; border-radius:8px; padding:20px; text-align:center; transition:all 0.3s; background:white;">
                    <div style="font-size:48px; font-weight:900; margin-bottom:10px;">
                        <span style="-webkit-text-fill-color:#333; -webkit-text-stroke:2px #999;">T</span>
                    </div>
                    <div
                        style="font-weight:600; font-size:14px; padding:8px; background:#000; color:white; border-radius:4px;">
                        Two Color</div>
                </div>

                <div class="accent-card" onclick="selectAccentStyle('two-color-shadow')"
                    style="cursor:pointer; border:3px solid #e0e0e0; border-radius:8px; padding:20px; text-align:center; transition:all 0.3s; background:white;">
                    <div style="font-size:48px; font-weight:900; margin-bottom:10px;">
                        <span
                            style="-webkit-text-fill-color:#fff; -webkit-text-stroke:2px #333; filter:drop-shadow(3px 3px 0 #666);">T</span>
                    </div>
                    <div
                        style="font-weight:600; font-size:14px; padding:8px; background:#000; color:white; border-radius:4px;">
                        Two Color with Drop Shadow</div>
                </div>

                <div class="accent-card" onclick="selectAccentStyle('three-color')"
                    style="cursor:pointer; border:3px solid #e0e0e0; border-radius:8px; padding:20px; text-align:center; transition:all 0.3s; background:white;">
                    <div style="font-size:48px; font-weight:900; margin-bottom:10px; position:relative;">
                        <span
                            style="position:absolute; left:50%; transform:translateX(-50%); -webkit-text-fill-color:transparent; -webkit-text-stroke:4px #999;">T</span>
                        <span style="-webkit-text-fill-color:#333; -webkit-text-stroke:2px #ccc;">T</span>
                    </div>
                    <div
                        style="font-weight:600; font-size:14px; padding:8px; background:#000; color:white; border-radius:4px;">
                        Three Color</div>
                </div>

                <div class="accent-card" onclick="selectAccentStyle('single-shadow')"
                    style="cursor:pointer; border:3px solid #e0e0e0; border-radius:8px; padding:20px; text-align:center; transition:all 0.3s; background:white;">
                    <div style="font-size:48px; font-weight:900; margin-bottom:10px;">
                        <span style="color:#000; filter:drop-shadow(3px 3px 0 #666);">T</span>
                    </div>
                    <div
                        style="font-weight:600; font-size:14px; padding:8px; background:#000; color:white; border-radius:4px;">
                        Single Color with Drop Shadow</div>
                </div>

                <div class="accent-card" onclick="selectAccentStyle('three-color-shadow')"
                    style="cursor:pointer; border:3px solid #e0e0e0; border-radius:8px; padding:20px; text-align:center; transition:all 0.3s; background:white;">
                    <div style="font-size:48px; font-weight:900; margin-bottom:10px; position:relative;">
                        <span
                            style="position:absolute; left:50%; transform:translateX(-50%); -webkit-text-fill-color:transparent; -webkit-text-stroke:4px #999; filter:drop-shadow(3px 3px 0 #666);">T</span>
                        <span
                            style="-webkit-text-fill-color:#333; -webkit-text-stroke:2px #ccc; filter:drop-shadow(3px 3px 0 #666);">T</span>
                    </div>
                    <div
                        style="font-weight:600; font-size:14px; padding:8px; background:#000; color:white; border-radius:4px;">
                        Three Color with Drop Shadow</div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- FONT MODAL — 1000px wide, 6 per row --}}
<div id="fontModal" class="color-modal" style="display:none;">
    <div class="color-modal-content" style="width:1000px; max-width:96%;">
        <div
            style="padding:20px; background:#2a2a2a; color:white; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:18px; font-weight:600;">SELECT FONT</h3>
            <span onclick="closeFontModal()" style="cursor:pointer; font-size:26px; line-height:1;">×</span>
        </div>
        <div id="fontGrid"
            style="padding:20px; display:grid; grid-template-columns:repeat(6, 1fr); gap:12px; max-height:70vh; overflow-y:auto;">
        </div>
    </div>
</div>

{{-- =================== APPLICATION MODAL =================== --}}
<div id="applicationModal" class="color-modal" style="display:none;">
    <div class="color-modal-content" style="width:900px; max-width:95%;">

        <div
            style="padding:20px; background:#2a2a2a; color:white; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:18px; font-weight:600;">Add a new Free Form Application</h3>
            <span onclick="closeApplicationModal()"
                style="cursor:pointer; font-size:28px; width:36px; height:36px; display:flex; align-items:center; justify-content:center; border-radius:50%;">×</span>
        </div>

        <div style="display:flex; gap:20px;">
            <div style="flex:1; padding:30px; border-right:1px solid #e0e0e0;">

                <div style="margin-bottom:30px;">
                    <h4 style="margin:0 0 15px 0; font-size:15px; font-weight:600; color:#333;">
                        1. What type of application do you want to add?
                    </h4>
                    <div class="application-type-grid">
                        <div class="app-type-card" data-type="number" onclick="selectApplicationType('number')">
                            <div class="app-icon">#</div>
                            <div class="app-label">Player #</div>
                        </div>
                        <div class="app-type-card" data-type="teamname" onclick="selectApplicationType('teamname')">
                            <div class="app-icon">A</div>
                            <div class="app-label">Team Name</div>
                        </div>
                        <div class="app-type-card" data-type="playername"
                            onclick="selectApplicationType('playername')">
                            <div class="app-icon">A</div>
                            <div class="app-label">Player Name</div>
                        </div>
                        <div class="app-type-card" data-type="mascot" onclick="selectApplicationType('mascot')">
                            <div class="app-icon">🦅</div>
                            <div class="app-label">Custom Mascot</div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom:30px;">
                    <h4 style="margin:0 0 15px 0; font-size:15px; font-weight:600; color:#333;">
                        2. What perspective do you want to add the application?
                    </h4>
                    <div class="perspective-grid">
                        <button class="perspective-btn selected" data-view="front"
                            onclick="selectPerspective('front')">Front</button>
                        <button class="perspective-btn" data-view="back"
                            onclick="selectPerspective('back')">Back</button>
                        <button class="perspective-btn" data-view="left"
                            onclick="selectPerspective('left')">Left</button>
                        <button class="perspective-btn" data-view="right"
                            onclick="selectPerspective('right')">Right</button>
                    </div>
                </div>

                <div style="margin-bottom:30px;">
                    <h4 style="margin:0 0 15px 0; font-size:15px; font-weight:600; color:#333;">
                        3. Which part do you want to add the application on?
                    </h4>
                    <div id="partSelectionGrid" class="part-grid"></div>
                </div>

                <div style="display:flex; gap:10px; margin-top:30px;">
                    <button onclick="closeApplicationModal()"
                        style="flex:1; padding:14px; background:#6c757d; color:white; border:none; border-radius:8px; font-size:16px; font-weight:600; cursor:pointer;">
                        Cancel
                    </button>
                    <button onclick="confirmAddApplication()"
                        style="flex:1; padding:14px; background:#000000; color:white; border:none; border-radius:8px; font-size:16px; font-weight:600; cursor:pointer;">
                        Ok
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    body {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    /* ===== SQUARE SLIDER THUMB ===== */
    /* ===== SQUARE FILLED SLIDER ===== */
    .app-slider {
        -webkit-appearance: none;
        appearance: none;
        height: 4px;
        background: linear-gradient(to right, #000 0%, #000 0%, #ddd 0%, #ddd 100%);
        border-radius: 2px;
        outline: none;
        cursor: pointer;
    }

    /* Square thumb - webkit */
    .app-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 16px;
        height: 16px;
        background: #000;
        border-radius: 3px;
        /* square box */
        cursor: pointer;
        border: none;
    }

    /* Square thumb - firefox */
    .app-slider::-moz-range-thumb {
        width: 16px;
        height: 16px;
        background: #000;
        border-radius: 3px;
        cursor: pointer;
        border: none;
    }

    text:focus {
        outline: none !important;
    }

    .app-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 16px;
        height: 16px;
        background: #000;
        border-radius: 3px;
        /* square box */
        cursor: pointer;
        border: none;
    }

    .app-slider::-moz-range-thumb {
        width: 16px;
        height: 16px;
        background: #000;
        border-radius: 3px;
        cursor: pointer;
        border: none;
    }

    /* Smaller slider for width/height */
    .app-slider-small {
        height: 3px;
    }

    .app-slider-small::-webkit-slider-thumb {
        width: 11px;
        height: 11px;
    }

    .app-slider-small::-moz-range-thumb {
        width: 11px;
        height: 11px;
    }

    /* Rotation vertical slider */
    .rotation-vertical-slider {
        -webkit-appearance: none;
        appearance: none;
        height: 4px;
        background: #e6e5e5;
        border-radius: 2px;
        outline: none;
    }

    .rotation-vertical-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 14px;
        height: 14px;
        background: #000;
        border-radius: 2px;
        cursor: pointer;
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .rotation-vertical-slider::-moz-range-thumb {
        width: 14px;
        height: 14px;
        background: #000;
        border-radius: 2px;
        cursor: pointer;
        border: none;
    }

    /* Font grid items */
    #fontGrid>div {
        border: 2px solid #ddd;
        padding: 10px 6px;
        text-align: center;
        border-radius: 8px;
        cursor: pointer;
        background: #fff;
        transition: all 0.2s;
    }

    #fontGrid>div:hover {
        background: #f2f2f2;
        border-color: #999;
    }

    .accent-card:hover {
        border-color: #000000 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .text-custom-tab:hover {
        background: #f8f9fa;
    }

    button:focus,
    input:focus,
    select:focus,
    textarea:focus,
    div:focus {
        outline: none !important;
        box-shadow: none !important;
    }

    * {
        -webkit-tap-highlight-color: transparent;
    }

    :focus-visible {
        outline: none !important;
    }

    .app-type-card:focus,
    .part-btn:focus,
    .perspective-btn:focus {
        outline: none !important;
        box-shadow: none !important;
    }

    /* rotation manual input arrow hide */
    #rotationManual::-webkit-outer-spin-button,
    #rotationManual::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    #rotationManual {
        -moz-appearance: textfield;
    }

    .stroke-shape-btn {
        display: flex;
        flex-direction: column;
        /* icon upar, text neeche */
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 5px;
    }

    .stroke-shape-btn i {
        font-size: 18px;
        display: block;
    }

    .stroke-shape-btn span {
        font-size: 10px;
        display: block;
        line-height: 1;
    }
</style>

@include('admin.models.partials.mascot-select-modal')

<script>
    window.backendFonts = @json($fonts ?? []);
</script>
{{-- HELPER FUNCTIONS - page mein ek dafa add karo --}}
<script>
    function appMoveBub(sl, bubId) {
        var b = document.getElementById(bubId);
        var p = (sl.value - sl.min) / (sl.max - sl.min);
        b.style.left = 'calc(' + Math.round(p * 100) + '% + ' + (8 - p * 16) + 'px)';
        b.textContent = sl.value;
        b.style.display = 'block';
    }

    function appFillSlider(sl) {
        var p = (sl.value - sl.min) / (sl.max - sl.min);
        sl.style.background = 'linear-gradient(to right, #000 ' + Math.round(p * 100) + '%, #ddd ' + Math.round(p *
            100) + '%)';
    }

    function appHideBub(bubId) {
        var b = document.getElementById(bubId);
        if (b) b.style.display = 'none';
    }
    function setSidebarPosition() {
  const sidebar = document.getElementById('applicationsSidebar')
                  || document.querySelector('.applications-sidebar');
  if (!sidebar) return;

  const isLandscape = window.innerWidth > window.innerHeight;
  const isMobile = window.innerHeight < 600;

  if (isLandscape && isMobile) {
    sidebar.style.right = '260px';   /* tools bar width */
    sidebar.style.width = '220px';
    sidebar.style.top   = '60px';
  } else {
    /* Desktop/Portrait — default values */
    sidebar.style.right = '510px';
    sidebar.style.width = '320px';
    sidebar.style.top   = '70px';
  }
}

window.addEventListener('resize', setSidebarPosition);
window.addEventListener('orientationchange', setSidebarPosition);
document.addEventListener('DOMContentLoaded', setSidebarPosition);

</script>


