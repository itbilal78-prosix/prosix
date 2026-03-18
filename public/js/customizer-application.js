(function () {
    'use strict';

    // =================== APPLICATION STATE ===================

    window.applicationLayers = [];
    window.currentApplicationLayer = null;
    window.selectedApplicationType = 'number';
    window.selectedApplicationView = 'front';
    window.selectedApplicationPart = null;
    window.modalPreviewText = null;

    if (!window.applicationsApplied) window.applicationsApplied = {};

    // =================== NEW: ACCENT/OUTLINE STATE ===================
    window.currentOutlineStyle = 'single'; // default
    window.outlineColors = {
        baseColor: '#FFFFFF',
        outline1: '#000000',
        outline2: '#666666',
        shadow: '#333333'
    };
    document.addEventListener("contextmenu", function (e) {
        e.preventDefault();
    });
    document.addEventListener("keydown", function (e) {
        if (e.ctrlKey && (e.key === 'c' || e.key === 'u')) {
            e.preventDefault();
        }
    });
    // =================== SIDEBAR TOGGLE ===================

    window.toggleApplicationsSidebar = function () {
        const sidebar = document.getElementById('applicationsSidebar');
        const toggleIcon = document.getElementById('applicationsSidebarToggleIcon');

        if (!sidebar) return;

        sidebar.classList.toggle('open');

        if (toggleIcon) {
            if (sidebar.classList.contains('open')) {
                toggleIcon.style.opacity = '0';
                toggleIcon.style.pointerEvents = 'none';
            } else {
                toggleIcon.style.opacity = '1';
                toggleIcon.style.pointerEvents = 'auto';
            }
        }
    };

    window.openApplicationsSidebar = function () {
        const sidebar = document.getElementById('applicationsSidebar');
        if (!sidebar) return;
        sidebar.classList.add('open');
    };

    window.closeApplicationsSidebar = function () {
        const sidebar = document.getElementById('applicationsSidebar');
        if (!sidebar) return;
        sidebar.classList.remove('open');
    };

    // =================== TAB SWITCHING ===================

    window.switchTextCustomizationTab = function (tabName) {

        // Update tab buttons
        document.querySelectorAll('.text-custom-tab').forEach(tab => {
            tab.style.borderBottom = '3px solid transparent';
            tab.style.color = '#999';
        });

        const activeTab = document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1));
        if (activeTab) {
            activeTab.style.borderBottom = '3px solid #000';
            activeTab.style.color = '#333';
        }

        // Show/hide tab content
        document.getElementById('colorsTabContent').style.display = tabName === 'colors' ? 'block' : 'none';
        document.getElementById('patternTabContent').style.display = tabName === 'pattern' ? 'block' : 'none';
        document.getElementById('mascotTabContent').style.display = tabName === 'mascot' ? 'block' : 'none';

        console.log(`✅ Switched to ${tabName} tab`);
    };

    // =================== ACCENTS MODAL ===================

    window.openAccentsModal = function () {
        document.getElementById('accentsModal').style.display = 'flex';
        console.log('✅ Accents modal opened');
    };

    window.closeAccentsModal = function () {
        document.getElementById('accentsModal').style.display = 'none';
        console.log('✅ Accents modal closed');
    };

    window.selectAccentStyle = function (styleName) {

        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        layer.outlineStyle = styleName;

        if (!layer.outlineColors) {
            layer.outlineColors = {
                baseColor: '#ffffff',
                outline1: '#000000',
                outline2: '#666666',
                shadow: '#333333'
            };
        }

        if (layer.shadowOffset === undefined) layer.shadowOffset = 3;

        window.currentOutlineStyle = styleName;
        window.outlineColors = { ...layer.outlineColors };

        applyOutlineStyleToText(layer.id);

        updateOutlineStylePreview();
        showHideColorPickers(styleName);
        populateOutlineColorPickers();


        // 🔥 REAL TIME STYLE NAME UPDATE
        const styleNameEl = document.getElementById('outlineStyleName');
        if (styleNameEl) {
            const styleNames = {
                'single': 'Single Color',
                'two-color': 'Two Color',
                'two-color-shadow': 'Two Color with Drop Shadow',
                'three-color': 'Three Color',
                'single-shadow': 'Single Color with Drop Shadow',
                'three-color-shadow': 'Three Color with Drop Shadow'
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

        // Reset styles
        preview.style.color = '';
        preview.style.webkitTextFillColor = '';
        preview.style.webkitTextStroke = '';
        preview.style.filter = '';
        preview.style.position = '';

        switch (style) {
            case 'single':
                preview.style.color = colors.baseColor;
                break;

            case 'two-color':
                preview.style.webkitTextFillColor = colors.baseColor;
                preview.style.webkitTextStroke = `3px ${colors.outline1}`;
                break;

            case 'two-color-shadow':
                preview.style.webkitTextFillColor = colors.baseColor;
                preview.style.webkitTextStroke = `3px ${colors.outline1}`;
                preview.style.filter = `drop-shadow(3px 3px 0 ${colors.shadow})`;
                break;

            case 'three-color':
                preview.style.webkitTextFillColor = colors.baseColor;
                preview.style.webkitTextStroke = `3px ${colors.outline1}`;
                preview.title = 'Outline 2: ' + colors.outline2;
                break;

            case 'single-shadow':
                preview.style.color = colors.baseColor;
                preview.style.filter = `drop-shadow(3px 3px 0 ${colors.shadow})`;
                break;

            case 'three-color-shadow':
                preview.style.webkitTextFillColor = colors.baseColor;
                preview.style.webkitTextStroke = `3px ${colors.outline1}`;
                preview.style.filter = `drop-shadow(3px 3px 0 ${colors.shadow})`;
                preview.title = 'Outline 2: ' + colors.outline2;
                break;
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
            case 'single':
                break;
            case 'two-color':
                outline1.style.display = 'flex';
                break;
            case 'two-color-shadow':
                outline1.style.display = 'flex';
                shadow.style.display = 'flex';
                break;
            case 'three-color':
                outline1.style.display = 'flex';
                outline2.style.display = 'flex';
                break;
            case 'single-shadow':
                shadow.style.display = 'flex';
                break;
            case 'three-color-shadow':
                outline1.style.display = 'flex';
                outline2.style.display = 'flex';
                shadow.style.display = 'flex';
                break;
        }
    };

    window.populateOutlineColorPickers = function () {

        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        const colors = window.selectedColors?.length
            ? window.selectedColors
            : ['#FFFFFF', '#000000'];

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
            box.style.borderRadius = '6px';
            box.style.cursor = 'pointer';
            box.style.border = color.toUpperCase() === selectedColor.toUpperCase()
                ? '3px solid #007bff'
                : '2px solid #ddd';
            box.style.transition = 'all 0.2s';

            box.onclick = function () {
                callback(color);
                container.querySelectorAll('div').forEach(b => b.style.border = '2px solid #ddd');
                this.style.border = '3px solid #007bff';
            };

            container.appendChild(box);
        });
    };

    // =================== APPLY OUTLINE STYLE TO TEXT ===================

    window.applyOutlineStyleToText = function (layerId) {

        const layer = findLayerById(layerId);
        if (!layer) return;

        const textEl = document.getElementById(layerId);
        if (!textEl) return;

        const colors = layer.outlineColors;
        const style = layer.outlineStyle;
        const shadow = layer.shadowOffset ?? 3;
        const stroke = layer.strokeWidth || 5;

        // remove old outlines
        textEl.parentElement
            .querySelectorAll(`[data-outline-for="${layerId}"]`)
            .forEach(e => e.remove());

        // IMPORTANT: base text NEVER shadow
        textEl.style.filter = 'none';

        switch (style) {

            case 'single':
                textEl.setAttribute('fill', colors.baseColor);
                textEl.removeAttribute('stroke');
                textEl.removeAttribute('stroke-width');
                textEl.style.filter = 'none';
                break;

            case 'single-shadow':
                textEl.setAttribute('fill', colors.baseColor);
                textEl.removeAttribute('stroke');
                textEl.removeAttribute('stroke-width');
                textEl.style.filter =
                    `drop-shadow(${shadow}px ${shadow}px 0 ${colors.shadow})`;
                break;

            case 'two-color':
                textEl.setAttribute('fill', colors.baseColor);
                textEl.setAttribute('stroke', colors.outline1);
                textEl.setAttribute('stroke-width', stroke);
                textEl.style.filter = 'none';
                break;

            case 'two-color-shadow':
                textEl.setAttribute('fill', colors.baseColor);
                textEl.setAttribute('stroke', colors.outline1);
                textEl.setAttribute('stroke-width', stroke);
                textEl.style.filter =
                    `drop-shadow(${shadow}px ${shadow}px 0 ${colors.shadow})`;
                break;

            case 'three-color':
            case 'three-color-shadow':
                createThreeColorOutline(textEl, layer, style.includes('shadow'));
                break;
        }

    };


    // =================== THREE COLOR OUTLINE ===================

    window.createThreeColorOutline = function (textEl, layer, withShadow) {

        const colors = layer.outlineColors;
        const shadow = layer.shadowOffset ?? 3;
        const stroke = layer.strokeWidth || 5;

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
        outer.setAttribute('stroke-linejoin', 'round');
        outer.style.pointerEvents = 'none';

        if (withShadow) {
            outer.style.filter = `drop-shadow(${shadow}px ${shadow}px 0 ${colors.shadow})`;
        } else {
            outer.style.filter = 'none';
        }

        textEl.parentElement.insertBefore(outer, textEl);

        textEl.setAttribute('fill', colors.baseColor);
        textEl.setAttribute('stroke', colors.outline1);
        textEl.setAttribute('stroke-width', stroke);
        textEl.style.filter = 'none';
    };


    // =================== OPEN/CLOSE APPLICATION MODAL ===================

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
        if (previewContainer) {
            previewContainer.innerHTML = '<p style="color:#999; font-size:14px;">Select options to preview</p>';
        }

        if (window.modalPreviewText) {
            window.modalPreviewText.remove();
            window.modalPreviewText = null;
        }
    };

    // =================== MODAL SELECTIONS ===================

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

        if (window.switchView) {
            window.switchView(view);
        }

        setTimeout(() => {
            loadPartsForApplicationModal();
            updateModalPreview();
        }, 300);
    };

    window.loadPartsForApplicationModal = function () {
        const container = document.getElementById('partSelectionGrid');
        if (!container) return;

        const view = window.selectedApplicationView;
        const modelData = window.modelDataByView?.[view];

        if (!modelData?.parts || modelData.parts.length === 0) {
            container.innerHTML = '<p style="color:#999; text-align:center; padding:20px;">No parts available</p>';
            return;
        }

        container.innerHTML = '';

        modelData.parts.forEach((part, index) => {
            const btn = document.createElement('button');
            btn.className = 'part-btn';
            btn.textContent = part.title || `Part ${index + 1}`;
            btn.dataset.partId = part.part;
            btn.dataset.partIndex = index;

            if (part.part === window.selectedApplicationPart) {
                btn.classList.add('selected');
            }

            btn.onclick = function () {
                window.selectedApplicationPart = part.part;
                const partIndex = parseInt(this.dataset.partIndex);

                if (window.allSvgParts && window.allSvgParts[partIndex]) {
                    if (window.selectSvgElement) {
                        window.selectSvgElement(window.allSvgParts[partIndex]);
                    }
                }

                container.querySelectorAll('.part-btn').forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');

                updateModalPreview();
            };

            container.appendChild(btn);
        });

        console.log(`✅ Loaded ${modelData.parts.length} parts for ${view}`);
    };

    // =================== MODAL PREVIEW ===================

    window.updateModalPreview = function () {
        const previewContainer = document.getElementById('modalPreviewSvg');
        if (!previewContainer) return;

        if (!window.selectedApplicationView || !window.selectedApplicationPart) {
            previewContainer.innerHTML = '<p style="color:#999; font-size:14px;">Select view and part to preview</p>';
            return;
        }

        const mainSvg = window.getMainSvg();
        if (!mainSvg) {
            previewContainer.innerHTML = '<p style="color:#999; font-size:14px;">SVG not loaded</p>';
            return;
        }

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
        if (!partElement) {
            previewContainer.innerHTML = '<p style="color:#dc3545; font-size:14px;">Part not found</p>';
            return;
        }

        const bbox = partElement.getBBox();
        const centerX = bbox.x + (bbox.width / 2);
        const centerY = bbox.y + (bbox.height / 2);

        clonedSvg.querySelectorAll('.modal-preview-text').forEach(t => t.remove());

        const globalColors = window.selectedColors || ['#FFFFFF', '#000000'];
        const textColor = globalColors[0] || '#FFFFFF';
        const strokeColor = globalColors[1] || '#000000';

        const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        text.classList.add('modal-preview-text');
        text.setAttribute('x', centerX);
        text.setAttribute('y', centerY);
        text.setAttribute('font-size', '2000');
        text.style.fontFamily = 'Arial Black';
        text.setAttribute('fill', textColor);
        text.setAttribute('stroke', strokeColor);
        text.setAttribute('stroke-width', '6');
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('dominant-baseline', 'middle');
        text.setAttribute('paint-order', 'stroke fill');
        text.setAttribute('stroke-linejoin', 'round');
        text.textContent = defaultText;

        clonedSvg.appendChild(text);

        previewContainer.innerHTML = '';
        previewContainer.appendChild(clonedSvg);

        console.log(`✅ Modal preview updated`);
    };

    // =================== CONFIRM ADD APPLICATION ===================

    // window.confirmAddApplication = function () {

    //     if (!window.selectedApplicationPart) {
    //         alert('Please select a part!');
    //         return;
    //     }

    //     const view = window.selectedApplicationView;
    //     const partId = window.selectedApplicationPart;

    //     // ====================================================
    //     // 🦅 MASCOT TYPE — Direct SVG mascot (no text layer)
    //     // ====================================================
    //     if (window.selectedApplicationType === 'mascot') {

    //         closeApplicationModal();

    //         const layerId = `app-${Date.now()}`;

    //         // Mascot layer — type 'direct-mascot' to distinguish from text layers
    //         const layer = {
    //             id: layerId,
    //             type: 'direct-mascot',
    //             view: view,
    //             partId: partId,
    //             // No text fields needed — this is a direct SVG mascot
    //             mascotSvg: null,
    //             mascotId: null,
    //             x: 0,
    //             y: 0,
    //             rotation: 0,
    //             mascotScaleX: 1,
    //             mascotScaleY: 1,
    //             mascotOpacity: 100
    //         };

    //         if (!window.applicationsApplied[view]) window.applicationsApplied[view] = {};
    //         if (!window.applicationsApplied[view][partId]) window.applicationsApplied[view][partId] = [];

    //         window.applicationsApplied[view][partId].push(layer);

    //         // Set current layer so mascot modal can apply to it
    //         window.currentApplicationLayer = layerId;

    //         updateApplicationLayersList();
    //         openApplicationsSidebar();

    //         // Open mascot select modal — when user picks mascot it will call applyDirectMascotToLayer
    //         window.openMascotSelectModal(layerId);

    //         if (window.saveCustomizations) window.saveCustomizations();

    //         console.log('✅ Direct mascot layer created:', layerId);
    //         return;
    //     }

    //     // ====================================================
    //     // TEXT TYPES — number, teamname, playername
    //     // ====================================================

    //     const layerId = `app-${Date.now()}`;

    //     let defaultText = '00';
    //     if (window.selectedApplicationType === 'teamname') defaultText = 'TEAM';
    //     if (window.selectedApplicationType === 'playername') defaultText = 'PLAYER';

    //     const globalColors = window.selectedColors || ['#FFFFFF', '#000000'];

    //     const layer = {
    //         id: layerId,
    //         type: window.selectedApplicationType,
    //         view: view,
    //         partId: partId,
    //         text: defaultText,
    //         fontSize: 2000,
    //         fontFamily: window.backendFonts?.[0] ? `font_${window.backendFonts[0].id}` : 'Arial Black',
    //         fill: globalColors[0] || '#FFFFFF',
    //         stroke: globalColors[1] || '#000000',
    //         strokeWidth: 5,
    //         x: 0,
    //         y: 0,
    //         rotation: 0,
    //         outlineStyle: window.currentOutlineStyle,
    //         outlineColors: { ...window.outlineColors }
    //     };

    //     if (!window.applicationsApplied[view]) window.applicationsApplied[view] = {};
    //     if (!window.applicationsApplied[view][partId]) window.applicationsApplied[view][partId] = [];

    //     window.applicationsApplied[view][partId].push(layer);

    //     addApplicationToSvg(layer);
    //     updateApplicationLayersList();
    //     closeApplicationModal();
    //     openApplicationsSidebar();
    //     selectApplicationLayer(layerId);

    //     if (window.saveCustomizations) window.saveCustomizations();

    //     console.log('✅ Text application added:', layer);
    // };

    // ============================================================
    // SIRF YEH FUNCTION apni applications.js mein replace karo
    // "window.confirmAddApplication" wala poora function
    // ============================================================

    window.confirmAddApplication = function () {

        if (!window.selectedApplicationPart) {
            alert('Please select a part!');
            return;
        }

        const view = window.selectedApplicationView;
        const partId = window.selectedApplicationPart;

        // ====================================================
        // 🦅 MASCOT TYPE
        // ====================================================
        if (window.selectedApplicationType === 'mascot') {

            const layerId = 'app-' + Date.now();

            const layer = {
                id: layerId,
                type: 'direct-mascot',
                view: view,
                partId: partId,
                mascotSvg: null,
                mascotId: null,
                x: 0,
                y: 0,
                rotation: 0,
                mascotScaleX: 1,
                mascotScaleY: 1,
                mascotOpacity: 100
            };

            if (!window.applicationsApplied[view]) window.applicationsApplied[view] = {};
            if (!window.applicationsApplied[view][partId]) window.applicationsApplied[view][partId] = [];
            window.applicationsApplied[view][partId].push(layer);

            window.currentApplicationLayer = layerId;

            updateApplicationLayersList();
            openApplicationsSidebar();

            if (window.saveCustomizations) window.saveCustomizations();

            // ✅ Pehle application modal band karo
            const appModal = document.getElementById('applicationModal');
            if (appModal) appModal.style.display = 'none';

            // ✅ 250ms baad mascot modal kholo (modal close animation finish ho jay)
            setTimeout(function () {
                if (typeof window.openMascotSelectModal === 'function') {
                    window.openMascotSelectModal(layerId);
                } else {
                    console.error('❌ openMascotSelectModal function not found!');
                    alert('Mascot modal load nahi hua. Page reload karein.');
                }
            }, 250);

            return;
        }

        // ====================================================
        // TEXT TYPES — number, teamname, playername
        // ====================================================
        const layerId = 'app-' + Date.now();

        let defaultText = '00';
        if (window.selectedApplicationType === 'teamname') defaultText = 'TEAM';
        if (window.selectedApplicationType === 'playername') defaultText = 'PLAYER';

        const globalColors = window.selectedColors || ['#FFFFFF', '#000000'];

        const layer = {
            id: layerId,
            type: window.selectedApplicationType,
            view: view,
            partId: partId,
            text: defaultText,
            fontSize: 2000,
            fontFamily: window.backendFonts?.[0] ? 'font_' + window.backendFonts[0].id : 'Arial Black',
            fill: globalColors[0] || '#FFFFFF',
            stroke: globalColors[1] || '#000000',
            strokeWidth: 5,
            x: 0,
            y: 0,
            rotation: 0,
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

        console.log('✅ Text application added:', layer);
    };
    // =================== ADD TEXT APPLICATION TO SVG ===================

    window.addApplicationToSvg = function (layer) {

        // Skip direct-mascot type — these are handled by applyDirectMascotToLayer
        if (layer.type === 'direct-mascot') {
            if (layer.mascotSvg) {
                applyDirectMascotToLayer(layer.mascotSvg, layer.id, false);
            }
            return;
        }

        const mainSvg = window.getMainSvg();
        if (!mainSvg) {
            setTimeout(() => addApplicationToSvg(layer), 300);
            return;
        }

        // ✅ DUPLICATE CHECK
        if (mainSvg.querySelector(`#${layer.id}`)) {
            console.log('⚠ Layer already exists, skipping:', layer.id);
            return;
        }

        // CREATE DEFS
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

        // CLIP PATH — unique per layer
        const clipId = `clip-${layer.id}`;

        if (!defs.querySelector(`#${clipId}`)) {
            const clip = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
            clip.setAttribute('id', clipId);
            const clone = partElement.cloneNode(true);
            clone.removeAttribute('id');
            clip.appendChild(clone);
            defs.appendChild(clip);
        }

        // INDIVIDUAL GROUP PER LAYER
        const layerGroupId = `app-group-${layer.id}`;
        let layerGroup = mainSvg.querySelector(`#${layerGroupId}`);
        if (!layerGroup) {
            layerGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            layerGroup.setAttribute('id', layerGroupId);
            layerGroup.setAttribute('clip-path', `url(#${clipId})`);
            mainSvg.appendChild(layerGroup);
        }

        // CENTER OF PART
        const bbox = partElement.getBBox();
        const cx = bbox.x + bbox.width / 2;
        const cy = bbox.y + bbox.height / 2;

        // CREATE TEXT
        const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        text.setAttribute('id', layer.id);
        text.setAttribute('x', cx + layer.x);
        text.setAttribute('y', cy + layer.y);
        text.setAttribute('font-size', layer.fontSize);
        text.style.fontFamily = layer.fontFamily;
        text.setAttribute('fill', layer.fill);
        text.setAttribute('stroke', layer.stroke);
        text.setAttribute('stroke-width', layer.strokeWidth);
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('dominant-baseline', 'middle');
        text.setAttribute('paint-order', 'stroke fill');
        text.setAttribute('stroke-linejoin', 'round');
        text.style.cursor = 'move';
        text.textContent = layer.text;

        if (layer.rotation) {
            text.setAttribute(
                'transform',
                `rotate(${layer.rotation} ${cx + layer.x} ${cy + layer.y})`
            );
        }

        layerGroup.appendChild(text);

        makeDraggable(text, layer);

        text.addEventListener('click', e => {
            e.stopPropagation();
            selectApplicationLayer(layer.id);
        });

        if (layer.outlineStyle) {
            window.currentOutlineStyle = layer.outlineStyle;
            if (layer.outlineColors) {
                window.outlineColors = { ...layer.outlineColors };
            }
            applyOutlineStyleToText(layer.id);
        }

        console.log('✅ Text application added to SVG:', layer.id);
    };


    // =================== APPLY DIRECT MASCOT TO LAYER (SVG IMAGE) ===================

    window.applyDirectMascotToLayer = function (svgContent, forcedLayerId, fromModal) {

        const layerId = forcedLayerId || window.currentApplicationLayer;
        if (!layerId) return;

        const layer = findLayerById(layerId);
        if (!layer || layer.type !== 'direct-mascot') return;

        const mainSvg = window.getMainSvg();
        if (!mainSvg) return;

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

        // ===== CLIP PATH (per layer) =====
        const clipId = `clip-${layerId}`;
        if (!defs.querySelector(`#${clipId}`)) {
            const clip = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
            clip.setAttribute('id', clipId);
            const clone = partElement.cloneNode(true);
            clone.removeAttribute('id');
            clip.appendChild(clone);
            defs.appendChild(clip);
        }

        // ===== LAYER GROUP =====
        const layerGroupId = `app-group-${layerId}`;
        let layerGroup = mainSvg.querySelector(`#${layerGroupId}`);
        if (layerGroup) layerGroup.remove(); // remove old if re-applying

        layerGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        layerGroup.setAttribute('id', layerGroupId);
        layerGroup.setAttribute('clip-path', `url(#${clipId})`);
        mainSvg.appendChild(layerGroup);

        // ===== PART BBOX =====
        const bbox = partElement.getBBox();
        const cx = bbox.x + bbox.width / 2;
        const cy = bbox.y + bbox.height / 2;

        // ===== PARSE MASCOT SVG =====
        const parser = new DOMParser();
        const doc = parser.parseFromString(svgContent, 'image/svg+xml');
        let mascotSvg = doc.documentElement;

        if (!mascotSvg || mascotSvg.nodeName !== 'svg') {
            alert('Mascot SVG load failed');
            return;
        }

        mascotSvg = mascotSvg.cloneNode(true);

        // ✅ Background remove
        const vb = (mascotSvg.getAttribute('viewBox') || '0 0 100 100').split(' ').map(Number);
        mascotSvg.querySelectorAll('rect, polygon, circle, ellipse').forEach(el => {
            const x = parseFloat(el.getAttribute('x') || 0);
            const y = parseFloat(el.getAttribute('y') || 0);
            const w = parseFloat(el.getAttribute('width') || 0);
            const h = parseFloat(el.getAttribute('height') || 0);
            const coversAll = x <= 1 && y <= 1 && w >= vb[2] * 0.7 && h >= vb[3] * 0.7;
            if (coversAll) el.remove();
        });

        mascotSvg.querySelectorAll('*').forEach(el => {
            const style = el.getAttribute('style') || '';
            const fill = el.getAttribute('fill') || '';
            const isWhite = fill === '#fff' || fill === '#ffffff' || fill === 'white' ||
                style.includes('fill:#fff') || style.includes('fill: #fff') ||
                style.includes('fill:white') || style.includes('fill:#ffffff');
            const tag = el.tagName.toLowerCase();
            if (isWhite && (tag === 'rect' || tag === 'polygon')) el.remove();
        });

        mascotSvg.style.background = 'transparent';

        // Ensure viewBox
        if (!mascotSvg.getAttribute('viewBox')) {
            mascotSvg.setAttribute('viewBox', '0 0 100 100');
        }

        // Size mascot to fit part (maintain aspect ratio, fit inside bbox)
        const mascotSize = Math.min(bbox.width, bbox.height) * (layer.mascotScaleX || 1);
        mascotSvg.setAttribute('width', mascotSize);
        mascotSvg.setAttribute('height', mascotSize);
        mascotSvg.setAttribute('preserveAspectRatio', 'xMidYMid meet');

        // Position: center on part center
        const mascotX = cx - mascotSize / 2 + (layer.x || 0);
        const mascotY = cy - mascotSize / 2 + (layer.y || 0);
        mascotSvg.setAttribute('x', mascotX);
        mascotSvg.setAttribute('y', mascotY);
        mascotSvg.setAttribute('id', layerId);
        mascotSvg.style.cursor = 'move';

        // Opacity
        if (layer.mascotOpacity !== undefined) {
            mascotSvg.setAttribute('opacity', layer.mascotOpacity / 100);
        }

        // Rotation
        if (layer.rotation) {
            mascotSvg.setAttribute('transform', `rotate(${layer.rotation} ${cx} ${cy})`);
        }

        layerGroup.appendChild(mascotSvg);

        // Store data on layer
        layer.mascotSvg = svgContent;
        layer.mascotId = layerId;
        layer._cx = cx;
        layer._cy = cy;
        layer._mascotSize = mascotSize;

        // Make draggable
        makeMascotDraggable(mascotSvg, layer);

        mascotSvg.addEventListener('click', e => {
            e.stopPropagation();
            selectApplicationLayer(layerId);
        });

        // Auto-select this layer and show mascot settings
        window.currentApplicationLayer = layerId;
        updateApplicationLayersList();
        selectApplicationLayer(layerId);

        if (window.saveCustomizations) window.saveCustomizations();

        console.log('✅ Direct mascot applied to SVG:', layerId);
    };


    // =================== MAKE MASCOT DRAGGABLE ===================

    window.makeMascotDraggable = function (element, layer) {
        let isDragging = false;
        let startX, startY, initOffX, initOffY;

        element.addEventListener('mousedown', function (e) {
            if (e.button !== 0) return;
            isDragging = true;
            element.style.cursor = 'grabbing';

            const svg = element.ownerSVGElement || element.closest('svg');
            const pt = svg.createSVGPoint();
            pt.x = e.clientX; pt.y = e.clientY;
            const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());
            startX = svgP.x;
            startY = svgP.y;
            initOffX = layer.x || 0;
            initOffY = layer.y || 0;
            e.preventDefault();
        });

        document.addEventListener('mousemove', function (e) {
            if (!isDragging) return;
            const svg = element.ownerSVGElement || element.closest('svg');
            const pt = svg.createSVGPoint();
            pt.x = e.clientX; pt.y = e.clientY;
            const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());

            const dx = svgP.x - startX;
            const dy = svgP.y - startY;

            layer.x = Math.round(initOffX + dx);
            layer.y = Math.round(initOffY + dy);

            const cx = layer._cx || 0;
            const cy = layer._cy || 0;
            const sz = layer._mascotSize || 100;

            element.setAttribute('x', cx - sz / 2 + layer.x);
            element.setAttribute('y', cy - sz / 2 + layer.y);
        });

        document.addEventListener('mouseup', function () {
            if (!isDragging) return;
            isDragging = false;
            element.style.cursor = 'move';

            if (window.currentApplicationLayer === layer.id) {
                const xInput = document.getElementById('mascotDirectPosX');
                const yInput = document.getElementById('mascotDirectPosY');
                if (xInput) { xInput.value = layer.x; document.getElementById('mascotDirectPosXValue').textContent = layer.x; }
                if (yInput) { yInput.value = layer.y; document.getElementById('mascotDirectPosYValue').textContent = layer.y; }
            }

            if (window.saveCustomizations) window.saveCustomizations();
        });
    };


    window.makeDraggable = function (element, layer) {
        let isDragging = false;
        let startX, startY, initialX, initialY;

        element.addEventListener('mousedown', function (e) {
            if (e.button !== 0) return;

            isDragging = true;
            element.style.cursor = 'grabbing';

            const svg = element.ownerSVGElement;
            const pt = svg.createSVGPoint();
            pt.x = e.clientX;
            pt.y = e.clientY;
            const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());

            startX = svgP.x;
            startY = svgP.y;

            initialX = parseFloat(element.getAttribute('x'));
            initialY = parseFloat(element.getAttribute('y'));

            e.preventDefault();
        });

        document.addEventListener('mousemove', function (e) {
            if (!isDragging) return;

            const svg = element.ownerSVGElement;
            const pt = svg.createSVGPoint();
            pt.x = e.clientX;
            pt.y = e.clientY;
            const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());

            const dx = svgP.x - startX;
            const dy = svgP.y - startY;

            const newX = Math.round(initialX + dx);
            const newY = Math.round(initialY + dy);

            element.setAttribute('x', newX);
            element.setAttribute('y', newY);

            const outlines = element.parentElement.querySelectorAll(`[data-outline-for="${layer.id}"]`);
            outlines.forEach(outline => {
                outline.setAttribute('x', newX);
                outline.setAttribute('y', newY);
            });

            if (layer.rotation) {
                element.setAttribute('transform', `rotate(${layer.rotation} ${newX} ${newY})`);
                outlines.forEach(outline => {
                    outline.setAttribute('transform', `rotate(${layer.rotation} ${newX} ${newY})`);
                });
            }
        });

        document.addEventListener('mouseup', function () {
            if (!isDragging) return;

            isDragging = false;
            element.style.cursor = 'move';

            const partElement = document.querySelector(`#${layer.partId}`);
            if (partElement) {
                const bbox = partElement.getBBox();
                const centerX = bbox.x + (bbox.width / 2);
                const centerY = bbox.y + (bbox.height / 2);

                const currentX = parseFloat(element.getAttribute('x'));
                const currentY = parseFloat(element.getAttribute('y'));

                layer.x = Math.round(currentX - centerX);
                layer.y = Math.round(currentY - centerY);

                if (window.currentApplicationLayer === layer.id) {
                    updateApplicationControls(layer);
                }

                if (window.saveCustomizations) window.saveCustomizations();
            }
        });
    };

    // =================== UPDATE LAYERS LIST ===================

    //     window.updateApplicationLayersList = function () {

    //         const container = document.getElementById('applicationLayersList');
    //         if (!container) return;

    //         container.innerHTML = '';

    //         if (!window.applicationsApplied || Object.keys(window.applicationsApplied).length === 0) {
    //             container.innerHTML = '<p style="color:#999;text-align:center;padding:20px;font-size:13px;">No applications added yet</p>';
    //             return;
    //         }

    //         let layerNum = 1;

    //         Object.entries(window.applicationsApplied).forEach(([view, parts]) => {
    //             Object.entries(parts).forEach(([partId, layers]) => {
    //                 layers.forEach(layer => {

    //                     const viewShort = view.charAt(0).toUpperCase();
    //                     const isActive = window.currentApplicationLayer === layer.id;

    //                     // Label for layer list
    //                     const labelText = layer.type === 'direct-mascot'
    //                         ? (layer.mascotTitle || (layer.mascotSvg ? 'Mascot' : 'Mascot (pending)'))
    //                         : (layer.text || 'Empty');

    //                     const typeLabel = layer.type === 'direct-mascot' ? 'mascot' : layer.type;

    //                     const item = document.createElement('div');
    //                     item.className = 'application-layer-item';
    //                     item.style.background = isActive ? '#000' : '#fff';
    //                     item.style.color = isActive ? '#fff' : '#000';

    //                     item.innerHTML = `
    // <div style="display:flex;align-items:center;gap:8px;width:100%">
    //     <div class="layer-number">#${layerNum}</div>
    //     <div style="flex:1">
    //         <div style="font-weight:700">
    //             ${labelText}
    //             <span style="background:#333;color:#fff;padding:2px 6px;border-radius:4px;font-size:10px;margin-left:6px;">${viewShort}</span>
    //         </div>
    //         <div style="font-size:11px;opacity:.7">${typeLabel} • ${partId}</div>
    //     </div>
    //     <div onclick="duplicateApplicationLayer('${layer.id}',event)"
    //         style="cursor:pointer;font-size:14px;padding:4px 6px;border-radius:4px;background:#eee;color:#000">
    //         ⧉ ${viewShort}
    //     </div>
    //     <div onclick="removeApplicationLayer('${layer.id}',event)"
    //         style="cursor:pointer;padding:0 6px;font-weight:700">×</div>
    // </div>`;

    //                     item.onclick = function () { selectApplicationLayer(layer.id); };
    //                     container.appendChild(item);
    //                     layerNum++;
    //                 });
    //             });
    //         });
    //     };


    // =================== PATCH 1: LAYER REORDERING (Drag & Drop) ===================
    // Replace window.updateApplicationLayersList with this version

    window.updateApplicationLayersList = function () {

        const container = document.getElementById('applicationLayersList');
        if (!container) return;

        container.innerHTML = '';

        if (!window.applicationsApplied || Object.keys(window.applicationsApplied).length === 0) {
            container.innerHTML = '<p style="color:#999;text-align:center;padding:20px;font-size:13px;">No applications added yet</p>';
            return;
        }

        let layerNum = 1;

        Object.entries(window.applicationsApplied).forEach(([view, parts]) => {
            Object.entries(parts).forEach(([partId, layers]) => {
                layers.forEach((layer, index) => {

                    const viewShort = view.charAt(0).toUpperCase();
                    const isActive = window.currentApplicationLayer === layer.id;

                    const labelText = layer.type === 'direct-mascot'
                        ? (layer.mascotTitle || (layer.mascotSvg ? 'Mascot' : 'Mascot (pending)'))
                        : (layer.text || 'Empty');

                    const typeLabel = layer.type === 'direct-mascot' ? 'mascot' : layer.type;

                    const item = document.createElement('div');
                    item.className = 'application-layer-item';
                    item.setAttribute('draggable', 'true');
                    item.dataset.layerId = layer.id;
                    item.dataset.view = view;
                    item.dataset.partId = partId;
                    item.dataset.index = index;

                    item.style.cssText = `
                    display:flex; align-items:center; gap:8px; width:100%;
                    padding:10px 12px; margin-bottom:6px; border-radius:8px;
                    background:${isActive ? '#000' : '#fff'};
                    color:${isActive ? '#fff' : '#000'};
                    border:2px solid ${isActive ? '#000' : '#e0e0e0'};
                    cursor:pointer; transition:all 0.2s;
                    user-select:none;
                `;

                    item.innerHTML = `
                    <div class="drag-handle" style="cursor:grab; font-size:16px; opacity:0.5; padding:0 4px; flex-shrink:0;" title="Drag to reorder">⠿</div>
                    <div class="layer-number" style="font-weight:700; font-size:13px; flex-shrink:0;">#${layerNum}</div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-weight:700; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            ${labelText}
                            <span style="background:#333;color:#fff;padding:2px 6px;border-radius:4px;font-size:10px;margin-left:6px;">${viewShort}</span>
                        </div>
                        <div style="font-size:11px;opacity:.7">${typeLabel} • ${partId}</div>
                    </div>
                    <div onclick="duplicateApplicationLayer('${layer.id}',event)"
                        style="cursor:pointer;font-size:13px;padding:4px 6px;border-radius:4px;background:#eee;color:#000;flex-shrink:0;">
                        ⧉
                    </div>
                    <div onclick="removeApplicationLayer('${layer.id}',event)"
                        style="cursor:pointer;padding:0 6px;font-weight:700;flex-shrink:0;">×</div>
                `;

                    // Click to select
                    item.onclick = function (e) {
                        if (e.target.closest('.drag-handle')) return;
                        selectApplicationLayer(layer.id);
                    };

                    // ===== DRAG & DROP EVENTS =====
                    item.addEventListener('dragstart', function (e) {
                        e.dataTransfer.effectAllowed = 'move';
                        e.dataTransfer.setData('text/plain', layer.id);
                        this.style.opacity = '0.4';
                        window._draggingLayerId = layer.id;
                        window._draggingView = view;
                        window._draggingPartId = partId;
                    });

                    item.addEventListener('dragend', function () {
                        this.style.opacity = '1';
                        container.querySelectorAll('.application-layer-item').forEach(el => {
                            el.style.borderColor = window.currentApplicationLayer === el.dataset.layerId ? '#000' : '#e0e0e0';
                            el.style.transform = '';
                        });
                        window._draggingLayerId = null;
                    });

                    item.addEventListener('dragover', function (e) {
                        e.preventDefault();
                        e.dataTransfer.dropEffect = 'move';
                        if (window._draggingLayerId === layer.id) return;
                        this.style.borderColor = '#007bff';
                        this.style.transform = 'scale(1.01)';
                    });

                    item.addEventListener('dragleave', function () {
                        this.style.borderColor = isActive ? '#000' : '#e0e0e0';
                        this.style.transform = '';
                    });

                    item.addEventListener('drop', function (e) {
                        e.preventDefault();
                        this.style.transform = '';

                        const fromId = window._draggingLayerId;
                        const fromView = window._draggingView;
                        const fromPartId = window._draggingPartId;
                        const toId = layer.id;
                        const toView = view;
                        const toPartId = partId;

                        if (fromId === toId) return;

                        // Only reorder within same view+part
                        if (fromView === toView && fromPartId === toPartId) {
                            const arr = window.applicationsApplied[fromView][fromPartId];
                            const fromIdx = arr.findIndex(l => l.id === fromId);
                            const toIdx = arr.findIndex(l => l.id === toId);

                            if (fromIdx !== -1 && toIdx !== -1) {
                                // Swap
                                const [removed] = arr.splice(fromIdx, 1);
                                arr.splice(toIdx, 0, removed);

                                // Re-render SVG layer order
                                reorderSvgLayers(fromView, fromPartId, arr);

                                updateApplicationLayersList();
                                if (window.saveCustomizations) window.saveCustomizations();
                            }
                        }
                    });

                    container.appendChild(item);
                    layerNum++;
                });
            });
        });
    };


    window.duplicateApplicationLayer = function (layerId, event) {

        if (event) event.stopPropagation();

        const original = findLayerById(layerId);
        if (!original) return;

        const view = original.view;
        const partId = original.partId;

        const copy = JSON.parse(JSON.stringify(original));
        copy.id = `app-${Date.now()}`;
        copy.x += 20;
        copy.y += 20;

        if (!window.applicationsApplied[view]) window.applicationsApplied[view] = {};
        if (!window.applicationsApplied[view][partId]) window.applicationsApplied[view][partId] = [];

        window.applicationsApplied[view][partId].push(copy);

        addApplicationToSvg(copy);
        updateApplicationLayersList();
        selectApplicationLayer(copy.id);

        if (window.saveCustomizations) window.saveCustomizations();

        console.log('✅ Layer duplicated', copy.id);
    };

    // =================== SELECT LAYER ===================

    window.selectApplicationLayer = function (layerId) {
        window.currentApplicationLayer = layerId;

        const foundLayer = findLayerById(layerId);
        if (!foundLayer) {
            console.warn('Layer not found:', layerId);
            return;
        }

        // AUTO VIEW SWITCH
        if (foundLayer.view && window.currentView !== foundLayer.view) {
            if (window.switchView) window.switchView(foundLayer.view);
            setTimeout(() => window.initializeApplicationsOnLoad(), 400);
        }

        updateApplicationLayersList();

        // Show controls panel
        document.getElementById('applicationLayerControls').style.display = 'block';

        // ================================================
        // 🦅 DIRECT MASCOT LAYER — show only mascot panel
        // ================================================
        if (foundLayer.type === 'direct-mascot') {
            showDirectMascotControls(foundLayer);
            return;
        }

        // ================================================
        // TEXT LAYER — show text controls
        // ================================================
        showTextLayerControls(foundLayer);
    };


    // =================== SHOW DIRECT MASCOT CONTROLS ===================

    function showDirectMascotControls(layer) {

        // Hide ALL children of applicationLayerControls except directMascotControls
        const appControls = document.getElementById('applicationLayerControls');
        if (appControls) {
            Array.from(appControls.children).forEach(child => {
                if (child.id !== 'directMascotControls') {
                    child.style.display = 'none';
                }
            });
        }

        // Show direct mascot controls section
        const directMascotControls = document.getElementById('directMascotControls');
        if (directMascotControls) directMascotControls.style.display = 'block';

        // Populate preview
        const preview = document.getElementById('directMascotPreview');
        if (preview && layer.mascotSvg) {
            preview.innerHTML = layer.mascotSvg;
            const svg = preview.querySelector('svg');
            if (svg) { svg.style.maxWidth = '80px'; svg.style.maxHeight = '80px'; }
        } else if (preview) {
            preview.innerHTML = '<span style="color:#aaa;font-size:12px;">No mascot selected</span>';
        }

        // Sync sliders
        const scaleSlider = document.getElementById('directMascotScale');
        const scaleVal = document.getElementById('directMascotScaleValue');
        const opSlider = document.getElementById('directMascotOpacity');
        const opVal = document.getElementById('directMascotOpacityValue');
        const rotSlider = document.getElementById('directMascotRotation');
        const rotVal = document.getElementById('directMascotRotationValue');
        const posXSlider = document.getElementById('mascotDirectPosX');
        const posXVal = document.getElementById('mascotDirectPosXValue');
        const posYSlider = document.getElementById('mascotDirectPosY');
        const posYVal = document.getElementById('mascotDirectPosYValue');

        if (scaleSlider) { scaleSlider.value = Math.round((layer.mascotScaleX || 1) * 100); }
        if (scaleVal) { scaleVal.textContent = Math.round((layer.mascotScaleX || 1) * 100); }
        if (opSlider) { opSlider.value = layer.mascotOpacity ?? 100; }
        if (opVal) { opVal.textContent = layer.mascotOpacity ?? 100; }
        if (rotSlider) { rotSlider.value = layer.rotation || 0; }
        if (rotVal) { rotVal.textContent = layer.rotation || 0; }
        if (posXSlider) { posXSlider.value = layer.x || 0; }
        if (posXVal) { posXVal.textContent = layer.x || 0; }
        if (posYSlider) { posYSlider.value = layer.y || 0; }
        if (posYVal) { posYVal.textContent = layer.y || 0; }

        _renderDirectMascotColors(layer);


        console.log('✅ Direct mascot controls shown for:', layer.id);
    }










    function _renderDirectMascotColors(layer) {
        var container = document.getElementById('directMascotColorSwatches');
        if (!container) return;

        // ✅ Already detected hain to reuse karo — view change pe reset nahi hoga
        if (layer._detectedColors && layer._detectedColors.length) {
            _buildColorSwatches(layer._detectedColors, layer, container);
            return;
        }

        container.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;">Detecting colors...</p>';

        if (!layer.mascotSvg) {
            container.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;">No mascot selected</p>';
            return;
        }
var maxColors = layer._selectedColorCount || 6;
        var parser = new DOMParser();
        var doc = parser.parseFromString(layer.mascotSvg, 'image/svg+xml');
        var imgTag = doc.querySelector('image');

        if (imgTag) {
            var href = imgTag.getAttribute('href') || imgTag.getAttribute('xlink:href') || '';
            if (href.startsWith('data:image/png')) {
                _detectColorsFromPng(href, layer, container);
                return;
            }
        }

        var colorCounts = {};
        doc.querySelectorAll('[fill]').forEach(function (el) {
            var fill = el.getAttribute('fill') || '';
            if (fill && fill !== 'none' && fill !== 'transparent') {
                var hex = _normalizeColor(fill);
                if (hex && hex !== '#ffffff') colorCounts[hex] = (colorCounts[hex] || 0) + 1;
            }
        });
        doc.querySelectorAll('[style]').forEach(function (el) {
            var s = el.getAttribute('style') || '';
            var m = s.match(/fill:\s*(#[0-9a-fA-F]{3,6})/);
            if (m) {
                var hex = _normalizeColor(m[1]);
                if (hex && hex !== '#ffffff') colorCounts[hex] = (colorCounts[hex] || 0) + 1;
            }
        });
var maxColors = layer._selectedColorCount
    || (window.selectedColors && window.selectedColors.length)
    || 6;
        var detected = Object.keys(colorCounts)
            .sort(function (a, b) { return colorCounts[b] - colorCounts[a]; })
            .slice(0, maxColors);
        _buildColorSwatches(detected, layer, container);
    }



    function _detectColorsFromPng(dataUrl, layer, container) {
        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');
        var img = new Image();
        img.onload = function () {
            var SIZE = 100;
            canvas.width = SIZE;
            canvas.height = SIZE;
            ctx.drawImage(img, 0, 0, SIZE, SIZE);
            var imageData = ctx.getImageData(0, 0, SIZE, SIZE).data;
            var colorCounts = {};
            for (var i = 0; i < imageData.length; i += 4) {
                var a = imageData[i + 3];
                if (a < 30) continue;
                var r = Math.round(imageData[i] / 32) * 32;
                var g = Math.round(imageData[i + 1] / 32) * 32;
                var b = Math.round(imageData[i + 2] / 32) * 32;
                r = Math.min(r, 255); g = Math.min(g, 255); b = Math.min(b, 255);
                if (r > 230 && g > 230 && b > 230) continue;
                var hex = '#' + [r, g, b].map(function (v) { return v.toString(16).padStart(2, '0'); }).join('');
                colorCounts[hex] = (colorCounts[hex] || 0) + 1;
            }
            // ✅ Mascot modal ki selected count use karo
            var maxColors = layer._selectedColorCount || 6;
            var detected = Object.keys(colorCounts)
                .sort(function (a, b) { return colorCounts[b] - colorCounts[a]; })
                .slice(0, maxColors);
            _buildColorSwatches(detected, layer, container);
        };
        img.onerror = function () {
            container.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;">Could not load image</p>';
        };
        img.src = dataUrl;
    }

    function _buildColorSwatches(detectedColors, layer, container) {
        container.innerHTML = '';
        if (!detectedColors.length) {
            container.innerHTML = '<p style="font-size:12px;color:#aaa;text-align:center;">No colors detected</p>';
            return;
        }

        // ✅ Save karo taake view change / scale pe reset na ho
        layer._detectedColors = detectedColors;
        if (!layer._colorMap) layer._colorMap = {};

 // ✅ NAYA CODE — color wheel ke selectedColors use karo:
var backendColors = (window.selectedColors && window.selectedColors.length)
    ? window.selectedColors
    : (window.backendColors || []).map(function (c) { return c.code || c; });
if (!backendColors.length) backendColors = ['#FF0000', '#FF6600', '#FFFF00', '#00FF00', '#0000FF', '#800080', '#FFFFFF', '#000000', '#FF8800', '#00FFFF'];

        detectedColors.forEach(function (detectedHex) {
            var row = document.createElement('div');
            row.style.cssText = 'display:flex; align-items:center; gap:8px; margin-bottom:10px;';
            var fromBox = document.createElement('div');
            fromBox.style.cssText = 'width:26px; height:26px; border-radius:5px; border:2px solid #ccc; flex-shrink:0;';
            fromBox.style.background = detectedHex;
            var arrow = document.createElement('span');
            arrow.textContent = '→';
            arrow.style.cssText = 'font-size:13px; color:#888; flex-shrink:0;';
            var swatchRow = document.createElement('div');
            swatchRow.style.cssText = 'display:flex; flex-wrap:wrap; gap:4px;';
            var currentReplacement = layer._colorMap[detectedHex.toLowerCase()] || null;

            backendColors.forEach(function (hex) {
                var isSelected = currentReplacement && hex.toLowerCase() === currentReplacement.toLowerCase();
                var box = document.createElement('div');
                box.style.cssText = 'width:22px; height:22px; border-radius:4px; cursor:pointer; box-sizing:border-box; position:relative; display:flex; align-items:center; justify-content:center;';
                box.style.background = hex;
                box.style.border = isSelected ? '3px solid #1a1a1a' : '2px solid #ddd';
                if (isSelected) {
                    var check = document.createElement('span');
                    check.textContent = '✓';
                    check.style.cssText = 'font-size:13px; font-weight:900; line-height:1;';
                    check.style.color = _getContrastColor(hex);
                    box.appendChild(check);
                }
                box.onclick = (function (dHex, nHex, b, sRow) {
                    return function () {
                        layer._colorMap[dHex.toLowerCase()] = nHex;
                        sRow.querySelectorAll('div').forEach(function (x) { x.style.border = '2px solid #ddd'; x.innerHTML = ''; });
                        b.style.border = '3px solid #1a1a1a';
                        var ck = document.createElement('span');
                        ck.textContent = '✓';
                        ck.style.cssText = 'font-size:13px; font-weight:900; line-height:1;';
                        ck.style.color = _getContrastColor(nHex);
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
        if (!hex) return '#000000';
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        var r = parseInt(hex.substr(0, 2), 16);
        var g = parseInt(hex.substr(2, 2), 16);
        var b = parseInt(hex.substr(4, 2), 16);
        var luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        return luminance > 0.5 ? '#000000' : '#ffffff';
    }

    function _normalizeColor(color) {
        if (!color) return null;
        color = color.trim().toLowerCase();
        if (color.match(/^#[0-9a-f]{6}$/)) return color;
        if (color.match(/^#[0-9a-f]{3}$/)) {
            return '#' + color[1] + color[1] + color[2] + color[2] + color[3] + color[3];
        }
        return null;
    }

    function _applyDirectMascotColorMap(layer) {
        if (!layer.mascotSvg || !layer._colorMap) return;

        var parser = new DOMParser();
        var doc = parser.parseFromString(layer.mascotSvg, 'image/svg+xml');
        var imgTag = doc.querySelector('image');

        if (imgTag) {
            var href = imgTag.getAttribute('href') || imgTag.getAttribute('xlink:href') || '';
            if (href.startsWith('data:image/png')) {
                _applyColorMapToPng(href, layer);
                return;
            }
        }

        // Pure SVG fallback
        Object.entries(layer._colorMap).forEach(function (entry) {
            var oldColor = entry[0];
            var newColor = entry[1];
            doc.querySelectorAll('[fill]').forEach(function (el) {
                var f = el.getAttribute('fill');
                if (f && _normalizeColor(f) === oldColor) {
                    el.setAttribute('fill', newColor);
                }
            });
        });

        var modifiedSvg = new XMLSerializer().serializeToString(doc.documentElement);
        _replaceElementInSvg(layer, modifiedSvg);
    }

   function _applyColorMapToPng(dataUrl, layer) {
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');
    var img = new Image();

    img.onload = function () {
        canvas.width = img.width;
        canvas.height = img.height;
        ctx.drawImage(img, 0, 0);

        var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var data = imageData.data;

        // ✅ layer._alphaMask se transparent pixels lo (mcCanvas band hone ke baad bhi kaam karega)
        var alphaMask = layer._alphaMask || null;
        var maskW = layer._alphaMaskW || 0;
        var maskH = layer._alphaMaskH || 0;

        // colorMap ko RGB mein convert karo
        var colorMappings = [];
        Object.entries(layer._colorMap).forEach(function (entry) {
            var oldHex = entry[0];
            var newHex = entry[1];
            var oldRgb = _hexToRgb(oldHex);
            var newRgb = _hexToRgb(newHex);
            if (oldRgb && newRgb) {
                colorMappings.push({ old: oldRgb, new: newRgb });
            }
        });

        // Har pixel check karo
        for (var i = 0; i < data.length; i += 4) {
            var pixelIndex = i / 4;

            // ✅ Alpha mask se check karo — agar wahan transparent tha to yahan bhi transparent karo
            if (alphaMask && alphaMask.length > 0) {
                // Pixel index ko mask dimensions ke saath scale karo
                var px = pixelIndex % canvas.width;
                var py = Math.floor(pixelIndex / canvas.width);
                var mx = Math.round(px * maskW / canvas.width);
                var my = Math.round(py * maskH / canvas.height);
                var maskIndex = my * maskW + mx;
                if (alphaMask[maskIndex] !== undefined && alphaMask[maskIndex] < 30) {
                    data[i + 3] = 0; // transparent rakho
                    continue;
                }
            }

            var a = data[i + 3];
            if (a < 30) continue;

            var r = data[i];
            var g = data[i + 1];
            var b = data[i + 2];

            var bestMatch = null;
            var bestDist = 60;

            colorMappings.forEach(function (mapping) {
                var dist = Math.sqrt(
                    Math.pow(r - mapping.old.r, 2) +
                    Math.pow(g - mapping.old.g, 2) +
                    Math.pow(b - mapping.old.b, 2)
                );
                if (dist < bestDist) {
                    bestDist = dist;
                    bestMatch = mapping;
                }
            });

            if (bestMatch) {
                data[i]     = bestMatch.new.r;
                data[i + 1] = bestMatch.new.g;
                data[i + 2] = bestMatch.new.b;
            }
        }

        ctx.putImageData(imageData, 0, 0);

        var newPngDataUrl = canvas.toDataURL('image/png');

        var parser = new DOMParser();
        var doc = parser.parseFromString(layer.mascotSvg, 'image/svg+xml');
        var imgEl = doc.querySelector('image');
        if (imgEl) {
            imgEl.setAttribute('href', newPngDataUrl);
            if (imgEl.getAttribute('xlink:href')) {
                imgEl.setAttribute('xlink:href', newPngDataUrl);
            }
        }

        var modifiedSvg = new XMLSerializer().serializeToString(doc.documentElement);
        _replaceElementInSvg(layer, modifiedSvg);
    };

    img.onerror = function () {
        console.error('PNG load failed');
    };

    img.src = dataUrl;
}

    function _replaceElementInSvg(layer, modifiedSvg) {
        var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return;

        var svgEl = mainSvg.querySelector('#' + layer.id);
        if (!svgEl) return;

        var parser = new DOMParser();
        var innerDoc = parser.parseFromString(modifiedSvg, 'image/svg+xml');
        var newSvg = innerDoc.documentElement.cloneNode(true);

        // Purani properties copy karo
        newSvg.setAttribute('id', svgEl.getAttribute('id'));
        newSvg.setAttribute('x', svgEl.getAttribute('x') || 0);
        newSvg.setAttribute('y', svgEl.getAttribute('y') || 0);
        newSvg.setAttribute('width', svgEl.getAttribute('width') || 100);
        newSvg.setAttribute('height', svgEl.getAttribute('height') || 100);
        newSvg.setAttribute('preserveAspectRatio', 'xMidYMid meet');

        if (svgEl.getAttribute('transform')) {
            newSvg.setAttribute('transform', svgEl.getAttribute('transform'));
        }
        if (svgEl.getAttribute('opacity')) {
            newSvg.setAttribute('opacity', svgEl.getAttribute('opacity'));
        }
        newSvg.style.cursor = 'move';

        svgEl.parentNode.replaceChild(newSvg, svgEl);

        // Events dobara lagao
        newSvg.addEventListener('click', function (e) {
            e.stopPropagation();
            window.selectApplicationLayer(layer.id);
        });

        if (window.makeMascotDraggable) {
            window.makeMascotDraggable(newSvg, layer);
        }

        if (window.saveCustomizations) window.saveCustomizations();
    }

    // =================== SHOW TEXT LAYER CONTROLS ===================



    function _hexToRgb(hex) {
        if (!hex) return null;
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        return {
            r: parseInt(hex.substr(0, 2), 16),
            g: parseInt(hex.substr(2, 2), 16),
            b: parseInt(hex.substr(4, 2), 16)
        };
    }



    function showTextLayerControls(layer) {

        // Show ALL children of applicationLayerControls except directMascotControls
        const appControls = document.getElementById('applicationLayerControls');
        if (appControls) {
            Array.from(appControls.children).forEach(child => {
                if (child.id !== 'directMascotControls') {
                    child.style.display = '';
                }
            });
        }

        // Hide direct mascot section
        const directMascotControls = document.getElementById('directMascotControls');
        if (directMascotControls) directMascotControls.style.display = 'none';

        // Show text controls
        const textControls = document.getElementById('textLayerControls');
        if (textControls) textControls.style.display = 'block';

        // Load outline style
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
                const styleNames = {
                    'single': 'Single Color',
                    'two-color': 'Two Color',
                    'two-color-shadow': 'Two Color with Drop Shadow',
                    'three-color': 'Three Color',
                    'single-shadow': 'Single Color with Drop Shadow',
                    'three-color-shadow': 'Three Color with Drop Shadow'
                };
                styleNameEl.textContent = styleNames[window.currentOutlineStyle] || window.currentOutlineStyle;
            }
        }

        updateApplicationControls(layer);

        // Flash highlight
        const textEl = document.getElementById(layer.id);
        if (textEl) {
            textEl.style.filter = 'drop-shadow(0 0 8px rgba(0,123,255,0.8))';
            setTimeout(() => {
                if (layer.outlineStyle && layer.outlineStyle.includes('shadow')) {
                    applyOutlineStyleToText(layer.id);
                } else {
                    textEl.style.filter = '';
                }
            }, 1000);
        }

        // Pattern UI sync
        if (layer.hasPattern) {
            document.getElementById('textPatternColorControls').style.display = 'block';
            document.getElementById('patternPlaceholder').style.display = 'none';
            if (layer.patternSvg) renderTextPatternPalette(layer.patternSvg);
        } else {
            document.getElementById('textPatternColorControls').style.display = 'none';
            document.getElementById('patternPlaceholder').style.display = 'block';
        }

        // Mascot UI sync (text-fill mascot)
        if (layer.hasMascot) {
            document.getElementById('textMascotColorControls').style.display = 'block';
            document.getElementById('mascotPlaceholder').style.display = 'none';
        } else {
            document.getElementById('textMascotColorControls').style.display = 'none';
            document.getElementById('mascotPlaceholder').style.display = 'block';
        }

        // Switch to colors tab by default
        switchTextCustomizationTab('colors');
    }


    // =================== DIRECT MASCOT CONTROLS ===================

    window.updateDirectMascotScale = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || layer.type !== 'direct-mascot') return;

        layer.mascotScaleX = value / 100;
        layer.mascotScaleY = value / 100;

        if (layer.mascotSvg) {
            applyDirectMascotToLayer(layer.mascotSvg, layer.id, false);
            // ✅ Scale ke baad colorMap dobara apply karo
            if (layer._colorMap && Object.keys(layer._colorMap).length > 0) {
                setTimeout(function () { _applyDirectMascotColorMap(layer); }, 150);
            }
        }

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

        const el = document.getElementById(layer.id);
        if (el) {
            const sz = layer._mascotSize || 100;
            const cx = layer._cx || 0;
            const cy = layer._cy || 0;

            // ✅ Mascot ka apna center calculate karo (position + size ka center)
            const mascotCenterX = cx + (layer.x || 0);
            const mascotCenterY = cy + (layer.y || 0);

            el.setAttribute('transform', `rotate(${value} ${mascotCenterX} ${mascotCenterY})`);
        }

        document.getElementById('directMascotRotationValue').textContent = value;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateDirectMascotPosition = function (axis, value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || layer.type !== 'direct-mascot') return;

        if (axis === 'x') {
            layer.x = parseInt(value);
            document.getElementById('mascotDirectPosXValue').textContent = value;
        } else {
            layer.y = parseInt(value);
            document.getElementById('mascotDirectPosYValue').textContent = value;
        }

        const el = document.getElementById(layer.id);
        if (el) {
            const cx = layer._cx || 0;
            const cy = layer._cy || 0;
            const sz = layer._mascotSize || 100;
            el.setAttribute('x', cx - sz / 2 + layer.x);
            el.setAttribute('y', cy - sz / 2 + layer.y);
        }

        if (window.saveCustomizations) window.saveCustomizations();
    };

    // Change mascot (re-open mascot select modal)
    window.changeDirectMascot = function () {
        if (!window.currentApplicationLayer) return;
        window.openMascotSelectModal(window.currentApplicationLayer);
    };


    // =================== UPDATE CONTROLS (TEXT) ===================

    window.updateApplicationControls = function (layer) {
        if (layer.type === 'direct-mascot') return; // handled separately

        document.getElementById('applicationText').value = layer.text || '';
        document.getElementById('fontSize').value = layer.fontSize || 2000;
        document.getElementById('fontSizeValue').textContent = layer.fontSize || 2000;

        const outlineSlider = document.getElementById('outlineWidthSlider');
        if (outlineSlider) {
            outlineSlider.value = layer.strokeWidth || 5;
            document.getElementById('outlineWidthValue').textContent = layer.strokeWidth || 5;
        }

        document.getElementById('posX').value = layer.x || 0;
        document.getElementById('posXValue').textContent = layer.x || 0;
        document.getElementById('posY').value = layer.y || 0;
        document.getElementById('posYValue').textContent = layer.y || 0;
        document.getElementById('rotation').value = layer.rotation || 0;
        document.getElementById('rotationValue').textContent = layer.rotation || 0;
    };

    // =================== REAL-TIME UPDATES (TEXT) ===================

    window.updateApplicationText = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        layer.text = value;

        const textEl = document.getElementById(layer.id);
        if (textEl) textEl.textContent = value;

        document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => o.textContent = value);

        if (window.saveCustomizations) window.saveCustomizations();
        updateApplicationLayersList();
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
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateFontFamily = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        layer.fontFamily = value;
        const textEl = document.getElementById(layer.id);
        if (textEl) textEl.style.fontFamily = value;

        document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => o.style.fontFamily = value);

        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updatePosition = function (x, y) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        const partElement = document.querySelector(`#${layer.partId}`);
        if (!partElement) return;

        const bbox = partElement.getBBox();
        const centerX = bbox.x + (bbox.width / 2);
        const centerY = bbox.y + (bbox.height / 2);

        if (x !== null) { layer.x = parseInt(x); document.getElementById('posXValue').textContent = x; }
        if (y !== null) { layer.y = parseInt(y); document.getElementById('posYValue').textContent = y; }

        const textEl = document.getElementById(layer.id);
        if (textEl) {
            const newX = centerX + layer.x;
            const newY = centerY + layer.y;

            textEl.setAttribute('x', newX);
            textEl.setAttribute('y', newY);

            document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => {
                o.setAttribute('x', newX);
                o.setAttribute('y', newY);
            });

            if (layer.rotation) {
                textEl.setAttribute('transform', `rotate(${layer.rotation} ${newX} ${newY})`);
                document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => {
                    o.setAttribute('transform', `rotate(${layer.rotation} ${newX} ${newY})`);
                });
            }
        }

        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateRotation = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        layer.rotation = parseInt(value);

        const textEl = document.getElementById(layer.id);
        if (textEl) {
            const x = textEl.getAttribute('x');
            const y = textEl.getAttribute('y');
            textEl.setAttribute('transform', `rotate(${value} ${x} ${y})`);

            document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => {
                o.setAttribute('transform', `rotate(${value} ${x} ${y})`);
            });
        }

        document.getElementById('rotationValue').textContent = value;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateOutlineStroke = function (type, value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        const textEl = document.getElementById(layer.id);
        if (!textEl) return;

        const outlines = document.querySelectorAll(`[data-outline-for="${layer.id}"]`);

        if (type === 'outline1') { textEl.setAttribute('stroke-width', value); layer.strokeWidth = parseInt(value); }
        if (type === 'outline2' && outlines.length > 0) { outlines[0].setAttribute('stroke-width', value); }

        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateShadowOffset = function (val) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        layer.shadowOffset = parseInt(val);
        applyOutlineStyleToText(layer.id);
        if (window.saveCustomizations) window.saveCustomizations();
    };

    // =================== FONT LOADING ===================

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

    window.openFontModal = function () {
        document.getElementById('fontModal').style.display = 'flex';
        renderFontGrid();
    };

    window.closeFontModal = function () {
        document.getElementById('fontModal').style.display = 'none';
    };

    function renderFontGrid() {
        const grid = document.getElementById('fontGrid');
        grid.innerHTML = '';
        const previewText = document.getElementById('applicationText')?.value || '85';
        const currentLayer = findLayerById(window.currentApplicationLayer);

        window.backendFonts.forEach(f => {
            const div = document.createElement('div');
            div.style.cssText = 'border:2px solid #ddd;padding:20px;text-align:center;border-radius:8px;cursor:pointer;background:#fff';
            div.innerHTML = `<div style="font-size:42px;font-family:font_${f.id}">${previewText}</div><p style="margin-top:10px;font-weight:600">${f.name}</p>`;

            if (currentLayer?.fontFamily === `font_${f.id}`) {
                div.style.border = '2px solid #000';
                div.style.background = '#8d8d8d';
            }

            div.onmouseenter = () => { if (currentLayer?.fontFamily !== `font_${f.id}`) div.style.background = '#f2f2f2'; };
            div.onmouseleave = () => { if (currentLayer?.fontFamily !== `font_${f.id}`) div.style.background = '#fff'; };
            div.onclick = () => { updateFontFamily(`font_${f.id}`); closeFontModal(); };
            grid.appendChild(div);
        });
    }

    // =================== PATTERN INTEGRATION ===================

    window.syncPatternColorsToTab = function () {
        const container = document.getElementById('patternColorPaletteInTab');
        if (!container) return;
        const mainPalette = document.getElementById('patternColorPalette');
        if (mainPalette && mainPalette.innerHTML) {
            container.innerHTML = mainPalette.innerHTML;
            document.getElementById('patternColorControls').style.display = 'block';
            document.getElementById('patternPlaceholder').style.display = 'none';
        }
    };

    window.updateTextPatternColor = function (color) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer?.patternId) return;
        const pattern = window.getMainSvg().querySelector(`#${layer.patternId}`);
        if (!pattern) return;
        pattern.querySelectorAll('*').forEach(el => {
            if (el.hasAttribute('fill') && el.getAttribute('fill') !== 'none') el.setAttribute('fill', color);
        });
    };

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
        if (!patternSvg || patternSvg.nodeName !== 'svg') { alert("Pattern SVG invalid"); return; }
        patternSvg = patternSvg.cloneNode(true);
        if (!patternSvg.getAttribute('viewBox')) patternSvg.setAttribute('viewBox', '0 0 100 100');
        patternSvg.setAttribute('width', '100');
        patternSvg.setAttribute('height', '100');

        const patternId = `text-pattern-${layer.id}`;
        const oldPattern = defs.querySelector(`#${patternId}`);
        if (oldPattern) oldPattern.remove();

        const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
        pattern.setAttribute('id', patternId);
        pattern.setAttribute('patternUnits', 'userSpaceOnUse');
        const bbox = textEl.getBBox();
        pattern.setAttribute('x', bbox.x);
        pattern.setAttribute('y', bbox.y);
        pattern.setAttribute('width', bbox.width);
        pattern.setAttribute('height', bbox.height);
        patternSvg.setAttribute('width', bbox.width);
        patternSvg.setAttribute('height', bbox.height);
        patternSvg.setAttribute('preserveAspectRatio', 'xMidYMid slice');
        pattern.appendChild(patternSvg);
        defs.appendChild(pattern);

        textEl.setAttribute('fill', `url(#${patternId})`);
        textEl.setAttribute('fill-opacity', '1');
        layer.patternId = patternId;
        layer.patternSvg = svgContent;
        layer.hasPattern = true;
        renderTextPatternPalette(svgContent);

        document.getElementById('textPatternColorControls').style.display = 'block';
        document.getElementById('patternPlaceholder').style.display = 'none';
        switchTextCustomizationTab('pattern');

        const preview = document.getElementById('textPatternPreview');
        if (preview) {
            preview.innerHTML = svgContent;
            const svg = preview.querySelector('svg');
            if (svg) { svg.style.width = '70px'; svg.style.height = '70px'; svg.style.display = 'block'; }
        }
    };

    function extractPatternColors(svg) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(svg, 'image/svg+xml');
        const colors = new Set();
        doc.querySelectorAll('[fill]').forEach(el => {
            const f = el.getAttribute('fill');
            if (f && f !== 'none') colors.add(f.toLowerCase());
        });
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

        const palette = window.backendColors.map(c => c.code);

        colors.forEach(patternColor => {
            const row = document.createElement('div');
            row.style = 'display:flex;align-items:center;gap:12px;margin-bottom:12px';
            row.innerHTML = `<div style="width:30px;height:30px;border-radius:6px;background:${patternColor};border:2px solid #ccc"></div><span style="font-weight:700;">→</span><div class="text-color-row" style="display:flex;gap:6px"></div>`;

            const choices = row.querySelector('.text-color-row');
            palette.forEach(userColor => {
                const box = document.createElement('div');
                box.style = `width:26px;height:26px;border-radius:6px;background:${userColor};cursor:pointer;border:2px solid #ddd;`;
                if (layer.replacements[patternColor?.toLowerCase()] === userColor) box.style.outline = '2px solid #000';
                box.onclick = () => {
                    choices.querySelectorAll('div').forEach(b => b.style.outline = 'none');
                    box.style.outline = '2px solid #000';
                    layer.replacements[patternColor.toLowerCase()] = userColor;
                    rebuildTextPattern(layer);
                };
                choices.appendChild(box);
            });

            row.appendChild(choices);
            container.appendChild(row);
        });
    }

    window.replacePatternColor = function (oldColor, newColor) {
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer?.patternId) return;
        const pattern = getMainSvg().querySelector(`#${layer.patternId}`);
        if (!pattern) return;
        pattern.querySelectorAll('[fill]').forEach(el => {
            if (el.getAttribute('fill')?.toLowerCase() === oldColor) el.setAttribute('fill', newColor);
        });
    };

    function rebuildTextPattern(layer) {
        const mainSvg = getMainSvg();
        const pattern = mainSvg.querySelector(`#${layer.patternId}`);
        if (!pattern) return;

        const textEl = document.getElementById(layer.id);
        if (!textEl) return;

        const oldTransform = pattern.getAttribute('patternTransform');
        const oldX = pattern.getAttribute('x');
        const oldY = pattern.getAttribute('y');
        const oldWidth = pattern.getAttribute('width');
        const oldHeight = pattern.getAttribute('height');

        const parser = new DOMParser();
        const doc = parser.parseFromString(layer.patternSvg, 'image/svg+xml');
        const svg = doc.querySelector('svg');

        Object.entries(layer.replacements || {}).forEach(([oldColor, newColor]) => {
            svg.querySelectorAll('[fill]').forEach(e => {
                if (e.getAttribute('fill')?.toLowerCase() === oldColor) e.setAttribute('fill', newColor);
            });
        });

        pattern.innerHTML = '';
        pattern.appendChild(svg);
        pattern.setAttribute('x', oldX);
        pattern.setAttribute('y', oldY);
        pattern.setAttribute('width', oldWidth);
        pattern.setAttribute('height', oldHeight);
        if (oldTransform) pattern.setAttribute('patternTransform', oldTransform);
        svg.setAttribute('width', oldWidth);
        svg.setAttribute('height', oldHeight);
        svg.setAttribute('preserveAspectRatio', 'xMidYMid slice');
    }

    // =================== MASCOT (TEXT FILL) ===================

    window.applyMascotToText = function (svgContent, forcedLayerId = null) {
        const layerId = forcedLayerId || window.currentApplicationLayer;
        if (!layerId) return;

        const layer = findLayerById(layerId);
        if (!layer) return;

        // If this is a direct-mascot layer, route to applyDirectMascotToLayer
        if (layer.type === 'direct-mascot') {
            applyDirectMascotToLayer(svgContent, layerId, true);
            return;
        }

        const textEl = document.getElementById(layer.id);
        if (!textEl) return;

        const mainSvg = window.getMainSvg();
        let defs = mainSvg.querySelector('defs');
        if (!defs) { defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs'); mainSvg.insertBefore(defs, mainSvg.firstChild); }

        const parser = new DOMParser();
        const doc = parser.parseFromString(svgContent, 'image/svg+xml');
        let mascotSvg = doc.documentElement;
        if (!mascotSvg || mascotSvg.nodeName !== 'svg') { alert("Mascot SVG load failed"); return; }
        mascotSvg = mascotSvg.cloneNode(true);

        // ✅ Background remove
        const vb = (mascotSvg.getAttribute('viewBox') || '0 0 100 100').split(' ').map(Number);
        mascotSvg.querySelectorAll('rect, polygon, circle, ellipse').forEach(el => {
            const x = parseFloat(el.getAttribute('x') || 0);
            const y = parseFloat(el.getAttribute('y') || 0);
            const w = parseFloat(el.getAttribute('width') || 0);
            const h = parseFloat(el.getAttribute('height') || 0);
            const coversAll = x <= 1 && y <= 1 && w >= vb[2] * 0.7 && h >= vb[3] * 0.7;
            if (coversAll) el.remove();
        });

        mascotSvg.querySelectorAll('*').forEach(el => {
            const style = el.getAttribute('style') || '';
            const fill = el.getAttribute('fill') || '';
            const isWhite = fill === '#fff' || fill === '#ffffff' || fill === 'white' ||
                style.includes('fill:#fff') || style.includes('fill: #fff') ||
                style.includes('fill:white') || style.includes('fill:#ffffff');
            const tag = el.tagName.toLowerCase();
            if (isWhite && (tag === 'rect' || tag === 'polygon')) el.remove();
        });

        mascotSvg.style.background = 'transparent';

        // Ensure viewBox
        if (!mascotSvg.getAttribute('viewBox')) {
            mascotSvg.setAttribute('viewBox', '0 0 100 100');
        }
        mascotSvg.querySelectorAll('rect,circle,path').forEach(el => {
            const fill = el.getAttribute('fill');
            if (fill && (fill === '#fff' || fill === '#ffffff' || fill === 'white')) el.remove();
        });

        if (!mascotSvg.hasAttribute('viewBox')) mascotSvg.setAttribute('viewBox', '0 0 100 100');

        const bbox = textEl.getBBox();
        const mascotId = `text-mascot-${layer.id}`;
        const oldMascot = defs.querySelector(`#${mascotId}`);
        if (oldMascot) oldMascot.remove();

        const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
        pattern.setAttribute('id', mascotId);
        pattern.setAttribute('patternUnits', 'userSpaceOnUse');
        pattern.setAttribute('x', bbox.x);
        pattern.setAttribute('y', bbox.y);
        const tileSize = Math.min(bbox.width, bbox.height) / 4;
        pattern.setAttribute('width', tileSize);
        pattern.setAttribute('height', tileSize);
        mascotSvg.setAttribute('width', tileSize);
        mascotSvg.setAttribute('height', tileSize);
        mascotSvg.setAttribute('preserveAspectRatio', 'xMidYMid meet');
        pattern.appendChild(mascotSvg);
        defs.appendChild(pattern);

        textEl.setAttribute('fill', `url(#${mascotId})`);
        textEl.setAttribute('fill-opacity', '1');
        layer.mascotSvg = svgContent;
        layer.hasMascot = true;
        layer.mascotId = mascotId;
        layer.mascotTileSize = tileSize;
        layer.mascotSize = 100;
        layer.mascotOpacity = 100;
        layer.mascotCount = 4;

        document.getElementById('textMascotColorControls').style.display = 'block';
        document.getElementById('mascotPlaceholder').style.display = 'none';

        const preview = document.getElementById('textMascotPreview');
        if (preview) {
            preview.innerHTML = svgContent;
            const svg = preview.querySelector('svg');
            if (svg) { svg.style.maxWidth = '60px'; svg.style.maxHeight = '60px'; }
        }

        const sizeSlider = document.getElementById('mascotSizeTabSlider');
        const opacitySlider = document.getElementById('mascotOpacityTabSlider');
        const countSlider = document.getElementById('mascotCountTabSlider');
        if (sizeSlider) sizeSlider.value = 100;
        if (document.getElementById('mascotSizeValueTab')) document.getElementById('mascotSizeValueTab').textContent = '100';
        if (opacitySlider) opacitySlider.value = 100;
        if (document.getElementById('mascotOpacityValueTab')) document.getElementById('mascotOpacityValueTab').textContent = '100';
        if (countSlider) countSlider.value = 4;
        if (document.getElementById('mascotCountValueTab')) document.getElementById('mascotCountValueTab').textContent = '4';

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
        const x = textEl.getAttribute('x');
        const y = textEl.getAttribute('y');
        textEl.setAttribute('transform', `translate(${x},${y}) scale(${layer.scaleX},${layer.scaleY}) translate(${-x},${-y}) rotate(${layer.rotation || 0} ${x} ${y})`);
        document.querySelectorAll(`[data-outline-for="${layer.id}"]`).forEach(o => {
            o.setAttribute('transform', `translate(${x},${y}) scale(${layer.scaleX},${layer.scaleY}) translate(${-x},${-y}) rotate(${layer.rotation || 0} ${x} ${y})`);
        });
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.deleteCurrentApplicationLayer = function () {
        if (!window.currentApplicationLayer) return;
        if (!confirm('Delete this application?')) return;
        removeApplicationLayer(window.currentApplicationLayer);
    };

    window.openTextPatternLibrary = function () {
        if (!window.currentApplicationLayer) { alert('Please select a text layer first!'); return; }
        window.selectingPatternForText = true;
        if (window.openPatternLibrary) window.openPatternLibrary();
        else {
            const modal = document.getElementById('patternLibraryModal');
            if (modal) { modal.style.display = 'flex'; loadPatternsFromDB(); }
        }
    };

    window.openTextMascotLibrary = function () {
        if (!window.currentApplicationLayer) { alert('Please select a text layer first!'); return; }
        window.selectingMascotForText = true;
        if (window.openMascotTemplateModal) window.openMascotTemplateModal();
        else {
            const modal = document.getElementById('mascotTemplateModal');
            if (modal) { modal.style.display = 'flex'; }
        }
    };

    window.selectMascotForText = function (svg) {
        if (svg) { const decodedSvg = decodeURIComponent(svg); window.applyMascotToText(decodedSvg); }
        const modal = document.getElementById('mascotTemplateModal');
        if (modal) modal.style.display = 'none';
        if (window.closeMascotTemplateModal) window.closeMascotTemplateModal();
        window.selectingMascotForText = false;
    };

    window.applyPatternFromLibrary = function (svgContent) {
        if (window.selectingPatternForText && window.currentApplicationLayer) {
            window.applyPatternToText(svgContent);
            window.selectingPatternForText = false;
        } else {
            if (window.applyUploadedPattern) window.applyUploadedPattern();
        }
    };

    window.applyMascotFromLibrary = function (svgContent) {
        if (window.selectingMascotForText && window.currentApplicationLayer) {
            window.applyMascotToText(svgContent);
            window.selectingMascotForText = false;
        } else {
            if (window.applyUploadedMascot) window.applyUploadedMascot();
        }
    };

    window.openMascotTemplateModal = function () {
        if (window.selectingMascotForText) {
            document.getElementById('mascotTemplateModal').style.display = 'flex';
            fetch('/api/mascot-templates').then(r => r.json()).then(data => {
                let html = `<div style="border:2px dashed #999;padding:10px;border-radius:8px;text-align:center;cursor:pointer;background:#fafafa" onclick="clearTextMascot(); closeMascotTemplateModal();">
                   <div style="height:140px;display:flex;align-items:center;justify-content:center;font-weight:700;">BLANK</div></div>`;
                data.forEach(t => {
                    html += `<div style="border:1px solid #ddd;padding:10px;border-radius:8px;text-align:center;cursor:pointer" onclick="selectMascotForText('${encodeURIComponent(t.svg_data || '')}')">
                        <img src="${t.image_data}" style="width:100%;height:140px;object-fit:contain;background:#f5f5f5">
                        <p style="margin-top:8px;font-weight:600">${t.title}</p></div>`;
                });
                document.getElementById('mascotTemplateGrid').innerHTML = html;
            }).catch(err => {
                document.getElementById('mascotTemplateGrid').innerHTML = '<p style="color:#999;text-align:center;padding:40px;">Error loading mascots</p>';
            });
        } else if (typeof originalOpenMascotTemplateModal === 'function') {
            originalOpenMascotTemplateModal();
        }
    };

    const originalOpenPatternLibrary = window.openPatternLibrary;
    window.openPatternLibrary = function () {
        if (window.selectingPatternForText) {
            document.getElementById('patternLibraryModal').style.display = 'flex';
            if (window.loadPatternsFromDB) { window.loadPatternsFromDB(); return; }
            fetch('/api/patterns').then(res => res.json()).then(patterns => {
                const container = document.getElementById('patternList');
                container.innerHTML = '';
                const blank = document.createElement('div');
                blank.style.cssText = 'border:2px dashed #999;border-radius:8px;padding:10px;cursor:pointer;text-align:center;background:#fafafa';
                blank.innerHTML = '<div style="height:120px;display:flex;align-items:center;justify-content:center;font-weight:700;">BLANK</div>';
                blank.onclick = () => { clearTextPattern(); closePatternLibrary(); };
                container.appendChild(blank);
                patterns.forEach(pattern => {
                    const div = document.createElement('div');
                    div.style.cssText = 'border:2px solid #ddd;border-radius:8px;padding:10px;cursor:pointer;text-align:center;background:#fff;transition:all 0.3s';
                    div.innerHTML = `<div style="height:120px;display:flex;align-items:center;justify-content:center;"><img src="${pattern.svg_url}" style="max-width:100%;max-height:100%;"></div><p style="margin-top:8px;font-weight:600;">${pattern.name}</p>`;
                    div.onmouseover = () => div.style.borderColor = '#007bff';
                    div.onmouseout = () => div.style.borderColor = '#ddd';
                    div.onclick = () => {
                        fetch(pattern.svg_url).then(res => res.text()).then(svgContent => {
                            window.applyPatternToText(svgContent);
                            closePatternLibrary();
                            window.selectingPatternForText = false;
                        });
                    };
                    container.appendChild(div);
                });
            });
        } else if (originalOpenPatternLibrary) {
            originalOpenPatternLibrary();
        }
    };

    window.clearTextPattern = function () {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasPattern) return;
        const textEl = document.getElementById(layer.id);
        if (!textEl) return;
        const mainSvg = window.getMainSvg();
        if (layer.patternId) { const p = mainSvg.querySelector(`#${layer.patternId}`); if (p) p.remove(); }
        const colors = layer.outlineColors || window.outlineColors;
        textEl.setAttribute('fill', colors.baseColor || '#FFFFFF');
        textEl.setAttribute('fill-opacity', '1');
        delete layer.patternSvg; delete layer.hasPattern; delete layer.patternId; delete layer.patternSize; delete layer.patternOpacity;
        document.getElementById('textPatternColorControls').style.display = 'none';
        document.getElementById('patternPlaceholder').style.display = 'block';
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
        const colors = layer.outlineColors || window.outlineColors;
        textEl.setAttribute('fill', colors.baseColor || '#FFFFFF');
        textEl.setAttribute('fill-opacity', '1');
        delete layer.mascotSvg; delete layer.hasMascot; delete layer.mascotId; delete layer.mascotSize; delete layer.mascotOpacity; delete layer.mascotCount; delete layer.mascotTileSize;
        document.getElementById('textMascotColorControls').style.display = 'none';
        document.getElementById('mascotPlaceholder').style.display = 'block';
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateTextPatternSize = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasPattern || !layer.patternId) return;
        const mainSvg = window.getMainSvg();
        const pattern = mainSvg.querySelector(`#${layer.patternId}`);
        if (!pattern) return;
        pattern.setAttribute('patternTransform', `scale(${value / 100})`);
        layer.patternSize = value;
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
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateTextMascotSize = function (value) {
        if (!window.currentApplicationLayer) return;
        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasMascot || !layer.mascotId) return;
        const mainSvg = window.getMainSvg();
        const pattern = mainSvg.querySelector(`#${layer.mascotId}`);
        if (!pattern) return;
        const svg = pattern.querySelector('svg');
        if (!svg) return;
        const scale = value / 50;
        const newSize = layer.mascotTileSize * scale;
        svg.setAttribute('width', newSize);
        svg.setAttribute('height', newSize);
        svg.setAttribute('x', (layer.mascotTileSize - newSize) / 2);
        svg.setAttribute('y', (layer.mascotTileSize - newSize) / 2);
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
        const textEl = document.getElementById(layer.id);
        const bbox = textEl.getBBox();
        const mainSvg = window.getMainSvg();
        const pattern = mainSvg.querySelector(`#${layer.mascotId}`);
        if (!pattern) return;
        const newTileSize = Math.min(bbox.width, bbox.height) / value;
        pattern.setAttribute('width', newTileSize);
        pattern.setAttribute('height', newTileSize);
        const svg = pattern.querySelector('svg');
        if (svg) { svg.setAttribute('width', newTileSize); svg.setAttribute('height', newTileSize); }
        layer.mascotTileSize = newTileSize;
        layer.mascotCount = value;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.removeApplicationLayer = function (layerId, event) {
        if (event) event.stopPropagation();

        Object.keys(window.applicationsApplied).forEach(view => {
            Object.keys(window.applicationsApplied[view]).forEach(partId => {
                window.applicationsApplied[view][partId] = window.applicationsApplied[view][partId].filter(l => l.id !== layerId);
                if (window.applicationsApplied[view][partId].length === 0) delete window.applicationsApplied[view][partId];
            });
        });

        const layerGroup = document.getElementById(`app-group-${layerId}`);
        if (layerGroup) layerGroup.remove();

        const mainSvg = window.getMainSvg();
        if (mainSvg) {
            const clip = mainSvg.querySelector(`#clip-${layerId}`);
            if (clip) clip.remove();
        }

        document.querySelectorAll(`[data-outline-for="${layerId}"]`).forEach(o => o.remove());

        if (window.currentApplicationLayer === layerId) {
            window.currentApplicationLayer = null;
            document.getElementById('applicationLayerControls').style.display = 'none';
        }

        updateApplicationLayersList();
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.findLayerById = function (layerId) {
        let found = null;
        Object.values(window.applicationsApplied).forEach(viewData => {
            Object.values(viewData).forEach(layers => {
                layers.forEach(layer => { if (layer.id === layerId) found = layer; });
            });
        });
        return found;
    };

    window.applyApplicationsToSvg = function (svg, view) {
        if (!window.applicationsApplied[view]) return;

        let appGroup = svg.querySelector('#application-group');
        if (!appGroup) {
            appGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            appGroup.setAttribute('id', 'application-group');
            svg.appendChild(appGroup);
        }

        Object.entries(window.applicationsApplied[view]).forEach(([partId, layers]) => {
            const partElement = svg.querySelector(`#${partId}`);
            if (!partElement) return;
            const bbox = partElement.getBBox();
            const centerX = bbox.x + (bbox.width / 2);
            const centerY = bbox.y + (bbox.height / 2);

            layers.forEach(layer => {
                if (layer.type === 'direct-mascot') {
                    // Direct mascot restore
                    if (layer.mascotSvg) applyDirectMascotToLayer(layer.mascotSvg, layer.id, false);
                    return;
                }

                const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                text.setAttribute('id', layer.id);
                text.setAttribute('x', centerX + layer.x);
                text.setAttribute('y', centerY + layer.y);
                text.setAttribute('font-size', layer.fontSize);
                text.style.fontFamily = layer.fontFamily;
                text.setAttribute('fill', layer.fill);
                text.setAttribute('stroke', layer.stroke);
                text.setAttribute('stroke-width', layer.strokeWidth);
                text.setAttribute('text-anchor', 'middle');
                text.setAttribute('dominant-baseline', 'middle');
                text.setAttribute('paint-order', 'stroke fill');
                text.setAttribute('stroke-linejoin', 'round');
                text.textContent = layer.text;
                if (layer.rotation) text.setAttribute('transform', `rotate(${layer.rotation} ${centerX + layer.x} ${centerY + layer.y})`);
                appGroup.appendChild(text);

                if (layer.outlineStyle) { window.currentOutlineStyle = layer.outlineStyle; window.outlineColors = { ...layer.outlineColors }; applyOutlineStyleToText(layer.id); }
                if (layer.hasPattern && layer.patternSvg) { applyPatternToText(layer.patternSvg); if (layer.patternSize) updateTextPatternSize(layer.patternSize); if (layer.patternOpacity) updateTextPatternOpacity(layer.patternOpacity); }
                if (layer.hasMascot && layer.mascotSvg) { applyMascotToText(layer.mascotSvg); if (layer.mascotSize) updateTextMascotSize(layer.mascotSize); if (layer.mascotOpacity) updateTextMascotOpacity(layer.mascotOpacity); if (layer.mascotCount) updateTextMascotCount(layer.mascotCount); }
            });
        });
    };

    window.initializeApplicationsOnLoad = function () {
        if (!window.applicationsApplied) return;
        const view = window.currentView;
        if (!window.applicationsApplied[view]) return;
        if (Object.keys(window.applicationsApplied[view]).length === 0) return;

        const svg = window.getMainSvg();
        if (!svg) { setTimeout(window.initializeApplicationsOnLoad, 300); return; }

        svg.querySelectorAll('[id^="app-group-"]').forEach(g => g.remove());
        svg.querySelectorAll('#application-group').forEach(g => g.remove());
        svg.querySelectorAll('[data-outline-for]').forEach(e => e.remove());

        Object.entries(window.applicationsApplied[view]).forEach(([partId, layers]) => {
            layers.forEach(layer => {
                addApplicationToSvg(layer);
                // ✅ Direct mascot ka colorMap restore karo
                if (layer.type === 'direct-mascot' && layer.mascotSvg &&
                    layer._colorMap && Object.keys(layer._colorMap).length > 0) {
                    (function (l) {
                        setTimeout(function () { _applyDirectMascotColorMap(l); }, 400);
                    })(layer);
                }
                if (layer.hasPattern && layer.patternSvg) {
                    const prevLayer = window.currentApplicationLayer;
                    window.currentApplicationLayer = layer.id;
                    applyPatternToText(layer.patternSvg, layer.id);
                    if (layer.patternSize) updateTextPatternSize(layer.patternSize);
                    if (layer.patternOpacity) updateTextPatternOpacity(layer.patternOpacity);
                    window.currentApplicationLayer = prevLayer;
                }

                if (layer.hasMascot && layer.mascotSvg) {
                    const prevLayer = window.currentApplicationLayer;
                    window.currentApplicationLayer = layer.id;
                    applyMascotToText(layer.mascotSvg, layer.id);
                    if (layer.mascotSize) updateTextMascotSize(layer.mascotSize);
                    if (layer.mascotOpacity) updateTextMascotOpacity(layer.mascotOpacity);
                    if (layer.mascotCount) updateTextMascotCount(layer.mascotCount);
                    window.currentApplicationLayer = prevLayer;
                }
            });
        });

        updateApplicationLayersList();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { setTimeout(window.initializeApplicationsOnLoad, 600); });
    } else {
        setTimeout(window.initializeApplicationsOnLoad, 600);
    }
    document.addEventListener('DOMContentLoaded', () => { loadBackendFonts(); });





















    // ============================================================
    // SELECTION BOX — Text + Direct Mascot dono ke liye
    // applications_oob_addon.js
    // ============================================================

    // =================== REORDER SVG LAYERS ===================
    window.reorderSvgLayers = function (view, partId, orderedLayers) {
        var mainSvg = window.getMainSvg();
        if (!mainSvg) return;
        orderedLayers.forEach(function (layer) {
            var group = mainSvg.querySelector('#app-group-' + layer.id);
            if (group) mainSvg.appendChild(group);
        });
    };

    // =================== STATE ===================
    window._oobState = {
        selectedLayerId: null,
        isDragging: false,
        dragStartX: 0,
        dragStartY: 0,
        dragStartLayerX: 0,
        dragStartLayerY: 0,
        _layer: null
    };

    // =================== BBOX HELPER ===================
    // SVG <text> aur <svg> (mascot) dono ke liye sahi bbox nikalta hai
    function _getElementBbox(svgEl, mainSvg) {
        var tagName = svgEl.tagName ? svgEl.tagName.toLowerCase() : '';

        // Nested <svg> element (direct-mascot)
        // getBoundingClientRect use karo — actual rendered size milti hai
        // phir SVG coords mein convert karo
        if (tagName === 'svg') {
            try {
                var cr = svgEl.getBoundingClientRect();
                var svgR = mainSvg.getBoundingClientRect();
                var vb2 = mainSvg.viewBox ? mainSvg.viewBox.baseVal : null;
                var vbW2 = (vb2 && vb2.width) ? vb2.width : svgR.width;
                var vbH2 = (vb2 && vb2.height) ? vb2.height : svgR.height;
                var sx2 = vbW2 / svgR.width;
                var sy2 = vbH2 / svgR.height;
                var bx = (cr.left - svgR.left) * sx2;
                var by = (cr.top - svgR.top) * sy2;
                var bw = cr.width * sx2;
                var bh = cr.height * sy2;
                if (bw > 0 && bh > 0) return { x: bx, y: by, width: bw, height: bh };
            } catch (e2) { }
            // Fallback: x/y/width/height attributes
            var x = parseFloat(svgEl.getAttribute('x') || 0);
            var y = parseFloat(svgEl.getAttribute('y') || 0);
            var w = parseFloat(svgEl.getAttribute('width') || 100);
            var h = parseFloat(svgEl.getAttribute('height') || 100);
            if (w > 0 && h > 0) return { x: x, y: y, width: w, height: h };
        }

        // SVG <text> — getBoundingClientRect use karo (font loaded ho ya na ho, actual size milti hai)
        try {
            var cr = svgEl.getBoundingClientRect();
            var svgR = mainSvg.getBoundingClientRect();
            var vb = mainSvg.viewBox ? mainSvg.viewBox.baseVal : null;
            var vbW = (vb && vb.width) ? vb.width : svgR.width;
            var vbH = (vb && vb.height) ? vb.height : svgR.height;
            var sx = vbW / svgR.width;
            var sy = vbH / svgR.height;
            var bw = cr.width * sx;
            var bh = cr.height * sy;
            if (bw > 0 && bh > 0) {
                return {
                    x: (cr.left - svgR.left) * sx,
                    y: (cr.top - svgR.top) * sy,
                    width: bw,
                    height: bh
                };
            }
        } catch (e) { }

        // Last fallback — getBBox()
        try {
            var bbox = svgEl.getBBox();
            if (bbox && bbox.width > 0) return bbox;
        } catch (e2) { }

        return null;
    }

    // =================== MAIN: SHOW SELECTION BOX ===================
    window.showSelectionBox = function (layerId) {
        if (!layerId) { _hideSelectionBox(); return; }

        var layer = window.findLayerById ? window.findLayerById(layerId) : null;
        if (!layer) { _hideSelectionBox(); return; }


        if (layer.view && layer.view !== window.currentView) {
            _hideSelectionBox();
            return;
        }

        var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return;

        var svgEl = mainSvg.querySelector('#' + layerId);
        if (!svgEl) { _hideSelectionBox(); return; }

        var partEl = mainSvg.querySelector('#' + layer.partId);

        // ---- Get bounding box ----
        var elemBbox = _getElementBbox(svgEl, mainSvg);
        if (!elemBbox || elemBbox.width === 0) return;

        // ---- Check inside/outside part ----
        var partBbox = null;
        var isOutside = false;
        if (partEl) {
            try { partBbox = partEl.getBBox(); } catch (e) { }
            if (partBbox) {
                isOutside = !(
                    elemBbox.x + elemBbox.width > partBbox.x &&
                    elemBbox.x < partBbox.x + partBbox.width &&
                    elemBbox.y + elemBbox.height > partBbox.y &&
                    elemBbox.y < partBbox.y + partBbox.height
                );
            }
        }

        // ---- SVG coords → screen coords ----
        var svgRect = mainSvg.getBoundingClientRect();
        var vb = mainSvg.viewBox ? mainSvg.viewBox.baseVal : null;
        var vbW = (vb && vb.width) ? vb.width : svgRect.width;
        var vbH = (vb && vb.height) ? vb.height : svgRect.height;
        var scaleX = svgRect.width / vbW;
        var scaleY = svgRect.height / vbH;

        var pad = 6;
        var screenX = svgRect.left + (elemBbox.x - pad) * scaleX;
        var screenY = svgRect.top + (elemBbox.y - pad) * scaleY;
        var screenW = (elemBbox.width + pad * 2) * scaleX;
        var screenH = (elemBbox.height + pad * 2) * scaleY;

        // ---- Selection box div ----
        var box = document.getElementById('appSelectionBox');
        if (!box) {
            box = document.createElement('div');
            box.id = 'appSelectionBox';
            box.style.cssText = [
                'position:fixed',
                'border-radius:4px',
                'pointer-events:all',
                'cursor:move',
                'z-index:9998',
                'box-sizing:border-box'
            ].join(';');
            document.body.appendChild(box);
            box.addEventListener('mousedown', _selBoxDragStart);
        }

        if (isOutside) {
            box.style.border = '2px dashed #FF4444';
            box.style.background = 'rgba(255,68,68,0.04)';
            box.style.boxShadow = '0 0 0 1px rgba(255,68,68,0.15)';
        } else {
            box.style.border = '2px solid #007bff';
            box.style.background = 'rgba(0,123,255,0.03)';
            box.style.boxShadow = '0 0 8px rgba(0,123,255,0.18)';
        }

        box.style.left = screenX + 'px';
        box.style.top = screenY + 'px';
        box.style.width = screenW + 'px';
        box.style.height = screenH + 'px';
        box.style.display = 'block';

        _updateCornerHandles(screenX, screenY, screenW, screenH, isOutside);

        // ---- Bring Inside button ----
        var bringBtn = document.getElementById('appBringInsideBtn');
        if (isOutside) {
            if (!bringBtn) {
                bringBtn = document.createElement('button');
                bringBtn.id = 'appBringInsideBtn';
                bringBtn.style.cssText = [
                    'position:fixed',
                    'background:#FF4444',
                    'color:#fff',
                    'border:none',
                    'border-radius:4px',
                    'padding:5px 12px',
                    'font-size:11px',
                    'font-weight:700',
                    'cursor:pointer',
                    'z-index:9999',
                    'white-space:nowrap',
                    'pointer-events:all',
                    'user-select:none',
                    'letter-spacing:.5px'
                ].join(';');
                document.body.appendChild(bringBtn);
            }
            bringBtn.textContent = '⬅ Bring Inside';
            bringBtn.style.left = (screenX + screenW / 2 - 54) + 'px';
            bringBtn.style.top = Math.max(4, screenY - 30) + 'px';
            bringBtn.style.display = 'block';
            bringBtn.onclick = function (e) {
                e.stopPropagation();
                _bringElementInside(layerId, layer);
            };
        } else {
            if (bringBtn) bringBtn.style.display = 'none';
        }

        window._oobState.selectedLayerId = layerId;
        window._oobState._layer = layer;
    };

    // ---- Corner handles ----
    function _updateCornerHandles(sx, sy, sw, sh, isOutside) {
        var color = isOutside ? '#FF4444' : '#007bff';
        var corners = [
            { id: 'sh-tl', l: sx - 5, t: sy - 5 },
            { id: 'sh-tr', l: sx + sw - 5, t: sy - 5 },
            { id: 'sh-bl', l: sx - 5, t: sy + sh - 5 },
            { id: 'sh-br', l: sx + sw - 5, t: sy + sh - 5 }
        ];
        corners.forEach(function (c) {
            var h = document.getElementById(c.id);
            if (!h) {
                h = document.createElement('div');
                h.id = c.id;
                h.style.cssText = 'position:fixed;width:10px;height:10px;border-radius:2px;z-index:9999;pointer-events:none;';
                document.body.appendChild(h);
            }
            h.style.background = color;
            h.style.left = c.l + 'px';
            h.style.top = c.t + 'px';
            h.style.display = 'block';
        });
    }

    function _hideSelectionBox() {
        ['appSelectionBox', 'appBringInsideBtn', 'sh-tl', 'sh-tr', 'sh-bl', 'sh-br'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
        window._oobState.selectedLayerId = null;
        window._oobState._layer = null;
    }

    // =================== DRAG BOX = DRAG LAYER ===================

    function _selBoxDragStart(e) {
        if (e.button !== 0) return;
        e.preventDefault();
        e.stopPropagation();

        var state = window._oobState;
        var layer = state._layer;
        state.isDragging = true;
        state.dragStartX = e.clientX;
        state.dragStartY = e.clientY;
        state.dragStartLayerX = layer ? (layer.x || 0) : 0;
        state.dragStartLayerY = layer ? (layer.y || 0) : 0;

        document.addEventListener('mousemove', _selBoxDragMove);
        document.addEventListener('mouseup', _selBoxDragEnd);
    }

    function _selBoxDragMove(e) {
        var state = window._oobState;
        if (!state.isDragging) return;

        var layer = state._layer;
        if (!layer) return;

        var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return;

        var svgRect = mainSvg.getBoundingClientRect();
        var vb = mainSvg.viewBox ? mainSvg.viewBox.baseVal : null;
        var vbW = (vb && vb.width) ? vb.width : svgRect.width;
        var vbH = (vb && vb.height) ? vb.height : svgRect.height;
        var scaleX = vbW / svgRect.width;
        var scaleY = vbH / svgRect.height;

        var dx = (e.clientX - state.dragStartX) * scaleX;
        var dy = (e.clientY - state.dragStartY) * scaleY;
        var newX = Math.round(state.dragStartLayerX + dx);
        var newY = Math.round(state.dragStartLayerY + dy);

        if (layer.type === 'direct-mascot') {
            layer.x = newX;
            layer.y = newY;
            var el = mainSvg.querySelector('#' + layer.id);
            if (el) {
                el.setAttribute('x', (layer._cx || 0) - (layer._mascotSize || 100) / 2 + newX);
                el.setAttribute('y', (layer._cy || 0) - (layer._mascotSize || 100) / 2 + newY);
            }
            // Sync sidebar sliders for mascot
            var mxEl = document.getElementById('mascotDirectPosX');
            var mxVal = document.getElementById('mascotDirectPosXValue');
            var myEl = document.getElementById('mascotDirectPosY');
            var myVal = document.getElementById('mascotDirectPosYValue');
            if (mxEl) mxEl.value = newX;
            if (mxVal) mxVal.textContent = newX;
            if (myEl) myEl.value = newY;
            if (myVal) myVal.textContent = newY;
        } else {
            layer.x = newX;
            layer.y = newY;
            var partEl = mainSvg.querySelector('#' + layer.partId);
            if (partEl) {
                var bbox = partEl.getBBox();
                var cx = bbox.x + bbox.width / 2;
                var cy = bbox.y + bbox.height / 2;
                var textEl = mainSvg.querySelector('#' + layer.id);
                if (textEl) {
                    textEl.setAttribute('x', cx + newX);
                    textEl.setAttribute('y', cy + newY);
                    mainSvg.querySelectorAll('[data-outline-for="' + layer.id + '"]').forEach(function (o) {
                        o.setAttribute('x', cx + newX);
                        o.setAttribute('y', cy + newY);
                    });
                }
            }
            // Sync sidebar sliders for text
            var pxEl = document.getElementById('posX');
            var pxVal = document.getElementById('posXValue');
            var pyEl = document.getElementById('posY');
            var pyVal = document.getElementById('posYValue');
            if (pxEl) pxEl.value = newX;
            if (pxVal) pxVal.textContent = newX;
            if (pyEl) pyEl.value = newY;
            if (pyVal) pyVal.textContent = newY;
        }

        window.showSelectionBox(layer.id);
    }

    function _selBoxDragEnd() {
        var state = window._oobState;
        state.isDragging = false;
        document.removeEventListener('mousemove', _selBoxDragMove);
        document.removeEventListener('mouseup', _selBoxDragEnd);
        if (window.saveCustomizations) window.saveCustomizations();
        if (state.selectedLayerId) window.showSelectionBox(state.selectedLayerId);
    }

    // =================== BRING INSIDE ===================

    function _bringElementInside(layerId, layer) {
        layer.x = 0;
        layer.y = 0;

        var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return;

        if (layer.type === 'direct-mascot') {
            if (window.applyDirectMascotToLayer && layer.mascotSvg) {
                window.applyDirectMascotToLayer(layer.mascotSvg, layerId, false);
            }
            // Sync sliders
            var mxEl = document.getElementById('mascotDirectPosX');
            var mxVal = document.getElementById('mascotDirectPosXValue');
            var myEl = document.getElementById('mascotDirectPosY');
            var myVal = document.getElementById('mascotDirectPosYValue');
            if (mxEl) mxEl.value = 0;
            if (mxVal) mxVal.textContent = 0;
            if (myEl) myEl.value = 0;
            if (myVal) myVal.textContent = 0;
        } else {
            var partEl = mainSvg.querySelector('#' + layer.partId);
            if (partEl) {
                var bbox = partEl.getBBox();
                var cx = bbox.x + bbox.width / 2;
                var cy = bbox.y + bbox.height / 2;
                var textEl = mainSvg.querySelector('#' + layerId);
                if (textEl) {
                    textEl.setAttribute('x', cx);
                    textEl.setAttribute('y', cy);
                    mainSvg.querySelectorAll('[data-outline-for="' + layerId + '"]').forEach(function (o) {
                        o.setAttribute('x', cx);
                        o.setAttribute('y', cy);
                    });
                }
            }
            var pxEl = document.getElementById('posX');
            var pxVal = document.getElementById('posXValue');
            var pyEl = document.getElementById('posY');
            var pyVal = document.getElementById('posYValue');
            if (pxEl) pxEl.value = 0;
            if (pxVal) pxVal.textContent = 0;
            if (pyEl) pyEl.value = 0;
            if (pyVal) pyVal.textContent = 0;
        }

        if (window.saveCustomizations) window.saveCustomizations();
        setTimeout(function () { window.showSelectionBox(layerId); }, 150);
    }

    // =================== HOOKS ===================

    // Patch selectApplicationLayer
    (function () {
        var orig = window.selectApplicationLayer;
        window.selectApplicationLayer = function (layerId) {
            if (orig) orig.call(this, layerId);
            setTimeout(function () { window.showSelectionBox(layerId); }, 150);
        };
    })();

    // SVG background click = deselect
    document.addEventListener('click', function (e) {
        var box = document.getElementById('appSelectionBox');
        var bBtn = document.getElementById('appBringInsideBtn');
        if (box && box.contains(e.target)) return;
        if (bBtn && bBtn.contains(e.target)) return;
        var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (mainSvg && e.target === mainSvg) {
            _hideSelectionBox();
            window.currentApplicationLayer = null;
        }
    });

    // Resize / scroll update
    window.addEventListener('resize', function () {
        if (window._oobState.selectedLayerId) window.showSelectionBox(window._oobState.selectedLayerId);
    });
    window.addEventListener('scroll', function () {
        if (window._oobState.selectedLayerId) window.showSelectionBox(window._oobState.selectedLayerId);
    }, true);

    // =================== MODAL OPEN/CLOSE HIDE BOX ===================
    // Jab bhi koi modal open ho — selection box hide karo
    // Jab modal close ho — wapas show karo

    (function () {
        // MutationObserver se modal display changes observe karo
        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type !== 'attributes' || mutation.attributeName !== 'style') return;
                var el = mutation.target;
                // Sirf modal divs check karo
                if (!el.id || !el.id.toLowerCase().includes('modal')) return;
                var isVisible = el.style.display !== 'none' && el.style.display !== '';
                if (isVisible) {
                    // Koi modal khula — box hide karo
                    _hideSelectionBox();
                } else {
                    // Modal band hua — agar layer selected hai to box wapas show karo
                    if (window.currentApplicationLayer) {
                        setTimeout(function () {
                            window.showSelectionBox(window.currentApplicationLayer);
                        }, 200);
                    }
                }
            });
        });

        // Observe all modals
        function _observeModals() {
            var modals = document.querySelectorAll('[id*="modal"],[id*="Modal"]');
            modals.forEach(function (m) {
                observer.observe(m, { attributes: true, attributeFilter: ['style'] });
            });
        }

        // DOM ready pe observe karo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', _observeModals);
        } else {
            setTimeout(_observeModals, 500);
        }
    })();

    console.log('✅ Selection box (text + mascot) loaded');








    // ============================================================
    // FLIP PATCH — Rotation + Flip ke baad drag direction fix
    // ============================================================

    window._applyFlipTransform = function (layer, mainSvg) {
        var el = mainSvg.querySelector('#' + layer.id);
        if (!el) return;

        var flipX = layer.flipX || 1;
        var rot = layer.rotation || 0;
        var transform;

        if (layer.type === 'direct-mascot') {
            var cx = (layer._cx || 0) + (layer.x || 0);
            var cy = (layer._cy || 0) + (layer.y || 0);
            if (flipX === -1) {
                transform = 'translate(' + cx + ',' + cy + ') rotate(' + rot + ') scale(-1,1) translate(' + (-cx) + ',' + (-cy) + ')';
            } else {
                transform = rot !== 0 ? 'rotate(' + rot + ' ' + cx + ' ' + cy + ')' : null;
            }
        } else {
            var x = parseFloat(el.getAttribute('x') || 0);
            var y = parseFloat(el.getAttribute('y') || 0);
            if (flipX === -1) {
                transform = 'translate(' + x + ',' + y + ') rotate(' + rot + ') scale(-1,1) translate(' + (-x) + ',' + (-y) + ')';
            } else {
                transform = rot !== 0 ? 'rotate(' + rot + ' ' + x + ' ' + y + ')' : null;
            }
            mainSvg.querySelectorAll('[data-outline-for="' + layer.id + '"]').forEach(function (o) {
                if (transform) o.setAttribute('transform', transform);
                else o.removeAttribute('transform');
            });
        }

        if (transform) el.setAttribute('transform', transform);
        else el.removeAttribute('transform');
    };

    // =================== DRAG DELTA — rotation + flip aware ===================
    // Mouse movement (screen coords) ko SVG movement mein convert karo
    // Rotation + flip dono consider karo
    function _rotateDelta(dx, dy, rotDeg, flipX) {
        var rad = rotDeg * Math.PI / 180;
        var cos = Math.cos(rad);
        var sin = Math.sin(rad);

        // Screen delta → rotated SVG delta
        // Agar flip ON hai to X axis mirror hai — toh dx negate karo pehle
        if (flipX === -1) dx = -dx;

        // Rotate the delta vector by -angle (inverse of element rotation)
        var svgDx = dx * cos + dy * sin;
        var svgDy = -dx * sin + dy * cos;

        return { dx: svgDx, dy: svgDy };
    }

    // =================== FLIP TOGGLE ===================
    window.flipApplicationLayer = function (layerId, event) {
        if (event) event.stopPropagation();
        var layer = window.findLayerById ? window.findLayerById(layerId) : null;
        if (!layer) return;
        var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
        if (!mainSvg) return;

        layer.flipX = (layer.flipX || 1) * -1;
        window._applyFlipTransform(layer, mainSvg);
        _updateFlipUI(layerId, layer);

        if (window.saveCustomizations) window.saveCustomizations();
        if (window.showSelectionBox) setTimeout(function () { window.showSelectionBox(layerId); }, 100);
    };

    function _updateFlipUI(layerId, layer) {
        if (layer.type === 'direct-mascot') {
            var preview = document.getElementById('directMascotPreview');
            if (preview && layer.mascotSvg) {
                preview.innerHTML = layer.mascotSvg;
                var s = preview.querySelector('svg');
                if (s) {
                    s.style.maxWidth = '80px';
                    s.style.maxHeight = '80px';
                    s.style.transform = layer.flipX === -1 ? 'scaleX(-1)' : '';
                }
            }
        }
        document.querySelectorAll('.application-layer-item').forEach(function (item) {
            if (item.dataset.layerId !== layerId) return;
            var btn = item.querySelector('.flip-btn-h');
            if (!btn) return;
            btn.style.background = layer.flipX === -1 ? '#1a1a1a' : '#eee';
            btn.style.color = layer.flipX === -1 ? '#fff' : '#000';
        });
    }

    // =================== makeMascotDraggable — rotation + flip aware ===================
    (function () {
        window.makeMascotDraggable = function (element, layer) {
            var isDragging = false;
            var startX, startY, initOffX, initOffY;

            element.addEventListener('mousedown', function (e) {
                if (e.button !== 0) return;
                isDragging = true;
                element.style.cursor = 'grabbing';
                var svg = element.ownerSVGElement || element.closest('svg');
                var pt = svg.createSVGPoint();
                pt.x = e.clientX; pt.y = e.clientY;
                var svgP = pt.matrixTransform(svg.getScreenCTM().inverse());
                startX = svgP.x;
                startY = svgP.y;
                initOffX = layer.x || 0;
                initOffY = layer.y || 0;
                e.preventDefault();
            });

            document.addEventListener('mousemove', function (e) {
                if (!isDragging) return;
                var svg = element.ownerSVGElement || element.closest('svg');
                var pt = svg.createSVGPoint();
                pt.x = e.clientX; pt.y = e.clientY;
                var svgP = pt.matrixTransform(svg.getScreenCTM().inverse());

                var rawDx = svgP.x - startX;
                var rawDy = svgP.y - startY;

                // Rotation + flip aware delta
                var d = _rotateDelta(rawDx, rawDy, layer.rotation || 0, layer.flipX || 1);

                layer.x = Math.round(initOffX + d.dx);
                layer.y = Math.round(initOffY + d.dy);

                var cx = layer._cx || 0;
                var cy = layer._cy || 0;
                var sz = layer._mascotSize || 100;

                element.setAttribute('x', cx - sz / 2 + layer.x);
                element.setAttribute('y', cy - sz / 2 + layer.y);

                var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
                if (mainSvg) window._applyFlipTransform(layer, mainSvg);
            });

            document.addEventListener('mouseup', function () {
                if (!isDragging) return;
                isDragging = false;
                element.style.cursor = 'move';
                if (window.currentApplicationLayer === layer.id) {
                    var xEl = document.getElementById('mascotDirectPosX');
                    var yEl = document.getElementById('mascotDirectPosY');
                    if (xEl) { xEl.value = layer.x; document.getElementById('mascotDirectPosXValue').textContent = layer.x; }
                    if (yEl) { yEl.value = layer.y; document.getElementById('mascotDirectPosYValue').textContent = layer.y; }
                }
                if (window.saveCustomizations) window.saveCustomizations();
            });
        };
    })();

    // =================== makeDraggable (text) — rotation + flip aware ===================
    (function () {
        window.makeDraggable = function (element, layer) {
            var isDragging = false;
            var startX, startY, initialX, initialY;

            element.addEventListener('mousedown', function (e) {
                if (e.button !== 0) return;
                isDragging = true;
                element.style.cursor = 'grabbing';
                var svg = element.ownerSVGElement;
                var pt = svg.createSVGPoint();
                pt.x = e.clientX; pt.y = e.clientY;
                var svgP = pt.matrixTransform(svg.getScreenCTM().inverse());
                startX = svgP.x;
                startY = svgP.y;
                initialX = parseFloat(element.getAttribute('x'));
                initialY = parseFloat(element.getAttribute('y'));
                e.preventDefault();
            });

            document.addEventListener('mousemove', function (e) {
                if (!isDragging) return;
                var svg = element.ownerSVGElement;
                var pt = svg.createSVGPoint();
                pt.x = e.clientX; pt.y = e.clientY;
                var svgP = pt.matrixTransform(svg.getScreenCTM().inverse());

                var rawDx = svgP.x - startX;
                var rawDy = svgP.y - startY;

                // Rotation + flip aware delta
                var d = _rotateDelta(rawDx, rawDy, layer.rotation || 0, layer.flipX || 1);

                var newX = Math.round(initialX + d.dx);
                var newY = Math.round(initialY + d.dy);

                element.setAttribute('x', newX);
                element.setAttribute('y', newY);

                var outlines = element.parentElement.querySelectorAll('[data-outline-for="' + layer.id + '"]');
                outlines.forEach(function (o) {
                    o.setAttribute('x', newX);
                    o.setAttribute('y', newY);
                });

                var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
                if (mainSvg) window._applyFlipTransform(layer, mainSvg);
            });

            document.addEventListener('mouseup', function () {
                if (!isDragging) return;
                isDragging = false;
                element.style.cursor = 'move';
                var partElement = document.querySelector('#' + layer.partId);
                if (partElement) {
                    var bbox = partElement.getBBox();
                    var centerX = bbox.x + bbox.width / 2;
                    var centerY = bbox.y + bbox.height / 2;
                    layer.x = Math.round(parseFloat(element.getAttribute('x')) - centerX);
                    layer.y = Math.round(parseFloat(element.getAttribute('y')) - centerY);
                    if (window.currentApplicationLayer === layer.id) {
                        if (window.updateApplicationControls) window.updateApplicationControls(layer);
                    }
                    if (window.saveCustomizations) window.saveCustomizations();
                }
            });
        };
    })();

    // =================== Selection box drag — rotation + flip aware ===================
    (function () {
        document.addEventListener('mousemove', function () {
            if (!window._oobState || !window._oobState.isDragging) return;
            var layer = window._oobState._layer;
            if (!layer) return;
            // Only re-apply flip transform (position already set by oob drag)
            if (layer.flipX !== -1 && !layer.rotation) return;
            var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
            if (mainSvg) window._applyFlipTransform(layer, mainSvg);
        }, true);
    })();

    // =================== applyDirectMascotToLayer — flip preserve ===================
    (function () {
        var _orig = window.applyDirectMascotToLayer;
        window.applyDirectMascotToLayer = function (svgContent, forcedLayerId, fromModal) {
            var layerId = forcedLayerId || window.currentApplicationLayer;
            var layer = layerId && window.findLayerById ? window.findLayerById(layerId) : null;
            var savedFlipX = layer ? (layer.flipX || 1) : 1;

            if (_orig) _orig.call(this, svgContent, forcedLayerId, fromModal);

            if (savedFlipX === -1 && layer) {
                layer.flipX = -1;
                var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
                if (mainSvg) setTimeout(function () {
                    window._applyFlipTransform(layer, mainSvg);
                    _updateFlipUI(layerId, layer);
                }, 50);
            }
        };
    })();

    // =================== Rotation patches ===================
    (function () {
        var _orig = window.updateRotation;
        window.updateRotation = function (value) {
            var layerId = window.currentApplicationLayer;
            var layer = layerId && window.findLayerById ? window.findLayerById(layerId) : null;
            if (layer && layer.flipX === -1) {
                layer.rotation = parseInt(value);
                var el = document.getElementById('rotationValue');
                if (el) el.textContent = value;
                var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
                if (mainSvg) window._applyFlipTransform(layer, mainSvg);
                if (window.saveCustomizations) window.saveCustomizations();
            } else {
                if (_orig) _orig.call(this, value);
            }
        };
    })();

    (function () {
        var _orig = window.updateDirectMascotRotation;
        window.updateDirectMascotRotation = function (value) {
            var layerId = window.currentApplicationLayer;
            var layer = layerId && window.findLayerById ? window.findLayerById(layerId) : null;
            if (layer && layer.flipX === -1) {
                layer.rotation = parseInt(value);
                var el = document.getElementById('directMascotRotationValue');
                if (el) el.textContent = value;
                var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
                if (mainSvg) window._applyFlipTransform(layer, mainSvg);
                if (window.saveCustomizations) window.saveCustomizations();
            } else {
                if (_orig) _orig.call(this, value);
            }
        };
    })();

    // =================== Position slider patches ===================
    (function () {
        var _orig = window.updatePosition;
        window.updatePosition = function (x, y) {
            if (_orig) _orig.call(this, x, y);
            var layerId = window.currentApplicationLayer;
            var layer = layerId && window.findLayerById ? window.findLayerById(layerId) : null;
            if (layer && layer.flipX === -1) {
                var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
                if (mainSvg) setTimeout(function () { window._applyFlipTransform(layer, mainSvg); }, 10);
            }
        };
    })();

    (function () {
        var _orig = window.updateDirectMascotPosition;
        window.updateDirectMascotPosition = function (axis, value) {
            if (_orig) _orig.call(this, axis, value);
            var layerId = window.currentApplicationLayer;
            var layer = layerId && window.findLayerById ? window.findLayerById(layerId) : null;
            if (layer && layer.flipX === -1) {
                var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
                if (mainSvg) setTimeout(function () { window._applyFlipTransform(layer, mainSvg); }, 10);
            }
        };
    })();

    // =================== Layer list button ===================
    (function () {
        var _orig = window.updateApplicationLayersList;
        window.updateApplicationLayersList = function () {
            if (_orig) _orig.call(this);
            var container = document.getElementById('applicationLayersList');
            if (!container) return;
            container.querySelectorAll('.application-layer-item').forEach(function (item) {
                var layerId = item.dataset.layerId;
                if (!layerId || item.querySelector('.flip-btn-h')) return;
                var dupBtn = item.querySelector('[onclick*="duplicateApplicationLayer"]');
                if (!dupBtn) return;
                var layer = window.findLayerById ? window.findLayerById(layerId) : null;
                var on = layer && layer.flipX === -1;
                var btn = document.createElement('div');
                btn.className = 'flip-btn-h';
                btn.innerHTML = '↔';
                btn.style.cssText = 'cursor:pointer;font-size:14px;padding:4px 6px;border-radius:4px;flex-shrink:0;user-select:none;transition:all .15s;background:' + (on ? '#1a1a1a' : '#eee') + ';color:' + (on ? '#fff' : '#000');
                btn.onclick = function (e) { e.stopPropagation(); window.flipApplicationLayer(layerId, e); };
                dupBtn.parentNode.insertBefore(btn, dupBtn);
            });
        };
    })();

    // =================== Restore on reload ===================
    (function () {
        var _orig = window.addApplicationToSvg;
        window.addApplicationToSvg = function (layer) {
            if (_orig) _orig.call(this, layer);
            if (layer.flipX && layer.flipX !== 1) {
                var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
                if (mainSvg) setTimeout(function () { window._applyFlipTransform(layer, mainSvg); }, 150);
            }
        };
    })();

    (function () {
        var _orig = window.reorderSvgLayers;
        window.reorderSvgLayers = function (view, partId, arr) {
            if (_orig) _orig.call(this, view, partId, arr);
            var mainSvg = window.getMainSvg ? window.getMainSvg() : null;
            if (!mainSvg) return;
            arr.forEach(function (layer) {
                if (layer.flipX && layer.flipX !== 1)
                    setTimeout(function () { window._applyFlipTransform(layer, mainSvg); }, 50);
            });
        };
    })();

    (function () {
        var _orig = window.selectApplicationLayer;
        window.selectApplicationLayer = function (layerId) {
            if (_orig) _orig.call(this, layerId);
            setTimeout(function () {
                var layer = window.findLayerById ? window.findLayerById(layerId) : null;
                if (layer) _updateFlipUI(layerId, layer);
            }, 200);
        };
    })();

    console.log('✅ Flip patch — rotation + drag direction fully fixed');

















})();
