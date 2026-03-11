(function () {
    'use strict';

    /* ================= GLOBAL VARIABLES ================= */
    window.selectedColors = []; // Default model colors
    window.paletteSelectedColors = []; // User selected from palette
    let currentFillType = 'solid';
    let currentGradientType = 'linear';
    function initGradientFromWheel() {

        // agar wheel me kam az kam 2 colors hon
        if (selectedColors.length >= 2) {

            gradientStops = [
                { color: selectedColors[0], position: 0 },
                { color: selectedColors[1], position: 100 }
            ];

        } else {

            // fallback
            gradientStops = [
                { color: '#000000', position: 0 },
                { color: '#ffffff', position: 100 }
            ];
        }

        renderGradientStops();
        updateGradientPreview();
    } // Gradient color stops

    /* ================= EXTRACT DEFAULT COLORS FROM MODEL ================= */

    window.extractDefaultColors = function () {
        const svg = window.getMainSvg?.() || document.querySelector('svg');
        if (!svg) {
            console.warn("SVG not ready yet — retrying color extraction...");
            setTimeout(window.extractDefaultColors, 300);
            return;
        }


        const colors = new Set();

        svg.querySelectorAll('path, polygon, circle, rect, ellipse').forEach(el => {
            const fill = el.getAttribute('fill');

            if (fill &&
                fill !== 'none' &&
                fill !== 'transparent' &&
                !fill.startsWith('url(')) {
                colors.add(fill.toUpperCase());
            }
        });

        selectedColors = Array.from(colors);

        if (selectedColors.length === 0) {
            selectedColors = ['#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#FF00FF', '#00FFFF', '#000000', '#FFFFFF'];
        }

        if (selectedColors.length > 24) {
            selectedColors = selectedColors.slice(0, 24);
        }

        console.log("✅ Extracted Colors:", selectedColors);
        updateColorWheel();
    };

    /* ================= UPDATE COLOR WHEEL ================= */
    function rotateWheelToAngle(angle) {
        const ring = document.querySelector('#colorWheelRing svg');
        if (!ring) return;

        ring.style.transform = `rotate(${-angle}deg)`;
    }

    window.updateColorWheel = function () {

        const wheel = document.getElementById('colorWheelRing');
        if (!wheel || selectedColors.length === 0) return;

        wheel.innerHTML = '';

        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('width', '340');
        svg.setAttribute('height', '340');
        svg.style.position = 'absolute';
        svg.style.transition = 'transform .6s cubic-bezier(.22,.61,.36,1)';

        const center = 170;
        const radius = 160;
        const innerRadius = 110;

        const angleStep = 360 / selectedColors.length;

        selectedColors.forEach((color, i) => {

            const startAngle = (i * angleStep - 90) * Math.PI / 180;
            const endAngle = ((i + 1) * angleStep - 90) * Math.PI / 180;

            const x1 = center + radius * Math.cos(startAngle);
            const y1 = center + radius * Math.sin(startAngle);
            const x2 = center + radius * Math.cos(endAngle);
            const y2 = center + radius * Math.sin(endAngle);
            const x3 = center + innerRadius * Math.cos(endAngle);
            const y3 = center + innerRadius * Math.sin(endAngle);
            const x4 = center + innerRadius * Math.cos(startAngle);
            const y4 = center + innerRadius * Math.sin(startAngle);

            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            // clone for outer stroke only
            const strokePath = path.cloneNode();

            strokePath.setAttribute('fill', 'none');
            strokePath.setAttribute('stroke', color);
            strokePath.setAttribute('stroke-width', '6');
            strokePath.style.pointerEvents = 'none';
            strokePath.style.display = 'none'; // hidden until active

            svg.appendChild(strokePath);

            // link clone to main path
            path._strokeClone = strokePath;

            const largeArc = angleStep > 180 ? 1 : 0;

            path.setAttribute('d',
                `M ${x1},${y1}
 A ${radius},${radius} 0 ${largeArc},1 ${x2},${y2}
 L ${x3},${y3}
 A ${innerRadius},${innerRadius} 0 ${largeArc},0 ${x4},${y4} Z`
            );

            path.setAttribute('fill', color);
            path.setAttribute('fill-rule', 'evenodd');

            path.setAttribute('stroke', 'none');
            path.style.color = color;   // 🔥 active outline ke liye

            path.style.cursor = 'pointer';
            path.style.transition = 'all .25s';

            // save angle for rotation
            path.setAttribute('data-angle', i * angleStep);

            // CLICK
            path.addEventListener('click', function () {

                document.querySelectorAll('#colorWheelRing path').forEach(p => {
                    p.classList.remove('active');
                    if (p._strokeClone) p._strokeClone.style.display = 'none';
                });

                this.classList.add('active');

                // show only THIS outline
                if (this._strokeClone) {
                    this._strokeClone.style.display = 'block';
                    svg.appendChild(this._strokeClone); // bring outline top
                }

                svg.appendChild(this); // bring slice top

                const deg = -(i * angleStep + angleStep / 2);
                svg.style.transform = `rotate(${deg}deg)`;

                applyColorToPart(color);
                updateCenterColor(color);
            });


            path.addEventListener('mouseenter', () => path.style.filter = 'brightness(1.2)');
            path.addEventListener('mouseleave', () => path.style.filter = 'none');

            svg.appendChild(path);

        });

        wheel.appendChild(svg);
    };

    function updateCenterColor(color) {

        const btn = document.getElementById('selectedColorBtn');
        btn.style.background = color;

        // Convert hex to brightness
        const hex = color.replace('#', '');
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);

        const brightness = (r * 299 + g * 587 + b * 114) / 1000;

        // Light color → black text
        if (brightness > 200) {
            btn.style.color = '#000';
            btn.style.textShadow = '0 0 2px #000';
        } else {
            btn.style.color = '#fff';
            btn.style.textShadow = '0 0 4px rgba(0,0,0,.6)';
        }
    }


    /* ================= APPLY COLOR TO SELECTED PART ================= */

    function applyColorToPart(color) {

        if (!window.selectedSvgElement) {
            alert("Please select a part first!");
            return;
        }

        const partId = window.selectedSvgElement.id;

        // 🔥 REMOVE GRADIENT FIRST (VERY IMPORTANT)
        if (window.gradientChanges[currentView]?.[partId]) {
            delete window.gradientChanges[currentView][partId];
        }

        // Save to history
        if (window.saveToHistory) window.saveToHistory();

        // Apply color
        window.selectedSvgElement.setAttribute('fill', color);

        // Store SOLID color
        if (!window.colorChanges[currentView]) window.colorChanges[currentView] = {};
        window.colorChanges[currentView][partId] = color;

        // Update center button
        const centerBtn = document.getElementById('selectedColorBtn');
        if (centerBtn) centerBtn.style.background = color;

        // Add to wheel if not present
        const upperColor = color.toUpperCase();
        if (!selectedColors.includes(upperColor)) {
            selectedColors.push(upperColor);
            if (selectedColors.length > 24) selectedColors.shift();
            updateColorWheel();
        }

        // Save customizations

        // Update undo/redo
        if (window.updateUndoRedoButtons) updateUndoRedoButtons();

        console.log(`✅ Applied SOLID ${color} to ${partId} in ${currentView}`);
    }


    /* ================= COLOR PALETTE MODAL ================= */

    window.openColorPalette = function () {
        const modal = document.getElementById('colorPaletteModal');
        if (modal) modal.style.display = 'flex';

        paletteSelectedColors = [];
        document.querySelectorAll('.color-box').forEach(box => {
            box.classList.remove('selected');
            box.innerHTML = '';
        });
    };

    window.closeColorPalette = function () {
        const modal = document.getElementById('colorPaletteModal');
        if (modal) {
            modal.style.display = 'none';
            modal.dataset.gradientMode = 'false';
        }
    };

    window.togglePaletteColor = function (el, color) {
        const upperColor = color.toUpperCase();

        if (paletteSelectedColors.includes(upperColor)) {
            paletteSelectedColors = paletteSelectedColors.filter(c => c !== upperColor);
            el.classList.remove('selected');
            el.innerHTML = '';
        } else {
            paletteSelectedColors.push(upperColor);
            el.classList.add('selected');
            el.innerHTML = '✔';
            el.style.color = getContrastColor(color);
            el.style.fontWeight = 'bold';
            el.style.display = 'flex';
            el.style.alignItems = 'center';
            el.style.justifyContent = 'center';
        }
    };




    window.applySelectedColors = function () {

        // paletteSelectedColors.forEach(c => {
        //     const upper = c.toUpperCase();
        //     if (!selectedColors.includes(upper)) {
        //         selectedColors.push(upper);
        //     }
        // });
        if (paletteSelectedColors.length < 2) {
            alert('Please select at least 2 colors');
            return;
        }
        selectedColors = paletteSelectedColors.map(c => c.toUpperCase());
        updateColorWheel();
        initGradientFromWheel();  // 🔥 gradient sync with wheel

        // ─── 2. Agar koi part select nahi to bas band kar do ───
        if (!selectedSvgElement) {
            closeColorPalette();
            return;
        }

        const modal = document.getElementById('colorPaletteModal');
        const isGradientMode = modal?.dataset?.gradientMode === 'true';

        if (isGradientMode && window.currentGradientStopIndex !== undefined) {

            // ─── GRADIENT STOP COLOR CHANGE ───
            const newColor = paletteSelectedColors[0]; // pehla selected color use karo

            if (newColor) {
                // Update gradientStops array
                gradientStops[currentGradientStopIndex].color = newColor;

                // UI update
                renderGradientStops();
                updateGradientPreview();

                // Real-time gradient apply (yeh function already bana hua hai)
                applyGradientRealtime();

                console.log(`Gradient stop #${currentGradientStopIndex} changed to ${newColor}`);
            }

        } else {

            // ─── NORMAL SOLID COLOR MODE ───
            const color = paletteSelectedColors[0];
            if (!color) return;

            const partId = selectedSvgElement.id;

            // Gradient hatao
            if (gradientChanges[currentView]?.[partId]) {
                delete gradientChanges[currentView][partId];
            }

            selectedSvgElement.setAttribute('fill', color);

            if (!colorChanges[currentView]) colorChanges[currentView] = {};
            colorChanges[currentView][partId] = color;

            console.log(`Solid color ${color} applied to ${partId}`);
        }

        // ─── Hamesha band karo ───
        closeColorPalette();
        window.currentGradientStopIndex = undefined; // reset
        modal.dataset.gradientMode = 'false';
    };

    /* ================= FILL TYPE SWITCHER ================= */

    window.setFillType = function (type) {

        document.getElementById('solidBtn').classList.toggle('active', type === 'solid');
        document.getElementById('gradientBtn').classList.toggle('active', type === 'gradient');

        // gradient panel
        document.getElementById('gradientPanel').style.display =
            type === 'gradient' ? 'block' : 'none';

        // 🔥 color wheel only solid mode
        document.querySelector('.color-wheel-container').style.display =
            type === 'solid' ? 'block' : 'none';
        if (type === 'gradient') {
            initGradientFromWheel();   // 🔥 wheel → gradient
        }
    };


    /* ================= GRADIENT FUNCTIONS ================= */

    window.setGradientType = function (type) {
        currentGradientType = type;

        const linearBtn = document.getElementById('linearBtn');
        const radialBtn = document.getElementById('radialBtn');

        if (linearBtn) linearBtn.classList.toggle('active', type === 'linear');
        if (radialBtn) radialBtn.classList.toggle('active', type === 'radial');

        // Show/hide angle controls for linear only
        const angleControls = document.getElementById('angleControls');
        if (angleControls) {
            angleControls.style.display = type === 'linear' ? 'block' : 'none';
        }

        updateGradientPreview();
        applyGradientRealtime(); // Auto-apply on type change
    };

    // Update gradient angle in real-time
    window.updateGradientAngle = function (angle) {

        if (!selectedSvgElement) return;

        if (!gradientChanges[currentView][selectedSvgElement.id]) {
            gradientChanges[currentView][selectedSvgElement.id] = {
                type: currentGradientType,
                angle: Number(angle),
                stops: JSON.parse(JSON.stringify(gradientStops))
            };
        }

        gradientChanges[currentView][selectedSvgElement.id].angle = Number(angle);

        document.getElementById('angleDisplay').innerText = angle + '°';

        // 🔥 REAL TIME APPLY
        applyGradientRealtime();
    };


    // Add new gradient stop


    window.addGradientStop = function () {

        if (!window.selectedSvgElement) {
            alert('Select a part first');
            return;
        }

        // Pick next wheel color (cycle through selectedColors)
        const usedColors = gradientStops.map(s => s.color.toUpperCase());
        let nextColor = '#FF0000'; // fallback

        if (selectedColors && selectedColors.length > 0) {
            // Find first wheel color not already used, else just cycle
            const unused = selectedColors.find(c => !usedColors.includes(c.toUpperCase()));
            nextColor = unused || selectedColors[gradientStops.length % selectedColors.length];
        }

        gradientStops.push({
            color: nextColor,
            position: 50
        });

        normalizeStops();

        // Sync gradient data
        if (!window.gradientChanges[window.currentView]) window.gradientChanges[window.currentView] = {};
        window.gradientChanges[window.currentView][window.selectedSvgElement.id] = {
            type: currentGradientType,
            angle: Number(document.getElementById('gradAngle')?.value || 90),
            stops: JSON.parse(JSON.stringify(gradientStops))
        };

        // Real-time apply + UI update
        applyGradientRealtime();
        renderGradientStops();
        updateGradientPreview();
    };


    function normalizeStops() {
        const step = 100 / (gradientStops.length - 1);

        gradientStops.forEach((s, i) => {
            s.position = Math.round(i * step);
        });
    }

    // Remove gradient stop
    window.removeGradientStop = function (index) {
        if (gradientStops.length <= 2) {
            alert("Need at least 2 color stops!");
            return;
        }
        gradientStops.splice(index, 1);
        renderGradientStops();
        updateGradientPreview();
        applyGradientRealtime(); // Auto-apply on remove
    };

    // Render gradient stops UI - HORIZONTAL LAYOUT
    window.renderGradientStops = function () {
        const container = document.getElementById('gradientStopsContainer');
        if (!container) return;

        container.innerHTML = '';

        // Sort by position
        gradientStops.sort((a, b) => a.position - b.position);

        gradientStops.forEach((stop, index) => {
            const isSelected = (index === window.currentGradientStopIndex);

            const stopDiv = document.createElement('div');
            stopDiv.className = 'gradient-stop-item' + (isSelected ? ' active' : '');
            stopDiv.dataset.index = index;

            // ── Wheel color swatches row ──────────────────────────
            const swatchRow = document.createElement('div');
            swatchRow.style.cssText = 'display:flex;gap:5px;align-items:center;flex-wrap:wrap;flex:1;';

            (selectedColors || []).forEach((wColor) => {
                const swatch = document.createElement('div');
                const isActive = wColor.toUpperCase() === stop.color.toUpperCase();

                swatch.style.cssText = `
                width: 26px;
                height: 26px;
                border-radius: 5px;
                background: ${wColor};
                cursor: pointer;
                border: ${isActive ? '3px solid #000' : '2px solid #ddd'};
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
                font-weight: bold;
                color: ${getContrastColorLocal(wColor)};
                transition: transform .15s;
                box-sizing: border-box;
            `;

                if (isActive) {
                    swatch.innerHTML = '✓';
                }

                swatch.title = wColor;

                swatch.onmouseenter = () => swatch.style.transform = 'scale(1.15)';
                swatch.onmouseleave = () => swatch.style.transform = 'scale(1)';

                swatch.onclick = () => {
                    gradientStops[index].color = wColor.toUpperCase();
                    window.currentGradientStopIndex = index;
                    renderGradientStops();
                    updateGradientPreview();
                    applyGradientRealtime();
                };

                swatchRow.appendChild(swatch);
            });

            // ── Position input ────────────────────────────────────
            const posInput = document.createElement('input');
            posInput.type = 'number';
            posInput.min = 0;
            posInput.max = 100;
            posInput.value = stop.position;
            posInput.className = 'stop-position-input';
            posInput.title = 'Position %';
            posInput.style.width = '50px';
            posInput.onchange = () => updateStopPositionInput(index, posInput.value);

            // ── % label ──────────────────────────────────────────
            const pctLabel = document.createElement('span');
            pctLabel.textContent = '%';
            pctLabel.style.cssText = 'font-size:12px;color:#666;';

            // ── Remove button ─────────────────────────────────────
            const removeBtn = document.createElement('button');
            if (gradientStops.length > 2) {
                removeBtn.className = 'remove-stop-btn';
                removeBtn.title = 'Remove';
                removeBtn.innerHTML = '×';
                removeBtn.onclick = () => removeGradientStop(index);
            } else {
                removeBtn.style.width = '28px';
                removeBtn.style.visibility = 'hidden';
            }

            // ── Assemble ──────────────────────────────────────────
            stopDiv.appendChild(swatchRow);
            stopDiv.appendChild(posInput);
            stopDiv.appendChild(pctLabel);
            stopDiv.appendChild(removeBtn);

            container.appendChild(stopDiv);
        });

        updateStopMarkers();
    };

    // Update stop markers on preview thumbnail
    window.updateStopMarkers = function () {

        const bar = document.getElementById('gradientPreview');
        const markers = document.getElementById('stopMarkers');
        if (!markers || !bar) return;

        markers.innerHTML = '';

        gradientStops.forEach((stop, index) => {

            const isSelected = (index === window.currentGradientStopIndex);

            const m = document.createElement('div');
            m.className = 'stop-marker' + (isSelected ? ' selected' : '');
            m.style.background = stop.color;
            m.style.left = stop.position + '%';

            // Show tick on selected marker
            if (isSelected) {
                m.innerHTML = `<span style="
                position:absolute;inset:0;display:flex;align-items:center;
                justify-content:center;font-size:10px;font-weight:bold;
                color:${getContrastColorLocal(stop.color)};
                pointer-events:none;transform:rotate(-45deg);
            ">✓</span>`;
            }

            m.onclick = () => {
                window.currentGradientStopIndex = index;
                renderGradientStops();
                updateStopMarkers();
            };

            m.onmousedown = function (e) {
                e.preventDefault();
                window.currentGradientStopIndex = index;
                renderGradientStops();

                document.onmousemove = function (ev) {
                    const rect = bar.getBoundingClientRect();
                    let x = ev.clientX - rect.left;
                    let percent = (x / rect.width) * 100;
                    percent = Math.max(0, Math.min(100, percent));

                    gradientStops[index].position = Math.round(percent);

                    renderGradientStops();
                    updateGradientPreview();
                    applyGradientRealtime();
                };

                document.onmouseup = function () {
                    document.onmousemove = null;
                    document.onmouseup = null;
                };
            };

            markers.appendChild(m);
        });
    };


    window.selectGradientStopColor = function (stopIndex) {
        window.currentGradientStopIndex = stopIndex;
        renderGradientStops(); // re-render to show active state
    };
    // Update stop position from input
    window.updateStopPositionInput = function (index, value) {
        let pos = parseInt(value);
        pos = Math.max(0, Math.min(100, pos)); // Clamp 0-100

        gradientStops[index].position = pos;
        renderGradientStops();
        updateGradientPreview();
        applyGradientRealtime(); // Auto-apply on position change
    };

    // Update gradient preview
    window.updateGradientPreview = function () {
        const preview = document.getElementById('gradientPreview');
        if (!preview) return;

        const stops = gradientStops
            .map(stop => `${stop.color} ${stop.position}%`)
            .join(', ');

        if (currentGradientType === 'linear') {
            const angleInput = document.getElementById('gradAngle');
            const angle = angleInput ? angleInput.value : 90;
            preview.style.background = `linear-gradient(${angle}deg, ${stops})`;
        } else {
            preview.style.background = `radial-gradient(circle, ${stops})`;
        }

        updateStopMarkers();
    }

    window.updateGradient = function () {
        updateGradientPreview();
    };

    // Apply gradient in real-time (no alert)
    function applyGradientRealtime() {
        if (!window.selectedSvgElement) {
            return; // Silently skip if no part selected
        }

        const mainSvg = getMainSvg();
        if (!mainSvg) return;

        let defs = mainSvg.querySelector('defs');

        if (!defs) {
            defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            mainSvg.insertBefore(defs, mainSvg.firstChild);
        }

        const gradientId = `gradient-${window.currentView}-${window.selectedSvgElement.id}`;

        // Remove old gradient
        const oldGrad = defs.querySelector(`#${gradientId}`);
        if (oldGrad) oldGrad.remove();

        // Create new gradient
        let gradient;

        if (currentGradientType === 'linear') {
            gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
            gradient.setAttribute('id', gradientId);
            const angle = document.getElementById('gradAngle')?.value || 90;

            // Convert angle to x1,y1,x2,y2 coordinates
            const rad = (angle - 90) * Math.PI / 180;
            const x1 = 50 + 50 * Math.cos(rad);
            const y1 = 50 + 50 * Math.sin(rad);
            const x2 = 50 - 50 * Math.cos(rad);
            const y2 = 50 - 50 * Math.sin(rad);

            gradient.setAttribute('x1', x1 + '%');
            gradient.setAttribute('y1', y1 + '%');
            gradient.setAttribute('x2', x2 + '%');
            gradient.setAttribute('y2', y2 + '%');
        } else {
            gradient = document.createElementNS('http://www.w3.org/2000/svg', 'radialGradient');
            gradient.setAttribute('id', gradientId);
            gradient.setAttribute('cx', '50%');
            gradient.setAttribute('cy', '50%');
            gradient.setAttribute('r', '50%');
        }

        // Add stops
        gradientStops.forEach(stop => {
            const stopEl = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
            stopEl.setAttribute('offset', `${stop.position}%`);
            stopEl.setAttribute('stop-color', stop.color);
            gradient.appendChild(stopEl);
        });

        defs.appendChild(gradient);

        // Apply to element
        const fillValue = `url(#${gradientId})`;
        window.selectedSvgElement.setAttribute('fill', fillValue);

        // Store in colorChanges
        if (!window.colorChanges[window.currentView]) {
            window.colorChanges[window.currentView] = {};
        }
        window.colorChanges[window.currentView][window.selectedSvgElement.id] = fillValue;

        window.gradientChanges[window.currentView][window.selectedSvgElement.id] = {
            type: currentGradientType,
            angle: document.getElementById('gradAngle')?.value || 90,
            stops: JSON.parse(JSON.stringify(gradientStops))
        };



        // Update undo/redo
        if (window.updateUndoRedoButtons) {
            window.updateUndoRedoButtons();
        }
    }

    // Legacy function - now just calls real-time version
    window.applyGradient = function () {
        if (!window.selectedSvgElement) {
            alert("Please select a part first!");
            return;
        }

        if (window.saveToHistory) {
            window.saveToHistory();
        }

        applyGradientRealtime();
    };

    window.resetAll = function () {
        if (!window.selectedSvgElement) return;

        const originalColor = window.originalColors[window.currentView]?.[window.selectedSvgElement.id] || '#FFFFFF';
        window.selectedSvgElement.setAttribute('fill', originalColor);

        if (window.colorChanges[window.currentView]) {
            delete window.colorChanges[window.currentView][window.selectedSvgElement.id];
        }


    };

    /* ================= HELPER FUNCTIONS ================= */

    function getContrastColorLocal(hex) {
        try {
            const h = hex.replace('#', '');
            const r = parseInt(h.substr(0, 2), 16);
            const g = parseInt(h.substr(2, 2), 16);
            const b = parseInt(h.substr(4, 2), 16);
            return ((r * 299 + g * 587 + b * 114) / 1000) > 128 ? '#000' : '#fff';
        } catch (e) {
            return '#000';
        }
    }

    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(() => {
            renderGradientStops();
            updateGradientPreview();
            window.extractDefaultColors();
        }, 500);
    });


})();
