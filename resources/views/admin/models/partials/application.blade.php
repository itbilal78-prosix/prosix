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
                <div style="padding:12px;  border-radius:10px; text-align:center;">

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
                <div id="directMascotColorSection" style="flex:1; padding:12px;  border-radius:10px;">

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
               <input
    type="range"
    id="directMascotScale"
    min="1"
    max="400"
    value="100"
    step="1"
    class="app-slider"
    oninput="
        updateDirectMascotScale(this.value);
        document.getElementById('directMascotScaleValue').textContent = this.value;
        appFillSlider(this);
    "
    style="
        width:100%;
        cursor:pointer;
        background:linear-gradient(to right,#000 24.8%,#ddd 24.8%);
    "
>
            </div>

            {{-- Opacity --}}
            <div class="control-group" style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; font-size:14px; margin-bottom:8px; color:#333;">
                    Opacity: <span id="directMascotOpacityValue">100</span>%
                </label>
              <input
    type="range"
    id="directMascotOpacity"
    min="0"
    max="100"
    value="100"
    class="app-slider"
    oninput="
        updateDirectMascotOpacity(this.value);
        document.getElementById('directMascotOpacityValue').textContent = this.value;
        appFillSlider(this);
    "
    style="
        width:100%;
        cursor:pointer;
        background:linear-gradient(to right,#000 100%,#ddd 100%);
    "
>
            </div>



            {{-- Position --}}
            <div class="control-group" style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; font-size:14px; margin-bottom:8px; color:#333;">
                    Position
                </label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label style="font-size:12px; color:#666;">X: <span
                                id="mascotDirectPosXValue">0</span></label>
                       <input
    type="range"
    id="mascotDirectPosX"
    min="-500"
    max="500"
    value="0"
    class="app-slider"
    oninput="
        updateDirectMascotPosition('x', this.value);
        appFillSlider(this);
    "
    style="
        width:100%;
        cursor:pointer;
        background:linear-gradient(to right,#000 50%,#ddd 50%);
    "
>
                    </div>
                    <div>
                        <label style="font-size:12px; color:#666;">Y: <span
                                id="mascotDirectPosYValue">0</span></label>
                     <input
    type="range"
    id="mascotDirectPosY"
    min="-500"
    max="500"
    value="0"
    class="app-slider"
    oninput="
        updateDirectMascotPosition('y', this.value);
        appFillSlider(this);
    "
    style="
        width:100%;
        cursor:pointer;
        background:linear-gradient(to right,#000 50%,#ddd 50%);
    "
>
                    </div>
                </div>
            </div>


            {{-- Rotation --}}
            {{-- Rotation --}}
            <div class="control-group" style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; font-size:14px; margin-bottom:8px; color:#333;">
                    Rotation: <span id="directMascotRotationValue">0</span>°
                </label>
                <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                    <div style="position:relative; width:160px; height:160px; flex-shrink:0;">
                        <svg id="mascotRotationSvg" width="160" height="160"
                            style="position:absolute; top:0; left:0; cursor:grab;">
                            <circle cx="80" cy="80" r="68" fill="#e0e0e0" stroke="#bbb"
                                stroke-width="3" />
                            <circle id="mascotRotationArc" cx="80" cy="80" r="68" fill="none"
                                stroke="#333" stroke-width="14" stroke-linecap="butt" stroke-dasharray="0 427.26"
                                transform="rotate(-90 80 80)" style="transition:none;" />
                            <circle cx="80" cy="80" r="52" fill="#888" />
                            <rect
    id="mascotRotationDot"
    x="71"
    y="3"
    width="18"
    height="18"
    rx="2"
    ry="2"
    fill="#222"
    stroke="#fff"
    stroke-width="2.5"
/>
                        </svg>
                        <div
                            style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none;">
                            <input type="number" id="mascotRotationManual" min="0" max="360"
                                value="0"
                                style="width:56px; height:40px; text-align:center;
                           border-radius:8px; border:2px solid #ccc;
                           font-weight:700; font-size:14px;
                           background:#fff; color:#222;
                           -moz-appearance:textfield;
                           pointer-events:all;"
                                oninput="
                        var v = Math.min(360, Math.max(0, parseInt(this.value) || 0));
                        setMascotWheelAngle(v);
                        updateDirectMascotRotation(v);
                    ">
                        </div>
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
appFillSlider(this);
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
appFillSlider(this);
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
appFillSlider(this);
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
                         <rect
    id="rotationDot"
    x="71"
    y="3"
    width="18"
    height="18"
    rx="2"
    ry="2"
    fill="#222"
    stroke="#fff"
    stroke-width="2.5"
/>
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

{{-- FONT MODAL — 3-column, screenshot style --}}
<div id="fontModal" class="color-modal" style="display:none;">
    <div class="color-modal-content" style="width:1060px; max-width:96%; border-radius:0; overflow:hidden;">

        {{-- Header --}}
        <div
            style="padding:16px 28px; background:#fff; border-bottom:1px solid #e0e0e0; display:flex; align-items:center; justify-content:center; position:relative;">
            <h3 id="fontModalTitle"
                style="margin:0; font-size:14px; font-weight:700; color:#222; letter-spacing:1.5px; text-transform:uppercase;">
                SELECT FONT
            </h3>

            <span onclick="closeFontModal()"
                style="position:absolute; right:20px; top:50%; transform:translateY(-50%); cursor:pointer; font-size:24px; color:#555; width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:4px;"
                onmouseover="this.style.background='#f0f0f0'"
                onmouseout="this.style.background='transparent'">×</span>
        </div>

        {{-- Search Bar --}}
        <div style="padding:14px 20px; background:#fff; border-bottom:1px solid #ddd;">
            <input
                type="text"
                id="fontSearchInput"
                placeholder="Search font..."
                oninput="window.renderFontGrid(this.value)"
                style="width:100%; height:42px; padding:10px 14px; border:1px solid #ccc; border-radius:6px; font-size:14px; outline:none;">
        </div>

        {{-- Grid --}}
        <div id="fontGrid"
            style="padding:20px; display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; max-height:62vh; overflow-y:auto; background:#e9e9e9;">
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
.app-slider::-webkit-slider-thumb {
    -webkit-appearance: none !important;
    appearance: none !important;
    width: 18px !important;
    height: 18px !important;
    background: #000 !important;
    border: 2px solid #fff !important;
    border-radius: 3px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,.35) !important;
    cursor: pointer !important;
}

.app-slider::-moz-range-thumb {
    width: 18px !important;
    height: 18px !important;
    background: #000 !important;
    border: 2px solid #fff !important;
    border-radius: 3px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,.35) !important;
    cursor: pointer !important;
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
    /* Font grid cards - screenshot style */
    #fontGrid>div {
        border: 1.5px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        background: #fff;
        transition: border-color 0.18s, box-shadow 0.18s;
        overflow: hidden;
        padding: 0;
        /* padding remove karo */
    }



    /* Font name top bar */
    #fontGrid>div .font-card-name {
        padding: 8px 12px 6px;
        font-size: 11px;
        font-weight: 700;
        color: #555;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        background: #fff;
    }

    /* Preview area */
    /* #fontGrid > div .font-card-preview {
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9e9e9;
    font-size: 38px;
    font-weight: 700;
    border: 14px solid white;
    color: #222;
    transition: background 0.18s, color 0.18s;
} */
    /* Preview area */
    #fontGrid>div .font-card-preview {
        min-height: 120px;
        height: auto;
        /* ← fixed height hatao */
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e9e9e9;
        font-size: 35px;
        font-weight: 700;
        border: 14px solid white;
        color: #222;
        transition: background 0.18s, color 0.18s;
        padding: 10px;
        /* ← padding add karo */
        word-break: break-word;
        /* ← long words break hone dena */
        text-align: center;
        /* ← center align */
        line-height: 1.2;
        /* ← line height */
        overflow-wrap: break-word;
    }

    #fontGrid>div.font-selected .font-card-preview {
        background: #353535;
        color: #fff;
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
        const sidebar = document.getElementById('applicationsSidebar') ||
            document.querySelector('.applications-sidebar');
        if (!sidebar) return;

        const isLandscape = window.innerWidth > window.innerHeight;
        const isMobile = window.innerHeight < 600;

        if (isLandscape && isMobile) {
            sidebar.style.right = '260px'; /* tools bar width */
            sidebar.style.width = '220px';
            sidebar.style.top = '60px';
        } else {
            /* Desktop/Portrait — default values */
            sidebar.style.right = '510px';
            sidebar.style.width = '320px';
            sidebar.style.top = '70px';
        }
    }

    window.addEventListener('resize', setSidebarPosition);
    window.addEventListener('orientationchange', setSidebarPosition);
    document.addEventListener('DOMContentLoaded', setSidebarPosition);
</script>


{{-- ============================================================
     FINAL MOBILE APPLICATION FIX
     - Sidebar sirf Application tab par khulega
     - Dusre tabs par automatically band hoga
     - Right panel poora scrollable
     - Text/Mascot rotation wheel mobile touch se kaam karega
     - Sliders mobile touch friendly
     - Desktop layout unchanged
============================================================ --}}
<style>
@media screen and (max-width:1024px) {
    /* Sidebar default hidden: JS sirf Application tab par show karega */
    #applicationsSidebar.applications-sidebar {
        display: none !important;
        position: fixed !important;
        top: 58px !important;
        right: calc(30% + 7px) !important;
        bottom: 7px !important;
        left: auto !important;
        width: min(29vw, 225px) !important;
        min-width: 180px !important;
        max-width: 225px !important;
        height: auto !important;
        max-height: calc(100dvh - 65px) !important;
        flex-direction: column !important;
        overflow: hidden !important;
        border: 1px solid #cfcfcf !important;
        border-radius: 9px !important;
        background: #f1f1f1 !important;
        box-shadow: 0 10px 28px rgba(0,0,0,.24) !important;
        z-index: 10025 !important;
        box-sizing: border-box !important;
    }

    #applicationsSidebar.mobile-app-sidebar-open {
        display: flex !important;
    }

    #applicationsSidebar .sidebar-header {
        flex: 0 0 auto !important;
        min-height: 35px !important;
        padding: 6px 9px !important;
        background: #f1f1f1 !important;
        border-bottom: 1px solid #d8d8d8 !important;
    }

    #applicationsSidebar .sidebar-header h3 {
        font-size: 12px !important;
    }

    #applicationsSidebar .sidebar-close-btn {
        width: 25px !important;
        height: 25px !important;
        font-size: 18px !important;
    }

    #applicationsSidebar .sidebar-header + div {
        flex: 0 0 auto !important;
        padding: 6px 9px !important;
        font-size: 9px !important;
        line-height: 1.3 !important;
    }

    #applicationsSidebar #applicationLayersList {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        max-height: none !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
        padding: 5px 7px !important;
        -webkit-overflow-scrolling: touch !important;
        overscroll-behavior: contain !important;
        touch-action: pan-y !important;
    }

    #applicationsSidebar #applicationLayersList + div {
        position: sticky !important;
        bottom: 0 !important;
        z-index: 4 !important;
        flex: 0 0 auto !important;
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 5px !important;
        padding: 6px 7px !important;
        background: #f1f1f1 !important;
        border-top: 1px solid #d8d8d8 !important;
    }

    #applicationsSidebar #applicationLayersList + div label {
        justify-content: center !important;
        font-size: 9px !important;
        white-space: normal !important;
    }

    #applicationsSidebar #applicationLayersList + div button {
        width: 100% !important;
        justify-content: center !important;
        padding: 7px 5px !important;
        font-size: 9px !important;
    }

    /* Right application panel end tak scroll */
    .tools-bar .color-wheel-section {
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        overscroll-behavior: contain !important;
        touch-action: pan-y !important;
    }

    #applicationPage {
        display: none;
        width: 100% !important;
        min-height: 0 !important;
        height: auto !important;
        overflow: visible !important;
        padding: 7px 8px 48px !important;
        box-sizing: border-box !important;
    }

    #applicationPage.mobile-application-page-active {
        display: block !important;
    }

    #applicationLayerControls {
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
        margin-top: 3px !important;
        padding-bottom: 24px !important;
        overflow: visible !important;
    }

    #applicationLayerControls > h4 {
        margin: 0 0 7px !important;
        font-size: 14px !important;
    }

    #applicationLayerControls .control-group {
        margin-bottom: 8px !important;
    }

    #applicationLayerControls label {
        font-size: 10px !important;
    }

    #applicationLayerControls input[type="range"] {
        min-height: 24px !important;
        touch-action: pan-x !important;
    }

    .app-slider {
        height: 5px !important;
    }

    .app-slider::-webkit-slider-thumb {
        width: 18px !important;
        height: 18px !important;
    }

    /* Text + font compact */
    #applicationText,
    #selectFontBtn {
        min-width: 0 !important;
        height: 34px !important;
        padding: 5px !important;
        font-size: 10px !important;
    }

    #selectFontBtn {
        font-size: 12px !important;
    }

    #textLayerControls > div[style*="grid-template-columns:1fr 1fr"] {
        grid-template-columns: 1fr !important;
        gap: 8px !important;
    }

    /* Rotation wheel: original SVG geometry preserve, visual size compact */
    #rotationSvg,
    #mascotRotationSvg {
        touch-action: none !important;
        -webkit-user-select: none !important;
        user-select: none !important;
        cursor: grab !important;
    }

    #rotationSvg:active,
    #mascotRotationSvg:active {
        cursor: grabbing !important;
    }

    #textLayerControls div[style*="width:160px"][style*="height:160px"],
    #directMascotControls div[style*="width:160px"][style*="height:160px"] {
        width: 122px !important;
        height: 122px !important;
    }

    #rotationSvg,
    #mascotRotationSvg {
        width: 122px !important;
        height: 122px !important;
    }

    #rotationSvg + div,
    #mascotRotationSvg + div {
        width: 122px !important;
        height: 122px !important;
    }

    #rotationManual,
    #mascotRotationManual {
        width: 48px !important;
        height: 34px !important;
        font-size: 12px !important;
    }

    /* Mascot preview above colors */
    #directMascotControls > div[style*="display:flex"][style*="gap:16px"] {
        flex-direction: column !important;
        align-items: center !important;
        gap: 5px !important;
        margin-bottom: 7px !important;
    }

    #directMascotColorSection {
        width: 100% !important;
        padding: 5px !important;
        box-sizing: border-box !important;
    }

    #directMascotPreview {
        width: 76px !important;
        height: 76px !important;
    }

    .text-custom-tab {
        padding: 6px 1px !important;
        font-size: 9px !important;
        border-bottom-width: 4px !important;
    }

    #patternTabContent > div[style*="display:flex"],
    #mascotTabContent > div[style*="display:flex"] {
        flex-direction: column !important;
        align-items: center !important;
        gap: 6px !important;
    }

    #textPatternColorControls,
    #textMascotColorControls {
        width: 100% !important;
        flex: none !important;
    }

    /* Application, font and accents popups */
    #applicationModal,
    #fontModal,
    #accentsModal {
        position: fixed !important;
        inset: 5px !important;
        z-index: 100080 !important;
        padding: 5px !important;
        overflow: hidden !important;
        align-items: center !important;
        justify-content: center !important;
        box-sizing: border-box !important;
    }

    #applicationModal > .color-modal-content,
    #fontModal > .color-modal-content,
    #accentsModal > .color-modal-content {
        width: min(80vw, 610px) !important;
        max-width: 610px !important;
        height: calc(100dvh - 16px) !important;
        max-height: 300px !important;
        margin: auto !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
        border-radius: 9px !important;
    }

    #applicationModal > .color-modal-content > div:first-child,
    #accentsModal > .color-modal-content > div:first-child {
        flex: 0 0 auto !important;
        min-height: 34px !important;
        padding: 6px 10px !important;
    }

    #applicationModal h3,
    #fontModal h3,
    #accentsModal h3 {
        font-size: 11px !important;
    }

    #applicationModal > .color-modal-content > div:nth-child(2) {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        display: block !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-y !important;
    }

    #applicationModal > .color-modal-content > div:nth-child(2) > div {
        width: 100% !important;
        padding: 9px !important;
        border-right: 0 !important;
        box-sizing: border-box !important;
    }

    #applicationModal h4 {
        margin-bottom: 5px !important;
        font-size: 10px !important;
    }

    #applicationModal .application-type-grid {
        grid-template-columns: repeat(4,minmax(52px,1fr)) !important;
        gap: 5px !important;
    }

    #applicationModal .perspective-grid {
        grid-template-columns: repeat(4,minmax(45px,1fr)) !important;
        gap: 4px !important;
    }

    #applicationModal .part-grid {
        grid-template-columns: repeat(3,minmax(56px,1fr)) !important;
        gap: 5px !important;
    }

    #applicationModal .app-type-card,
    #applicationModal .perspective-btn,
    #applicationModal .part-btn {
        padding: 6px 3px !important;
        font-size: 8px !important;
    }

    #applicationModal div[style*="margin-bottom:30px"] {
        margin-bottom: 9px !important;
    }

    #applicationModal div[style*="margin-top:30px"] {
        position: sticky !important;
        bottom: 0 !important;
        z-index: 5 !important;
        margin-top: 7px !important;
        padding-top: 5px !important;
        background: #fff !important;
    }

    #applicationModal div[style*="margin-top:30px"] button {
        padding: 7px !important;
        font-size: 10px !important;
    }

    #fontModal > .color-modal-content > div:first-child,
    #fontModal > .color-modal-content > div:nth-child(2) {
        flex: 0 0 auto !important;
        padding: 6px 8px !important;
    }

    #fontSearchInput {
        height: 31px !important;
        padding: 5px 8px !important;
        font-size: 10px !important;
    }

    #fontGrid {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        max-height: none !important;
        grid-template-columns: repeat(2,minmax(0,1fr)) !important;
        gap: 5px !important;
        padding: 6px !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }

    #fontGrid > div .font-card-preview {
        min-height: 62px !important;
        border-width: 5px !important;
        padding: 4px !important;
        font-size: 19px !important;
    }

    #fontGrid > div .font-card-name {
        padding: 4px 5px !important;
        font-size: 7px !important;
    }

    #accentsModal > .color-modal-content > div:nth-child(2) {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        padding: 7px !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }

    #accentsModal > .color-modal-content > div:nth-child(2) > div {
        grid-template-columns: repeat(3,minmax(0,1fr)) !important;
        gap: 5px !important;
    }

    #accentsModal .accent-card {
        padding: 5px !important;
        border-width: 2px !important;
    }

    #accentsModal .accent-card > div:first-child {
        margin-bottom: 3px !important;
        font-size: 24px !important;
    }

    #accentsModal .accent-card > div:last-child {
        padding: 4px 1px !important;
        font-size: 7px !important;
    }
}
</style>

<script>
(function () {
    'use strict';

    const mobileQuery = window.matchMedia('(max-width:1024px)');

    function getSidebar() {
        return document.getElementById('applicationsSidebar');
    }

    function getApplicationPage() {
        return document.getElementById('applicationPage');
    }

    function isApplicationTabActive() {
        const button = document.getElementById('applicationBtn');
        return Boolean(
            button &&
            (
                button.classList.contains('active') ||
                button.getAttribute('aria-selected') === 'true'
            )
        );
    }

    function setApplicationMobileState(open) {
        if (!mobileQuery.matches) return;

        const sidebar = getSidebar();
        const page = getApplicationPage();

        if (sidebar) {
            sidebar.classList.toggle('mobile-app-sidebar-open', Boolean(open));
            sidebar.style.removeProperty('left');
            sidebar.style.removeProperty('height');
            sidebar.style.removeProperty('width');
            sidebar.style.removeProperty('right');
            sidebar.style.removeProperty('top');
        }

        if (page) {
            page.classList.toggle('mobile-application-page-active', Boolean(open));
        }

        if (open) {
            const scrollArea = document.querySelector('.tools-bar .color-wheel-section');
            if (scrollArea) scrollArea.scrollTop = 0;
        }
    }

    /*
     * Existing toggle function ko mobile par controlled banaya:
     * close button band karega; dobara Application tab click se khulega.
     */
    const oldToggle = window.toggleApplicationsSidebar;
    window.toggleApplicationsSidebar = function () {
        if (!mobileQuery.matches) {
            if (typeof oldToggle === 'function') {
                return oldToggle.apply(this, arguments);
            }
            return;
        }

        const sidebar = getSidebar();
        if (!sidebar) return;

        const shouldOpen = !sidebar.classList.contains('mobile-app-sidebar-open');
        setApplicationMobileState(shouldOpen);
    };

    function activateApplicationMobileUI() {
        if (!mobileQuery.matches) return;
        setApplicationMobileState(true);

        if (typeof window.toggleMobileToolbar === 'function') {
            window.toggleMobileToolbar(false);
        }
    }

    document.addEventListener('click', function (event) {
        const applicationButton = event.target.closest('#applicationBtn');

        if (applicationButton) {
            setTimeout(activateApplicationMobileUI, 20);
            return;
        }

        const anotherMainTab = event.target.closest(
            '#colorBtn,#patternBtn,#saveBtn,#previewBtn,#zoomBtn'
        );

        if (anotherMainTab) {
            setTimeout(function () {
                setApplicationMobileState(false);
            }, 20);
        }
    }, true);

    function normalizedAngle(value) {
        let angle = Math.round(Number(value) || 0) % 360;
        return angle < 0 ? angle + 360 : angle;
    }

    function pointerAngle(svg, clientX, clientY) {
        const rect = svg.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        const radians = Math.atan2(clientY - centerY, clientX - centerX);
        return normalizedAngle(radians * 180 / Math.PI + 90);
    }

    function bindRotationWheel(config) {
        const svg = document.getElementById(config.svgId);
        if (!svg || svg.dataset.mobileRotationBound === '1') return;

        svg.dataset.mobileRotationBound = '1';
        let dragging = false;
        let pointerId = null;

        function apply(event) {
            if (!dragging || !mobileQuery.matches) return;
            event.preventDefault();

            const angle = pointerAngle(svg, event.clientX, event.clientY);

            const input = document.getElementById(config.inputId);
            const value = document.getElementById(config.valueId);

            if (input) input.value = angle;
            if (value) value.textContent = angle;

            if (typeof window[config.drawFunction] === 'function') {
                window[config.drawFunction](angle);
            }

            if (typeof window[config.updateFunction] === 'function') {
                window[config.updateFunction](angle);
            }
        }

        svg.addEventListener('pointerdown', function (event) {
            if (!mobileQuery.matches) return;

            dragging = true;
            pointerId = event.pointerId;

            try {
                svg.setPointerCapture(pointerId);
            } catch (error) {}

            apply(event);
        }, { passive:false });

        svg.addEventListener('pointermove', apply, { passive:false });

        function stop(event) {
            dragging = false;

            try {
                if (pointerId !== null && svg.hasPointerCapture(pointerId)) {
                    svg.releasePointerCapture(pointerId);
                }
            } catch (error) {}

            pointerId = null;

            if (event && event.cancelable) event.preventDefault();
        }

        svg.addEventListener('pointerup', stop, { passive:false });
        svg.addEventListener('pointercancel', stop, { passive:false });
        svg.addEventListener('lostpointercapture', stop);
    }

    function bindAllRotationWheels() {
        bindRotationWheel({
            svgId: 'rotationSvg',
            inputId: 'rotationManual',
            valueId: 'rotationValue',
            drawFunction: 'setWheelAngle',
            updateFunction: 'updateRotation'
        });

        bindRotationWheel({
            svgId: 'mascotRotationSvg',
            inputId: 'mascotRotationManual',
            valueId: 'directMascotRotationValue',
            drawFunction: 'setMascotWheelAngle',
            updateFunction: 'updateDirectMascotRotation'
        });
    }

    function initialize() {
        if (mobileQuery.matches) {
            setApplicationMobileState(false);
        }

        bindAllRotationWheels();

        document.querySelectorAll('.app-slider').forEach(function (slider) {
            slider.style.touchAction = 'pan-x';
            if (typeof window.appFillSlider === 'function') {
                window.appFillSlider(slider);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

    window.addEventListener('resize', function () {
        if (!mobileQuery.matches) {
            const sidebar = getSidebar();
            const page = getApplicationPage();

            if (sidebar) sidebar.classList.remove('mobile-app-sidebar-open');
            if (page) page.classList.remove('mobile-application-page-active');
        }

        bindAllRotationWheels();
    });

    /*
     * Dynamic application layer select hone ke baad controls dubara bind/sync.
     */
    document.addEventListener('click', function (event) {
        if (
            event.target.closest('#applicationLayersList') ||
            event.target.closest('[onclick*="selectApplicationLayer"]')
        ) {
            setTimeout(function () {
                bindAllRotationWheels();

                document.querySelectorAll('.app-slider').forEach(function (slider) {
                    if (typeof window.appFillSlider === 'function') {
                        window.appFillSlider(slider);
                    }
                });
            }, 80);
        }
    });
})();
</script>


{{-- ============================================================
     MOBILE-ONLY FINAL FIX
     Applies only when viewport is 1024px or less.
     Desktop/web over 1024px remains unchanged.
============================================================ --}}
<style>
@media screen and (max-width:1024px) {

    /* =========================================================
       1) APPLICATIONS SIDEBAR — narrower and closeable
    ========================================================== */
    #applicationsSidebar.applications-sidebar {
        width: 160px !important;
        min-width: 160px !important;
        max-width: 160px !important;
        right: calc(30% + 4px) !important;
        top: 58px !important;
        bottom: 7px !important;
        height: auto !important;
        max-height: calc(100dvh - 65px) !important;
        overflow: hidden !important;
        border-radius: 8px !important;
        box-sizing: border-box !important;
    }

    #applicationsSidebar .sidebar-header {
        min-height: 31px !important;
        padding: 4px 6px !important;
    }

    #applicationsSidebar .sidebar-header h3 {
        font-size: 9px !important;
    }

    #applicationsSidebar .sidebar-close-btn {
        position: relative !important;
        z-index: 999 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 26px !important;
        height: 26px !important;
        min-width: 26px !important;
        padding: 0 !important;
        margin: 0 !important;
        border: 0 !important;
        background: transparent !important;
        color: #555 !important;
        font-size: 19px !important;
        line-height: 1 !important;
        cursor: pointer !important;
        pointer-events: auto !important;
        touch-action: manipulation !important;
    }

    #applicationsSidebar .sidebar-header + div {
        padding: 4px 6px !important;
        font-size: 7px !important;
        line-height: 1.25 !important;
    }

    #applicationsSidebar #applicationLayersList {
        min-height: 0 !important;
        padding: 4px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        -webkit-overflow-scrolling: touch !important;
    }

    #applicationsSidebar #applicationLayersList + div {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 4px !important;
        padding: 4px !important;
    }

    #applicationsSidebar #applicationLayersList + div label {
        justify-content: center !important;
        font-size: 7px !important;
        white-space: normal !important;
        text-align: center !important;
    }

    #applicationsSidebar #applicationLayersList + div button {
        width: 100% !important;
        min-width: 0 !important;
        padding: 6px 2px !important;
        font-size: 7px !important;
        justify-content: center !important;
        box-sizing: border-box !important;
    }

    #applicationLayersList > * {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
    }

    /* =========================================================
       2) ADD APPLICATION MODAL — less width + internal scrolling
    ========================================================== */
    #applicationModal {
        position: fixed !important;
        inset: 4px !important;
        z-index: 100300 !important;
        padding: 4px !important;
        align-items: center !important;
        justify-content: center !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }

    #applicationModal > .color-modal-content {
        width: min(58vw, 430px) !important;
        min-width: 330px !important;
        max-width: 430px !important;
        height: min(78dvh, 255px) !important;
        max-height: calc(100dvh - 12px) !important;
        margin: auto !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
        border-radius: 8px !important;
        box-sizing: border-box !important;
    }

    #applicationModal > .color-modal-content > div:first-child {
        flex: 0 0 auto !important;
        min-height: 31px !important;
        padding: 5px 8px !important;
    }

    #applicationModal > .color-modal-content > div:first-child h3 {
        font-size: 9px !important;
    }

    #applicationModal > .color-modal-content > div:first-child span {
        width: 27px !important;
        height: 27px !important;
        font-size: 18px !important;
        pointer-events: auto !important;
        touch-action: manipulation !important;
    }

    #applicationModal > .color-modal-content > div:nth-child(2) {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        display: block !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        overscroll-behavior: contain !important;
        touch-action: pan-y !important;
    }

    #applicationModal > .color-modal-content > div:nth-child(2) > div {
        width: 100% !important;
        padding: 7px !important;
        border-right: 0 !important;
        box-sizing: border-box !important;
    }

    #applicationModal h4 {
        margin: 0 0 4px !important;
        font-size: 8px !important;
        line-height: 1.25 !important;
    }

    #applicationModal div[style*="margin-bottom:30px"] {
        margin-bottom: 7px !important;
    }

    #applicationModal .application-type-grid {
        grid-template-columns: repeat(4, minmax(48px,1fr)) !important;
        gap: 4px !important;
    }

    #applicationModal .perspective-grid {
        grid-template-columns: repeat(4, minmax(42px,1fr)) !important;
        gap: 4px !important;
    }

    #applicationModal .part-grid {
        grid-template-columns: repeat(3, minmax(48px,1fr)) !important;
        gap: 4px !important;
    }

    #applicationModal .app-type-card,
    #applicationModal .perspective-btn,
    #applicationModal .part-btn {
        min-width: 0 !important;
        padding: 5px 2px !important;
        font-size: 7px !important;
    }

    #applicationModal div[style*="margin-top:30px"] {
        position: sticky !important;
        bottom: 0 !important;
        z-index: 8 !important;
        margin-top: 6px !important;
        padding-top: 4px !important;
        background: #fff !important;
    }

    #applicationModal div[style*="margin-top:30px"] button {
        padding: 6px !important;
        font-size: 8px !important;
    }

    /* =========================================================
       3) FONT MODAL — force real scroll
    ========================================================== */
    #fontModal {
        position: fixed !important;
        inset: 4px !important;
        z-index: 100400 !important;
        padding: 4px !important;
        align-items: center !important;
        justify-content: center !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }

    #fontModal > .color-modal-content {
        width: min(62vw, 470px) !important;
        min-width: 350px !important;
        max-width: 470px !important;
        height: min(68dvh, 220px) !important;
        max-height: calc(100dvh - 12px) !important;
        margin: auto !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
        border-radius: 8px !important;
        box-sizing: border-box !important;
    }

    #fontModal > .color-modal-content > div:first-child {
        flex: 0 0 auto !important;
        min-height: 29px !important;
        padding: 4px 7px !important;
    }

    #fontModalTitle {
        font-size: 8px !important;
    }

    #fontModal > .color-modal-content > div:first-child span {
        right: 5px !important;
        width: 25px !important;
        height: 25px !important;
        font-size: 17px !important;
        z-index: 20 !important;
        pointer-events: auto !important;
        touch-action: manipulation !important;
    }

    #fontModal > .color-modal-content > div:nth-child(2) {
        flex: 0 0 auto !important;
        padding: 4px 5px !important;
    }

    #fontSearchInput {
        width: 100% !important;
        height: 26px !important;
        padding: 3px 5px !important;
        font-size: 8px !important;
        box-sizing: border-box !important;
    }

    #fontGrid {
        position: relative !important;
        flex: 1 1 0 !important;
        min-height: 0 !important;
        height: 0 !important;
        max-height: none !important;
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0,1fr)) !important;
        grid-auto-rows: max-content !important;
        align-content: start !important;
        gap: 4px !important;
        padding: 4px !important;
        overflow-x: hidden !important;
        overflow-y: scroll !important;
        -webkit-overflow-scrolling: touch !important;
        overscroll-behavior: contain !important;
        touch-action: pan-y !important;
        scrollbar-width: thin !important;
    }

    #fontGrid::-webkit-scrollbar {
        width: 5px !important;
    }

    #fontGrid::-webkit-scrollbar-thumb {
        background: #777 !important;
        border-radius: 10px !important;
    }

    #fontGrid > div {
        min-width: 0 !important;
        flex: none !important;
    }

    #fontGrid > div .font-card-preview {
        min-height: 48px !important;
        height: auto !important;
        border-width: 3px !important;
        padding: 3px !important;
        font-size: 15px !important;
    }

    #fontGrid > div .font-card-name {
        padding: 3px !important;
        font-size: 6px !important;
    }
}
</style>

<script>
(function () {
    'use strict';

    const isMobileViewport = function () {
        return window.matchMedia('(max-width:1024px)').matches;
    };

    let applicationSidebarClosedByUser = false;

    function getApplicationSidebar() {
        return document.getElementById('applicationsSidebar');
    }

    function forceCloseApplicationSidebar() {
        const sidebar = getApplicationSidebar();
        if (!sidebar) return;

        applicationSidebarClosedByUser = true;

        sidebar.classList.remove(
            'mobile-app-sidebar-open',
            'active',
            'open',
            'show'
        );

        sidebar.style.setProperty('display', 'none', 'important');
        sidebar.style.setProperty('visibility', 'hidden', 'important');
        sidebar.style.setProperty('pointer-events', 'none', 'important');
    }

    function forceOpenApplicationSidebar() {
        const sidebar = getApplicationSidebar();
        if (!sidebar) return;

        applicationSidebarClosedByUser = false;

        sidebar.style.removeProperty('visibility');
        sidebar.style.removeProperty('pointer-events');
        sidebar.style.setProperty('display', 'flex', 'important');
        sidebar.classList.add('mobile-app-sidebar-open');
    }

    function bindSidebarCloseButton() {
        const sidebar = getApplicationSidebar();
        if (!sidebar || sidebar.dataset.absoluteCloseBound === '1') return;

        const closeButton = sidebar.querySelector('.sidebar-close-btn');
        if (!closeButton) return;

        sidebar.dataset.absoluteCloseBound = '1';

        function closeNow(event) {
            if (!isMobileViewport()) return;

            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            forceCloseApplicationSidebar();
        }

        closeButton.onclick = null;
        closeButton.removeAttribute('onclick');

        closeButton.addEventListener('pointerdown', closeNow, true);
        closeButton.addEventListener('touchstart', closeNow, {
            capture: true,
            passive: false
        });
        closeButton.addEventListener('click', closeNow, true);
    }

    /*
     * Application tab par click karne se sidebar dobara khul sakta hai.
     * Close karne ke baad kisi internal event ko usi waqt reopen nahi karne dena.
     */
    document.addEventListener('click', function (event) {
        if (!isMobileViewport()) return;

        if (event.target.closest('#applicationBtn')) {
            setTimeout(forceOpenApplicationSidebar, 30);
        }

        if (
            event.target.closest(
                '#colorBtn,#patternBtn,#saveBtn,#previewBtn,#zoomBtn'
            )
        ) {
            setTimeout(forceCloseApplicationSidebar, 20);
        }
    }, true);

    /*
     * Purani scripts display:flex dobara laga dein to closed flag enforce kare.
     */
    const sidebarObserver = new MutationObserver(function () {
        if (
            isMobileViewport() &&
            applicationSidebarClosedByUser
        ) {
            const sidebar = getApplicationSidebar();

            if (
                sidebar &&
                getComputedStyle(sidebar).display !== 'none'
            ) {
                sidebar.style.setProperty(
                    'display',
                    'none',
                    'important'
                );
                sidebar.style.setProperty(
                    'visibility',
                    'hidden',
                    'important'
                );
                sidebar.style.setProperty(
                    'pointer-events',
                    'none',
                    'important'
                );
            }
        }
    });

    function startSidebarObserver() {
        const sidebar = getApplicationSidebar();
        if (!sidebar || sidebar.dataset.observerStarted === '1') return;

        sidebar.dataset.observerStarted = '1';
        sidebarObserver.observe(sidebar, {
            attributes: true,
            attributeFilter: ['style', 'class']
        });
    }

    function prepareScrollableModal(modalId, scrollSelector) {
        if (!isMobileViewport()) return;

        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.style.setProperty('display', 'flex', 'important');

        const scrollArea = modal.querySelector(scrollSelector);
        if (!scrollArea) return;

        scrollArea.scrollTop = 0;
        scrollArea.style.setProperty(
            'overflow-y',
            'scroll',
            'important'
        );
        scrollArea.style.setProperty(
            'touch-action',
            'pan-y',
            'important'
        );
        scrollArea.style.setProperty(
            '-webkit-overflow-scrolling',
            'touch'
        );
    }

    function bindFontGridWheel() {
        const grid = document.getElementById('fontGrid');
        if (!grid || grid.dataset.forceWheelBound === '1') return;

        grid.dataset.forceWheelBound = '1';

        grid.addEventListener('wheel', function (event) {
            if (!isMobileViewport()) return;

            const before = grid.scrollTop;
            grid.scrollTop += event.deltaY;

            if (grid.scrollTop !== before) {
                event.preventDefault();
                event.stopPropagation();
            }
        }, { passive: false });

        grid.addEventListener('touchmove', function (event) {
            if (!isMobileViewport()) return;
            event.stopPropagation();
        }, { passive: true });
    }

    function bindApplicationModalClose() {
        const modal = document.getElementById('applicationModal');
        if (!modal || modal.dataset.mobileCloseBound === '1') return;

        const close = modal.querySelector(
            '[onclick*="closeApplicationModal"]'
        );

        if (!close) return;

        modal.dataset.mobileCloseBound = '1';

        close.addEventListener('pointerdown', function (event) {
            if (!isMobileViewport()) return;

            event.preventDefault();
            event.stopPropagation();

            if (typeof window.closeApplicationModal === 'function') {
                window.closeApplicationModal();
            } else {
                modal.style.display = 'none';
            }
        }, true);
    }

    function initialize() {
        bindSidebarCloseButton();
        startSidebarObserver();
        bindFontGridWheel();
        bindApplicationModalClose();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

    document.addEventListener('click', function (event) {
        if (event.target.closest('#selectFontBtn')) {
            setTimeout(function () {
                prepareScrollableModal(
                    'fontModal',
                    '#fontGrid'
                );
                bindFontGridWheel();
            }, 60);

            setTimeout(function () {
                prepareScrollableModal(
                    'fontModal',
                    '#fontGrid'
                );
            }, 180);
        }

        if (
            event.target.closest(
                '[onclick*="openApplicationModal"]'
            )
        ) {
            setTimeout(function () {
                prepareScrollableModal(
                    'applicationModal',
                    '.color-modal-content > div:nth-child(2)'
                );
            }, 60);
        }
    });

    window.addEventListener('resize', initialize);
    window.addEventListener('orientationchange', function () {
        setTimeout(initialize, 120);
    });
})();
</script>


{{-- ============================================================
     FINAL MOBILE CLEAN APPLICATION LAYOUT
     ONLY mobile/tablet viewport <= 1024px
     Desktop/web remains unchanged.
============================================================ --}}
<style>
@media screen and (max-width:1024px) {

    /* =========================================================
       APPLICATION LAYER CARD
       All icons including delete/cross remain visible.
    ========================================================== */
    #applicationLayersList {
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }

    #applicationLayersList > * {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
    }

    #applicationLayersList .application-layer-item,
    #applicationLayersList .layer-item,
    #applicationLayersList [data-layer-id] {
        display: grid !important;
        grid-template-columns:
            minmax(27px, auto)
            minmax(25px, 1fr)
            25px
            25px
            25px
            25px !important;
        align-items: center !important;
        gap: 2px !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        padding: 5px 4px !important;
        box-sizing: border-box !important;
        overflow: visible !important;
    }

    #applicationLayersList .application-layer-item > *,
    #applicationLayersList .layer-item > *,
    #applicationLayersList [data-layer-id] > * {
        min-width: 0 !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding-left: 2px !important;
        padding-right: 2px !important;
        box-sizing: border-box !important;
    }

    #applicationLayersList button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 23px !important;
        width: 23px !important;
        height: 23px !important;
        padding: 0 !important;
        font-size: 9px !important;
        line-height: 1 !important;
        overflow: visible !important;
        flex-shrink: 0 !important;
    }

    /* Last button is normally delete/cross */
    #applicationLayersList .application-layer-item > button:last-child,
    #applicationLayersList .layer-item > button:last-child,
    #applicationLayersList [data-layer-id] > button:last-child {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    #applicationLayersList .application-layer-item span,
    #applicationLayersList .layer-item span,
    #applicationLayersList [data-layer-id] span {
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        font-size: 8px !important;
    }

    /* =========================================================
       RIGHT PANEL — COMPACT SLIDERS
    ========================================================== */
    #applicationPage input[type="range"],
    #applicationLayerControls input[type="range"],
    #textLayerControls input[type="range"],
    #directMascotControls input[type="range"] {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        height: 15px !important;
        min-height: 15px !important;
        margin: 0 !important;
        padding: 0 !important;
        background-clip: content-box !important;
        box-sizing: border-box !important;
        touch-action: pan-x !important;
    }

    .app-slider {
        height: 3px !important;
        border-radius: 2px !important;
    }

    .app-slider::-webkit-slider-runnable-track {
        height: 3px !important;
        border-radius: 2px !important;
    }

    .app-slider::-moz-range-track {
        height: 3px !important;
        border-radius: 2px !important;
    }

    .app-slider::-webkit-slider-thumb {
        width: 13px !important;
        height: 13px !important;
        margin-top: -5px !important;
        border: 1px solid #fff !important;
        border-radius: 3px !important;
    }

    .app-slider::-moz-range-thumb {
        width: 13px !important;
        height: 13px !important;
        border: 1px solid #fff !important;
        border-radius: 3px !important;
    }

    /* Reduce empty vertical spacing around Size/Position sliders */
    #textLayerControls .control-group > div[style*="gap:30%"] > div {
        padding-top: 13px !important;
    }

    #fontSizeBubble,
    #posXBubble,
    #posYBubble {
        padding: 0 4px !important;
        font-size: 8px !important;
    }

    /* =========================================================
       ROTATION WHEEL — SMALL, COMPLETE, TOUCH FRIENDLY
    ========================================================== */
    #textLayerControls div[style*="width:160px"][style*="height:160px"],
    #directMascotControls div[style*="width:160px"][style*="height:160px"] {
        position: relative !important;
        width: 86px !important;
        height: 86px !important;
        min-width: 86px !important;
        min-height: 86px !important;
        margin: 0 auto !important;
        overflow: visible !important;
    }

    #rotationSvg,
    #mascotRotationSvg {
        display: block !important;
        width: 86px !important;
        height: 86px !important;
        overflow: visible !important;
        touch-action: none !important;
        -webkit-user-select: none !important;
        user-select: none !important;
        cursor: grab !important;
    }

    #rotationSvg + div,
    #mascotRotationSvg + div {
        position: absolute !important;
        inset: 0 !important;
        width: 86px !important;
        height: 86px !important;
    }

    #rotationManual,
    #mascotRotationManual {
        width: 37px !important;
        height: 27px !important;
        padding: 1px !important;
        border-width: 1px !important;
        border-radius: 6px !important;
        font-size: 9px !important;
    }

    #rotationValue,
    #directMascotRotationValue {
        font-size: 9px !important;
    }

    /* =========================================================
       COLORS / PATTERN / MASCOT
       Tabs first, content cleanly BELOW tabs.
    ========================================================== */
    #textLayerControls > div[style*="margin-top:25px"] {
        display: flex !important;
        flex-direction: column !important;
        width: 100% !important;
        margin-top: 10px !important;
        margin-bottom: 8px !important;
        overflow: visible !important;
    }

    #textLayerControls > div[style*="margin-top:25px"] >
    div[style*="display:flex"][style*="border-bottom"] {
        order: 1 !important;
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0,1fr)) !important;
        width: 100% !important;
        gap: 5px !important;
        margin-bottom: 8px !important;
        padding: 0 !important;
        border-bottom: 1px solid #ddd !important;
        box-sizing: border-box !important;
    }

    .text-custom-tab {
        width: 100% !important;
        min-width: 0 !important;
        height: 29px !important;
        padding: 5px 2px !important;
        border-bottom-width: 3px !important;
        font-size: 8px !important;
        white-space: nowrap !important;
        box-sizing: border-box !important;
    }

    #colorsTabContent {
        order: 2 !important;
        display: block;
        width: 100% !important;
        min-width: 0 !important;
        padding: 2px 0 8px !important;
        box-sizing: border-box !important;
    }

    #patternTabContent,
    #mascotTabContent {
        order: 2 !important;
        width: 100% !important;
        min-width: 0 !important;
        padding: 4px 0 8px !important;
        box-sizing: border-box !important;
    }

    #colorsTabContent > div {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        width: 100% !important;
        gap: 7px !important;
    }

    #currentOutlineDisplay {
        width: 100% !important;
        margin: 0 0 5px !important;
        text-align: center !important;
    }

    #outlineStylePreview {
        font-size: 39px !important;
        margin-bottom: 3px !important;
    }

    #outlineStyleName {
        font-size: 9px !important;
    }

    #outlineColorsSection {
        width: 100% !important;
        min-width: 0 !important;
    }

    #outlineColorsSection .control-group {
        display: grid !important;
        grid-template-columns: 49px minmax(0,1fr) auto !important;
        width: 100% !important;
        gap: 4px !important;
        margin-bottom: 7px !important;
        align-items: center !important;
        box-sizing: border-box !important;
    }

    #outlineColorsSection .control-group > label {
        width: auto !important;
        min-width: 0 !important;
        font-size: 8px !important;
    }

    #baseColorPicker,
    #outline1ColorPicker,
    #outline2ColorPicker,
    #shadowColorPicker {
        display: flex !important;
        flex-wrap: wrap !important;
        width: 100% !important;
        min-width: 0 !important;
        gap: 4px !important;
        overflow: visible !important;
    }

    #outlineColorsSection input[type="number"] {
        width: 38px !important;
        height: 25px !important;
        padding: 2px !important;
        font-size: 8px !important;
    }

    #cornersSection {
        grid-template-columns: 49px repeat(3, minmax(34px,1fr)) !important;
    }

    #cornersSection .stroke-shape-btn {
        width: 100% !important;
        min-width: 0 !important;
        height: 40px !important;
        padding: 3px 1px !important;
    }

    #cornersSection .stroke-shape-btn i {
        font-size: 12px !important;
    }

    #cornersSection .stroke-shape-btn span {
        font-size: 7px !important;
    }

    /* Pattern and Mascot thumbnail on top; controls below */
    #patternTabContent > div[style*="display:flex"],
    #mascotTabContent > div[style*="display:flex"] {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        width: 100% !important;
        gap: 6px !important;
        margin-bottom: 8px !important;
    }

    #textPatternColorControls,
    #textMascotColorControls,
    #textPatternSizeOpacityControls,
    #textMascotSizeOpacityControls {
        width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
    }
}
</style>

<script>
(function () {
    'use strict';

    function isMobileApplicationView() {
        return window.matchMedia('(max-width:1024px)').matches;
    }

    function normalizeAngle(value) {
        let angle = Math.round(Number(value) || 0) % 360;
        return angle < 0 ? angle + 360 : angle;
    }

    function getPointerAngle(svg, clientX, clientY) {
        const rect = svg.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        const radians = Math.atan2(
            clientY - centerY,
            clientX - centerX
        );

        return normalizeAngle(
            radians * 180 / Math.PI + 90
        );
    }

    function bindCompactRotationWheel(config) {
        const svg = document.getElementById(config.svgId);

        if (
            !svg ||
            svg.dataset.compactMobileWheelBound === '1'
        ) {
            return;
        }

        svg.dataset.compactMobileWheelBound = '1';
        svg.setAttribute('viewBox', '0 0 160 160');
        svg.setAttribute(
            'preserveAspectRatio',
            'xMidYMid meet'
        );

        let dragging = false;
        let activePointerId = null;

        function applyAngle(event) {
            if (!dragging || !isMobileApplicationView()) {
                return;
            }

            event.preventDefault();

            const angle = getPointerAngle(
                svg,
                event.clientX,
                event.clientY
            );

            const input = document.getElementById(
                config.inputId
            );

            const output = document.getElementById(
                config.outputId
            );

            if (input) {
                input.value = angle;
            }

            if (output) {
                output.textContent = angle;
            }

            if (
                typeof window[config.drawFunction] ===
                'function'
            ) {
                window[config.drawFunction](angle);
            }

            if (
                typeof window[config.updateFunction] ===
                'function'
            ) {
                window[config.updateFunction](angle);
            }
        }

        svg.addEventListener(
            'pointerdown',
            function (event) {
                if (!isMobileApplicationView()) return;

                dragging = true;
                activePointerId = event.pointerId;

                try {
                    svg.setPointerCapture(
                        activePointerId
                    );
                } catch (error) {}

                applyAngle(event);
            },
            { passive: false }
        );

        svg.addEventListener(
            'pointermove',
            applyAngle,
            { passive: false }
        );

        function stopDragging(event) {
            if (!dragging) return;

            dragging = false;

            try {
                if (
                    activePointerId !== null &&
                    svg.hasPointerCapture(
                        activePointerId
                    )
                ) {
                    svg.releasePointerCapture(
                        activePointerId
                    );
                }
            } catch (error) {}

            activePointerId = null;

            if (event && event.cancelable) {
                event.preventDefault();
            }
        }

        svg.addEventListener(
            'pointerup',
            stopDragging,
            { passive: false }
        );

        svg.addEventListener(
            'pointercancel',
            stopDragging,
            { passive: false }
        );

        svg.addEventListener(
            'lostpointercapture',
            stopDragging
        );
    }

    function bindApplicationRotationWheels() {
        bindCompactRotationWheel({
            svgId: 'rotationSvg',
            inputId: 'rotationManual',
            outputId: 'rotationValue',
            drawFunction: 'setWheelAngle',
            updateFunction: 'updateRotation'
        });

        bindCompactRotationWheel({
            svgId: 'mascotRotationSvg',
            inputId: 'mascotRotationManual',
            outputId: 'directMascotRotationValue',
            drawFunction: 'setMascotWheelAngle',
            updateFunction: 'updateDirectMascotRotation'
        });
    }

    function fixLayerCardDeleteButtons() {
        if (!isMobileApplicationView()) return;

        const list = document.getElementById(
            'applicationLayersList'
        );

        if (!list) return;

        list.querySelectorAll(
            'button, [role="button"]'
        ).forEach(function (button) {
            const text = (
                button.textContent || ''
            ).trim().toLowerCase();

            const isDelete =
                text === '×' ||
                text === 'x' ||
                button.querySelector(
                    '.fa-times, .fa-xmark, .fa-trash'
                );

            if (isDelete) {
                button.style.setProperty(
                    'display',
                    'inline-flex',
                    'important'
                );

                button.style.setProperty(
                    'visibility',
                    'visible',
                    'important'
                );

                button.style.setProperty(
                    'opacity',
                    '1',
                    'important'
                );
            }
        });
    }

    function initializeMobileApplicationCleanFix() {
        bindApplicationRotationWheels();
        fixLayerCardDeleteButtons();

        document.querySelectorAll(
            '.app-slider'
        ).forEach(function (slider) {
            slider.style.touchAction = 'pan-x';

            if (
                typeof window.appFillSlider ===
                'function'
            ) {
                window.appFillSlider(slider);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initializeMobileApplicationCleanFix
        );
    } else {
        initializeMobileApplicationCleanFix();
    }

    const list = document.getElementById(
        'applicationLayersList'
    );

    if (list) {
        new MutationObserver(function () {
            fixLayerCardDeleteButtons();
        }).observe(list, {
            childList: true,
            subtree: true
        });
    }

    document.addEventListener(
        'click',
        function (event) {
            if (
                event.target.closest(
                    '#applicationLayersList'
                ) ||
                event.target.closest(
                    '[onclick*="selectApplicationLayer"]'
                ) ||
                event.target.closest(
                    '#tabColors,#tabPattern,#tabMascot'
                )
            ) {
                setTimeout(
                    initializeMobileApplicationCleanFix,
                    60
                );
            }
        }
    );

    window.addEventListener(
        'resize',
        initializeMobileApplicationCleanFix
    );

    window.addEventListener(
        'orientationchange',
        function () {
            setTimeout(
                initializeMobileApplicationCleanFix,
                120
            );
        }
    );
})();
</script>

