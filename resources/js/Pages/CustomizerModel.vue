<template>
  <div class="customize-container">
    <!-- Header -->
    <div class="header-bar">
      <a :href="routes.modelsIndex" class="logo">
        <img :src="assets.logoUrl" alt="Logo" class="logo-img" />
      </a>

      <div class="model-title" id="modelTitle">{{ headerTitle }}</div>

      <a :href="routes.modelsIndex" class="close-btn">✕ Close</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Model View Area -->
      <div class="model-view-area">
        <div class="model-display">
          <div class="model-view-container" id="modelDisplay">
            <div v-if="isLoading" style="color:#999;font-size:18px;text-align:center;padding:40px;">
              Loading model...
            </div>

            <!-- Dynamic injected HTML (SVG stacking) -->
            <div v-else v-html="modelDisplayHtml"></div>
          </div>
        </div>
      </div>

      <!-- Tools Bar -->
      <div class="tools-bar">
        <!-- Part Selection -->
        <div class="part-selection" style="position:relative;">
          <button class="nav-arrow" @click="navigatePart(-1)">‹</button>

          <div class="part-selection" style="position:relative;">
            <button id="partDropdownBtn" @click="togglePartDropdown">
              Select Part ▼
            </button>

            <ul
              id="partDropdown"
              v-show="isPartDropdownOpen"
              style="position:absolute; list-style:none; padding:0; margin:0; border:1px solid #ccc; background:#fff; max-height:200px; overflow-y:auto; z-index:1000; left:0; right:0;"
            >
              <li
                v-for="(part, index) in allSvgParts"
                :key="part.id || index"
                style="padding:8px; cursor:pointer;"
                @mouseenter="hoverLi = index"
                @mouseleave="hoverLi = null"
                :style="{ background: hoverLi === index ? '#eee' : '#fff' }"
                @click="onPickPart(part)"
              >
                {{ part.id || `Part ${index + 1}` }}
              </li>
            </ul>
          </div>

          <button class="nav-arrow" @click="navigatePart(1)">›</button>
        </div>

        <!-- Color Wheel Section -->
        <div class="color-wheel-section" id="colorSection">
          <!-- COLOR WHEEL VIEW -->
          <div v-show="activeMainTab === 'color'" id="colorWheelView">
            <div class="color-wheel-container">
              <div class="color-wheel-outer">
                <div class="color-wheel-ring" id="colorWheelRing"></div>
                <div class="color-wheel-white-ring"></div>
                <div
                  class="color-wheel-center"
                  id="selectedColorBtn"
                  :style="{ background: selectedColorBtnBg }"
                  @click="openColorPalette"
                >
                  SELECT<br />COLORS
                </div>
              </div>
            </div>
          </div>

          <!-- PATTERN VIEW -->
          <div
            v-show="activeMainTab === 'pattern'"
            id="patternView"
            style="width:100%;height:100%;padding:20px;box-sizing:border-box;flex-direction:column;display:flex;"
          >
            <div style="display:flex;gap:12px;margin-bottom:20px;max-width:360px;margin:0 auto;">
              <button class="custom-btn" @click="openPatternLibrary">
                <span class="custom-icon">🎨</span>
                <span class="custom-text">PATTERN<br />SELECT PATTERN</span>
              </button>

              <button class="custom-btn">
                <span class="custom-icon">👾</span>
                <span class="custom-text">🎭 MASCOT</span>
              </button>
            </div>

            <!-- Pattern Controls -->
            <div
              id="patternControls"
              v-show="showPatternControls"
              style="background:#fff;padding:20px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.25);width:100%;max-width:360px;margin:20px auto;"
            >
              <!-- Pattern Preview -->
              <div style="text-align:center;margin-bottom:20px;" id="patternPreviewBox"></div>

              <!-- Color Mix Grid -->
              <div id="patternColorPalette"></div>

              <!-- Size -->
              <div style="margin-bottom:12px;">
                <label style="font-weight:600;font-size:14px;">SIZE</label>
                <input
                  type="range"
                  min="10"
                  max="200"
                  v-model.number="patternSize"
                  @input="updatePatternSize(patternSize)"
                  style="width:100%;"
                />
                <div style="text-align:right;font-size:12px;">
                  <span id="sizeValue">{{ patternSize }}</span>
                </div>
              </div>

              <!-- Opacity -->
              <div style="margin-bottom:12px;">
                <label style="font-weight:600;font-size:14px;">OPACITY</label>
                <input
                  type="range"
                  min="0"
                  max="100"
                  v-model.number="patternOpacity"
                  @input="updatePatternOpacity(patternOpacity)"
                  style="width:100%;"
                />
                <div style="text-align:right;font-size:12px;">
                  <span id="opacityValue">{{ patternOpacity }}</span>%
                </div>
              </div>

              <!-- Angle circular slider (same DOM - you can keep it) -->
              <div style="margin-bottom:12px;">
                <label style="font-weight:600;font-size:14px;">ANGLE (Clockwise)</label>
                <div id="circularSlider" style="position:relative; width:140px; height:140px; margin:20px auto;">
                  <div style="position:absolute; inset:0; border:2px solid #ddd; border-radius:50%;"></div>

                  <svg width="140" height="140" style="position:absolute; inset:0;">
                    <circle cx="70" cy="70" r="65" fill="none" stroke="#e0e0e0" stroke-width="8" />
                    <circle
                      id="progressCircle"
                      cx="70"
                      cy="70"
                      r="65"
                      fill="none"
                      stroke="#007bff"
                      stroke-width="8"
                      stroke-dasharray="408"
                      stroke-dashoffset="408"
                      transform="rotate(-90 70 70)"
                    />
                  </svg>

                  <div
                    id="knob"
                    style="position:absolute; width:24px; height:24px; background:#007bff; border:3px solid white; box-shadow:0 2px 8px rgba(0,0,0,0.4); cursor:grab; user-select:none; transform:translate(-50%,-50%);"
                  ></div>

                  <div
                    id="angleValue"
                    style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-size:18px; font-weight:700; color:#333;"
                  >
                    {{ Math.round(patternAngle) }}°
                  </div>
                </div>
              </div>

              <button
                @click="removePatternFromPart"
                style="width:100%; padding:10px; background:#dc3545; color:white; border:none; border-radius:6px; font-weight:600; cursor:pointer;"
              >
                Remove Pattern
              </button>
            </div>
          </div>
        </div>

        <!-- Tabs Container -->
        <div class="tabs-container">
          <div class="tab-row">
            <div class="tab-btn" :class="{ active: activeTabId==='colorBtn' }" id="colorBtn" @click="activateTab('colorBtn')">
              <i class="fas fa-droplet"></i><span>Color</span>
            </div>
            <div class="tab-btn" :class="{ active: activeTabId==='patternBtn' }" id="patternBtn" @click="activateTab('patternBtn')">
              <i class="fas fa-palette"></i><span>Pattern</span>
            </div>
            <div class="tab-btn" :class="{ active: activeTabId==='applicationBtn' }" id="applicationBtn" @click="activateTab('applicationBtn')">
              <i class="fas fa-layer-group"></i><span>Application</span>
            </div>
            <div class="tab-btn" :class="{ active: activeTabId==='saveBtn' }" id="saveBtn" @click="activateTab('saveBtn')">
              <i class="fas fa-save"></i><span>Save</span>
            </div>
            <div class="tab-btn" :class="{ active: activeTabId==='previewBtn' }" id="previewBtn" @click="activateTab('previewBtn')">
              <i class="fas fa-eye"></i><span>Preview</span>
            </div>
            <div class="tab-btn" :class="{ active: activeTabId==='zoomBtn' }" id="zoomBtn" @click="activateTab('zoomBtn')">
              <i class="fas fa-search-plus"></i><span>Zoom</span>
            </div>
          </div>

          <div class="tab-row">
            <div class="tab-btn" :class="{ active: activeTabId==='frontBtn' }" id="frontBtn" @click="activateTab('frontBtn')">
              <i class="fas fa-arrow-up"></i><span>Front</span>
            </div>
            <div class="tab-btn" :class="{ active: activeTabId==='backBtn' }" id="backBtn" @click="activateTab('backBtn')">
              <i class="fas fa-arrow-down"></i><span>Back</span>
            </div>
            <div class="tab-btn" :class="{ active: activeTabId==='leftBtn' }" id="leftBtn" @click="activateTab('leftBtn')">
              <i class="fas fa-arrow-left"></i><span>Left</span>
            </div>
            <div class="tab-btn" :class="{ active: activeTabId==='rightBtn' }" id="rightBtn" @click="activateTab('rightBtn')">
              <i class="fas fa-arrow-right"></i><span>Right</span>
            </div>

            <div class="tab-btn" :class="{ disabled: !canUndo }" id="undoBtn" @click="activateTab('undoBtn')">
              <i class="fas fa-arrow-left"></i><span>Undo</span>
            </div>
            <div class="tab-btn" :class="{ disabled: !canRedo }" id="redoBtn" @click="activateTab('redoBtn')">
              <i class="fas fa-arrow-right"></i><span>Redo</span>
            </div>

            <div class="tab-btn" :class="{ active: activeTabId==='resetBtn' }" id="resetBtn" @click="activateTab('resetBtn')">
              <i class="fas fa-undo"></i><span>Reset</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Color Palette Modal -->
    <div id="colorPaletteModal" class="color-modal" v-show="isColorModalOpen">
      <div class="color-modal-content">
        <div class="color-modal-header">
          <h3>Select Colors</h3>
          <span class="color-modal-close" @click="closeColorPalette">✕</span>
        </div>

        <div class="color-grid">
          <div
            v-for="c in colors"
            :key="c"
            class="color-box"
            :style="colorBoxStyle(c)"
            @click="togglePaletteColor(c)"
          >
            <span v-if="paletteSelectedColors.includes(c.toUpperCase())" style="font-weight:800;color:#007bff;">✔</span>
          </div>
        </div>

        <div style="padding: 16px; text-align: center;">
          <button
            @click="applySelectedColors"
            style="padding:10px 24px; font-weight:600; background:#007bff; color:#fff; border:none; border-radius:6px; cursor:pointer;"
          >
            Apply Colors
          </button>
        </div>
      </div>
    </div>

    <!-- Application Modal -->
    <div id="applicationModal" class="color-modal" v-show="isApplicationModalOpen">
      <div class="color-modal-content" style="width:480px;">
        <div class="color-modal-header">
          <h3>Application Options</h3>
          <span class="color-modal-close" @click="closeApplicationModal">✕</span>
        </div>

        <div style="padding:40px 30px; text-align:center;">
          <p style="margin-bottom:30px; font-size:16px; color:#555;">
            Choose what you want to apply on this part
          </p>
          <button
            id="openAdvancedOptionsBtn"
            style="padding:16px 40px; font-size:18px; font-weight:600; background:#ff5722; color:white; border:none; border-radius:10px; cursor:pointer;"
            @click="isAdvancedModalOpen = true"
          >
            Advanced Applications & Effects →
          </button>
        </div>
      </div>
    </div>

    <!-- Advanced Application Modal -->
    <div id="advancedApplicationModal" class="color-modal" v-show="isAdvancedModalOpen">
      <div class="color-modal-content" style="width:420px;">
        <div class="color-modal-header" style="background:#ff5722; display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; color:white;">Add a new Free Form Application</h3>
          <span style="cursor:pointer; color:white;" @click="isAdvancedModalOpen=false">×</span>
        </div>

        <div style="padding:20px;">
          <p><strong>1. What type of application do you want to add?</strong></p>
          <input v-model="appForm.playerNo" placeholder="Player #" style="width:100%; margin-bottom:5px; padding:6px;" />
          <input v-model="appForm.teamName" placeholder="Team Name" style="width:100%; margin-bottom:5px; padding:6px;" />
          <input v-model="appForm.playerName" placeholder="Player Name" style="width:100%; margin-bottom:5px; padding:6px;" />
          <input v-model="appForm.mascot" placeholder="Custom Mascot" style="width:100%; margin-bottom:10px; padding:6px;" />

          <p><strong>2. What perspective?</strong></p>
          <select v-model="appForm.view" style="width:100%; margin-bottom:10px; padding:6px;">
            <option value="front">Front</option>
            <option value="back">Back</option>
            <option value="left">Left</option>
            <option value="right">Right</option>
          </select>

          <p><strong>3. Which part?</strong></p>
          <select v-model="appForm.part" style="width:100%; margin-bottom:15px; padding:6px;">
            <option>Part 1</option>
            <option>Collar</option>
            <option>Stripe 1</option>
            <option>Stripe 2</option>
            <option>Sleeves</option>
            <option>Body</option>
            <option>Cuffs</option>
          </select>

          <button
            style="width:100%; padding:10px; background:#ff5722; color:white; border:none; border-radius:8px; font-weight:600;"
            @click="submitApplication"
          >
            Add Application
          </button>
        </div>
      </div>
    </div>

    <!-- Pattern Library Modal -->
    <div id="patternLibraryModal" class="color-modal" v-show="isPatternLibraryOpen">
      <div class="color-modal-content" style="width:700px;">
        <div class="color-modal-header">
          <h3>Select Pattern</h3>
          <span class="color-modal-close" @click="closePatternLibrary">✕</span>
        </div>

        <div style="padding:20px;">
          <div id="patternList" style="display:grid;grid-template-columns:repeat(4,1fr);gap:15px;">
            <div
              v-for="p in patterns"
              :key="p.id"
              style="border:2px solid #ddd;border-radius:8px;padding:10px;cursor:pointer;text-align:center;background:#fff;"
              @click="applyDBPattern(p.svg_url)"
            >
              <div style="height:120px;display:flex;align-items:center;justify-content:center;">
                <img :src="p.svg_url" style="max-width:100%;max-height:100%;" />
              </div>
              <p style="margin-top:8px;font-weight:600;">{{ p.name }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Preview Panel -->
    <div id="previewPanel" :style="previewPanelStyle">
      <div style="padding:20px 30px; background:#111; color:white; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 10px rgba(0,0,0,0.2);">
        <h3 style="margin:0; font-size:22px; font-weight:600;">
          <i class="fas fa-eye"></i> Preview All Views
        </h3>
        <button
          @click="closePreviewPanel"
          style="background:rgba(255,255,255,0.1); border:2px solid white; color:white; font-size:24px; cursor:pointer; width:45px; height:45px; display:flex; align-items:center; justify-content:center; border-radius:50%; transition:all 0.3s;"
        >
          ×
        </button>
      </div>

      <div style="flex:1; display:flex; align-items:center; justify-content:center; padding:40px 30px; background:#f5f5f5; gap:25px;">
        <div class="preview-card-horizontal">
          <h4><i class="fas fa-arrow-up"></i> Front</h4>
          <div id="previewFront" class="preview-container-horizontal">
            <div class="preview-loading">Loading...</div>
          </div>
        </div>

        <div class="preview-card-horizontal">
          <h4><i class="fas fa-arrow-down"></i> Back</h4>
          <div id="previewBack" class="preview-container-horizontal">
            <div class="preview-loading">Loading...</div>
          </div>
        </div>

        <div class="preview-card-horizontal">
          <h4><i class="fas fa-arrow-left"></i> Left</h4>
          <div id="previewLeft" class="preview-container-horizontal">
            <div class="preview-loading">Loading...</div>
          </div>
        </div>

        <div class="preview-card-horizontal">
          <h4><i class="fas fa-arrow-right"></i> Right</h4>
          <div id="previewRight" class="preview-container-horizontal">
            <div class="preview-loading">Loading...</div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
export default {
  name: "CustomizeModel",
  props: {
    modelId: { type: Number, required: true },
    apiUrl: { type: String, required: true },

    // Colors palette from backend (array of hex strings)
    colors: { type: Array, default: () => [] },

    // Routes/assets passed from blade
    routes: {
      type: Object,
      default: () => ({ modelsIndex: "/" }),
    },
    assets: {
      type: Object,
      default: () => ({ logoUrl: "" }),
    },
  },

  data() {
    return {
      headerTitle: "Customize Model",

      // UI state
      activeTabId: "colorBtn",
      activeMainTab: "color", // 'color' | 'pattern'
      isColorModalOpen: false,
      isApplicationModalOpen: false,
      isAdvancedModalOpen: false,
      isPatternLibraryOpen: false,
      isPreviewOpen: false,
      isPartDropdownOpen: false,
      hoverLi: null,

      // Model state
      isLoading: true,
      currentModel: null,
      currentView: "front",
      modelViews: { front: null, back: null, left: null, right: null },

      modelDisplayHtml: "", // injected HTML stack (svg+overlays)

      // SVG selection
      selectedSvgElement: null,
      allSvgParts: [],
      currentPartIndex: 0,

      // Color wheel
      selectedColors: [],
      paletteSelectedColors: [],
      selectedColorBtnBg: "#ff0000",

      // storage
      STORAGE_KEY_COLORS: null,
      STORAGE_KEY_PATTERNS: null,

      colorChanges: { front: {}, back: {}, left: {}, right: {} },
      originalColors: { front: {}, back: {}, left: {}, right: {} },

      // history
      history: [],
      historyIndex: -1,

      // patterns
      patterns: [],
      uploadedSvgContent: null,
      patternsApplied: { front: {}, back: {}, left: {}, right: {} },
      selectedPatternReplacements: {},
      showPatternControls: false,

      // pattern controls
      patternAngle: 0,
      patternOpacity: 100,
      patternSize: 50,

      // panzoom
      isPanZoomActive: false,
      scale: 1,
      posX: 0,
      posY: 0,
      startX: 0,
      startY: 0,
      isDragging: false,

      // angle knob drag
      isDraggingKnob: false,

      // application form
      appForm: {
        playerNo: "",
        teamName: "",
        playerName: "",
        mascot: "",
        view: "front",
        part: "Part 1",
      },
    };
  },

  computed: {
    canUndo() {
      return this.historyIndex > 0;
    },
    canRedo() {
      return this.historyIndex >= 0 && this.historyIndex < this.history.length - 1;
    },
    previewPanelStyle() {
      return {
        position: "fixed",
        top: "0",
        right: this.isPreviewOpen ? "0" : "-100vw",
        width: "100vw",
        height: "100vh",
        background: "white",
        transition: "right 0.4s cubic-bezier(0.25, 0.8, 0.25, 1)",
        zIndex: 999,
        overflowY: "auto",
        display: "flex",
        flexDirection: "column",
        boxShadow: "-5px 0 20px rgba(0,0,0,0.3)",
      };
    },
  },

  mounted() {
    // ✅ Route se id lete hain
    this.modelId = Number(this.route.params.id);
    this.apiUrl = `/models/${this.modelId}/api`;

    console.log("✅ modelId:", this.modelId, "apiUrl:", this.apiUrl); // debug — baad mein hatao

    // unique localStorage keys per model
    this.STORAGE_KEY_COLORS = `prosix_model_${this.modelId}_colors`;
    this.STORAGE_KEY_PATTERNS = `prosix_model_${this.modelId}_patterns`;

    document.addEventListener("click", this.onOutsideClick);
    document.addEventListener("keydown", this.onKeyDown);

    this.init();
  },

  beforeUnmount() {
    document.removeEventListener("click", this.onOutsideClick);
    document.removeEventListener("keydown", this.onKeyDown);
  },

  methods: {
    // =========================
    // INIT + LOAD
    // =========================
    async init() {
      this.loadSavedCustomizations();
      await this.loadModel();
      this.setupCircularSlider(); // knob slider
    },

    loadSavedCustomizations() {
      try {
        const savedColors = localStorage.getItem(this.STORAGE_KEY_COLORS);
        if (savedColors) this.colorChanges = JSON.parse(savedColors);

        const savedPatterns = localStorage.getItem(this.STORAGE_KEY_PATTERNS);
        if (savedPatterns) this.patternsApplied = JSON.parse(savedPatterns);
      } catch (e) {
        console.error("loadSavedCustomizations error:", e);
      }
    },

    saveCustomizations() {
      try {
        localStorage.setItem(this.STORAGE_KEY_COLORS, JSON.stringify(this.colorChanges));
        localStorage.setItem(this.STORAGE_KEY_PATTERNS, JSON.stringify(this.patternsApplied));
      } catch (e) {
        console.error("saveCustomizations error:", e);
      }
    },

    async loadModel() {
      try {
        this.isLoading = true;
        const res = await fetch(this.apiUrl);
        const data = await res.json();

        this.currentModel = data;
        this.modelViews.front = data.front_view || {};
        this.modelViews.back = data.back_view || {};
        this.modelViews.left = data.left_view || {};
        this.modelViews.right = data.right_view || {};

        this.displayView("front");

        // wait a bit then extract default palette
        setTimeout(() => this.extractDefaultColors(), 400);

        this.isLoading = false;
      } catch (e) {
        console.error("Error loading model:", e);
        this.modelDisplayHtml = `<div style="color:#ff0000;padding:40px;">Error loading model</div>`;
        this.isLoading = false;
      }
    },

    displayView(view) {
      this.currentView = view;

      const viewData = this.modelViews[view];
      if (!viewData || (!viewData.svg_url && !viewData.black_image_url && !viewData.white_image_url)) {
        this.modelDisplayHtml = `<div style="color:#999;padding:40px;">No images available for this view</div>`;
        return;
      }

      let html = `<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:center;">`;

      if (viewData.svg_url) {
        // NOTE: Vue can't auto-call onload="processSvg()" reliably inside v-html,
        // so we call processSvg() after DOM update using nextTick.
        html += `<img id="svgImage" src="${viewData.svg_url}?t=${Date.now()}" style="position:absolute;max-width:100%;max-height:100%;z-index:1;" />`;
      }

      if (viewData.white_image_url) {
        html += `<img src="${viewData.white_image_url}?t=${Date.now()}" style="position:absolute;max-width:100%;max-height:100%;z-index:2;mix-blend-mode:multiply;pointer-events:none;" />`;
      }

      if (viewData.black_image_url) {
        html += `<img src="${viewData.black_image_url}?t=${Date.now()}" style="position:absolute;max-width:100%;max-height:100%;z-index:3;mix-blend-mode:screen;pointer-events:none;" />`;
      }

      html += `</div>`;
      this.modelDisplayHtml = html;

      this.$nextTick(() => {
        const svgImage = document.getElementById("svgImage");
        if (svgImage) {
          svgImage.onload = () => this.processSvg();
        }
      });
    },

    async processSvg() {
      const svgImage = document.getElementById("svgImage");
      if (!svgImage) return;

      try {
        const svgText = await (await fetch(svgImage.src)).text();
        const parser = new DOMParser();
        const svgDoc = parser.parseFromString(svgText, "image/svg+xml");
        const svgElement = svgDoc.querySelector("svg");
        if (!svgElement) return;

        svgElement.setAttribute("width", "100%");
        svgElement.setAttribute("height", "100%");
        svgElement.setAttribute("preserveAspectRatio", "xMidYMid meet");

        const elements = svgElement.querySelectorAll("path, polygon, circle, rect, ellipse");
        this.allSvgParts = Array.from(elements);

        elements.forEach((el, index) => {
          if (!el.id) el.id = `svg-part-${index}`;
          if (!el.dataset.partName) el.dataset.partName = `Part ${index + 1}`;

          el.classList.add("svg-hoverable");
          el.style.cursor = "pointer";
          el.style.transition = "all 0.3s ease";

          el.addEventListener("click", () => this.selectSvgElement(el));
          el.addEventListener("mouseenter", () => {
            if (el !== this.selectedSvgElement) el.style.filter = "brightness(1.2)";
          });
          el.addEventListener("mouseleave", () => {
            if (el !== this.selectedSvgElement) el.style.filter = "";
          });

          // store original
          if (!this.originalColors[this.currentView][el.id]) {
            this.originalColors[this.currentView][el.id] = el.getAttribute("fill") || "#ffffff";
          }

          // apply saved color
          if (this.colorChanges[this.currentView] && this.colorChanges[this.currentView][el.id]) {
            el.setAttribute("fill", this.colorChanges[this.currentView][el.id]);
          }
        });

        svgImage.replaceWith(svgElement);

        // apply saved patterns
        this.applyPatternsToSvg(svgElement, this.currentView);

        // redraw wheel
        this.updateColorWheel();
      } catch (err) {
        console.error("SVG load error:", err);
      }
    },

    // =========================
    // PART DROPDOWN
    // =========================
    togglePartDropdown() {
      this.isPartDropdownOpen = !this.isPartDropdownOpen;
    },

    onOutsideClick(e) {
      const box = document.querySelector(".part-selection");
      if (box && !box.contains(e.target)) {
        this.isPartDropdownOpen = false;
      }
    },

    onPickPart(part) {
      this.selectSvgElement(part);
      this.isPartDropdownOpen = false;
    },

    navigatePart(dir) {
      if (!this.allSvgParts.length) return;
      this.currentPartIndex = (this.currentPartIndex + dir + this.allSvgParts.length) % this.allSvgParts.length;
      this.selectSvgElement(this.allSvgParts[this.currentPartIndex]);
    },

    // =========================
    // SELECT SVG ELEMENT
    // =========================
    selectSvgElement(el) {
      document.querySelectorAll(".selected").forEach((e) => {
        e.classList.remove("selected");
        e.style.filter = "";
      });

      this.selectedSvgElement = el;
      el.classList.add("selected");
      el.style.filter = "brightness(1.3)";
      this.currentPartIndex = this.allSvgParts.indexOf(el);

      const fill = el.getAttribute("fill") || "#ff0000";
      this.selectedColorBtnBg = fill;

      // pattern controls show/hide
      if (el.dataset.hasPattern && el.dataset.patternId) {
        this.showPatternControls = true;

        // load saved replacements for this part
        const saved = this.patternsApplied?.[this.currentView]?.[el.id]?.replacements;
        this.selectedPatternReplacements = saved ? { ...saved } : {};

        // update palette for pattern colors
        this.$nextTick(() => this.updatePatternColorPalette());
      } else {
        this.showPatternControls = false;
        this.selectedPatternReplacements = {};
      }
    },

    // =========================
    // COLOR WHEEL + MODAL
    // =========================
    extractDefaultColors() {
      const svg = document.querySelector(".model-view-container svg");
      if (!svg) return;

      const colors = new Set();
      svg.querySelectorAll("path, polygon, circle, rect, ellipse").forEach((el) => {
        const f = el.getAttribute("fill");
        if (f && f !== "none" && f !== "transparent" && !f.startsWith("url")) {
          colors.add(f.toUpperCase());
        }
      });

      this.selectedColors = Array.from(colors);
      if (this.selectedColors.length < 2) this.selectedColors = ["#FF0000", "#000000"];
      if (this.selectedColors.length > 24) this.selectedColors = this.selectedColors.slice(0, 24);

      this.updateColorWheel();
    },

    updateColorWheel() {
      const wheel = document.getElementById("colorWheelRing");
      if (!wheel || !this.selectedColors.length) return;
      wheel.innerHTML = "";

      const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
      svg.setAttribute("width", "250");
      svg.setAttribute("height", "250");
      svg.style.position = "absolute";

      const center = 125;
      const radius = 120;
      const innerRadius = 80;
      const angleStep = 360 / this.selectedColors.length;

      this.selectedColors.forEach((color, i) => {
        const startAngle = (i * angleStep - 90) * (Math.PI / 180);
        const endAngle = ((i + 1) * angleStep - 90) * (Math.PI / 180);

        const x1 = center + radius * Math.cos(startAngle);
        const y1 = center + radius * Math.sin(startAngle);
        const x2 = center + radius * Math.cos(endAngle);
        const y2 = center + radius * Math.sin(endAngle);
        const x3 = center + innerRadius * Math.cos(endAngle);
        const y3 = center + innerRadius * Math.sin(endAngle);
        const x4 = center + innerRadius * Math.cos(startAngle);
        const y4 = center + innerRadius * Math.sin(startAngle);

        const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
        const largeArc = angleStep > 180 ? 1 : 0;

        path.setAttribute(
          "d",
          `M ${center},${center} L ${x1},${y1} A ${radius},${radius} 0 ${largeArc},1 ${x2},${y2} L ${x3},${y3} A ${innerRadius},${innerRadius} 0 ${largeArc},0 ${x4},${y4} Z`
        );
        path.setAttribute("fill", color);
        path.setAttribute("stroke", "#fff");
        path.setAttribute("stroke-width", "1");
        path.style.cursor = "pointer";

        path.addEventListener("click", () => {
          if (!this.selectedSvgElement) return;

          this.saveToHistory();

          this.selectedSvgElement.setAttribute("fill", color);
          this.selectedColorBtnBg = color;

          if (!this.colorChanges[this.currentView]) this.colorChanges[this.currentView] = {};
          this.colorChanges[this.currentView][this.selectedSvgElement.id] = color;

          this.saveCustomizations();
        });

        svg.appendChild(path);
      });

      wheel.appendChild(svg);
    },

    openColorPalette() {
      this.isColorModalOpen = true;
    },

    closeColorPalette() {
      this.isColorModalOpen = false;
      this.paletteSelectedColors = [];
    },

    togglePaletteColor(color) {
      const upper = String(color).toUpperCase();
      const idx = this.paletteSelectedColors.indexOf(upper);
      if (idx >= 0) this.paletteSelectedColors.splice(idx, 1);
      else this.paletteSelectedColors.push(upper);
    },

    applySelectedColors() {
      if (!this.paletteSelectedColors.length) {
        alert("Select at least one color!");
        return;
      }

      this.paletteSelectedColors.forEach((c) => {
        if (!this.selectedColors.includes(c)) this.selectedColors.push(c);
      });

      if (this.selectedColors.length > 24) this.selectedColors = this.selectedColors.slice(-24);

      // apply first picked color to selected part
      if (this.selectedSvgElement) {
        const c = this.paletteSelectedColors[0];

        this.saveToHistory();

        this.selectedSvgElement.setAttribute("fill", c);
        this.selectedColorBtnBg = c;

        if (!this.colorChanges[this.currentView]) this.colorChanges[this.currentView] = {};
        this.colorChanges[this.currentView][this.selectedSvgElement.id] = c;

        this.saveCustomizations();
      }

      this.updateColorWheel();
      this.closeColorPalette();

      // if preview open, update
      if (this.isPreviewOpen) this.updateSinglePreviewView(this.currentView);
    },

    colorBoxStyle(c) {
      const code = String(c).toUpperCase();
      return {
        background: c,
        border: code === "#FFFFFF" ? "1px solid #ccc" : "2px solid transparent",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
      };
    },

    // =========================
    // HISTORY / UNDO REDO
    // =========================
    saveToHistory() {
      this.history = this.history.slice(0, this.historyIndex + 1);
      this.history.push({
        view: this.currentView,
        colors: JSON.parse(JSON.stringify(this.colorChanges)),
        patterns: JSON.parse(JSON.stringify(this.patternsApplied)),
      });
      this.historyIndex++;

      if (this.history.length > 50) {
        this.history.shift();
        this.historyIndex--;
      }
    },

    undoChange() {
      if (!this.canUndo) return;
      this.historyIndex--;
      const state = this.history[this.historyIndex];
      this.colorChanges = JSON.parse(JSON.stringify(state.colors));
      this.patternsApplied = JSON.parse(JSON.stringify(state.patterns));
      this.saveCustomizations();
      this.displayView(this.currentView);
    },

    redoChange() {
      if (!this.canRedo) return;
      this.historyIndex++;
      const state = this.history[this.historyIndex];
      this.colorChanges = JSON.parse(JSON.stringify(state.colors));
      this.patternsApplied = JSON.parse(JSON.stringify(state.patterns));
      this.saveCustomizations();
      this.displayView(this.currentView);
    },

    resetDesign() {
      if (!confirm("Are you sure you want to reset all changes?")) return;

      this.colorChanges = { front: {}, back: {}, left: {}, right: {} };
      this.patternsApplied = { front: {}, back: {}, left: {}, right: {} };
      this.history = [];
      this.historyIndex = -1;

      this.saveCustomizations();
      this.displayView(this.currentView);

      alert("Design reset successfully!");
    },

    // =========================
    // SAVE DESIGN (server)
    // =========================
    async saveDesign() {
      try {
        const res = await fetch(`/models/${this.modelId}/save-design`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "",
            Accept: "application/json",
          },
          body: JSON.stringify({ color_changes: JSON.stringify(this.colorChanges) }),
        });

        const result = await res.json();
        if (result.success) alert("Design successfully save ho gaya! ✓");
        else alert("Save nahi hua: " + (result.message || "Unknown error"));
      } catch (e) {
        console.error("Save error:", e);
        alert("Save karte waqt error aaya: " + e.message);
      }
    },

    // =========================
    // TABS
    // =========================
    activateTab(tabId) {
      this.activeTabId = tabId;

      switch (tabId) {
        case "colorBtn":
          this.activeMainTab = "color";
          break;

        case "patternBtn":
          this.activeMainTab = "pattern";
          break;

        case "applicationBtn":
          this.isApplicationModalOpen = true;
          break;

        case "saveBtn":
          this.saveDesign();
          break;

        case "previewBtn":
          this.openPreviewPanel();
          break;

        case "zoomBtn":
          this.togglePanZoom();
          break;

        case "frontBtn":
          this.switchView("front");
          break;
        case "backBtn":
          this.switchView("back");
          break;
        case "leftBtn":
          this.switchView("left");
          break;
        case "rightBtn":
          this.switchView("right");
          break;

        case "undoBtn":
          this.undoChange();
          break;
        case "redoBtn":
          this.redoChange();
          break;

        case "resetBtn":
          this.resetDesign();
          break;
      }
    },

    switchView(view) {
      this.displayView(view);
      if (this.isPreviewOpen) this.updateSinglePreviewView(view);
    },

    // =========================
    // APPLICATION MODALS
    // =========================
    closeApplicationModal() {
      this.isApplicationModalOpen = false;
    },

    submitApplication() {
      alert("Application Added!");
      this.isAdvancedModalOpen = false;
    },

    // =========================
    // PATTERN LIBRARY
    // =========================
    openPatternLibrary() {
      this.isPatternLibraryOpen = true;
      this.loadPatternsFromDB();
    },

    closePatternLibrary() {
      this.isPatternLibraryOpen = false;
    },

    async loadPatternsFromDB() {
      try {
        const res = await fetch("/api/patterns");
        this.patterns = await res.json();
      } catch (e) {
        console.error("loadPatternsFromDB error:", e);
      }
    },

    async applyDBPattern(svgUrl) {
      if (!this.selectedSvgElement) {
        alert("Pehle model ka part select karo!");
        return;
      }

      try {
        const svgContent = await (await fetch(svgUrl)).text();
        this.uploadedSvgContent = svgContent;
        this.applyUploadedPattern();
        this.closePatternLibrary();

        if (this.isPreviewOpen) this.updateSinglePreviewView(this.currentView);
      } catch (e) {
        console.error("applyDBPattern error:", e);
      }
    },

    applyUploadedPattern() {
      if (!this.selectedSvgElement) return;
      if (!this.uploadedSvgContent) return;

      this.saveToHistory();

      const parser = new DOMParser();
      const svgDoc = parser.parseFromString(this.uploadedSvgContent, "image/svg+xml");
      const uploadedSvg = svgDoc.querySelector("svg");
      if (!uploadedSvg) {
        alert("Invalid SVG!");
        return;
      }

      const mainSvg = document.querySelector(".model-view-container svg");
      if (!mainSvg) return;

      let defs = mainSvg.querySelector("defs");
      if (!defs) {
        defs = document.createElementNS("http://www.w3.org/2000/svg", "defs");
        mainSvg.insertBefore(defs, mainSvg.firstChild);
      }

      const patternId = `pattern-${this.selectedSvgElement.id}-${this.currentView}`;
      const bbox = this.selectedSvgElement.getBBox();

      const pattern = document.createElementNS("http://www.w3.org/2000/svg", "pattern");
      pattern.setAttribute("id", patternId);
      pattern.setAttribute("patternUnits", "userSpaceOnUse");
      pattern.setAttribute("x", bbox.x);
      pattern.setAttribute("y", bbox.y);
      pattern.setAttribute("width", bbox.width);
      pattern.setAttribute("height", bbox.height);

      const g = document.createElementNS("http://www.w3.org/2000/svg", "g");
      const content = uploadedSvg.cloneNode(true);

      content.setAttribute("width", bbox.width);
      content.setAttribute("height", bbox.height);
      content.setAttribute("preserveAspectRatio", "xMidYMid slice");

      g.appendChild(content);
      pattern.appendChild(g);
      defs.appendChild(pattern);

      // overlay group
      let overlayGroup = mainSvg.querySelector("#pattern-overlay-group");
      if (!overlayGroup) {
        overlayGroup = document.createElementNS("http://www.w3.org/2000/svg", "g");
        overlayGroup.setAttribute("id", "pattern-overlay-group");
        mainSvg.appendChild(overlayGroup);
      }

      const overlay = this.selectedSvgElement.cloneNode(true);
      overlay.setAttribute("id", "pattern-overlay-" + this.selectedSvgElement.id);
      overlay.setAttribute("fill", `url(#${patternId})`);
      overlay.style.opacity = "1";
      overlay.style.pointerEvents = "none";

      const oldOverlay = overlayGroup.querySelector(`#pattern-overlay-${this.selectedSvgElement.id}`);
      if (oldOverlay) oldOverlay.remove();

      overlayGroup.appendChild(overlay);

      // dataset
      this.selectedSvgElement.dataset.hasPattern = "true";
      this.selectedSvgElement.dataset.patternId = patternId;
      this.selectedSvgElement.dataset.patternOpacity = "100";
      this.selectedSvgElement.dataset.patternSize = String(this.patternSize);
      this.selectedSvgElement.dataset.patternAngle = String(this.patternAngle);

      if (!this.patternsApplied[this.currentView]) this.patternsApplied[this.currentView] = {};
      this.patternsApplied[this.currentView][this.selectedSvgElement.id] = {
        patternId,
        svgContent: this.uploadedSvgContent,
        opacity: 100,
        size: this.patternSize,
        angle: this.patternAngle,
        replacements: {},
      };

      this.saveCustomizations();
      this.showPatternControls = true;

      this.$nextTick(() => this.updatePatternColorPalette());
    },

    removePatternFromPart() {
      if (!this.selectedSvgElement) return;

      this.saveToHistory();

      const originalColor = this.originalColors[this.currentView][this.selectedSvgElement.id] || "#ffffff";
      this.selectedSvgElement.setAttribute("fill", originalColor);
      this.selectedSvgElement.style.opacity = "1";

      const overlayGroup = document.querySelector("#pattern-overlay-group");
      if (overlayGroup) {
        const overlay = overlayGroup.querySelector(`#pattern-overlay-${this.selectedSvgElement.id}`);
        if (overlay) overlay.remove();
      }

      delete this.selectedSvgElement.dataset.hasPattern;
      delete this.selectedSvgElement.dataset.patternId;
      delete this.selectedSvgElement.dataset.patternSize;
      delete this.selectedSvgElement.dataset.patternOpacity;
      delete this.selectedSvgElement.dataset.patternAngle;

      if (this.patternsApplied[this.currentView]?.[this.selectedSvgElement.id]) {
        delete this.patternsApplied[this.currentView][this.selectedSvgElement.id];
        this.saveCustomizations();
      }

      this.showPatternControls = false;
      alert("Pattern remove ho gaya!");

      if (this.isPreviewOpen) this.updateSinglePreviewView(this.currentView);
    },

    applyPatternsToSvg(svgElement, view) {
      if (!this.patternsApplied[view]) return;

      let defs = svgElement.querySelector("defs");
      if (!defs) {
        defs = document.createElementNS("http://www.w3.org/2000/svg", "defs");
        svgElement.insertBefore(defs, svgElement.firstChild);
      }

      Object.entries(this.patternsApplied[view]).forEach(([partId, patternData]) => {
        const element = svgElement.querySelector(`#${partId}`);
        if (!element) return;

        const pattern = document.createElementNS("http://www.w3.org/2000/svg", "pattern");
        pattern.setAttribute("id", patternData.patternId);
        pattern.setAttribute("patternUnits", "userSpaceOnUse");

        const bbox = element.getBBox();
        pattern.setAttribute("x", bbox.x);
        pattern.setAttribute("y", bbox.y);
        pattern.setAttribute("width", bbox.width);
        pattern.setAttribute("height", bbox.height);

        if (patternData.svgContent) {
          const parser = new DOMParser();
          const doc = parser.parseFromString(patternData.svgContent, "image/svg+xml");
          const patternSvg = doc.querySelector("svg");

          if (patternSvg) {
            // apply replacements
            if (patternData.replacements) {
              Object.entries(patternData.replacements).forEach(([oldColor, newColor]) => {
                patternSvg.querySelectorAll("[fill]").forEach((n) => {
                  if ((n.getAttribute("fill") || "").toUpperCase() === oldColor.toUpperCase()) {
                    n.setAttribute("fill", newColor);
                  }
                });
                patternSvg.querySelectorAll("[stroke]").forEach((n) => {
                  if ((n.getAttribute("stroke") || "").toUpperCase() === oldColor.toUpperCase()) {
                    n.setAttribute("stroke", newColor);
                  }
                });
              });
            }

            const g = document.createElementNS("http://www.w3.org/2000/svg", "g");
            const content = patternSvg.cloneNode(true);

            const scale = (patternData.size || 50) / 10;
            const angle = patternData.angle || 0;
            const opacity = (patternData.opacity || 100) / 100;

            g.setAttribute("opacity", opacity);
            g.setAttribute("transform", `scale(${scale}) rotate(${angle} 0.5 0.5)`);

            content.setAttribute("width", bbox.width);
            content.setAttribute("height", bbox.height);
            content.setAttribute("preserveAspectRatio", "xMidYMid slice");

            g.appendChild(content);
            pattern.appendChild(g);
          }
        }

        defs.appendChild(pattern);

        // overlay
        let overlayGroup = svgElement.querySelector("#pattern-overlay-group");
        if (!overlayGroup) {
          overlayGroup = document.createElementNS("http://www.w3.org/2000/svg", "g");
          overlayGroup.setAttribute("id", "pattern-overlay-group");
          svgElement.appendChild(overlayGroup);
        }

        const overlay = element.cloneNode(true);
        overlay.setAttribute("id", "pattern-overlay-" + element.id);
        overlay.setAttribute("fill", `url(#${patternData.patternId})`);
        overlay.style.opacity = (patternData.opacity || 100) / 100;
        overlay.style.pointerEvents = "none";

        const oldOverlay = overlayGroup.querySelector(`#pattern-overlay-${element.id}`);
        if (oldOverlay) oldOverlay.remove();

        overlayGroup.appendChild(overlay);

        element.dataset.hasPattern = "true";
        element.dataset.patternId = patternData.patternId;
        element.dataset.patternSize = patternData.size || 50;
        element.dataset.patternOpacity = patternData.opacity || 100;
        element.dataset.patternAngle = patternData.angle || 0;
      });
    },

    // Build pattern color palette UI (same logic, DOM-based)
    getPatternUniqueColors(svgContent) {
      if (!svgContent) return ["#000000"];
      const parser = new DOMParser();
      const doc = parser.parseFromString(svgContent, "image/svg+xml");
      const elements = doc.querySelectorAll("[fill], [stroke]");
      const unique = new Set();

      elements.forEach((el) => {
        const fill = el.getAttribute("fill");
        if (fill && fill !== "none" && !fill.startsWith("url")) unique.add(fill.toUpperCase());

        const stroke = el.getAttribute("stroke");
        if (stroke && stroke !== "none" && !stroke.startsWith("url")) unique.add(stroke.toUpperCase());
      });

      return Array.from(unique);
    },

    updatePatternColorPalette() {
      const container = document.getElementById("patternColorPalette");
      if (!container) return;

      container.innerHTML = "";
      if (!this.selectedSvgElement?.dataset?.hasPattern) return;

      const svgContent =
        this.uploadedSvgContent ||
        this.patternsApplied?.[this.currentView]?.[this.selectedSvgElement.id]?.svgContent;

      const patternColors = this.getPatternUniqueColors(svgContent);
      const userColors = this.selectedColors.length ? [...this.selectedColors] : ["#000000"];

      patternColors.forEach((patternColor) => {
        const row = document.createElement("div");
        row.className = "pattern-color-row";

        const originalBox = document.createElement("div");
        originalBox.className = "original-color-box";
        originalBox.style.backgroundColor = patternColor;

        const arrow = document.createElement("div");
        arrow.className = "color-arrow";
        arrow.textContent = "→";

        const choices = document.createElement("div");
        choices.className = "color-choices";

        userColors.forEach((userColor) => {
          const box = document.createElement("div");
          box.className = "user-color-box";
          box.style.backgroundColor = userColor;

          const check = document.createElement("div");
          check.className = "color-checkmark";
          check.textContent = "✓";
          box.appendChild(check);

          // selected state from saved replacements
          const saved = this.selectedPatternReplacements?.[patternColor.toUpperCase()];
          if (saved && saved.toUpperCase() === userColor.toUpperCase()) {
            box.classList.add("selected");
            check.style.display = "flex";
          }

          box.onclick = () => {
            // clear row selections
            choices.querySelectorAll(".user-color-box").forEach((b) => {
              b.classList.remove("selected");
              const c = b.querySelector(".color-checkmark");
              if (c) c.style.display = "none";
            });

            box.classList.add("selected");
            check.style.display = "flex";

            this.selectedPatternReplacements[patternColor.toUpperCase()] = userColor;

            this.recreatePatternAndOverlayWithNewColors();
          };

          choices.appendChild(box);
        });

        row.appendChild(originalBox);
        row.appendChild(arrow);
        row.appendChild(choices);
        container.appendChild(row);
      });

      container.style.display = "flex";
      container.style.flexDirection = "column";
      container.style.gap = "12px";
    },

    recreatePatternAndOverlayWithNewColors() {
      if (!this.selectedSvgElement?.dataset?.hasPattern) return;

      const view = this.currentView;
      const partId = this.selectedSvgElement.id;
      const patternData = this.patternsApplied?.[view]?.[partId];
      if (!patternData) return;

      const mainSvg = document.querySelector(".model-view-container svg");
      if (!mainSvg) return;

      const defs = mainSvg.querySelector("defs") || this.createDefs(mainSvg);
      const patternId = patternData.patternId;

      // remove old pattern
      const oldPattern = defs.querySelector(`#${patternId}`);
      if (oldPattern) oldPattern.remove();

      // recreate
      const pattern = document.createElementNS("http://www.w3.org/2000/svg", "pattern");
      pattern.setAttribute("id", patternId);
      pattern.setAttribute("patternUnits", "userSpaceOnUse");

      const bbox = this.selectedSvgElement.getBBox();
      pattern.setAttribute("x", bbox.x);
      pattern.setAttribute("y", bbox.y);
      pattern.setAttribute("width", bbox.width);
      pattern.setAttribute("height", bbox.height);

      const parser = new DOMParser();
      const doc = parser.parseFromString(patternData.svgContent, "image/svg+xml");
      const patternSvg = doc.querySelector("svg");

      if (patternSvg) {
        Object.entries(this.selectedPatternReplacements).forEach(([oldColor, newColor]) => {
          patternSvg.querySelectorAll("[fill]").forEach((n) => {
            if ((n.getAttribute("fill") || "").toUpperCase() === oldColor.toUpperCase()) {
              n.setAttribute("fill", newColor);
            }
          });
          patternSvg.querySelectorAll("[stroke]").forEach((n) => {
            if ((n.getAttribute("stroke") || "").toUpperCase() === oldColor.toUpperCase()) {
              n.setAttribute("stroke", newColor);
            }
          });
        });

        const g = document.createElementNS("http://www.w3.org/2000/svg", "g");
        const content = patternSvg.cloneNode(true);

        const scale = (patternData.size || this.patternSize) / 10;
        const angle = patternData.angle || this.patternAngle;
        const opacity = (patternData.opacity || this.patternOpacity) / 100;

        g.setAttribute("transform", `scale(${scale}) rotate(${angle} 0.5 0.5)`);
        g.setAttribute("opacity", opacity);

        content.setAttribute("width", bbox.width);
        content.setAttribute("height", bbox.height);
        content.setAttribute("preserveAspectRatio", "xMidYMid slice");

        g.appendChild(content);
        pattern.appendChild(g);
      }

      defs.appendChild(pattern);

      // overlay group
      let overlayGroup = mainSvg.querySelector("#pattern-overlay-group");
      if (!overlayGroup) {
        overlayGroup = document.createElementNS("http://www.w3.org/2000/svg", "g");
        overlayGroup.setAttribute("id", "pattern-overlay-group");
        mainSvg.appendChild(overlayGroup);
      }

      const oldOverlay = overlayGroup.querySelector(`#pattern-overlay-${partId}`);
      if (oldOverlay) oldOverlay.remove();

      const newOverlay = this.selectedSvgElement.cloneNode(true);
      newOverlay.setAttribute("id", `pattern-overlay-${partId}`);
      newOverlay.setAttribute("fill", `url(#${patternId})`);
      newOverlay.setAttribute("opacity", (patternData.opacity || this.patternOpacity) / 100);
      newOverlay.style.pointerEvents = "none";

      overlayGroup.appendChild(newOverlay);

      // save replacements
      this.patternsApplied[view][partId].replacements = { ...this.selectedPatternReplacements };
      this.saveCustomizations();

      // update preview if open
      if (this.isPreviewOpen) this.updateSinglePreviewView(this.currentView);
    },

    createDefs(svg) {
      const defs = document.createElementNS("http://www.w3.org/2000/svg", "defs");
      svg.insertBefore(defs, svg.firstChild);
      return defs;
    },

    updatePatternOpacity(value) {
      const opacity = value / 100;
      if (this.selectedSvgElement?.dataset?.patternId) {
        const pattern = document.querySelector(`#${this.selectedSvgElement.dataset.patternId}`);
        const g = pattern?.querySelector("g");
        if (g) g.setAttribute("opacity", opacity);
      }

      const data = this.patternsApplied?.[this.currentView]?.[this.selectedSvgElement?.id];
      if (data) {
        data.opacity = parseInt(value, 10);
        this.saveCustomizations();
      }
    },

    updatePatternSize(value) {
      if (!this.selectedSvgElement?.dataset?.patternId) return;

      const pattern = document.querySelector(`#${this.selectedSvgElement.dataset.patternId}`);
      const g = pattern?.querySelector("g");
      if (!g) return;

      this.selectedSvgElement.dataset.patternSize = String(value);

      const data = this.patternsApplied?.[this.currentView]?.[this.selectedSvgElement.id];
      if (data) {
        data.size = parseInt(value, 10);
        this.saveCustomizations();
      }

      const scale = value / 10;
      g.setAttribute("transform", `scale(${scale}) rotate(${this.patternAngle} 0.5 0.5)`);
    },

    // =========================
    // CIRCULAR KNOB ANGLE
    // =========================
    setupCircularSlider() {
      const circularSlider = document.getElementById("circularSlider");
      const knob = document.getElementById("knob");
      const progressCircle = document.getElementById("progressCircle");
      if (!circularSlider || !knob || !progressCircle) return;

      const radius = 65;
      const centerX = 70;
      const centerY = 70;
      const circumference = 2 * Math.PI * radius;

      const update = (angle) => {
        angle = ((angle % 360) + 360) % 360;
        this.patternAngle = angle;

        const rad = (angle - 90) * (Math.PI / 180);
        const x = centerX + radius * Math.cos(rad);
        const y = centerY + radius * Math.sin(rad);

        knob.style.left = x + "px";
        knob.style.top = y + "px";

        const offset = circumference * (1 - angle / 360);
        progressCircle.style.strokeDashoffset = offset;

        // apply to current pattern
        if (this.selectedSvgElement?.dataset?.patternId) {
          const pattern = document.querySelector(`#${this.selectedSvgElement.dataset.patternId}`);
          const g = pattern?.querySelector("g");
          if (g) {
            const scale = (this.patternSize || 50) / 10;
            g.setAttribute("transform", `scale(${scale}) rotate(${angle} 0.5 0.5)`);
          }

          // store angle
          const data = this.patternsApplied?.[this.currentView]?.[this.selectedSvgElement.id];
          if (data) {
            data.angle = angle;
            this.saveCustomizations();
          }
        }
      };

      const onMove = (clientX, clientY) => {
        const rect = circularSlider.getBoundingClientRect();
        const mx = clientX - rect.left - centerX;
        const my = clientY - rect.top - centerY;
        let angle = Math.atan2(my, mx) * 180 / Math.PI;
        angle = (angle + 90 + 360) % 360;
        update(angle);
      };

      knob.addEventListener("mousedown", (e) => {
        this.isDraggingKnob = true;
        e.preventDefault();
      });

      document.addEventListener("mousemove", (e) => {
        if (!this.isDraggingKnob) return;
        onMove(e.clientX, e.clientY);
      });

      document.addEventListener("mouseup", () => (this.isDraggingKnob = false));

      knob.addEventListener("touchstart", (e) => {
        this.isDraggingKnob = true;
        e.preventDefault();
      }, { passive: false });

      document.addEventListener("touchmove", (e) => {
        if (!this.isDraggingKnob) return;
        const t = e.touches[0];
        onMove(t.clientX, t.clientY);
      }, { passive: false });

      document.addEventListener("touchend", () => (this.isDraggingKnob = false));

      update(0);
    },

    // =========================
    // PAN / ZOOM (kept simple)
    // =========================
    togglePanZoom() {
      this.isPanZoomActive = !this.isPanZoomActive;

      const container = document.getElementById("modelDisplay");
      const inner =
        container?.querySelector('div[style*="position:relative"]') || container?.firstElementChild;

      if (!container || !inner) return;

      if (this.isPanZoomActive) {
        container.classList.add("panzoom-container", "zoomed");
        inner.classList.add("panzoom-element");
        this.applyTransform();

        container.addEventListener("wheel", this.handleWheel, { passive: false });
        container.addEventListener("mousedown", this.startDrag);
        container.addEventListener("mousemove", this.drag);
        container.addEventListener("mouseup", this.endDrag);
        container.addEventListener("mouseleave", this.endDrag);
      } else {
        container.classList.remove("panzoom-container", "zoomed");
        inner.classList.remove("panzoom-element");

        container.removeEventListener("wheel", this.handleWheel);
        container.removeEventListener("mousedown", this.startDrag);
        container.removeEventListener("mousemove", this.drag);
        container.removeEventListener("mouseup", this.endDrag);
        container.removeEventListener("mouseleave", this.endDrag);

        this.scale = 1;
        this.posX = 0;
        this.posY = 0;
        inner.style.transform = "none";
      }
    },

    applyTransform() {
      const inner = document.querySelector(".panzoom-element");
      if (inner) inner.style.transform = `translate(${this.posX}px, ${this.posY}px) scale(${this.scale})`;
    },

    handleWheel(e) {
      e.preventDefault();
      const delta = e.deltaY > 0 ? 0.9 : 1.1;
      this.scale *= delta;
      this.scale = Math.max(1, Math.min(this.scale, 10));
      this.applyTransform();
    },

    startDrag(e) {
      this.isDragging = true;
      this.startX = e.clientX - this.posX;
      this.startY = e.clientY - this.posY;
      document.body.style.cursor = "grabbing";
    },

    drag(e) {
      if (!this.isDragging) return;
      this.posX = e.clientX - this.startX;
      this.posY = e.clientY - this.startY;
      this.applyTransform();
    },

    endDrag() {
      this.isDragging = false;
      document.body.style.cursor = "default";
    },

    // =========================
    // PREVIEW PANEL
    // =========================
    openPreviewPanel() {
      this.isPreviewOpen = true;
      this.captureCurrentViewsForPreview();
    },

    closePreviewPanel() {
      this.isPreviewOpen = false;
    },

    onKeyDown(e) {
      if (e.key === "Escape" && this.isPreviewOpen) this.closePreviewPanel();
    },

    captureCurrentViewsForPreview() {
      ["front", "back", "left", "right"].forEach((view) => {
        this.updateSinglePreviewView(view);
      });
    },

    updateSinglePreviewView(view) {
      const id = `preview${view.charAt(0).toUpperCase() + view.slice(1)}`;
      const container = document.getElementById(id);
      if (!container) return;

      container.innerHTML = "";

      const wrapper = document.createElement("div");
      wrapper.style.width = "100%";
      wrapper.style.height = "100%";
      wrapper.style.position = "relative";

      // render svg + overlays (same as original preview logic)
      const v = this.modelViews[view];
      if (v?.svg_url) {
        fetch(v.svg_url)
          .then((r) => r.text())
          .then((svgText) => {
            const parser = new DOMParser();
            const svgDoc = parser.parseFromString(svgText, "image/svg+xml");
            const svgEl = svgDoc.querySelector("svg");

            if (svgEl) {
              svgEl.setAttribute("width", "100%");
              svgEl.setAttribute("height", "100%");
              svgEl.setAttribute("preserveAspectRatio", "xMidYMid meet");
              svgEl.style.position = "absolute";
              svgEl.style.zIndex = "1";
              svgEl.style.pointerEvents = "none";

              const els = svgEl.querySelectorAll("path, polygon, circle, rect, ellipse");
              els.forEach((el, idx) => {
                if (!el.id) el.id = `svg-part-${idx}`;
                if (this.colorChanges[view]?.[el.id]) el.setAttribute("fill", this.colorChanges[view][el.id]);
                el.style.pointerEvents = "none";
              });

              // apply patterns
              this.applyPatternsToSvg(svgEl, view);

              wrapper.appendChild(svgEl);
            }
          })
          .catch((err) => console.error("Preview SVG load error:", err));
      }

      // overlays
      if (v?.white_image_url) {
        const img = document.createElement("img");
        img.src = v.white_image_url + "?t=" + Date.now();
        img.style.position = "absolute";
        img.style.width = "100%";
        img.style.height = "100%";
        img.style.objectFit = "contain";
        img.style.mixBlendMode = "multiply";
        img.style.zIndex = "2";
        img.style.pointerEvents = "none";
        wrapper.appendChild(img);
      }

      if (v?.black_image_url) {
        const img = document.createElement("img");
        img.src = v.black_image_url + "?t=" + Date.now();
        img.style.position = "absolute";
        img.style.width = "100%";
        img.style.height = "100%";
        img.style.objectFit = "contain";
        img.style.mixBlendMode = "screen";
        img.style.zIndex = "3";
        img.style.pointerEvents = "none";
        wrapper.appendChild(img);
      }

      container.appendChild(wrapper);
    },
  },
};
</script>

<style scoped>
/* ✅ Aapka pura CSS same copy paste kar sakte ho (jo aapne <style> me diya) */
/* For brevity, yahan sirf required classes leave ki hain. Aap apna full CSS paste kar dein. */

* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: 'Poppins', sans-serif; background:#f5f5f5; color:#000; overflow:hidden; }

.customize-container{ display:flex; flex-direction:column; height:100vh; }

.header-bar{
  background:#2a2a2a; color:#fff; padding:12px 30px;
  display:flex; align-items:center; justify-content:space-between; height:60px;
}

.logo-img { height: 40px; width:auto; margin-left:20px; }

.main-content{ display:flex; flex:1; overflow:hidden; }

.model-view-area{
  flex:1; display:flex; flex-direction:column; background:#f5f5f5;
  padding:40px; overflow:auto; align-items:center; justify-content:center;
}

.model-display{
  width:100%; max-width:900px; height:100%;
  display:flex; align-items:center; justify-content:center;
  background:#fff; border-radius:8px; position:relative;
  box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

.model-view-container{ position:relative; width:100%; height:100%; display:flex; align-items:center; justify-content:center; padding:40px; }

.tools-bar{ width:500px; background:#fff; border-left:1px solid #e0e0e0; display:flex; flex-direction:column; overflow:hidden; }

.part-selection{ background:#e8e8e8; padding:12px 20px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #d0d0d0; }

.nav-arrow{
  background:transparent; border:none; font-size:24px; cursor:pointer; color:#666;
  width:30px; height:30px; display:flex; align-items:center; justify-content:center;
}
.nav-arrow:hover{ color:#000; }

.color-wheel-section{ margin-top:50px; flex:1; padding:10px 5px; display:flex; flex-direction:column; align-items:center; overflow-y:auto; }

.color-wheel-container{ position:relative; width:250px; height:250px; margin:0 auto; }
.color-wheel-outer{ width:100%; height:100%; border-radius:50%; position:relative; }
.color-wheel-ring{ width:100%; height:100%; border-radius:50%; position:relative; }

.color-wheel-white-ring{
  position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
  width:150px; height:150px; border-radius:50%; background:#fff; z-index:1;
  box-shadow:0 2px 4px rgba(0,0,0,0.1);
}

.color-wheel-center{
  position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
  width:120px; height:120px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  cursor:pointer; font-size:11px; font-weight:600; color:#fff;
  box-shadow:0 2px 8px rgba(0,0,0,0.3);
  transition:transform 0.2s; text-align:center; z-index:2;
}
.color-wheel-center:hover{ transform:translate(-50%,-50%) scale(1.05); }

.tab-row{ display:flex; justify-content:space-between; gap:6px; border:2px solid #000; }
.tab-btn{
  flex:1 1 auto; min-width:63px; max-width:120px;
  text-align:center; padding:6px 4px; cursor:pointer; color:#7e7e7e;
  font-weight:600; transition:all 0.3s;
  display:flex; flex-direction:column; align-items:center; justify-content:center;
}
.tab-btn.active{ background:#141414; color:#fff; }
.tab-btn.disabled{ opacity:0.4; pointer-events:none; }

.color-modal{
  position:fixed; top:0; left:0; width:100%; height:100%;
  background:rgba(0,0,0,0.6); display:flex; align-items:center; justify-content:center; z-index:1000;
}
.color-modal-content{
  background:#fff; border-radius:12px; width:420px; max-width:90%;
  box-shadow:0 10px 30px rgba(0,0,0,0.3); overflow:hidden;
}
.color-modal-header{
  padding:16px 20px; background:#2a2a2a; color:#fff;
  display:flex; justify-content:space-between; align-items:center;
}
.color-grid{ display:grid; grid-template-columns:repeat(6,1fr); gap:12px; padding:24px; }
.color-box{ width:50px; height:50px; border-radius:8px; cursor:pointer; transition:0.2s; border:2px solid transparent; }
.color-box:hover{ transform:scale(1.1); box-shadow:0 4px 12px rgba(0,0,0,0.3); border-color:#000; }

.selected{ stroke:#007bff; stroke-width:4; filter:brightness(1.3); }
.svg-hoverable:hover{ filter:brightness(1.2); cursor:pointer; }

.preview-card-horizontal{ background:white; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.15); width:22%; max-width:320px; }
.preview-container-horizontal{ width:100%; height:400px; background:#fafafa; position:relative; display:flex; align-items:center; justify-content:center; padding:15px; }
.preview-loading{ display:flex; flex-direction:column; align-items:center; justify-content:center; color:#999; font-size:13px; gap:10px; }
.preview-loading::before{ content:''; width:24px; height:24px; border:3px solid #e0e0e0; border-top-color:#007bff; border-radius:50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to{ transform:rotate(360deg); } }

.pattern-color-row{ display:flex; align-items:center; gap:10px; padding:8px; background:#f8f9fa; border-radius:8px; }
.original-color-box{ width:32px; height:32px; border-radius:6px; border:2px solid #aaa; }
.color-choices{ display:flex; gap:6px; flex-wrap:wrap; flex:1; }
.user-color-box{ width:28px; height:28px; border-radius:6px; border:2px solid #ddd; cursor:pointer; position:relative; }
.user-color-box.selected{ border:2px solid #007bff; box-shadow:0 0 0 3px rgba(0,123,255,0.2); }
.color-checkmark{ position:absolute; top:-6px; right:-6px; width:16px; height:16px; background:white; border-radius:50%; display:none; align-items:center; justify-content:center; font-size:12px; color:#28a745; box-shadow:0 1px 3px rgba(0,0,0,0.2); font-weight:bold; }
.user-color-box.selected .color-checkmark{ display:flex; }
</style>
