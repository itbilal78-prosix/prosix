(function () {
    'use strict';

    // =================== PATTERN LIBRARY ===================
    if (!window.patternsApplied || Array.isArray(window.patternsApplied)) {
        window.patternsApplied = {};
    }
    if (!window.mascotsApplied || Array.isArray(window.mascotsApplied)) {
        window.mascotsApplied = {};
    }

    let slider = document.getElementById("circularSlider");
    let angleValue = document.getElementById("angleValue");
    slider.addEventListener("mousemove", function (e) {

        if (e.buttons !== 1) return;

        let rect = slider.getBoundingClientRect();

        let centerX = rect.left + rect.width / 2;
        let centerY = rect.top + rect.height / 2;

        let angle = Math.atan2(
            e.clientY - centerY,
            e.clientX - centerX
        ) * (180 / Math.PI);

        angle = angle + 90;

        if (angle < 0) angle += 360;

        slider.style.background =
            `conic-gradient(#000 ${angle}deg, #e5e5e5 ${angle}deg)`;

        angleValue.value = Math.round(angle);

        updatePatternAngle(Math.round(angle));


        // ⭐ NEW CODE — rotate knob movement
        const knob = document.getElementById("rotateKnob");

        if (knob) {

            const radius = slider.offsetWidth / 2;

            const rad = (angle - 90) * Math.PI / 180;

            const x = radius + radius * Math.cos(rad);

            const y = radius + radius * Math.sin(rad);

            knob.style.left = x + "px";

            knob.style.top = y + "px";

        }

    });
    window.movePattern = function (axis, value) {

        if (!window.selectedSvgElement?.dataset.patternId) return;

        const view = window.currentView;
        const partId = window.selectedSvgElement.id;

        const patternData = window.patternsApplied[view]?.[partId];
        if (!patternData) return;

        const pattern = document.querySelector(
            `#${window.selectedSvgElement.dataset.patternId}`
        );

        if (!pattern) return;

        let offsetX = patternData.offsetX || 0;
        let offsetY = patternData.offsetY || 0;

        if (axis === "x") offsetX = value;
        if (axis === "y") offsetY = value;

        const bbox = patternData.bbox;

        const cx = bbox.x + bbox.width / 2;
        const cy = bbox.y + bbox.height / 2;

        pattern.setAttribute(
            "patternTransform",
            `translate(${offsetX} ${offsetY}) rotate(${patternData.angle || 0} ${cx} ${cy})`
        );

        patternData.offsetX = offsetX;
        patternData.offsetY = offsetY;

        if (window.saveCustomizations)
            window.saveCustomizations();

    };
    window.openPatternLibrary = function () {
        document.getElementById('patternLibraryModal').style.display = 'flex';
        loadPatternsFromDB();
    };

    window.closePatternLibrary = function () {
        document.getElementById('patternLibraryModal').style.display = 'none';
    };

    function loadPatternsFromDB() {
        fetch('/api/patterns')
            .then(res => res.json())
            .then(patterns => {
                const container = document.getElementById('patternList');
                container.innerHTML = '';




                // ===== BLANK TILE (ALWAYS FIRST) =====
                const blank = document.createElement('div');
                blank.style.border = '2px dashed #999';
                blank.style.borderRadius = '8px';
                blank.style.padding = '10px';
                blank.style.cursor = 'pointer';
                blank.style.textAlign = 'center';
                blank.style.background = '#fafafa';

                blank.innerHTML = `
    <div style="height:120px;display:flex;align-items:center;justify-content:center;font-weight:700;">
        BLANK
    </div>
`;

                blank.onclick = () => {
                    clearPatternForSelectedPart();
                    closePatternLibrary();
                };

                container.appendChild(blank);
                // ===================================


                patterns.forEach(pattern => {
                    const div = document.createElement('div');
                    div.style.border = '2px solid #ddd';
                    div.style.borderRadius = '8px';
                    div.style.padding = '10px';
                    div.style.cursor = 'pointer';
                    div.style.textAlign = 'center';
                    div.style.background = '#fff';
                    div.style.transition = 'all 0.3s';

                    div.innerHTML = `
                    <div style="height:120px; display:flex; align-items:center; justify-content:center;">
                        <img src="${pattern.svg_url}" style="max-width:100%; max-height:100%;">
                    </div>
                    <p style="margin-top:8px; font-weight:600;">${pattern.name}</p>
                `;

                    div.onmouseover = () => div.style.borderColor = '#007bff';
                    div.onmouseout = () => div.style.borderColor = '#ddd';
                    div.onclick = () => applyDBPattern(pattern.svg_url);

                    container.appendChild(div);
                });
            })
            .catch(err => {
                console.error('Error loading patterns:', err);
                document.getElementById('patternList').innerHTML =
                    '<p style="color:#999; text-align:center; padding:20px;">Error loading patterns</p>';
            });
    }



    window.clearPatternForSelectedPart = function () {




// TEXT PATTERN REMOVE SUPPORT
if (window.currentApplicationLayer && window.selectingPatternForText) {

    const layer = findLayerById(window.currentApplicationLayer);
    if (!layer) return;

    const textEl = document.getElementById(layer.id);
    if (!textEl) return;

    const mainSvg = window.getMainSvg();

    if (layer.patternId) {
        const pattern = mainSvg.querySelector(`#${layer.patternId}`);
        if (pattern) pattern.remove();
    }

    if (layer.baseColor) {
        textEl.setAttribute('fill', layer.baseColor);
    } else {
        textEl.setAttribute('fill', '#ffffff');
    }

    delete layer.hasPattern;
    delete layer.patternId;
    delete layer.patternSvg;

    window.selectingPatternForText = false;

    if (window.saveCustomizations)
        window.saveCustomizations();

    return;
}


        if (!window.selectedSvgElement) return;

        const view = window.currentView;
        const partId = window.selectedSvgElement.id;
        const mainSvg = window.getMainSvg();

        // overlay remove
        mainSvg.querySelector(`#pattern-overlay-${partId}`)?.remove();

        // defs remove
        mainSvg.querySelector(`#pattern-${partId}-${view}`)?.remove();
        mainSvg.querySelector(`#clip-${partId}-${view}`)?.remove();

        // state clear
        if (window.patternsApplied?.[view]?.[partId]) {
            delete window.patternsApplied[view][partId];
        }

        delete window.selectedSvgElement.dataset.hasPattern;
        delete window.selectedSvgElement.dataset.patternId;

        // UI reset
        document.getElementById('patternControls').style.display = 'none';
        document.getElementById('patternPreviewBox').innerHTML =
            '<span style="color:#999;">No pattern applied</span>';

        window.uploadedSvgContent = null;

        if (window.saveCustomizations) window.saveCustomizations();
        // show SELECT PATTERN button again
        const btns = document.querySelector('.pattern-top-buttons');
        if (btns) btns.style.display = 'flex';

        // disable preview click
        const preview = document.getElementById('patternPreviewBox');
        if (preview) {
            preview.style.cursor = 'default';
            preview.onclick = null;
        }
        console.log("✅ BLANK clicked → SVG cleaned");
    };



    window.applyDBPattern = function (svgUrl) {
        if (!window.selectedSvgElement) {
            alert("Please select a part first!");
            return;
        }

        fetch(svgUrl)
            .then(res => res.text())
            .then(svgContent => {
                window.uploadedSvgContent = svgContent;
                applyUploadedPattern();
                closePatternLibrary();
            })
            .catch(err => {
                console.error('Error loading pattern:', err);
                alert('Failed to load pattern');
            });
    };

   // ============================================================
// ✅ PATTERN.JS FIXES
// Yeh 3 functions customizer-pattern.js mein replace karo
// ============================================================


// ============================================================
// FIX 1: applyUploadedPattern — POORA REPLACE KARO
// (end mein bringApplicationLayersToTop add kiya)
// ============================================================

window.applyUploadedPattern = function () {
    if (window.selectingPatternForText && window.currentApplicationLayer) {
        applyPatternToText(window.uploadedSvgContent);
        window.selectingPatternForText = false;
        return;
    }

    if (!window.selectedSvgElement) {
        alert("Please select a part first!");
        return;
    }

    if (!window.uploadedSvgContent) {
        alert("Please select a pattern first!");
        return;
    }

    const targetPart = window.selectedSvgElement;
    const partId = targetPart.id;
    const view = window.currentView;

    if (!partId) {
        alert("Selected part has no ID!");
        return;
    }

    if (window.saveToHistory) window.saveToHistory();

    const mainSvg = window.getMainSvg();
    if (!mainSvg) return;

    let defs = mainSvg.querySelector('defs');
    if (!defs) {
        defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        mainSvg.insertBefore(defs, mainSvg.firstChild);
    }

    // Old pattern remove karo sirf is part ka
    const oldPatternId = `pattern-${partId}-${view}`;
    const oldClipId = `clip-${partId}-${view}`;
    const oldPattern = defs.querySelector(`#${oldPatternId}`);
    if (oldPattern) oldPattern.remove();
    const oldClip = defs.querySelector(`#${oldClipId}`);
    if (oldClip) oldClip.remove();

    const oldOverlayId = `pattern-overlay-${partId}`;
    const overlayGroup = mainSvg.querySelector('#pattern-overlay-group');
    if (overlayGroup) {
        const oldOverlay = overlayGroup.querySelector(`#${oldOverlayId}`);
        if (oldOverlay) oldOverlay.remove();
    }

    // Parse pattern SVG
    const parser = new DOMParser();
    const doc = parser.parseFromString(window.uploadedSvgContent, 'image/svg+xml');
    let patternSvg = doc.documentElement;
    if (!patternSvg || patternSvg.nodeName !== 'svg') {
        alert("Pattern SVG load failed");
        return;
    }
    patternSvg = patternSvg.cloneNode(true);
    if (!patternSvg.hasAttribute('viewBox')) {
        patternSvg.setAttribute('viewBox', '0 0 100 100');
    }

    // Clip path banao
    const clipId = `clip-${partId}-${view}`;
    const clipPath = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
    clipPath.setAttribute('id', clipId);
    const clonedPart = targetPart.cloneNode(true);
    clonedPart.removeAttribute('id');
    clipPath.appendChild(clonedPart);
    defs.appendChild(clipPath);

    // BBox
    const rawBbox = targetPart.getBBox();
    const bbox = {
        x: rawBbox.x,
        y: rawBbox.y,
        width: rawBbox.width,
        height: rawBbox.height
    };

    // Pattern banao
    const patternId = `pattern-${partId}-${view}`;
    const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
    pattern.setAttribute('id', patternId);
    pattern.setAttribute('patternUnits', 'userSpaceOnUse');
    pattern.setAttribute('x', bbox.x);
    pattern.setAttribute('y', bbox.y);
    pattern.setAttribute('width', bbox.width);
    pattern.setAttribute('height', bbox.height);
    patternSvg.setAttribute('width', bbox.width);
    patternSvg.setAttribute('height', bbox.height);
    patternSvg.setAttribute('preserveAspectRatio', 'xMidYMid slice');
    pattern.appendChild(patternSvg);
    defs.appendChild(pattern);

    // Overlay group — pattern-overlay-group ko application groups se PEHLE banana hai
    let og = mainSvg.querySelector('#pattern-overlay-group');
    if (!og) {
        og = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        og.setAttribute('id', 'pattern-overlay-group');
        // ✅ Application groups se pehle insert karo
        const firstAppGroup = mainSvg.querySelector('[id^="app-group-"]');
        if (firstAppGroup) {
            mainSvg.insertBefore(og, firstAppGroup);
        } else {
            mainSvg.appendChild(og);
        }
    } else {
        // ✅ Agar pehle se hai to application groups se pehle move karo
        const firstAppGroup = mainSvg.querySelector('[id^="app-group-"]');
        if (firstAppGroup && og.nextSibling !== firstAppGroup) {
            mainSvg.insertBefore(og, firstAppGroup);
        }
    }

    // Overlay banao
    const overlay = targetPart.cloneNode(true);
    overlay.setAttribute('id', `pattern-overlay-${partId}`);
    overlay.setAttribute('fill', `url(#${patternId})`);
    overlay.setAttribute('clip-path', `url(#${clipId})`);
    overlay.style.pointerEvents = 'none';
    og.appendChild(overlay);

    // State save karo
    if (!window.patternsApplied) window.patternsApplied = {};
    if (!window.patternsApplied[view]) window.patternsApplied[view] = {};
    window.patternsApplied[view][partId] = {
        patternId,
        svgContent: window.uploadedSvgContent,
        bbox: bbox,
        size: 50,
        opacity: 100,
        angle: 0,
        replacements: {}
    };

    targetPart.dataset.hasPattern = 'true';
    targetPart.dataset.patternId = patternId;
    targetPart.dataset.patternView = view;

    if (window.saveCustomizations) window.saveCustomizations();

    document.getElementById('patternControls').style.display = 'block';

    const btns = document.querySelector('.pattern-top-buttons');
    if (btns) btns.style.display = 'none';

    const preview = document.getElementById('patternPreviewBox');
    if (preview) {
        preview.style.cursor = 'pointer';
        preview.onclick = openPatternLibrary;
    }

    if (window.updatePatternPreview) window.updatePatternPreview();
    if (window.updatePatternColorPalette) window.updatePatternColorPalette();
    if (window.updateUndoRedoButtons) window.updateUndoRedoButtons();

    // ✅ KEY FIX: Application layers wapas top pe lao
    setTimeout(() => {
        if (window.bringApplicationLayersToTop) {
            window.bringApplicationLayersToTop();
        }
    }, 50);
};




window.getMascotUniqueColors = function (svgContent) {

    if (!svgContent) return ['#000000'];

    const parser = new DOMParser();
    const doc = parser.parseFromString(svgContent, 'image/svg+xml');

    const elements = doc.querySelectorAll('[fill], [stroke]');

    const uniqueColors = new Set();

    elements.forEach(el => {

        const fill = el.getAttribute('fill');

        if (fill && fill !== 'none' && !fill.startsWith('url')) {
            uniqueColors.add(fill.toUpperCase());
        }

        const stroke = el.getAttribute('stroke');

        if (stroke && stroke !== 'none' && !stroke.startsWith('url')) {
            uniqueColors.add(stroke.toUpperCase());
        }

    });

    return Array.from(uniqueColors);
};

    // =================== PATTERN COLOR PALETTE ===================

    window.updatePatternColorPalette = function () {
        const container = document.getElementById('patternColorPalette');
        if (!container) return;

        container.innerHTML = '';

        if (!window.selectedSvgElement?.dataset.hasPattern) return;

        const view = window.currentView;
        const partId = window.selectedSvgElement.id;
        const patternData = window.patternsApplied[view]?.[partId];

        if (!patternData || !patternData.svgContent) {
            console.warn(`No pattern data found for ${partId} in ${view}`);
            return;
        }

        const patternColors = getPatternUniqueColors(patternData.svgContent);
        const userColors = window.selectedColors?.length > 0 ? [...window.selectedColors] : ['#000000'];

        patternColors.forEach((patternColor) => {
            const row = document.createElement('div');
            row.className = 'pattern-color-row';

            const originalBox = document.createElement('div');
            originalBox.className = 'original-color-box';
            originalBox.style.backgroundColor = patternColor;

            const arrow = document.createElement('div');
            arrow.className = 'color-arrow';
            arrow.textContent = '→';

            const choicesContainer = document.createElement('div');
            choicesContainer.className = 'color-choices';

            userColors.forEach((userColor) => {
                const box = document.createElement('div');
                box.className = 'user-color-box';
                box.style.backgroundColor = userColor;
                box.dataset.userColor = userColor;
                box.dataset.patternColor = patternColor;

                const check = document.createElement('div');
                check.className = 'color-checkmark';
                check.textContent = '✓';
                box.appendChild(check);

                box.onclick = function () {
                    this.parentElement.querySelectorAll('.user-color-box').forEach(b => {
                        b.classList.remove('selected');
                    });

                    this.classList.add('selected');

                    if (!window.patternsApplied[view][partId].replacements) {
                        window.patternsApplied[view][partId].replacements = {};
                    }
                    window.patternsApplied[view][partId].replacements[patternColor.toUpperCase()] = userColor;

                    recreatePatternAndOverlayWithNewColors();
                };

                const currentReplacements = patternData.replacements || {};
                if (currentReplacements[patternColor.toUpperCase()] === userColor) {
                    box.classList.add('selected');
                }

                choicesContainer.appendChild(box);
            });

            row.appendChild(originalBox);
            row.appendChild(arrow);
            row.appendChild(choicesContainer);
            container.appendChild(row);
        });

        container.style.display = 'flex';
    }

    window.getPatternUniqueColors = function (svgContent) {
        if (!svgContent) return ['#000000'];

        const parser = new DOMParser();
        const doc = parser.parseFromString(svgContent, 'image/svg+xml');
        const elements = doc.querySelectorAll('[fill], [stroke]');

        const uniqueColors = new Set();
        elements.forEach(el => {
            const fill = el.getAttribute('fill');
            if (fill && fill !== 'none' && !fill.startsWith('url')) {
                uniqueColors.add(fill.toUpperCase());
            }
            const stroke = el.getAttribute('stroke');
            if (stroke && stroke !== 'none' && !stroke.startsWith('url')) {
                uniqueColors.add(stroke.toUpperCase());
            }
        });

        return Array.from(uniqueColors);
    }

   // ============================================================
// FIX 3: recreatePatternAndOverlayWithNewColors — POORA REPLACE KARO
// (end mein bringApplicationLayersToTop add kiya)
// ============================================================

window.recreatePatternAndOverlayWithNewColors = function () {
    if (!window.selectedSvgElement?.dataset.hasPattern) return;

    const view = window.currentView;
    const partId = window.selectedSvgElement.id;
    const patternData = window.patternsApplied[view]?.[partId];

    if (!patternData || !patternData.svgContent) {
        console.warn(`No pattern data for ${partId} in ${view}`);
        return;
    }

    const mainSvg = window.getMainSvg();
    let defs = mainSvg.querySelector('defs');
    if (!defs) {
        defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        mainSvg.insertBefore(defs, mainSvg.firstChild);
    }

    const patternId = patternData.patternId;
    const oldPattern = defs.querySelector(`#${patternId}`);
    if (oldPattern) oldPattern.remove();

    const bbox = patternData.bbox || window.selectedSvgElement.getBBox();

    const pattern = document.createElementNS("http://www.w3.org/2000/svg", "pattern");
    pattern.setAttribute("id", patternId);
    pattern.setAttribute("patternUnits", "userSpaceOnUse");
    pattern.setAttribute("x", bbox.x);
    pattern.setAttribute("y", bbox.y);
    pattern.setAttribute("width", bbox.width);
    pattern.setAttribute("height", bbox.height);

    const parser = new DOMParser();
    const doc = parser.parseFromString(patternData.svgContent, "image/svg+xml");
    const patternSvg = doc.querySelector("svg");

    if (patternSvg) {
        const replacements = patternData.replacements || {};
        Object.entries(replacements).forEach(([oldColor, newColor]) => {
            patternSvg.querySelectorAll("[fill]").forEach(el => {
                if (el.getAttribute("fill")?.toUpperCase() === oldColor.toUpperCase()) {
                    el.setAttribute("fill", newColor);
                }
            });
            patternSvg.querySelectorAll("[stroke]").forEach(el => {
                if (el.getAttribute("stroke")?.toUpperCase() === oldColor.toUpperCase()) {
                    el.setAttribute("stroke", newColor);
                }
            });
        });

        const g = document.createElementNS("http://www.w3.org/2000/svg", "g");
        const content = patternSvg.cloneNode(true);
        const scale = (patternData.size || 50) / 50;
        const angle = patternData.angle || 0;
        const opacity = (patternData.opacity || 100) / 100;

        g.setAttribute("transform", `scale(${scale}) rotate(${angle})`);
        g.setAttribute("opacity", opacity);
        content.setAttribute("width", bbox.width);
        content.setAttribute("height", bbox.height);
        content.setAttribute("preserveAspectRatio", "xMidYMid slice");
        g.appendChild(content);
        pattern.appendChild(g);
    }

    defs.appendChild(pattern);

    // Overlay group
    let overlayGroup = mainSvg.querySelector("#pattern-overlay-group");
    if (!overlayGroup) {
        overlayGroup = document.createElementNS("http://www.w3.org/2000/svg", "g");
        overlayGroup.setAttribute("id", "pattern-overlay-group");
        // Application groups se pehle insert karo
        const firstAppGroup = mainSvg.querySelector('[id^="app-group-"]');
        if (firstAppGroup) {
            mainSvg.insertBefore(overlayGroup, firstAppGroup);
        } else {
            mainSvg.appendChild(overlayGroup);
        }
    }

    const oldOverlay = overlayGroup.querySelector(`#pattern-overlay-${partId}`);
    if (oldOverlay) oldOverlay.remove();

    const clipId = `clip-${partId}-${view}`;
    const newOverlay = window.selectedSvgElement.cloneNode(true);
    newOverlay.setAttribute("id", `pattern-overlay-${partId}`);
    newOverlay.setAttribute("fill", `url(#${patternId})`);
    newOverlay.setAttribute("clip-path", `url(#${clipId})`);
    newOverlay.setAttribute("opacity", (patternData.opacity || 100) / 100);
    newOverlay.style.pointerEvents = "none";
    overlayGroup.appendChild(newOverlay);

    if (window.saveCustomizations) window.saveCustomizations();

    // ✅ KEY FIX: Application layers wapas top pe lao
    setTimeout(() => {
        if (window.bringApplicationLayersToTop) {
            window.bringApplicationLayersToTop();
        }
    }, 50);

    console.log(`✅ Pattern colors updated for ${partId}`);
};

    // =================== PATTERN CONTROLS ===================
    window.updatePatternSize = function (value) {
        if (!window.selectedSvgElement?.dataset.patternId) return;

        const view = window.currentView;
        const partId = window.selectedSvgElement.id;
        const patternData = window.patternsApplied[view]?.[partId];

        if (!patternData) return;

        const pid = window.selectedSvgElement.dataset.patternId;
        const pattern = document.querySelector(`#${pid}`);
        if (!pattern) return;

        let g = pattern.querySelector('g');
        if (!g) {
            g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            while (pattern.firstChild) {
                g.appendChild(pattern.firstChild);
            }
            pattern.appendChild(g);
        }

        const minSize = 50;
        const safeValue = Math.max(value, minSize);
        const scale = safeValue / 50;

        const angle = patternData.angle || 0;
        const opacity = (patternData.opacity || 100) / 100;

        g.setAttribute("transform", `scale(${scale}) rotate(${angle})`);
        g.setAttribute("opacity", opacity);

        window.patternsApplied[view][partId].size = safeValue;
        if (window.saveCustomizations) window.saveCustomizations();

        const slider = document.getElementById('patternSize');
        if (slider) slider.value = safeValue;
        const sizeValue = document.getElementById('sizeValue');
        if (sizeValue) sizeValue.textContent = safeValue;

        console.log(`Pattern size updated: ${safeValue}`);
    };

    window.updatePatternOpacity = function (value) {
        const opacity = value / 100;
        const opacityValue = document.getElementById('opacityValue');
        if (opacityValue) opacityValue.textContent = value + '%';

        if (!window.selectedSvgElement?.dataset.patternId) return;

        const view = window.currentView;
        const partId = window.selectedSvgElement.id;
        const pid = window.selectedSvgElement.dataset.patternId;

        const pattern = document.querySelector(`#${pid}`);
        if (pattern) {
            const g = pattern.querySelector('g');
            if (g) {
                g.setAttribute('opacity', opacity);
            }
        }

        const overlay = document.querySelector(`#pattern-overlay-${partId}`);
        if (overlay) {
            overlay.setAttribute('opacity', opacity);
        }

        if (window.patternsApplied[view]?.[partId]) {
            window.patternsApplied[view][partId].opacity = parseInt(value);
            if (window.saveCustomizations) window.saveCustomizations();
        }
    };

    window.updatePatternPreview = function () {
        const previewBox = document.getElementById('patternPreviewBox');
        if (!previewBox) return;

        const view = window.currentView;
        const partId = window.selectedSvgElement?.id;

        if (window.selectedSvgElement?.dataset.hasPattern && partId) {
            const patternData = window.patternsApplied[view]?.[partId];

            if (patternData && patternData.svgContent) {
                previewBox.innerHTML = `
<div style="
width:100%;
height:100%;
background-image:url('data:image/svg+xml;utf8,${encodeURIComponent(patternData.svgContent)}');
background-size:cover;
background-position:center;
background-repeat:no-repeat;
">
</div>
`;

                const svg = previewBox.querySelector('svg');
                if (svg) {
                    svg.style.width = '100%';
                    svg.style.height = '100%';
                    svg.style.objectFit = 'cover';
                    svg.style.display = 'block';
                }
            } else {
                previewBox.innerHTML = '<span style="color:#999;">No pattern data</span>';
            }
        } else {
            previewBox.innerHTML = '<span style="color:#999;">No pattern applied</span>';
        }
    }

    window.removePatternFromPart = function () {
        if (!window.selectedSvgElement) {
            alert("Please select a part first!");
            return;
        }

        if (window.saveToHistory) window.saveToHistory();

        const view = window.currentView;
        const partId = window.selectedSvgElement.id;

        const originalColor = window.originalColors?.[view]?.[partId] || '#ffffff';
        window.selectedSvgElement.setAttribute('fill', originalColor);
        window.selectedSvgElement.style.opacity = '1';

        const mainSvg = window.getMainSvg();
        const overlayGroup = mainSvg.querySelector('#pattern-overlay-group');
        if (overlayGroup) {
            const overlay = overlayGroup.querySelector(`#pattern-overlay-${partId}`);
            if (overlay) overlay.remove();
        }

        const defs = mainSvg.querySelector('defs');
        if (defs) {
            const pattern = defs.querySelector(`#pattern-${partId}-${view}`);
            if (pattern) pattern.remove();

            const clip = defs.querySelector(`#clip-${partId}-${view}`);
            if (clip) clip.remove();
        }

        delete window.selectedSvgElement.dataset.hasPattern;
        delete window.selectedSvgElement.dataset.patternId;
        delete window.selectedSvgElement.dataset.patternSize;
        delete window.selectedSvgElement.dataset.patternOpacity;
        delete window.selectedSvgElement.dataset.patternAngle;
        delete window.selectedSvgElement.dataset.patternView;

        if (window.patternsApplied?.[view]?.[partId]) {
            delete window.patternsApplied[view][partId];
            if (window.saveCustomizations) window.saveCustomizations();
        }

        document.getElementById('patternControls').style.display = 'none';
        window.uploadedSvgContent = null;

        alert("Pattern removed!");
        if (window.updateUndoRedoButtons) window.updateUndoRedoButtons();
    };

    // =================== APPLY PATTERNS TO SVG (FOR SAVE/PREVIEW) ===================

   // ============================================================
// FIX 4: applyPatternsToSvg — POORA REPLACE KARO
// (save/preview ke liye — application layers ke baad call hoga)
// ============================================================

window.applyPatternsToSvg = function (svg, view, isPreview = false) {
    if (!window.patternsApplied?.[view] || Object.keys(window.patternsApplied[view]).length === 0) {
        return;
    }
    if (window.applyMascotsToSvg) {
        window.applyMascotsToSvg(svg, view);
    }

    let defs = svg.querySelector('defs');
    if (!defs) {
        defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        svg.insertBefore(defs, svg.firstChild);
    }

    const groupId = isPreview ? 'overlay-preview' : 'pattern-overlay-group';
    let group = svg.querySelector(`#${groupId}`);
    if (!group) {
        group = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        group.setAttribute('id', groupId);
        // ✅ Application groups se PEHLE insert karo
        const firstAppGroup = svg.querySelector('[id^="app-group-"]');
        if (firstAppGroup) {
            svg.insertBefore(group, firstAppGroup);
        } else {
            svg.appendChild(group);
        }
    }

    Object.entries(window.patternsApplied[view]).forEach(([partId, data]) => {
        const base = svg.querySelector(`#${partId}`);
        if (!base) return;

        const pid = data.patternId + (isPreview ? '-preview' : '');
        const clipId = `clip-${partId}-${view}` + (isPreview ? '-preview' : '');

        const oldPattern = defs.querySelector(`#${pid}`);
        if (oldPattern) oldPattern.remove();
        const oldClip = defs.querySelector(`#${clipId}`);
        if (oldClip) oldClip.remove();

        const clipPath = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
        clipPath.setAttribute('id', clipId);
        const clonedBase = base.cloneNode(true);
        clonedBase.removeAttribute('id');
        clipPath.appendChild(clonedBase);
        defs.appendChild(clipPath);

        const bbox = data.bbox || base.getBBox();

        const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
        pattern.setAttribute('id', pid);
        pattern.setAttribute('patternUnits', 'userSpaceOnUse');
        pattern.setAttribute('x', bbox.x);
        pattern.setAttribute('y', bbox.y);
        pattern.setAttribute('width', bbox.width);
        pattern.setAttribute('height', bbox.height);
        pattern.setAttribute('preserveAspectRatio', 'xMidYMid slice');

        const doc = new DOMParser().parseFromString(data.svgContent, 'image/svg+xml');
        const psvg = doc.querySelector('svg');

        if (psvg) {
            if (data.replacements) {
                Object.entries(data.replacements).forEach(([oldColor, newColor]) => {
                    psvg.querySelectorAll('[fill]').forEach(e => {
                        if (e.getAttribute('fill')?.toUpperCase() === oldColor.toUpperCase()) {
                            e.setAttribute('fill', newColor);
                        }
                    });
                    psvg.querySelectorAll('[stroke]').forEach(e => {
                        if (e.getAttribute('stroke')?.toUpperCase() === oldColor.toUpperCase()) {
                            e.setAttribute('stroke', newColor);
                        }
                    });
                });
            }

            if (!psvg.hasAttribute('viewBox')) {
                psvg.setAttribute('viewBox', '0 0 100 100');
            }

            const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            const userSize = data.size || 50;
            const scale = userSize / 50;
            const angle = data.angle || 0;
            const opacity = (data.opacity || 100) / 100;

            psvg.setAttribute('width', bbox.width);
            psvg.setAttribute('height', bbox.height);
            psvg.setAttribute('preserveAspectRatio', 'xMidYMid slice');
            g.setAttribute('transform', `scale(${scale}) rotate(${angle})`);
            g.setAttribute('opacity', opacity);
            g.appendChild(psvg.cloneNode(true));
            pattern.appendChild(g);
        }

        defs.appendChild(pattern);

        const oldOverlay = group.querySelector(`#pattern-overlay-${partId}` + (isPreview ? '-preview' : ''));
        if (oldOverlay) oldOverlay.remove();

        const over = base.cloneNode(true);
        over.setAttribute('id', `pattern-overlay-${partId}` + (isPreview ? '-preview' : ''));
        over.setAttribute('fill', `url(#${pid})`);
        over.setAttribute('clip-path', `url(#${clipId})`);
        over.style.pointerEvents = 'none';
        group.appendChild(over);

        base.dataset.hasPattern = "true";
        base.dataset.patternId = pid;
        base.dataset.patternSize = data.size || 50;
        base.dataset.patternOpacity = data.opacity || 100;
        base.dataset.patternAngle = data.angle || 0;
        base.dataset.patternView = view;
    });
};

    // =================== MASCOT TEMPLATE ===================

    window.openMascotTemplateModal = function () {
        // ⭐ selectedSvgElement check HATA DIYA - seedha modal open hoga
        document.getElementById('mascotTemplateModal').style.display = 'flex';

        fetch('/api/mascot-templates')
            .then(r => r.json())
            .then(data => {
                let html = '';

                html += `
            <div style="border:2px dashed #999;padding:10px;border-radius:8px;text-align:center;cursor:pointer;background:#fafafa"
                onclick="clearMascotForSelectedPart(); closeMascotTemplateModal();">
                <div style="height:140px;display:flex;align-items:center;justify-content:center;font-weight:700;">
                    BLANK
                </div>
            </div>`;

                data.forEach(t => {
                    html += `
                <div style="border:1px solid #ddd;padding:10px;border-radius:8px;text-align:center;cursor:pointer"
                     onclick="selectMascotTemplate('${t.image_data}','${encodeURIComponent(t.svg_data || '')}')">
                    <img src="${t.image_data}" style="width:100%;height:140px;object-fit:contain;background:#f5f5f5">
                    <p style="margin-top:8px;font-weight:600">${t.title}</p>
                </div>`;
                });

                document.getElementById('mascotTemplateGrid').innerHTML = html;
            })
            .catch(err => {
                console.error('Error:', err);
                document.getElementById('mascotTemplateGrid').innerHTML =
                    '<p style="color:red;">Error loading mascots</p>';
            });
    };

    window.clearMascotForSelectedPart = function () {


if (window.currentApplicationLayer) {

    const layer = findLayerById(window.currentApplicationLayer);

    if (layer && layer.hasMascot) {

        const mainSvg = window.getMainSvg();

        const textEl = document.getElementById(layer.id);

        if (textEl) {

            textEl.removeAttribute('fill');

        }

        if (layer.mascotId) {

            const pattern = mainSvg.querySelector(`#${layer.mascotId}`);

            if (pattern) pattern.remove();

        }

        delete layer.hasMascot;
        delete layer.mascotSvg;
        delete layer.mascotId;

        if (window.saveCustomizations)
            window.saveCustomizations();

        return;
    }
}



        if (!window.selectedSvgElement) return;

        const view = window.currentView;
        const partId = window.selectedSvgElement.id;
        const svg = window.getMainSvg();

        svg.querySelector(`#mascot-overlay-${partId}`)?.remove();
        svg.querySelector(`#mascot-pattern-${partId}-${view}`)?.remove();
        svg.querySelector(`#mascot-clip-${partId}-${view}`)?.remove();

        if (window.mascotsApplied?.[view]?.[partId]) {
            delete window.mascotsApplied[view][partId];
        }

        delete window.selectedSvgElement.dataset.hasMascot;
        delete window.selectedSvgElement.dataset.mascotId;

        // ⭐ BUTTONS WAPAS SHOW KAR DO
        const btns = document.querySelector('.pattern-top-buttons');
        if (btns) btns.style.display = 'flex';

        document.getElementById('mascotControls').style.display = 'none';

        if (window.saveCustomizations) window.saveCustomizations();
        console.log("✅ Mascot cleared");
    };

    window.closeMascotTemplateModal = function () {
        document.getElementById('mascotTemplateModal').style.display = 'none';
    };

    // ⭐ YEH FUNCTION MASCOT KO REPEAT KAREGA ⭐
    window.selectMascotTemplate = function (img, svg) {


if (window.selectingMascotForText && window.currentApplicationLayer) {

    applyMascotToText(decodeURIComponent(svg));

    window.selectingMascotForText = false;

    closeMascotTemplateModal();

    return;
}


        if (!window.selectedSvgElement) {
            closeMascotTemplateModal();
            alert("First select a part of the jersey, then click SELECT MASCOT again!");
            return;
        }

        const decodedSvg = svg ? decodeURIComponent(svg) : null;

        if (!decodedSvg || !decodedSvg.includes('<svg')) {
            fetch(img)
                .then(r => r.text())
                .then(content => {
                    if (content.includes('<svg')) {
                        window.uploadedSvgContent = content;
                        window.applyUploadedMascot();
                        closeMascotTemplateModal();
                        const btns = document.querySelector('.pattern-top-buttons');
                        if (btns) btns.style.display = 'none';
                    }
                });
            return;
        }

        window.uploadedSvgContent = decodedSvg;
        window.applyUploadedMascot();
        closeMascotTemplateModal();
        const btns = document.querySelector('.pattern-top-buttons');
        if (btns) btns.style.display = 'none';
    };

   // ============================================================
// FIX 2: applyUploadedMascot — POORA REPLACE KARO
// (end mein bringApplicationLayersToTop add kiya)
// ============================================================

window.applyUploadedMascot = function () {
    if (!window.selectedSvgElement) {
        alert("Please select a part first!");
        return;
    }

    if (!window.uploadedSvgContent) {
        alert("Please select a mascot first!");
        return;
    }

    const targetPart = window.selectedSvgElement;
    const partId = targetPart.id;
    const view = window.currentView;

    const mainSvg = window.getMainSvg();

    // Old mascot remove karo sirf is part ka
    mainSvg.querySelector(`#mascot-overlay-${partId}`)?.remove();
    mainSvg.querySelector(`#mascot-pattern-${partId}-${view}`)?.remove();
    mainSvg.querySelector(`#mascot-clip-${partId}-${view}`)?.remove();

    if (!partId) {
        alert("Selected part has no ID!");
        return;
    }

    if (window.saveToHistory) window.saveToHistory();

    let defs = mainSvg.querySelector('defs');
    if (!defs) {
        defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        mainSvg.insertBefore(defs, mainSvg.firstChild);
    }

    // Parse mascot SVG
    const parser = new DOMParser();
    const doc = parser.parseFromString(window.uploadedSvgContent, 'image/svg+xml');
    let mascotSvg = doc.documentElement;
    if (!mascotSvg || mascotSvg.nodeName !== 'svg') {
        alert("Mascot SVG load failed");
        return;
    }
    mascotSvg = mascotSvg.cloneNode(true);

    // White bg remove karo
    mascotSvg.querySelectorAll('rect,circle,path').forEach(el => {
        const fill = el.getAttribute('fill');
        if (fill && (fill === '#fff' || fill === '#ffffff' || fill === 'white')) {
            el.remove();
        }
    });

    if (!mascotSvg.hasAttribute('viewBox')) {
        mascotSvg.setAttribute('viewBox', '0 0 100 100');
    }

    // Clip path
    const clipId = `mascot-clip-${partId}-${view}`;
    const clipPath = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
    clipPath.setAttribute('id', clipId);
    const clonedPart = targetPart.cloneNode(true);
    clonedPart.removeAttribute('id');
    clipPath.appendChild(clonedPart);
    defs.appendChild(clipPath);

    // Pattern
    const bbox = targetPart.getBBox();
    const patternId = `mascot-pattern-${partId}-${view}`;
    const tileSize = Math.min(bbox.width, bbox.height) / 4;

    const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
    pattern.setAttribute('id', patternId);
    pattern.setAttribute('patternUnits', 'userSpaceOnUse');
    pattern.setAttribute('x', bbox.x);
    pattern.setAttribute('y', bbox.y);
    pattern.setAttribute('width', tileSize);
    pattern.setAttribute('height', tileSize);
    mascotSvg.setAttribute('width', tileSize);
    mascotSvg.setAttribute('height', tileSize);
    mascotSvg.setAttribute('preserveAspectRatio', 'xMidYMid meet');
    pattern.appendChild(mascotSvg);
    defs.appendChild(pattern);

    // Mascot overlay group — application groups se PEHLE
    let mascotGroup = mainSvg.querySelector('#mascot-overlay-group');
    if (!mascotGroup) {
        mascotGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        mascotGroup.setAttribute('id', 'mascot-overlay-group');
        // ✅ Application groups se pehle insert karo
        const firstAppGroup = mainSvg.querySelector('[id^="app-group-"]');
        if (firstAppGroup) {
            mainSvg.insertBefore(mascotGroup, firstAppGroup);
        } else {
            mainSvg.appendChild(mascotGroup);
        }
    } else {
        // ✅ Agar pehle se hai to application groups se pehle move karo
        const firstAppGroup = mainSvg.querySelector('[id^="app-group-"]');
        if (firstAppGroup && mascotGroup.nextSibling !== firstAppGroup) {
            mainSvg.insertBefore(mascotGroup, firstAppGroup);
        }
    }

    const overlay = targetPart.cloneNode(true);
    overlay.setAttribute('id', `mascot-overlay-${partId}`);
    overlay.setAttribute('fill', `url(#${patternId})`);
    overlay.setAttribute('clip-path', `url(#${clipId})`);
    overlay.style.pointerEvents = 'none';
    mascotGroup.appendChild(overlay);

    // State save karo
    if (!window.mascotsApplied) window.mascotsApplied = {};
    if (!window.mascotsApplied[view]) window.mascotsApplied[view] = {};

    const mascotBbox = targetPart.getBBox();
    window.mascotsApplied[view][partId] = {
        patternId,
        svgContent: new XMLSerializer().serializeToString(mascotSvg),
        bbox: {
            x: mascotBbox.x,
            y: mascotBbox.y,
            width: mascotBbox.width,
            height: mascotBbox.height
        },
        tileSize,
        replacements: {}
    };

    targetPart.dataset.hasMascot = 'true';
    targetPart.dataset.mascotId = patternId;

    if (window.saveCustomizations) window.saveCustomizations();
    if (window.updateUndoRedoButtons) window.updateUndoRedoButtons();
    if (window.updateMascotPreview) window.updateMascotPreview();
    if (window.updateMascotColorPalette) window.updateMascotColorPalette();

    const mascotControls = document.getElementById('mascotControls');
    if (mascotControls) {
        mascotControls.style.display = 'block';
        document.getElementById('mascotSize').value = 50;
        document.getElementById('mascotSizeValue').textContent = '50';
        document.getElementById('mascotOpacity').value = 100;
        document.getElementById('mascotOpacityValue').textContent = '100%';
        document.getElementById('mascotCount').value = 4;
        document.getElementById('mascotCountValue').textContent = '4';
    }

    // ✅ KEY FIX: Application layers wapas top pe lao
    setTimeout(() => {
        if (window.bringApplicationLayersToTop) {
            window.bringApplicationLayersToTop();
        }
    }, 50);
};


    // Mascot Size Control
    window.updateMascotSize = function (value) {

        if (!window.selectedSvgElement?.dataset.hasMascot) return;

        const view = window.currentView;
        const partId = window.selectedSvgElement.id;

        const mascotData = window.mascotsApplied[view]?.[partId];
        if (!mascotData) return;

        const pattern = document.querySelector(`#${mascotData.patternId}`);
        if (!pattern) return;

        const svg = pattern.querySelector('svg');
        if (!svg) return;

        const tileSize = mascotData.tileSize;

        const scale = value / 100;

        let g = svg.querySelector('.mascot-scale');

        if (!g) {

            g = document.createElementNS(
                "http://www.w3.org/2000/svg",
                "g"
            );

            g.classList.add("mascot-scale");

            while (svg.firstChild)
                g.appendChild(svg.firstChild);

            svg.appendChild(g);
        }

        // ⭐ VERY IMPORTANT CENTER SCALE FIX
        g.setAttribute(
            "transform",
            `translate(${tileSize / 2} ${tileSize / 2})
         scale(${scale})
         translate(${-tileSize / 2} ${-tileSize / 2})`
        );

        mascotData.size = value;

        document.getElementById("mascotSizeValue").textContent = value;

        if (window.saveCustomizations)
            window.saveCustomizations();
    };
    window.updateMascotPreview = function () {

        const previewBox = document.getElementById('mascotPreviewBox');
        if (!previewBox) return;

        const view = window.currentView;
        const partId = window.selectedSvgElement?.id;

        if (window.selectedSvgElement?.dataset.hasMascot && partId) {

            const mascotData = window.mascotsApplied?.[view]?.[partId];

            if (mascotData && mascotData.svgContent) {

                previewBox.innerHTML = `
            <div style="
                width:100%;
                height:100%;
                background-image:url('data:image/svg+xml;utf8,${encodeURIComponent(mascotData.svgContent)}');
                background-size:contain;
                background-position:center;
                background-repeat:no-repeat;
            ">
            </div>
            `;

                // ⭐ CLICKABLE PREVIEW
                previewBox.style.cursor = "pointer";
                previewBox.onclick = openMascotTemplateModal;

            } else {

                previewBox.innerHTML =
                    '<span style="color:#999;">No mascot applied</span>';

            }

        } else {

            previewBox.innerHTML =
                '<span style="color:#999;">No mascot applied</span>';

        }

    };

    // Mascot Opacity Control
    window.updateMascotOpacity = function (value) {
        const opacity = value / 100;

        if (!window.selectedSvgElement?.dataset.hasMascot) return;

        const partId = window.selectedSvgElement.id;
        const overlay = document.querySelector(`#mascot-overlay-${partId}`);

        if (overlay) {
            overlay.setAttribute('opacity', opacity);
        }

        const view = window.currentView;
        if (window.mascotsApplied[view]?.[partId]) {
            window.mascotsApplied[view][partId].opacity = parseInt(value);
            if (window.saveCustomizations) window.saveCustomizations();
        }

        document.getElementById('mascotOpacityValue').textContent = value + '%';
    };
    window.updatePatternAngle = function (value) {

        const angle = parseInt(value);

        const angleValue = document.getElementById('angleValue');
        if (angleValue) angleValue.textContent = angle + "°";

        if (!window.selectedSvgElement?.dataset.patternId) return;

        const view = window.currentView;
        const partId = window.selectedSvgElement.id;
        const patternData = window.patternsApplied[view]?.[partId];

        if (!patternData) return;

        const pattern = document.querySelector(
            `#${window.selectedSvgElement.dataset.patternId}`
        );

        if (!pattern) return;

        const bbox = patternData.bbox;

        const cx = bbox.x + bbox.width / 2;
        const cy = bbox.y + bbox.height / 2;

        // ⭐ rotate pattern around CENTER
        pattern.setAttribute(
            "patternTransform",
            `rotate(${angle} ${cx} ${cy})`
        );

        patternData.angle = angle;

        if (window.saveCustomizations)
            window.saveCustomizations();
        const slider = document.getElementById("circularSlider");

        if (slider) {

            slider.style.background =
                `conic-gradient(#000 ${value}deg, #e5e5e5 ${value}deg)`;

        }
    };

    // Mascot Count/Repetition Control
    window.updateMascotCount = function (value) {
        if (!window.selectedSvgElement?.dataset.hasMascot) return;

        const view = window.currentView;
        const partId = window.selectedSvgElement.id;
        const mascotData = window.mascotsApplied[view]?.[partId];

        if (!mascotData) return;

        const bbox = window.selectedSvgElement.getBBox();
        const newTileSize = Math.min(bbox.width, bbox.height) / value; // divide by count

        const pattern = document.querySelector(`#${mascotData.patternId}`);
        if (pattern) {
            pattern.setAttribute('width', newTileSize);
            pattern.setAttribute('height', newTileSize);

            const svg = pattern.querySelector('svg');
            if (svg) {
                svg.setAttribute('width', newTileSize);
                svg.setAttribute('height', newTileSize);
            }
        }

        mascotData.tileSize = newTileSize;
        mascotData.count = value;

        if (window.saveCustomizations) window.saveCustomizations();

        document.getElementById('mascotCountValue').textContent = value;
    };



    window.applyMascotsToSvg = function (svg, view) {

        if (!window.mascotsApplied?.[view]) return;

        let defs = svg.querySelector('defs');
        if (!defs) {
            defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            svg.insertBefore(defs, svg.firstChild);
        }

        let group = svg.querySelector('#mascot-overlay-group');
        if (!group) {
            group = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            group.setAttribute('id', 'mascot-overlay-group');
            svg.appendChild(group);
        }

        Object.entries(window.mascotsApplied[view]).forEach(([partId, data]) => {

            const base = svg.querySelector(`#${partId}`);
            if (!base) return;

            const clipId = `mascot-clip-${partId}-${view}`;
            const patId = data.patternId;

            // clip
            const clip = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
            clip.setAttribute('id', clipId);
            clip.appendChild(base.cloneNode(true));
            defs.appendChild(clip);

            // pattern
            const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
            pattern.setAttribute('id', patId);
            pattern.setAttribute('patternUnits', 'userSpaceOnUse');
            pattern.setAttribute('width', data.tileSize);
            pattern.setAttribute('height', data.tileSize);

            const doc = new DOMParser().parseFromString(data.svgContent, 'image/svg+xml');
            const msvg = doc.querySelector('svg');

            msvg.setAttribute('width', data.tileSize);
            msvg.setAttribute('height', data.tileSize);

            pattern.appendChild(msvg);
            defs.appendChild(pattern);

            const over = base.cloneNode(true);
            over.setAttribute('fill', `url(#${patId})`);
            over.setAttribute('clip-path', `url(#${clipId})`);
            over.style.pointerEvents = 'none';

            group.appendChild(over);
        });
    };


    //============== pattern remove===============//


    // =================== PATTERN STATE RESTORATION ===================

    window.restorePatternStateForPart = function (partId, view) {
        console.log(`🔄 Restoring pattern state for ${partId} in ${view}`);

        const patternData = window.patternsApplied?.[view]?.[partId];

        if (!patternData) {
            console.log(`No pattern data for ${partId}`);
            const controls = document.getElementById('patternControls');
            if (controls) controls.style.display = 'none';
            return false;
        }

        console.log(`✅ Found pattern data for ${partId}`);

        window.uploadedSvgContent = patternData.svgContent;
        window.patternAngle = patternData.angle || 0;
        window.patternSize = patternData.size || 50;
        window.patternOpacity = patternData.opacity || 100;
        window.patternScale = (patternData.size || 50) / 50;

        const sizeSlider = document.getElementById('patternSize');
        if (sizeSlider) {
            sizeSlider.value = window.patternSize;
            const sizeValue = document.getElementById('sizeValue');
            if (sizeValue) sizeValue.textContent = window.patternSize;
        }

        const opacitySlider = document.getElementById('patternOpacity');
        if (opacitySlider) {
            opacitySlider.value = window.patternOpacity;
            const opacityValue = document.getElementById('opacityValue');
            if (opacityValue) opacityValue.textContent = window.patternOpacity + '%';
        }

        const angleDisplay = document.getElementById('angleValue');
        if (angleDisplay) {
            angleDisplay.textContent = window.patternAngle + '°';
        }

        const controls = document.getElementById('patternControls');
        if (controls) {
            controls.style.display = 'block';
            console.log('✅ Pattern controls shown');
        }
        const btns = document.querySelector('.pattern-top-buttons');
        if (btns) btns.style.display = 'none';
        const angleSlider = document.getElementById('patternAngle');
        if (angleSlider) {
            angleSlider.value = window.patternAngle;
        }
        if (window.updatePatternPreview) window.updatePatternPreview();
        if (window.updatePatternColorPalette) window.updatePatternColorPalette();

        return true;
    };

    // =================== PATTERN INITIALIZATION ===================

    window.initializePatternsOnLoad = function () {
        console.log('🎨 Initializing patterns...');

        if (!window.patternsApplied) {
            console.log('No saved patterns');
            return;
        }
        window.patternsApplied = window.patternsApplied || {};

        const mainSvg = window.getMainSvg?.() || document.querySelector('svg');
        if (!mainSvg) {
            console.warn('Main SVG not found — retrying...');
            setTimeout(window.initializePatternsOnLoad, 300);
            return;
        }


        Object.keys(window.patternsApplied).forEach(view => {

            Object.keys(window.patternsApplied[view]).forEach(partId => {

                const part = mainSvg.querySelector(`[id="${partId}"]`);

                if (!part) return;

                const patternData = window.patternsApplied[view][partId];

                part.dataset.hasPattern = 'true';
                part.dataset.patternId = patternData.patternId;
                part.dataset.patternView = view;

                // ⭐ FIX — SELECT PART AGAIN
                window.selectedSvgElement = part;

                // ⭐ FIX — RESTORE CONTROLS AGAIN
                window.restorePatternStateForPart(partId, view);

            });

        });
        console.log('✅ Pattern initialization complete');
    };

    // =================== PART CLICK HANDLER ===================

    // In your part click handler, add this check:



    // window.onPartClickForPattern = function (partElement) {

    //     window.selectedSvgElement = partElement;

    //     const view = window.currentView;
    //     const partId = partElement.id;

    //     const topButtons = document.querySelector('.pattern-top-buttons');

    //     // CASE 1 — pattern already applied
    //     if (partElement.dataset.hasPattern === 'true') {

    //         if (topButtons) topButtons.style.display = 'none';

    //         document.getElementById('mascotControls').style.display = 'none';

    //         window.restorePatternStateForPart(partId, view);

    //         return;
    //     }

    //     // CASE 2 — mascot already applied
    //     if (partElement.dataset.hasMascot === 'true') {

    //         if (topButtons) topButtons.style.display = 'none';

    //         document.getElementById('patternControls').style.display = 'none';

    //         window.restoreMascotStateForPart(partId, view);

    //         updateMascotColorPalette(); // ⭐ ADD THIS LINE

    //         return;
    //     }

    //     // CASE 3 — NO pattern + NO mascot
    //     if (topButtons) topButtons.style.display = 'flex';

    //     document.getElementById('patternControls').style.display = 'none';

    //     document.getElementById('mascotControls').style.display = 'none';

    //     // reset preview
    //     const preview = document.getElementById('patternPreviewBox');

    //     if (preview) {

    //         preview.innerHTML =
    //             '<span style="color:#999;">No pattern applied</span>';

    //         preview.style.cursor = "pointer";

    //         preview.onclick = openPatternLibrary;

    //     }
    // };
    window.onPartClickForPattern = function (partElement) {
    window.selectedSvgElement = partElement;
    const view = window.currentView;
    const partId = partElement.id;
    const topButtons = document.querySelector('.pattern-top-buttons');

    // ✅ STEP 1: PEHLE SAARI UI RESET KARO
    if (topButtons) topButtons.style.display = 'none';

    const patternControls = document.getElementById('patternControls');
    if (patternControls) patternControls.style.display = 'none';

    const mascotControls = document.getElementById('mascotControls');
    if (mascotControls) mascotControls.style.display = 'none';

    // Pattern preview reset
    const preview = document.getElementById('patternPreviewBox');
    if (preview) {
        preview.innerHTML = '<span style="color:#999;">No pattern applied</span>';
        preview.style.cursor = 'default';
        preview.onclick = null;
    }

    // Mascot preview reset
    const mascotPreview = document.getElementById('mascotPreviewBox');
    if (mascotPreview) {
        mascotPreview.innerHTML = '<span style="color:#999;">No mascot applied</span>';
        mascotPreview.style.cursor = 'default';
        mascotPreview.onclick = null;
    }

    // Pattern color palette reset
    const patternPalette = document.getElementById('patternColorPalette');
    if (patternPalette) patternPalette.innerHTML = '';

    // Mascot color palette reset
    const mascotPalette = document.getElementById('mascotColorPalette');
    if (mascotPalette) mascotPalette.innerHTML = '';

    // ✅ STEP 2: CHECK KARO IS PART PAR KYA HAI
    const hasPatternInState = window.patternsApplied?.[view]?.[partId];
    const hasMascotInState = window.mascotsApplied?.[view]?.[partId];

    // ✅ CASE 1 — Is part par PATTERN hai
    if (partElement.dataset.hasPattern === 'true' || hasPatternInState) {
        // dataset sync karo
        partElement.dataset.hasPattern = 'true';

        // Pattern controls dikhao, mascot hide
        if (patternControls) patternControls.style.display = 'block';
        if (mascotControls) mascotControls.style.display = 'none';
        if (topButtons) topButtons.style.display = 'none';

        // Pattern state restore karo
        window.restorePatternStateForPart(partId, view);

        // Preview clickable banao
        if (preview) {
            preview.style.cursor = 'pointer';
            preview.onclick = openPatternLibrary;
        }

        console.log('✅ Pattern part selected:', partId);
        return;
    }

    // ✅ CASE 2 — Is part par MASCOT hai
    if (partElement.dataset.hasMascot === 'true' || hasMascotInState) {
        // dataset sync karo
        partElement.dataset.hasMascot = 'true';

        // Mascot controls dikhao, pattern hide
        if (mascotControls) mascotControls.style.display = 'block';
        if (patternControls) patternControls.style.display = 'none';
        if (topButtons) topButtons.style.display = 'none';

        // Mascot state restore karo
        window.restoreMascotStateForPart(partId, view);

        // Mascot color palette update karo
        if (window.updateMascotColorPalette) window.updateMascotColorPalette();

        // Mascot preview clickable banao
        if (mascotPreview) {
            mascotPreview.style.cursor = 'pointer';
            mascotPreview.onclick = openMascotTemplateModal;
        }

        console.log('✅ Mascot part selected:', partId);
        return;
    }

    // ✅ CASE 3 — BILKUL BLANK PART — fresh buttons dikhao
    if (topButtons) topButtons.style.display = 'flex';

    if (patternControls) patternControls.style.display = 'none';
    if (mascotControls) mascotControls.style.display = 'none';

    // Preview clickable banao taake pattern select ho sake
    if (preview) {
        preview.style.cursor = 'pointer';
        preview.onclick = openPatternLibrary;
    }

    console.log('✅ Blank part selected — showing fresh buttons:', partId);
};

    window.restoreMascotStateForPart = function (partId, view) {
        const mascotData = window.mascotsApplied?.[view]?.[partId];
        if (!mascotData) {
            document.getElementById('mascotControls').style.display = 'none';
            return false;
        }

        // Sliders restore karo
        const sizeSlider = document.getElementById('mascotSize');
        if (sizeSlider) {
            sizeSlider.value = mascotData.size || 50;
            document.getElementById('mascotSizeValue').textContent = mascotData.size || 50;
        }

        const opacitySlider = document.getElementById('mascotOpacity');
        if (opacitySlider) {
            opacitySlider.value = mascotData.opacity || 100;
            document.getElementById('mascotOpacityValue').textContent = (mascotData.opacity || 100) + '%';
        }

        const countSlider = document.getElementById('mascotCount');
        if (countSlider) {
            countSlider.value = mascotData.count || 4;
            document.getElementById('mascotCountValue').textContent = mascotData.count || 4;
        }

        document.getElementById('mascotControls').style.display = 'block';
        updateMascotColorPalette();
        return true;
    };


    // Mascot apply karne se pehle:
    function applyMascotToModel(mascotSvg) {
        const mainSvg = document.getElementById('your-main-svg-id');

        // ⭐ PEHLE saare purane mascot overlays remove karo
        const oldMascotOverlays = mainSvg.querySelectorAll('[id^="mascot-overlay"]');
        oldMascotOverlays.forEach(overlay => overlay.remove());

        // ⭐ Ya specific mascot group remove karo
        const oldMascotGroup = mainSvg.querySelector('#mascot-group');
        if (oldMascotGroup) oldMascotGroup.remove();

        // Ab naya mascot add karo
        // ... your mascot code
    }

    window.initializeMascotsOnLoad = function () {

        if (!window.mascotsApplied) return;

        const svg = window.getMainSvg();
        if (!svg) return;

        Object.keys(window.mascotsApplied).forEach(view => {

            Object.entries(window.mascotsApplied[view]).forEach(([partId, data]) => {

                const part = svg.querySelector(`[id="${partId}"]`);
                if (!part) return;

                part.dataset.hasMascot = 'true';
                part.dataset.mascotId = data.patternId;

                // ⭐ SELECT AGAIN
                window.selectedSvgElement = part;

                // ⭐ RESTORE SLIDERS AGAIN
                window.restoreMascotStateForPart(partId, view);

            });

        });

        console.log("✅ Mascots restored properly");
    };

window.updateMascotColorPalette = function () {
    const container = document.getElementById('mascotColorPalette');
    if (!container) return;

    container.innerHTML = '';

    if (!window.selectedSvgElement?.dataset.hasMascot) return;

    const view = window.currentView;
    const partId = window.selectedSvgElement.id;
    const mascotData = window.mascotsApplied?.[view]?.[partId];

    if (!mascotData || !mascotData.svgContent) {
        console.warn(`No mascot data found for ${partId} in ${view}`);
        return;
    }

    // Mascot ke colors detect karo (same as pattern)
    let mascotColors = window.getPatternUniqueColors(mascotData.svgContent);

    // Fallback agar koi color nahi mila
    if (!mascotColors.length) {
        mascotColors = ['#000000', '#FFFFFF'];
    }

    const userColors = window.selectedColors?.length > 0
        ? [...window.selectedColors]
        : ['#000000'];

    mascotColors.forEach((mascotColor) => {
        const row = document.createElement('div');
        row.className = 'pattern-color-row'; // ✅ Same class as pattern

        // Left box — detected mascot color (thumbnail)
        const originalBox = document.createElement('div');
        originalBox.className = 'original-color-box'; // ✅ Same class as pattern
        originalBox.style.backgroundColor = mascotColor;

        // Arrow
        const arrow = document.createElement('div');
        arrow.className = 'color-arrow';
        arrow.textContent = '→';

        // Right side — user color choices
        const choicesContainer = document.createElement('div');
        choicesContainer.className = 'color-choices'; // ✅ Same class as pattern

        userColors.forEach((userColor) => {
            const box = document.createElement('div');
            box.className = 'user-color-box'; // ✅ Same class as pattern
            box.style.backgroundColor = userColor;
            box.dataset.userColor = userColor;
            box.dataset.mascotColor = mascotColor;

            // Checkmark
            const check = document.createElement('div');
            check.className = 'color-checkmark';
            check.textContent = '✓';
            box.appendChild(check);

            box.onclick = function () {
                // Pehle sab se selected class hata do
                this.parentElement.querySelectorAll('.user-color-box').forEach(b => {
                    b.classList.remove('selected');
                });

                // Selected mark karo
                this.classList.add('selected');

                // Replacement save karo
                if (!window.mascotsApplied[view][partId].replacements) {
                    window.mascotsApplied[view][partId].replacements = {};
                }
                window.mascotsApplied[view][partId].replacements[mascotColor.toUpperCase()] = userColor;

                // Mascot redraw karo nayi colors ke saath
                window.recreateMascotAndOverlayWithNewColors();
            };

            // Already selected replacement highlight karo
            const currentReplacements = mascotData.replacements || {};
            if (currentReplacements[mascotColor.toUpperCase()] === userColor) {
                box.classList.add('selected');
            }

            choicesContainer.appendChild(box);
        });

        row.appendChild(originalBox);
        row.appendChild(arrow);
        row.appendChild(choicesContainer);
        container.appendChild(row);
    });

    container.style.display = 'flex';
    container.style.flexDirection = 'column';
    container.style.gap = '8px';
};




    window.recreateMascotAndOverlayWithNewColors =
        function () {

            if (!window.selectedSvgElement?.dataset.hasMascot)
                return;

            const view = window.currentView;
            const partId = window.selectedSvgElement.id;

            const mascotData =
                window.mascotsApplied[view]?.[partId];

            if (!mascotData) return;

            const mainSvg = window.getMainSvg();

            const defs =
                mainSvg.querySelector("defs");

            const patternId =
                mascotData.patternId;

            const oldPattern =
                defs.querySelector(`#${patternId}`);

            if (oldPattern) oldPattern.remove();

            const bbox =
                mascotData.bbox;

            const pattern =
                document.createElementNS(
                    "http://www.w3.org/2000/svg",
                    "pattern"
                );

            pattern.setAttribute("id", patternId);
            pattern.setAttribute("patternUnits", "userSpaceOnUse");
            pattern.setAttribute("width", mascotData.tileSize);
            pattern.setAttribute("height", mascotData.tileSize);

            const parser =
                new DOMParser();

            const doc =
                parser.parseFromString(
                    mascotData.svgContent,
                    "image/svg+xml"
                );

            const svg =
                doc.querySelector("svg");

            if (!svg) return;

            const replacements =
                mascotData.replacements || {};

            Object.entries(replacements)
                .forEach(([oldColor, newColor]) => {

                    svg.querySelectorAll("[fill]")
                        .forEach(el => {

                            if (
                                el.getAttribute("fill")?.toUpperCase()
                                === oldColor
                            )
                                el.setAttribute("fill", newColor);

                        });

                });

            pattern.appendChild(svg);

            defs.appendChild(pattern);

            document.querySelector(
                `#mascot-overlay-${partId}`
            ).setAttribute(
                "fill",
                `url(#${patternId})`
            );

            if (window.saveCustomizations)
                window.saveCustomizations();

        };
    // =================== AUTO-INITIALIZE ===================

    if (document.readyState === 'loading') {

        document.addEventListener('DOMContentLoaded', function () {

            setTimeout(window.initializePatternsOnLoad, 500);
            setTimeout(window.initializeMascotsOnLoad, 500);

        });

    } else {

        setTimeout(window.initializePatternsOnLoad, 500);
        setTimeout(window.initializeMascotsOnLoad, 500);   // 🔥 THIS WAS MISSING

    }


    setTimeout(() => {

        const svg = window.getMainSvg?.() || document.querySelector('svg');

        if (!svg) return;

        svg.addEventListener('click', function (e) {

            const clickedPart = e.target.closest('[id]');

            if (!clickedPart) return;

            window.selectedSvgElement = clickedPart;

            if (window.onPartClickForPattern) {
                window.onPartClickForPattern(clickedPart);
            }

        });

        console.log("✅ Pattern global click handler attached");

    }, 700);


})();
