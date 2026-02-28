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
                // This needs multiple text elements - simplified for preview
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

    // window.showHideColorPickers = function (style) {

    //     // Show outline colors section
    //     document.getElementById('outlineColorsSection').style.display = 'block';

    //     // Show/hide based on style
    //     const outline1 = document.getElementById('outline1Section');
    //     const outline2 = document.getElementById('outline2Section');
    //     const shadow = document.getElementById('shadowSection');

    //     // Reset all
    //     outline1.style.display = 'none';
    //     outline2.style.display = 'none';
    //     shadow.style.display = 'none';

    //     switch (style) {
    //         case 'single':
    //             // Only base color
    //             break;

    //         case 'two-color':
    //             outline1.style.display = 'block';
    //             break;

    //         case 'two-color-shadow':
    //             outline1.style.display = 'block';
    //             shadow.style.display = 'block';
    //             break;

    //         case 'three-color':
    //             outline1.style.display = 'block';
    //             outline2.style.display = 'block';
    //             break;

    //         case 'single-shadow':
    //             shadow.style.display = 'block';
    //             break;

    //         case 'three-color-shadow':
    //             outline1.style.display = 'block';
    //             outline2.style.display = 'block';
    //             shadow.style.display = 'block';
    //             break;
    //     }
    // };


window.showHideColorPickers = function (style) {

    // Show outline colors section
    document.getElementById('outlineColorsSection').style.display = 'block';

    // Show/hide based on style
    const outline1 = document.getElementById('outline1Section');
    const outline2 = document.getElementById('outline2Section');
    const shadow   = document.getElementById('shadowSection');

    // Reset all
    outline1.style.display = 'none';
    outline2.style.display = 'none';
    shadow.style.display   = 'none';

    switch (style) {
        case 'single':
            // Only base color
            break;

        case 'two-color':
            outline1.style.display = 'flex';   // ✅ flex
            break;

        case 'two-color-shadow':
            outline1.style.display = 'flex';   // ✅ flex
            shadow.style.display   = 'flex';   // ✅ flex
            break;

        case 'three-color':
            outline1.style.display = 'flex';   // ✅ flex
            outline2.style.display = 'flex';   // ✅ flex
            break;

        case 'single-shadow':
            shadow.style.display = 'flex';     // ✅ flex
            break;

        case 'three-color-shadow':
            outline1.style.display = 'flex';   // ✅ flex
            outline2.style.display = 'flex';   // ✅ flex
            shadow.style.display   = 'flex';   // ✅ flex
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


    // =================== THREE COLOR OUTLINE (EQUAL WIDTH) ===================

    window.createThreeColorOutline = function (textEl, layer, withShadow) {

        const colors = layer.outlineColors;
        const shadow = layer.shadowOffset ?? 3;
        const stroke = layer.strokeWidth || 5;

        // ===== OUTERMOST OUTLINE (ONLY THIS GETS SHADOW) =====
        const outer = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        outer.setAttribute('data-outline-for', layer.id);

        ['x', 'y', 'font-size', 'transform'].forEach(a => {
            if (textEl.getAttribute(a)) outer.setAttribute(a, textEl.getAttribute(a));
        });

        outer.style.fontFamily = textEl.style.fontFamily;
        outer.textContent = textEl.textContent;

        outer.setAttribute('fill', colors.outline2); // shadow layer now FILLED
        outer.setAttribute('stroke', colors.outline2);
        outer.setAttribute('stroke-width', stroke * 2);
        outer.setAttribute('text-anchor', 'middle');
        outer.setAttribute('dominant-baseline', 'middle');
        outer.setAttribute('stroke-linejoin', 'round');
        outer.style.pointerEvents = 'none';

        // ✅ SHADOW ONLY HERE
        if (withShadow) {
            outer.style.filter = `drop-shadow(${shadow}px ${shadow}px 0 ${colors.shadow})`;
        } else {
            outer.style.filter = 'none';
        }

        textEl.parentElement.insertBefore(outer, textEl);

        // ===== INNER OUTLINE (NO SHADOW EVER) =====
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
        text.setAttribute('font-size', '2000');  // 🔥 LARGER PREVIEW SIZE
        text.style.fontFamily = layer?.fontFamily || 'Arial Black';
        text.setAttribute('fill', textColor);
        text.setAttribute('stroke', strokeColor);
        text.setAttribute('stroke-width', '6');  // 🔥 THICKER STROKE
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

    window.confirmAddApplication = function () {
        if (!window.selectedApplicationPart) {
            alert('Please select a part!');
            return;
        }

        let defaultText = '00';
        if (window.selectedApplicationType === 'teamname') defaultText = 'TEAM';
        if (window.selectedApplicationType === 'playername') defaultText = 'PLAYER';
        if (window.selectedApplicationType === 'mascot') defaultText = '🦅';

        const layerId = `app-${Date.now()}`;
        const view = window.selectedApplicationView;
        const partId = window.selectedApplicationPart;

        const globalColors = window.selectedColors || ['#FFFFFF', '#000000'];

        const layer = {
            id: layerId,
            type: window.selectedApplicationType,
            view: view,
            partId: partId,
            text: defaultText,
            fontSize: 2000,  // 🔥 MUCH LARGER DEFAULT SIZE
            fontFamily: window.backendFonts?.[0] ? `font_${window.backendFonts[0].id}` : 'Arial Black',
            fill: globalColors[0] || '#FFFFFF',
            stroke: globalColors[1] || '#000000',
            strokeWidth: 5,  // Thicker stroke for larger text
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

        console.log('✅ Application added:', layer);
    };

    // =================== ADD TO SVG ===================

    window.addApplicationToSvg = function (layer) {



        const mainSvg = window.getMainSvg();
        if (!mainSvg) return;

        if (mainSvg.querySelector(`#${layer.id}`)) {
            console.log('⚠ Layer already exists, skipping:', layer.id);
            return;
        }

        // ================= CREATE DEFS =================
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

        // ================= CREATE CLIP PATH =================
        const clipId = `clip-${layer.partId}`;

        if (!defs.querySelector(`#${clipId}`)) {
            const clip = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
            clip.setAttribute('id', clipId);

            const clone = partElement.cloneNode(true);
            clone.removeAttribute('id');

            clip.appendChild(clone);
            defs.appendChild(clip);
        }

        // ================= APPLICATION GROUP =================
        let appGroup = mainSvg.querySelector('#application-group');
        if (!appGroup) {
            appGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            appGroup.setAttribute('id', 'application-group');
            mainSvg.appendChild(appGroup);
        }

        // APPLY CLIP TO GROUP
        appGroup.setAttribute('clip-path', `url(#${clipId})`);

        // ================= CENTER OF PART =================
        const bbox = partElement.getBBox();
        const cx = bbox.x + bbox.width / 2;
        const cy = bbox.y + bbox.height / 2;

        // ================= CREATE TEXT =================
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

        appGroup.appendChild(text);

        // ================= DRAG =================
        makeDraggable(text, layer);

        text.addEventListener('click', e => {
            e.stopPropagation();
            selectApplicationLayer(layer.id);
        });

        // ================= OUTLINE / PATTERN =================
        if (layer.outlineStyle) {
            window.currentOutlineStyle = layer.outlineStyle;
            if (layer.outlineColors) {
                window.outlineColors = { ...layer.outlineColors };
            }
            applyOutlineStyleToText(layer.id);
        }

        console.log('✅ Application added WITH CLIP:', layer.id);
    };


    // =================== MAKE DRAGGABLE ===================

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

            // Update any outline elements
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

                layers.forEach(layer => {

                    const viewShort = view.charAt(0).toUpperCase(); // F B L R

                    const isActive = window.currentApplicationLayer === layer.id;

                    const item = document.createElement('div');
                    item.className = 'application-layer-item';

                    // ✅ ACTIVE = BLACK
                    item.style.background = isActive ? '#000' : '#fff';
                    item.style.color = isActive ? '#fff' : '#000';

                    item.innerHTML = `
<div style="display:flex;align-items:center;gap:8px;width:100%">
    <div class="layer-number">#${layerNum}</div>

    <div style="flex:1">
        <div style="font-weight:700">
            ${layer.text || 'Empty'}
            <span style="
                background:#333;
                color:#fff;
                padding:2px 6px;
                border-radius:4px;
                font-size:10px;
                margin-left:6px;">
                ${viewShort}
            </span>
        </div>

        <div style="font-size:11px;opacity:.7">
            ${layer.type} • ${partId}
        </div>
    </div>

    <!-- DUPLICATE -->
    <div onclick="duplicateApplicationLayer('${layer.id}',event)"
        style="cursor:pointer;font-size:14px;padding:4px 6px;border-radius:4px;background:#eee;color:#000">
        ⧉ ${viewShort}
    </div>

    <!-- REMOVE -->
    <div onclick="removeApplicationLayer('${layer.id}',event)"
        style="cursor:pointer;padding:0 6px;font-weight:700">
        ×
    </div>
</div>
`;

                    item.onclick = function () {
                        selectApplicationLayer(layer.id);
                    };

                    container.appendChild(item);
                    layerNum++;
                });

            });

        });

    };



    //=======Duplicate layer ============= //
    window.duplicateApplicationLayer = function (layerId, event) {

        if (event) event.stopPropagation();

        const original = findLayerById(layerId);
        if (!original) return;

        const view = original.view;
        const partId = original.partId;

        const copy = JSON.parse(JSON.stringify(original));

        copy.id = `app-${Date.now()}`;     // new ID
        copy.x += 20;                     // little offset
        copy.y += 20;

        if (!window.applicationsApplied[view])
            window.applicationsApplied[view] = {};

        if (!window.applicationsApplied[view][partId])
            window.applicationsApplied[view][partId] = [];

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

        let foundLayer = null;
        Object.values(window.applicationsApplied).forEach(viewData => {
            Object.values(viewData).forEach(layers => {
                layers.forEach(layer => {
                    if (layer.id === layerId) foundLayer = layer;
                });
            });
        });

        if (!foundLayer) {
            console.warn('Layer not found:', layerId);
            return;
        }

        // ================= AUTO VIEW SWITCH =================
        if (foundLayer.view && window.currentView !== foundLayer.view) {

            console.log('🔁 Switching view to:', foundLayer.view);

            if (window.switchView) {
                window.switchView(foundLayer.view);
            }

            setTimeout(() => {
                window.initializeApplicationsOnLoad();
            }, 400);
        }


        // Load the layer's outline style
        if (foundLayer.outlineStyle) {
            window.currentOutlineStyle = foundLayer.outlineStyle;
            if (foundLayer.outlineColors) {
                window.outlineColors = { ...foundLayer.outlineColors };
            }

            // Update UI
            const displaySection = document.getElementById('currentOutlineDisplay');
            if (displaySection) {
                displaySection.style.display = 'block';
            }

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

        updateApplicationLayersList();
        updateApplicationControls(foundLayer);

        document.getElementById('applicationLayerControls').style.display = 'block';

        const textEl = document.getElementById(layerId);
        if (textEl) {
            textEl.style.filter = 'drop-shadow(0 0 8px rgba(0,123,255,0.8))';
            setTimeout(() => {
                if (foundLayer.outlineStyle && foundLayer.outlineStyle.includes('shadow')) {
                    // Restore original shadow
                    applyOutlineStyleToText(layerId);
                } else {
                    textEl.style.filter = '';
                }
            }, 1000);
        }


        // ================= PATTERN UI SYNC =================
        if (foundLayer.hasPattern) {

            document.getElementById('textPatternColorControls').style.display = 'block';
            document.getElementById('patternPlaceholder').style.display = 'none';

            if (foundLayer.patternSvg) {
                renderTextPatternPalette(foundLayer.patternSvg);
            }

        } else {

            document.getElementById('textPatternColorControls').style.display = 'none';
            document.getElementById('patternPlaceholder').style.display = 'block';
        }



        // ================= MASCOT UI SYNC =================
        if (foundLayer.hasMascot) {

            document.getElementById('textMascotColorControls').style.display = 'block';
            document.getElementById('mascotPlaceholder').style.display = 'none';

        } else {

            document.getElementById('textMascotColorControls').style.display = 'none';
            document.getElementById('mascotPlaceholder').style.display = 'block';
        }




    };

    // =================== UPDATE CONTROLS ===================

    window.updateApplicationControls = function (layer) {
        document.getElementById('applicationText').value = layer.text || '';
        document.getElementById('fontSize').value = layer.fontSize || 2000;
        document.getElementById('fontSizeValue').textContent = layer.fontSize || 2000;

        // Update outline width slider
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

    // =================== REAL-TIME UPDATES ===================

    window.updateApplicationText = function (value) {
        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        layer.text = value;

        const textEl = document.getElementById(layer.id);
        if (textEl) {
            textEl.textContent = value;
        }

        // Update outline elements too
        const outlines = document.querySelectorAll(`[data-outline-for="${layer.id}"]`);
        outlines.forEach(outline => {
            outline.textContent = value;
        });

        if (window.saveCustomizations) window.saveCustomizations();
        updateApplicationLayersList();
    };

    window.updateFontSize = function (value) {
        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        layer.fontSize = parseInt(value);

        const textEl = document.getElementById(layer.id);
        if (textEl) {
            textEl.setAttribute('font-size', value);
        }

        const outlines = document.querySelectorAll(`[data-outline-for="${layer.id}"]`);
        outlines.forEach(outline => {
            outline.setAttribute('font-size', value);
        });

        document.getElementById('fontSizeValue').textContent = value;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.updateFontFamily = function (value) {
        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        layer.fontFamily = value;

        const textEl = document.getElementById(layer.id);
        if (textEl) {
            textEl.style.fontFamily = value;
        }

        const outlines = document.querySelectorAll(`[data-outline-for="${layer.id}"]`);
        outlines.forEach(outline => {
            outline.style.fontFamily = value;
        });

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

        if (x !== null) {
            layer.x = parseInt(x);
            document.getElementById('posXValue').textContent = x;
        }

        if (y !== null) {
            layer.y = parseInt(y);
            document.getElementById('posYValue').textContent = y;
        }

        const textEl = document.getElementById(layer.id);
        if (textEl) {
            const newX = centerX + layer.x;
            const newY = centerY + layer.y;

            textEl.setAttribute('x', newX);
            textEl.setAttribute('y', newY);

            const outlines = document.querySelectorAll(`[data-outline-for="${layer.id}"]`);
            outlines.forEach(outline => {
                outline.setAttribute('x', newX);
                outline.setAttribute('y', newY);
            });

            if (layer.rotation) {
                textEl.setAttribute('transform', `rotate(${layer.rotation} ${newX} ${newY})`);
                outlines.forEach(outline => {
                    outline.setAttribute('transform', `rotate(${layer.rotation} ${newX} ${newY})`);
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

            const outlines = document.querySelectorAll(`[data-outline-for="${layer.id}"]`);
            outlines.forEach(outline => {
                outline.setAttribute('transform', `rotate(${value} ${x} ${y})`);
            });
        }

        document.getElementById('rotationValue').textContent = value;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    // =================== UPDATE OUTLINE WIDTH ===================

    window.updateOutlineStroke = function (type, value) {

        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        const textEl = document.getElementById(layer.id);
        if (!textEl) return;

        const outlines = document.querySelectorAll(`[data-outline-for="${layer.id}"]`);

        if (type === 'outline1') {
            textEl.setAttribute('stroke-width', value);
            layer.strokeWidth = parseInt(value);
        }

        if (type === 'outline2' && outlines.length > 0) {
            outlines[0].setAttribute('stroke-width', value);
        }

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

                style.innerHTML = `
@font-face {
font-family: 'font_${font.id}';
src: url('${font.file_url}') format('truetype');
font-display: swap;
}
`;

                document.head.appendChild(style);
            }

        });
    }




    window.openFontModal = function () {
        document.getElementById('fontModal').style.display = 'flex';
        renderFontGrid();
    }

    window.closeFontModal = function () {
        document.getElementById('fontModal').style.display = 'none';
    }

    function renderFontGrid() {

        const grid = document.getElementById('fontGrid');
        grid.innerHTML = '';

        const previewText =
            document.getElementById('applicationText')?.value || '85';

        const currentLayer = findLayerById(window.currentApplicationLayer);

        window.backendFonts.forEach(f => {

            const div = document.createElement('div');
            div.style.cssText = 'border:2px solid #ddd;padding:20px;text-align:center;border-radius:8px;cursor:pointer;background:#fff';

            div.innerHTML = `
<div style="font-size:42px;font-family:font_${f.id}">${previewText}</div>
<p style="margin-top:10px;font-weight:600">${f.name}</p>
`;

            // ✅ SELECTED FONT STYLE
            if (currentLayer?.fontFamily === `font_${f.id}`) {
                div.style.border = '2px solid #000';
                div.style.background = '#8d8d8d';
            }

            // ✅ HOVER EFFECT
            div.onmouseenter = () => {
                if (currentLayer?.fontFamily !== `font_${f.id}`)
                    div.style.background = '#f2f2f2';
            };

            div.onmouseleave = () => {
                if (currentLayer?.fontFamily !== `font_${f.id}`)
                    div.style.background = '#fff';
            };

            div.onclick = () => {
                updateFontFamily(`font_${f.id}`);
                closeFontModal();
            };

            grid.appendChild(div);

        });
    }


    // =================== PATTERN/MASCOT INTEGRATION ===================

    // Sync pattern colors to tab when pattern is applied
    window.syncPatternColorsToTab = function () {
        const container = document.getElementById('patternColorPaletteInTab');
        if (!container) return;

        // Get pattern color palette HTML from main pattern section
        const mainPalette = document.getElementById('patternColorPalette');
        if (mainPalette && mainPalette.innerHTML) {
            container.innerHTML = mainPalette.innerHTML;

            // Show pattern controls, hide placeholder
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
            if (el.hasAttribute('fill') && el.getAttribute('fill') !== 'none') {
                el.setAttribute('fill', color);
            }
        });

        console.log("🎨 Pattern recolored");
    };


    // Apply pattern to text (wrapper for existing function)
    window.applyPatternToText = function (svgContent, forcedLayerId = null) {

        const layerId = forcedLayerId || window.currentApplicationLayer;
        if (!layerId) return;

        const layer = findLayerById(layerId);

        if (!layer) return;

        const textEl = document.getElementById(layer.id);
        if (!textEl) return;

        const mainSvg = window.getMainSvg();

        let defs = mainSvg.querySelector('defs');
        if (!defs) {
            defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            mainSvg.insertBefore(defs, mainSvg.firstChild);
        }

        const parser = new DOMParser();
        const doc = parser.parseFromString(svgContent, 'image/svg+xml');
        let patternSvg = doc.documentElement;

        if (!patternSvg || patternSvg.nodeName !== 'svg') {
            alert("Pattern SVG invalid");
            return;
        }

        patternSvg = patternSvg.cloneNode(true);

        // ✅ ENSURE viewBox
        if (!patternSvg.getAttribute('viewBox')) {
            patternSvg.setAttribute('viewBox', '0 0 100 100');
        }

        // ✅ FORCE SIZE
        patternSvg.setAttribute('width', '100');
        patternSvg.setAttribute('height', '100');

        // ===== CREATE PATTERN =====
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

        // ✅ APPLY TO TEXT
        textEl.setAttribute('fill', `url(#${patternId})`);
        textEl.setAttribute('fill-opacity', '1');

        layer.patternId = patternId;
        layer.patternSvg = svgContent;
        layer.hasPattern = true;
        renderTextPatternPalette(svgContent);


        document.getElementById('textPatternColorControls').style.display = 'block';
        document.getElementById('patternPlaceholder').style.display = 'none';
        switchTextCustomizationTab('pattern');

        console.log("✅ Pattern Applied Successfully");


        // ---------- SHOW THUMBNAIL PREVIEW ----------
        const preview = document.getElementById('textPatternPreview');

        if (preview) {
            preview.innerHTML = svgContent;

            const svg = preview.querySelector('svg');
            if (svg) {
                svg.style.width = '70px';
                svg.style.height = '70px';
                svg.style.display = 'block';
            }
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

            row.innerHTML = `
<div style="width:30px;height:30px;border-radius:6px;background:${patternColor};border:2px solid #ccc"></div>
<span style="font-weight:700;">→</span>
<div class="text-color-row" style="display:flex;gap:6px"></div>
`;

            const choices = row.querySelector('.text-color-row');

            palette.forEach(userColor => {

                const box = document.createElement('div');
                box.style = `
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
            if (el.getAttribute('fill')?.toLowerCase() === oldColor) {
                el.setAttribute('fill', newColor);
            }
        });

    };

    // function rebuildTextPattern(layer){

    // const pattern = getMainSvg().querySelector(`#${layer.patternId}`);
    // if(!pattern) return;

    // const parser=new DOMParser();
    // const doc=parser.parseFromString(layer.patternSvg,'image/svg+xml');
    // const svg=doc.querySelector('svg');

    // Object.entries(layer.replacements).forEach(([oldColor,newColor])=>{

    // svg.querySelectorAll('[fill]').forEach(e=>{
    //  if(e.getAttribute('fill')?.toLowerCase() === oldColor){
    //    e.setAttribute('fill',newColor);
    //  }
    // });

    // });

    // pattern.innerHTML='';
    // pattern.appendChild(svg);

    // console.log("✅ Text pattern rebuilt");

    // }





    // Apply mascot to text

    function rebuildTextPattern(layer) {

        const mainSvg = getMainSvg();
        const pattern = mainSvg.querySelector(`#${layer.patternId}`);
        if (!pattern) return;

        const textEl = document.getElementById(layer.id);
        if (!textEl) return;

        // 🔒 SAVE CURRENT SETTINGS
        const oldTransform = pattern.getAttribute('patternTransform');
        const oldX = pattern.getAttribute('x');
        const oldY = pattern.getAttribute('y');
        const oldWidth = pattern.getAttribute('width');
        const oldHeight = pattern.getAttribute('height');

        // Parse original SVG
        const parser = new DOMParser();
        const doc = parser.parseFromString(layer.patternSvg, 'image/svg+xml');
        const svg = doc.querySelector('svg');

        // Apply color replacements
        Object.entries(layer.replacements || {}).forEach(([oldColor, newColor]) => {
            svg.querySelectorAll('[fill]').forEach(e => {
                if (e.getAttribute('fill')?.toLowerCase() === oldColor) {
                    e.setAttribute('fill', newColor);
                }
            });
        });

        // 🔥 CLEAR & REBUILD
        pattern.innerHTML = '';
        pattern.appendChild(svg);

        // 🔒 RESTORE ALL POSITION + SIZE SETTINGS
        pattern.setAttribute('x', oldX);
        pattern.setAttribute('y', oldY);
        pattern.setAttribute('width', oldWidth);
        pattern.setAttribute('height', oldHeight);

        if (oldTransform) {
            pattern.setAttribute('patternTransform', oldTransform);
        }

        svg.setAttribute('width', oldWidth);
        svg.setAttribute('height', oldHeight);
        svg.setAttribute('preserveAspectRatio', 'xMidYMid slice');

        console.log("✅ Pattern rebuilt WITHOUT size break");
    }




    window.applyMascotToText = function (svgContent, forcedLayerId = null) {

        const layerId = forcedLayerId || window.currentApplicationLayer;
        if (!layerId) return;

        const layer = findLayerById(layerId);

        if (!layer) return;

        const textEl = document.getElementById(layer.id);
        if (!textEl) return;

        // 🔥 CREATE MASCOT PATTERN FOR TEXT FILL
        const mainSvg = window.getMainSvg();
        let defs = mainSvg.querySelector('defs');
        if (!defs) {
            defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            mainSvg.insertBefore(defs, mainSvg.firstChild);
        }

        // Parse mascot SVG
        const parser = new DOMParser();
        const doc = parser.parseFromString(svgContent, 'image/svg+xml');
        let mascotSvg = doc.documentElement;

        if (!mascotSvg || mascotSvg.nodeName !== 'svg') {
            alert("Mascot SVG load failed");
            return;
        }

        mascotSvg = mascotSvg.cloneNode(true);

        // Remove white backgrounds
        mascotSvg.querySelectorAll('rect,circle,path').forEach(el => {
            const fill = el.getAttribute('fill');
            if (fill && (fill === '#fff' || fill === '#ffffff' || fill === 'white')) {
                el.remove();
            }
        });

        if (!mascotSvg.hasAttribute('viewBox')) {
            mascotSvg.setAttribute('viewBox', '0 0 100 100');
        }

        // Get text bounding box
        const bbox = textEl.getBBox();

        // Create unique mascot pattern ID
        const mascotId = `text-mascot-${layer.id}`;

        // Remove old mascot if exists
        const oldMascot = defs.querySelector(`#${mascotId}`);
        if (oldMascot) oldMascot.remove();

        // Create pattern element for mascot tiling
        const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
        pattern.setAttribute('id', mascotId);
        pattern.setAttribute('patternUnits', 'userSpaceOnUse');
        pattern.setAttribute('x', bbox.x);
        pattern.setAttribute('y', bbox.y);

        // Tile size based on count (default 4 tiles)
        const tileSize = Math.min(bbox.width, bbox.height) / 4;
        pattern.setAttribute('width', tileSize);
        pattern.setAttribute('height', tileSize);

        mascotSvg.setAttribute('width', tileSize);
        mascotSvg.setAttribute('height', tileSize);
        mascotSvg.setAttribute('preserveAspectRatio', 'xMidYMid meet');

        pattern.appendChild(mascotSvg);
        defs.appendChild(pattern);

        // Apply mascot as TEXT FILL
        textEl.setAttribute('fill', `url(#${mascotId})`);
        textEl.setAttribute('fill-opacity', '1');

        // Store mascot data on layer
        layer.mascotSvg = svgContent;
        layer.hasMascot = true;
        layer.mascotId = mascotId;
        layer.mascotTileSize = tileSize;
        layer.mascotSize = 100;  // Default 100%
        layer.mascotOpacity = 100;  // Default 100%
        layer.mascotCount = 4;  // Default 4 tiles

        console.log('✅ Mascot applied to TEXT:', layer.id);

        // Show mascot controls
        document.getElementById('textMascotColorControls').style.display = 'block';
        document.getElementById('mascotPlaceholder').style.display = 'none';

        // Update preview
        const preview = document.getElementById('textMascotPreview');
        if (preview) {
            preview.innerHTML = svgContent;
            const svg = preview.querySelector('svg');
            if (svg) {
                svg.style.maxWidth = '60px';
                svg.style.maxHeight = '60px';
            }
        }

        // Reset sliders to default
        const sizeSlider = document.getElementById('mascotSizeTabSlider');
        const opacitySlider = document.getElementById('mascotOpacityTabSlider');
        const countSlider = document.getElementById('mascotCountTabSlider');

        const sizeValue = document.getElementById('mascotSizeValueTab');
        const opacityValue = document.getElementById('mascotOpacityValueTab');
        const countValue = document.getElementById('mascotCountValueTab');

        if (sizeSlider) sizeSlider.value = 100;
        if (sizeValue) sizeValue.textContent = '100';

        if (opacitySlider) opacitySlider.value = 100;
        if (opacityValue) opacityValue.textContent = '100';

        if (countSlider) countSlider.value = 4;
        if (countValue) countValue.textContent = '4';

        if (window.saveCustomizations) window.saveCustomizations();
    };
    //================== Letter Spacing ==================//
    window.updateLetterSpacing = function (value) {
        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        layer.letterSpacing = parseFloat(value);

        const textEl = document.getElementById(layer.id);
        if (textEl) {
            textEl.style.letterSpacing = value + 'px';
        }

        const outlines = document.querySelectorAll(`[data-outline-for="${layer.id}"]`);
        outlines.forEach(o => {
            o.style.letterSpacing = value + 'px';
        });

        document.getElementById('letterSpacingValue').textContent = value;

        if (window.saveCustomizations) window.saveCustomizations();
    };
    // ============= Width hight ============== //
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

        textEl.setAttribute(
            'transform',
            `translate(${x},${y}) scale(${layer.scaleX},${layer.scaleY}) translate(${-x},${-y}) rotate(${layer.rotation || 0} ${x} ${y})`
        );

        const outlines = document.querySelectorAll(`[data-outline-for="${layer.id}"]`);
        outlines.forEach(o => {
            o.setAttribute(
                'transform',
                `translate(${x},${y}) scale(${layer.scaleX},${layer.scaleY}) translate(${-x},${-y}) rotate(${layer.rotation || 0} ${x} ${y})`
            );
        });

        if (window.saveCustomizations) window.saveCustomizations();
    };

    // =================== DELETE LAYER ===================

    window.deleteCurrentApplicationLayer = function () {
        if (!window.currentApplicationLayer) return;
        if (!confirm('Delete this application?')) return;
        removeApplicationLayer(window.currentApplicationLayer);
    };

    // =================== TEXT PATTERN/MASCOT LIBRARIES (SEPARATE FROM PART PATTERNS) ===================

    // Open pattern library specifically for TEXT filling
    window.openTextPatternLibrary = function () {
        if (!window.currentApplicationLayer) {
            alert('Please select a text layer first! (Go to Applications sidebar and click on a text layer)');
            return;
        }

        // Set flag that we're selecting for TEXT, not part
        window.selectingPatternForText = true;

        // Open the pattern library modal (reuse existing modal)
        if (window.openPatternLibrary) {
            window.openPatternLibrary();
        } else {
            // Fallback: open modal directly
            const modal = document.getElementById('patternLibraryModal');
            if (modal) {
                modal.style.display = 'flex';
                loadPatternsFromDB();
            }
        }

        console.log('📝 Opening pattern library for TEXT fill');
    };

    // Open mascot library specifically for TEXT filling
    window.openTextMascotLibrary = function () {
        if (!window.currentApplicationLayer) {
            alert('Please select a text layer first! (Go to Applications sidebar and click on a text layer)');
            return;
        }

        // Set flag that we're selecting for TEXT, not part
        window.selectingMascotForText = true;

        // Open the mascot template modal (reuse existing modal)
        if (window.openMascotTemplateModal) {
            window.openMascotTemplateModal();
        } else {
            // Fallback: open modal directly
            const modal = document.getElementById('mascotTemplateModal');
            if (modal) {
                modal.style.display = 'flex';
                // Load mascots
                fetch('/api/mascot-templates')
                    .then(r => r.json())
                    .then(data => {
                        let html = '<div style="border:2px dashed #999;padding:10px;border-radius:8px;text-align:center;cursor:pointer;background:#fafafa" onclick="clearTextMascot();closeMascotTemplateModal();"><div style="height:140px;display:flex;align-items:center;justify-content:center;font-weight:700;">BLANK</div></div>';

                        data.forEach(t => {
                            html += `<div style="border:1px solid #ddd;padding:10px;border-radius:8px;text-align:center;cursor:pointer" onclick="selectMascotForText('${encodeURIComponent(t.svg_data || '')}')"><img src="${t.image_data}" style="width:100%;height:140px;object-fit:contain;background:#f5f5f5"><p style="margin-top:8px;font-weight:600">${t.title}</p></div>`;
                        });

                        document.getElementById('mascotTemplateGrid').innerHTML = html;
                    });
            }
        }

        console.log('📝 Opening mascot library for TEXT fill');
    };

    // Helper function for selecting mascot for text
    window.selectMascotForText = function (svg) {
        if (svg) {
            const decodedSvg = decodeURIComponent(svg);
            window.applyMascotToText(decodedSvg);
        }

        // Close modal
        const modal = document.getElementById('mascotTemplateModal');
        if (modal) modal.style.display = 'none';

        if (window.closeMascotTemplateModal) {
            window.closeMascotTemplateModal();
        }

        window.selectingMascotForText = false;
    };

    // Modified pattern application - checks if for text or part
    window.applyPatternFromLibrary = function (svgContent) {
        if (window.selectingPatternForText && window.currentApplicationLayer) {
            // Apply to TEXT
            window.applyPatternToText(svgContent);
            window.selectingPatternForText = false;
        } else {
            // Apply to PART (your existing functionality)
            if (window.applyUploadedPattern) {
                window.applyUploadedPattern();
            }
        }
    };

    // Modified mascot application - checks if for text or part
    window.applyMascotFromLibrary = function (svgContent) {
        if (window.selectingMascotForText && window.currentApplicationLayer) {
            // Apply to TEXT
            window.applyMascotToText(svgContent);
            window.selectingMascotForText = false;
        } else {
            // Apply to PART (your existing functionality)
            if (window.applyUploadedMascot) {
                window.applyUploadedMascot();
            }
        }
    };

    // Override to prevent "Please select a part" error for text mascots
    const originalOpenMascotTemplateModal = window.openMascotTemplateModal;
    window.openMascotTemplateModal = function () {
        // If opening for text, skip part check
        if (window.selectingMascotForText) {
            document.getElementById('mascotTemplateModal').style.display = 'flex';

            fetch('/api/mascot-templates')
                .then(r => r.json())
                .then(data => {
                    let html = '';

                    // BLANK option
                    html += `
                <div style="border:2px dashed #999;padding:10px;border-radius:8px;text-align:center;cursor:pointer;background:#fafafa"
                     onclick="clearTextMascot(); closeMascotTemplateModal();">
                   <div style="height:140px;display:flex;align-items:center;justify-content:center;font-weight:700;">
                     BLANK
                   </div>
                </div>
                `;

                    data.forEach(t => {
                        html += `
                    <div style="border:1px solid #ddd;padding:10px;border-radius:8px;text-align:center;cursor:pointer"
                         onclick="selectMascotForText('${encodeURIComponent(t.svg_data || '')}')">
                        <img src="${t.image_data}" style="width:100%;height:140px;object-fit:contain;background:#f5f5f5">
                        <p style="margin-top:8px;font-weight:600">${t.title}</p>
                    </div>`;
                    });

                    document.getElementById('mascotTemplateGrid').innerHTML = html;
                })
                .catch(err => {
                    console.error('Error loading mascots:', err);
                    document.getElementById('mascotTemplateGrid').innerHTML =
                        '<p style="color:#999;text-align:center;padding:40px;">Error loading mascots</p>';
                });
        } else if (originalOpenMascotTemplateModal) {
            // Use original function for part mascots
            originalOpenMascotTemplateModal();
        }
    };

    // Override pattern library open to skip part check for text
    const originalOpenPatternLibrary = window.openPatternLibrary;
    window.openPatternLibrary = function () {
        // If opening for text, skip part check
        if (window.selectingPatternForText) {
            document.getElementById('patternLibraryModal').style.display = 'flex';

            if (window.loadPatternsFromDB) {
                window.loadPatternsFromDB();
            } else {
                // Fallback
                fetch('/api/patterns')
                    .then(res => res.json())
                    .then(patterns => {
                        const container = document.getElementById('patternList');
                        container.innerHTML = '';

                        // BLANK tile
                        const blank = document.createElement('div');
                        blank.style.cssText = 'border:2px dashed #999;border-radius:8px;padding:10px;cursor:pointer;text-align:center;background:#fafafa';
                        blank.innerHTML = '<div style="height:120px;display:flex;align-items:center;justify-content:center;font-weight:700;">BLANK</div>';
                        blank.onclick = () => {
                            clearTextPattern();
                            closePatternLibrary();
                        };
                        container.appendChild(blank);

                        patterns.forEach(pattern => {
                            const div = document.createElement('div');
                            div.style.cssText = 'border:2px solid #ddd;border-radius:8px;padding:10px;cursor:pointer;text-align:center;background:#fff;transition:all 0.3s';
                            div.innerHTML = `
                            <div style="height:120px;display:flex;align-items:center;justify-content:center;">
                                <img src="${pattern.svg_url}" style="max-width:100%;max-height:100%;">
                            </div>
                            <p style="margin-top:8px;font-weight:600;">${pattern.name}</p>
                        `;
                            div.onmouseover = () => div.style.borderColor = '#007bff';
                            div.onmouseout = () => div.style.borderColor = '#ddd';
                            div.onclick = () => {
                                fetch(pattern.svg_url)
                                    .then(res => res.text())
                                    .then(svgContent => {
                                        window.applyPatternToText(svgContent);
                                        closePatternLibrary();
                                        window.selectingPatternForText = false;
                                    });
                            };
                            container.appendChild(div);
                        });
                    })
                    .catch(err => console.error('Error loading patterns:', err));
            }
        } else if (originalOpenPatternLibrary) {
            // Use original function for part patterns
            originalOpenPatternLibrary();
        }
    };

    // Clear pattern from text
    window.clearTextPattern = function () {
        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasPattern) return;

        const textEl = document.getElementById(layer.id);
        if (!textEl) return;

        // Remove pattern from defs
        const mainSvg = window.getMainSvg();
        if (layer.patternId) {
            const pattern = mainSvg.querySelector(`#${layer.patternId}`);
            if (pattern) pattern.remove();
        }

        // Reset text fill to base color
        const colors = layer.outlineColors || window.outlineColors;
        textEl.setAttribute('fill', colors.baseColor || '#FFFFFF');
        textEl.setAttribute('fill-opacity', '1');

        // Clear layer data
        delete layer.patternSvg;
        delete layer.hasPattern;
        delete layer.patternId;
        delete layer.patternSize;
        delete layer.patternOpacity;

        // Hide controls
        document.getElementById('textPatternColorControls').style.display = 'none';
        document.getElementById('patternPlaceholder').style.display = 'block';

        if (window.saveCustomizations) window.saveCustomizations();

        console.log('✅ Pattern cleared from text');
    };

    // Clear mascot from text
    window.clearTextMascot = function () {
        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasMascot) return;

        const textEl = document.getElementById(layer.id);
        if (!textEl) return;

        // Remove mascot pattern from defs
        const mainSvg = window.getMainSvg();
        if (layer.mascotId) {
            const pattern = mainSvg.querySelector(`#${layer.mascotId}`);
            if (pattern) pattern.remove();
        }

        // Reset text fill to base color
        const colors = layer.outlineColors || window.outlineColors;
        textEl.setAttribute('fill', colors.baseColor || '#FFFFFF');
        textEl.setAttribute('fill-opacity', '1');

        // Clear layer data
        delete layer.mascotSvg;
        delete layer.hasMascot;
        delete layer.mascotId;
        delete layer.mascotSize;
        delete layer.mascotOpacity;
        delete layer.mascotCount;
        delete layer.mascotTileSize;

        // Hide controls
        document.getElementById('textMascotColorControls').style.display = 'none';
        document.getElementById('mascotPlaceholder').style.display = 'block';

        if (window.saveCustomizations) window.saveCustomizations();

        console.log('✅ Mascot cleared from text');
    };

    // Update pattern size in text
    window.updateTextPatternSize = function (value) {
        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasPattern || !layer.patternId) return;

        const mainSvg = window.getMainSvg();
        const pattern = mainSvg.querySelector(`#${layer.patternId}`);
        if (!pattern) return;

        const scale = value / 100;  // Convert 0-100 to 0-1 scale
        pattern.setAttribute('patternTransform', `scale(${scale})`);

        layer.patternSize = value;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    // Update pattern opacity in text
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

    // Update mascot size in text
    window.updateTextMascotSize = function (value) {
        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasMascot || !layer.mascotId) return;

        const mainSvg = window.getMainSvg();
        const pattern = mainSvg.querySelector(`#${layer.mascotId}`);
        if (!pattern) return;

        const svg = pattern.querySelector('svg');
        if (!svg) return;

        const scale = value / 50;  // 50 is baseline
        const newSize = layer.mascotTileSize * scale;

        svg.setAttribute('width', newSize);
        svg.setAttribute('height', newSize);

        // Center the mascot in tile
        svg.setAttribute('x', (layer.mascotTileSize - newSize) / 2);
        svg.setAttribute('y', (layer.mascotTileSize - newSize) / 2);

        layer.mascotSize = value;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    // Update mascot opacity in text
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

    // Update mascot count (tiling) in text
    window.updateTextMascotCount = function (value) {
        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer || !layer.hasMascot || !layer.mascotId) return;

        const textEl = document.getElementById(layer.id);
        const bbox = textEl.getBBox();

        const mainSvg = window.getMainSvg();
        const pattern = mainSvg.querySelector(`#${layer.mascotId}`);
        if (!pattern) return;

        // Recalculate tile size based on count
        const newTileSize = Math.min(bbox.width, bbox.height) / value;

        pattern.setAttribute('width', newTileSize);
        pattern.setAttribute('height', newTileSize);

        const svg = pattern.querySelector('svg');
        if (svg) {
            svg.setAttribute('width', newTileSize);
            svg.setAttribute('height', newTileSize);
        }

        layer.mascotTileSize = newTileSize;
        layer.mascotCount = value;
        if (window.saveCustomizations) window.saveCustomizations();
    };

    window.removeApplicationLayer = function (layerId, event) {
        if (event) event.stopPropagation();

        Object.keys(window.applicationsApplied).forEach(view => {
            Object.keys(window.applicationsApplied[view]).forEach(partId => {
                window.applicationsApplied[view][partId] = window.applicationsApplied[view][partId].filter(l => l.id !== layerId);

                if (window.applicationsApplied[view][partId].length === 0) {
                    delete window.applicationsApplied[view][partId];
                }
            });
        });

        const textEl = document.getElementById(layerId);
        if (textEl) textEl.remove();

        const outlines = document.querySelectorAll(`[data-outline-for="${layerId}"]`);
        outlines.forEach(outline => outline.remove());

        if (window.currentApplicationLayer === layerId) {
            window.currentApplicationLayer = null;
            document.getElementById('applicationLayerControls').style.display = 'none';
        }

        updateApplicationLayersList();

        if (window.saveCustomizations) window.saveCustomizations();

        console.log('✅ Application removed:', layerId);




    };

    // =================== HELPER FUNCTIONS ===================

    window.findLayerById = function (layerId) {
        let found = null;

        Object.values(window.applicationsApplied).forEach(viewData => {
            Object.values(viewData).forEach(layers => {
                layers.forEach(layer => {
                    if (layer.id === layerId) found = layer;
                });
            });
        });

        return found;
    };

    // =================== APPLY APPLICATIONS TO SVG (FOR SAVE/PREVIEW) ===================

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

                if (layer.rotation) {
                    text.setAttribute('transform', `rotate(${layer.rotation} ${centerX + layer.x} ${centerY + layer.y})`);
                }

                appGroup.appendChild(text);
                // 🔥 RESTORE OUTLINE
                if (layer.outlineStyle) {
                    window.currentOutlineStyle = layer.outlineStyle;
                    window.outlineColors = { ...layer.outlineColors };
                    applyOutlineStyleToText(layer.id);
                }

                // 🔥 RESTORE TEXT PATTERN
                if (layer.hasPattern && layer.patternSvg) {
                    applyPatternToText(layer.patternSvg);

                    if (layer.patternSize)
                        updateTextPatternSize(layer.patternSize);

                    if (layer.patternOpacity)
                        updateTextPatternOpacity(layer.patternOpacity);
                }

                // 🔥 RESTORE TEXT MASCOT
                if (layer.hasMascot && layer.mascotSvg) {
                    applyMascotToText(layer.mascotSvg);

                    if (layer.mascotSize)
                        updateTextMascotSize(layer.mascotSize);

                    if (layer.mascotOpacity)
                        updateTextMascotOpacity(layer.mascotOpacity);

                    if (layer.mascotCount)
                        updateTextMascotCount(layer.mascotCount);
                }

            });
        });

        console.log(`✅ Applications applied to ${view}`);
    };

    // =================== INITIALIZE ON LOAD ===================



    window.initializeApplicationsOnLoad = function () {
        if (!window.applicationsApplied) return;

        const view = window.currentView;
        if (!window.applicationsApplied[view]) return;
        if (Object.keys(window.applicationsApplied[view]).length === 0) return;

        // Purane elements clean karo
        const svg = window.getMainSvg();
        if (!svg) {
            console.warn('SVG not ready yet, retrying...');
            setTimeout(window.initializeApplicationsOnLoad, 300);
            return;
        }

        svg.querySelectorAll('[id^="app-group-"]').forEach(g => g.remove());
        svg.querySelectorAll('#application-group').forEach(g => g.remove());
        svg.querySelectorAll('[data-outline-for]').forEach(e => e.remove());

        console.log('🎨 Initializing applications for view:', view, window.applicationsApplied[view]);

        Object.entries(window.applicationsApplied[view]).forEach(([partId, layers]) => {
            layers.forEach(layer => {

                // Layer ko SVG mein add karo
                addApplicationToSvg(layer);

                // ✅ Pattern restore
                if (layer.hasPattern && layer.patternSvg) {
                    const prevLayer = window.currentApplicationLayer;
                    window.currentApplicationLayer = layer.id;
                    applyPatternToText(layer.patternSvg, layer.id);
                    if (layer.patternSize) updateTextPatternSize(layer.patternSize);
                    if (layer.patternOpacity) updateTextPatternOpacity(layer.patternOpacity);
                    window.currentApplicationLayer = prevLayer;
                }

                // ✅ Mascot restore
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

        // Layer list update karo
        updateApplicationLayersList();
        console.log('✅ Applications initialized for view:', view);
    };



    // Auto-initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(window.initializeApplicationsOnLoad, 600);
        });
    } else {
        setTimeout(window.initializeApplicationsOnLoad, 600);
    }
    document.addEventListener('DOMContentLoaded', () => {
        loadBackendFonts();
    });

})();
