(function () {
    'use strict';

    // =================== APPLICATION STATE ===================
    window.currentModelId = window.MODEL_ID;


    console.log('MODEL ID FOUND:', window.MODEL_ID);

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

    window.showHideColorPickers = function (style) {

        // Show outline colors section
        document.getElementById('outlineColorsSection').style.display = 'block';

        // Show/hide based on style
        const outline1 = document.getElementById('outline1Section');
        const outline2 = document.getElementById('outline2Section');
        const shadow = document.getElementById('shadowSection');

        // Reset all
        outline1.style.display = 'none';
        outline2.style.display = 'none';
        shadow.style.display = 'none';

        switch (style) {
            case 'single':
                // Only base color
                break;

            case 'two-color':
                outline1.style.display = 'block';
                break;

            case 'two-color-shadow':
                outline1.style.display = 'block';
                shadow.style.display = 'block';
                break;

            case 'three-color':
                outline1.style.display = 'block';
                outline2.style.display = 'block';
                break;

            case 'single-shadow':
                shadow.style.display = 'block';
                break;

            case 'three-color-shadow':
                outline1.style.display = 'block';
                outline2.style.display = 'block';
                shadow.style.display = 'block';
                break;
        }
    };

    window.populateOutlineColorPickers = function () {

        if (!window.currentApplicationLayer) return;

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        const colors = window.backendColors?.length
            ? window.backendColors.map(c => c.code)
            : ['#FFFFFF', '#000000', '#FF5722', '#2196F3', '#4CAF50', '#FFC107', '#9C27B0', '#666666'];

        function picker(id, key) {
            createColorPicker(id, colors, layer.outlineColors[key], color => {
                layer.outlineColors[key] = color;
                window.outlineColors = { ...layer.outlineColors };
                applyOutlineStyleToText(layer.id);
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
                layer.fill = colors.baseColor; // 🔥 ADD

                textEl.removeAttribute('stroke');
                break;

            case 'two-color':
                textEl.setAttribute('fill', colors.baseColor);
                textEl.setAttribute('stroke', colors.outline1);
                textEl.setAttribute('stroke-width', stroke);
                break;

            case 'two-color-shadow':
                textEl.setAttribute('fill', colors.baseColor);
                textEl.setAttribute('stroke', colors.outline1);
                textEl.setAttribute('stroke-width', stroke);
                break;

            case 'single-shadow':
                textEl.setAttribute('fill', colors.baseColor);
                textEl.style.filter = `drop-shadow(${shadow}px ${shadow}px 0 ${colors.shadow})`;
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

        const view = window.currentView;
        const viewLayers = window.applicationsApplied[view];

        if (!viewLayers || Object.keys(viewLayers).length === 0) {
            container.innerHTML = '<p style="color:#999; text-align:center; padding:20px; font-size:13px;">No applications added yet</p>';
            return;
        }

        let layerNum = 1;

        Object.entries(viewLayers).forEach(([partId, layers]) => {
            layers.forEach(layer => {
                const item = document.createElement('div');
                item.className = 'application-layer-item';
                if (window.currentApplicationLayer === layer.id) {
                    item.classList.add('active');
                }

                item.innerHTML = `
                <div class="layer-number">#${layerNum}</div>
                <div class="layer-info">
                    <div class="layer-title">${layer.text || 'Empty'}</div>
                    <div class="layer-details">${layer.type} • ${layer.partId}</div>
                </div>
<div style="display:flex;gap:6px;">
    <div onclick="duplicateApplicationLayer('${layer.id}', event)"
        style="cursor:pointer;font-size:14px;padding:4px 6px;border-radius:4px;background:#eee">
        ⧉
    </div>

    <div class="layer-remove" onclick="removeApplicationLayer('${layer.id}', event)">×</div>
</div>
            `;

                item.onclick = function (e) {
                    if (e.target.classList.contains('layer-remove')) return;
                    selectApplicationLayer(layer.id);
                };

                container.appendChild(item);
                layerNum++;
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

    // Apply pattern to text (wrapper for existing function)
    window.applyPatternToText = function (svgContent) {
        if (!window.currentApplicationLayer) {
            alert('Please select a text layer first!');
            return;
        }

        const layer = findLayerById(window.currentApplicationLayer);
        if (!layer) return;

        const textEl = document.getElementById(layer.id);
        if (!textEl) return;

        // 🔥 CREATE PATTERN FOR TEXT FILL
        const mainSvg = window.getMainSvg();
        let defs = mainSvg.querySelector('defs');
        if (!defs) {
            defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            mainSvg.insertBefore(defs, mainSvg.firstChild);
        }

        // Parse pattern SVG
        const parser = new DOMParser();
        const doc = parser.parseFromString(svgContent, 'image/svg+xml');
        let patternSvg = doc.documentElement;

        if (!patternSvg || patternSvg.nodeName !== 'svg') {
            alert("Pattern SVG load failed");
            return;
        }

        patternSvg = patternSvg.cloneNode(true);

        if (!patternSvg.hasAttribute('viewBox')) {
            patternSvg.setAttribute('viewBox', '0 0 100 100');
        }

        // Get text bounding box
        const bbox = textEl.getBBox();

        // Create unique pattern ID for this text
        const patternId = `text-pattern-${layer.id}`;

        // Remove old pattern if exists
        const oldPattern = defs.querySelector(`#${patternId}`);
        if (oldPattern) oldPattern.remove();

        // Create pattern element
        const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
        pattern.setAttribute('id', patternId);
        pattern.setAttribute('patternUnits', 'userSpaceOnUse');
        pattern.setAttribute('x', bbox.x);
        pattern.setAttribute('y', bbox.y);
        pattern.setAttribute('width', bbox.width / 2);  // Tile size
        pattern.setAttribute('height', bbox.height / 2);
        pattern.setAttribute('patternTransform', 'scale(1)');  // Initial scale at 100%

        patternSvg.setAttribute('width', '100%');
        patternSvg.setAttribute('height', '100%');
        patternSvg.setAttribute('preserveAspectRatio', 'xMidYMid slice');

        pattern.appendChild(patternSvg);
        defs.appendChild(pattern);

        // Apply pattern as TEXT FILL
        textEl.setAttribute('fill', `url(#${patternId})`);
        layer.fill = `url(#${patternId})`;

        textEl.setAttribute('fill-opacity', '1');

        // Store pattern data on layer
        layer.patternSvg = svgContent;
        layer.hasPattern = true;
        layer.patternId = patternId;
        layer.patternSize = 100;  // Default 100%
        layer.patternOpacity = 100;  // Default 100%

        console.log('✅ Pattern applied to TEXT:', layer.id);

        // Show pattern controls in tab
        document.getElementById('textPatternColorControls').style.display = 'block';
        document.getElementById('patternPlaceholder').style.display = 'none';

        // Update preview
        const preview = document.getElementById('textPatternPreview');
        if (preview) {
            preview.innerHTML = svgContent;
            const svg = preview.querySelector('svg');
            if (svg) {
                svg.style.maxWidth = '60px';
                svg.style.maxHeight = '60px';
            }
        }

        // Reset sliders to default
        const sizeSlider = document.getElementById('patternSizeTab');
        const opacitySlider = document.getElementById('patternOpacityTab');
        const sizeValue = document.getElementById('patternSizeValueTab');
        const opacityValue = document.getElementById('patternOpacityValueTab');

        if (sizeSlider) sizeSlider.value = 100;
        if (sizeValue) sizeValue.textContent = '100';
        if (opacitySlider) opacitySlider.value = 100;
        if (opacityValue) opacityValue.textContent = '100';

        if (window.saveCustomizations) window.saveCustomizations();
    };

    // Apply mascot to text
    window.applyMascotToText = function (svgContent) {
        if (!window.currentApplicationLayer) {
            alert('Please select a text layer first!');
            return;
        }

        const layer = findLayerById(window.currentApplicationLayer);
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
        layer.fill = `url(#${mascotId})`;

        textEl.setAttribute('fill-opacity', '1');

        // Store mascot data on layer
        layer.mascotSvg = svgContent;
        layer.hasMascot = true;
        layer.mascotId = mascotId;
        layer.mascotTileSize = tileSize;


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

        const scale = value / 100;
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

                    window.currentApplicationLayer = layer.id;

                    if (!layer.mascotId) {
                        applyMascotToText(layer.mascotSvg);
                    }

                    updateTextMascotCount(layer.mascotCount || 4);
                    updateTextMascotSize(layer.mascotSize || 100);
                    updateTextMascotOpacity(layer.mascotOpacity || 100);

                    const el = document.getElementById(layer.id);
                    if (el) el.setAttribute('fill', el.getAttribute('fill'));
                }


            });
        });

        console.log(`✅ Applications applied to ${view}`);
    };

    // =================== INITIALIZE ON LOAD ===================

    // window.initializeApplicationsOnLoad = function () {

    //     const svg = window.getMainSvg();
    //     if (svg) {
    //         const oldGroup = svg.querySelector('#application-group');
    //         if (oldGroup) oldGroup.remove();
    //     }




    //     if (!window.applicationsApplied) return;

    //     console.log('🎨 Initializing applications...');

    //     const view = window.currentView;
    //     if (!window.applicationsApplied[view]) return;

    //     Object.entries(window.applicationsApplied[view]).forEach(([partId, layers]) => {
    //         layers.forEach(layer => {
    //             addApplicationToSvg(layer);
    //         });
    //     });

    //     updateApplicationLayersList();

    //     console.log('✅ Applications initialized');
    // };
window.initializeApplicationsOnLoad = function () {

    const svg = window.getMainSvg();
    if (!svg) {
        console.warn('SVG not ready yet, retrying...');
        setTimeout(window.initializeApplicationsOnLoad, 300);
        return;
    }

    // ✅ PEHLE SAARI PURANI APPLICATION ELEMENTS CLEAN KARO
    const oldGroup = svg.querySelector('#application-group');
    if (oldGroup) oldGroup.remove();

    svg.querySelectorAll('[data-outline-for]').forEach(e => e.remove());

    if (!window.applicationsApplied) return;

    const view = window.currentView;
    if (!window.applicationsApplied[view]) return;
    if (Object.keys(window.applicationsApplied[view]).length === 0) return;

    console.log('🎨 Initializing applications for view:', view);

    Object.entries(window.applicationsApplied[view]).forEach(([partId, layers]) => {
        layers.forEach(layer => {

            // addApplicationToSvg ke andar duplicate check hai - safe hai
            addApplicationToSvg(layer);

            // ✅ Pattern restore
            if (layer.hasPattern && layer.patternSvg) {
                const prev = window.currentApplicationLayer;
                window.currentApplicationLayer = layer.id;
                applyPatternToText(layer.patternSvg, layer.id);
                if (layer.patternSize) updateTextPatternSize(layer.patternSize);
                if (layer.patternOpacity) updateTextPatternOpacity(layer.patternOpacity);
                window.currentApplicationLayer = prev;
            }

            // ✅ Mascot restore
            if (layer.hasMascot && layer.mascotSvg) {
                const prev = window.currentApplicationLayer;
                window.currentApplicationLayer = layer.id;
                applyMascotToText(layer.mascotSvg, layer.id);
                if (layer.mascotSize) updateTextMascotSize(layer.mascotSize);
                if (layer.mascotOpacity) updateTextMascotOpacity(layer.mascotOpacity);
                if (layer.mascotCount) updateTextMascotCount(layer.mascotCount);
                window.currentApplicationLayer = prev;
            }

        });
    });

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
// Global shared pattern variables (dono blocks ke liye)
window.uploadedSvgContent = null;
window.patternAngle = 0;
window.patternOpacity = 100;
window.patternSize = 50;
window.patternScale = 1;
window.selectedPatternReplacements = {};
window.isDraggingKnob = false;
(function () {
    'use strict';

    window.currentGradientType = 'linear';

    window.gradientStops = [
        { color: '#FFC000', position: 0 },
        { color: '#228B22', position: 100 }
    ];

    window.getMainSvg = function () {
        return document.querySelector('#modelDisplay svg');
    };

    window.safeListener = function (id, event, cb) {
        const el = document.getElementById(id);
        if (el) el.addEventListener(event, cb);
    };
    window.switchToolPage = function (page) {

        document.getElementById('colorPage').style.display = 'none';
        document.getElementById('patternPage').style.display = 'none';
        document.getElementById('applicationPage').style.display = 'none';

        const fs = document.getElementById('fillSwitch');

        if (fs) {
            fs.style.display = 'flex';   // 🔥 ALWAYS SHOW
        }

        document.getElementById(page + 'Page').style.display = 'block';
    }


    // * =================== Active tab ===================== * //
    window.activateTab = function (tabId) {
        document.querySelectorAll('.tab-btn').forEach(tab => {
            tab.classList.remove('active');
        });

        const btn = document.getElementById(tabId);
        if (btn) btn.classList.add('active');

        // 🔥 GET TOGGLE ICON
        const toggleIcon = document.getElementById('applicationsSidebarToggleIcon');

        // TOOL PAGES
        if (tabId === 'colorBtn') {
            switchToolPage('color');
            if (toggleIcon) toggleIcon.classList.remove('visible'); // 🔥 HIDE ICON
        }

        if (tabId === 'patternBtn') {
            switchToolPage('pattern');
            if (toggleIcon) toggleIcon.classList.remove('visible'); // 🔥 HIDE ICON
        }
        if (tabId === 'applicationBtn') {

            switchToolPage('application');

            // SHOW TOGGLE ICON
            if (toggleIcon) toggleIcon.classList.add('visible');

            // OPEN SIDEBAR
            if (window.openApplicationsSidebar) {
                window.openApplicationsSidebar();
            }

            // UPDATE LAYER LIST
            if (window.updateApplicationLayersList) {
                window.updateApplicationLayersList();
            }

        } else {

            // 🔥 ANY OTHER TAB → CLOSE SIDEBAR
            if (window.closeApplicationsSidebar) {
                window.closeApplicationsSidebar();
            }

            // 🔥 REMOVE ACTIVE TEXT LAYER
            if (window.currentApplicationLayer) {

                const oldText = document.getElementById(window.currentApplicationLayer);
                if (oldText) oldText.style.filter = '';

                window.currentApplicationLayer = null;

                // hide controls
                const ctrl = document.getElementById('applicationLayerControls');
                if (ctrl) ctrl.style.display = 'none';

                // remove active class
                document.querySelectorAll('.application-layer-item').forEach(i => {
                    i.classList.remove('active');
                });
            }

            // hide toggle icon
            if (toggleIcon) toggleIcon.classList.remove('visible');
        }


        if (tabId === 'saveBtn') {
            saveDesign();
            if (toggleIcon) toggleIcon.classList.remove('visible'); // 🔥 HIDE ICON
        }

        if (tabId === 'previewBtn') {
            openPreviewPanel();
            if (toggleIcon) toggleIcon.classList.remove('visible'); // 🔥 HIDE ICON
        }

        if (tabId === 'zoomBtn') {
            togglePanZoom();
            if (toggleIcon) toggleIcon.classList.remove('visible'); // 🔥 HIDE ICON
        }

        if (tabId === 'frontBtn') switchView('front');
        if (tabId === 'backBtn') switchView('back');
        if (tabId === 'leftBtn') switchView('left');
        if (tabId === 'rightBtn') switchView('right');

        if (tabId === 'resetBtn') {
            resetDesign();
            if (toggleIcon) toggleIcon.classList.remove('visible'); // 🔥 HIDE ICON
        }
    };


    /* ================= CORE GLOBAL ================= */

    window.MODEL_ID = window.MODEL_ID;
    window.API_URL = window.API_URL;

    window.currentView = 'front';
    window.selectedSvgElement = null;
    window.allSvgParts = [];
    window.colorChanges = { front: {}, back: {}, left: {}, right: {} };
    window.gradientChanges = { front: {}, back: {}, left: {}, right: {} };

    window.patternsApplied = { front: {}, back: {}, left: {}, right: {} };
    window.mascotsApplied = { front: {}, back: {}, left: {}, right: {} };   // ⭐ ADD THIS
    window.originalColors = { front: {}, back: {}, left: {}, right: {} };


    window.modelDataByView = {
        front: { parts: [] },
        back: { parts: [] },
        left: { parts: [] },
        right: { parts: [] }
    };

    let currentModel = null;
    let history = [];
    let historyIndex = -1;
    window.modelViews = { front: null, back: null, left: null, right: null };
    let modelViews = window.modelViews;
    /* ================= INIT ================= */

    async function init() {

        await loadModel();
        loadSavedCustomizations();

        if (window.setupCircularSlider) setupCircularSlider();
        if (window.loadFonts) loadFonts();

    }

    ////////////////
    function loadSavedCustomizations() {

        const saved = localStorage.getItem(`model_${MODEL_ID}_customizations`);
        if (!saved) return;

        try {
            const data = JSON.parse(saved);

            if (data.colorChanges) {
                colorChanges = data.colorChanges;
            }

            if (data.gradientChanges) {
                gradientChanges = data.gradientChanges;
            }
            if (data.mascot_changes) {
                mascotsApplied = data.mascot_changes;   // ⭐ ADD THIS
            }
            if (data.patternsApplied) {
                patternsApplied = data.patternsApplied;

                // ✅ SAFETY: ensure svgContent exists
                Object.values(patternsApplied).forEach(viewObj => {
                    Object.values(viewObj).forEach(p => {
                        if (!p.svgContent) {
                            console.warn("⚠ Pattern missing svgContent", p);
                        }
                    });
                });
            }

            console.log("✅ Saved customizations restored (patterns OK)");

        } catch (e) {
            console.error("❌ Failed to restore customizations", e);
        }
    }


    /* ================= MODEL ================= */

    async function loadModel() {

        try {
            const response = await fetch(API_URL);
            const data = await response.json();
            currentModel = data;
            if (data.pattern_changes) {
                patternsApplied = data.pattern_changes;
            }


            if (data.color_changes) {
                colorChanges = data.color_changes;
            }
            if (data.mascot_changes) {
                mascotsApplied = data.mascot_changes;   // 🔥 REQUIRED
            }


            //             if (data.applications) {
            //     window.applicationsApplied = data.applications;
            // }
            if (data.applications) {
                Object.assign(window.applicationsApplied, data.applications);
            }





            modelViews.front = data.front_view || {};
            modelViews.back = data.back_view || {};
            modelViews.left = data.left_view || {};
            modelViews.right = data.right_view || {};

            displayView('front');

            setTimeout(() => {
                if (window.extractDefaultColors) {
                    extractDefaultColors();
                }
            }, 500);
        } catch (e) {
            console.error('Error loading model:', e);
            document.getElementById('modelDisplay').innerHTML =
                '<div style="color:#ff0000;padding:40px;">Error loading model</div>';
        }
    }


    /* ================= VIEW ================= */

    window.switchView = function (view) {
        displayView(view);
    }
    function displayView(view) {
        currentView = view;
        const viewData = modelViews[view];
        const container = document.getElementById('modelDisplay');
        if (!container) return;

        if (!viewData || (!viewData.svg_url && !viewData.black_image_url && !viewData.white_image_url)) {
            container.innerHTML = '<div style="color:#999;padding:40px;">No images available for this view</div>';
            return;
        }

        // Optional: Clean any leftover pattern overlays from previous SVG
        // (helps in case processSvg didn't run yet or failed)
        const existingSvg = container.querySelector('svg');
        if (existingSvg) {
            const oldGroup = existingSvg.querySelector('#pattern-overlay-group');
            if (oldGroup) {
                oldGroup.remove();
                console.log('Cleaned leftover pattern overlay before new view load');
            }
        }

        let html = '<div style="position:relative;width:100%;height:100%;display:flex;align-items:center;justify-content:center;">';

        if (viewData.svg_url) {
            html += `<img id="svgImage" src="${viewData.svg_url}?t=${Date.now()}" style="position:absolute;max-width:100%;max-height:100%;z-index:1;" onload="processSvg()" />`;
        }
        if (viewData.white_image_url) {
            html += `<img src="${viewData.white_image_url}?t=${Date.now()}" style="position:absolute;max-width:100%;max-height:100%;z-index:2;mix-blend-mode:multiply;pointer-events:none;" />`;
        }
        if (viewData.black_image_url) {
            html += `<img src="${viewData.black_image_url}?t=${Date.now()}" style="position:absolute;max-width:100%;max-height:100%;z-index:3;mix-blend-mode:screen;pointer-events:none;" />`;
        }

        html += '</div>';
        container.innerHTML = html;
        // setTimeout(() => {
        //     if (window.initializeApplicationsOnLoad) {
        //         window.initializeApplicationsOnLoad();
        //     }
        // }, 400);

    }


    /* ================= SVG ================= */

    window.processSvg = function () {
        const svgImage = document.getElementById('svgImage');
        if (!svgImage) return;

        fetch(svgImage.src)
            .then(res => res.text())
            .then(svgText => {
                const parser = new DOMParser();
                const svgDoc = parser.parseFromString(svgText, 'image/svg+xml');
                const svgElement = svgDoc.querySelector('svg');
                if (!svgElement) return;

                svgElement.setAttribute('width', '100%');
                svgElement.setAttribute('height', '100%');
                svgElement.setAttribute('preserveAspectRatio', 'xMidYMid meet');

                const elements = svgElement.querySelectorAll('path, polygon, circle, rect, ellipse');
                allSvgParts = Array.from(elements);

                elements.forEach((el, index) => {
                    if (!el.id) el.id = `svg-part-${index}`;
                    // el.dataset.partName = el.id;
el.dataset.partName = el.getAttribute('inkscape:label')
    || el.getAttribute('data-name')
    || el.getAttribute('id')
    || `part-${index + 1}`;
                    el.classList.add('svg-hoverable');
                    el.style.cursor = 'pointer';
                    el.style.transition = 'all 0.3s ease';

                    el.addEventListener('click', () => selectSvgElement(el));
                    //      el.addEventListener('mouseenter', () => {
                    //     if (el !== selectedSvgElement) {
                    //         el.style.stroke = '#007bff';
                    //         el.style.strokeWidth = '2';
                    //     }
                    // });

                    // el.addEventListener('mouseleave', () => {
                    //     if (el !== selectedSvgElement) {
                    //         el.style.stroke = 'none';
                    //     }
                    // });



                    if (!originalColors[currentView][el.id]) {
                        originalColors[currentView][el.id] = el.getAttribute('fill') ||
                            '#ffffff';
                    }

                    if (colorChanges[currentView] && colorChanges[currentView][el.id]) {
                        el.setAttribute('fill', colorChanges[currentView][el.id]);
                    }
                });

                document.getElementById('svgImage').replaceWith(svgElement);
                applyPatternsToSvg(svgElement, currentView);
                applyMascotsToSvg(svgElement, currentView);
                updateUndoRedoButtons();
                populatePartDropdown();
                updatePartDropdown();
                populateModelDataForView(currentView);

                // ✅ SIRF YAHAN SE CALL KARO:
                setTimeout(() => {
                    if (window.initializeApplicationsOnLoad) {
                        window.initializeApplicationsOnLoad();
                    }
                }, 400);
            })
            .catch(err => console.error('SVG load error:', err));
    };





    function loadPartGradient(partId) {
        if (gradientChanges[currentView]?.[partId]) {
            const saved = gradientChanges[currentView][partId];

            currentGradientType = saved.type || 'linear';
            gradientStops = saved.stops.map(stop => ({ ...stop }));  // deep copy

            // Angle etc. bhi set karo jaise upar
            const angleInput = document.getElementById('gradAngle');
            if (angleInput) angleInput.value = saved.angle || 90;

            console.log(`loadPartGradient: Loaded ${gradientStops.length} stops`);
        } else {
            gradientStops = [
                { color: '#FFC000        ', position: 0 },
                { color: '#228B22', position: 100 }
            ];
        }

        renderGradientStops();
        updateGradientPreview();
        updateStopMarkers();
    }

    function selectSvgElement(el) {



        // 🔥 CLEAR ACTIVE APPLICATION LAYER WHEN PART CHANGES
        if (window.currentApplicationLayer) {

            // remove glow from old text
            const oldText = document.getElementById(window.currentApplicationLayer);
            if (oldText) {
                oldText.style.filter = '';
            }

            window.currentApplicationLayer = null;

            // hide application controls
            const ctrl = document.getElementById('applicationLayerControls');
            if (ctrl) ctrl.style.display = 'none';

            // remove active class from sidebar layers
            document.querySelectorAll('.application-layer-item').forEach(i => {
                i.classList.remove('active');
            });
        }




        // 🔥 SET CURRENT INDEX
        window.currentPartIndex = allSvgParts.indexOf(el);

        // 🔥 UPDATE ONLY PART NAME (TOP BUTTON)
        const partName = document.getElementById('partName');
        if (partName) {
            partName.textContent = el.dataset.partName || 'Part';
        }

        // 1. Clear previous selection
     document.querySelectorAll('.selected').forEach(e => {
    e.classList.remove('selected');

});

        // 2. Set new selection
        window.selectedSvgElement = el;
el.classList.add('selected');


        const partId = el.id;
        const activeTab = document.querySelector('.tab-btn.active');
        const activeTabId = activeTab ? activeTab.id : 'colorBtn';



        // ─────────────────────────────────────────────────────────────
        // PATTERN TAB ACTIVE HAI → Pattern logic priority do
        // ─────────────────────────────────────────────────────────────
        if (activeTabId === 'patternBtn') {
            const saved = window.patternsApplied?.[window.currentView]?.[partId];

            if (saved && saved.svgContent) {
                // ─── State restore ───
                window.uploadedSvgContent = saved.svgContent;
                window.selectedPatternReplacements = { ...(saved.replacements || {}) };
                window.patternAngle = saved.angle || 0;
                window.patternSize = saved.size || 50;
                window.patternOpacity = saved.opacity || 100;
                window.patternScale = (saved.size || 50) / 50;

                // ─── UI controls update ───
                const sizeSlider = document.getElementById('patternSize');
                if (sizeSlider) {
                    sizeSlider.value = window.patternSize;
                    document.getElementById('sizeValue').textContent = window.patternSize;
                }

                const opacitySlider = document.getElementById('patternOpacity');
                if (opacitySlider) {
                    opacitySlider.value = window.patternOpacity;
                    document.getElementById('opacityValue').textContent = window.patternOpacity + '%';
                }

                const angleDisplay = document.getElementById('angleValue');
                if (angleDisplay) {
                    angleDisplay.textContent = window.patternAngle + '°';
                }

                // Circular knob bhi update (agar tumhara circular slider hai)
                if (window.updateKnobAndPattern) {
                    window.updateKnobAndPattern(window.patternAngle);
                }

                // ─── Pattern ko turant re-apply / refresh karo ───
                recreatePatternAndOverlayWithNewColors();

                // Controls show karo
                document.getElementById('patternControls').style.display = 'block';

                // Preview aur palette update (thoda delay safe hai)
                setTimeout(() => {
                    if (window.updatePatternPreview) window.updatePatternPreview();
                    if (window.updatePatternColorPalette) window.updatePatternColorPalette();
                }, 80);

                console.log(`Pattern restored & reapplied for part: ${partId}`);
            }
            else {
                // No pattern → reset UI
                window.uploadedSvgContent = null;
                window.selectedPatternReplacements = {};
                window.patternAngle = 0;
                window.patternSize = 50;
                window.patternOpacity = 100;

                document.getElementById('patternControls').style.display = 'none';

                const previewBox = document.getElementById('patternPreviewBox');
                if (previewBox) {
                    previewBox.innerHTML = '<span style="color:#999;">No pattern applied on selected part</span>';
                }

                console.log(`No pattern found for part: ${partId}`);
            }

            return;  // ← Color/gradient logic skip karo
        }

        // ─────────────────────────────────────────────────────────────
        // COLOR / GRADIENT TAB → purana normal logic
        // ─────────────────────────────────────────────────────────────
        let savedGradient = gradientChanges[window.currentView]?.[partId];
        if (!savedGradient) {
            const svgGradient = extractStopsFromSvg(el);
            if (svgGradient) savedGradient = svgGradient;
        }

        const currentFill = el.getAttribute('fill') || '#fff';
        const isGradientFill = currentFill.startsWith('url(') || !!savedGradient;

        if (isGradientFill) {
            setFillType('gradient', false);

            if (savedGradient) {
                if (!gradientChanges[window.currentView][partId]) {
                    gradientChanges[window.currentView][partId] = {
                        type: savedGradient.type || 'linear',
                        angle: savedGradient.angle || 90,
                        stops: JSON.parse(JSON.stringify(savedGradient.stops))
                    };
                }

                window.currentGradientType = savedGradient.type || 'linear';
                window.gradientStops = JSON.parse(JSON.stringify(savedGradient.stops));

                const angleInput = document.getElementById('gradAngle');
                if (angleInput) {
                    angleInput.value = savedGradient.angle || 90;
                    document.getElementById('angleDisplay').textContent = (savedGradient.angle || 90) + '°';
                }

                const angleControls = document.getElementById('angleControls');
                if (angleControls) {
                    angleControls.style.display = window.currentGradientType === 'linear' ? 'block' : 'none';
                }

                document.getElementById('linearBtn')?.classList.toggle('active', window.currentGradientType === 'linear');
                document.getElementById('radialBtn')?.classList.toggle('active', window.currentGradientType === 'radial');
            } else {
                window.currentGradientType = 'linear';
                window.gradientStops = [
                    { color: '#FFC000', position: 0 },
                    { color: '#228B22', position: 100 }
                ];
            }

            const centerBtn = document.getElementById('selectedColorBtn');
            if (centerBtn) {
                centerBtn.style.background = 'linear-gradient(90deg, ' +
                    window.gradientStops.map(s => s.color).join(', ') + ')';
            }
        } else {
            setFillType('solid', false);
            const solidColor = colorChanges[window.currentView]?.[partId] || currentFill;

            const centerBtn = document.getElementById('selectedColorBtn');
            if (centerBtn) centerBtn.style.background = solidColor;
window.highlightWheelColor(solidColor);

            window.gradientStops = [
                { color: '#FFC000', position: 0 },
                { color: '#228B22', position: 100 }
            ];
        }

        // UI refresh for color/gradient
        if (window.renderGradientStops) window.renderGradientStops();
        if (window.updateGradientPreview) window.updateGradientPreview();
        if (window.updateStopMarkers) window.updateStopMarkers();

        console.log(`Color tab: Part ${partId} selected | Mode: ${isGradientFill ? 'GRADIENT' : 'SOLID'}`);
    }








    // ================= PART SELECT FIX =================

    const partBtn = document.getElementById('partDropdownBtn');
    const partList = document.getElementById('partDropdown');

    // OPEN / CLOSE DROPDOWN
    partBtn.addEventListener('click', function () {
        partList.classList.toggle('open');
    });

    // BUILD PART LIST FROM SVG
    function populatePartDropdown() {

        if (!allSvgParts.length) return;

        partList.innerHTML = '';

        allSvgParts.forEach((el, i) => {

            const li = document.createElement('li');

            li.innerHTML = `
     <span class="partNum">${i + 1}</span>
     <span class="partLabel">${el.dataset.partName || 'Part'}</span>
   `;

            li.onclick = () => {
                selectSvgElement(el);
                partList.classList.remove('open');
            };

            partList.appendChild(li);
        });

    }


    // UPDATE ACTIVE PART NAME + HIGHLIGHT
    function updatePartDropdown() {

        document.querySelectorAll('#partDropdown li').forEach((li, i) => {
            li.classList.toggle('active', i === window.currentPartIndex);
        });

        const active = allSvgParts[window.currentPartIndex];

      if (active) {
    const nameSpan = document.getElementById('partName');
    if (nameSpan) {
        nameSpan.textContent = active.dataset.partName || 'Part';
    }
}
    }





    // NEXT / PREVIOUS BUTTONS
    window.navigatePart = function (dir) {

        if (!allSvgParts.length) return;

        window.currentPartIndex += dir;

        if (window.currentPartIndex < 0) window.currentPartIndex = allSvgParts.length - 1;
        if (window.currentPartIndex >= allSvgParts.length) window.currentPartIndex = 0;

        selectSvgElement(allSvgParts[window.currentPartIndex]);
    };

    // PATCH selectSvgElement
   // PATCH selectSvgElement
// PATCH selectSvgElement
const originalSelect = window.selectSvgElement;
window.selectSvgElement = function (element) {
    if (!element) return;

    window.selectedSvgElement = element;

    // ✅ Part name update karo - HAMESHA
    const partName = document.getElementById('partName');
    if (partName) {
        partName.textContent = element.dataset.partName || element.id || 'Part';
    }

    // ✅ Current index update karo
    const idx = window.allSvgParts ? window.allSvgParts.indexOf(element) : -1;
    if (idx !== -1) window.currentPartIndex = idx;

    // ✅ Dropdown highlight update karo
    document.querySelectorAll('#partDropdown li').forEach((li, i) => {
        li.classList.toggle('active', i === window.currentPartIndex);
    });

    // ✅ Selected CSS class
    document.querySelectorAll('.selected-svg-part').forEach(e => {
        e.classList.remove('selected-svg-part');
    });
    element.classList.add('selected-svg-part');

    // ✅ Original function bhi call karo (color/gradient logic)
    if (originalSelect) originalSelect(element);
};


    function updatePartDropdown() {

        document.querySelectorAll('#partDropdown li').forEach((li, i) => {
            li.classList.toggle('active', i === window.currentPartIndex);
        });

        const active = allSvgParts[window.currentPartIndex];

       if (active) {
    const nameSpan = document.getElementById('partName');
    if (nameSpan) {
        nameSpan.textContent = active.dataset.partName || 'Part';
    }
}
    }

    // 🔥 ADD THIS FUNCTION:
    window.populateModelDataForView = function (view) {
        if (!window.allSvgParts || window.allSvgParts.length === 0) {
            console.warn('No SVG parts available for', view);
            return;
        }

        window.modelDataByView[view].parts = window.allSvgParts.map((el, index) => ({
            part: el.id,
            title: el.dataset.partName || `Part ${index + 1}`
        }));

        console.log(`✅ Populated ${window.modelDataByView[view].parts.length} parts for ${view}`);
    };

    /* ================= HISTORY ================= */

    window.saveToHistory = function () {
        history = history.slice(0, historyIndex + 1);
        const state = {
            view: currentView,
            colors: JSON.parse(JSON.stringify(colorChanges))
        };
        history.push(state);
        historyIndex++;
        if (history.length > 50) {
            history.shift();
            historyIndex--;
        }
    }

    window.updateUndoRedoButtons = function () {
        // Placeholder - implement if you have undo/redo buttons
        console.log('History updated:', historyIndex, '/', history.length);
    }

    window.undoChange = function () {
        if (historyIndex <= 0) {
            alert("Nothing to undo!");
            return;
        }
        historyIndex--;
        const state = history[historyIndex];
        colorChanges = JSON.parse(JSON.stringify(state.colors));
        displayView(currentView);
        updateUndoRedoButtons();
    }
    window.redoChange = function () {
        if (historyIndex >= history.length - 1) {
            alert("Nothing to redo!");
            return;
        }
        historyIndex++;
        const state = history[historyIndex];
        colorChanges = JSON.parse(JSON.stringify(state.colors));
        displayView(currentView);
        updateUndoRedoButtons();
    }


    window.saveDesign = async function () {
        if (window.isSaving) return;
        window.isSaving = true;

        try {
            const views = ['front', 'back', 'left', 'right'];
            const allSvgs = {};

            // 🔥 Font CSS embed karo SVG mein
            function embedFontsInSvg(svgElement) {
                let fontCSS = '';
                if (window.backendFonts) {
                    window.backendFonts.forEach(font => {
                        fontCSS += `@font-face { font-family: 'font_${font.id}'; src: url('${font.file_url}') format('truetype'); }`;
                    });
                }

                let defs = svgElement.querySelector('defs');
                if (!defs) {
                    defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
                    svgElement.insertBefore(defs, svgElement.firstChild);
                }

                // Old style remove karo
                const oldStyle = defs.querySelector('style[data-fonts]');
                if (oldStyle) oldStyle.remove();

                if (fontCSS) {
                    const style = document.createElementNS('http://www.w3.org/2000/svg', 'style');
                    style.setAttribute('data-fonts', 'true');
                    style.textContent = fontCSS;
                    defs.insertBefore(style, defs.firstChild);
                }
            }

            for (const v of views) {
                if (!modelViews[v]?.svg_url) continue;

                if (v === window.currentView) {
                    const liveSvg = window.getMainSvg();
                    if (liveSvg) {
                        const clone = liveSvg.cloneNode(true);
                        embedFontsInSvg(clone);
                        allSvgs[v] = new XMLSerializer().serializeToString(clone);
                        continue;
                    }
                }

                const res = await fetch(modelViews[v].svg_url);
                const text = await res.text();
                const doc = new DOMParser().parseFromString(text, 'image/svg+xml');
                const svg = doc.querySelector('svg');
                if (!svg) continue;

                svg.setAttribute('width', '100%');
                svg.setAttribute('height', '100%');

                svg.querySelectorAll('path,polygon,circle,rect,ellipse').forEach((el, i) => {
                    if (!el.id) el.id = `svg-part-${i}`;
                    if (gradientChanges?.[v]?.[el.id]) {
                        rebuildGradient(svg, el.id, gradientChanges[v][el.id], v);
                        el.setAttribute('fill', `url(#gradient-${v}-${el.id})`);
                    } else if (colorChanges?.[v]?.[el.id]) {
                        el.setAttribute('fill', colorChanges[v][el.id]);
                    }
                });

                if (window.applyPatternsToSvg) applyPatternsToSvg(svg, v);
                if (window.applyMascotsToSvg) applyMascotsToSvg(svg, v);
                if (window.applyApplicationsToSvg) applyApplicationsToSvg(svg, v);

                embedFontsInSvg(svg);
                allSvgs[v] = new XMLSerializer().serializeToString(svg);
            }

            const response = await fetch(`/admin/models/${MODEL_ID}/save-design`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    svgs: allSvgs,
                    color_changes: colorChanges || {},
                    pattern_changes: patternsApplied || {},
                    mascot_changes: mascotsApplied || {},
                    applications: window.applicationsApplied || {}
                })
            });

            // 🔥 Thumbnail generate karo
            await generateAndSaveThumbnail();

            alert("✅ Design Saved Successfully");

        } catch (e) {
            console.error(e);
            alert("❌ Save Failed");
        }

        window.isSaving = false;
    };




    /* ================= RESET ================= */

    window.resetDesign = function () {
        if (!confirm("Are you sure you want to reset all changes?")) {
            return;
        }
        colorChanges = {
            front: {},
            back: {},
            left: {},
            right: {}
        };
        history = [];
        historyIndex = -1;
        displayView(currentView);
        updateUndoRedoButtons();
        alert("Design reset successfully!");
    }

    /* ================= ZOOM ================= */

    let isPanZoomActive = false;
    let scale = 1;
    let posX = 0;
    let posY = 0;
    let startX = 0;
    let startY = 0;

    function togglePanZoom() {

        const btn = document.getElementById('zoomBtn');
        const container = document.getElementById('modelDisplay');
        const inner = container.firstElementChild;

        if (!inner) return;

        isPanZoomActive = !isPanZoomActive;

        // 🔒 container lock
        container.style.overflow = 'hidden';

        if (isPanZoomActive) {

            btn.classList.add('active');
            inner.style.cursor = 'grab';

            container.onwheel = e => {
                e.preventDefault();

                scale *= e.deltaY > 0 ? 0.9 : 1.1;
                scale = Math.max(1, Math.min(scale, 4));

                applyTransform(inner, container);
            };

            container.onmousedown = e => {
                startX = e.clientX - posX;
                startY = e.clientY - posY;
                inner.style.cursor = 'grabbing';

                document.onmousemove = ev => {
                    posX = ev.clientX - startX;
                    posY = ev.clientY - startY;
                    applyTransform(inner, container);
                };

                document.onmouseup = () => {
                    document.onmousemove = null;
                    inner.style.cursor = 'grab';
                };
            };

        } else {

            btn.classList.remove('active');

            scale = 1;
            posX = 0;
            posY = 0;

            inner.style.transform = 'none';

            container.onwheel = null;
            container.onmousedown = null;
        }
    }

    function applyTransform(el, container) {

        const cw = container.clientWidth;
        const ch = container.clientHeight;

        const iw = el.offsetWidth * scale;
        const ih = el.offsetHeight * scale;

        const maxX = Math.max(0, (iw - cw) / 2);
        const maxY = Math.max(0, (ih - ch) / 2);

        posX = Math.max(-maxX, Math.min(posX, maxX));
        posY = Math.max(-maxY, Math.min(posY, maxY));

        el.style.transform =
            `translate(${posX}px,${posY}px) scale(${scale})`;
    }

    /* ================= SAVE CUSTOMIZATIONS (LOCAL STORAGE) ================= */

    window.saveCustomizations = function () {
        return; // autosave band
    };




    /* ================= PREVIEW ================= */

    // ✅ Preview Panel Functions
    window.openPreviewPanel = function () {
        // Capture all current views
        captureCurrentViewsForPreview();

        // Slide panel in
        document.getElementById('previewPanel').style.right = '0px';
        document.getElementById('previewBtn').classList.add('active');
    };

    window.closePreviewPanel = function () {
        // Slide panel out
        document.getElementById('previewPanel').style.right = '-100vw';

        // Remove active state from preview button
        document.getElementById('previewBtn').classList.remove('active');
    };

    // ================= PREVIEW AS PNG (REAL-TIME) =================
    async function captureCurrentViewsForPreview() {
        const views = ['front', 'back', 'left', 'right'];

        await Promise.all(views.map(async (view) => {
            const container = document.getElementById(`preview${view.charAt(0).toUpperCase() + view.slice(1)}`);
            if (!container) return;

            container.innerHTML = '<div style="color:#999;padding:20px;">Loading...</div>';

            if (!modelViews[view]?.svg_url) {
                container.innerHTML = '<div style="color:#999;padding:20px;text-align:center;">No view</div>';
                return;
            }

            try {
                // Create canvas for this view
                const canvas = await createMergedCanvas(view);

                // Convert canvas to image and show in preview
                const img = document.createElement('img');
                img.src = canvas.toDataURL('image/png');
                img.style.cssText = 'max-width:100%;max-height:100%;object-fit:contain;';

                container.innerHTML = '';
                container.appendChild(img);

            } catch (err) {
                console.error(`Preview error (${view}):`, err);
                container.innerHTML = '<div style="color:#f33;padding:20px;text-align:center;">Error</div>';
            }
        }));
    }

    // ================= CREATE MERGED CANVAS (SVG + OVERLAYS) =================
    async function createMergedCanvas(view) {

        // 1️⃣ Load SVG
        const res = await fetch(modelViews[view].svg_url + '?t=' + Date.now());
        const svgText = await res.text();

        const parser = new DOMParser();
        const svgDoc = parser.parseFromString(svgText, 'image/svg+xml');
        const svg = svgDoc.querySelector('svg');
        // 🔥 APPLY APPLICATION TEXT INTO PREVIEW SVG
        if (window.applyApplicationsToSvg) {
            applyApplicationsToSvg(svg, view);
        }


        if (!svg) throw new Error("No SVG");

        // Apply colors to SVG
        svg.querySelectorAll('path, polygon, circle, rect, ellipse').forEach((el, i) => {
            // ✅ APPLY PATTERNS FOR PREVIEW
            if (window.applyPatternsToSvg) {
                applyPatternsToSvg(svg, view, true);
                applyMascotsToSvg(svg, view);   // 🔥 ADD
            }

            if (!el.id) el.id = `svg-part-${i}`;

            if (gradientChanges[view]?.[el.id]) {

                rebuildGradient(svg, el.id, gradientChanges[view][el.id], view);
                el.setAttribute('fill', `url(#gradient-${view}-${el.id})`);

            } else if (colorChanges[view]?.[el.id]) {

                el.setAttribute('fill', colorChanges[view][el.id]);

            }
        });


        // 2️⃣ Get SVG natural     size
        const viewBox = svg.getAttribute('viewBox');
        let width = 800;  // default
        let height = 800;

        if (viewBox) {
            const [, , w, h] = viewBox.split(' ').map(Number);
            width = w;
            height = h;
        }

        // 3️⃣ Create canvas
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');

        // ❌ NO BACKGROUND (transparent)
        ctx.clearRect(0, 0, width, height);

        // 4️⃣ Draw SVG
        const svgData = new XMLSerializer().serializeToString(svg);
        const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
        const svgUrl = URL.createObjectURL(svgBlob);
        const svgImg = await loadImage(svgUrl);
        ctx.drawImage(svgImg, 0, 0, width, height);
        URL.revokeObjectURL(svgUrl);

        // 5️⃣ Draw WHITE overlay (MULTIPLY)
        if (modelViews[view]?.white_image_url) {
            const whiteImg = await loadImage(modelViews[view].white_image_url);
            ctx.globalCompositeOperation = 'multiply';
            ctx.drawImage(whiteImg, 0, 0, width, height);
            ctx.globalCompositeOperation = 'source-over';
        }

        // 6️⃣ Draw BLACK overlay (SCREEN)
        if (modelViews[view]?.black_image_url) {
            const blackImg = await loadImage(modelViews[view].black_image_url);
            ctx.globalCompositeOperation = 'screen';
            ctx.drawImage(blackImg, 0, 0, width, height);
            ctx.globalCompositeOperation = 'source-over';
        }

        return canvas;
    }




    // 🔥 AUTO CLOSE DROPDOWN ON OUTSIDE CLICK
    document.addEventListener('click', function (e) {

        const partBtn = document.getElementById('partDropdownBtn');
        const partList = document.getElementById('partDropdown');

        if (!partBtn || !partList) return;

        // Agar click dropdown ya button ke andar nahi hua
        if (!partBtn.contains(e.target) && !partList.contains(e.target)) {
            partList.classList.remove('open');
        }

    });

    // ================= DOWNLOAD WITH SMART SIZING =================
    document.addEventListener('click', async function (e) {
        if (e.target.id !== 'downloadAllViews') return;

        const btn = e.target;
        btn.disabled = true;
        btn.textContent = 'Downloading...';

        try {
            const views = ['front', 'back', 'left', 'right'];
            let downloaded = 0;

            for (const view of views) {

                // Get canvas from preview (already rendered)
                const container = document.getElementById(`preview${view.charAt(0).toUpperCase() + view.slice(1)}`);
                const previewImg = container?.querySelector('img');

                if (!previewImg) {
                    console.warn(`No preview for ${view}`);
                    continue;
                }

                // Use the same canvas creation function
                const canvas = await createMergedCanvas(view);

                // Download as PNG (transparent background)
                const dataUrl = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.download = `${view}-view.png`;
                link.href = dataUrl;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                downloaded++;
            }

            if (downloaded > 0) {
                alert(`✅ Downloaded ${downloaded} PNG files`);
            } else {
                alert("No valid previews to download");
            }

        } catch (err) {
            console.error('Download error:', err);
            alert("Download failed: " + err.message);
        } finally {
            btn.disabled = false;
            btn.textContent = 'Download All (PNG)';
        }
    });
    function rebuildGradient(svg, partId, data, view) {

        let defs = svg.querySelector('defs');
        if (!defs) {
            defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            svg.insertBefore(defs, svg.firstChild);
        }

        const gid = `gradient-${view}-${partId}`;

        // REMOVE OLD GRADIENT IF EXISTS
        const old = svg.querySelector(`#${gid}`);
        if (old) old.remove();
        let g;

        if (data.type === 'linear') {
            g = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');

            const rad = (data.angle - 90) * Math.PI / 180;
            g.setAttribute('x1', (50 + 50 * Math.cos(rad)) + '%');
            g.setAttribute('y1', (50 + 50 * Math.sin(rad)) + '%');
            g.setAttribute('x2', (50 - 50 * Math.cos(rad)) + '%');
            g.setAttribute('y2', (50 - 50 * Math.sin(rad)) + '%');

        } else {

            g = document.createElementNS('http://www.w3.org/2000/svg', 'radialGradient');
            g.setAttribute('cx', '50%');
            g.setAttribute('cy', '50%');
            g.setAttribute('r', '50%');
        }

        g.setAttribute('id', gid);

        data.stops.forEach(s => {
            const st = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
            st.setAttribute('offset', s.position + '%');
            st.setAttribute('stop-color', s.color);
            g.appendChild(st);
        });

        defs.appendChild(g);
    }

    // Helper function (same as before)
    function loadImage(src) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = () => resolve(img);
            img.onerror = () => reject(new Error(`Failed to load: ${src}`));
            img.src = src;
        });
    }
    function extractStopsFromSvg(el) {

        const svg = getMainSvg();
        if (!svg) return null;

        const fill = el.getAttribute('fill');
        if (!fill || !fill.startsWith('url')) return null;

        const gid = fill.replace('url(#', '').replace(')', '');

        const grad = svg.querySelector(`#${gid}`);
        if (!grad) return null;

        const stops = [];

        grad.querySelectorAll('stop').forEach(s => {
            stops.push({
                color: s.getAttribute('stop-color'),
                position: parseFloat(s.getAttribute('offset'))
            });
        });

        return {
            type: grad.tagName === 'radialGradient' ? 'radial' : 'linear',
            stops
        };
    }

    // async function generateAndSaveThumbnail() {
    //     try {
    //         const view = window.currentView || 'front';
    //         const viewData = window.modelViews?.[view];

    //         const canvas = document.createElement('canvas');
    //         canvas.width = 800;
    //         canvas.height = 800;
    //         const ctx = canvas.getContext('2d');
    //         // ctx.fillStyle = '#ffffff';
    //         // ctx.fillRect(0, 0, 800, 800);

    //         function loadImg(src) {
    //             return new Promise((resolve) => {
    //                 const img = new Image();
    //                 img.crossOrigin = 'anonymous';
    //                 img.onload = () => resolve(img);
    //                 img.onerror = () => resolve(null);
    //                 img.src = src;
    //             });
    //         }

    //         // 🔥 Font ko Base64 mein convert karo
    //         async function fontToBase64(url) {
    //             try {
    //                 const res = await fetch(url);
    //                 const buf = await res.arrayBuffer();
    //                 const bytes = new Uint8Array(buf);
    //                 let binary = '';
    //                 bytes.forEach(b => binary += String.fromCharCode(b));
    //                 return 'data:font/truetype;base64,' + btoa(binary);
    //             } catch (e) {
    //                 return url;
    //             }
    //         }

    //         const svgUrl = viewData?.svg_url;
    //         if (svgUrl) {
    //             const res = await fetch(svgUrl);
    //             const svgText = await res.text();

    //             const parser = new DOMParser();
    //             const svgDoc = parser.parseFromString(svgText, 'image/svg+xml');
    //             const svgEl = svgDoc.querySelector('svg');

    //             // Colors apply
    //             svgEl.querySelectorAll('path,polygon,circle,rect,ellipse').forEach((el, i) => {
    //                 const id = el.id || `svg-part-${i}`;
    //                 if (window.colorChanges?.[view]?.[id]) {
    //                     el.setAttribute('fill', window.colorChanges[view][id]);
    //                 }
    //             });

    //             // Applications apply
    //             if (window.applyApplicationsToSvg) {
    //                 window.applyApplicationsToSvg(svgEl, view);
    //             }

    //             // 🔥 Font Base64 embed karo
    //             let defs = svgEl.querySelector('defs');
    //             if (!defs) {
    //                 defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
    //                 svgEl.insertBefore(defs, svgEl.firstChild);
    //             }

    //             if (window.backendFonts && window.backendFonts.length > 0) {
    //                 let fontCSS = '';
    //                 for (const f of window.backendFonts) {
    //                     const base64 = await fontToBase64(f.file_url);
    //                     fontCSS += `@font-face{font-family:'font_${f.id}';src:url('${base64}') format('truetype');}`;
    //                 }
    //                 const style = document.createElementNS('http://www.w3.org/2000/svg', 'style');
    //                 style.textContent = fontCSS;
    //                 defs.insertBefore(style, defs.firstChild);
    //             }

    //             svgEl.setAttribute('preserveAspectRatio', 'xMidYMid meet');

    //             if (!svgEl.getAttribute('viewBox')) {
    //                 svgEl.setAttribute('viewBox', '0 0 800 800');
    //             }


    //             const serialized = new XMLSerializer().serializeToString(svgEl);
    //             const blob = new Blob([serialized], { type: 'image/svg+xml' });
    //             const url = URL.createObjectURL(blob);
    //             //svg //
    //             await new Promise((resolve) => {
    //                 const img = new Image();
    //                 img.onload = () => {
    //                     const ratio = Math.min(800 / img.width, 800 / img.height);
    //                     const w = img.width * ratio;
    //                     const h = img.height * ratio;
    //                     const x = (800 - w) / 2;
    //                     const y = (800 - h) / 2;

    //                     ctx.drawImage(img, x, y, w, h);
    //                     URL.revokeObjectURL(url);
    //                     resolve();
    //                 };
    //                 img.onerror = () => { URL.revokeObjectURL(url); resolve(); };
    //                 img.src = url;
    //             });
    //         }

    //         // White overlay
    //         if (viewData?.white_image_url) {
    //             const img = await loadImg(viewData.white_image_url);
    //             if (img) {
    //                 ctx.globalCompositeOperation = 'multiply';
    //                 const ratio = Math.min(800 / img.width, 800 / img.height);
    //                 const w = img.width * ratio;
    //                 const h = img.height * ratio;
    //                 const x = (800 - w) / 2;
    //                 const y = (800 - h) / 2;

    //                 ctx.drawImage(img, x, y, w, h);
    //                 ctx.globalCompositeOperation = 'source-over';
    //             }
    //         }

    //         // Black overlay
    //         if (viewData?.black_image_url) {
    //             const img = await loadImg(viewData.black_image_url);
    //             if (img) {
    //                 ctx.globalCompositeOperation = 'screen';
    //                 const ratio = Math.min(800 / img.width, 800 / img.height);
    //                 const w = img.width * ratio;
    //                 const h = img.height * ratio;
    //                 const x = (800 - w) / 2;
    //                 const y = (800 - h) / 2;

    //                 ctx.drawImage(img, x, y, w, h);
    //                 ctx.globalCompositeOperation = 'source-over';
    //             }
    //         }


    //         return new Promise((resolve) => {
    //             canvas.toBlob(async (blob) => {
    //                 const formData = new FormData();
    //                 formData.append('thumbnail', blob, 'thumbnail.png');
    //                 await fetch(`/admin/models/${MODEL_ID}/save-thumbnail`, {
    //                     method: 'POST',
    //                     headers: {
    //                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    //                     },
    //                     body: formData
    //                 });
    //                 console.log('✅ Thumbnail saved with fonts!');
    //                 resolve();
    //             }, 'image/png', 0.9);
    //         });

    //     } catch (e) {
    //         console.error('Thumbnail error:', e);
    //     }
    // }
async function generateAndSaveThumbnail() {
    try {
        const view = window.currentView || 'front';
        const viewData = window.modelViews?.[view];

        const canvas = document.createElement('canvas');
        canvas.width = 800;
        canvas.height = 800;
        const ctx = canvas.getContext('2d');

        function loadImg(src) {
            return new Promise((resolve) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => resolve(img);
                img.onerror = () => resolve(null);
                img.src = src;
            });
        }

        async function fontToBase64(url) {
            try {
                const res = await fetch(url);
                const buf = await res.arrayBuffer();
                const bytes = new Uint8Array(buf);
                let binary = '';
                bytes.forEach(b => binary += String.fromCharCode(b));
                return 'data:font/truetype;base64,' + btoa(binary);
            } catch (e) {
                return url;
            }
        }

        // ✅ LIVE SVG USE KARO - fresh fetch nahi
        const liveSvg = window.getMainSvg();
        if (liveSvg) {
            const clone = liveSvg.cloneNode(true);

            // Font embed karo
            let defs = clone.querySelector('defs');
            if (!defs) {
                defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
                clone.insertBefore(defs, clone.firstChild);
            }

            if (window.backendFonts && window.backendFonts.length > 0) {
                let fontCSS = '';
                for (const f of window.backendFonts) {
                    const base64 = await fontToBase64(f.file_url);
                    fontCSS += `@font-face{font-family:'font_${f.id}';src:url('${base64}') format('truetype');}`;
                }
                const style = document.createElementNS('http://www.w3.org/2000/svg', 'style');
                style.textContent = fontCSS;
                defs.insertBefore(style, defs.firstChild);
            }

            clone.setAttribute('preserveAspectRatio', 'xMidYMid meet');
            if (!clone.getAttribute('viewBox')) {
                clone.setAttribute('viewBox', '0 0 800 800');
            }

            const serialized = new XMLSerializer().serializeToString(clone);
            const blob = new Blob([serialized], { type: 'image/svg+xml' });
            const url = URL.createObjectURL(blob);

            await new Promise((resolve) => {
                const img = new Image();
                img.onload = () => {
                    const ratio = Math.min(800 / img.width, 800 / img.height);
                    const w = img.width * ratio;
                    const h = img.height * ratio;
                    const x = (800 - w) / 2;
                    const y = (800 - h) / 2;
                    ctx.drawImage(img, x, y, w, h);
                    URL.revokeObjectURL(url);
                    resolve();
                };
                img.onerror = () => { URL.revokeObjectURL(url); resolve(); };
                img.src = url;
            });
        }

        // White overlay
        if (viewData?.white_image_url) {
            const img = await loadImg(viewData.white_image_url);
            if (img) {
                ctx.globalCompositeOperation = 'multiply';
                const ratio = Math.min(800 / img.width, 800 / img.height);
                const w = img.width * ratio;
                const h = img.height * ratio;
                const x = (800 - w) / 2;
                const y = (800 - h) / 2;
                ctx.drawImage(img, x, y, w, h);
                ctx.globalCompositeOperation = 'source-over';
            }
        }

        // Black overlay
        if (viewData?.black_image_url) {
            const img = await loadImg(viewData.black_image_url);
            if (img) {
                ctx.globalCompositeOperation = 'screen';
                const ratio = Math.min(800 / img.width, 800 / img.height);
                const w = img.width * ratio;
                const h = img.height * ratio;
                const x = (800 - w) / 2;
                const y = (800 - h) / 2;
                ctx.drawImage(img, x, y, w, h);
                ctx.globalCompositeOperation = 'source-over';
            }
        }

        return new Promise((resolve) => {
            canvas.toBlob(async (blob) => {
                const formData = new FormData();
                formData.append('thumbnail', blob, 'thumbnail.png');
                await fetch(`/admin/models/${MODEL_ID}/save-thumbnail`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                console.log('✅ Thumbnail saved!');
                resolve();
            }, 'image/png', 0.9);
        });

    } catch (e) {
        console.error('Thumbnail error:', e);
    }
}
    document.addEventListener('DOMContentLoaded', init);





})();
