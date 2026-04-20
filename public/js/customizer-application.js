(function () {
    'use strict';

    // ============================================================
    // =================== APPLICATION STATE ===================
    // ============================================================

    window.applicationLayers = [];
    window.currentApplicationLayer = null;
    window.selectedApplicationType = 'number';
    window.selectedApplicationView = 'front';
    window.selectedApplicationPart = null;
    window.modalPreviewText = null;

    if (!window.applicationsApplied) window.applicationsApplied = {};

    window.currentOutlineStyle = 'single';
    window.outlineColors = {
        baseColor: '#FFFFFF',
        outline1: '#000000',
        outline2: '#666666',
        shadow: '#333333'
    };

    // ── Security ──────────────────────────────────────────────
    document.addEventListener('contextmenu', function (e) { e.preventDefault(); });
    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey && (e.key === 'c' || e.key === 'u')) e.preventDefault();
    });

    // ============================================================
    // =================== SIDEBAR TOGGLE ===================
    // ============================================================

    window.toggleApplicationsSidebar = function () {
        const sidebar = document.getElementById('applicationsSidebar');
        const toggleIcon = document.getElementById('applicationsSidebarToggleIcon');
        if (!sidebar) return;
        sidebar.classList.toggle('open');
        if (toggleIcon) {
            const isOpen = sidebar.classList.contains('open');
            toggleIcon.style.opacity = isOpen ? '0' : '1';
            toggleIcon.style.pointerEvents = isOpen ? 'none' : 'auto';
        }
    };

    window.openApplicationsSidebar = function () {
        const sidebar = document.getElementById('applicationsSidebar');
        if (sidebar) sidebar.classList.add('open');
    };

    window.closeApplicationsSidebar = function () {
        const sidebar = document.getElementById('applicationsSidebar');
        if (sidebar) sidebar.classList.remove('open');
    };

    // ============================================================
    // =================== TAB SWITCHING ===================
    // ============================================================

    window.switchTextCustomizationTab = function (tabName) {
        document.querySelectorAll('.text-custom-tab').forEach(tab => {
            tab.style.borderBottom = '3px solid transparent';
            tab.style.color = '#999';
        });
        const activeTab = document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1));
        if (activeTab) {
            activeTab.style.borderBottom = '3px solid #000';
            activeTab.style.color = '#333';
        }
        const colorsTab = document.getElementById('colorsTabContent');
        const patternTab = document.getElementById('patternTabContent');
        const mascotTab = document.getElementById('mascotTabContent');
        if (colorsTab) colorsTab.style.display = tabName === 'colors' ? 'block' : 'none';
        if (patternTab) patternTab.style.display = tabName === 'pattern' ? 'block' : 'none';
        if (mascotTab) mascotTab.style.display = tabName === 'mascot' ? 'block' : 'none';
    };

    // ============================================================
    // =================== ACCENTS MODAL ===================
    // ============================================================

    window.openAccentsModal = function () {
        document.getElementById('accentsModal').style.display = 'flex';
    };

    window.closeAccentsModal = function () {
        document.getElementById('accentsModal').style.display = 'none';
    };

    window.selectAccentStyle = function (styleName) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        layer.outlineStyle = styleName;
        if (!layer.outlineColors) {
            layer.outlineColors = { baseColor: '#ffffff', outline1: '#000000', outline2: '#666666', shadow: '#333333' };
        }
        if (layer.shadowOffset === undefined) layer.shadowOffset = 3;

        window.currentOutlineStyle = styleName;
        window.outlineColors = { ...layer.outlineColors };

        applyOutlineStyleToText(layer.id);
        updateOutlineStylePreview();
        showHideColorPickers(styleName);
        populateOutlineColorPickers();

        const styleNameEl = document.getElementById('outlineStyleName');
        if (styleNameEl) {
            const styleNames = {
                'single': 'Single Color', 'two-color': 'Two Color',
                'two-color-shadow': 'Two Color with Drop Shadow', 'three-color': 'Three Color',
                'single-shadow': 'Single Color with Drop Shadow', 'three-color-shadow': 'Three Color with Drop Shadow'
            };
            styleNameEl.textContent = styleNames[styleName] || styleName;
        }
        closeAccentsModal();
    };

    window.updateOutlineStylePreview = function () {
        const preview = document.getElementById('outlineStylePreview');
        if (!preview) return;
        const style = window.currentOutlineStyle;
        const colors = window.outlineColors;
        preview.textContent = 'T';
        preview.style.fontFamily = 'Arial Black';
        preview.style.color = '';
        preview.style.webkitTextFillColor = '';
        preview.style.webkitTextStroke = '';
        preview.style.filter = '';
        switch (style) {
            case 'single': preview.style.color = colors.baseColor; break;
            case 'two-color': preview.style.webkitTextFillColor = colors.baseColor; preview.style.webkitTextStroke = `3px ${colors.outline1}`; break;
            case 'two-color-shadow': preview.style.webkitTextFillColor = colors.baseColor; preview.style.webkitTextStroke = `3px ${colors.outline1}`; preview.style.filter = `drop-shadow(3px 3px 0 ${colors.shadow})`; break;
            case 'three-color': preview.style.webkitTextFillColor = colors.baseColor; preview.style.webkitTextStroke = `3px ${colors.outline1}`; break;
            case 'single-shadow': preview.style.color = colors.baseColor; preview.style.filter = `drop-shadow(3px 3px 0 ${colors.shadow})`; break;
            case 'three-color-shadow': preview.style.webkitTextFillColor = colors.baseColor; preview.style.webkitTextStroke = `3px ${colors.outline1}`; preview.style.filter = `drop-shadow(3px 3px 0 ${colors.shadow})`; break;
        }
    };

    window.showHideColorPickers = function (style) {
        document.getElementById('outlineColorsSection').style.display = 'block';
        const outline1 = document.getElementById('outline1Section');
        const outline2 = document.getElementById('outline2Section');
        const shadow = document.getElementById('shadowSection');
        outline1.style.display = 'none';
        outline2.style.display = 'none';
        shadow.style.display = 'none';
        switch (style) {
            case 'two-color': outline1.style.display = 'flex'; break;
            case 'two-color-shadow': outline1.style.display = 'flex'; shadow.style.display = 'flex'; break;
            case 'three-color': outline1.style.display = 'flex'; outline2.style.display = 'flex'; break;
            case 'single-shadow': shadow.style.display = 'flex'; break;
            case 'three-color-shadow': outline1.style.display = 'flex'; outline2.style.display = 'flex'; shadow.style.display = 'flex'; break;
        }
    };

    window.populateOutlineColorPickers = function () {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;
        const colors = window.selectedColors?.length ? window.selectedColors : ['#FFFFFF', '#000000'];
        function picker(id, key) {
            createColorPicker(id, colors, layer.outlineColors[key], color => {
                layer.outlineColors[key] = color;
                window.outlineColors = { ...layer.outlineColors };
                applyOutlineStyleToText(layer.id);
                updateOutlineStylePreview();
                if (window.saveCustomizations) window.saveCustomizations();
            });
        }
        picker('baseColorPicker', 'baseColor');
        picker('outline1ColorPicker', 'outline1');
        picker('outline2ColorPicker', 'outline2');
        picker('shadowColorPicker', 'shadow');
    };

    window.createColorPicker = function (containerId, colors, selectedColor, callback) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';
        colors.forEach(color => {
            const box = document.createElement('div');
            box.style.width = '32px';
            box.style.height = '32px';
            box.style.background = color;
            box.style.cursor = 'pointer';
            box.style.border = color.toUpperCase() === selectedColor.toUpperCase() ? '3px solid #007bff' : '2px solid #ddd';
            box.style.transition = 'all 0.2s';
            box.onclick = function () {
                callback(color);
                container.querySelectorAll('div').forEach(b => b.style.border = '2px solid #ddd');
                this.style.border = '3px solid #007bff';
            };
            container.appendChild(box);
        });
    };

    // ============================================================
    // =================== OUTLINE STYLE APPLICATION ===================
    // ============================================================

    window.applyOutlineStyleToText = function (layerId) {
        const layer = findLayerById(layerId);
        if (!layer) return;
        const textEl = document.getElementById(layerId);
        if (!textEl) return;
        const colors = layer.outlineColors;
        const style = layer.outlineStyle;
        const shadow = layer.shadowOffset ?? 3;
        const stroke = layer.strokeWidth || 5;
        const hasFill = layer.hasPattern || layer.hasMascot;

        textEl.parentElement.querySelectorAll(`[data-outline-for="${layerId}"]`).forEach(e => e.remove());
        textEl.style.filter = 'none';

        switch (style) {
            case 'single':
                if (!hasFill) textEl.setAttribute('fill', colors.baseColor);
                textEl.removeAttribute('stroke');
                textEl.removeAttribute('stroke-width');
                textEl.style.filter = 'none';
                break;
            case 'single-shadow':
                if (!hasFill) textEl.setAttribute('fill', colors.baseColor);
                textEl.removeAttribute('stroke');
                textEl.removeAttribute('stroke-width');
                textEl.style.filter = `drop-shadow(${shadow}px ${shadow}px 0 ${colors.shadow})`;
                break;
            case 'two-color':
                if (!hasFill) textEl.setAttribute('fill', colors.baseColor);
                textEl.setAttribute('stroke', colors.outline1);
                textEl.setAttribute('stroke-width', stroke);
                textEl.style.filter = 'none';
                break;
            case 'two-color-shadow':
                if (!hasFill) textEl.setAttribute('fill', colors.baseColor);
                textEl.setAttribute('stroke', colors.outline1);
                textEl.setAttribute('stroke-width', stroke);
                textEl.style.filter = `drop-shadow(${shadow}px ${shadow}px 0 ${colors.shadow})`;
                break;
            case 'three-color':
            case 'three-color-shadow':
                createThreeColorOutline(textEl, layer, style.includes('shadow'));
                break;
        }
    };

    // ============================================================
    // =================== THREE COLOR OUTLINE ===================
    // ============================================================

    window.createThreeColorOutline = function (textEl, layer, withShadow) {
        const colors = layer.outlineColors;
        const shadow = layer.shadowOffset ?? 3;
        const stroke = layer.strokeWidth || 5;
        const hasFill = layer.hasPattern || layer.hasMascot;

        const outer = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        outer.setAttribute('data-outline-for', layer.id);
        ['x', 'y', 'font-size', 'transform'].forEach(a => {
            if (textEl.getAttribute(a)) outer.setAttribute(a, textEl.getAttribute(a));
        });
        outer.style.fontFamily = textEl.style.fontFamily;
        outer.textContent = textEl.textContent;
        outer.setAttribute('fill', colors.outline2);
        outer.setAttribute('stroke', colors.outline2);
        outer.setAttribute('stroke-width', stroke * 2);
        outer.setAttribute('text-anchor', 'middle');
        outer.setAttribute('dominant-baseline', 'middle');
        outer.setAttribute('stroke-linejoin', 'miter');
        outer.style.pointerEvents = 'none';
        outer.style.filter = withShadow ? `drop-shadow(${shadow}px ${shadow}px 0 ${colors.shadow})` : 'none';

        textEl.parentElement.insertBefore(outer, textEl);
        if (!hasFill) textEl.setAttribute('fill', colors.baseColor);
        textEl.setAttribute('stroke', colors.outline1);
        textEl.setAttribute('stroke-width', stroke);
        textEl.style.filter = 'none';
    };

    // ============================================================
    // =================== OPEN / CLOSE APPLICATION MODAL ===================
    // ============================================================

    window.openApplicationModal = function () {
        document.getElementById('applicationModal').style.display = 'flex';
        window.selectedApplicationType = 'number';
        window.selectedApplicationView = window.currentView || 'front';
        window.selectedApplicationPart = null;

        document.querySelectorAll('.app-type-card').forEach(card => {
            card.classList.remove('selected');
            if (card.dataset.type === 'number') card.classList.add('selected');
        });
        document.querySelectorAll('.perspective-btn').forEach(btn => {
            btn.classList.remove('selected');
            if (btn.dataset.view === window.selectedApplicationView) btn.classList.add('selected');
        });

        loadPartsForApplicationModal();
        updateModalPreview();
        loadBackendFonts();
    };

    window.closeApplicationModal = function () {
        document.getElementById('applicationModal').style.display = 'none';
        const previewContainer = document.getElementById('modalPreviewSvg');
        if (previewContainer) previewContainer.innerHTML = '<p style="color:#999;font-size:14px;">Select options to preview</p>';
        if (window.modalPreviewText) { window.modalPreviewText.remove(); window.modalPreviewText = null; }
    };

    // ============================================================
    // =================== MODAL SELECTIONS ===================
    // ============================================================

    window.selectApplicationType = function (type) {
        window.selectedApplicationType = type;
        document.querySelectorAll('.app-type-card').forEach(card => {
            card.classList.remove('selected');
            if (card.dataset.type === type) card.classList.add('selected');
        });
        updateModalPreview();
    };

    window.selectPerspective = function (view) {
        window.selectedApplicationView = view;
        document.querySelectorAll('.perspective-btn').forEach(btn => {
            btn.classList.remove('selected');
            if (btn.dataset.view === view) btn.classList.add('selected');
        });
        if (window.switchView) window.switchView(view);
        setTimeout(() => { loadPartsForApplicationModal(); updateModalPreview(); }, 300);
    };

    window.loadPartsForApplicationModal = function () {
        const container = document.getElementById('partSelectionGrid');
        if (!container) return;
        const view = window.selectedApplicationView;
        const modelData = window.modelDataByView?.[view];
        if (!modelData?.parts?.length) {
            container.innerHTML = '<p style="color:#999;text-align:center;padding:20px;">No parts available</p>';
            return;
        }
        container.innerHTML = '';
        modelData.parts.forEach((part, index) => {
            const btn = document.createElement('button');
            btn.className = 'part-btn';
            btn.textContent = part.title || `Part ${index + 1}`;
            btn.dataset.partId = part.part;
            btn.dataset.partIndex = index;
            if (part.part === window.selectedApplicationPart) btn.classList.add('selected');
            btn.onclick = function () {
                window.selectedApplicationPart = part.part;
                const partIndex = parseInt(this.dataset.partIndex);
                if (window.allSvgParts?.[partIndex] && window.selectSvgElement) {
                    window.selectSvgElement(window.allSvgParts[partIndex]);
                }
                container.querySelectorAll('.part-btn').forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
                updateModalPreview();
            };
            container.appendChild(btn);
        });
    };

    window.updateModalPreview = function () {
        const previewContainer = document.getElementById('modalPreviewSvg');
        if (!previewContainer) return;
        if (!window.selectedApplicationView || !window.selectedApplicationPart) {
            previewContainer.innerHTML = '<p style="color:#999;font-size:14px;">Select view and part to preview</p>';
            return;
        }
        const mainSvg = window.getMainSvg();
        if (!mainSvg) { previewContainer.innerHTML = '<p style="color:#999;font-size:14px;">SVG not loaded</p>'; return; }

        const clonedSvg = mainSvg.cloneNode(true);
        clonedSvg.removeAttribute('id');
        clonedSvg.style.maxWidth = '100%';
        clonedSvg.style.maxHeight = '100%';
        clonedSvg.style.width = 'auto';
        clonedSvg.style.height = 'auto';

        let defaultText = '88';
        if (window.selectedApplicationType === 'teamname') defaultText = 'TEAM';
        if (window.selectedApplicationType === 'playername') defaultText = 'PLAYER';
        if (window.selectedApplicationType === 'mascot') defaultText = '🦅';

        const partElement = clonedSvg.querySelector(`#${window.selectedApplicationPart}`);
        if (!partElement) { previewContainer.innerHTML = '<p style="color:#dc3545;font-size:14px;">Part not found</p>'; return; }

        const bbox = partElement.getBBox();
        const centerX = bbox.x + bbox.width / 2;
        const centerY = bbox.y + bbox.height / 2;
        clonedSvg.querySelectorAll('.modal-preview-text').forEach(t => t.remove());

        const globalColors = window.selectedColors || ['#FFFFFF', '#000000'];
        const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        text.classList.add('modal-preview-text');
        text.setAttribute('x', centerX);
        text.setAttribute('y', centerY);
        text.setAttribute('font-size', '100');
        text.style.fontFamily = 'Arial Black';
        text.setAttribute('fill', globalColors[0] || '#FFFFFF');
        text.setAttribute('stroke', globalColors[1] || '#000000');
        text.setAttribute('stroke-width', '6');
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('dominant-baseline', 'middle');
        text.setAttribute('paint-order', 'stroke fill');
        text.setAttribute('stroke-linejoin', 'miter');
        text.textContent = defaultText;
        clonedSvg.appendChild(text);

        previewContainer.innerHTML = '';
        previewContainer.appendChild(clonedSvg);
    };

    // ============================================================
    // =================== CONFIRM ADD APPLICATION ===================
    // ============================================================

    window.confirmAddApplication = function () {
        if (!window.selectedApplicationPart) { alert('Please select a part!'); return; }

        const view = window.selectedApplicationView;
        const partId = window.selectedApplicationPart;

        if (window.selectedApplicationType === 'mascot') {
            const layerId = 'app-' + Date.now();
            const layer = {
                id: layerId, type: 'direct-mascot', view, partId,
                mascotSvg: null, mascotId: null,
                x: 0, y: 0, rotation: 0,
                flipX: 1, flipY: 1, _flipState: 0,
                mascotScaleX: 1, mascotScaleY: 1, mascotOpacity: 100
            };
            if (!window.applicationsApplied[view]) window.applicationsApplied[view] = {};
            if (!window.applicationsApplied[view][partId]) window.applicationsApplied[view][partId] = [];
            window.applicationsApplied[view][partId].push(layer);
            window.currentApplicationLayer = layerId;
            updateApplicationLayersList();
            openApplicationsSidebar();
            if (window.saveCustomizations) window.saveCustomizations();
            const appModal = document.getElementById('applicationModal');
            if (appModal) appModal.style.display = 'none';
            setTimeout(function () {
                if (typeof window.openMascotSelectModal === 'function') {
                    window.openMascotSelectModal(layerId);
                } else {
                    alert('Mascot modal load nahi hua. Page reload karein.');
                }
            }, 250);
            return;
        }

        const layerId = 'app-' + Date.now();
        let defaultText = '00';
        if (window.selectedApplicationType === 'teamname') defaultText = 'TEAM';
        if (window.selectedApplicationType === 'playername') defaultText = 'PLAYER';
        const globalColors = window.selectedColors || ['#FFFFFF', '#000000'];
        const layer = {
            id: layerId, type: window.selectedApplicationType, view, partId,
            text: defaultText, fontSize: 500,
            fontFamily: window.backendFonts?.[0] ? 'font_' + window.backendFonts[0].id : 'Arial Black',
            fill: globalColors[0] || '#FFFFFF', stroke: globalColors[1] || '#000000',
            x: 0, y: 0, rotation: 0,
            flipX: 1, flipY: 1, _flipState: 0,
            scaleX: 1, scaleY: 1,
            outlineStyle: window.currentOutlineStyle,
            outlineColors: { ...window.outlineColors }
        };
        if (!window.applicationsApplied[view]) window.applicationsApplied[view] = {};
        if (!window.applicationsApplied[view][partId]) window.applicationsApplied[view][partId] = [];
        window.applicationsApplied[view][partId].push(layer);
console.log('AFTER ADD APPLICATION =', JSON.stringify(window.applicationsApplied, null, 2));
        addApplicationToSvg(layer);
        updateApplicationLayersList();
        closeApplicationModal();
        openApplicationsSidebar();
        selectApplicationLayer(layerId);
        if (window.saveCustomizations) window.saveCustomizations();
    };

    // ============================================================
    // ✅ FIX 2: addApplicationToSvg — POORA REPLACE KARO
    // ============================================================

  window.addApplicationToSvg = function (layer) {

  if (layer.view !== window.currentView) return;




    if (layer.type === 'direct-mascot') {
        if (layer.mascotSvg) applyDirectMascotToLayer(layer.mascotSvg, layer.id, false);
        return;
    }

    const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
    if (!mainSvg) {
        setTimeout(() => window.addApplicationToSvg(layer), 300);
        return;
    }

    let defs = mainSvg.querySelector('defs');
    if (!defs) {
        defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        mainSvg.insertBefore(defs, mainSvg.firstChild);
    }

    const partElement = mainSvg.querySelector(`#${layer.partId}`);
    if (!partElement) {
        console.warn('Part not found', layer.partId);
        return;
    }

    // ===== HARD CLEAN SAME LAYER =====
    const oldGroup = mainSvg.querySelector(`#app-group-${layer.id}`);
    if (oldGroup) oldGroup.remove();

    const oldText = mainSvg.querySelector(`#${layer.id}`);
    if (oldText) oldText.remove();

    mainSvg.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(e => e.remove());

    const oldClip = defs.querySelector(`#clip-${layer.id}`);
    if (oldClip) oldClip.remove();

    const oldPattern = defs.querySelector(`#text-pattern-${layer.id}`);
    if (oldPattern) oldPattern.remove();

    const oldMascot = defs.querySelector(`#text-mascot-${layer.id}`);
    if (oldMascot) oldMascot.remove();

    // ===== CLIP =====
    const clipId = `clip-${layer.id}`;
    const clip = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
    clip.setAttribute('id', clipId);

    const clone = partElement.cloneNode(true);
    clone.removeAttribute('id');
    clone.removeAttribute('clip-path');
    clone.removeAttribute('mask');

    clip.appendChild(clone);
    defs.appendChild(clip);

    // ===== GROUP =====
    const layerGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    layerGroup.setAttribute('id', `app-group-${layer.id}`);
    layerGroup.setAttribute('clip-path', `url(#${clipId})`);
    mainSvg.appendChild(layerGroup);

    // ===== TEXT =====
    const bbox = partElement.getBBox();
    const cx = bbox.x + bbox.width / 2;
    const cy = bbox.y + bbox.height / 2;

    const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
    text.setAttribute('id', layer.id);
    text.setAttribute('x', cx + (layer.x || 0));
    text.setAttribute('y', cy + (layer.y || 0));
    text.setAttribute('font-size', layer.fontSize || 500);
    text.style.fontFamily = layer.fontFamily || 'Arial Black';
    text.setAttribute('fill', layer.fill || '#FFFFFF');
    text.setAttribute('stroke', layer.stroke || '#000000');
    text.setAttribute('stroke-width', layer.strokeWidth || 5);
    text.setAttribute('text-anchor', 'middle');
    text.setAttribute('dominant-baseline', 'middle');
    text.setAttribute('paint-order', 'stroke fill');
    text.setAttribute('stroke-linejoin', 'miter');
    text.style.cursor = 'move';
    text.textContent = layer.text || '';

    if (layer.letterSpacing) {
        text.style.letterSpacing = layer.letterSpacing + 'px';
    }

    layerGroup.appendChild(text);

    makeDraggable(text, layer);

    text.addEventListener('click', e => {
        e.stopPropagation();
        selectApplicationLayer(layer.id);
    });

    if (typeof _applyFlipTransform === 'function') {
        _applyFlipTransform(layer, mainSvg);
    }

    if (layer.outlineStyle) {
        window.currentOutlineStyle = layer.outlineStyle;
        if (layer.outlineColors) window.outlineColors = { ...layer.outlineColors };
        applyOutlineStyleToText(layer.id);
    }
};


    // ============================================================
    // ✅ FIX 3: applyDirectMascotToLayer — POORA REPLACE KARO
    // ============================================================

    window.applyDirectMascotToLayer = function (svgContent, forcedLayerId, fromModal) {

        if (window.selectingMascotForText && window.currentApplicationLayer) {
            applyMascotToText(svgContent);
            window.selectingMascotForText = false;
            return;
        }

        const layerId = forcedLayerId || window.currentApplicationLayer;
        if (!layerId) return;
        const layer = findLayerById(layerId);
        if (!layer || layer.type !== 'direct-mascot') return;
        const mainSvg = window.getMainSvg();
        if (!mainSvg) return;

        const savedFlipX = layer.flipX || 1;
        const savedFlipY = layer.flipY || 1;
        const savedFlipState = layer._flipState || 0;

        let defs = mainSvg.querySelector('defs');
        if (!defs) {
            defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            mainSvg.insertBefore(defs, mainSvg.firstChild);
        }

        const partElement = mainSvg.querySelector(`#${layer.partId}`);
        if (!partElement) { console.warn('Part not found', layer.partId); return; }

        const clipId = `clip-${layerId}`;
        if (!defs.querySelector(`#${clipId}`)) {
            const clip = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
            clip.setAttribute('id', clipId);
            const clone = partElement.cloneNode(true);
            clone.removeAttribute('id');
            clip.appendChild(clone);
            defs.appendChild(clip);
        }

        const layerGroupId = `app-group-${layerId}`;
        let layerGroup = mainSvg.querySelector(`#${layerGroupId}`);
        if (layerGroup) layerGroup.remove();

        layerGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        layerGroup.setAttribute('id', layerGroupId);
        layerGroup.setAttribute('clip-path', `url(#${clipId})`);

        // ✅ KEY FIX: hamesha LAST mein append karo
        mainSvg.appendChild(layerGroup);

        const bbox = partElement.getBBox();
        const cx = bbox.x + bbox.width / 2;
        const cy = bbox.y + bbox.height / 2;

        const parser = new DOMParser();
        const doc = parser.parseFromString(svgContent, 'image/svg+xml');
        let mascotSvg = doc.documentElement;
        if (!mascotSvg || mascotSvg.nodeName !== 'svg') { alert('Mascot SVG load failed'); return; }
        mascotSvg = mascotSvg.cloneNode(true);

        const vb = (mascotSvg.getAttribute('viewBox') || '0 0 100 100').split(' ').map(Number);
        mascotSvg.querySelectorAll('rect,polygon,circle,ellipse').forEach(el => {
            const x = parseFloat(el.getAttribute('x') || 0);
            const y = parseFloat(el.getAttribute('y') || 0);
            const w = parseFloat(el.getAttribute('width') || 0);
            const h = parseFloat(el.getAttribute('height') || 0);
            if (x <= 1 && y <= 1 && w >= vb[2] * 0.7 && h >= vb[3] * 0.7) el.remove();
        });
        mascotSvg.querySelectorAll('*').forEach(el => {
            const style = el.getAttribute('style') || '';
            const fill = el.getAttribute('fill') || '';
            const isWhite = fill === '#fff' || fill === '#ffffff' || fill === 'white' ||
                style.includes('fill:#fff') || style.includes('fill:white') || style.includes('fill:#ffffff');
            const tag = el.tagName.toLowerCase();
            if (isWhite && (tag === 'rect' || tag === 'polygon')) el.remove();
        });

        if (!mascotSvg.getAttribute('viewBox')) mascotSvg.setAttribute('viewBox', '0 0 100 100');
        mascotSvg.style.background = 'transparent';

        const mascotSize = Math.min(bbox.width, bbox.height) * (layer.mascotScaleX || 1);
        mascotSvg.setAttribute('width', mascotSize);
        mascotSvg.setAttribute('height', mascotSize);
        mascotSvg.setAttribute('preserveAspectRatio', 'xMidYMid meet');

        const mascotX = cx - mascotSize / 2 + (layer.x || 0);
        const mascotY = cy - mascotSize / 2 + (layer.y || 0);
        mascotSvg.setAttribute('x', mascotX);
        mascotSvg.setAttribute('y', mascotY);
        mascotSvg.setAttribute('id', layerId);
        mascotSvg.style.cursor = 'default';
        if (layer.mascotOpacity !== undefined) mascotSvg.setAttribute('opacity', layer.mascotOpacity / 100);

        layerGroup.appendChild(mascotSvg);

        layer.mascotSvg = svgContent;
        layer.mascotId = layerId;
        layer._cx = cx;
        layer._cy = cy;
        layer._mascotSize = mascotSize;
        layer._selectedColorCount = window.selectedMascotColorCount || (window.selectedColors ? window.selectedColors.length : 6);
        layer.flipX = savedFlipX;
        layer.flipY = savedFlipY;
        layer._flipState = savedFlipState;

        setTimeout(() => {
            _applyFlipTransform(layer, mainSvg);
        }, 50);

        mascotSvg.addEventListener('click', e => { e.stopPropagation(); selectApplicationLayer(layerId); });

        window.currentApplicationLayer = layerId;
        updateApplicationLayersList();
        selectApplicationLayer(layerId);
        if (window.saveCustomizations) window.saveCustomizations();
    };


    // ============================================================
    // =================== CORE TRANSFORM ENGINE ===================
    // FIX: Rotation + Flip properly combined using SVG transform
    // ============================================================

    /**
     * Master transform function — handles flip + rotation correctly.
     * Order: translate-to-center → flip → rotate → translate-back
     * This ensures rotation and flip NEVER conflict.
     */
    window._applyFlipTransform = function (layer, mainSvg) {
        if (!mainSvg) mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg || !layer) return;

        const el = mainSvg.querySelector('#' + layer.id);
        if (!el) return;

        const flipX = layer.flipX || 1;
        const flipY = layer.flipY || 1;
        const rot = layer.rotation || 0;

        if (layer.type === 'direct-mascot') {
            // Mascot center = _cx + x offset, _cy + y offset
            const cx = (layer._cx || 0) + (layer.x || 0);
            const cy = (layer._cy || 0) + (layer.y || 0);

            // Build transform: translate to center, flip, rotate, translate back
            let t = `translate(${cx} ${cy})`;
            if (rot !== 0) t += ` rotate(${rot})`;
            if (flipX !== 1 || flipY !== 1) t += ` scale(${flipX} ${flipY})`;
            t += ` translate(${-cx} ${-cy})`;

            el.setAttribute('transform', t);
        } else {
            // Text element: use its current x,y as pivot
            const x = parseFloat(el.getAttribute('x') || 0);
            const y = parseFloat(el.getAttribute('y') || 0);

            // scaleX/scaleY from layer
            const sx = (layer.scaleX || 1) * flipX;
            const sy = (layer.scaleY || 1) * flipY;

            let t = '';
            // Only add scale if needed
            if (sx !== 1 || sy !== 1) {
                t += `translate(${x} ${y}) scale(${sx} ${sy}) translate(${-x} ${-y}) `;
            }
            if (rot !== 0) {
                t += `rotate(${rot} ${x} ${y})`;
            }

            if (t.trim()) {
                el.setAttribute('transform', t.trim());
            } else {
                el.removeAttribute('transform');
            }

            // Sync outline layers
            mainSvg.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => {
                const ox = parseFloat(o.getAttribute('x') || x);
                const oy = parseFloat(o.getAttribute('y') || y);
                let ot = '';
                if (sx !== 1 || sy !== 1) {
                    ot += `translate(${ox} ${oy}) scale(${sx} ${sy}) translate(${-ox} ${-oy}) `;
                }
                if (rot !== 0) {
                    ot += `rotate(${rot} ${ox} ${oy})`;
                }
                if (ot.trim()) o.setAttribute('transform', ot.trim());
                else o.removeAttribute('transform');
            });
        }

        // Update plus icon position after transform
        setTimeout(() => _updatePlusIconPosition(layer.id), 20);
    };

    function _applyFlipTransform(layer, mainSvg) { window._applyFlipTransform(layer, mainSvg); }

    // ============================================================
    // =================== FLIP LOGIC ===================
    // 4-state cycle: normal → flipX → flipY → flipBoth
    // ============================================================

    window.flipApplicationLayer = function (layerId, event) {
        if (event) event.stopPropagation();
        const layer = window.findLayerById ? window.findLayerById(layerId) : null;
        if (!layer) return;

        const current = layer._flipState || 0;
        const next = (current + 1) % 4;
        layer._flipState = next;

        switch (next) {
            case 0: layer.flipX = 1; layer.flipY = 1; break; // normal
            case 1: layer.flipX = -1; layer.flipY = 1; break; // left-right
            case 2: layer.flipX = 1; layer.flipY = -1; break; // top-bottom
            case 3: layer.flipX = -1; layer.flipY = -1; break; // both
        }

        _applyFlipTransform(layer, window.getMainSvg ? window.getMainSvg() : null);
        _updateFlipBtnUI(layerId, layer);
        if (window.saveCustomizations) window.saveCustomizations();
        setTimeout(() => { _showPlusIcon(layerId); }, 100);
    };

    function _updateFlipBtnUI(layerId, layer) {
        const icons = { 0: '↔', 1: '↔', 2: '↕', 3: '↕↔' };
        const state = layer._flipState || 0;

        document.querySelectorAll('.application-layer-item').forEach(item => {
            if (item.dataset.layerId !== layerId) return;
            const btn = item.querySelector('.flip-btn-h');
            if (btn) {
                btn.textContent = icons[state];
                btn.style.background = state !== 0 ? '#1a1a1a' : 'transparent';
                btn.style.color = state !== 0 ? '#fff' : 'inherit';
            }
            const allDivs = item.querySelectorAll('div[onclick*="flipApplicationLayer"]');
            allDivs.forEach(d => { d.textContent = icons[state]; });
        });
    }

    // ============================================================
    // =================== DRAGGABLE (MASCOT) ===================
    // ============================================================
    window.makeMascotDraggable = function (element, layer) {
        element.style.cursor = 'default';
        // Direct drag disabled — use Plus icon
    };

    // ============================================================
    // =================== DRAGGABLE (TEXT) ===================
    // FIX: Use SVG coordinate space directly — no rotation math needed
    // ============================================================
    window.makeDraggable = function (element, layer) {
        let isDragging = false, startSvgX, startSvgY, initialLayerX, initialLayerY;

        element.addEventListener('mousedown', function (e) {
            if (e.button !== 0) return;
            isDragging = true;
            const svgPt = _clientToSvgCoord(e.clientX, e.clientY);
            startSvgX = svgPt.x;
            startSvgY = svgPt.y;
            initialLayerX = layer.x || 0;
            initialLayerY = layer.y || 0;
            e.preventDefault();
        });

        document.addEventListener('mousemove', function (e) {
            if (!isDragging) return;
            const svgPt = _clientToSvgCoord(e.clientX, e.clientY);
            const newX = Math.round(initialLayerX + (svgPt.x - startSvgX));
            const newY = Math.round(initialLayerY + (svgPt.y - startSvgY));
            _moveTextLayer(layer, newX, newY);
        });

        document.addEventListener('mouseup', function () {
            if (!isDragging) return;
            isDragging = false;
            window._hidePositionIndicator();

            // Sync sliders
            const pxSlider = document.getElementById('posX');
            const pySlider = document.getElementById('posY');
            if (pxSlider) { pxSlider.value = layer.x; appFillSlider(pxSlider); }
            if (pySlider) { pySlider.value = layer.y; appFillSlider(pySlider); }
            if (pxSlider) document.getElementById('posXValue').textContent = layer.x;
            if (pySlider) document.getElementById('posYValue').textContent = layer.y;

            if (layer.hasPattern && layer.patternId) _updatePatternPosition(layer);
            if (layer.hasMascot && layer.mascotId) _updateMascotPatternPosition(layer);
            if (window.saveCustomizations) window.saveCustomizations();
        });
    };

    /**
     * Core move: update layer.x/y, reposition text element, reapply transform.
     * Works correctly regardless of flip or rotation state.
     */
    function _moveTextLayer(layer, newX, newY) {
        const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return;

        layer.x = newX;
        layer.y = newY;

        const partEl = mainSvg.querySelector('#' + layer.partId);
        if (!partEl) return;
        const bbox = partEl.getBBox();
        const cx = bbox.x + bbox.width / 2;
        const cy = bbox.y + bbox.height / 2;

        const textEl = mainSvg.querySelector('#' + layer.id);
        if (textEl) {
            textEl.setAttribute('x', cx + newX);
            textEl.setAttribute('y', cy + newY);
            // Sync outline elements
            mainSvg.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => {
                o.setAttribute('x', cx + newX);
                o.setAttribute('y', cy + newY);
            });
            // Re-apply flip+rotation transform
            _applyFlipTransform(layer, mainSvg);
        }

        _updatePlusIconPosition(layer.id);
    }

    // ============================================================
    // =================== LAYER LIST ===================
    // ============================================================

 // =================== LAYER LIST ===================
window.updateApplicationLayersList = function () {
    const container = document.getElementById('applicationLayersList');
    if (!container) return;
    container.innerHTML = '';

    if (!window.applicationsApplied || !Object.keys(window.applicationsApplied).length) {
        container.innerHTML = '<p style="color:#999;text-align:center;padding:20px;font-size:13px;">No applications added yet</p>';
        return;
    }

    let allLayerEntries = [];
    Object.entries(window.applicationsApplied).forEach(([view, parts]) => {
        Object.entries(parts).forEach(([partId, layers]) => {
            layers.forEach((layer, index) => {
                allLayerEntries.push({ view, partId, layer, index });
            });
        });
    });

    allLayerEntries.reverse();   // latest on top

    let layerNum = 1;
    allLayerEntries.forEach(({ view, partId, layer, index }) => {
        const viewShort = view.charAt(0).toUpperCase();
        const isActive = window.currentApplicationLayer === layer.id;
        const labelText = layer.type === 'direct-mascot'
            ? (layer.mascotTitle || 'Mascot')
            : (layer.text || 'Empty');

        const item = document.createElement('div');
        item.className = 'application-layer-item';
        item.setAttribute('draggable', 'true');
        item.dataset.layerId = layer.id;
        item.dataset.view = view;
        item.dataset.partId = partId;
        item.dataset.index = index;

        item.style.cssText = `
            display:flex; align-items:center; gap:10px; width:100%; padding:10px 12px; margin-bottom:6px;
            background:${isActive ? '#383838' : '#585858'}; color:#fff; border:none; border-radius:6px;
            cursor:grab; transition:all 0.2s; user-select:none;
        `;

        const flipState = layer._flipState || 0;
        const flipIcons = { 0: '↔', 1: '↔', 2: '↕', 3: '↕↔' };

        item.innerHTML = `
            <div style="background:#2f2f2f;color:#fff;font-weight:700;font-size:13px;padding:4px 8px;border-radius:4px;flex-shrink:0;">
                #${layerNum}
            </div>
            <div style="flex:1; font-weight:600; font-size:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                ${labelText.toUpperCase()}
            </div>
            <div style="display:flex; gap:10px; align-items:center; flex-shrink:0;">
                <span style="background:#2f2f2f;color:#fff;padding:2px 6px;font-size:11px;border-radius:3px;">${viewShort}</span>
                <div onclick="window.flipApplicationLayer('${layer.id}',event)" style="cursor:pointer; font-size:16px;">${flipIcons[flipState]}</div>
                <div onclick="duplicateApplicationLayer('${layer.id}',event)" style="cursor:pointer; font-size:16px;">⧉</div>
                <div onclick="removeApplicationLayer('${layer.id}',event)" style="cursor:pointer; font-size:18px; color:#fffff;">×</div>
            </div>
        `;

        // Click to select
        item.onclick = function (e) {
            if (e.target.closest('div[onclick]')) return; // buttons pe click na ho
            selectApplicationLayer(layer.id);
        };

        // =================== DRAG & DROP (Real-time Feel) ===================
        item.addEventListener('dragstart', e => {
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', layer.id);
            item.style.opacity = '0.4';
            window._draggingLayerId = layer.id;
            window._draggingView = view;
            window._draggingPartId = partId;
        });

        item.addEventListener('dragend', () => {
            item.style.opacity = '1';
            window._draggingLayerId = null;
        });

        item.addEventListener('dragover', e => {
            e.preventDefault();
            if (window._draggingLayerId !== layer.id) {
                item.style.borderTop = '3px solid #000';
            }
        });

        item.addEventListener('dragleave', () => {
            item.style.borderTop = 'none';
        });

        item.addEventListener('drop', e => {
            e.preventDefault();
            item.style.borderTop = 'none';

            const fromId = window._draggingLayerId;
            if (!fromId || fromId === layer.id) return;
            if (window._draggingView !== view || window._draggingPartId !== partId) return;

            const arr = window.applicationsApplied[view][partId];
            if (!arr) return;

            const fromIdx = arr.findIndex(l => l.id === fromId);
            const toIdx = arr.findIndex(l => l.id === layer.id);

            if (fromIdx === -1 || toIdx === -1) return;

            // Reorder array
            const [movedLayer] = arr.splice(fromIdx, 1);
            arr.splice(toIdx, 0, movedLayer);

            // 🔥 Real-time SVG reorder
            reorderSvgLayers(view, partId, arr);

            // List refresh
            updateApplicationLayersList();

            if (window.saveCustomizations) window.saveCustomizations();
        });

        container.appendChild(item);
        layerNum++;
    });
};

    window.duplicateApplicationLayer = function (layerId, event) {
        if (event) event.stopPropagation();
        const original = findLayerById(layerId);
        if (!original) return;
        const copy = JSON.parse(JSON.stringify(original));
        copy.id = `app-${Date.now()}`;
        copy.x += 20; copy.y += 20;
        if (!window.applicationsApplied[original.view]) window.applicationsApplied[original.view] = {};
        if (!window.applicationsApplied[original.view][original.partId]) window.applicationsApplied[original.view][original.partId] = [];
        window.applicationsApplied[original.view][original.partId].push(copy);
        addApplicationToSvg(copy);
        updateApplicationLayersList();
        selectApplicationLayer(copy.id);
        if (window.saveCustomizations) window.saveCustomizations();
    };

    // ============================================================
    // =================== SELECT LAYER ===================
    // ============================================================

    window.selectApplicationLayer = function (layerId) {
        window.currentApplicationLayer = layerId;
        activateTab('applicationBtn');
        openApplicationsSidebar();
        const foundLayer = findLayerById(layerId);
        if (!foundLayer) { console.warn('Layer not found:', layerId); return; }

        if (foundLayer.view && window.currentView !== foundLayer.view) {
            if (window.switchView) window.switchView(foundLayer.view);
            setTimeout(() => window.initializeApplicationsOnLoad(), 400);
        }

        updateApplicationLayersList();
        document.getElementById('applicationLayerControls').style.display = 'block';

        if (foundLayer.type === 'direct-mascot') {
            showDirectMascotControls(foundLayer);
        } else {
            showTextLayerControls(foundLayer);
        }

        setTimeout(() => {
            _showPlusIcon(layerId);
            if (foundLayer && foundLayer.type === 'direct-mascot') {
                _hideMascotBox();
            }
        }, 80);
    };

    // ============================================================
    // =================== PLUS DRAG ICON ===================
    // ============================================================

    var _plusState = {
        layerId: null,
        isDragging: false,
        startClient: { x: 0, y: 0 },
        startLayerX: 0,
        startLayerY: 0
    };

    function _ensurePlusIcon() {
        let icon = document.getElementById('appPlusDragIcon');
        if (!icon) {
            icon = document.createElement('div');
            icon.id = 'appPlusDragIcon';
            icon.innerHTML = '<i class="fa-sharp fa-solid fa-up-down-left-right"></i>';
            icon.style.cssText = [
                'position:fixed',
                'width:28px',
                'height:28px',
                'color:#ffffff',
                'font-size:28px',
                'font-weight:200',
                'line-height:28px',
                'text-align:center',
                'cursor:grab',
                'z-index:10000',
                'transform:translate(-50%,-50%)',
                'user-select:none',
                'display:none',
                'pointer-events:all',
                '-webkit-text-stroke:1px #000000',
            ].join(';');

            document.body.appendChild(icon);
            icon.addEventListener('mousedown', _plusDragStart);
        }
        return icon;
    }

    function _plusDragStart(e) {
        if (e.button !== 0) return;
        e.preventDefault();
        e.stopPropagation();

        const layerId = _plusState.layerId;
        if (!layerId) return;
        const layer = findLayerById(layerId);
        if (!layer) return;

        _plusState.isDragging = true;
        _plusState.startClient = { x: e.clientX, y: e.clientY };
        _plusState.startSvgPos = _clientToSvgCoord(e.clientX, e.clientY);
        _plusState.startLayerX = layer.x || 0;
        _plusState.startLayerY = layer.y || 0;

        const icon = document.getElementById('appPlusDragIcon');
        if (icon) { icon.style.cursor = 'grabbing'; }

        document.addEventListener('mousemove', _plusDragMove);
        document.addEventListener('mouseup', _plusDragEnd);
    }

    function _plusDragMove(e) {
        if (!_plusState.isDragging) return;
        const layerId = _plusState.layerId;
        const layer = findLayerById(layerId);
        if (!layer) return;

        const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return;

        // Use SVG coordinate space — no rotation math needed
        const curSvg = _clientToSvgCoord(e.clientX, e.clientY);
        const dx = curSvg.x - _plusState.startSvgPos.x;
        const dy = curSvg.y - _plusState.startSvgPos.y;

        const newX = Math.round(_plusState.startLayerX + dx);
        const newY = Math.round(_plusState.startLayerY + dy);

        if (layer.type === 'direct-mascot') {
            layer.x = newX;
            layer.y = newY;
            const el = mainSvg.querySelector('#' + layerId);
            if (el) {
                const sz = layer._mascotSize || 100;
                el.setAttribute('x', (layer._cx || 0) - sz / 2 + newX);
                el.setAttribute('y', (layer._cy || 0) - sz / 2 + newY);
                _applyFlipTransform(layer, mainSvg);
            }
            // Sync mascot sliders
            const mxEl = document.getElementById('mascotDirectPosX');
            const myEl = document.getElementById('mascotDirectPosY');
            if (mxEl) { mxEl.value = newX; document.getElementById('mascotDirectPosXValue').textContent = newX; }
            if (myEl) { myEl.value = newY; document.getElementById('mascotDirectPosYValue').textContent = newY; }
        } else {
            _moveTextLayer(layer, newX, newY);
            // Sync text sliders
            const pxEl = document.getElementById('posX');
            const pyEl = document.getElementById('posY');
            if (pxEl) { pxEl.value = newX; document.getElementById('posXValue').textContent = newX; appFillSlider(pxEl); }
            if (pyEl) { pyEl.value = newY; document.getElementById('posYValue').textContent = newY; appFillSlider(pyEl); }

            if (layer.hasPattern && layer.patternId) _updatePatternPosition(layer);
            if (layer.hasMascot && layer.mascotId) _updateMascotPatternPosition(layer);
        }

        // Update plus icon screen position
        const center = layer.type === 'direct-mascot' ? _getMascotVisualCenter(layerId) : getTextVisualCenter(layerId);
        if (center) {
            const icon = document.getElementById('appPlusDragIcon');
            if (icon && icon.style.display !== 'none') {
                icon.style.left = center.x + 'px';
                icon.style.top = center.y + 'px';
            }
        }

        window._showPositionIndicator(newX, newY);
    }

    function _plusDragEnd() {
        _plusState.isDragging = false;
        window._hidePositionIndicator();

        const icon = document.getElementById('appPlusDragIcon');
        if (icon) { icon.style.cursor = 'grab'; }

        document.removeEventListener('mousemove', _plusDragMove);
        document.removeEventListener('mouseup', _plusDragEnd);

        if (_plusState.layerId) {
            const layer = findLayerById(_plusState.layerId);
            if (layer && layer.hasPattern && layer.patternId) _updatePatternPosition(layer);
            if (layer && layer.hasMascot && layer.mascotId) _updateMascotPatternPosition(layer);
        }

        if (window.saveCustomizations) window.saveCustomizations();
    }

    function _clientToSvgCoord(clientX, clientY) {
        const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return { x: 0, y: 0 };
        const pt = mainSvg.createSVGPoint();
        pt.x = clientX; pt.y = clientY;
        return pt.matrixTransform(mainSvg.getScreenCTM().inverse());
    }

    function _showPlusIcon(layerId) {
        const layer = findLayerById(layerId);
        if (!layer) { _hidePlusIcon(); return; }

        let center = null;
        if (layer.type === 'direct-mascot') {
            center = _getMascotVisualCenter(layerId);
        } else {
            center = getTextVisualCenter(layerId);
        }

        if (!center) { _hidePlusIcon(); return; }

        const icon = _ensurePlusIcon();
        icon.style.left = center.x + 'px';
        icon.style.top = center.y + 'px';
        icon.style.display = 'block';
        _plusState.layerId = layerId;
    }

    function _updatePlusIconPosition(layerId) {
        if (_plusState.layerId !== layerId) return;
        const layer = findLayerById(layerId);
        if (!layer) return;
        const center = layer.type === 'direct-mascot' ? _getMascotVisualCenter(layerId) : getTextVisualCenter(layerId);
        if (!center) return;
        const icon = document.getElementById('appPlusDragIcon');
        if (icon && icon.style.display !== 'none') {
            icon.style.left = center.x + 'px';
            icon.style.top = center.y + 'px';
        }
    }

    function _hidePlusIcon() {
        const icon = document.getElementById('appPlusDragIcon');
        if (icon) icon.style.display = 'none';
        _plusState.layerId = null;
    }

    function getTextVisualCenter(layerId) {
        const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return null;
        const el = mainSvg.querySelector('#' + layerId);
        if (!el) return null;
        try {
            const rect = el.getBoundingClientRect();
            if (rect.width === 0 && rect.height === 0) return null;
            return { x: rect.left + rect.width / 2, y: rect.top + rect.height / 2 };
        } catch (e) { return null; }
    }

    document.addEventListener('click', function (e) {
        const icon = document.getElementById('appPlusDragIcon');
        if (icon && icon.contains(e.target)) return;
        const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg || !mainSvg.contains(e.target)) return;
        let el = e.target;
        let clickedLayer = false;
        while (el && el !== mainSvg) {
            if (el.id && el.id.startsWith('app-group-')) { clickedLayer = true; break; }
            if (el.id && window.findLayerById && window.findLayerById(el.id)) { clickedLayer = true; break; }
            el = el.parentElement;
        }
        if (!clickedLayer) {
            _hidePlusIcon();
            window.currentApplicationLayer = null;
            const controls = document.getElementById('applicationLayerControls');
            if (controls) controls.style.display = 'none';
            if (window.updateApplicationLayersList) window.updateApplicationLayersList();
        }
    });

    window.addEventListener('resize', () => { if (_plusState.layerId) _updatePlusIconPosition(_plusState.layerId); });
    window.addEventListener('scroll', () => { if (_plusState.layerId) _updatePlusIconPosition(_plusState.layerId); }, true);

    // Modal observer — hide/show plus icon
    (function () {
        const observer = new MutationObserver(mutations => {
            mutations.forEach(m => {
                if (m.type !== 'attributes' || m.attributeName !== 'style') return;
                const el = m.target;
                if (!el.id || !el.id.toLowerCase().includes('modal')) return;
                const visible = el.style.display !== 'none' && el.style.display !== '';
                if (visible) { _hidePlusIcon(); }
                else if (window.currentApplicationLayer) { setTimeout(() => _showPlusIcon(window.currentApplicationLayer), 200); }
            });
        });
        function observeModals() { document.querySelectorAll('[id*="modal"],[id*="Modal"]').forEach(m => { observer.observe(m, { attributes: true, attributeFilter: ['style'] }); }); }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => setTimeout(observeModals, 500));
        else setTimeout(observeModals, 500);
    })();

    // ============================================================
    // =================== MASCOT BOX (disabled — use Plus icon) ===================
    // ============================================================

    var _mascotBox = { layerId: null };

    function _ensureMascotBox() {
        let box = document.getElementById('appMascotDragBox');
        if (!box) {
            box = document.createElement('div');
            box.id = 'appMascotDragBox';
            box.style.cssText = 'position:fixed;border:2px dashed #000;border-radius:4px;background:transparent;z-index:9998;display:none;pointer-events:none;box-sizing:border-box;';
            document.body.appendChild(box);
        }
        return box;
    }

    function _getMascotVisualCenter(layerId) {
        const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return null;
        const mascotEl = mainSvg.querySelector('#' + layerId);
        if (!mascotEl) return null;
        try {
            const rect = mascotEl.getBoundingClientRect();
            if (rect.width === 0 && rect.height === 0) return null;
            return { x: rect.left + rect.width / 2, y: rect.top + rect.height / 2 };
        } catch (e) { return null; }
    }

    function _showMascotBox(layerId) { _hideMascotBox(); } // disabled
    function _hideMascotBox() {
        const box = document.getElementById('appMascotDragBox');
        if (box) box.style.display = 'none';
        _mascotBox.layerId = null;
    }

    window.addEventListener('resize', () => { if (_mascotBox.layerId) _showMascotBox(_mascotBox.layerId); });

    // ============================================================
    // =================== DIRECT MASCOT CONTROLS ===================
    // ============================================================

    function showDirectMascotControls(layer) {
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

        sync('directMascotScale', 'directMascotScaleValue', Math.round((layer.mascotScaleX || 1) * 100));
        sync('directMascotOpacity', 'directMascotOpacityValue', layer.mascotOpacity ?? 100);
        sync('directMascotRotation', 'directMascotRotationValue', layer.rotation || 0);
        sync('mascotDirectPosX', 'mascotDirectPosXValue', layer.x || 0);
        sync('mascotDirectPosY', 'mascotDirectPosYValue', layer.y || 0);

        setTimeout(() => _renderDirectMascotColors(layer), 100);
        setTimeout(() => _showMascotBox(layer.id), 80);
    }

    function _renderDirectMascotColors(layer) {
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
    }

    function _detectColorsFromPng(dataUrl, layer, container) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();

        img.onload = function () {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);

            const data = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
            const rawColors = [];
            const step = Math.max(1, Math.floor(data.length / 4 / 5000));

            for (let i = 0; i < data.length; i += 4 * step) {
                if (data[i + 3] < 128) continue;
                rawColors.push({ r: data[i], g: data[i + 1], b: data[i + 2] });
            }

            const maxColors = (layer._selectedColorCount && layer._selectedColorCount > 0)
                ? layer._selectedColorCount
                : (window.selectedColors ? window.selectedColors.length : 3);

            const clustered = _kMeansColors(rawColors, maxColors);

            layer._detectedColors = clustered;
            layer._detectedRgbs = clustered.map(hex => _hexToRgb(hex));

            if (!clustered.length) {
                container.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;">No colors detected</p>';
                return;
            }

            _buildColorSwatches(clustered, layer, container);
        };

        img.onerror = () => { container.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;">Could not load image</p>'; };
        img.crossOrigin = 'anonymous';
        img.src = dataUrl;
    }

    function _kMeansColors(pixels, k) {
        if (!pixels.length || k <= 0) return [];
        let centroids = [pixels[0]];
        for (let c = 1; c < k; c++) {
            let maxDist = -1, farthest = pixels[0];
            pixels.forEach(p => {
                let minDist = Infinity;
                centroids.forEach(cent => {
                    const d = Math.sqrt(Math.pow(p.r - cent.r, 2) + Math.pow(p.g - cent.g, 2) + Math.pow(p.b - cent.b, 2));
                    if (d < minDist) minDist = d;
                });
                if (minDist > maxDist) { maxDist = minDist; farthest = p; }
            });
            centroids.push(farthest);
        }
        for (let iter = 0; iter < 10; iter++) {
            const clusters = centroids.map(() => ({ r: 0, g: 0, b: 0, count: 0 }));
            pixels.forEach(p => {
                let nearest = 0, nearestDist = Infinity;
                centroids.forEach((c, idx) => {
                    const dist = Math.sqrt(Math.pow(p.r - c.r, 2) + Math.pow(p.g - c.g, 2) + Math.pow(p.b - c.b, 2));
                    if (dist < nearestDist) { nearestDist = dist; nearest = idx; }
                });
                clusters[nearest].r += p.r;
                clusters[nearest].g += p.g;
                clusters[nearest].b += p.b;
                clusters[nearest].count++;
            });
            centroids = clusters.map((c, i) => {
                if (c.count === 0) return centroids[i];
                return { r: Math.round(c.r / c.count), g: Math.round(c.g / c.count), b: Math.round(c.b / c.count), count: c.count };
            });
        }
        return centroids.filter(c => c.count > 0).sort((a, b) => (b.count || 0) - (a.count || 0))
            .map(c => '#' + [c.r, c.g, c.b].map(v => Math.max(0, Math.min(255, v)).toString(16).padStart(2, '0')).join(''));
    }

    function _buildColorSwatches(detectedColors, layer, container) {
        container.innerHTML = '';
        const maxColors = layer._selectedColorCount || (window.selectedColors ? window.selectedColors.length : 4);
        detectedColors = detectedColors.slice(0, maxColors);

        if (!detectedColors.length) {
            container.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;">No colors detected</p>';
            return;
        }

        layer._detectedColors = detectedColors;
        if (!layer._colorMap) layer._colorMap = {};

        const backendColors = (window.selectedColors?.length) ? window.selectedColors :
            (window.backendColors || []).map(c => c.code || c);
        const palette = backendColors.length ? backendColors :
            ['#FF0000', '#FF6600', '#FFFF00', '#00FF00', '#0000FF', '#800080', '#FFFFFF', '#000000'];

        detectedColors.forEach(detectedHex => {
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;gap:8px;margin-bottom:10px;';

            const fromBox = document.createElement('div');
            fromBox.style.cssText = `width:26px;height:26px;border-radius:5px;border:2px solid #ccc;flex-shrink:0;background:${detectedHex}`;

            const arrow = document.createElement('span');
            arrow.textContent = '→';
            arrow.style.cssText = 'font-size:13px;color:#888;flex-shrink:0;';

            const swatchRow = document.createElement('div');
            swatchRow.style.cssText = 'display:flex;flex-wrap:wrap;gap:4px;';

            const currentReplacement = layer._colorMap[detectedHex.toLowerCase()] || null;

            palette.forEach(hex => {
                const isSelected = currentReplacement && hex.toLowerCase() === currentReplacement.toLowerCase();
                const box = document.createElement('div');
                box.style.cssText = `width:22px;height:22px;border-radius:4px;cursor:pointer;box-sizing:border-box;position:relative;display:flex;align-items:center;justify-content:center;background:${hex};border:${isSelected ? '3px solid #1a1a1a' : '2px solid #ddd'};`;

                if (isSelected) {
                    const check = document.createElement('span');
                    check.textContent = '✓';
                    check.style.cssText = `font-size:13px;font-weight:900;line-height:1;color:${_getContrastColor(hex)};`;
                    box.appendChild(check);
                }

                box.onclick = (function (dHex, nHex, b, sRow) {
                    return function () {
                        layer._colorMap[dHex.toLowerCase()] = nHex;
                        sRow.querySelectorAll('div').forEach(x => { x.style.border = '2px solid #ddd'; x.innerHTML = ''; });
                        b.style.border = '3px solid #1a1a1a';
                        const ck = document.createElement('span');
                        ck.textContent = '✓';
                        ck.style.cssText = `font-size:13px;font-weight:900;line-height:1;color:${_getContrastColor(nHex)};`;
                        b.appendChild(ck);
                        _applyDirectMascotColorMap(layer);
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

    function _getContrastColor(hex) {
        if (!hex) return '#000000'; hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        const r = parseInt(hex.substr(0, 2), 16); const g = parseInt(hex.substr(2, 2), 16); const b = parseInt(hex.substr(4, 2), 16);
        return (0.299 * r + 0.587 * g + 0.114 * b) / 255 > 0.5 ? '#000000' : '#ffffff';
    }

    function _normalizeColor(color) {
        if (!color) return null; color = color.trim().toLowerCase();
        if (color.match(/^#[0-9a-f]{6}$/)) return color;
        if (color.match(/^#[0-9a-f]{3}$/)) return '#' + color[1] + color[1] + color[2] + color[2] + color[3] + color[3];
        return null;
    }

    function _applyDirectMascotColorMap(layer) {
        if (!layer.mascotSvg || !layer._colorMap) return;
        const parser = new DOMParser(); const doc = parser.parseFromString(layer.mascotSvg, 'image/svg+xml');
        const imgTag = doc.querySelector('image');
        if (imgTag) { const href = imgTag.getAttribute('href') || imgTag.getAttribute('xlink:href') || ''; if (href.startsWith('data:image/png')) { _applyColorMapToPng(href, layer); return; } }
        Object.entries(layer._colorMap).forEach(([oldColor, newColor]) => { doc.querySelectorAll('[fill]').forEach(el => { if (_normalizeColor(el.getAttribute('fill')) === oldColor) el.setAttribute('fill', newColor); }); });
        _replaceElementInSvg(layer, new XMLSerializer().serializeToString(doc.documentElement));
    }

    function _applyColorMapToPng(dataUrl, layer) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();

        img.onload = function () {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            const colorMap = layer._colorMap || {};
            const detectedColors = layer._detectedColors || [];
            if (!Object.keys(colorMap).length) return;

            const detectedRgbs = detectedColors.map(hex => ({ hex: hex.toLowerCase(), rgb: _hexToRgb(hex) })).filter(d => d.rgb);
            const replacementCache = {};
            Object.entries(colorMap).forEach(([dHex, rHex]) => { replacementCache[dHex.toLowerCase()] = _hexToRgb(rHex); });

            for (let i = 0; i < data.length; i += 4) {
                if (data[i + 3] < 30) continue;
                const r = data[i], g = data[i + 1], b = data[i + 2];
                let nearestHex = null, nearestDist = Infinity;
                detectedRgbs.forEach(d => {
                    const dist = Math.sqrt(Math.pow(r - d.rgb.r, 2) + Math.pow(g - d.rgb.g, 2) + Math.pow(b - d.rgb.b, 2));
                    if (dist < nearestDist) { nearestDist = dist; nearestHex = d.hex; }
                });
                if (nearestHex && replacementCache[nearestHex]) {
                    const newRgb = replacementCache[nearestHex];
                    data[i] = newRgb.r; data[i + 1] = newRgb.g; data[i + 2] = newRgb.b;
                }
            }

            ctx.putImageData(imageData, 0, 0);
            const newPngDataUrl = canvas.toDataURL('image/png', 1.0);
            const parser = new DOMParser();
            const doc = parser.parseFromString(layer.mascotSvg, 'image/svg+xml');
            const imgEl = doc.querySelector('image');
            if (imgEl) { imgEl.setAttribute('href', newPngDataUrl); if (imgEl.getAttribute('xlink:href')) imgEl.setAttribute('xlink:href', newPngDataUrl); }
            _replaceElementInSvg(layer, new XMLSerializer().serializeToString(doc.documentElement));
        };

        img.onerror = () => console.error('PNG load failed');
        img.src = dataUrl;
    }

    function _replaceElementInSvg(layer, modifiedSvg) {
        const mainSvg = window.getMainSvg ? window.getMainSvg() : null; if (!mainSvg) return;
        const svgEl = mainSvg.querySelector('#' + layer.id); if (!svgEl) return;
        const parser = new DOMParser();
        const newSvg = parser.parseFromString(modifiedSvg, 'image/svg+xml').documentElement.cloneNode(true);
        ['id', 'x', 'y', 'width', 'height', 'opacity'].forEach(a => { const v = svgEl.getAttribute(a); if (v) newSvg.setAttribute(a, v); });
        newSvg.setAttribute('preserveAspectRatio', 'xMidYMid meet');
        newSvg.style.cursor = 'default';
        svgEl.parentNode.replaceChild(newSvg, svgEl);
        newSvg.addEventListener('click', e => { e.stopPropagation(); window.selectApplicationLayer(layer.id); });
        if (window.makeMascotDraggable) window.makeMascotDraggable(newSvg, layer);
        // Re-apply flip+rotation
        setTimeout(() => _applyFlipTransform(layer, mainSvg), 50);
        if (window.saveCustomizations) window.saveCustomizations();
    }

    function _hexToRgb(hex) {
        if (!hex) return null; hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        return { r: parseInt(hex.substr(0, 2), 16), g: parseInt(hex.substr(2, 2), 16), b: parseInt(hex.substr(4, 2), 16) };
    }

    // ============================================================
    // =================== SHOW TEXT LAYER CONTROLS ===============
    // ============================================================

    function showTextLayerControls(layer) {
        _hideMascotBox();
        const appControls = document.getElementById('applicationLayerControls');
        if (appControls) { Array.from(appControls.children).forEach(child => { if (child.id !== 'directMascotControls') child.style.display = ''; }); }
        const directMascotControls = document.getElementById('directMascotControls');
        if (directMascotControls) directMascotControls.style.display = 'none';

        if (layer.outlineStyle) {
            window.currentOutlineStyle = layer.outlineStyle;
            if (layer.outlineColors) window.outlineColors = { ...layer.outlineColors };
            const displaySection = document.getElementById('currentOutlineDisplay');
            if (displaySection) displaySection.style.display = 'block';
            updateOutlineStylePreview();
            showHideColorPickers(window.currentOutlineStyle);
            populateOutlineColorPickers();
            const styleNameEl = document.getElementById('outlineStyleName');
            if (styleNameEl) {
                const styleNames = { 'single': 'Single Color', 'two-color': 'Two Color', 'two-color-shadow': 'Two Color with Drop Shadow', 'three-color': 'Three Color', 'single-shadow': 'Single Color with Drop Shadow', 'three-color-shadow': 'Three Color with Drop Shadow' };
                styleNameEl.textContent = styleNames[window.currentOutlineStyle] || window.currentOutlineStyle;
            }
        }

        updateApplicationControls(layer);

        const hasPattern = !!layer.hasPattern;
        const hasMascot = !!layer.hasMascot;

        const patternControls = document.getElementById('textPatternColorControls');
        const patternPlaceholder = document.getElementById('patternPlaceholder');
        const patternSoc = document.getElementById('textPatternSizeOpacityControls');
        if (patternControls) patternControls.style.display = hasPattern ? 'block' : 'none';
        if (patternPlaceholder) patternPlaceholder.style.display = hasPattern ? 'none' : 'block';
        if (patternSoc) patternSoc.style.display = hasPattern ? 'block' : 'none';

        const mascotControls = document.getElementById('textMascotColorControls');
        const mascotPlaceholder = document.getElementById('mascotPlaceholder');
        const mascotSoc = document.getElementById('textMascotSizeOpacityControls');
        if (mascotControls) mascotControls.style.display = hasMascot ? 'block' : 'none';
        if (mascotPlaceholder) mascotPlaceholder.style.display = hasMascot ? 'none' : 'block';
        if (mascotSoc) mascotSoc.style.display = hasMascot ? 'block' : 'none';

        if (hasPattern) {
            setTimeout(updateTextPatternButtonPreview, 50);
            const sizeSlider = document.getElementById('patternSizeTab');
            const opacitySlider = document.getElementById('patternOpacityTab');
            if (sizeSlider && layer.patternSize) { sizeSlider.value = layer.patternSize; document.getElementById('patternSizeValueTab').textContent = layer.patternSize; }
            if (opacitySlider && layer.patternOpacity) { opacitySlider.value = layer.patternOpacity; document.getElementById('patternOpacityValueTab').textContent = layer.patternOpacity; }
            renderTextPatternPalette(layer.patternSvg);
        }
        // ✅ FIX: Outline count restore per layer
        const outline1Input = document.querySelector('#outline1Section input[type="number"]');
        const outline2Input = document.querySelector('#outline2Section input[type="number"]');
        const shadowInput = document.querySelector('#shadowSection input[type="number"]');
        if (outline1Input) outline1Input.value = layer.strokeWidth || 3;
        if (outline2Input) outline2Input.value = layer.strokeWidth2 || 3;
        if (shadowInput) shadowInput.value = layer.shadowOffset || 3;
        switchTextCustomizationTab('colors');
    }

    // ============================================================
    // =================== DIRECT MASCOT SLIDER CONTROLS ===================
    // ============================================================

    window.updateDirectMascotScale = function (value) {
        // Value ko number mein convert karo
        value = parseInt(value) || 100;

        // ==================== SAFETY LIMITS ====================
        if (value < 30) value = 30;      // Minimum size (bahut chhota na ho)
        if (value > 400) value = 400;    // Maximum size (ab 4x tak ja sakta hai)

        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || layer.type !== 'direct-mascot') return;

        // Scale save karo layer mein
        layer.mascotScaleX = value / 100;
        layer.mascotScaleY = value / 100;

        const mainSvg = window.getMainSvg();
        if (!mainSvg) return;

        const partEl = mainSvg.querySelector('#' + layer.partId);
        if (!partEl) return;

        const bbox = partEl.getBBox();
        const newSize = Math.min(bbox.width, bbox.height) * (layer.mascotScaleX || 1);

        const mascotEl = mainSvg.querySelector('#' + layer.id);
        if (mascotEl) {
            const cx = layer._cx || 0;
            const cy = layer._cy || 0;

            mascotEl.setAttribute('width', newSize);
            mascotEl.setAttribute('height', newSize);
            mascotEl.setAttribute('x', cx - newSize / 2 + (layer.x || 0));
            mascotEl.setAttribute('y', cy - newSize / 2 + (layer.y || 0));

            layer._mascotSize = newSize;

            // Flip + Rotation properly apply karo
            _applyFlipTransform(layer, mainSvg);
        }

        // UI Update
        const scaleValueEl = document.getElementById('directMascotScaleValue');
        if (scaleValueEl) scaleValueEl.textContent = value;

        // Agar color mapping hai to re-apply karo
        if (layer._colorMap && Object.keys(layer._colorMap).length > 0) {
            setTimeout(() => _applyDirectMascotColorMap(layer), 100);
        }

        // Save customization
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateDirectMascotOpacity = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || layer.type !== 'direct-mascot') return;
        layer.mascotOpacity = parseInt(value);
        const el = document.getElementById(layer.id);
        if (el) el.setAttribute('opacity', value / 100);
        document.getElementById('directMascotOpacityValue').textContent = value;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateDirectMascotRotation = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || layer.type !== 'direct-mascot') return;
        layer.rotation = parseInt(value);
        const mainSvg = window.getMainSvg();
        if (mainSvg) _applyFlipTransform(layer, mainSvg);
        document.getElementById('directMascotRotationValue').textContent = value;
        if (window.saveCustomizations) window.saveCustomizations();
        setTimeout(() => _showPlusIcon(layer.id), 30);
    };

    window.updateDirectMascotPosition = function (axis, value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || layer.type !== 'direct-mascot') return;

        if (axis === 'x') { layer.x = parseInt(value); document.getElementById('mascotDirectPosXValue').textContent = value; }
        else { layer.y = parseInt(value); document.getElementById('mascotDirectPosYValue').textContent = value; }

        const mainSvg = window.getMainSvg();
        const el = mainSvg ? mainSvg.querySelector('#' + layer.id) : null;
        if (el) {
            const sz = layer._mascotSize || 100;
            el.setAttribute('x', (layer._cx || 0) - sz / 2 + layer.x);
            el.setAttribute('y', (layer._cy || 0) - sz / 2 + layer.y);
            _applyFlipTransform(layer, mainSvg);
        }
        setTimeout(() => _showPlusIcon(layer.id), 30);
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.changeDirectMascot = function () { if (!window.currentApplicationLayer) return; window.openMascotSelectModal(window.currentApplicationLayer); };

    // ============================================================
    // =================== TEXT CONTROLS ===================
    // ============================================================

    window.updateApplicationControls = function (layer) {
        if (layer.type === 'direct-mascot') return;

        const posX = document.getElementById('posX');
        const posY = document.getElementById('posY');
        const posXVal = document.getElementById('posXValue');
        const posYVal = document.getElementById('posYValue');

        if (posX) posX.value = layer.x || 0;
        if (posXVal) posXVal.textContent = layer.x || 0;
        if (posY) posY.value = layer.y || 0;
        if (posYVal) posYVal.textContent = layer.y || 0;

        // Restore font size slider
        const fsSlider = document.getElementById('fontSize');
        if (fsSlider) {
            fsSlider.value = layer.fontSize || 500;
            document.getElementById('fontSizeValue').textContent = layer.fontSize || 500;
            appFillSlider(fsSlider);
        }

        // Restore text input
        const textInput = document.getElementById('applicationText');
        if (textInput) textInput.value = layer.text || '';

        // Restore font button
        const btn = document.getElementById('selectFontBtn');
        if (btn) {
            const fontObj = window.backendFonts?.find(f => `font_${f.id}` === layer.fontFamily);
            btn.textContent = fontObj ? fontObj.name : 'Select Font';
            if (layer.fontFamily) btn.style.fontFamily = layer.fontFamily;
        }

        if (posX) appFillSlider(posX);
        if (posY) appFillSlider(posY);

        // ✅ FIX: Letter Spacing restore
        const allLetterSpacingInputs = document.querySelectorAll('input[oninput*="updateLetterSpacing"]');
        allLetterSpacingInputs.forEach(inp => { inp.value = layer.letterSpacing || 0; });

        // ✅ FIX: Width/Height restore
        const allWidthInputs = document.querySelectorAll('input[oninput*="updateTextScale(\'x\'"]');
        allWidthInputs.forEach(inp => { inp.value = Math.round((layer.scaleX || 1) * 100); });
        const allHeightInputs = document.querySelectorAll('input[oninput*="updateTextScale(\'y\'"]');
        allHeightInputs.forEach(inp => { inp.value = Math.round((layer.scaleY || 1) * 100); });

        // Restore rotation wheel
        if (window.setWheelAngle) window.setWheelAngle(layer.rotation || 0);
    };

    window.updateFontSize = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;
        layer.fontSize = parseInt(value);
        const textEl = document.getElementById(layer.id);
        if (textEl) textEl.setAttribute('font-size', value);
        document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => o.setAttribute('font-size', value));
        document.getElementById('fontSizeValue').textContent = value;
        if (layer.hasPattern && layer.patternId) setTimeout(() => _updatePatternPosition(layer), 50);
        if (layer.hasMascot && layer.mascotId) setTimeout(() => _updateMascotPatternPosition(layer), 50);
        if (window.saveCustomizations) window.saveCustomizations();
        setTimeout(() => _showPlusIcon(layer.id), 80);
    };

    window.updateApplicationText = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;
        layer.text = value;
        const textEl = document.getElementById(layer.id);
        if (textEl) textEl.textContent = value;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateFontFamily = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;
        layer.fontFamily = value;
        const textEl = document.getElementById(layer.id);
        if (textEl) textEl.style.fontFamily = value;
        const btn = document.getElementById('selectFontBtn');
        if (btn) {
            btn.style.fontFamily = value;
            const fontObj = window.backendFonts?.find(f => `font_${f.id}` === value);
            btn.textContent = fontObj ? fontObj.name : 'Select Font';
        }
        document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => o.style.fontFamily = value);
        if (layer.hasPattern && layer.patternId) setTimeout(() => _updatePatternPosition(layer), 100);
        if (layer.hasMascot && layer.mascotId) setTimeout(() => _updateMascotPatternPosition(layer), 100);
        if (window.saveCustomizations) window.saveCustomizations();
        setTimeout(() => _showPlusIcon(layer.id), 80);
    };

    window.updatePosition = function (x, y) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        if (x !== null) { layer.x = parseInt(x); document.getElementById('posXValue').textContent = x; }
        if (y !== null) { layer.y = parseInt(y); document.getElementById('posYValue').textContent = y; }

        _moveTextLayer(layer, layer.x, layer.y);

        if (window.saveCustomizations) window.saveCustomizations();
        setTimeout(() => _updatePlusIconPosition(layer.id), 20);
    };

    window.updateRotation = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;
        layer.rotation = parseInt(value);
        const mainSvg = window.getMainSvg();
        if (mainSvg) _applyFlipTransform(layer, mainSvg);
        if (window.saveCustomizations) window.saveCustomizations();
        setTimeout(() => _showPlusIcon(layer.id), 30);
    };

    window.updateOutlineStroke = function (type, value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;
        const textEl = document.getElementById(layer.id);
        if (!textEl) return;
        if (type === 'outline1') {
            textEl.setAttribute('stroke-width', value);
            layer.strokeWidth = parseInt(value);
        }
        if (type === 'outline2') {
            layer.strokeWidth2 = parseInt(value);
            const outlines = document.querySelectorAll(`[data-outline-for="${layer.id}"]`);
            if (outlines.length) outlines[0].setAttribute('stroke-width', value);
        }
        if (type === 'shadow') {
            layer.shadowOffset = parseInt(value);
            applyOutlineStyleToText(layer.id);
        }
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.selectStrokeLinecap = function (value, btn) {
        document.querySelectorAll('[id^="cap-"]').forEach(b => b.classList.remove('selected'));
        if (btn) btn.classList.add('selected');
        if (!window.currentApplicationLayer) return;
        const layer = window.findLayerById ? window.findLayerById(window.currentApplicationLayer) : null;
        if (!layer) return;
        layer.strokeLinecap = value;
        const textEl = document.getElementById(layer.id);
        if (textEl) textEl.setAttribute('stroke-linecap', value);
        document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => o.setAttribute('stroke-linecap', value));
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.selectStrokeLinejoin = function (value, btn) {
        document.querySelectorAll('[id^="join-"]').forEach(b => b.classList.remove('selected'));
        if (btn) btn.classList.add('selected');
        if (!window.currentApplicationLayer) return;
        const layer = window.findLayerById ? window.findLayerById(window.currentApplicationLayer) : null;
        if (!layer) return;
        layer.strokeLinejoin = value;
        const textEl = document.getElementById(layer.id);
        if (textEl) textEl.setAttribute('stroke-linejoin', value);
        document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => o.setAttribute('stroke-linejoin', value));
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.restoreStrokeShapeUI = function (layer) {
        if (!layer) return;
        const cap = layer.strokeLinecap || 'butt';
        document.querySelectorAll('[id^="cap-"]').forEach(b => b.classList.remove('selected'));
        const capBtn = document.getElementById('cap-' + cap);
        if (capBtn) capBtn.classList.add('selected');
        const join = layer.strokeLinejoin || 'miter';
        document.querySelectorAll('[id^="join-"]').forEach(b => b.classList.remove('selected'));
        const joinBtn = document.getElementById('join-' + join);
        if (joinBtn) joinBtn.classList.add('selected');
    };

    // Rotation range slider sync
    (function () {
        const rangeSlider = document.getElementById('rotationRangeSlider');
        if (!rangeSlider) return;
        const _origUpdateRotation = window.updateRotation;
        window.updateRotation = function (value) {
            if (rangeSlider) rangeSlider.value = value;
            const rv = document.getElementById('rotationValue');
            if (rv) rv.textContent = value;
            if (_origUpdateRotation) _origUpdateRotation(value);
        };
        const _origSetWheelAngle = window.setWheelAngle;
        window.setWheelAngle = function (a) {
            if (rangeSlider) rangeSlider.value = Math.round(((a % 360) + 360) % 360);
            if (_origSetWheelAngle) _origSetWheelAngle(a);
        };
    })();

    // Hook restoreStrokeShapeUI on selectApplicationLayer
    (function () {
        const _origSelect = window.selectApplicationLayer;
        window.selectApplicationLayer = function (layerId) {
            if (_origSelect) _origSelect(layerId);
            const layer = window.findLayerById ? window.findLayerById(layerId) : null;
            if (layer && layer.type !== 'direct-mascot') window.restoreStrokeShapeUI(layer);
        };
    })();

    window.updateShadowOffset = function (val) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;
        layer.shadowOffset = parseInt(val);
        applyOutlineStyleToText(layer.id);
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateLetterSpacing = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;
        layer.letterSpacing = parseFloat(value);
        const textEl = document.getElementById(layer.id);
        if (textEl) textEl.style.letterSpacing = value + 'px';
        document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => o.style.letterSpacing = value + 'px');
        document.getElementById('letterSpacingValue').textContent = value;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateTextScale = function (type, value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;
        if (!layer.scaleX) layer.scaleX = 1;
        if (!layer.scaleY) layer.scaleY = 1;
        if (type === 'x') layer.scaleX = value / 100;
        if (type === 'y') layer.scaleY = value / 100;
        // Apply via the master transform
        _applyFlipTransform(layer, window.getMainSvg());
        if (window.saveCustomizations) window.saveCustomizations();
        setTimeout(() => _showPlusIcon(layer.id), 50);
    };

    window.deleteCurrentApplicationLayer = function () {
        if (!window.currentApplicationLayer) return;
        if (!confirm('Delete this application?')) return;
        removeApplicationLayer(window.currentApplicationLayer);
    };







    window.embedFontsInSvg = function (svgElement) {
        if (!window.backendFonts || !svgElement) return;

        let defs = svgElement.querySelector('defs');
        if (!defs) {
            defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            svgElement.insertBefore(defs, svgElement.firstChild);
        }

        // Purane font styles hata do
        defs.querySelectorAll('style[data-fonts]').forEach(s => s.remove());

        // SVG ke andar use ho rahe fonts nikalo
        const usedFonts = new Set();
        svgElement.querySelectorAll('text').forEach(t => {
            const ff = t.style.fontFamily || t.getAttribute('font-family');
            if (ff) usedFonts.add(ff.replace(/['"]/g, '').trim());
        });

        // Sirf use ho rahe fonts embed karo
        let fontCSS = '';
        window.backendFonts.forEach(font => {
            if (usedFonts.has(`font_${font.id}`)) {
                fontCSS += `@font-face { font-family: 'font_${font.id}'; src: url('${font.file_url}') format('truetype'); }\n`;
            }
        });

        if (fontCSS) {
            const styleEl = document.createElementNS('http://www.w3.org/2000/svg', 'style');
            styleEl.setAttribute('data-fonts', '1');
            styleEl.textContent = fontCSS;
            defs.appendChild(styleEl);
        }
    };
    // ============================================================
    // =================== FONT LOADING ===================
    // ============================================================

    function loadBackendFonts() {
        if (!window.backendFonts) return;
        window.backendFonts.forEach(font => {
            if (!document.getElementById('font-style-' + font.id)) {
                const style = document.createElement('style');
                style.id = 'font-style-' + font.id;
                style.innerHTML = `@font-face { font-family: 'font_${font.id}'; src: url('${font.file_url}') format('truetype'); font-display: swap; }`;
                document.head.appendChild(style);
            }
        });
    }

    window.openFontModal = function () { document.getElementById('fontModal').style.display = 'flex'; renderFontGrid(); };
    window.closeFontModal = function () { document.getElementById('fontModal').style.display = 'none'; };

    function renderFontGrid() {
        const grid = document.getElementById('fontGrid');
        if (!grid) return;
        grid.innerHTML = '';
        let previewText = 'Aa';
        const currentLayer = findLayerById(window.currentApplicationLayer);
        if (currentLayer && currentLayer.text && currentLayer.text.trim() !== '') {
            previewText = currentLayer.text.trim();
        } else {
            const textInput = document.getElementById('applicationText');
            if (textInput && textInput.value.trim() !== '') previewText = textInput.value.trim();
        }
        window.backendFonts.forEach(f => {
            const div = document.createElement('div');
            div.style.cssText = 'border:2px solid #ddd;padding:20px;text-align:center;border-radius:8px;cursor:pointer;background:#fff';
            div.innerHTML = `
<div style="font-size:42px;font-family:font_${f.id};line-height:1.1;min-height:55px;display:flex;align-items:center;justify-content:center;">${previewText}</div>
<p style="margin-top:12px;font-weight:600;margin-bottom:0;">${f.name}</p>`;
            if (currentLayer && currentLayer.fontFamily === `font_${f.id}`) {
                div.style.border = '2px solid #000';
                div.style.background = '#8d8d8d';
                div.style.color = '#fff';
            }
            div.onclick = () => { window.updateFontFamily(`font_${f.id}`); closeFontModal(); };
            grid.appendChild(div);
        });
    }

    // ============================================================
    // =================== PATTERN — TEXT FILL (FIXED) ===================
    // KEY FIX: getBBox() retry + ensure pattern stays inside text element
    // ============================================================

    function _getTextBBoxWithRetry(layerId, callback, retries) {
        retries = retries || 0;
        const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return;
        const textEl = mainSvg.querySelector('#' + layerId);
        if (!textEl) return;
        try {
            const bb = textEl.getBBox();
            if (bb.width > 0 && bb.height > 0) {
                callback(bb, textEl, mainSvg);
                return;
            }
        } catch (e) { }
        if (retries < 10) {
            setTimeout(() => _getTextBBoxWithRetry(layerId, callback, retries + 1), 150);
        }
    }

    function _getTextBBox(textEl) {
        try {
            const bb = textEl.getBBox();
            if (bb.width > 0 && bb.height > 0) return bb;
        } catch (e) { }
        return null;
    }

    function _updatePatternPosition(layer) {
        const mainSvg = window.getMainSvg();
        if (!mainSvg) return;
        const textEl = mainSvg.querySelector(`#${layer.id}`);
        const pattern = mainSvg.querySelector(`#${layer.patternId}`);
        if (!textEl || !pattern) return;
        const bb = _getTextBBox(textEl);
        if (!bb) return;
        pattern.setAttribute('x', bb.x);
        pattern.setAttribute('y', bb.y);
        const baseW = bb.width;
        const baseH = bb.height;
        const scale = (layer.patternSize || 100) / 100;
        pattern.setAttribute('width', baseW * scale);
        pattern.setAttribute('height', baseH * scale);
        const innerSvg = pattern.querySelector('svg');
        if (innerSvg) { innerSvg.setAttribute('width', baseW * scale); innerSvg.setAttribute('height', baseH * scale); }
    }

    function _updateMascotPatternPosition(layer) {
        const mainSvg = window.getMainSvg();
        if (!mainSvg) return;
        const textEl = mainSvg.querySelector(`#${layer.id}`);
        const pattern = mainSvg.querySelector(`#${layer.mascotId}`);
        if (!textEl || !pattern) return;
        const bb = _getTextBBox(textEl);
        if (!bb) return;
        const count = layer.mascotCount || 4;
        const tileSize = Math.min(bb.width, bb.height) / count;
        pattern.setAttribute('x', bb.x);
        pattern.setAttribute('y', bb.y);
        pattern.setAttribute('width', tileSize);
        pattern.setAttribute('height', tileSize);
        const innerSvg = pattern.querySelector('svg');
        if (innerSvg) { innerSvg.setAttribute('width', tileSize); innerSvg.setAttribute('height', tileSize); }
    }

    function updateTextPatternButtonPreview() {
        const previewImg = document.getElementById('textPatternThumbnail');
        if (!previewImg) return;
        const layer = window.currentApplicationLayer ? window.findLayerById(window.currentApplicationLayer) : null;
        if (!layer || !layer.patternId) return;
        const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return;
        const patternEl = mainSvg.querySelector('#' + layer.patternId);
        if (!patternEl) return;
        const patternCopy = patternEl.cloneNode(true);
        patternCopy.setAttribute('id', 'preview-temp-pattern');
        patternCopy.setAttribute('patternUnits', 'userSpaceOnUse');
        patternCopy.setAttribute('x', '0');
        patternCopy.setAttribute('y', '0');
        const baseW = parseFloat(patternEl.getAttribute('width')) || 100;
        const baseH = parseFloat(patternEl.getAttribute('height')) || 100;
        patternCopy.setAttribute('width', baseW);
        patternCopy.setAttribute('height', baseH);
        const innerSvg = patternCopy.querySelector('svg');
        if (innerSvg) { innerSvg.setAttribute('width', baseW); innerSvg.setAttribute('height', baseH); innerSvg.setAttribute('preserveAspectRatio', 'xMidYMid slice'); }
        const opacity = layer.patternOpacity !== undefined ? layer.patternOpacity / 100 : 1;
        const previewSvgStr = `<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><defs>${patternCopy.outerHTML}</defs><rect width="200" height="200" fill="url(#preview-temp-pattern)" opacity="${opacity}"/></svg>`;
        const blob = new Blob([previewSvgStr], { type: 'image/svg+xml' });
        previewImg.src = URL.createObjectURL(blob);
    }

    // ============================================================
    // KEY FIX: applyPatternToText
    // Pattern must fill ONLY the text element — use userSpaceOnUse + getBBox retry
    // ============================================================
    window.applyPatternToText = function (svgContent, forcedLayerId) {
        const layerId = forcedLayerId || window.currentApplicationLayer;
        if (!layerId) return;
        const layer = findLayerById(layerId);
        if (!layer) return;

        const mainSvg = window.getMainSvg();
        if (!mainSvg) return;

        let defs = mainSvg.querySelector('defs');
        if (!defs) { defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs'); mainSvg.insertBefore(defs, mainSvg.firstChild); }

        // Remove old pattern
        const patternId = `text-pattern-${layerId}`;
        const oldPattern = defs.querySelector(`#${patternId}`);
        if (oldPattern) oldPattern.remove();

        const parser = new DOMParser();
        const doc = parser.parseFromString(svgContent, 'image/svg+xml');
        let patternSvg = doc.documentElement;
        if (!patternSvg || patternSvg.nodeName !== 'svg') { alert('Pattern SVG invalid'); return; }
        patternSvg = patternSvg.cloneNode(true);
        if (!patternSvg.getAttribute('viewBox')) patternSvg.setAttribute('viewBox', '0 0 100 100');

        // Store pattern data in layer
        layer.patternId = patternId;
        layer.patternSvg = svgContent;
        layer.hasPattern = true;

        // Function to build and apply the pattern once we have a valid bbox
        function _buildPattern(bb) {
            const textEl = mainSvg.querySelector('#' + layerId);
            if (!textEl) return;

            // Remove any existing
            const existing = defs.querySelector(`#${patternId}`);
            if (existing) existing.remove();

            const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
            pattern.setAttribute('id', patternId);
            pattern.setAttribute('patternUnits', 'userSpaceOnUse');
            // Pin to text's current bounding box
            pattern.setAttribute('x', bb.x);
            pattern.setAttribute('y', bb.y);
            pattern.setAttribute('width', bb.width);
            pattern.setAttribute('height', bb.height);

            const svgClone = patternSvg.cloneNode(true);
            svgClone.setAttribute('width', bb.width);
            svgClone.setAttribute('height', bb.height);
            svgClone.setAttribute('preserveAspectRatio', 'xMidYMid slice');

            pattern.appendChild(svgClone);
            defs.appendChild(pattern);

            textEl.setAttribute('fill', `url(#${patternId})`);
            textEl.setAttribute('fill-opacity', '1');

            // UI update
            const patternControls = document.getElementById('textPatternColorControls');
            const patternPlaceholder = document.getElementById('patternPlaceholder');
            const soc = document.getElementById('textPatternSizeOpacityControls');
            if (patternControls) patternControls.style.display = 'block';
            if (patternPlaceholder) patternPlaceholder.style.display = 'none';
            if (soc) soc.style.display = 'block';

            switchTextCustomizationTab('pattern');
            renderTextPatternPalette(svgContent);

            const preview = document.getElementById('textPatternPreview');
            if (preview) { preview.innerHTML = svgContent; const s = preview.querySelector('svg'); if (s) { s.style.width = '70px'; s.style.height = '70px'; s.style.display = 'block'; } }

            setTimeout(() => {
                _updatePatternPosition(layer);
                updateTextPatternButtonPreview();
            }, 200);
        }

        // Try to get bbox — retry if font not loaded yet
        _getTextBBoxWithRetry(layerId, function (bb) {
            _buildPattern(bb);
        });
    };

    function extractPatternColors(svg) {
        const parser = new DOMParser(); const doc = parser.parseFromString(svg, 'image/svg+xml');
        const colors = new Set();
        doc.querySelectorAll('[fill]').forEach(el => { const f = el.getAttribute('fill'); if (f && f !== 'none') colors.add(f.toLowerCase()); });
        return [...colors];
    }

  function renderTextPatternPalette(svg) {
    const layer = findLayerById(window.currentApplicationLayer);
    if (!layer) return;

    if (!layer.replacements) layer.replacements = {};

    const colors = extractPatternColors(svg);
    const container = document.getElementById('patternColorPaletteInTab');
    if (!container) return;

    container.innerHTML = '';

    // ✅ Sirf color wheel / selected colors
    const palette = (window.selectedColors?.length ? [...window.selectedColors] : []).filter(Boolean);

    if (!palette.length) {
        container.innerHTML = '<div style="color:#999;font-size:12px;">Please select colors from wheel first</div>';
        return;
    }

    colors.forEach(patternColor => {
        const row = document.createElement('div');
        row.style.cssText = 'display:flex;align-items:center;gap:12px;margin-bottom:12px';

        row.innerHTML = `
            <div style="width:30px;height:30px;border-radius:6px;background:${patternColor};border:2px solid #ccc"></div>
            <span style="font-weight:700;">→</span>
            <div class="text-color-row" style="display:flex;gap:6px;flex-wrap:wrap"></div>
        `;

        const choices = row.querySelector('.text-color-row');

        palette.forEach(userColor => {
            const box = document.createElement('div');
            box.style.cssText = `
                width:26px;
                height:26px;
                border-radius:6px;
                background:${userColor};
                cursor:pointer;
                border:2px solid #ddd;
            `;

            if (layer.replacements[patternColor?.toLowerCase()] === userColor) {
                box.style.outline = '2px solid #000';
            }

            box.onclick = function () {
                choices.querySelectorAll('div').forEach(b => b.style.outline = 'none');
                box.style.outline = '2px solid #000';

                layer.replacements[patternColor.toLowerCase()] = userColor;
                rebuildTextPattern(layer);
            };

            choices.appendChild(box);
        });

        container.appendChild(row);
    });
}

    function rebuildTextPattern(layer) {
        const mainSvg = window.getMainSvg();
        const pattern = mainSvg.querySelector(`#${layer.patternId}`);
        if (!pattern) return;
        const textEl = mainSvg.querySelector(`#${layer.id}`);
        if (!textEl) return;
        const oldX = pattern.getAttribute('x'), oldY = pattern.getAttribute('y');
        const oldW = pattern.getAttribute('width'), oldH = pattern.getAttribute('height');
        const oldTransform = pattern.getAttribute('patternTransform');
        const parser = new DOMParser(); const doc = parser.parseFromString(layer.patternSvg, 'image/svg+xml');
        const svg = doc.querySelector('svg');
        Object.entries(layer.replacements || {}).forEach(([oldColor, newColor]) => {
            svg.querySelectorAll('[fill]').forEach(e => { if (e.getAttribute('fill')?.toLowerCase() === oldColor) e.setAttribute('fill', newColor); });
        });
        pattern.innerHTML = '';
        pattern.appendChild(svg);
        pattern.setAttribute('x', oldX); pattern.setAttribute('y', oldY);
        pattern.setAttribute('width', oldW); pattern.setAttribute('height', oldH);
        pattern.setAttribute('patternUnits', 'userSpaceOnUse');
        if (oldTransform) pattern.setAttribute('patternTransform', oldTransform);
        svg.setAttribute('width', oldW); svg.setAttribute('height', oldH);
        svg.setAttribute('preserveAspectRatio', 'xMidYMid slice');
    }

    window.clearTextPattern = function () {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasPattern) return;
        const mainSvg = window.getMainSvg();
        const textEl = mainSvg.querySelector(`#${layer.id}`);
        if (!textEl) return;
        if (layer.patternId) { const p = mainSvg.querySelector(`#${layer.patternId}`); if (p) p.remove(); }
        textEl.setAttribute('fill', (layer.outlineColors || window.outlineColors).baseColor || '#FFFFFF');
        textEl.setAttribute('fill-opacity', '1');
        delete layer.patternSvg; delete layer.hasPattern; delete layer.patternId; delete layer.patternSize; delete layer.patternOpacity;
        const patternControls = document.getElementById('textPatternColorControls');
        const patternPlaceholder = document.getElementById('patternPlaceholder');
        const soc = document.getElementById('textPatternSizeOpacityControls');
        if (patternControls) patternControls.style.display = 'none';
        if (patternPlaceholder) patternPlaceholder.style.display = 'block';
        if (soc) soc.style.display = 'none';
        const previewImg = document.getElementById('textPatternThumbnail');
        if (previewImg) previewImg.src = '/assets/images/pattern logo.avif';
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateTextPatternSize = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasPattern || !layer.patternId) return;
        const mainSvg = window.getMainSvg();
        const pattern = mainSvg.querySelector(`#${layer.patternId}`);
        if (!pattern) return;
        const textEl = mainSvg.querySelector(`#${layer.id}`);
        if (!textEl) return;
        const bb = _getTextBBox(textEl);
        if (!bb) return;
        const scale = value / 100;
        const scaledW = bb.width * scale;
        const scaledH = bb.height * scale;
        pattern.setAttribute('width', scaledW);
        pattern.setAttribute('height', scaledH);
        const innerSvg = pattern.querySelector('svg');
        if (innerSvg) { innerSvg.setAttribute('width', scaledW); innerSvg.setAttribute('height', scaledH); }
        layer.patternSize = value;
        updateTextPatternButtonPreview();
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateTextPatternOpacity = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasPattern) return;
        const mainSvg = window.getMainSvg();
        const textEl = mainSvg.querySelector(`#${layer.id}`);
        if (!textEl) return;
        textEl.setAttribute('fill-opacity', value / 100);
        layer.patternOpacity = value;
        updateTextPatternButtonPreview();
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateTextPatternColor = function (color) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer?.patternId) return;
        const pattern = window.getMainSvg().querySelector(`#${layer.patternId}`);
        if (!pattern) return;
        pattern.querySelectorAll('*').forEach(el => { if (el.hasAttribute('fill') && el.getAttribute('fill') !== 'none') el.setAttribute('fill', color); });
        updateTextPatternButtonPreview();
    };

    // ============================================================
    // KEY FIX: applyMascotToText — tiled mascot inside text (getBBox retry)
    // ============================================================
    window.applyMascotToText = function (svgContent, forcedLayerId) {
        const layerId = forcedLayerId || window.currentApplicationLayer;
        if (!layerId) return;
        const layer = findLayerById(layerId);
        if (!layer) return;

        // Redirect to direct mascot if type matches
        if (layer.type === 'direct-mascot') { applyDirectMascotToLayer(svgContent, layerId, true); return; }

        // Clear existing pattern if any
        if (layer.hasPattern) {
            const mainSvgTemp = window.getMainSvg();
            if (layer.patternId) { const p = mainSvgTemp.querySelector(`#${layer.patternId}`); if (p) p.remove(); }
            delete layer.patternSvg; delete layer.hasPattern; delete layer.patternId;
            const pc = document.getElementById('textPatternColorControls');
            const pp = document.getElementById('patternPlaceholder');
            const soc = document.getElementById('textPatternSizeOpacityControls');
            if (pc) pc.style.display = 'none';
            if (pp) pp.style.display = 'block';
            if (soc) soc.style.display = 'none';
        }

        const mainSvg = window.getMainSvg();
        if (!mainSvg) return;

        let defs = mainSvg.querySelector('defs');
        if (!defs) { defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs'); mainSvg.insertBefore(defs, mainSvg.firstChild); }

        const parser = new DOMParser();
        const doc = parser.parseFromString(svgContent, 'image/svg+xml');
        let mascotSvg = doc.documentElement;
        if (!mascotSvg || mascotSvg.nodeName !== 'svg') { alert('Mascot SVG load failed'); return; }
        mascotSvg = mascotSvg.cloneNode(true);

        const vb = (mascotSvg.getAttribute('viewBox') || '0 0 100 100').split(' ').map(Number);
        mascotSvg.querySelectorAll('rect,polygon,circle,ellipse').forEach(el => {
            const x = parseFloat(el.getAttribute('x') || 0), y = parseFloat(el.getAttribute('y') || 0);
            const w = parseFloat(el.getAttribute('width') || 0), h = parseFloat(el.getAttribute('height') || 0);
            if (x <= 1 && y <= 1 && w >= vb[2] * 0.7 && h >= vb[3] * 0.7) el.remove();
        });
        mascotSvg.querySelectorAll('*').forEach(el => {
            const style = el.getAttribute('style') || ''; const fill = el.getAttribute('fill') || '';
            const isWhite = fill === '#fff' || fill === '#ffffff' || fill === 'white' || style.includes('fill:#fff') || style.includes('fill:white');
            if (isWhite && (el.tagName.toLowerCase() === 'rect' || el.tagName.toLowerCase() === 'polygon')) el.remove();
        });
        if (!mascotSvg.getAttribute('viewBox')) mascotSvg.setAttribute('viewBox', '0 0 100 100');
        mascotSvg.style.background = 'transparent';

        const mascotPatternId = `text-mascot-${layerId}`;
        const oldMascot = defs.querySelector(`#${mascotPatternId}`);
        if (oldMascot) oldMascot.remove();

        // Store data
        layer.mascotSvg = svgContent;
        layer.hasMascot = true;
        layer.mascotId = mascotPatternId;
        layer.mascotCount = layer.mascotCount || 4;

        function _buildMascotPattern(bb) {
            const textEl = mainSvg.querySelector('#' + layerId);
            if (!textEl) return;

            // Remove old
            const existing = defs.querySelector(`#${mascotPatternId}`);
            if (existing) existing.remove();

            const count = layer.mascotCount || 4;
            const tileSize = Math.min(bb.width, bb.height) / count;

            const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
            pattern.setAttribute('id', mascotPatternId);
            pattern.setAttribute('patternUnits', 'userSpaceOnUse');
            pattern.setAttribute('x', bb.x);
            pattern.setAttribute('y', bb.y);
            pattern.setAttribute('width', tileSize);
            pattern.setAttribute('height', tileSize);

            const svgClone = mascotSvg.cloneNode(true);
            svgClone.setAttribute('width', tileSize);
            svgClone.setAttribute('height', tileSize);
            svgClone.setAttribute('preserveAspectRatio', 'xMidYMid meet');
            svgClone.removeAttribute('x');
            svgClone.removeAttribute('y');

            pattern.appendChild(svgClone);
            defs.appendChild(pattern);

            textEl.setAttribute('fill', `url(#${mascotPatternId})`);
            textEl.setAttribute('fill-opacity', '1');

            // UI
            const mascotControls = document.getElementById('textMascotColorControls');
            const mascotPlaceholder = document.getElementById('mascotPlaceholder');
            const msoc = document.getElementById('textMascotSizeOpacityControls');
            if (mascotControls) mascotControls.style.display = 'block';
            if (mascotPlaceholder) mascotPlaceholder.style.display = 'none';
            if (msoc) msoc.style.display = 'block';

            const preview = document.getElementById('textMascotPreview');
            if (preview) { preview.innerHTML = svgContent; const s = preview.querySelector('svg'); if (s) { s.style.maxWidth = '60px'; s.style.maxHeight = '60px'; } }

            ['mascotSizeTabSlider', 'mascotOpacityTabSlider', 'mascotCountTabSlider'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = id.includes('Count') ? count : 100;
            });
            ['mascotSizeValueTab', 'mascotOpacityValueTab'].forEach(id => { const el = document.getElementById(id); if (el) el.textContent = '100'; });
            const cve = document.getElementById('mascotCountValueTab'); if (cve) cve.textContent = count;
        }

        // Use getBBox retry to ensure font is loaded
        _getTextBBoxWithRetry(layerId, function (bb) {
            _buildMascotPattern(bb);
        });

        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.clearTextMascot = function () {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasMascot) return;
        const mainSvg = window.getMainSvg();
        const textEl = mainSvg.querySelector(`#${layer.id}`);
        if (!textEl) return;
        if (layer.mascotId) { const p = mainSvg.querySelector(`#${layer.mascotId}`); if (p) p.remove(); }
        textEl.setAttribute('fill', (layer.outlineColors || window.outlineColors).baseColor || '#FFFFFF');
        textEl.setAttribute('fill-opacity', '1');
        delete layer.mascotSvg; delete layer.hasMascot; delete layer.mascotId; delete layer.mascotSize; delete layer.mascotOpacity; delete layer.mascotCount;
        const mascotControls = document.getElementById('textMascotColorControls');
        const mascotPlaceholder = document.getElementById('mascotPlaceholder');
        const msoc = document.getElementById('textMascotSizeOpacityControls');
        if (mascotControls) mascotControls.style.display = 'none';
        if (mascotPlaceholder) mascotPlaceholder.style.display = 'block';
        if (msoc) msoc.style.display = 'none';
        const prev = document.getElementById('textMascotPreview'); if (prev) prev.innerHTML = '<span style="font-size:28px;">🦅</span>';
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateTextMascotSize = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasMascot || !layer.mascotId) return;
        const mainSvg = window.getMainSvg();
        const pattern = mainSvg.querySelector(`#${layer.mascotId}`);
        if (!pattern) return;
        const textEl = mainSvg.querySelector(`#${layer.id}`);
        const bb = textEl ? _getTextBBox(textEl) : null;
        if (!bb) return;
        const count = layer.mascotCount || 4;
        const baseTile = Math.min(bb.width, bb.height) / count;
        const scale = value / 100;
        const newSize = baseTile * scale;
        const offset = (baseTile - newSize) / 2;
        const svg = pattern.querySelector('svg');
        if (svg) { svg.setAttribute('width', newSize); svg.setAttribute('height', newSize); svg.setAttribute('x', offset); svg.setAttribute('y', offset); }
        layer.mascotSize = value;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateTextMascotOpacity = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasMascot) return;
        const mainSvg = window.getMainSvg();
        const textEl = mainSvg.querySelector(`#${layer.id}`);
        if (!textEl) return;
        textEl.setAttribute('fill-opacity', value / 100);
        layer.mascotOpacity = value;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateTextMascotCount = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasMascot || !layer.mascotId) return;
        const mainSvg = window.getMainSvg();
        const pattern = mainSvg.querySelector(`#${layer.mascotId}`);
        if (!pattern) return;
        const textEl = mainSvg.querySelector(`#${layer.id}`);
        const bb = textEl ? _getTextBBox(textEl) : null;
        if (!bb) return;
        const count = parseInt(value);
        const tileSize = Math.min(bb.width, bb.height) / count;
        pattern.setAttribute('width', tileSize);
        pattern.setAttribute('height', tileSize);
        const svg = pattern.querySelector('svg');
        if (svg) { svg.setAttribute('width', tileSize); svg.setAttribute('height', tileSize); }
        layer.mascotCount = count;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    // Library openers
    window.openTextPatternLibrary = function () {
        window.selectingPatternForText = true;
        if (window.openPatternLibrary) window.openPatternLibrary();
        else { const modal = document.getElementById('patternLibraryModal'); if (modal) { modal.style.display = 'flex'; if (window.loadPatternsFromDB) window.loadPatternsFromDB(); } }
    };

    window.openTextMascotLibrary = function () {
        if (!window.currentApplicationLayer) { alert('Please select a text layer first!'); return; }
        window.selectingMascotForText = true;
        if (window.openMascotTemplateModal) window.openMascotTemplateModal();
        else { const modal = document.getElementById('mascotTemplateModal'); if (modal) modal.style.display = 'flex'; }
    };

    window.selectMascotForText = function (svg) {
        if (svg) window.applyMascotToText(decodeURIComponent(svg));
        const modal = document.getElementById('mascotTemplateModal'); if (modal) modal.style.display = 'none';
        if (window.closeMascotTemplateModal) window.closeMascotTemplateModal();
        window.selectingMascotForText = false;
    };

    window.applyPatternFromLibrary = function (svgContent) {
        if (window.currentApplicationLayer) {
            window.applyPatternToText(svgContent);
            window.selectingPatternForText = false;
            return;
        }
        window.selectingPatternForText = false;
        if (window.applyUploadedPattern) window.applyUploadedPattern(svgContent);
    };

    window.applyMascotFromLibrary = function (svgContent) {
        if (window.selectingMascotForText && window.currentApplicationLayer) {
            window.applyMascotToText(svgContent);
            window.selectingMascotForText = false;
        } else if (window.applyUploadedMascot) { window.applyUploadedMascot(); }
    };

    // ============================================================
    // =================== REMOVE / FIND LAYER ===================
    // ============================================================

  window.removeApplicationLayer = function (layerId, event) {
    if (event) event.stopPropagation();

    let foundLayer = null;

    Object.keys(window.applicationsApplied).forEach(view => {
        Object.keys(window.applicationsApplied[view]).forEach(partId => {
            window.applicationsApplied[view][partId] = window.applicationsApplied[view][partId].filter(l => {
                if (l.id === layerId) foundLayer = l;
                return l.id !== layerId;
            });

            if (!window.applicationsApplied[view][partId].length) {
                delete window.applicationsApplied[view][partId];
            }
        });
    });

    const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
    if (mainSvg) {
        const group = mainSvg.querySelector(`#app-group-${layerId}`);
        if (group) group.remove();

        const text = mainSvg.querySelector(`#${layerId}`);
        if (text) text.remove();

        const defs = mainSvg.querySelector('defs');
        if (defs) {
            const clip = defs.querySelector(`#clip-${layerId}`);
            if (clip) clip.remove();

            const pattern = defs.querySelector(`#text-pattern-${layerId}`);
            if (pattern) pattern.remove();

            const mascot = defs.querySelector(`#text-mascot-${layerId}`);
            if (mascot) mascot.remove();
        }
    }

    document.querySelectorAll(`[data-outline-for="${layerId}"]`).forEach(o => o.remove());

    if (window.currentApplicationLayer === layerId) {
        window.currentApplicationLayer = null;
        const ctrl = document.getElementById('applicationLayerControls');
        if (ctrl) ctrl.style.display = 'none';
        if (typeof _hidePlusIcon === 'function') _hidePlusIcon();
        if (typeof _hideMascotBox === 'function') _hideMascotBox();
    }

    updateApplicationLayersList();
    if (window.saveCustomizations) window.saveCustomizations();
};

    window.findLayerById = function (layerId) {
        let found = null;
        Object.values(window.applicationsApplied).forEach(viewData => { Object.values(viewData).forEach(layers => { layers.forEach(layer => { if (layer.id === layerId) found = layer; }); }); });
        return found;
    };

    function findLayerById(id) { return window.findLayerById(id); }

    // ============================================================
    // ✅ FIX 4: initializeApplicationsOnLoad — POORA REPLACE KARO
    // ============================================================

window.initializeApplicationsOnLoad = function () {
    if (!window.applicationsApplied) return;

    const view = window.currentView || 'front';
    const svg = window.getMainSvg ? window.getMainSvg() : null;

    if (!svg) {
        setTimeout(() => window.initializeApplicationsOnLoad(), 300);
        return;
    }

    // HARD CLEAN FIRST
    svg.querySelectorAll('[id^="app-group-"]').forEach(g => g.remove());
    svg.querySelectorAll('#application-group').forEach(g => g.remove());
    svg.querySelectorAll('[data-outline-for]').forEach(e => e.remove());
    const defs = svg.querySelector('defs');
    if (defs) {
        defs.querySelectorAll('clipPath[id^="clip-"]').forEach(c => c.remove());
        defs.querySelectorAll('pattern[id^="text-pattern-"]').forEach(p => p.remove());
        defs.querySelectorAll('pattern[id^="text-mascot-"]').forEach(p => p.remove());
    }

    if (!window.applicationsApplied[view] || !Object.keys(window.applicationsApplied[view]).length) {
        updateApplicationLayersList();
        return;
    }

    Object.entries(window.applicationsApplied[view]).forEach(([partId, layers]) => {
        layers.forEach(layer => {
            window.addApplicationToSvg(layer);
        });
    });

    updateApplicationLayersList();
};


    // ============================================================
    // ✅ FIX 5: reorderSvgLayers — POORA REPLACE KARO
    // ============================================================

 window.reorderSvgLayers = function (view, partId, orderedLayers) {
    const mainSvg = window.getMainSvg();
    if (!mainSvg) return;

    orderedLayers.forEach(layer => {
        const group = mainSvg.querySelector(`#app-group-${layer.id}`);
        if (group) {
            mainSvg.appendChild(group);   // Last mein append = top pe
        }
    });

    // Extra safety - sab ko top pe le aao
    setTimeout(() => {
        if (window.bringApplicationLayersToTop) {
            window.bringApplicationLayersToTop();
        }
    }, 10);
};

    // ============================================================
    // =================== ROTATION WHEEL ===================
    // ============================================================

    function initRotationWheel() {
        const svg = document.getElementById('rotationSvg');
        const arc = document.getElementById('rotationArc');
        const dot = document.getElementById('rotationDot');
        if (!svg || !arc || !dot) { setTimeout(initRotationWheel, 400); return; }

        const cx = 80, cy = 80, r = 68;
        const circumference = 2 * Math.PI * r;
        const snapPoints = [0, 90, 180, 270, 360];
        const snapThreshold = 5;

        let angle = 0, dragging = false, startAngle = 0, startRot = 0;

        function getCenter() {
            const rect = svg.getBoundingClientRect();
            return [rect.left + rect.width / 2, rect.top + rect.height / 2];
        }

        function getAngle(cx2, cy2, ex, ey) {
            let a = Math.atan2(ey - cy2, ex - cx2) * (180 / Math.PI) + 90;
            return ((a % 360) + 360) % 360;
        }

        function snapAngle(a) {
            for (let s of snapPoints) {
                if (Math.abs(a - s) <= snapThreshold) return s;
            }
            return a;
        }

        function updateArc(a) {
            const progress = (a / 360) * circumference;
            arc.setAttribute('stroke-dasharray', progress + ' ' + circumference);
            const rad = (a - 90) * Math.PI / 180;
            const dx = cx + r * Math.cos(rad);
            const dy = cy + r * Math.sin(rad);
            dot.setAttribute('cx', dx);
            dot.setAttribute('cy', dy);
        }

        window.setWheelAngle = function (a) {
            angle = ((a % 360) + 360) % 360;
            updateArc(angle);
            const manual = document.getElementById('rotationManual');
            const hidden = document.getElementById('rotation');
            const display = document.getElementById('rotationValue');
            if (manual) manual.value = Math.round(angle);
            if (hidden) hidden.value = Math.round(angle);
            if (display) display.textContent = Math.round(angle);
        };

        svg.addEventListener('mousedown', function (e) {
            if (e.target.tagName === 'INPUT') return;
            dragging = true;
            const c = getCenter();
            startAngle = getAngle(c[0], c[1], e.clientX, e.clientY);
            startRot = angle;
            svg.style.cursor = 'grabbing';
            e.preventDefault();
        });

        window.addEventListener('mousemove', function (e) {
            if (!dragging) return;
            const c = getCenter();
            let a = getAngle(c[0], c[1], e.clientX, e.clientY);
            let newAngle = ((startRot + (a - startAngle)) % 360 + 360) % 360;
            newAngle = snapAngle(newAngle);
            window.setWheelAngle(newAngle);
            window.updateRotation(Math.round(newAngle));
        });

        window.addEventListener('mouseup', function () {
            if (!dragging) return;
            dragging = false;
            svg.style.cursor = 'grab';
        });

        svg.addEventListener('touchstart', function (e) {
            if (e.target.tagName === 'INPUT') return;
            dragging = true;
            const t = e.touches[0];
            const c = getCenter();
            startAngle = getAngle(c[0], c[1], t.clientX, t.clientY);
            startRot = angle;
            e.preventDefault();
        }, { passive: false });

        window.addEventListener('touchmove', function (e) {
            if (!dragging) return;
            const t = e.touches[0];
            const c = getCenter();
            let a = getAngle(c[0], c[1], t.clientX, t.clientY);
            let newAngle = ((startRot + (a - startAngle)) % 360 + 360) % 360;
            newAngle = snapAngle(newAngle);
            window.setWheelAngle(newAngle);
            window.updateRotation(Math.round(newAngle));
        });

        window.addEventListener('touchend', function () { dragging = false; });

        updateArc(0);
    }

    // Modal observer for mascot box
    (function () {
        const observer = new MutationObserver(mutations => {
            mutations.forEach(m => {
                if (m.type !== 'attributes' || m.attributeName !== 'style') return;
                const el = m.target;
                if (!el.id || !el.id.toLowerCase().includes('modal')) return;
                const visible = el.style.display !== 'none' && el.style.display !== '';
                if (visible) { _hideMascotBox(); }
                else if (window.currentApplicationLayer) {
                    const layer = findLayerById(window.currentApplicationLayer);
                    if (layer && layer.type === 'direct-mascot') setTimeout(() => _showMascotBox(window.currentApplicationLayer), 200);
                }
            });
        });
        function observeModals() { document.querySelectorAll('[id*="modal"],[id*="Modal"]').forEach(m => { observer.observe(m, { attributes: true, attributeFilter: ['style'] }); }); }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => setTimeout(observeModals, 500));
        else setTimeout(observeModals, 500);
    })();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(window.initializeApplicationsOnLoad, 600);
            setTimeout(initRotationWheel, 500);
            loadBackendFonts();
        });
    } else {
        setTimeout(window.initializeApplicationsOnLoad, 600);
        setTimeout(initRotationWheel, 500);
        loadBackendFonts();
    }

    // ===== LIVE POSITION INDICATOR =====
    window._showPositionIndicator = function (x, y) {
        let ind = document.getElementById('appPositionIndicator');
        if (!ind) {
            ind = document.createElement('div');
            ind.id = 'appPositionIndicator';
            ind.style.cssText = 'position:fixed;background:#1a1a1a;color:#fff;padding:5px 12px;border-radius:6px;font-size:13px;font-weight:700;pointer-events:none;z-index:99999;display:none;white-space:nowrap;transform:translate(-50%,-130%);';
            document.body.appendChild(ind);
        }
        const icon = document.getElementById('appPlusDragIcon');
        if (icon && icon.style.display !== 'none') {
            ind.style.left = icon.style.left;
            ind.style.top = icon.style.top;
        }
        ind.textContent = 'X: ' + x + '   Y: ' + y;
        ind.style.display = 'block';
    };

    window._hidePositionIndicator = function () {
        const ind = document.getElementById('appPositionIndicator');
        if (ind) ind.style.display = 'none';
    };

    console.log('✅ applications.js — v2 FIXED: rotation+flip, drag direction, pattern/mascot inside text');


    window.bringApplicationLayersToTop = function () {
        const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return;

        // Saare application groups collect karo
        const appGroups = Array.from(mainSvg.querySelectorAll('[id^="app-group-"]'));

        // Ek ek karke last mein append karo (yahi top pe aata hai SVG mein)
        appGroups.forEach(group => {
            mainSvg.appendChild(group);
        });

        console.log('✅ Application layers brought to top:', appGroups.length);
    };




})();
