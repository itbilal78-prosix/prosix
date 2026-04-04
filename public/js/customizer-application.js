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
            outlineStyle: window.currentOutlineStyle,
            outlineColors: { ...window.outlineColors }
        };
        if (!window.applicationsApplied[view]) window.applicationsApplied[view] = {};
        if (!window.applicationsApplied[view][partId]) window.applicationsApplied[view][partId] = [];
        window.applicationsApplied[view][partId].push(layer);

        addApplicationToSvg(layer);
        updateApplicationLayersList();
        closeApplicationModal();
        openApplicationsSidebar();
        selectApplicationLayer(layerId);
        if (window.saveCustomizations) window.saveCustomizations();
    };

    // ============================================================
    // =================== ADD TEXT APPLICATION TO SVG ===================
    // ============================================================

    window.addApplicationToSvg = function (layer) {
        if (layer.type === 'direct-mascot') {
            if (layer.mascotSvg) applyDirectMascotToLayer(layer.mascotSvg, layer.id, false);
            return;
        }
        const mainSvg = window.getMainSvg();
        if (!mainSvg) { setTimeout(() => addApplicationToSvg(layer), 300); return; }
        if (mainSvg.querySelector(`#${layer.id}`)) return;

        let defs = mainSvg.querySelector('defs');
        if (!defs) { defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs'); mainSvg.insertBefore(defs, mainSvg.firstChild); }

        const partElement = mainSvg.querySelector(`#${layer.partId}`);
        if (!partElement) { console.warn('Part not found', layer.partId); return; }

        const clipId = `clip-${layer.id}`;
        if (!defs.querySelector(`#${clipId}`)) {
            const clip = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
            clip.setAttribute('id', clipId);
            const clone = partElement.cloneNode(true);
            clone.removeAttribute('id');
            clip.appendChild(clone);
            defs.appendChild(clip);
        }

        const layerGroupId = `app-group-${layer.id}`;
        let layerGroup = mainSvg.querySelector(`#${layerGroupId}`);
        if (!layerGroup) {
            layerGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            layerGroup.setAttribute('id', layerGroupId);
            layerGroup.setAttribute('clip-path', `url(#${clipId})`);
            mainSvg.appendChild(layerGroup);
        }

        const bbox = partElement.getBBox();
        const cx = bbox.x + bbox.width / 2;
        const cy = bbox.y + bbox.height / 2;

        const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        text.setAttribute('id', layer.id);
        text.setAttribute('x', cx + layer.x);
        text.setAttribute('y', cy + layer.y);
        text.setAttribute('font-size', layer.fontSize);
        text.style.fontFamily = layer.fontFamily;
        text.setAttribute('fill', layer.fill);
        text.setAttribute('stroke', layer.stroke);
        text.setAttribute('stroke-width', layer.strokeWidth || 5);
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('dominant-baseline', 'middle');
        text.setAttribute('paint-order', 'stroke fill');
        text.setAttribute('stroke-linejoin', 'miter');
        text.style.cursor = 'move';
        text.textContent = layer.text;

        if (layer.rotation) {
            text.setAttribute('transform', `rotate(${layer.rotation} ${cx + layer.x} ${cy + layer.y})`);
        }

        layerGroup.appendChild(text);
        makeDraggable(text, layer);
        text.addEventListener('click', e => { e.stopPropagation(); selectApplicationLayer(layer.id); });

        if (layer.outlineStyle) {
            window.currentOutlineStyle = layer.outlineStyle;
            if (layer.outlineColors) window.outlineColors = { ...layer.outlineColors };
            applyOutlineStyleToText(layer.id);
        }

        if (layer.flipX && layer.flipX !== 1) {
            setTimeout(() => _applyFlipTransform(layer, mainSvg), 150);
        }
    };

    // ============================================================
    // =================== APPLY DIRECT MASCOT ===================
    // ============================================================

    window.applyDirectMascotToLayer = function (svgContent, forcedLayerId, fromModal) {
        const layerId = forcedLayerId || window.currentApplicationLayer;
        if (!layerId) return;
        const layer = findLayerById(layerId);
        if (!layer || layer.type !== 'direct-mascot') return;
        const mainSvg = window.getMainSvg();
        if (!mainSvg) return;

        const savedFlipX = layer.flipX || 1;

        let defs = mainSvg.querySelector('defs');
        if (!defs) { defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs'); mainSvg.insertBefore(defs, mainSvg.firstChild); }

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
        mascotSvg.style.cursor = 'move';
        if (layer.mascotOpacity !== undefined) mascotSvg.setAttribute('opacity', layer.mascotOpacity / 100);

        if (layer.rotation && savedFlipX !== -1) {
            mascotSvg.setAttribute('transform', `rotate(${layer.rotation} ${cx} ${cy})`);
        }

        layerGroup.appendChild(mascotSvg);

        layer.mascotSvg = svgContent;
        layer.mascotId = layerId;
        layer._cx = cx;
        layer._cy = cy;
        layer._mascotSize = mascotSize;
        layer._selectedColorCount = window.selectedMascotColorCount || (window.selectedColors ? window.selectedColors.length : 6);

        makeMascotDraggable(mascotSvg, layer);
        mascotSvg.addEventListener('click', e => { e.stopPropagation(); selectApplicationLayer(layerId); });

        if (savedFlipX === -1) {
            layer.flipX = -1;
            setTimeout(() => {
                _applyFlipTransform(layer, mainSvg);
                _updateFlipBtnUI(layerId, layer);
            }, 50);
        }

        window.currentApplicationLayer = layerId;
        updateApplicationLayersList();
        selectApplicationLayer(layerId);
        if (window.saveCustomizations) window.saveCustomizations();
    };

    // ============================================================
    // =================== FLIP HELPERS ===================
    // ============================================================

    function _rotateDelta(dx, dy, rotDeg, flipX) {
        const rad = rotDeg * Math.PI / 180;
        const cos = Math.cos(rad);
        const sin = Math.sin(rad);
        return { dx: dx * cos + dy * sin, dy: -dx * sin + dy * cos };
    }

    window._applyFlipTransform = function (layer, mainSvg) {
        if (!mainSvg) mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return;
        const el = mainSvg.querySelector('#' + layer.id);
        if (!el) return;
        const flipX = layer.flipX || 1;
        const rot = layer.rotation || 0;
        let transform;

        if (layer.type === 'direct-mascot') {
            const cx = (layer._cx || 0) + (layer.x || 0);
            const cy = (layer._cy || 0) + (layer.y || 0);
            if (flipX === -1) {
                transform = `translate(${cx},${cy}) rotate(${rot}) scale(-1,1) translate(${-cx},${-cy})`;
            } else {
                transform = rot !== 0 ? `rotate(${rot} ${cx} ${cy})` : null;
            }
        } else {
            const x = parseFloat(el.getAttribute('x') || 0);
            const y = parseFloat(el.getAttribute('y') || 0);
            const sx = layer.scaleX || 1;
            const sy = layer.scaleY || 1;
            if (flipX === -1) {
                transform = `translate(${x},${y}) scale(${-sx},${sy}) translate(${-x},${-y})`;
                if (rot !== 0) transform += ` rotate(${rot} ${x} ${y})`;
            } else {
                if (sx !== 1 || sy !== 1) {
                    transform = `translate(${x},${y}) scale(${sx},${sy}) translate(${-x},${-y})`;
                    if (rot !== 0) transform += ` rotate(${rot} ${x} ${y})`;
                } else {
                    transform = rot !== 0 ? `rotate(${rot} ${x} ${y})` : null;
                }
            }
            mainSvg.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => {
                if (transform) o.setAttribute('transform', transform); else o.removeAttribute('transform');
            });
        }

        if (transform) el.setAttribute('transform', transform); else el.removeAttribute('transform');
    };

    function _applyFlipTransform(layer, mainSvg) { window._applyFlipTransform(layer, mainSvg); }

    function _updateFlipBtnUI(layerId, layer) {
        document.querySelectorAll('.application-layer-item').forEach(item => {
            if (item.dataset.layerId !== layerId) return;
            const btn = item.querySelector('.flip-btn-h');
            if (!btn) return;
            btn.style.background = layer.flipX === -1 ? '#1a1a1a' : '#eee';
            btn.style.color = layer.flipX === -1 ? '#fff' : '#000';
        });
        if (layer.type === 'direct-mascot') {
            const preview = document.getElementById('directMascotPreview');
            if (preview && layer.mascotSvg) {
                preview.innerHTML = layer.mascotSvg;
                const s = preview.querySelector('svg');
                if (s) {
                    s.style.maxWidth = '80px';
                    s.style.maxHeight = '80px';
                    s.style.transform = layer.flipX === -1 ? 'scaleX(-1)' : '';
                }
            }
        }
    }

    window.flipApplicationLayer = function (layerId, event) {
        if (event) event.stopPropagation();
        const layer = window.findLayerById ? window.findLayerById(layerId) : null;
        if (!layer) return;

        // 4-state cycle: 0=normal, 1=flipX, 2=flipY, 3=flipBoth
        const current = layer._flipState || 0;
        const next = (current + 1) % 4;
        layer._flipState = next;

        switch (next) {
            case 0: layer.flipX = 1; layer.flipY = 1; break; // normal
            case 1: layer.flipX = -1; layer.flipY = 1; break; // left-right
            case 2: layer.flipX = 1; layer.flipY = -1; break; // top-bottom
            case 3: layer.flipX = -1; layer.flipY = -1; break; // dono
        }

        _applyFlipTransformXY(layer, window.getMainSvg ? window.getMainSvg() : null);
        _updateFlipBtnUI(layerId, layer);
        if (window.saveCustomizations) window.saveCustomizations();
        setTimeout(() => { _showPlusIcon(layerId); }, 100);
    };

    window._applyFlipTransformXY = function (layer, mainSvg) {
        if (!mainSvg) return;
        const el = mainSvg.querySelector('#' + layer.id);
        if (!el) return;

        const flipX = layer.flipX || 1;
        const flipY = layer.flipY || 1;
        const rot = layer.rotation || 0;
        let transform;

        if (layer.type === 'direct-mascot') {
            const cx = (layer._cx || 0) + (layer.x || 0);
            const cy = (layer._cy || 0) + (layer.y || 0);
            transform = `translate(${cx},${cy}) rotate(${rot}) scale(${flipX},${flipY}) translate(${-cx},${-cy})`;
        } else {
            const x = parseFloat(el.getAttribute('x') || 0);
            const y = parseFloat(el.getAttribute('y') || 0);
            const sx = layer.scaleX || 1;
            const sy = layer.scaleY || 1;

            if (flipX !== 1 || flipY !== 1) {
                transform = `translate(${x},${y}) scale(${flipX * sx},${flipY * sy}) translate(${-x},${-y})`;
                if (rot !== 0) transform += ` rotate(${rot} ${x} ${y})`;
            } else {
                if (sx !== 1 || sy !== 1) {
                    transform = `translate(${x},${y}) scale(${sx},${sy}) translate(${-x},${-y})`;
                    if (rot !== 0) transform += ` rotate(${rot} ${x} ${y})`;
                } else {
                    transform = rot !== 0 ? `rotate(${rot} ${x} ${y})` : null;
                }
            }

            // Outline elements bhi update karo
            mainSvg.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => {
                if (transform) o.setAttribute('transform', transform);
                else o.removeAttribute('transform');
            });
        }

        if (transform) el.setAttribute('transform', transform);
        else el.removeAttribute('transform');
    };

    function _applyFlipTransformXY(layer, mainSvg) { window._applyFlipTransformXY(layer, mainSvg); }
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
            // Inline flip div bhi update karo (jo ↔ show karta hai)
            const allDivs = item.querySelectorAll('div[onclick*="flipApplicationLayer"]');
            allDivs.forEach(d => { d.textContent = icons[state]; });
        });
    }
    // ============================================================
    // =================== DRAGGABLE (MASCOT) ===================
    // ============================================================

    window.makeMascotDraggable = function (element, layer) {
        let isDragging = false, startX, startY, initOffX, initOffY;

        element.addEventListener('mousedown', function (e) {
            if (e.button !== 0) return;
            isDragging = true;
            const svg = element.ownerSVGElement || element.closest('svg');
            const pt = svg.createSVGPoint();
            pt.x = e.clientX; pt.y = e.clientY;
            const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());
            startX = svgP.x; startY = svgP.y;
            initOffX = layer.x || 0; initOffY = layer.y || 0;
            e.preventDefault();
        });

        document.addEventListener('mousemove', function (e) {
            if (!isDragging) return;
            const svg = element.ownerSVGElement || element.closest('svg');
            const pt = svg.createSVGPoint();
            pt.x = e.clientX; pt.y = e.clientY;
            const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());
            const d = _rotateDelta(svgP.x - startX, svgP.y - startY, layer.rotation || 0, layer.flipX || 1);
            layer.x = Math.round(initOffX + d.dx);
            layer.y = Math.round(initOffY + d.dy);
            const sz = layer._mascotSize || 100;
            element.setAttribute('x', (layer._cx || 0) - sz / 2 + layer.x);
            element.setAttribute('y', (layer._cy || 0) - sz / 2 + layer.y);
            const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
            if (mainSvg && (layer.flipX === -1 || layer.rotation)) {
                _applyFlipTransform(layer, mainSvg);
            }
        });

        document.addEventListener('mouseup', function () {
            if (!isDragging) return;
            isDragging = false;
            if (window.currentApplicationLayer === layer.id) {
                const xInput = document.getElementById('mascotDirectPosX');
                const yInput = document.getElementById('mascotDirectPosY');
                if (xInput) { xInput.value = layer.x; document.getElementById('mascotDirectPosXValue').textContent = layer.x; }
                if (yInput) { yInput.value = layer.y; document.getElementById('mascotDirectPosYValue').textContent = layer.y; }
            }
            if (window.saveCustomizations) window.saveCustomizations();
        });
    };

    // ============================================================
    // =================== DRAGGABLE (TEXT) ===================
    // ============================================================

    window.makeDraggable = function (element, layer) {
        let isDragging = false, startX, startY, initialX, initialY;

        element.addEventListener('mousedown', function (e) {
            if (e.button !== 0) return;
            isDragging = true;
            const svg = element.ownerSVGElement;
            const pt = svg.createSVGPoint();
            pt.x = e.clientX; pt.y = e.clientY;
            const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());
            startX = svgP.x; startY = svgP.y;
            initialX = parseFloat(element.getAttribute('x'));
            initialY = parseFloat(element.getAttribute('y'));
            e.preventDefault();
        });

        document.addEventListener('mousemove', function (e) {
            if (!isDragging) return;
            const svg = element.ownerSVGElement;
            const pt = svg.createSVGPoint();
            pt.x = e.clientX; pt.y = e.clientY;
            const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());
            const d = _rotateDelta(svgP.x - startX, svgP.y - startY, layer.rotation || 0, layer.flipX || 1);
            const newX = Math.round(initialX + d.dx);
            const newY = Math.round(initialY + d.dy);
            element.setAttribute('x', newX);
            element.setAttribute('y', newY);
            const outlines = element.parentElement.querySelectorAll(`[data-outline-for="${layer.id}"]`);
            outlines.forEach(o => { o.setAttribute('x', newX); o.setAttribute('y', newY); });
            const mainSvg = element.ownerSVGElement;
            if (mainSvg && layer.flipX === -1) _applyFlipTransform(layer, mainSvg);
            _updatePlusIconPosition(layer.id);
            const partEl2 = document.querySelector('#' + layer.partId);
            if (partEl2) {
                const bb2 = partEl2.getBBox();
                const dispX = Math.round(newX - (bb2.x + bb2.width / 2));
                const dispY = Math.round(newY - (bb2.y + bb2.height / 2));
                const pxEl2 = document.getElementById('posX');
                const pyEl2 = document.getElementById('posY');
                if (pxEl2) { pxEl2.value = dispX; document.getElementById('posXValue').textContent = dispX; appFillSlider(pxEl2); }
                if (pyEl2) { pyEl2.value = dispY; document.getElementById('posYValue').textContent = dispY; appFillSlider(pyEl2); }
                window._showPositionIndicator(dispX, dispY);
            }
        });

        document.addEventListener('mouseup', function () {
            if (!isDragging) return;
            isDragging = false;
            window._hidePositionIndicator();
            const partElement = document.querySelector(`#${layer.partId}`);
            if (partElement) {
                const bbox = partElement.getBBox();
                layer.x = Math.round(parseFloat(element.getAttribute('x')) - (bbox.x + bbox.width / 2));
                layer.y = Math.round(parseFloat(element.getAttribute('y')) - (bbox.y + bbox.height / 2));
                if (window.currentApplicationLayer === layer.id) updateApplicationControls(layer);
                const pxSlider = document.getElementById('posX');
                const pySlider = document.getElementById('posY');
                if (pxSlider) appFillSlider(pxSlider);
                if (pySlider) appFillSlider(pySlider);

                // ── Pattern/Mascot update after drag ──
                if (layer.hasPattern && layer.patternId) _updatePatternPosition(layer);
                if (layer.hasMascot && layer.mascotId) _updateMascotPatternPosition(layer);
                if (window.saveCustomizations) window.saveCustomizations();
            }
        });
    };

    // ============================================================
    // =================== LAYER LIST ===================
    // ============================================================

   window.updateApplicationLayersList = function () {
        const container = document.getElementById('applicationLayersList');
        if (!container) return;
        container.innerHTML = '';

        if (!window.applicationsApplied || !Object.keys(window.applicationsApplied).length) {
            container.innerHTML = '<p style="color:#999;text-align:center;padding:20px;font-size:13px;">No applications added yet</p>';
            return;
        }

        // Pehle saari layers flat array mein collect karo
        let allLayerEntries = [];
        Object.entries(window.applicationsApplied).forEach(([view, parts]) => {
            Object.entries(parts).forEach(([partId, layers]) => {
                layers.forEach((layer, index) => {
                    allLayerEntries.push({ view, partId, layer, index });
                });
            });
        });

        // REVERSE — SVG mein jo sabse upar hai woh list mein #1 pe aaye
        allLayerEntries.reverse();

        let layerNum = 1;
        allLayerEntries.forEach(({ view, partId, layer, index }) => {

                    const viewShort = view.charAt(0).toUpperCase();
                    const isActive = window.currentApplicationLayer === layer.id;
                    const labelText = layer.type === 'direct-mascot'
                        ? (layer.mascotTitle || (layer.mascotSvg ? 'Mascot' : 'Mascot (pending)'))
                        : (layer.text || 'Empty');
                    const typeLabel = layer.type === 'direct-mascot' ? 'mascot' : layer.type;
                    const flipOn = layer.flipX === -1;

                    const item = document.createElement('div');
                    item.className = 'application-layer-item';
                    item.setAttribute('draggable', 'true');
                    item.dataset.layerId = layer.id;
                    item.dataset.view = view;
                    item.dataset.partId = partId;
                    item.dataset.index = index;

item.style.cssText = `
display:flex;
align-items:center;
gap:10px;
width:100%;
padding:8px 10px;
margin-bottom:6px;
background:${isActive ? '#5a5a5a' : '#7a7a7a'};
color:#fff;
border:none;
border-radius:0;
cursor:pointer;
transition:all 0.2s;
user-select:none;
`;

item.innerHTML = `
<div style="
background:#2f2f2f;
color:#fff;
font-weight:700;
font-size:13px;
padding:4px 8px;
flex-shrink:0;
">
#${layerNum}
</div>

<div style="
flex:1;
display:flex;
align-items:center;
gap:8px;
font-weight:700;
font-size:14px;
white-space:nowrap;
overflow:hidden;
text-overflow:ellipsis;
">
<span style="
overflow:hidden;
text-overflow:ellipsis;
white-space:nowrap;
">
${labelText.toUpperCase()}
</span>
</div>

<div style="display:flex;gap:12px;align-items:center;flex-shrink:0;">
<span style="
background:#2f2f2f;
color:#fff;
padding:2px 6px;
font-size:11px;
font-weight:700;
">
${viewShort}
</span>
<div onclick="window.flipApplicationLayer('${layer.id}',event)"
style="cursor:pointer;">↔</div>

<div onclick="duplicateApplicationLayer('${layer.id}',event)"
style="cursor:pointer;">⧉</div>

<div onclick="removeApplicationLayer('${layer.id}',event)"
style="cursor:pointer;">×</div>
</div>
`;

                    item.onclick = function (e) {
                        if (e.target.closest('.drag-handle')) return;
                        selectApplicationLayer(layer.id);
                    };

                    // ── Drag & Drop ──
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
                        if (window._draggingLayerId !== layer.id) item.style.outline = '2px solid #000000';
                    });

                    item.addEventListener('dragleave', () => {
                        item.style.outline = 'none';
                    });

                    item.addEventListener('drop', e => {
                        e.preventDefault();
                        item.style.outline = 'none';

                        const fromId = window._draggingLayerId;
                        if (!fromId || fromId === layer.id) return;
                        if (window._draggingView !== view || window._draggingPartId !== partId) return;

                        const arr = window.applicationsApplied[view][partId];
                        const fromIdx = arr.findIndex(l => l.id === fromId);
                        const toIdx   = arr.findIndex(l => l.id === layer.id);
                        if (fromIdx === -1 || toIdx === -1) return;

                        // List reverse hai isliye SVG array mein opposite direction mein move karo
                        const [removed] = arr.splice(fromIdx, 1);
                        const adjustedToIdx = toIdx <= fromIdx ? toIdx + 1 : toIdx;
                        arr.splice(adjustedToIdx, 0, removed);

                        reorderSvgLayers(view, partId, arr);
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

        setTimeout(() => { _showPlusIcon(layerId); }, 80);
    };

    // ============================================================
    // =================== PLUS DRAG ICON + PART CLICK SELECT ===================
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
                'color:#ffffff',                          // ✅ White color
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
                '-webkit-text-stroke:1px #000000',        // ✅ Black stroke around icon
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
        _plusState.startLayerX = layer.x || 0;
        _plusState.startLayerY = layer.y || 0;

        const icon = document.getElementById('appPlusDragIcon');
        if (icon) {
            icon.style.cursor = 'grabbing';
            icon.style.background = 'transparent';
            icon.style.color = '#000';
        }

        document.addEventListener('mousemove', _plusDragMove);
        document.addEventListener('mouseup', _plusDragEnd);
    }

    function _plusDragEnd() {
        _plusState.isDragging = false;
        window._hidePositionIndicator();

        const icon = document.getElementById('appPlusDragIcon');
        if (icon) {
            icon.style.cursor = 'grab';
            // ←←← Background color reset bhi hata diya
        }

        document.removeEventListener('mousemove', _plusDragMove);
        document.removeEventListener('mouseup', _plusDragEnd);

        if (_plusState.layerId) {
            const layer = findLayerById(_plusState.layerId);
            if (layer && layer.hasPattern && layer.patternId) _updatePatternPosition(layer);
            if (layer && layer.hasMascot && layer.mascotId) _updateMascotPatternPosition(layer);
        }

        if (window.saveCustomizations) window.saveCustomizations();
    }

    function _getTextCenterScreen(layerId) {
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

    function _showPlusIcon(layerId) {
        const layer = findLayerById(layerId);
        if (!layer || layer.type === 'direct-mascot') { _hidePlusIcon(); return; }
        const center = _getTextCenterScreen(layerId);
        if (!center) { _hidePlusIcon(); return; }
        const icon = _ensurePlusIcon();
        icon.style.left = center.x + 'px';
        icon.style.top = center.y + 'px';
        icon.style.display = 'block';
        _plusState.layerId = layerId;
    }

    function _hidePlusIcon() {
        const icon = document.getElementById('appPlusDragIcon');
        if (icon) icon.style.display = 'none';
        _plusState.layerId = null;
    }

    function _updatePlusIconPosition(layerId) {
        if (_plusState.layerId !== layerId) return;
        const center = _getTextCenterScreen(layerId);
        if (!center) return;
        const icon = document.getElementById('appPlusDragIcon');
        if (icon && icon.style.display !== 'none') { icon.style.left = center.x + 'px'; icon.style.top = center.y + 'px'; }
    }

    function _clientToSvgCoord(clientX, clientY) {
        const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return { x: 0, y: 0 };
        const pt = mainSvg.createSVGPoint();
        pt.x = clientX; pt.y = clientY;
        return pt.matrixTransform(mainSvg.getScreenCTM().inverse());
    }

    function _plusDragStart(e) {
        if (e.button !== 0) return;
        e.preventDefault(); e.stopPropagation();
        const layerId = _plusState.layerId;
        if (!layerId) return;
        const layer = findLayerById(layerId);
        if (!layer) return;
        _plusState.isDragging = true;
        _plusState.startClient = { x: e.clientX, y: e.clientY };
        _plusState.startLayerX = layer.x || 0;
        _plusState.startLayerY = layer.y || 0;
        const icon = document.getElementById('appPlusDragIcon');
        if (icon) { icon.style.cursor = 'grabbing'; icon.style.color = '#ffffff'; }
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

        const startSvg = _clientToSvgCoord(_plusState.startClient.x, _plusState.startClient.y);
        const curSvg = _clientToSvgCoord(e.clientX, e.clientY);
        const rawDx = curSvg.x - startSvg.x;
        const rawDy = curSvg.y - startSvg.y;
        const d = _rotateDelta(rawDx, rawDy, layer.rotation || 0, layer.flipX || 1);
        const newX = Math.round(_plusState.startLayerX + d.dx);
        const newY = Math.round(_plusState.startLayerY + d.dy);
        layer.x = newX; layer.y = newY;

        const partEl = mainSvg.querySelector('#' + layer.partId);
        if (partEl) {
            const bbox = partEl.getBBox();
            const cx = bbox.x + bbox.width / 2;
            const cy = bbox.y + bbox.height / 2;
            const textEl = mainSvg.querySelector('#' + layerId);
            if (textEl) {
                textEl.setAttribute('x', cx + newX);
                textEl.setAttribute('y', cy + newY);
                mainSvg.querySelectorAll(`[data-outline-for="${layerId}"]`).forEach(o => { o.setAttribute('x', cx + newX); o.setAttribute('y', cy + newY); });
                if (layer.flipX === -1) _applyFlipTransform(layer, mainSvg);
            }
        }


        if (pxEl) { pxEl.value = newX; document.getElementById('posXValue').textContent = newX; }
        if (pyEl) { pyEl.value = newY; document.getElementById('posYValue').textContent = newY; }
        if (pxEl) appFillSlider(pxEl);
        if (pyEl) appFillSlider(pyEl);

        _updatePlusIconPosition(layerId);

        // Slider fill + indicator
        const pxEl = document.getElementById('posX');
        const pyEl = document.getElementById('posY');
        if (pxEl) { pxEl.value = newX; document.getElementById('posXValue').textContent = newX; appFillSlider(pxEl); }
        if (pyEl) { pyEl.value = newY; document.getElementById('posYValue').textContent = newY; appFillSlider(pyEl); }
        window._showPositionIndicator(newX, newY);

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
    // =================== MASCOT DRAG BOX ===================
    // ============================================================

    var _mascotBox = { layerId: null, isDragging: false, startClient: { x: 0, y: 0 }, startLayerX: 0, startLayerY: 0 };

    function _ensureMascotBox() {
        let box = document.getElementById('appMascotDragBox');
        if (!box) {
            box = document.createElement('div');
            box.id = 'appMascotDragBox';
            box.style.cssText = ['position:fixed', 'border:2px dashed #000', 'border-radius:4px', 'background:transparent', 'z-index:9998', 'display:none', 'cursor:move', 'pointer-events:all', 'box-sizing:border-box'].join(';');
            document.body.appendChild(box);
            box.addEventListener('mousedown', _mascotBoxDragStart);
        }
        return box;
    }

    function _getMascotScreenRect(layerId) {
        const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return null;
        const el = mainSvg.querySelector('#' + layerId);
        if (!el) return null;
        try { const rect = el.getBoundingClientRect(); if (rect.width === 0 && rect.height === 0) return null; return rect; } catch (e) { return null; }
    }

    function _showMascotBox(layerId) {
        const layer = findLayerById(layerId);
        if (!layer || layer.type !== 'direct-mascot') { _hideMascotBox(); return; }
        const rect = _getMascotScreenRect(layerId);
        if (!rect) { _hideMascotBox(); return; }
        const box = _ensureMascotBox();
        const pad = 4;
        box.style.left = (rect.left - pad) + 'px'; box.style.top = (rect.top - pad) + 'px';
        box.style.width = (rect.width + pad * 2) + 'px'; box.style.height = (rect.height + pad * 2) + 'px';
        box.style.display = 'block';
        _mascotBox.layerId = layerId;
    }

    function _hideMascotBox() { const box = document.getElementById('appMascotDragBox'); if (box) box.style.display = 'none'; _mascotBox.layerId = null; }

    function _mascotBoxDragStart(e) {
        if (e.button !== 0) return;
        e.preventDefault(); e.stopPropagation();
        const layerId = _mascotBox.layerId;
        if (!layerId) return;
        const layer = findLayerById(layerId);
        if (!layer) return;
        _mascotBox.isDragging = true;
        _mascotBox.startClient = { x: e.clientX, y: e.clientY };
        _mascotBox.startLayerX = layer.x || 0;
        _mascotBox.startLayerY = layer.y || 0;
        document.addEventListener('mousemove', _mascotBoxDragMove);
        document.addEventListener('mouseup', _mascotBoxDragEnd);
    }

    function _mascotBoxDragMove(e) {
        if (!_mascotBox.isDragging) return;
        const layerId = _mascotBox.layerId;
        const layer = findLayerById(layerId);
        if (!layer) return;
        const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return;
        const svgRect = mainSvg.getBoundingClientRect();
        const vb = mainSvg.viewBox ? mainSvg.viewBox.baseVal : null;
        const vbW = (vb && vb.width) ? vb.width : svgRect.width;
        const vbH = (vb && vb.height) ? vb.height : svgRect.height;
        const scaleX = vbW / svgRect.width; const scaleY = vbH / svgRect.height;
        const rawDx = (e.clientX - _mascotBox.startClient.x) * scaleX;
        const rawDy = (e.clientY - _mascotBox.startClient.y) * scaleY;
        const d = _rotateDelta(rawDx, rawDy, layer.rotation || 0, layer.flipX || 1);
        const newX = Math.round(_mascotBox.startLayerX + d.dx);
        const newY = Math.round(_mascotBox.startLayerY + d.dy);
        layer.x = newX; layer.y = newY;
        const el = mainSvg.querySelector('#' + layerId);
        if (el) {
            const sz = layer._mascotSize || 100;
            el.setAttribute('x', (layer._cx || 0) - sz / 2 + newX);
            el.setAttribute('y', (layer._cy || 0) - sz / 2 + newY);
            if (layer.flipX === -1 || layer.rotation) _applyFlipTransform(layer, mainSvg);
        }
        const mxEl = document.getElementById('mascotDirectPosX'); const myEl = document.getElementById('mascotDirectPosY');
        if (mxEl) { mxEl.value = newX; document.getElementById('mascotDirectPosXValue').textContent = newX; }
        if (myEl) { myEl.value = newY; document.getElementById('mascotDirectPosYValue').textContent = newY; }
        const rect = _getMascotScreenRect(layerId);
        if (rect) { const box = document.getElementById('appMascotDragBox'); const pad = 4; if (box) { box.style.left = (rect.left - pad) + 'px'; box.style.top = (rect.top - pad) + 'px'; } }
    }

    function _mascotBoxDragEnd() {
        _mascotBox.isDragging = false;
        document.removeEventListener('mousemove', _mascotBoxDragMove);
        document.removeEventListener('mouseup', _mascotBoxDragEnd);
        if (window.saveCustomizations) window.saveCustomizations();
        if (_mascotBox.layerId) _showMascotBox(_mascotBox.layerId);
    }

    window.addEventListener('resize', () => { if (_mascotBox.layerId) _showMascotBox(_mascotBox.layerId); });
    window.addEventListener('scroll', () => { if (_mascotBox.layerId) _showMascotBox(_mascotBox.layerId); }, true);

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
            if (svg) { svg.style.maxWidth = '80px'; svg.style.maxHeight = '80px'; svg.style.transform = layer.flipX === -1 ? 'scaleX(-1)' : ''; }
        } else if (preview) { preview.innerHTML = '<span style="color:#aaa;font-size:12px;">No mascot selected</span>'; }
        const sync = (id, valId, val) => { const el = document.getElementById(id); const ve = document.getElementById(valId); if (el) el.value = val; if (ve) ve.textContent = val; };
        sync('directMascotScale', 'directMascotScaleValue', Math.round((layer.mascotScaleX || 1) * 100));
        sync('directMascotOpacity', 'directMascotOpacityValue', layer.mascotOpacity ?? 100);
        sync('directMascotRotation', 'directMascotRotationValue', layer.rotation || 0);
        sync('mascotDirectPosX', 'mascotDirectPosXValue', layer.x || 0);
        sync('mascotDirectPosY', 'mascotDirectPosYValue', layer.y || 0);
        _renderDirectMascotColors(layer);
        setTimeout(() => _showMascotBox(layer.id), 80);
    }

    function _renderDirectMascotColors(layer) {
        const container = document.getElementById('directMascotColorSwatches');
        if (!container) return;
        if (!layer.mascotSvg) { container.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;">No mascot selected</p>'; return; }
        container.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;">Detecting colors...</p>';
        const maxColors = layer._selectedColorCount || window.selectedMascotColorCount || (window.selectedColors ? window.selectedColors.length : 6) || 6;
        const parser = new DOMParser();
        const doc = parser.parseFromString(layer.mascotSvg, 'image/svg+xml');
        const imgTag = doc.querySelector('image');
        if (imgTag) {
            const href = imgTag.getAttribute('href') || imgTag.getAttribute('xlink:href') || '';
            if (href.startsWith('data:image/png')) { _detectColorsFromPng(href, layer, container); return; }
        }
        const colorCounts = {};
        doc.querySelectorAll('[fill]').forEach(el => { const hex = _normalizeColor(el.getAttribute('fill') || ''); if (hex && hex !== '#ffffff') colorCounts[hex] = (colorCounts[hex] || 0) + 1; });
        doc.querySelectorAll('[style]').forEach(el => { const m = (el.getAttribute('style') || '').match(/fill:\s*(#[0-9a-fA-F]{3,6})/); if (!m) return; const hex = _normalizeColor(m[1]); if (hex && hex !== '#ffffff') colorCounts[hex] = (colorCounts[hex] || 0) + 1; });
        const detected = Object.keys(colorCounts).sort((a, b) => colorCounts[b] - colorCounts[a]).slice(0, maxColors);
        layer._detectedColors = detected;
        _buildColorSwatches(detected, layer, container);
    }

    function _detectColorsFromPng(dataUrl, layer, container) {
        const canvas = document.createElement('canvas'); const ctx = canvas.getContext('2d'); const img = new Image();
        img.onload = function () {
            const SIZE = 100; canvas.width = SIZE; canvas.height = SIZE; ctx.drawImage(img, 0, 0, SIZE, SIZE);
            const data = ctx.getImageData(0, 0, SIZE, SIZE).data; const colorCounts = {};
            for (let i = 0; i < data.length; i += 4) {
                if (data[i + 3] < 30) continue;
                const r = Math.min(Math.round(data[i] / 32) * 32, 255); const g = Math.min(Math.round(data[i + 1] / 32) * 32, 255); const b = Math.min(Math.round(data[i + 2] / 32) * 32, 255);
                if (r > 230 && g > 230 && b > 230) continue;
                const hex = '#' + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('');
                colorCounts[hex] = (colorCounts[hex] || 0) + 1;
            }
            const maxColors = layer._selectedColorCount || 6;
            const detected = Object.keys(colorCounts).sort((a, b) => colorCounts[b] - colorCounts[a]).slice(0, maxColors);
            _buildColorSwatches(detected, layer, container);
        };
        img.onerror = () => { container.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;">Could not load image</p>'; };
        img.src = dataUrl;
    }

    function _buildColorSwatches(detectedColors, layer, container) {
        container.innerHTML = '';
        if (!detectedColors.length) { container.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;">No colors detected</p>'; return; }
        layer._detectedColors = detectedColors;
        if (!layer._colorMap) layer._colorMap = {};
        const backendColors = (window.selectedColors?.length) ? window.selectedColors : (window.backendColors || []).map(c => c.code || c);
        const palette = backendColors.length ? backendColors : ['#FF0000', '#FF6600', '#FFFF00', '#00FF00', '#0000FF', '#800080', '#FFFFFF', '#000000'];
        detectedColors.forEach(detectedHex => {
            const row = document.createElement('div'); row.style.cssText = 'display:flex;align-items:center;gap:8px;margin-bottom:10px;';
            const fromBox = document.createElement('div'); fromBox.style.cssText = `width:26px;height:26px;border-radius:5px;border:2px solid #ccc;flex-shrink:0;background:${detectedHex}`;
            const arrow = document.createElement('span'); arrow.textContent = '→'; arrow.style.cssText = 'font-size:13px;color:#888;flex-shrink:0;';
            const swatchRow = document.createElement('div'); swatchRow.style.cssText = 'display:flex;flex-wrap:wrap;gap:4px;';
            const currentReplacement = layer._colorMap[detectedHex.toLowerCase()] || null;
            palette.forEach(hex => {
                const isSelected = currentReplacement && hex.toLowerCase() === currentReplacement.toLowerCase();
                const box = document.createElement('div');
                box.style.cssText = `width:22px;height:22px;border-radius:4px;cursor:pointer;box-sizing:border-box;position:relative;display:flex;align-items:center;justify-content:center;background:${hex};border:${isSelected ? '3px solid #1a1a1a' : '2px solid #ddd'};`;
                if (isSelected) { const check = document.createElement('span'); check.textContent = '✓'; check.style.cssText = `font-size:13px;font-weight:900;line-height:1;color:${_getContrastColor(hex)};`; box.appendChild(check); }
                box.onclick = (function (dHex, nHex, b, sRow) {
                    return function () {
                        layer._colorMap[dHex.toLowerCase()] = nHex;
                        sRow.querySelectorAll('div').forEach(x => { x.style.border = '2px solid #ddd'; x.innerHTML = ''; });
                        b.style.border = '3px solid #1a1a1a';
                        const ck = document.createElement('span'); ck.textContent = '✓'; ck.style.cssText = `font-size:13px;font-weight:900;line-height:1;color:${_getContrastColor(nHex)};`; b.appendChild(ck);
                        _applyDirectMascotColorMap(layer);
                    };
                })(detectedHex, hex, box, swatchRow);
                swatchRow.appendChild(box);
            });
            row.appendChild(fromBox); row.appendChild(arrow); row.appendChild(swatchRow);
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
        const canvas = document.createElement('canvas'); const ctx = canvas.getContext('2d'); const img = new Image();
        img.onload = function () {
            canvas.width = img.width; canvas.height = img.height; ctx.drawImage(img, 0, 0);
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height); const data = imageData.data;
            const alphaMask = layer._alphaMask || null; const maskW = layer._alphaMaskW || 0; const maskH = layer._alphaMaskH || 0;
            const colorMappings = Object.entries(layer._colorMap).map(([oldHex, newHex]) => ({ old: _hexToRgb(oldHex), new: _hexToRgb(newHex) })).filter(m => m.old && m.new);
            for (let i = 0; i < data.length; i += 4) {
                const pixelIndex = i / 4;
                if (alphaMask?.length) { const px = pixelIndex % canvas.width; const py = Math.floor(pixelIndex / canvas.width); const mx = Math.round(px * maskW / canvas.width); const my = Math.round(py * maskH / canvas.height); const maskIndex = my * maskW + mx; if (alphaMask[maskIndex] !== undefined && alphaMask[maskIndex] < 30) { data[i + 3] = 0; continue; } }
                if (data[i + 3] < 30) continue;
                let bestMatch = null, bestDist = 60;
                colorMappings.forEach(m => { const dist = Math.sqrt(Math.pow(data[i] - m.old.r, 2) + Math.pow(data[i + 1] - m.old.g, 2) + Math.pow(data[i + 2] - m.old.b, 2)); if (dist < bestDist) { bestDist = dist; bestMatch = m; } });
                if (bestMatch) { data[i] = bestMatch.new.r; data[i + 1] = bestMatch.new.g; data[i + 2] = bestMatch.new.b; }
            }
            ctx.putImageData(imageData, 0, 0);
            const newPngDataUrl = canvas.toDataURL('image/png');
            const parser = new DOMParser(); const doc = parser.parseFromString(layer.mascotSvg, 'image/svg+xml');
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
        ['id', 'x', 'y', 'width', 'height', 'transform', 'opacity'].forEach(a => { const v = svgEl.getAttribute(a); if (v) newSvg.setAttribute(a, v); });
        newSvg.setAttribute('preserveAspectRatio', 'xMidYMid meet'); newSvg.style.cursor = 'move';
        svgEl.parentNode.replaceChild(newSvg, svgEl);
        newSvg.addEventListener('click', e => { e.stopPropagation(); window.selectApplicationLayer(layer.id); });
        if (window.makeMascotDraggable) window.makeMascotDraggable(newSvg, layer);
        if (layer.flipX === -1) setTimeout(() => _applyFlipTransform(layer, mainSvg), 50);
        if (window.saveCustomizations) window.saveCustomizations();
    }

    function _hexToRgb(hex) {
        if (!hex) return null; hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        return { r: parseInt(hex.substr(0, 2), 16), g: parseInt(hex.substr(2, 2), 16), b: parseInt(hex.substr(4, 2), 16) };
    }

    // ============================================================
    // =================== SHOW TEXT LAYER CONTROLS ===================
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

        // Pattern tab UI
        const patternControls = document.getElementById('textPatternColorControls');
        const patternPlaceholder = document.getElementById('patternPlaceholder');
        const patternSoc = document.getElementById('textPatternSizeOpacityControls');
        if (patternControls) patternControls.style.display = hasPattern ? 'block' : 'none';
        if (patternPlaceholder) patternPlaceholder.style.display = hasPattern ? 'none' : 'block';
        if (patternSoc) patternSoc.style.display = hasPattern ? 'block' : 'none';

        // Mascot tab UI
        const mascotControls = document.getElementById('textMascotColorControls');
        const mascotPlaceholder = document.getElementById('mascotPlaceholder');
        const mascotSoc = document.getElementById('textMascotSizeOpacityControls');
        if (mascotControls) mascotControls.style.display = hasMascot ? 'block' : 'none';
        if (mascotPlaceholder) mascotPlaceholder.style.display = hasMascot ? 'none' : 'block';
        if (mascotSoc) mascotSoc.style.display = hasMascot ? 'block' : 'none';

        // Pattern thumbnail
        if (hasPattern) {
            setTimeout(updateTextPatternButtonPreview, 50);
            const sizeSlider = document.getElementById('patternSizeTab');
            const opacitySlider = document.getElementById('patternOpacityTab');
            if (sizeSlider && layer.patternSize) { sizeSlider.value = layer.patternSize; document.getElementById('patternSizeValueTab').textContent = layer.patternSize; }
            if (opacitySlider && layer.patternOpacity) { opacitySlider.value = layer.patternOpacity; document.getElementById('patternOpacityValueTab').textContent = layer.patternOpacity; }
            renderTextPatternPalette(layer.patternSvg);
        }

        switchTextCustomizationTab('colors');
    }

    // ============================================================
    // =================== DIRECT MASCOT SLIDER CONTROLS ===================
    // ============================================================

    window.updateDirectMascotScale = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || layer.type !== 'direct-mascot') return;
        layer.mascotScaleX = value / 100; layer.mascotScaleY = value / 100;
        if (layer.mascotSvg) { applyDirectMascotToLayer(layer.mascotSvg, layer.id, false); if (layer._colorMap && Object.keys(layer._colorMap).length) setTimeout(() => _applyDirectMascotColorMap(layer), 150); }
        document.getElementById('directMascotScaleValue').textContent = value;
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
    };

    window.updateDirectMascotPosition = function (axis, value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || layer.type !== 'direct-mascot') return;
        if (axis === 'x') { layer.x = parseInt(value); document.getElementById('mascotDirectPosXValue').textContent = value; }
        else { layer.y = parseInt(value); document.getElementById('mascotDirectPosYValue').textContent = value; }
        const el = document.getElementById(layer.id);
        if (el) {
            const sz = layer._mascotSize || 100;
            el.setAttribute('x', (layer._cx || 0) - sz / 2 + layer.x);
            el.setAttribute('y', (layer._cy || 0) - sz / 2 + layer.y);
            const mainSvg = window.getMainSvg();
            if (mainSvg && layer.flipX === -1) _applyFlipTransform(layer, mainSvg);
        }
        setTimeout(() => _showMascotBox(layer.id), 30);
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.changeDirectMascot = function () { if (!window.currentApplicationLayer) return; window.openMascotSelectModal(window.currentApplicationLayer); };

    // ============================================================
    // =================== TEXT CONTROLS ===================
    // ============================================================

    window.updateApplicationControls = function (layer) {
        if (layer.type === 'direct-mascot') return;

        // ... existing code ...
        document.getElementById('posX').value = layer.x || 0;
        document.getElementById('posXValue').textContent = layer.x || 0;
        document.getElementById('posY').value = layer.y || 0;
        document.getElementById('posYValue').textContent = layer.y || 0;

        // YEH ADD KARO — select hone par fill restore ho:
        const pxSlider = document.getElementById('posX');
        const pySlider = document.getElementById('posY');
        const fsSlider = document.getElementById('fontSize');
        if (pxSlider) appFillSlider(pxSlider);
        if (pySlider) appFillSlider(pySlider);
        if (fsSlider) appFillSlider(fsSlider);
    };

    // ── MISSING FUNCTION FIX ── HTML mein updateFontSize call ho raha tha
    window.updateFontSize = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;
        layer.fontSize = parseInt(value);
        const textEl = document.getElementById(layer.id);
        if (textEl) textEl.setAttribute('font-size', value);
        document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => o.setAttribute('font-size', value));
        document.getElementById('fontSizeValue').textContent = value;
        // Font size change ke baad pattern/mascot position update karo
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

        const btn = document.getElementById('selectFontBtn');
        if (btn) btn.textContent = value || 'Aa';

        if (window.saveCustomizations)
            window.saveCustomizations();
    };

    window.updateFontFamily = function (value) {

        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        layer.fontFamily = value;

        const textEl = document.getElementById(layer.id);

        if (textEl)
            textEl.style.fontFamily = value;

        // ✅ Select Font button realtime preview + font name
        const btn = document.getElementById('selectFontBtn');

        if (btn) {

            btn.style.fontFamily = value;

            // backend font name extract
            const fontObj = window.backendFonts?.find(f =>
                ('font_' + f.id) === value
            );

            btn.textContent = fontObj ? fontObj.name : 'Font';

        }

        // outline sync
        document
            .querySelectorAll(`[data-outline-for="${layer.id}"]`)
            .forEach(o => o.style.fontFamily = value);


        if (layer.hasPattern && layer.patternId)
            setTimeout(() => _updatePatternPosition(layer), 100);


        if (layer.hasMascot && layer.mascotId)
            setTimeout(() => _updateMascotPatternPosition(layer), 100);


        if (window.saveCustomizations)
            window.saveCustomizations();


        setTimeout(() => _showPlusIcon(layer.id), 80);

    };

    window.updatePosition = function (x, y) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;
        const partElement = document.querySelector(`#${layer.partId}`);
        if (!partElement) return;
        const bbox = partElement.getBBox();
        const centerX = bbox.x + bbox.width / 2;
        const centerY = bbox.y + bbox.height / 2;
        if (x !== null) { layer.x = parseInt(x); document.getElementById('posXValue').textContent = x; }
        if (y !== null) { layer.y = parseInt(y); document.getElementById('posYValue').textContent = y; }
        const textEl = document.getElementById(layer.id);
        if (textEl) {
            const newX = centerX + layer.x; const newY = centerY + layer.y;
            textEl.setAttribute('x', newX); textEl.setAttribute('y', newY);
            document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => { o.setAttribute('x', newX); o.setAttribute('y', newY); });
            _applyFlipTransform(layer, window.getMainSvg());
            if (layer.hasPattern && layer.patternId) _updatePatternPosition(layer);
            if (layer.hasMascot && layer.mascotId) _updateMascotPatternPosition(layer);
        }
        if (window.saveCustomizations) window.saveCustomizations();
        _updatePlusIconPosition(layer.id);
    };

    window.updateRotation = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;
        layer.rotation = parseInt(value);
        const textEl = document.getElementById(layer.id);
        if (textEl) _applyFlipTransform(layer, textEl.ownerSVGElement);
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateOutlineStroke = function (type, value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;
        const textEl = document.getElementById(layer.id);
        if (!textEl) return;
        if (type === 'outline1') { textEl.setAttribute('stroke-width', value); layer.strokeWidth = parseInt(value); }
        const outlines = document.querySelectorAll(`[data-outline-for="${layer.id}"]`);
        if (type === 'outline2' && outlines.length) outlines[0].setAttribute('stroke-width', value);
        if (window.saveCustomizations) window.saveCustomizations();
    };

    /* ====================================================
         STROKE SHAPE SELECTORS — Line Cap & Line Join
         ==================================================== */

    window.selectStrokeLinecap = function (value, btn) {
        document.querySelectorAll('[id^="cap-"]').forEach(function (b) {
            b.classList.remove('selected');
        });
        if (btn) btn.classList.add('selected');

        if (!window.currentApplicationLayer) return;
        var layer = window.findLayerById ? window.findLayerById(window.currentApplicationLayer) : null;
        if (!layer) return;
        layer.strokeLinecap = value;
        var textEl = document.getElementById(layer.id);
        if (textEl) textEl.setAttribute('stroke-linecap', value);
        document.querySelectorAll('[data-outline-for="' + layer.id + '"]').forEach(function (o) {
            o.setAttribute('stroke-linecap', value);
        });
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.selectStrokeLinejoin = function (value, btn) {
        document.querySelectorAll('[id^="join-"]').forEach(function (b) {
            b.classList.remove('selected');
        });
        if (btn) btn.classList.add('selected');

        if (!window.currentApplicationLayer) return;
        var layer = window.findLayerById ? window.findLayerById(window.currentApplicationLayer) : null;
        if (!layer) return;
        layer.strokeLinejoin = value;
        var textEl = document.getElementById(layer.id);
        if (textEl) textEl.setAttribute('stroke-linejoin', value);
        document.querySelectorAll('[data-outline-for="' + layer.id + '"]').forEach(function (o) {
            o.setAttribute('stroke-linejoin', value);
        });
        if (window.saveCustomizations) window.saveCustomizations();
    };

    /* Restore stroke shape UI when layer is selected */
    window.restoreStrokeShapeUI = function (layer) {
        if (!layer) return;

        /* Line Cap */
        var cap = layer.strokeLinecap || 'butt';
        document.querySelectorAll('[id^="cap-"]').forEach(function (b) { b.classList.remove('selected'); });
        var capBtn = document.getElementById('cap-' + cap);
        if (capBtn) capBtn.classList.add('selected');

        /* Line Join */
        var join = layer.strokeLinejoin || 'miter';
        document.querySelectorAll('[id^="join-"]').forEach(function (b) { b.classList.remove('selected'); });
        var joinBtn = document.getElementById('join-' + join);
        if (joinBtn) joinBtn.classList.add('selected');
    };

    /* ====================================================
       ROTATION RANGE SLIDER sync with existing wheel
       ==================================================== */
    (function () {
        var rangeSlider = document.getElementById('rotationRangeSlider');
        if (!rangeSlider) return;

        /* Patch updateRotation to also sync range slider */
        var _origUpdateRotation = window.updateRotation;
        window.updateRotation = function (value) {
            if (rangeSlider) rangeSlider.value = value;
            var rv = document.getElementById('rotationValue');
            if (rv) rv.textContent = value;
            if (_origUpdateRotation) _origUpdateRotation(value);
        };

        /* Patch setWheelAngle to also sync range slider */
        var _origSetWheelAngle = window.setWheelAngle;
        window.setWheelAngle = function (a) {
            if (rangeSlider) rangeSlider.value = Math.round(((a % 360) + 360) % 360);
            if (_origSetWheelAngle) _origSetWheelAngle(a);
        };
    })();

    /* ====================================================
       HOOK: restore stroke shape UI on selectApplicationLayer
       ==================================================== */
    (function () {
        var _origSelect = window.selectApplicationLayer;
        window.selectApplicationLayer = function (layerId) {
            if (_origSelect) _origSelect(layerId);
            var layer = window.findLayerById ? window.findLayerById(layerId) : null;
            if (layer && layer.type !== 'direct-mascot') {
                window.restoreStrokeShapeUI(layer);
            }
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
        const textEl = document.getElementById(layer.id);
        if (!textEl) return;
        _applyScaleTransform(textEl, layer);
        document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => _applyScaleTransform(o, layer));
        if (window.saveCustomizations) window.saveCustomizations();
        setTimeout(() => _showPlusIcon(layer.id), 50);
    };

    window.deleteCurrentApplicationLayer = function () {
        if (!window.currentApplicationLayer) return;
        if (!confirm('Delete this application?')) return;
        removeApplicationLayer(window.currentApplicationLayer);
    };

    function _applyScaleTransform(el, layer) {
        const x = el.getAttribute('x'); const y = el.getAttribute('y');
        const scaleX = layer.scaleX || 1; const scaleY = layer.scaleY || 1;
        const rot = layer.rotation || 0; const flipX = layer.flipX || 1;
        let transform = `translate(${x},${y}) scale(${flipX === -1 ? -scaleX : scaleX},${scaleY}) translate(${-x},${-y})`;
        if (rot !== 0) transform += ` rotate(${rot} ${x} ${y})`;
        el.setAttribute('transform', transform);
    }

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
        const grid = document.getElementById('fontGrid'); grid.innerHTML = '';
        const previewText = document.getElementById('applicationText')?.value || '85';
        const currentLayer = findLayerById(window.currentApplicationLayer);
        window.backendFonts.forEach(f => {
            const div = document.createElement('div');
            div.style.cssText = 'border:2px solid #ddd;padding:20px;text-align:center;border-radius:8px;cursor:pointer;background:#fff';
            div.innerHTML = `<div style="font-size:42px;font-family:font_${f.id}">${previewText}</div><p style="margin-top:10px;font-weight:600">${f.name}</p>`;
            if (currentLayer?.fontFamily === `font_${f.id}`) { div.style.border = '2px solid #000'; div.style.background = '#8d8d8d'; }
            div.onmouseenter = () => { if (currentLayer?.fontFamily !== `font_${f.id}`) div.style.background = '#f2f2f2'; };
            div.onmouseleave = () => { if (currentLayer?.fontFamily !== `font_${f.id}`) div.style.background = '#fff'; };
            div.onclick = () => { window.updateFontFamily(`font_${f.id}`); closeFontModal(); };
            grid.appendChild(div);
        });
    }

    // ============================================================
    // =================== PATTERN - CORE FIX ===================
    // TEXT KE ANDAR PATTERN APPLY KARNE KA SAHI TARIKA
    // userSpaceOnUse + getBBox() with retry — yeh text ke saath kaam karta hai
    // ============================================================

    function _getTextBBox(textEl) {
        try {
            const bb = textEl.getBBox();
            if (bb.width > 0 && bb.height > 0) return bb;
        } catch (e) { }
        return null;
    }

    // Pattern ko text ke exact position/size par set karo
    function _updatePatternPosition(layer) {
        const mainSvg = window.getMainSvg();
        if (!mainSvg) return;
        const textEl = document.getElementById(layer.id);
        const pattern = mainSvg.querySelector(`#${layer.patternId}`);
        if (!textEl || !pattern) return;
        const bb = _getTextBBox(textEl);
        if (!bb) return;
        pattern.setAttribute('x', bb.x);
        pattern.setAttribute('y', bb.y);
        pattern.setAttribute('width', bb.width);
        pattern.setAttribute('height', bb.height);
        const innerSvg = pattern.querySelector('svg');
        if (innerSvg) {
            innerSvg.setAttribute('width', bb.width);
            innerSvg.setAttribute('height', bb.height);
        }
        // patternSize scale apply karo
        if (layer.patternSize && layer.patternSize !== 100) {
            const scale = layer.patternSize / 100;
            const scaledW = bb.width * scale;
            const scaledH = bb.height * scale;
            pattern.setAttribute('width', scaledW);
            pattern.setAttribute('height', scaledH);
            if (innerSvg) { innerSvg.setAttribute('width', scaledW); innerSvg.setAttribute('height', scaledH); }
        }
    }


    function _updateMascotPatternPosition(layer) {
        const mainSvg = window.getMainSvg();
        if (!mainSvg) return;
        const textEl = document.getElementById(layer.id);
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
        patternCopy.setAttribute('x', '0'); patternCopy.setAttribute('y', '0');
        const baseW = parseFloat(patternEl.getAttribute('width')) || 100;
        const baseH = parseFloat(patternEl.getAttribute('height')) || 100;
        patternCopy.setAttribute('width', baseW); patternCopy.setAttribute('height', baseH);
        const innerSvg = patternCopy.querySelector('svg');
        if (innerSvg) { innerSvg.setAttribute('width', baseW); innerSvg.setAttribute('height', baseH); innerSvg.setAttribute('preserveAspectRatio', 'xMidYMid slice'); }
        const opacity = layer.patternOpacity !== undefined ? layer.patternOpacity / 100 : 1;
        const previewSvgStr = `<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><defs>${patternCopy.outerHTML}</defs><rect width="200" height="200" fill="url(#preview-temp-pattern)" opacity="${opacity}"/></svg>`;
        const blob = new Blob([previewSvgStr], { type: 'image/svg+xml' });
        previewImg.src = URL.createObjectURL(blob);
    }

    // ============================================================
    // MAIN FIX: applyPatternToText
    // userSpaceOnUse + getBBox() — text ke exact andar fill
    // ============================================================
    window.applyPatternToText = function (svgContent, forcedLayerId = null) {
        const layerId = forcedLayerId || window.currentApplicationLayer;
        if (!layerId) return;
        const layer = findLayerById(layerId);
        if (!layer) return;
        const textEl = document.getElementById(layer.id);
        if (!textEl) return;
        const mainSvg = window.getMainSvg();
        let defs = mainSvg.querySelector('defs');
        if (!defs) { defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs'); mainSvg.insertBefore(defs, mainSvg.firstChild); }

        const parser = new DOMParser();
        const doc = parser.parseFromString(svgContent, 'image/svg+xml');
        let patternSvg = doc.documentElement;
        if (!patternSvg || patternSvg.nodeName !== 'svg') { alert('Pattern SVG invalid'); return; }
        patternSvg = patternSvg.cloneNode(true);
        if (!patternSvg.getAttribute('viewBox')) patternSvg.setAttribute('viewBox', '0 0 100 100');

        const patternId = `text-pattern-${layer.id}`;
        const oldPattern = defs.querySelector(`#${patternId}`);
        if (oldPattern) oldPattern.remove();

        const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
        pattern.setAttribute('id', patternId);
        pattern.setAttribute('patternUnits', 'userSpaceOnUse');

        // getBBox se text ka exact size lo — agar zero ho toh default use karo
        let bb = _getTextBBox(textEl);
        if (!bb) {
            // Font load nahi hua — default part bbox use karo
            const partEl = mainSvg.querySelector(`#${layer.partId}`);
            if (partEl) { const pb = partEl.getBBox(); bb = { x: pb.x + pb.width / 2 - 100, y: pb.y + pb.height / 2 - 50, width: 200, height: 100 }; }
            else { bb = { x: 0, y: 0, width: 200, height: 100 }; }
        }

        pattern.setAttribute('x', bb.x);
        pattern.setAttribute('y', bb.y);
        pattern.setAttribute('width', bb.width);
        pattern.setAttribute('height', bb.height);

        patternSvg.setAttribute('width', bb.width);
        patternSvg.setAttribute('height', bb.height);
        patternSvg.setAttribute('preserveAspectRatio', 'xMidYMid slice');

        pattern.appendChild(patternSvg);
        defs.appendChild(pattern);

        textEl.setAttribute('fill', `url(#${patternId})`);
        textEl.setAttribute('fill-opacity', '1');

        layer.patternId = patternId;
        layer.patternSvg = svgContent;
        layer.hasPattern = true;

        // UI update
        const patternControls = document.getElementById('textPatternColorControls');
        const patternPlaceholder = document.getElementById('patternPlaceholder');
        const soc = document.getElementById('textPatternSizeOpacityControls');
        if (patternControls) patternControls.style.display = 'block';
        if (patternPlaceholder) patternPlaceholder.style.display = 'none';
        if (soc) soc.style.display = 'block';

        // Pattern tab pe switch karo
        switchTextCustomizationTab('pattern');
        renderTextPatternPalette(svgContent);

        const preview = document.getElementById('textPatternPreview');
        if (preview) { preview.innerHTML = svgContent; const s = preview.querySelector('svg'); if (s) { s.style.width = '70px'; s.style.height = '70px'; s.style.display = 'block'; } }

        // Font load hone ke baad position update karo
        setTimeout(() => {
            _updatePatternPosition(layer);
            updateTextPatternButtonPreview();
        }, 300);
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
        const palette = (window.backendColors || []).map(c => c.code).filter(Boolean);
        if (!palette.length) return;
        colors.forEach(patternColor => {
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;gap:12px;margin-bottom:12px';
            row.innerHTML = `<div style="width:30px;height:30px;border-radius:6px;background:${patternColor};border:2px solid #ccc"></div><span style="font-weight:700;">→</span><div class="text-color-row" style="display:flex;gap:6px"></div>`;
            const choices = row.querySelector('.text-color-row');
            palette.forEach(userColor => {
                const box = document.createElement('div');
                box.style.cssText = `width:26px;height:26px;border-radius:6px;background:${userColor};cursor:pointer;border:2px solid #ddd;`;
                if (layer.replacements[patternColor?.toLowerCase()] === userColor) box.style.outline = '2px solid #000';
                box.onclick = () => {
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
        const textEl = document.getElementById(layer.id);
        if (!textEl) return;
        const oldX = pattern.getAttribute('x'); const oldY = pattern.getAttribute('y');
        const oldW = pattern.getAttribute('width'); const oldH = pattern.getAttribute('height');
        const oldTransform = pattern.getAttribute('patternTransform');
        const parser = new DOMParser(); const doc = parser.parseFromString(layer.patternSvg, 'image/svg+xml');
        const svg = doc.querySelector('svg');
        Object.entries(layer.replacements || {}).forEach(([oldColor, newColor]) => {
            svg.querySelectorAll('[fill]').forEach(e => { if (e.getAttribute('fill')?.toLowerCase() === oldColor) e.setAttribute('fill', newColor); });
        });
        pattern.innerHTML = ''; pattern.appendChild(svg);
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
        const textEl = document.getElementById(layer.id);
        if (!textEl) return;
        const mainSvg = window.getMainSvg();
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
        const textEl = document.getElementById(layer.id);
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
        const textEl = document.getElementById(layer.id);
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
    // MAIN FIX: applyMascotToText
    // ============================================================
    window.applyMascotToText = function (svgContent, forcedLayerId = null) {
        const layerId = forcedLayerId || window.currentApplicationLayer;
        if (!layerId) return;
        const layer = findLayerById(layerId);
        if (!layer) return;

        if (layer.type === 'direct-mascot') { applyDirectMascotToLayer(svgContent, layerId, true); return; }

        if (layer.hasPattern) {
            const mainSvgTemp = window.getMainSvg();
            if (layer.patternId) { const p = mainSvgTemp.querySelector(`#${layer.patternId}`); if (p) p.remove(); }
            delete layer.patternSvg; delete layer.hasPattern; delete layer.patternId; delete layer.patternSize; delete layer.patternOpacity;
            const patternControls = document.getElementById('textPatternColorControls');
            const patternPlaceholder = document.getElementById('patternPlaceholder');
            const soc = document.getElementById('textPatternSizeOpacityControls');
            if (patternControls) patternControls.style.display = 'none';
            if (patternPlaceholder) patternPlaceholder.style.display = 'block';
            if (soc) soc.style.display = 'none';
            const previewImg = document.getElementById('textPatternThumbnail');
            if (previewImg) previewImg.src = '/assets/images/pattern logo.avif';
        }

        const textEl = document.getElementById(layer.id);
        if (!textEl) return;
        const mainSvg = window.getMainSvg();
        let defs = mainSvg.querySelector('defs');
        if (!defs) { defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs'); mainSvg.insertBefore(defs, mainSvg.firstChild); }

        const parser = new DOMParser(); const doc = parser.parseFromString(svgContent, 'image/svg+xml');
        let mascotSvg = doc.documentElement;
        if (!mascotSvg || mascotSvg.nodeName !== 'svg') { alert('Mascot SVG load failed'); return; }
        mascotSvg = mascotSvg.cloneNode(true);

        const vb = (mascotSvg.getAttribute('viewBox') || '0 0 100 100').split(' ').map(Number);
        mascotSvg.querySelectorAll('rect,polygon,circle,ellipse').forEach(el => {
            const x = parseFloat(el.getAttribute('x') || 0); const y = parseFloat(el.getAttribute('y') || 0);
            const w = parseFloat(el.getAttribute('width') || 0); const h = parseFloat(el.getAttribute('height') || 0);
            if (x <= 1 && y <= 1 && w >= vb[2] * 0.7 && h >= vb[3] * 0.7) el.remove();
        });
        mascotSvg.querySelectorAll('*').forEach(el => {
            const style = el.getAttribute('style') || ''; const fill = el.getAttribute('fill') || '';
            const isWhite = fill === '#fff' || fill === '#ffffff' || fill === 'white' || style.includes('fill:#fff') || style.includes('fill:white');
            if (isWhite && (el.tagName.toLowerCase() === 'rect' || el.tagName.toLowerCase() === 'polygon')) el.remove();
        });
        if (!mascotSvg.getAttribute('viewBox')) mascotSvg.setAttribute('viewBox', '0 0 100 100');
        mascotSvg.style.background = 'transparent';

        const mascotId = `text-mascot-${layer.id}`;
        const oldMascot = defs.querySelector(`#${mascotId}`);
        if (oldMascot) oldMascot.remove();

        const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
        pattern.setAttribute('id', mascotId);
        pattern.setAttribute('patternUnits', 'userSpaceOnUse');

        let bb = _getTextBBox(textEl);
        if (!bb) {
            const partEl = mainSvg.querySelector(`#${layer.partId}`);
            if (partEl) { const pb = partEl.getBBox(); bb = { x: pb.x + pb.width / 2 - 100, y: pb.y + pb.height / 2 - 50, width: 200, height: 100 }; }
            else { bb = { x: 0, y: 0, width: 200, height: 100 }; }
        }

        const count = layer.mascotCount || 4;
        const tileSize = Math.min(bb.width, bb.height) / count;

        pattern.setAttribute('x', bb.x);
        pattern.setAttribute('y', bb.y);
        pattern.setAttribute('width', tileSize);
        pattern.setAttribute('height', tileSize);

        mascotSvg.setAttribute('width', tileSize);
        mascotSvg.setAttribute('height', tileSize);
        mascotSvg.setAttribute('preserveAspectRatio', 'xMidYMid meet');
        mascotSvg.removeAttribute('x'); mascotSvg.removeAttribute('y');

        pattern.appendChild(mascotSvg);
        defs.appendChild(pattern);

        textEl.setAttribute('fill', `url(#${mascotId})`);
        textEl.setAttribute('fill-opacity', '1');

        layer.mascotSvg = svgContent; layer.hasMascot = true; layer.mascotId = mascotId;
        layer.mascotSize = 100; layer.mascotOpacity = 100; layer.mascotCount = count;

        const mascotControls = document.getElementById('textMascotColorControls');
        const mascotPlaceholder = document.getElementById('mascotPlaceholder');
        const msoc = document.getElementById('textMascotSizeOpacityControls');
        if (mascotControls) mascotControls.style.display = 'block';
        if (mascotPlaceholder) mascotPlaceholder.style.display = 'none';
        if (msoc) msoc.style.display = 'block';

        const preview = document.getElementById('textMascotPreview');
        if (preview) { preview.innerHTML = svgContent; const s = preview.querySelector('svg'); if (s) { s.style.maxWidth = '60px'; s.style.maxHeight = '60px'; } }

        ['mascotSizeTabSlider', 'mascotOpacityTabSlider', 'mascotCountTabSlider'].forEach(id => { const el = document.getElementById(id); if (el) el.value = id.includes('Count') ? count : 100; });
        ['mascotSizeValueTab', 'mascotOpacityValueTab'].forEach(id => { const el = document.getElementById(id); if (el) el.textContent = '100'; });
        const cve = document.getElementById('mascotCountValueTab'); if (cve) cve.textContent = count;

        // Font load hone ke baad position update karo
        setTimeout(() => _updateMascotPatternPosition(layer), 300);

        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.clearTextMascot = function () {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasMascot) return;
        const textEl = document.getElementById(layer.id);
        if (!textEl) return;
        const mainSvg = window.getMainSvg();
        if (layer.mascotId) { const p = mainSvg.querySelector(`#${layer.mascotId}`); if (p) p.remove(); }
        textEl.setAttribute('fill', (layer.outlineColors || window.outlineColors).baseColor || '#FFFFFF');
        textEl.setAttribute('fill-opacity', '1');
        delete layer.mascotSvg; delete layer.hasMascot; delete layer.mascotId; delete layer.mascotSize; delete layer.mascotOpacity; delete layer.mascotCount; delete layer.mascotTileSize;
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
        const textEl = document.getElementById(layer.id);
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
        const textEl = document.getElementById(layer.id);
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
        const textEl = document.getElementById(layer.id);
        const bb = textEl ? _getTextBBox(textEl) : null;
        if (!bb) return;
        const count = parseInt(value);
        const tileSize = Math.min(bb.width, bb.height) / count;
        pattern.setAttribute('width', tileSize); pattern.setAttribute('height', tileSize);
        const svg = pattern.querySelector('svg');
        if (svg) { svg.setAttribute('width', tileSize); svg.setAttribute('height', tileSize); }
        layer.mascotCount = count;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    // ── Library openers ────────────────────────────────────────

    window.openTextPatternLibrary = function () {
        // currentApplicationLayer check hatao — library seedha kholo
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

    // KEY FIX: applyPatternFromLibrary — currentApplicationLayer check ke saath
    window.applyPatternFromLibrary = function (svgContent) {
        if (window.currentApplicationLayer) {
            // Application layer selected hai — text mein fill karo
            window.applyPatternToText(svgContent);
            window.selectingPatternForText = false;
            return;
        }
        // Normal pattern system
        window.selectingPatternForText = false;
        if (window.applyUploadedPattern) window.applyUploadedPattern(svgContent);
    };

    window.applyMascotFromLibrary = function (svgContent) {
        if (window.selectingMascotForText && window.currentApplicationLayer) {
            window.applyMascotToText(svgContent); window.selectingMascotForText = false;
        } else if (window.applyUploadedMascot) { window.applyUploadedMascot(); }
    };

    // ============================================================
    // =================== REMOVE / FIND LAYER ===================
    // ============================================================

    window.removeApplicationLayer = function (layerId, event) {
        if (event) event.stopPropagation();
        Object.keys(window.applicationsApplied).forEach(view => {
            Object.keys(window.applicationsApplied[view]).forEach(partId => {
                window.applicationsApplied[view][partId] = window.applicationsApplied[view][partId].filter(l => l.id !== layerId);
                if (!window.applicationsApplied[view][partId].length) delete window.applicationsApplied[view][partId];
            });
        });
        const layerGroup = document.getElementById(`app-group-${layerId}`);
        if (layerGroup) layerGroup.remove();
        const mainSvg = window.getMainSvg();
        if (mainSvg) { const clip = mainSvg.querySelector(`#clip-${layerId}`); if (clip) clip.remove(); }
        document.querySelectorAll(`[data-outline-for="${layerId}"]`).forEach(o => o.remove());
        if (window.currentApplicationLayer === layerId) {
            window.currentApplicationLayer = null;
            document.getElementById('applicationLayerControls').style.display = 'none';
            _hidePlusIcon(); _hideMascotBox();
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
    // =================== RESTORE ON LOAD ===================
    // ============================================================

    window.initializeApplicationsOnLoad = function () {
        if (!window.applicationsApplied) return;
        const view = window.currentView;
        if (!window.applicationsApplied[view] || !Object.keys(window.applicationsApplied[view]).length) return;
        const svg = window.getMainSvg();
        if (!svg) { setTimeout(window.initializeApplicationsOnLoad, 300); return; }

        svg.querySelectorAll('[id^="app-group-"]').forEach(g => g.remove());
        svg.querySelectorAll('#application-group').forEach(g => g.remove());
        svg.querySelectorAll('[data-outline-for]').forEach(e => e.remove());

        Object.entries(window.applicationsApplied[view]).forEach(([partId, layers]) => {
            layers.forEach(layer => {
                addApplicationToSvg(layer);
                if (layer.type === 'direct-mascot' && layer.mascotSvg && layer._colorMap && Object.keys(layer._colorMap).length) {
                    (l => setTimeout(() => _applyDirectMascotColorMap(l), 400))(layer);
                }
                if (layer.hasPattern && layer.patternSvg) {
                    const prev = window.currentApplicationLayer; window.currentApplicationLayer = layer.id;
                    window.applyPatternToText(layer.patternSvg, layer.id);
                    if (layer.patternSize) setTimeout(() => window.updateTextPatternSize(layer.patternSize), 400);
                    if (layer.patternOpacity) window.updateTextPatternOpacity(layer.patternOpacity);
                    window.currentApplicationLayer = prev;
                }
                if (layer.hasMascot && layer.mascotSvg) {
                    const prev = window.currentApplicationLayer; window.currentApplicationLayer = layer.id;
                    window.applyMascotToText(layer.mascotSvg, layer.id);
                    if (layer.mascotSize) setTimeout(() => window.updateTextMascotSize(layer.mascotSize), 400);
                    if (layer.mascotOpacity) window.updateTextMascotOpacity(layer.mascotOpacity);
                    if (layer.mascotCount) setTimeout(() => window.updateTextMascotCount(layer.mascotCount), 400);
                    window.currentApplicationLayer = prev;
                }
            });
        });
        updateApplicationLayersList();
    };

    window.reorderSvgLayers = function (view, partId, orderedLayers) {
        const mainSvg = window.getMainSvg(); if (!mainSvg) return;
        orderedLayers.forEach(layer => {
            const group = mainSvg.querySelector(`#app-group-${layer.id}`);
            if (group) mainSvg.appendChild(group);
            if (layer.flipX && layer.flipX !== 1) setTimeout(() => _applyFlipTransform(layer, mainSvg), 50);
        });
    };

    document.addEventListener('click', function (e) {
        const plusIcon = document.getElementById('appPlusDragIcon');
        if (plusIcon && plusIcon.contains(e.target)) return;
        const mascotBox = document.getElementById('appMascotDragBox');
        if (mascotBox && mascotBox.contains(e.target)) return;
        const mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg || !mainSvg.contains(e.target)) return;
        let el = e.target; let clickedLayer = false;
        while (el && el !== mainSvg) {
            if (el.id && el.id.startsWith('app-group-')) { clickedLayer = true; break; }
            if (el.id && window.findLayerById && window.findLayerById(el.id)) { clickedLayer = true; break; }
            el = el.parentElement;
        }
        if (!clickedLayer) {
            _hidePlusIcon(); _hideMascotBox();
            window.currentApplicationLayer = null;
            const controls = document.getElementById('applicationLayerControls');
            if (controls) controls.style.display = 'none';
            if (window.updateApplicationLayersList) window.updateApplicationLayersList();
        }
    });

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
        const snapThreshold = 25; // degrees

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
            // Progress arc
            const progress = (a / 360) * circumference;
            arc.setAttribute('stroke-dasharray', progress + ' ' + circumference);

            // Dot position on edge
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

        // Touch support
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
    (function () {
        const observer = new MutationObserver(mutations => {
            mutations.forEach(m => {
                if (m.type !== 'attributes' || m.attributeName !== 'style') return;
                const el = m.target;
                if (!el.id || !el.id.toLowerCase().includes('modal')) return;
                const visible = el.style.display !== 'none' && el.style.display !== '';
                if (visible) { _hideMascotBox(); }
                else if (window.currentApplicationLayer) { const layer = findLayerById(window.currentApplicationLayer); if (layer && layer.type === 'direct-mascot') setTimeout(() => _showMascotBox(window.currentApplicationLayer), 200); }
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

    console.log('✅ applications.js — complete fix loaded');






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









})();
