(function () {
    'use strict';
console.log('FILE B LOADED');
    /* ================= GLOBAL VARIABLES ================= */
    window.selectedColors = []; // Colors currently shown in wheel
    window.paletteSelectedColors = []; // Colors selected in modal
    window.defaultModelColors = window.defaultModelColors || [];
    window.hasCustomPalette = window.hasCustomPalette || false;

    let currentFillType = 'solid';
    let currentGradientType = 'linear';

    function initGradientFromWheel() {
        if (selectedColors.length >= 2) {
            gradientStops = [
                { color: selectedColors[0], position: 0 },
                { color: selectedColors[1], position: 100 }
            ];
        } else {
            gradientStops = [
                { color: '#000000', position: 0 },
                { color: '#ffffff', position: 100 }
            ];
        }

        renderGradientStops();
        updateGradientPreview();
    }

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

            if (
                fill &&
                fill !== 'none' &&
                fill !== 'transparent' &&
                !fill.startsWith('url(')
            ) {
                colors.add(fill.toUpperCase());
            }
        });

        window.defaultModelColors = Array.from(colors);

        if (window.defaultModelColors.length === 0) {
            window.defaultModelColors = [
                '#FF0000', '#00FF00', '#0000FF', '#FFFF00',
                '#FF00FF', '#00FFFF', '#000000', '#FFFFFF'
            ];
        }

        if (window.defaultModelColors.length > 24) {
            window.defaultModelColors = window.defaultModelColors.slice(0, 24);
        }

        // Only load defaults into wheel if custom palette not applied yet
        if (!window.hasCustomPalette || !window.selectedColors.length) {
            window.selectedColors = [...window.defaultModelColors];
        }

        updateColorWheel();
    };

    /* ================= UPDATE COLOR WHEEL ================= */
    function rotateWheelToAngle(angle) {
        const ring = document.querySelector('#colorWheelRing svg');
        if (!ring) return;

        ring.style.transform = `rotate(${-angle}deg)`;
    }

    function darkenColor(hex, amount = 40) {
        const h = hex.replace('#', '');
        let r = parseInt(h.substr(0, 2), 16);
        let g = parseInt(h.substr(2, 2), 16);
        let b = parseInt(h.substr(4, 2), 16);
        r = Math.max(0, r - amount);
        g = Math.max(0, g - amount);
        b = Math.max(0, b - amount);
        return '#' + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('');
    }

    window.updateColorWheel = function () {
        const wheel = document.getElementById('colorWheelRing');
        if (!wheel || selectedColors.length === 0) return;

        wheel.innerHTML = '';

        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('viewBox', '0 0 340 340');
        svg.setAttribute('width', '340');
        svg.setAttribute('height', '340');
        svg.setAttribute('overflow', 'hidden');
        svg.style.position = 'absolute';
        svg.style.transition = 'transform .6s cubic-bezier(.22,.61,.36,1)';

        const center = 170;
        const radius = 160;
        const activeRadius = 168;
        const innerRadius = 110;
        const angleStep = 360 / selectedColors.length;

        const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        const clipPath = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
        clipPath.setAttribute('id', 'wheelRingClip');
        const clipCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        clipCircle.setAttribute('cx', String(center));
        clipCircle.setAttribute('cy', String(center));
        clipCircle.setAttribute('r', String(activeRadius + 2));
        clipPath.appendChild(clipCircle);
        defs.appendChild(clipPath);
        svg.appendChild(defs);

        const slicesGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        slicesGroup.setAttribute('clip-path', 'url(#wheelRingClip)');
        svg.appendChild(slicesGroup);

        selectedColors.forEach((color, i) => {

            function makeSlicePath(r) {
                const sa = (i * angleStep - 90) * Math.PI / 180;
                const ea = ((i + 1) * angleStep - 90) * Math.PI / 180;
                const x1 = center + r * Math.cos(sa);
                const y1 = center + r * Math.sin(sa);
                const x2 = center + r * Math.cos(ea);
                const y2 = center + r * Math.sin(ea);
                const x3 = center + innerRadius * Math.cos(ea);
                const y3 = center + innerRadius * Math.sin(ea);
                const x4 = center + innerRadius * Math.cos(sa);
                const y4 = center + innerRadius * Math.sin(sa);
                const la = angleStep > 180 ? 1 : 0;
                return `M ${x1},${y1} A ${r},${r} 0 ${la},1 ${x2},${y2} L ${x3},${y3} A ${innerRadius},${innerRadius} 0 ${la},0 ${x4},${y4} Z`;
            }

            const normalD = makeSlicePath(radius);
            const activeD = makeSlicePath(activeRadius);

            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', normalD);
            path.setAttribute('fill', color);
            path.setAttribute('stroke', 'none');
            path.style.cursor = 'pointer';
            path.style.transition = 'd .2s, stroke-width .2s';
            path._color = color;
            path._index = i;
            path._normalD = normalD;
            path._activeD = activeD;

            path.addEventListener('click', function () {
                Array.from(slicesGroup.querySelectorAll('path')).forEach(p => {
                    if (p._normalD) {
                        p.setAttribute('d', p._normalD);
                        p.setAttribute('stroke', 'none');
                        p.setAttribute('stroke-width', '0');
                    }
                });

                this.setAttribute('d', this._activeD);
                this.setAttribute('stroke', 'none');
                this.setAttribute('stroke-width', '0');

                slicesGroup.appendChild(this);

                const deg = -(i * angleStep + angleStep / 2);
                svg.style.transform = `rotate(${deg}deg)`;

                applyColorToPart(color);
                updateCenterColor(color);
            });

            path.addEventListener('mouseenter', function () {
                if (this.getAttribute('stroke') === 'none' || !this.getAttribute('stroke')) {
                    this.style.filter = 'brightness(1.15)';
                }
            });

            path.addEventListener('mouseleave', function () {
                this.style.filter = 'none';
            });

            slicesGroup.appendChild(path);
        });

        wheel.appendChild(svg);

        resizeColorWheelForLandscape();
    };

    function resizeColorWheelForLandscape() {
        const isLandscape = window.innerWidth > window.innerHeight && window.innerHeight < 500;
        const svg = document.querySelector('#colorWheelRing svg');
        if (!svg) return;

        if (isLandscape) {
            const size = 160;

            svg.style.width = size + 'px';
            svg.style.height = size + 'px';
            svg.style.position = 'relative';
            svg.style.top = '0';
            svg.style.left = '0';

            const ring = document.getElementById('colorWheelRing');
            if (ring) {
                ring.style.marginTop = '0';
                ring.style.width = size + 'px';
                ring.style.height = size + 'px';
            }

            const container = document.querySelector('.color-wheel-container');
            if (container) {
                container.style.width = size + 'px';
                container.style.height = size + 'px';
                container.style.margin = '4px auto';
            }

            const outer = document.querySelector('.color-wheel-outer');
            if (outer) {
                outer.style.width = size + 'px';
                outer.style.height = size + 'px';
            }

            const whiteRing = document.querySelector('.color-wheel-white-ring');
            if (whiteRing) {
                whiteRing.style.width = '80px';
                whiteRing.style.height = '80px';
            }

            const center = document.querySelector('.color-wheel-center');
            if (center) {
                center.style.width = '80px';
                center.style.height = '80px';
                center.style.fontSize = '8px';
            }
        } else {
            svg.setAttribute('width', '340');
            svg.setAttribute('height', '340');
            svg.style.width = '';
            svg.style.height = '';

            const ring = document.getElementById('colorWheelRing');
            if (ring) {
                ring.style.marginTop = '';
                ring.style.width = '';
                ring.style.height = '';
            }

            const container = document.querySelector('.color-wheel-container');
            if (container) container.style.cssText = '';

            const outer = document.querySelector('.color-wheel-outer');
            if (outer) outer.style.cssText = '';

            const whiteRing = document.querySelector('.color-wheel-white-ring');
            if (whiteRing) whiteRing.style.cssText = '';

            const center = document.querySelector('.color-wheel-center');
            if (center) center.style.cssText = '';
        }
    }

    window.addEventListener('resize', resizeColorWheelForLandscape);
    window.addEventListener('orientationchange', function () {
        setTimeout(resizeColorWheelForLandscape, 300);
    });

    function updateCenterColor(color) {
        const btn = document.getElementById('selectedColorBtn');
        if (!btn) return;

        btn.style.background = color;

        const hex = color.replace('#', '');
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        const brightness = (r * 299 + g * 587 + b * 114) / 1000;

        if (brightness > 200) {
            btn.style.color = '#000';
            btn.style.textShadow = '0 0 2px #000';
        } else {
            btn.style.color = '#fff';
            btn.style.textShadow = '0 0 4px rgba(0,0,0,.6)';
        }

        const colorObj = window.backendColors?.find(
            c => c.code.toUpperCase() === color.toUpperCase()
        );

        if (colorObj?.name) {
            const shortName = colorObj.name
                .split(' ')
                .map(w => w.charAt(0).toUpperCase())
                .join('');

            btn.innerHTML = `
                <span style="font-size:28px;font-weight:800;line-height:1.2;display:block;">${shortName}</span>
                <span style="font-size:13px;font-weight:600;line-height:1.3;display:block;word-break:break-word;">${colorObj.name}</span>
            `;
        } else {
            btn.innerHTML = 'SELECT<br>COLORS';
        }
    }

    window.highlightWheelColor = function (color) {
        if (!color || color.startsWith('url(')) return;
        const upperColor = color.toUpperCase();
        const index = selectedColors.findIndex(c => c.toUpperCase() === upperColor);
        if (index === -1) return;

        const svg = document.querySelector('#colorWheelRing svg');
        if (!svg) return;

        const angleStep = 360 / selectedColors.length;
        const paths = Array.from(svg.querySelectorAll('path'));

        paths.forEach(p => {
            if (p._normalD) {
                p.setAttribute('d', p._normalD);
                p.setAttribute('stroke', 'none');
            }
        });

        const target = paths[index];
        if (!target) return;

        target.setAttribute('d', target._activeD);
        const darker = darkenColor(target._color, 50);
        target.setAttribute('stroke', darker);
        target.setAttribute('stroke-width', '0');

        const deg = -(index * angleStep + angleStep / 2);
        svg.style.transform = `rotate(${deg}deg)`;

        updateCenterColor(color);
    };

    /* ================= APPLY COLOR TO SELECTED PART ================= */
    function applyColorToPart(color) {
        if (!window.selectedSvgElement) {
            alert("Please select a part first!");
            return;
        }

        const partId = window.selectedSvgElement.id;

        if (window.gradientChanges[currentView]?.[partId]) {
            delete window.gradientChanges[currentView][partId];
        }

        if (window.saveToHistory) window.saveToHistory();

        window.selectedSvgElement.setAttribute('fill', color);

        if (!colorChanges[currentView]) colorChanges[currentView] = {};
        colorChanges[currentView][partId] = color;

        if (!window.colorChanges) window.colorChanges = { front: {}, back: {}, left: {}, right: {} };
        if (!window.colorChanges[currentView]) window.colorChanges[currentView] = {};
        window.colorChanges[currentView][partId] = color;

        console.log('Color saved:', currentView, partId, color);

        const centerBtn = document.getElementById('selectedColorBtn');
        if (centerBtn) centerBtn.style.background = color;

        const upperColor = color.toUpperCase();

        // If custom palette already set, don't auto-insert new random colors into wheel
        if (!window.hasCustomPalette && !selectedColors.includes(upperColor)) {
            selectedColors.push(upperColor);
            if (selectedColors.length > 24) selectedColors.shift();
            updateColorWheel();
        }

        if (window.updateUndoRedoButtons) updateUndoRedoButtons();
    }

    /* ================= COLOR PALETTE MODAL ================= */
window.openColorPalette = function () {
    const panel = document.getElementById('inlineColorSelector');
    const wheelPanel = document.getElementById('colorWheelPanel');
    const gradientPanel = document.getElementById('gradientPanel');

    paletteSelectedColors = window.hasCustomPalette
        ? [...window.selectedColors].map(c => c.toUpperCase())
        : [];

    document.querySelectorAll('#inlineColorSelector .color-box').forEach(box => {
        box.classList.remove('selected');
        box.innerHTML = '';

        const rawBg =
            box.style.background ||
            box.style.backgroundColor ||
            window.getComputedStyle(box).backgroundColor ||
            '';

        const bg = rgbToHex(rawBg);
        if (!bg) return;

        if (paletteSelectedColors.includes(bg)) {
            box.classList.add('selected');
            box.innerHTML = '✔';
            box.style.color = getContrastColorLocal(bg);
            box.style.fontWeight = 'bold';
            box.style.display = 'flex';
            box.style.alignItems = 'center';
            box.style.justifyContent = 'center';
        } else {
            box.style.color = '';
            box.style.fontWeight = '';
            box.style.display = '';
            box.style.alignItems = '';
            box.style.justifyContent = '';
        }
    });

    if (wheelPanel) wheelPanel.style.display = 'none';
    if (gradientPanel) gradientPanel.style.display = 'none';
    if (panel) panel.classList.add('open');
};

window.closeColorPalette = function () {
    const panel = document.getElementById('inlineColorSelector');
    const wheelPanel = document.getElementById('colorWheelPanel');

    if (panel) panel.classList.remove('open');
    if (wheelPanel) wheelPanel.style.display = 'block';
};

function rgbToHex(color) {
    if (!color) return '';

    color = color.trim();

    if (color.startsWith('#')) {
        let hex = color.toUpperCase();

        if (hex.length === 4) {
            hex = '#' + hex[1] + hex[1] + hex[2] + hex[2] + hex[3] + hex[3];
        }

        return hex;
    }

    const match = color.match(/rgba?\s*\(\s*(\d+)[,\s]+(\d+)[,\s]+(\d+)/i);
    if (!match) return '';

    const r = parseInt(match[1], 10).toString(16).padStart(2, '0');
    const g = parseInt(match[2], 10).toString(16).padStart(2, '0');
    const b = parseInt(match[3], 10).toString(16).padStart(2, '0');

    return (`#${r}${g}${b}`).toUpperCase();
}
window.togglePaletteColor = function (el, color) {
    const upperColor = color.toUpperCase();

    if (paletteSelectedColors.includes(upperColor)) {
        paletteSelectedColors = paletteSelectedColors.filter(c => c !== upperColor);
        el.classList.remove('selected');
        el.innerHTML = '';
        el.style.color = '';
        el.style.fontWeight = '';
        el.style.display = '';
        el.style.alignItems = '';
        el.style.justifyContent = '';
    } else {
        paletteSelectedColors.push(upperColor);
        el.classList.add('selected');
        el.innerHTML = '✔';
        el.style.color = getContrastColorLocal(color);
        el.style.fontWeight = 'bold';
        el.style.display = 'flex';
        el.style.alignItems = 'center';
        el.style.justifyContent = 'center';
    }
};

window.applySelectedColors = function () {
    if (paletteSelectedColors.length < 1) {
        alert('Please select at least 1 color');
        return;
    }

    selectedColors = paletteSelectedColors.map(c => c.toUpperCase());
    window.selectedColors = [...selectedColors];
    window.hasCustomPalette = true;

    if (selectedColors.length > 24) {
        selectedColors = selectedColors.slice(0, 24);
        window.selectedColors = [...selectedColors];
    }

    updateColorWheel();
    initGradientFromWheel();
    closeColorPalette();
};

    /* ================= FILL TYPE SWITCHER ================= */
   window.setFillType = function (type) {
    document.getElementById('solidBtn').classList.toggle('active', type === 'solid');
    document.getElementById('gradientBtn').classList.toggle('active', type === 'gradient');

    const gradientPanel = document.getElementById('gradientPanel');
    const solidPanel = document.getElementById('solidPanel');
    const inlineSelector = document.getElementById('inlineColorSelector');
    const wheelPanel = document.getElementById('colorWheelPanel');

    if (type === 'gradient') {
        if (solidPanel) solidPanel.style.display = 'none';
        if (gradientPanel) gradientPanel.style.display = 'block';

        initGradientFromWheel();

        requestAnimationFrame(() => {
            const angle = parseInt(document.getElementById('gradAngle')?.value || 90, 10);
            updateAngleWheelKnob(angle);
            updateGradientPreview();
        });
    } else {
        if (gradientPanel) gradientPanel.style.display = 'none';
        if (solidPanel) solidPanel.style.display = 'block';
        if (inlineSelector) inlineSelector.classList.remove('open');
        if (wheelPanel) wheelPanel.style.display = 'block';
    }
};

    /* ================= GRADIENT FUNCTIONS ================= */
    window.setGradientType = function (type) {
        currentGradientType = type;

        const linearBtn = document.getElementById('linearBtn');
        const radialBtn = document.getElementById('radialBtn');

        if (linearBtn) linearBtn.classList.toggle('active', type === 'linear');
        if (radialBtn) radialBtn.classList.toggle('active', type === 'radial');

        const angleControls = document.getElementById('angleControls');
        if (angleControls) {
            angleControls.style.display = type === 'linear' ? 'block' : 'none';
        }

        updateGradientPreview();
        applyGradientRealtime();
    };

    window.updateGradientAngle = function (angle) {
        let finalAngle = parseInt(angle, 10);
        if (isNaN(finalAngle)) finalAngle = 0;
        finalAngle = Math.max(0, Math.min(360, finalAngle));

        const input = document.getElementById('gradAngle');
        const display = document.getElementById('angleDisplay');

        if (input) input.value = finalAngle;
        if (display) display.innerText = finalAngle + '°';

        updateAngleWheelKnob(finalAngle);
        updateGradientPreview();

        if (window.selectedSvgElement) {
            if (!gradientChanges[currentView][selectedSvgElement.id]) {
                gradientChanges[currentView][selectedSvgElement.id] = {
                    type: currentGradientType,
                    angle: finalAngle,
                    stops: JSON.parse(JSON.stringify(gradientStops))
                };
            }

            gradientChanges[currentView][selectedSvgElement.id].angle = finalAngle;
            applyGradientRealtime();
        }
    };

    function updateAngleWheelKnob(angle) {
        const wheel = document.getElementById('angleWheel');
        const knob = document.getElementById('angleWheelKnob');
        if (!wheel || !knob) return;

        const rect = wheel.getBoundingClientRect();
        const size = rect.width;
        if (!size) return;

        const center = size / 2;
        const knobSize = knob.offsetWidth || 16;
        const radius = center - (knobSize / 2) - 1;

        const rad = (angle - 90) * Math.PI / 180;
        const x = center + radius * Math.cos(rad);
        const y = center + radius * Math.sin(rad);

        knob.style.left = x + 'px';
        knob.style.top = y + 'px';
        knob.style.transform = 'translate(-50%, -50%)';
    }

    function initAngleWheel() {
        const wheel = document.getElementById('angleWheel');
        const input = document.getElementById('gradAngle');
        if (!wheel) return;

        let isDragging = false;

        function setAngleFromPointer(clientX, clientY) {
            const rect = wheel.getBoundingClientRect();
            const cx = rect.left + rect.width / 2;
            const cy = rect.top + rect.height / 2;

            const dx = clientX - cx;
            const dy = clientY - cy;

            let angle = Math.atan2(dy, dx) * 180 / Math.PI + 90;
            if (angle < 0) angle += 360;

            updateGradientAngle(Math.round(angle));
        }

        if (input) {
            ['mousedown', 'click', 'pointerdown', 'touchstart'].forEach(evt => {
                input.addEventListener(evt, function (e) {
                    e.stopPropagation();
                });
            });

            input.addEventListener('input', function (e) {
                e.stopPropagation();
                updateGradientAngle(this.value);
            });

            input.addEventListener('change', function (e) {
                e.stopPropagation();
                updateGradientAngle(this.value);
            });
        }

        wheel.addEventListener('mousedown', function (e) {
            if (e.target === input) return;
            isDragging = true;
            setAngleFromPointer(e.clientX, e.clientY);
        });

        document.addEventListener('mousemove', function (e) {
            if (!isDragging) return;
            setAngleFromPointer(e.clientX, e.clientY);
        });

        document.addEventListener('mouseup', function () {
            isDragging = false;
        });

        wheel.addEventListener('click', function (e) {
            if (e.target === input) return;
            setAngleFromPointer(e.clientX, e.clientY);
        });

        updateAngleWheelKnob(parseInt(input?.value || 0, 10));
    }

    window.addGradientStop = function () {
        if (!window.selectedSvgElement) {
            alert('Select a part first');
            return;
        }

        const usedColors = gradientStops.map(s => s.color.toUpperCase());
        let nextColor = '#000000';

        if (selectedColors && selectedColors.length > 0) {
            const unused = selectedColors.find(c => !usedColors.includes(c.toUpperCase()));
            nextColor = unused || selectedColors[gradientStops.length % selectedColors.length];
        }

        gradientStops.push({
            color: nextColor,
            position: 50
        });

        normalizeStops();

        if (!window.gradientChanges[window.currentView]) window.gradientChanges[window.currentView] = {};
        window.gradientChanges[window.currentView][window.selectedSvgElement.id] = {
            type: currentGradientType,
            angle: Number(document.getElementById('gradAngle')?.value || 90),
            stops: JSON.parse(JSON.stringify(gradientStops))
        };

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

    window.removeGradientStop = function (index) {
        if (gradientStops.length <= 2) {
            alert("Need at least 2 color stops!");
            return;
        }
        gradientStops.splice(index, 1);
        renderGradientStops();
        updateGradientPreview();
        applyGradientRealtime();
    };
window.renderGradientStops = function () {
    const container = document.getElementById('gradientStopsContainer');
    if (!container) return;

    container.innerHTML = '';
    gradientStops.sort((a, b) => a.position - b.position);

    gradientStops.forEach((stop, index) => {
        const isSelected = (index === window.currentGradientStopIndex);
        const isManualColor = !isColorUsedInWheel(stop.color);

        const stopDiv = document.createElement('div');
        stopDiv.className = 'gradient-stop-item' + (isSelected ? ' active' : '');
        stopDiv.dataset.index = index;

        // Single compact row
        const row = document.createElement('div');
        row.className = 'gradient-stop-single-row';

        // Manual color box (same as sketch left side)
        const manualBox = document.createElement('div');
        manualBox.className = 'manual-stop-color-box compact';
        manualBox.style.background = stop.color;
        manualBox.title = 'Manual color';
        manualBox.onclick = () => pickManualStopColor(index);

        if (isManualColor) {
            manualBox.innerHTML = `<span style="color:${getContrastColorLocal(stop.color)};font-size:11px;font-weight:700;">M</span>`;
        } else {
            manualBox.innerHTML = '';
        }

        row.appendChild(manualBox);

        // Wheel colors in same row
        const swatchRow = document.createElement('div');
        swatchRow.className = 'stop-wheel-swatch-row compact';

        (selectedColors || []).forEach((wColor) => {
            const swatch = document.createElement('div');
            const isActive = wColor.toUpperCase() === stop.color.toUpperCase();

            swatch.className = 'wheel-stop-swatch compact';
            swatch.style.background = wColor;
            swatch.style.color = getContrastColorLocal(wColor);
            swatch.style.border = isActive ? '2px solid #000' : '1px solid #bbb';

            if (isActive) swatch.innerHTML = '✓';

            swatch.title = wColor;

            swatch.onclick = () => {
                gradientStops[index].color = wColor.toUpperCase();
                window.currentGradientStopIndex = index;
                renderGradientStops();
                updateGradientPreview();
                applyGradientRealtime();
            };

            swatchRow.appendChild(swatch);
        });

        row.appendChild(swatchRow);

        // Position
        const posInput = document.createElement('input');
        posInput.type = 'number';
        posInput.min = 0;
        posInput.max = 100;
        posInput.value = stop.position;
        posInput.className = 'stop-position-input compact';
        posInput.title = 'Position %';
        posInput.onchange = () => updateStopPositionInput(index, posInput.value);

        const pctLabel = document.createElement('span');
        pctLabel.textContent = '%';
        pctLabel.className = 'pct-label';

        row.appendChild(posInput);
        row.appendChild(pctLabel);

        // Remove button
        const removeBtn = document.createElement('button');
        removeBtn.className = 'remove-stop-btn compact';
        removeBtn.title = 'Remove';
        removeBtn.innerHTML = '×';

        if (gradientStops.length > 2) {
            removeBtn.onclick = () => removeGradientStop(index);
        } else {
            removeBtn.style.visibility = 'hidden';
        }

        row.appendChild(removeBtn);

        stopDiv.appendChild(row);
        container.appendChild(stopDiv);
    });

    updateStopMarkers();
};

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
        renderGradientStops();
    };

    window.updateStopPositionInput = function (index, value) {
        let pos = parseInt(value);
        pos = Math.max(0, Math.min(100, pos));

        gradientStops[index].position = pos;
        renderGradientStops();
        updateGradientPreview();
        applyGradientRealtime();
    };

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
    };

    window.updateGradient = function () {
        updateGradientPreview();
    };

    function applyGradientRealtime() {
        if (!window.selectedSvgElement) return;

        const mainSvg = getMainSvg();
        if (!mainSvg) return;

        let defs = mainSvg.querySelector('defs');

        if (!defs) {
            defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            mainSvg.insertBefore(defs, mainSvg.firstChild);
        }

        const gradientId = `gradient-${window.currentView}-${window.selectedSvgElement.id}`;

        const oldGrad = defs.querySelector(`#${gradientId}`);
        if (oldGrad) oldGrad.remove();

        let gradient;

        if (currentGradientType === 'linear') {
            gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
            gradient.setAttribute('id', gradientId);
            const angle = document.getElementById('gradAngle')?.value || 90;

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

        gradientStops.forEach(stop => {
            const stopEl = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
            stopEl.setAttribute('offset', `${stop.position}%`);
            stopEl.setAttribute('stop-color', stop.color);
            gradient.appendChild(stopEl);
        });

        defs.appendChild(gradient);

        const fillValue = `url(#${gradientId})`;
        window.selectedSvgElement.setAttribute('fill', fillValue);

        if (!window.colorChanges[window.currentView]) {
            window.colorChanges[window.currentView] = {};
        }
        window.colorChanges[window.currentView][window.selectedSvgElement.id] = fillValue;

        window.gradientChanges[window.currentView][window.selectedSvgElement.id] = {
            type: currentGradientType,
            angle: document.getElementById('gradAngle')?.value || 90,
            stops: JSON.parse(JSON.stringify(gradientStops))
        };

        if (window.updateUndoRedoButtons) {
            window.updateUndoRedoButtons();
        }
    }

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

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(() => {
            renderGradientStops();
            updateGradientPreview();
            window.extractDefaultColors();
            initAngleWheel();
            updateAngleWheelKnob(parseInt(document.getElementById('gradAngle')?.value || 90, 10));
        }, 500);
    });

    function isColorUsedInWheel(color) {
    return (selectedColors || []).some(c => c.toUpperCase() === color.toUpperCase());
}

window.pickManualStopColor = function (index) {
    window.currentGradientStopIndex = index;

    const currentColor = gradientStops[index]?.color || '#000000';
    const picked = prompt('Enter manual color code (example: #FF6600)', currentColor);

    if (!picked) return;

    const color = picked.trim().toUpperCase();

    if (!/^#([0-9A-F]{3}|[0-9A-F]{6})$/i.test(color)) {
        alert('Please enter valid hex color like #FF6600');
        return;
    }

    gradientStops[index].color = color;
    renderGradientStops();
    updateGradientPreview();
    applyGradientRealtime();
};


})();
