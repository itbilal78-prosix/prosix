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

    {{-- Layers List --}}
    <div id="applicationLayersList" class="sidebar-layers-list">
    </div>

    <div style="padding:15px; border-bottom:1px solid #e0e0e0;">
        <button onclick="openApplicationModal()" class="add-new-layer-btn"
            style="width:100%; padding:14px; background:#000000; color:white; border:none; border-radius:6px; font-weight:600; cursor:pointer; font-size:14px; display:flex; align-items:center; justify-content:center; gap:8px; transition:all 0.3s;">
            <i class="fas fa-plus-circle"></i>
            Add New Free Form Application
        </button>
    </div>

    {{-- Show Location Markers Toggle --}}
    <div style="padding:15px; border-top:1px solid #e0e0e0; margin-top:auto;">
        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:13px; color:#666;">
            <input type="checkbox" id="showLocationMarkers" onchange="toggleLocationMarkers(this.checked)"
                style="width:16px; height:16px; cursor:pointer;">
            <i class="fas fa-map-marker-alt"></i>
            Show Location Markers
        </label>
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
            <div style="margin-bottom:20px; padding:16px; background:#f5f5f5; border-radius:10px; text-align:center;">
                <div style="font-size:11px; font-weight:700; color:#999; letter-spacing:1px; margin-bottom:10px;">MASCOT
                    PREVIEW</div>
                <div id="directMascotPreview"
                    style="width:100px; height:100px; margin:0 auto 12px; background:#e8e8e8; border-radius:8px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                    <span style="color:#aaa; font-size:11px;">No mascot</span>
                </div>
                <button onclick="changeDirectMascot()"
                    style="width:100%; padding:10px; background:#1a1a1a; color:#fff; border:none; border-radius:6px; font-weight:700; font-size:12px; cursor:pointer; letter-spacing:.5px;">
                    Change Mascot
                </button>
                {{-- Mascot Colors --}}
                <div id="directMascotColorSection"
                    style="margin-bottom:20px; padding:16px; background:#f5f5f5; border-radius:10px;">
                    <div style="font-size:11px; font-weight:700; color:#999; letter-spacing:1px; margin-bottom:10px;">
                        MASCOT COLORS</div>
                    <div id="directMascotColorSwatches" style="display:flex; flex-direction:column; gap:10px;">
                        <p style="font-size:12px; color:#aaa; text-align:center;">Loading colors...</p>
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
                            oninput="updateDirectMascotPosition('x', this.value)" class="app-slider" style="width:100%; cursor:pointer;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:#666;">Y: <span id="mascotDirectPosYValue">0</span></label>
                        <input type="range" id="mascotDirectPosY" min="-500" max="500" value="0"
                            oninput="updateDirectMascotPosition('y', this.value)" class="app-slider" style="width:100%; cursor:pointer;">
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

            {{-- ===== ROW 1: Text label + Select Font button ===== --}}
           <div class="control-group" style="margin-bottom:15px;">

    <label style="font-weight:600; font-size:14px; color:#333; margin-bottom:6px;">
        Text
    </label>

    <div style="display:flex; gap:30px;">

        <input type="text"
               id="applicationText"
               placeholder="Enter text..."
               oninput="updateApplicationText(this.value)"
               style="flex:1; padding:10px;
                      border:2px solid #ddd;
                      border-radius:6px;
                      font-size:14px;">

        <button onclick="openFontModal()"
                style="padding:10px 14px;
                       background:#fff;
                       color:#000;
                       border:2px solid #000;
                       border-radius:6px;
                       font-weight:600;
                       font-size:12px;
                       cursor:pointer;">
            Select Font
        </button>
        

    </div>

</div>

            {{-- ===== ROW 2: Font Size + Letter Spacing side by side ===== --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:15px;">
                <div class="control-group">
                    <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px; color:#333;">
                        Font Size: <span id="fontSizeValue">100</span>
                    </label>
                    <input type="range" id="fontSize" min="50" max="5500" value="100"
                        oninput="updateFontSize(this.value)" class="app-slider" style="width:100%; cursor:pointer;">
                </div>
                <div class="control-group">
                    <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px; color:#333;">
                        Letter Spacing: <span id="letterSpacingValue">0</span>px
                    </label>
                    <input type="number" value="0" oninput="updateLetterSpacing(this.value); document.getElementById('letterSpacingValue').textContent=this.value;"
                        style="width:30%; padding:6px 8px; border:2px solid #ddd; border-radius:6px; font-size:13px; box-sizing:border-box;">
                </div>
            </div>

            {{-- ===== ROW 3: Position Controls ===== --}}
            <div class="control-group" style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; font-size:14px; margin-bottom:8px; color:#333;">
                    Position
                </label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label style="font-size:12px; color:#666;">X: <span id="posXValue">0</span></label>
                        <input type="range" id="posX" min="-200" max="200" value="0"
                            oninput="updatePosition(this.value, null)" class="app-slider" style="width:100%; cursor:pointer;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:#666;">Y: <span id="posYValue">0</span></label>
                        <input type="range" id="posY" min="-200" max="200" value="0"
                            oninput="updatePosition(null, this.value)" class="app-slider" style="width:100%; cursor:pointer;">
                    </div>
                </div>
            </div>

            {{-- ===== ROW 4: Width + Height (left) | Rotation (right) ===== --}}
            <div class="control-group" style="margin-bottom:15px;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; align-items:start;">

                    {{-- Left: Width + Height --}}
                    <div>
                        <div style="margin-bottom:10px;">
                            <label style="display:block; font-weight:600; font-size:12px; margin-bottom:5px; color:#333;">
                                Width %
                            </label>
                            <input type="range" min="10" max="300" value="100"
                                oninput="updateTextScale('x',this.value); this.nextElementSibling.textContent=this.value+'%';"
                                class="app-slider app-slider-small" style="width:100%; cursor:pointer;">
                            <span style="font-size:11px; color:#888;">100%</span>
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; font-size:12px; margin-bottom:5px; color:#333;">
                                Height %
                            </label>
                            <input type="range" min="10" max="300" value="100"
                                oninput="updateTextScale('y',this.value); this.nextElementSibling.textContent=this.value+'%';"
                                class="app-slider app-slider-small" style="width:100%; cursor:pointer;">
                            <span style="font-size:11px; color:#888;">100%</span>
                        </div>
                    </div>






                    {{-- Right: Rotation --}}
{{-- Right: Rotation --}}
<div style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:6px;">
    <label style="font-weight:600; font-size:12px; color:#333; text-align:center;">Rotation</label>

    <input type="hidden" id="rotation" value="0">

    {{-- Wheel --}}
    <div id="rotationWheel"
         style="position:relative; width:100px; height:100px; border-radius:50%;
                border:2.5px solid #bbb; background:#ececec; cursor:grab; flex-shrink:0;">

        {{-- Dot — wheel ke EDGE par ghoomega --}}
        <div id="rotationDot"
             style="position:absolute; width:13px; height:13px; border-radius:50%;
                    background:#222; top:-6px; left:50%;
                    transform:translateX(-50%);
                    box-shadow:0 1px 4px rgba(0,0,0,0.3);
                    pointer-events:none;">
        </div>

        {{-- Center: rounded square input box --}}
        <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center;">
            <input type="number" id="rotationManual" min="0" max="360" value="0"
                   style="width:54px; height:40px; text-align:center;
                          border-radius:8px; border:2px solid #ccc;
                          font-weight:700; font-size:13px;
                          background:#fff; color:#222;
                          -moz-appearance:textfield;"
                   oninput="
                       var v=Math.min(360,Math.max(0,parseInt(this.value)||0));
                       setWheelAngle(v);
                       updateRotation(v);
                   ">
        </div>
    </div>

    <span style="font-size:12px; color:#666; font-weight:600;">
        <span id="rotationValue">0</span>°
    </span>
</div>









                </div>
            </div>

            {{-- ========== COLORS/PATTERN/MASCOT TABS ========== --}}
            <div style="margin-top:25px; margin-bottom:15px;">

                {{-- Tab Headers --}}
                <div style="display:flex; gap:15px; border-bottom:2px solid #e0e0e0; margin-bottom:20px;">
                    <button onclick="switchTextCustomizationTab('colors')" id="tabColors" class="text-custom-tab"
                        style="flex:1; padding:12px; background:#fff; border:none; border-bottom:3px solid #000; font-weight:600; cursor:pointer; transition:all 0.3s; color:#333;">
                        Colors
                    </button>
                    <button onclick="switchTextCustomizationTab('pattern')" id="tabPattern" class="text-custom-tab"
                        style="flex:1; padding:12px; background:#fff; border:none; border-bottom:3px solid transparent; font-weight:600; cursor:pointer; transition:all 0.3s; color:#999;">
                        Pattern
                    </button>
                    <button onclick="switchTextCustomizationTab('mascot')" id="tabMascot" class="text-custom-tab"
                        style="flex:1; padding:12px; background:#fff; border:none; border-bottom:3px solid transparent; font-weight:600; cursor:pointer; transition:all 0.3s; color:#999;">
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

                            <div class="control-group"
                                style="display:flex; align-items:center; gap:10px; margin-bottom:12px; flex-wrap:nowrap;">
                                <label
                                    style="font-weight:500; font-size:12px; color:#333; width:75px; flex-shrink:0;">Text-Color</label>
                                <div id="baseColorPicker"
                                    style="display:flex; gap:5px; flex-wrap:nowrap; align-items:center;"></div>
                            </div>

                            <div id="outline1Section" class="control-group"
                                style="display:flex; align-items:center; gap:10px; margin-bottom:12px; flex-wrap:nowrap;">
                                <label
                                    style="font-weight:500; font-size:12px; color:#333; width:75px; flex-shrink:0;">Outline 1</label>
                                <div id="outline1ColorPicker"
                                    style="display:flex; gap:5px; flex-wrap:nowrap; align-items:center; flex-shrink:0;">
                                </div>
                                <input type="number" min="0" value="3"
                                    onchange="updateOutlineStroke('outline1', this.value)"
                                    style="width:55px; padding:4px 6px; border:1px solid #ccc; border-radius:4px; font-size:12px; text-align:center; flex-shrink:0;">
                            </div>

                            <div id="outline2Section" class="control-group"
                                style="display:none; align-items:center; gap:10px; margin-bottom:12px; flex-wrap:nowrap;">
                                <label
                                    style="font-weight:500; font-size:12px; color:#333; width:75px; flex-shrink:0;">Outline 2</label>
                                <div id="outline2ColorPicker"
                                    style="display:flex; gap:5px; flex-wrap:nowrap; align-items:center; flex-shrink:0;">
                                </div>
                                <input type="number" min="0" value="3"
                                    onchange="updateOutlineStroke('outline2', this.value)"
                                    style="width:55px; padding:4px 6px; border:1px solid #ccc; border-radius:4px; font-size:12px; text-align:center; flex-shrink:0;">
                            </div>

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
                        </div>
                    </div>
                </div>

                {{-- Tab Content: PATTERN --}}
                <div id="patternTabContent" class="tab-content" style="display:none;">

                    <div style="margin-bottom:20px;">
                        <button onclick="openTextPatternLibrary()"
                            style="width:100%; padding:14px; background:#000; color:white; border:none; border-radius:6px; font-weight:600; cursor:pointer; font-size:14px; display:flex; align-items:center; justify-content:center; gap:8px; transition:all 0.3s;">
                            <span style="font-size:18px;">🎨</span>
                            Select Pattern for Text
                        </button>
                    </div>

                    <div id="textPatternColorControls" style="display:none;">
                        <div
                            style="margin-bottom:15px; padding:12px; background:white; border-radius:6px; border:2px solid #e0e0e0;">
                            <div style="font-size:12px; font-weight:600; color:#666; margin-bottom:8px;">PATTERN PREVIEW:</div>
                            <div id="textPatternPreview"
                                style="height:80px; display:flex; align-items:center; justify-content:center; background:#f5f5f5; border-radius:4px;">
                            </div>
                        </div>

                        <div
                            style="margin-bottom:15px; padding:12px; background:#f8f9fa; border-radius:6px; border:2px solid #e0e0e0;">
                            <div style="font-size:12px; font-weight:600; color:#666; margin-bottom:10px;">PATTERN COLORS:</div>
                            <div id="patternColorPaletteInTab"></div>
                        </div>

                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; font-size:14px;">Pattern Size: <span
                                    id="patternSizeValueTab">100</span>%</label>
                            <input type="range" min="10" max="200" value="100" id="patternSizeTab"
                                oninput="updateTextPatternSize(this.value); document.getElementById('patternSizeValueTab').textContent=this.value;"
                                class="app-slider" style="width:100%; cursor:pointer;">
                        </div>

                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; font-size:14px;">Pattern Opacity: <span
                                    id="patternOpacityValueTab">100</span>%</label>
                            <input type="range" min="0" max="100" value="100"
                                id="patternOpacityTab"
                                oninput="updateTextPatternOpacity(this.value); document.getElementById('patternOpacityValueTab').textContent=this.value;"
                                class="app-slider" style="width:100%; cursor:pointer;">
                        </div>

                        <button onclick="clearTextPattern()"
                            style="width:100%; padding:10px; background:#000000; color:white; border:none; border-radius:6px; font-weight:600; cursor:pointer; margin-top:10px;">
                            Clear Pattern
                        </button>
                    </div>

                    <p id="patternPlaceholder"
                        style="color:#999; text-align:center; padding:40px 20px; font-size:14px;">
                        Select a pattern to fill your text</p>
                </div>

                {{-- Tab Content: MASCOT (text fill) --}}
                <div id="mascotTabContent" class="tab-content" style="display:none;">

                    <div style="margin-bottom:20px;">
                        <button onclick="openTextMascotLibrary()"
                            style="width:100%; padding:14px; background:#000; color:white; border:none; border-radius:6px; font-weight:600; cursor:pointer; font-size:14px; display:flex; align-items:center; justify-content:center; gap:8px; transition:all 0.3s;">
                            <span style="font-size:18px;">🦅</span>
                            Select Mascot for Text
                        </button>
                    </div>

                    <div id="textMascotColorControls" style="display:none;">
                        <div
                            style="margin-bottom:15px; padding:12px; background:white; border-radius:6px; border:2px solid #e0e0e0;">
                            <div style="font-size:12px; font-weight:600; color:#666; margin-bottom:8px;">MASCOT PREVIEW:</div>
                            <div id="textMascotPreview"
                                style="height:80px; display:flex; align-items:center; justify-content:center; background:#f5f5f5; border-radius:4px;">
                            </div>
                        </div>

                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; font-size:14px;">Mascot Size: <span
                                    id="mascotSizeValueTab">100</span>%</label>
                            <input type="range" min="10" max="200" value="100"
                                id="mascotSizeTabSlider"
                                oninput="updateTextMascotSize(this.value); document.getElementById('mascotSizeValueTab').textContent=this.value;"
                                class="app-slider" style="width:100%; cursor:pointer;">
                        </div>

                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; font-size:14px;">Mascot Opacity: <span
                                    id="mascotOpacityValueTab">100</span>%</label>
                            <input type="range" min="0" max="100" value="100"
                                id="mascotOpacityTabSlider"
                                oninput="updateTextMascotOpacity(this.value); document.getElementById('mascotOpacityValueTab').textContent=this.value;"
                                class="app-slider" style="width:100%; cursor:pointer;">
                        </div>

                        <div style="margin-bottom:12px;">
                            <label style="font-weight:600; font-size:14px;">Mascot Count: <span
                                    id="mascotCountValueTab">4</span></label>
                            <input type="range" min="1" max="12" value="4"
                                id="mascotCountTabSlider"
                                oninput="updateTextMascotCount(this.value); document.getElementById('mascotCountValueTab').textContent=this.value;"
                                class="app-slider" style="width:100%; cursor:pointer;">
                        </div>

                        <button onclick="clearTextMascot()"
                            style="width:100%; padding:10px; background:#000000; color:white; border:none; border-radius:6px; font-weight:600; cursor:pointer; margin-top:10px;">
                            Clear Mascot
                        </button>
                    </div>

                    <p id="mascotPlaceholder"
                        style="color:#999; text-align:center; padding:40px 20px; font-size:14px;">
                        Select a mascot to fill your text</p>
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
        <div style="padding:20px; background:#2a2a2a; color:white; display:flex; justify-content:space-between; align-items:center;">
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
    .app-slider {
        -webkit-appearance: none;
        appearance: none;
        height: 4px;
        background: #ddd;
        border-radius: 2px;
        outline: none;
    }
text:focus{
outline:none !important;
}

   .app-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 16px;
    height: 16px;
    background: #000;
    border-radius: 3px; /* square box */
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
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
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
    #fontGrid > div {
        border: 2px solid #ddd;
        padding: 10px 6px;
        text-align: center;
        border-radius: 8px;
        cursor: pointer;
        background: #fff;
        transition: all 0.2s;
    }

    #fontGrid > div:hover {
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
    #rotationManual { -moz-appearance: textfield; }
</style>

@include('admin.models.partials.mascot-select-modal')

<script>
    window.backendFonts = @json($fonts ?? []);
</script>
