let backendTargetHex = null;
let fabricCanvas = null;
let hiddenCanvas = null;
let hiddenCtx = null;
let layers = [];
let layerCounter = 0;
let currentImage = null;
let originalImageData = null;
let detectedColors = [];
let colorMappings = {};
let selectedColorsForVector = [];
let selectedColorCount = 0;
let colorChangeTimeout = null;
let isProcessing = false;
let isApplyingColorChange = false;
let isRestoringFromUndo = false;
let isReorderingLayers = false;
let eraserMode = false;
let eraserRadius = 30;
let removedPixels = new Map();
let removedRegions = new Map();
let currentImageDataBackup = null;
let eyeDropperMode = false;
let eyeDropperTargetColor = null;
let eyeDropperAddMode = false;
let contextMenuClipboard = null;
var undoHistory = [];
var redoHistory = [];
var maxUndoSteps = 50;
var rightSidebarView = 'layers'; // 'layers' = show layers list, 'object' = show text/object settings
var pendingUploadDataUrl = null; // after upload, store data URL until user clicks Done in modal
var uploadModalFinalDataUrl = null; // real-time edited image; used when Done is clicked
var uploadModalCanvas = null;
var uploadModalCtx = null;
var savedForModification = false;
var uploadModalErasedPixels = new Set();
var uploadModalEraserMode = false;
var uploadModalEraserRadius = 30;

const COLOR_MATCH_TOLERANCE = 10;
const COLOR_TOLERANCE = 30;
const MAX_PROCESSING_SIZE = 800;
const CENTER_GUIDE_TOLERANCE = 12;

// Initialize application
window.onload = function() {
    initializeCanvas();
    setupEventListeners();
};

function initializeCanvas() {
    fabricCanvas = new fabric.Canvas('designCanvas', {
        width: 1000,
        height: 600,
        backgroundColor: 'white',
        selection: true,
        preserveObjectStacking: true,
        perPixelTargetFind: true,      // 🔥 Changed to true
        targetFindTolerance: 10,       // 🔥 Increased
        stopContextMenu: true          // 🔥 Add this
    });

    // 🔥 CRITICAL - Override Fabric's findTarget method
    var originalFindTarget = fabricCanvas.findTarget;
    fabricCanvas.findTarget = function(e, skipGroup) {
        var target = originalFindTarget.call(this, e, skipGroup);

        // Agar target kisi group ka child hai, to parent group return karo
        if (target && target.group && target.group.type === 'group') {
            return target.group;
        }

        return target;
    };

    // 🔥 Disable sub-target selection on all mouse events
    fabricCanvas.on('mouse:down:before', function(opt) {
        var target = fabricCanvas.findTarget(opt.e);
        if (target && target.type === 'group') {
            target.subTargetCheck = false;
            target.__corner = 0;
        }
    });

    fabricCanvas.on('mouse:move:before', function(opt) {
        var target = fabricCanvas.findTarget(opt.e);
        if (target && target.type === 'group') {
            target.subTargetCheck = false;
        }
    });

    hiddenCanvas = document.getElementById('hiddenCanvasMascot');
    if (hiddenCanvas) {
        hiddenCtx = hiddenCanvas.getContext('2d');
    }

    fabricCanvas.on('object:added', function(opt) {
        var obj = opt.target;
        if (obj && obj.type === 'group') {
            obj.subTargetCheck = false;
            obj.interactive = false;
        }
        updateLayers();
    });

    fabricCanvas.on('object:removed', updateLayers);

    fabricCanvas.on('selection:created', function(opt) {
        if (opt.selected && opt.selected.length === 1) {
            const obj = opt.selected[0];
            if (obj.type === 'group') {
                obj.subTargetCheck = false;
                obj.interactive = false;
            }
        }
        updateLayerSelection();
        updateLayerControlsBar();
    });

    fabricCanvas.on('selection:updated', function(opt) {
        if (opt.selected && opt.selected.length === 1) {
            const obj = opt.selected[0];
            if (obj.type === 'group') {
                obj.subTargetCheck = false;
                obj.interactive = false;
            }
        }
        updateLayerSelection();
        updateLayerControlsBar();
    });

    fabricCanvas.on('selection:cleared', function() {
        updateLayerSelection();
        hideLayerControlsBar();
        hideCenterGuide();
    });

    fabricCanvas.on('object:moving', function(opt) {
        if (!savedForModification) {
            saveStateForUndo();
            savedForModification = true;
        }
        updateLayerControlsBar();
        updateCenterGuide(opt.target);
    });

    fabricCanvas.on('object:scaling', function(opt) {
        if (!savedForModification) {
            saveStateForUndo();
            savedForModification = true;
        }
        updateLayerControlsBar();
        updateCenterGuide(opt.target);
    });

    fabricCanvas.on('object:rotating', function(opt) {
        if (!savedForModification) {
            saveStateForUndo();
            savedForModification = true;
        }
        updateLayerControlsBar();
        updateCenterGuide(opt.target);
    });

    fabricCanvas.on('object:modified', function() {
        savedForModification = false;
        hideCenterGuide();
    });

    fabricCanvas.on('mouse:down', handleCanvasClick);

    fabricCanvas.on('mouse:down', function(opt) {
        document.getElementById('contextMenu').style.display = 'none';
    });

    fabricCanvas.on('mouse:dblclick', function(opt) {
        const target = opt.target;
        if (!target) return;

        // 🔥 Group double-click - edit mode
        if (target.type === 'group' && (target.textContent || target.textShape)) {
            fabricCanvas.setActiveObject(target);
            updateTextSettingsPanel();
            const textContentEl = document.getElementById('textContent');
            if (textContentEl) {
                textContentEl.focus();
                textContentEl.select();
            }
            return;
        }

        if (target.type === 'i-text' || target.type === 'itext') {
            target.enterEditing();
            target.selectAll();
            fabricCanvas.renderAll();
        } else if (target.type === 'text') {
            const text = target;
            const IText = fabric.IText || fabric.FabricIText;
            if (IText) {
                const iText = new IText(text.text, {
                    left: text.left,
                    top: text.top,
                    fontSize: text.fontSize,
                    fontFamily: text.fontFamily || 'Arial',
                    fill: text.fill,
                    fontWeight: text.fontWeight,
                    fontStyle: text.fontStyle,
                    stroke: text.stroke,
                    strokeWidth: text.strokeWidth || 0,
                    paintFirst: text.paintFirst || 'fill',
                    lineHeight: text.lineHeight,
                    charSpacing: text.charSpacing || 0,
                    textAlign: text.textAlign,
                    name: text.name,
                    selectable: true,
                    hasControls: true,
                    hasBorders: true
                });
                const objects = fabricCanvas.getObjects();
                const idx = objects.indexOf(text);
                fabricCanvas.remove(text);
                fabricCanvas.insertAt(idx, iText);
                fabricCanvas.setActiveObject(iText);
                iText.enterEditing();
                iText.selectAll();
                fabricCanvas.renderAll();
                updateLayers();
            }
        }
    });
}
function setupEventListeners() {
    const imageInput = document.getElementById('imageInput');
    if (imageInput) imageInput.addEventListener('change', handleImageUpload);
    bindUploadModal();

    var wrapper = document.querySelector('.canvas-wrapper');
    var menu = document.getElementById('contextMenu');
    if (wrapper && menu) {
        wrapper.addEventListener('contextmenu', function(ev) {
            ev.preventDefault();
            var target = fabricCanvas.findTarget(ev);
            if (!target && !contextMenuClipboard) return;
            if (target) {
                fabricCanvas.setActiveObject(target);
                fabricCanvas.renderAll();
                updateLayerControlsBar();
                updateLayerSelection();
            }
            menu.style.left = ev.clientX + 'px';
            menu.style.top = ev.clientY + 'px';
            menu.style.display = 'block';
            menu.querySelector('[data-action="paste"]').style.display = contextMenuClipboard ? 'block' : 'none';
        });
    }
    document.addEventListener('click', function() {
        if (menu) menu.style.display = 'none';
    });
    if (menu) {
        menu.querySelectorAll('button[data-action]').forEach(function(btn) {
            btn.addEventListener('click', function(ev) {
                ev.stopPropagation();
                var action = btn.getAttribute('data-action');
                runContextMenuAction(action);
                menu.style.display = 'none';
            });
        });
    }
}

function runContextMenuAction(action) {
    var active = fabricCanvas.getActiveObject();
    if (action === 'paste') {
        if (contextMenuClipboard) {
            saveStateForUndo();
            contextMenuClipboard.clone(function(cloned) {
                cloned.set({ left: (cloned.left || 0) + 20, top: (cloned.top || 0) + 20 });
                fabricCanvas.add(cloned);
                fabricCanvas.setActiveObject(cloned);
                fabricCanvas.renderAll();
                updateLayers();
            });
        }
        return;
    }
    if (!active) return;
    if (action === 'copy') {
        active.clone(function(c) { contextMenuClipboard = c; });
        return;
    }
    if (action === 'cut') {
        saveStateForUndo();
        active.clone(function(c) {
            contextMenuClipboard = c;
            fabricCanvas.remove(active);
            if (active === currentImage) currentImage = null;
            fabricCanvas.renderAll();
            updateLayers();
        });
        return;
    }
    if (action === 'flipH') {
        saveStateForUndo();
        active.set('flipX', !active.flipX);
        fabricCanvas.renderAll();
        return;
    }
    if (action === 'flipV') {
        saveStateForUndo();
        active.set('flipY', !active.flipY);
        fabricCanvas.renderAll();
        return;
    }
    var objects = fabricCanvas.getObjects();
    var idx = objects.indexOf(active);
    if (idx === -1) return;
    saveStateForUndo();
    if (action === 'bringToFront') {
        fabricCanvas.remove(active);
        fabricCanvas.add(active);
    } else if (action === 'sendToBack') {
        fabricCanvas.remove(active);
        if (fabricCanvas.insertAt) fabricCanvas.insertAt(0, active);
        else { var objs = fabricCanvas.getObjects().slice(); objs.unshift(active); objs.forEach(function(o){ fabricCanvas.remove(o); }); objs.forEach(function(o){ fabricCanvas.add(o); }); }
    } else if (action === 'bringForward' && idx < objects.length - 1) {
        fabricCanvas.remove(active);
        if (fabricCanvas.insertAt) fabricCanvas.insertAt(idx + 1, active);
        else { var objs = fabricCanvas.getObjects().slice(); objs.splice(idx, 1); objs.splice(idx + 1, 0, active); objs.forEach(function(o){ fabricCanvas.remove(o); }); objs.forEach(function(o){ fabricCanvas.add(o); }); }
    } else if (action === 'sendBackward' && idx > 0) {
        fabricCanvas.remove(active);
        if (fabricCanvas.insertAt) fabricCanvas.insertAt(idx - 1, active);
        else { var objs = fabricCanvas.getObjects().slice(); objs.splice(idx, 1); objs.splice(idx - 1, 0, active); objs.forEach(function(o){ fabricCanvas.remove(o); }); objs.forEach(function(o){ fabricCanvas.add(o); }); }
    }
    fabricCanvas.renderAll();
    updateLayers();
}

function handleImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('Please select a valid image file.');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        var dataUrl = e.target.result;
        var img = new Image();
        img.onload = function() {
            detectColors(img);
            pendingUploadDataUrl = dataUrl;
            uploadModalFinalDataUrl = dataUrl;
            var countEl = document.getElementById('uploadModalColorCount');
            if (countEl) countEl.innerHTML = 'Detected: <strong>' + (detectedColors.length) + '</strong> colors.';
            var origImg = document.getElementById('uploadModalOriginal');
            var previewImg = document.getElementById('uploadModalPreview');
            if (origImg) origImg.src = dataUrl;
            if (previewImg) previewImg.src = dataUrl;
            buildUploadModalColorGrid();
            updateUploadModalPreview();
            uploadModalErasedPixels = new Set();
            uploadModalEraserMode = false;
            var eraserBtn = document.getElementById('uploadModalEraserBtn');
            var sizeWrap = document.getElementById('uploadModalEraserSizeWrap');
            var previewWrap = document.getElementById('uploadModalPreviewWrap');
            if (eraserBtn) eraserBtn.classList.remove('active');
            if (sizeWrap) sizeWrap.style.display = 'none';
            if (previewWrap) previewWrap.classList.remove('eraser-active');
            var modalBtns = document.querySelectorAll('#uploadModalColorCountButtons .color-count-btn-small');
            var autoCount = detectedColors.length >= 8 ? 8 : Math.max(1, detectedColors.length);
            selectColorCount(autoCount, modalBtns[autoCount - 1] || null);
            var modal = document.getElementById('uploadImageModal');
            if (modal) modal.style.display = 'flex';
        };
        img.src = dataUrl;
    };
    reader.readAsDataURL(file);
    event.target.value = '';
}

function buildUploadModalColorGrid() {

    var grid = document.getElementById('uploadModalColorsGrid');
    if (!grid) return;

    grid.innerHTML = '';

    var colorsToShow = selectedColorCount === 8
        ? Math.min(20, detectedColors.length)
        : Math.min(selectedColorCount, detectedColors.length);

    var colorsForGrid = detectedColors.slice(0, colorsToShow);
    selectedColorsForVector = colorsForGrid.map(c => c.hex);

    colorsForGrid.forEach(function(color) {

        var colorHex = color.hex;

        var item = document.createElement('div');
        item.style.display = 'inline-block';
        item.style.margin = '5px';

        // 🔥 COLOR BOX
        var box = document.createElement('div');
        box.style.width = '40px';
        box.style.height = '40px';
box.style.background = colorMappings[colorHex] || colorHex;
        box.style.border = '1px solid #000';
        box.style.cursor = 'pointer';
        box.style.borderRadius = '6px';

        // 🔥 CLICK → backend modal
        box.addEventListener('click', function() {
            openBackendColorModal(colorHex);
        });

        item.appendChild(box);
        grid.appendChild(item);
    });
}


function updateUploadModalPreview() {
    if (!pendingUploadDataUrl || !detectedColors.length) return;
    var previewImg = document.getElementById('uploadModalPreview');
    if (!previewImg) return;
    if (!uploadModalCanvas) {
        uploadModalCanvas = document.createElement('canvas');
        uploadModalCtx = uploadModalCanvas.getContext('2d');
    }
    var img = new Image();
    img.onload = function() {
        var w = img.width;
        var h = img.height;
        if (w > MAX_PROCESSING_SIZE || h > MAX_PROCESSING_SIZE) {
            var scale = Math.min(MAX_PROCESSING_SIZE / w, MAX_PROCESSING_SIZE / h);
            w = Math.floor(w * scale);
            h = Math.floor(h * scale);
        }
        uploadModalCanvas.width = w;
        uploadModalCanvas.height = h;
        uploadModalCtx.drawImage(img, 0, 0, w, h);
        var imageData = uploadModalCtx.getImageData(0, 0, w, h);
        var data = imageData.data;
        var selectedSet = (selectedColorsForVector && selectedColorsForVector.length) ? new Set(selectedColorsForVector) : new Set(detectedColors.map(function(c){ return c.hex; }));
        var colorMap = new Map();
        detectedColors.forEach(function(color) {
            if (!selectedSet.has(color.hex)) return;
            var newHex = colorMappings[color.hex] || color.hex;
            var rgb = hexToRgb(newHex);
            if (rgb && color.originalKeys) {
                color.originalKeys.forEach(function(key) {
                    colorMap.set(key, { r: rgb.r, g: rgb.g, b: rgb.b });
                });
            }
        });
        var COLOR_TOL = 40;
        var targetColors = [];
        selectedSet.forEach(function(hex) {
            var rgb = hexToRgb(colorMappings[hex] || hex);
            if (rgb) targetColors.push(rgb);
        });
        for (var i = 0; i < data.length; i += 4) {
            var r = data[i], g = data[i + 1], b = data[i + 2], a = data[i + 3];
            if (a < 128) {
                data[i] = 0;
                data[i + 1] = 0;
                data[i + 2] = 0;
                data[i + 3] = 0;
                continue;
            }
            var key = r + ',' + g + ',' + b;
            var nc = null;
            if (colorMap.has(key)) {
                nc = colorMap.get(key);
            } else {
                for (var mapKey of colorMap.keys()) {
                    var ncc = colorMap.get(mapKey);
                    var parts = mapKey.split(',');
                    var or = parseInt(parts[0], 10), og = parseInt(parts[1], 10), ob = parseInt(parts[2], 10);
                    var dist = Math.sqrt(Math.pow(r - or, 2) + Math.pow(g - og, 2) + Math.pow(b - ob, 2));
                    if (dist <= COLOR_TOL) { nc = ncc; break; }
                }
            }
            if (!nc && targetColors.length > 0) {
                var bestIdx = 0, minDist = 1e9;
                for (var t = 0; t < targetColors.length; t++) {
                    var tr = targetColors[t].r, tg = targetColors[t].g, tb = targetColors[t].b;
                    var d = Math.sqrt(Math.pow(r - tr, 2) + Math.pow(g - tg, 2) + Math.pow(b - tb, 2));
                    if (d < minDist) { minDist = d; bestIdx = t; }
                }
                var darkSum = r + g + b;
                if (darkSum < 45 && minDist > 80) {
                    data[i] = 0;
                    data[i + 1] = 0;
                    data[i + 2] = 0;
                    data[i + 3] = 0;
                } else {
                    nc = targetColors[bestIdx];
                }
            }
            if (!nc && targetColors.length > 0) nc = targetColors[0];
            if (nc) {
                data[i] = nc.r;
                data[i + 1] = nc.g;
                data[i + 2] = nc.b;
            }
        }
        uploadModalCtx.putImageData(imageData, 0, 0);
        uploadModalErasedPixels.forEach(function(key) {
            var parts = key.split(',');
            var px = parseInt(parts[0], 10);
            var py = parseInt(parts[1], 10);
            if (px >= 0 && px < w && py >= 0 && py < h) {
                var idx = (py * w + px) * 4;
                imageData.data[idx + 3] = 0;
            }
        });
        if (uploadModalErasedPixels.size) {
            uploadModalCtx.putImageData(imageData, 0, 0);
        }
        var finalUrl = uploadModalCanvas.toDataURL('image/png');
        uploadModalFinalDataUrl = finalUrl;
        previewImg.src = finalUrl;
    };
    img.src = pendingUploadDataUrl;
}

function closeUploadModule() {
    var modal = document.getElementById('uploadImageModal');
    if (modal) modal.style.display = 'none';
    pendingUploadDataUrl = null;
    uploadModalFinalDataUrl = null;
    uploadModalErasedPixels = new Set();
    uploadModalEraserMode = false;
    var eraserBtn = document.getElementById('uploadModalEraserBtn');
    var sizeWrap = document.getElementById('uploadModalEraserSizeWrap');
    var previewWrap = document.getElementById('uploadModalPreviewWrap');
    if (eraserBtn) eraserBtn.classList.remove('active');
    if (sizeWrap) sizeWrap.style.display = 'none';
    if (previewWrap) previewWrap.classList.remove('eraser-active');
}

function addImageToCanvasFromPending() {
    var dataUrl = null;
    if (uploadModalCanvas && uploadModalCanvas.width > 0) {
        dataUrl = uploadModalCanvas.toDataURL('image/png');
    } else {
        dataUrl = uploadModalFinalDataUrl || pendingUploadDataUrl;
    }
    if (!dataUrl) return;
    originalImageData = dataUrl;
    showLoading('Convert in SVG...');
    addImageAsSVGToCanvas(dataUrl);
}

function addImageAsSVGToCanvas(dataUrl) {
    var isSvg = (dataUrl || '').indexOf('svg') !== -1;
    if (isSvg) {
        fabric.loadSVGFromURL(dataUrl).then(function(result) {
            var objects = result && result.objects;
            var options = result && result.options;
            if (!objects || objects.length === 0) {
                addImageAsRasterFallback(dataUrl);
                return;
            }
            var svgGroup = fabric.util.groupSVGElements(objects, options);
            placeAndAddToCanvas(svgGroup, true);
        }).catch(function() { addImageAsRasterFallback(dataUrl); });
        return;
    }
    var img = new Image();
    img.crossOrigin = 'anonymous';
    img.onload = function() {
        try {
            if (typeof ImageTracer !== 'undefined' && ImageTracer.imagedataToSVG) {
                var w = img.width, h = img.height;
                if (w > MAX_PROCESSING_SIZE || h > MAX_PROCESSING_SIZE) {
                    var scale = Math.min(MAX_PROCESSING_SIZE / w, MAX_PROCESSING_SIZE / h);
                    w = Math.floor(w * scale);
                    h = Math.floor(h * scale);
                }
                var tmpCanvas = document.createElement('canvas');
                tmpCanvas.width = w;
                tmpCanvas.height = h;
                var tmpCtx = tmpCanvas.getContext('2d');
                tmpCtx.drawImage(img, 0, 0, w, h);
                var imagedata = tmpCtx.getImageData(0, 0, w, h);
                var opts = { scale: 1, roundcoord: 1, lcpr: 0, qcpr: 0 };
                var svgstr = ImageTracer.imagedataToSVG(imagedata, opts);
                if (svgstr) {
                    fabric.loadSVGFromString(svgstr).then(function(result) {
                        var objects = result && result.objects;
                        var options = result && result.options;
                        if (!objects || objects.length === 0) {
                            addImageAsRasterFallback(dataUrl);
                            return;
                        }
                        var svgGroup = fabric.util.groupSVGElements(objects, options);
                        placeAndAddToCanvas(svgGroup, true);
                    }).catch(function() { addImageAsRasterFallback(dataUrl); });
                } else {
                    addImageAsRasterFallback(dataUrl);
                }
            } else {
                addImageAsRasterFallback(dataUrl);
            }
        } catch (e) {
            addImageAsRasterFallback(dataUrl);
        }
    };
    img.onerror = function() { hideLoading(); addImageAsRasterFallback(dataUrl); };
    img.src = dataUrl;
}






function addImageAsRasterFallback(dataUrl) {
    fabric.Image.fromURL(dataUrl, function(fabricImg) {
        placeAndAddToCanvas(fabricImg, false);
    });
}

function placeAndAddToCanvas(obj, isSvg) {
    var maxWidth = fabricCanvas.width - 40;
    var maxHeight = fabricCanvas.height - 40;
    var w = obj.width || (obj.get && obj.get('width')) || 100;
    var h = obj.height || (obj.get && obj.get('height')) || 100;
    var scale = Math.min(maxWidth / w, maxHeight / h, 1);
    obj.scale(scale);
    obj.set({
        left: (fabricCanvas.width - w * scale) / 2,
        top: (fabricCanvas.height - h * scale) / 2,
        selectable: true,
        hasControls: true,
        hasBorders: true,
        lockRotation: false,
        lockScalingFlip: false
    });
    obj.name = (isSvg ? 'Vector ' : 'Image ') + (++layerCounter);
    currentImage = obj;
    fabricCanvas.add(obj);
    fabricCanvas.setActiveObject(obj);
    fabricCanvas.renderAll();
    updateLayers();
    var modal = document.getElementById('uploadImageModal');
    if (modal) modal.style.display = 'none';
    pendingUploadDataUrl = null;
    uploadModalFinalDataUrl = null;
    hideLoading();
}

function bindUploadModal() {
    var done = document.getElementById('uploadModalDone');
    var previewWrap = document.getElementById('uploadModalPreviewWrap');
    if (done) done.addEventListener('click', addImageToCanvasFromPending);
    if (previewWrap) {
        previewWrap.addEventListener('click', function(ev) {
            if (!uploadModalEraserMode || !uploadModalCanvas || !uploadModalCtx) return;
            var previewImg = document.getElementById('uploadModalPreview');
            if (!previewImg || !previewImg.src || !previewImg.complete) return;
            var rect = previewImg.getBoundingClientRect();
            var natW = previewImg.naturalWidth || uploadModalCanvas.width;
            var natH = previewImg.naturalHeight || uploadModalCanvas.height;
            var scale = Math.min(rect.width / natW, rect.height / natH);
            var renderedW = natW * scale;
            var renderedH = natH * scale;
            var offsetX = (rect.width - renderedW) / 2;
            var offsetY = (rect.height - renderedH) / 2;
            var clickX = ev.clientX - rect.left - offsetX;
            var clickY = ev.clientY - rect.top - offsetY;
            if (clickX < 0 || clickX >= renderedW || clickY < 0 || clickY >= renderedH) return;
            var pixelX = Math.floor((clickX / renderedW) * uploadModalCanvas.width);
            var pixelY = Math.floor((clickY / renderedH) * uploadModalCanvas.height);
            if (pixelX >= 0 && pixelX < uploadModalCanvas.width && pixelY >= 0 && pixelY < uploadModalCanvas.height) {
                applyUploadModalEraserAt(pixelX, pixelY);
            }
        });
    }
}

function toggleUploadModalEraser() {
    uploadModalEraserMode = !uploadModalEraserMode;
    var btn = document.getElementById('uploadModalEraserBtn');
    var sizeWrap = document.getElementById('uploadModalEraserSizeWrap');
    var previewWrap = document.getElementById('uploadModalPreviewWrap');
    if (uploadModalEraserMode) {
        if (btn) btn.classList.add('active');
        if (sizeWrap) sizeWrap.style.display = 'flex';
        if (previewWrap) previewWrap.classList.add('eraser-active');
    } else {
        if (btn) btn.classList.remove('active');
        if (sizeWrap) sizeWrap.style.display = 'none';
        if (previewWrap) previewWrap.classList.remove('eraser-active');
    }
}

function setUploadModalEraserRadius(val) {
    uploadModalEraserRadius = parseInt(val, 10) || 30;
    var valEl = document.getElementById('uploadModalEraserSizeVal');
    if (valEl) valEl.textContent = uploadModalEraserRadius;
}

function applyUploadModalEraserAt(pixelX, pixelY) {
    if (!uploadModalCanvas || !uploadModalCtx || !pendingUploadDataUrl) return;
    var imageData = uploadModalCtx.getImageData(0, 0, uploadModalCanvas.width, uploadModalCanvas.height);
    var data = imageData.data;
    var w = uploadModalCanvas.width;
    var h = uploadModalCanvas.height;
    var index = (pixelY * w + pixelX) * 4;
    var r = data[index];
    var g = data[index + 1];
    var b = data[index + 2];
    var a = data[index + 3];
    if (a < 128) return;
    var pixelsToRemove = findConnectedPixels(data, pixelX, pixelY, w, h, r, g, b);
    pixelsToRemove.forEach(function(pos) {
        var idx = (pos.y * w + pos.x) * 4;
        data[idx + 3] = 0;
        uploadModalErasedPixels.add(pos.x + ',' + pos.y);
    });
    uploadModalCtx.putImageData(imageData, 0, 0);
    var finalUrl = uploadModalCanvas.toDataURL('image/png');
    uploadModalFinalDataUrl = finalUrl;
    var previewImg = document.getElementById('uploadModalPreview');
    if (previewImg) previewImg.src = finalUrl;
}

function detectColors(img) {
    if (!hiddenCanvas || !hiddenCtx) return;

    const maxSize = 200;
    let width = img.width;
    let height = img.height;

    if (width > maxSize || height > maxSize) {
        const scale = Math.min(maxSize / width, maxSize / height);
        width = Math.floor(width * scale);
        height = Math.floor(height * scale);
    }

    hiddenCanvas.width = width;
    hiddenCanvas.height = height;

    hiddenCtx.drawImage(img, 0, 0, width, height);

    const imageData = hiddenCtx.getImageData(0, 0, width, height);
    const data = imageData.data;

    const colorMap = new Map();
    const sampleRate = 80;

    for (let i = 0; i < data.length; i += sampleRate) {
        const r = data[i];
        const g = data[i + 1];
        const b = data[i + 2];
        const a = data[i + 3];

        if (a < 128) continue;

        const colorKey = `${r},${g},${b}`;

        if (colorMap.has(colorKey)) {
            colorMap.set(colorKey, colorMap.get(colorKey) + 1);
        } else {
            colorMap.set(colorKey, 1);
        }
    }

    const groupedColors = [];
    const processed = new Set();

    for (const [colorKey, count] of colorMap.entries()) {
        if (processed.has(colorKey)) continue;

        const [r, g, b] = colorKey.split(',').map(Number);
        let totalCount = count;
        const similarColors = [colorKey];
        processed.add(colorKey);

        for (const [otherKey, otherCount] of colorMap.entries()) {
            if (processed.has(otherKey)) continue;

            const [or, og, ob] = otherKey.split(',').map(Number);
            const distance = Math.sqrt(
                Math.pow(r - or, 2) +
                Math.pow(g - og, 2) +
                Math.pow(b - ob, 2)
            );

            if (distance <= COLOR_TOLERANCE) {
                totalCount += otherCount;
                similarColors.push(otherKey);
                processed.add(otherKey);
            }
        }

        let avgR = 0, avgG = 0, avgB = 0;
        let total = 0;

        for (const key of similarColors) {
            const [cr, cg, cb] = key.split(',').map(Number);
            const weight = colorMap.get(key);
            avgR += cr * weight;
            avgG += cg * weight;
            avgB += cb * weight;
            total += weight;
        }

        avgR = Math.round(avgR / total);
        avgG = Math.round(avgG / total);
        avgB = Math.round(avgB / total);

        const hexColor = rgbToHex(avgR, avgG, avgB);

        groupedColors.push({
            r: avgR,
            g: avgG,
            b: avgB,
            hex: hexColor,
            count: totalCount,
            originalKeys: similarColors
        });
    }

    groupedColors.sort((a, b) => b.count - a.count);
    var colors = groupedColors.slice(0, 20);
    var MAX_DETECTED = 8;
    while (colors.length > MAX_DETECTED) {
        var bestI = -1, bestJ = -1, minDist = 1e9;
        for (var i = 0; i < colors.length; i++) {
            for (var j = i + 1; j < colors.length; j++) {
                var d = Math.sqrt(
                    Math.pow(colors[i].r - colors[j].r, 2) +
                    Math.pow(colors[i].g - colors[j].g, 2) +
                    Math.pow(colors[i].b - colors[j].b, 2)
                );
                if (d < minDist) { minDist = d; bestI = i; bestJ = j; }
            }
        }
        if (bestI < 0 || bestJ < 0) break;
        var c1 = colors[bestI], c2 = colors[bestJ];
        var tot = c1.count + c2.count;
        var merged = {
            r: Math.round((c1.r * c1.count + c2.r * c2.count) / tot),
            g: Math.round((c1.g * c1.count + c2.g * c2.count) / tot),
            b: Math.round((c1.b * c1.count + c2.b * c2.count) / tot),
            count: tot,
            originalKeys: (c1.originalKeys || []).concat(c2.originalKeys || [])
        };
        merged.hex = rgbToHex(merged.r, merged.g, merged.b);
        colors.splice(bestJ, 1);
        colors.splice(bestI, 1);
        colors.push(merged);
        colors.sort(function(a, b) { return b.count - a.count; });
    }
    detectedColors = colors;

    colorMappings = {};
    detectedColors.forEach(color => {
        colorMappings[color.hex] = color.hex;
    });

    selectedColorsForVector = detectedColors.map(c => c.hex);
}

function rgbToHex(r, g, b) {
    return "#" + [r, g, b].map(x => {
        const clamped = Math.max(0, Math.min(255, Math.round(x)));
        const hex = clamped.toString(16);
        return hex.length === 1 ? "0" + hex : hex;
    }).join("");
}

function normalizeHex(hex) {
    if (!hex || typeof hex !== 'string') return hex;
    var m = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return m ? '#' + m[1].toLowerCase() + m[2].toLowerCase() + m[3].toLowerCase() : hex;
}

function hexToRgb(hex) {
    var h = normalizeHex(hex);
    if (!h) return null;
    var result = /^#([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(h);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function displayColorSelection() {
    const colorsGrid = document.getElementById('colorsGridMascot');
    if (!colorsGrid) return;

    colorsGrid.innerHTML = '';
    selectedColorCount = 0;
    selectedColorsForVector = [];

    if (detectedColors.length === 0) {
        return;
    }
}

function selectColorCount(count, buttonElement) {
    const buttons = document.querySelectorAll('.color-count-btn-small');
    buttons.forEach(btn => btn.classList.remove('active'));

    if (buttonElement) {
        buttonElement.classList.add('active');
    }

    selectedColorCount = count;

    let colorsToShow = count;
    if (count === 8) {
        colorsToShow = Math.min(detectedColors.length, 20);
    } else {
        colorsToShow = Math.min(count, detectedColors.length);
    }

    const colorsToDisplay = detectedColors.slice(0, colorsToShow);
    selectedColorsForVector = colorsToDisplay.map(c => c.hex);

    displaySelectedColors(colorsToDisplay);

    var uploadModal = document.getElementById('uploadImageModal');
    if (uploadModal && uploadModal.style.display === 'flex') {
        buildUploadModalColorGrid();
        updateUploadModalPreview();
    }

    const convertBtn = document.getElementById('convertBtn');
    if (convertBtn) {
        convertBtn.style.display = 'block';
    }
}

// function displaySelectedColors(colors){

//     const colorsGrid = document.getElementById('colorsGridMascot');
//     if(!colorsGrid) return;

//     colorsGrid.innerHTML = '';

//     colors.forEach(color=>{

//         const box = document.createElement('div');
//         box.className = 'color-item-mascot';
//         box.style.background = color.hex;

//         // IMPORTANT — click opens backend popup
//         box.onclick = () => openColorPalette();

//         colorsGrid.appendChild(box);
//     });
// }
function displaySelectedColors(colors){

    const colorsGrid = document.getElementById('colorsGridMascot');
    if(!colorsGrid) return;

    colorsGrid.innerHTML = '';

    colors.forEach(color=>{

        const box = document.createElement('div');
        box.className = 'color-item-mascot';
        box.style.background = color.hex;

        // 🔥 click → backend colors
        box.onclick = () => openBackendColorModal(color.hex);

        colorsGrid.appendChild(box);
    });
}

function openColorPalette(){
    document.getElementById('colorPaletteModal').style.display='flex';
}

function closeColorPalette(){
    document.getElementById('colorPaletteModal').style.display='none';
}

function togglePaletteColor(el){
    el.classList.toggle('selected');
}

function applySelectedColors(){

    if(!currentImage){
        alert('No image selected');
        return;
    }

    const picked = [];

    document.querySelectorAll('.modern-box.selected').forEach(el=>{
        picked.push(window.getComputedStyle(el).backgroundColor);
    });

    if(!picked.length){
        alert('Select at least one color');
        return;
    }

    const img = new Image();
    img.crossOrigin='anonymous';

    img.onload = ()=> recolorImageWithBackend(img,picked);

    img.src = currentImage.getElement().src;
}
function recolorImageWithBackend(img,backendColors){

    hiddenCanvas.width = img.width;
    hiddenCanvas.height = img.height;

    hiddenCtx.drawImage(img,0,0);

    const imageData = hiddenCtx.getImageData(0,0,img.width,img.height);
    const data = imageData.data;

    const rgbList = backendColors.map(c=>rgbFromCss(c));

    for(let i=0;i<data.length;i+=4){

        if(data[i+3]<50) continue;

        let best=rgbList[0];
        let min=999999;

        rgbList.forEach(rgb=>{
            const d=Math.abs(data[i]-rgb.r)+Math.abs(data[i+1]-rgb.g)+Math.abs(data[i+2]-rgb.b);
            if(d<min){min=d;best=rgb;}
        });

        data[i]=best.r;
        data[i+1]=best.g;
        data[i+2]=best.b;
    }

    hiddenCtx.putImageData(imageData,0,0);

    const url = hiddenCanvas.toDataURL('image/png');

    fabric.Image.fromURL(url,newImg=>{

        newImg.scaleX=currentImage.scaleX;
        newImg.scaleY=currentImage.scaleY;
        newImg.left=currentImage.left;
        newImg.top=currentImage.top;

        fabricCanvas.remove(currentImage);
        currentImage=newImg;
        fabricCanvas.add(newImg);
        fabricCanvas.setActiveObject(newImg);
        fabricCanvas.renderAll();

        closeColorPalette();
    });
}
function rgbFromCss(css){

    if(css.startsWith('#')){
        return hexToRgb(css);
    }

    const m=css.match(/\d+/g);

    return {
        r:parseInt(m[0]),
        g:parseInt(m[1]),
        b:parseInt(m[2])
    };
}

function convertToVector() {
    if (isProcessing) {
        alert('Please wait, processing in progress...');
        return;
    }

    if (selectedColorCount === 0) {
        alert('Please select number of colors first.');
        return;
    }

    if (selectedColorsForVector.length === 0) {
        alert('No colors selected for vector conversion.');
        return;
    }

    if (!originalImageData) {
        alert('No image loaded.');
        return;
    }

    isProcessing = true;
    showLoading('Converting to vector...');

    setTimeout(() => {
        const img = new Image();
        img.onload = function() {
            reduceImageToColorsAsync(img, selectedColorsForVector);
        };
        img.src = originalImageData;
    }, 100);
}

function reduceImageToColorsAsync(img, selectedColorHexes) {
    if (!hiddenCanvas || !hiddenCtx) return;

    let width = img.width;
    let height = img.height;
    let scale = 1;

    if (width > MAX_PROCESSING_SIZE || height > MAX_PROCESSING_SIZE) {
        scale = Math.min(MAX_PROCESSING_SIZE / width, MAX_PROCESSING_SIZE / height);
        width = Math.floor(width * scale);
        height = Math.floor(height * scale);
    }

    hiddenCanvas.width = width;
    hiddenCanvas.height = height;

    if (scale < 1) {
        hiddenCtx.drawImage(img, 0, 0, width, height);
    } else {
        hiddenCtx.drawImage(img, 0, 0);
    }

    const imageData = hiddenCtx.getImageData(0, 0, width, height);
    const data = imageData.data;

    const selectedColorRgbs = [];
    detectedColors.forEach(color => {
        if (selectedColorHexes.includes(color.hex)) {
            const rgb = hexToRgb(color.hex);
            if (rgb) {
                selectedColorRgbs.push({
                    r: rgb.r,
                    g: rgb.g,
                    b: rgb.b,
                    hex: color.hex
                });
            }
        }
    });

    const chunkSize = 10000;
    let processedPixels = 0;
    const totalPixels = data.length / 4;

    function processChunk() {
        const end = Math.min(processedPixels + chunkSize, totalPixels);

        for (let i = processedPixels * 4; i < end * 4; i += 4) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            const a = data[i + 3];

            if (a < 128) continue;

            let minDistance = Infinity;
            let nearestColor = selectedColorRgbs[0];

            for (const color of selectedColorRgbs) {
                const distance = Math.sqrt(
                    Math.pow(r - color.r, 2) +
                    Math.pow(g - color.g, 2) +
                    Math.pow(b - color.b, 2)
                );

                if (distance < minDistance) {
                    minDistance = distance;
                    nearestColor = color;
                }
            }

            data[i] = nearestColor.r;
            data[i + 1] = nearestColor.g;
            data[i + 2] = nearestColor.b;
        }

        processedPixels = end;

        if (processedPixels < totalPixels) {
            requestAnimationFrame(processChunk);
        } else {
            hiddenCtx.putImageData(imageData, 0, 0);

            const reducedDataUrl = hiddenCanvas.toDataURL('image/png');

            fabric.Image.fromURL(reducedDataUrl, function(reducedImg) {
                const maxWidth = fabricCanvas.width - 40;
                const maxHeight = fabricCanvas.height - 40;
                const imgScale = Math.min(
                    maxWidth / reducedImg.width,
                    maxHeight / reducedImg.height,
                    1
                );

                reducedImg.scale(imgScale);
                reducedImg.set({
                    left: (fabricCanvas.width - reducedImg.width * imgScale) / 2,
                    top: (fabricCanvas.height - reducedImg.height * imgScale) / 2,
                    selectable: true,
                    hasControls: true,
                    hasBorders: true
                });

                if (currentImage) {
                    fabricCanvas.remove(currentImage);
                }

                reducedImg.name = `Vector Image ${++layerCounter}`;
                reducedImg.set('colorMappings', Object.assign({}, colorMappings));
                currentImage = reducedImg;
                fabricCanvas.add(reducedImg);
                fabricCanvas.setActiveObject(reducedImg);
                fabricCanvas.renderAll();
                updateLayers();

                hideLoading();
                isProcessing = false;
            });
        }
    }

    processChunk();
}

function displayColorPickers(targetGridId) {
    const colorEditGrid = document.getElementById(targetGridId || 'colorEditGridMascot');
    if (!colorEditGrid) return;

    colorEditGrid.innerHTML = '';

    selectedColorsForVector.forEach((colorHex, index) => {
        const colorObj = detectedColors.find(c => c.hex === colorHex);
        if (!colorObj) return;

        const colorItem = document.createElement('div');
        colorItem.className = 'color-item-mascot color-item-right-single';

        const label = document.createElement('div');
        label.className = 'color-label-right';
        label.textContent = 'Color ' + (index + 1);

const picker = document.createElement('div');
picker.className = 'color-preview-box';
picker.style.background = colorMappings[colorHex] || colorHex;

picker.classList.add('backend-color-btn');
        picker.className = 'color-picker-input-mascot';
        picker.value = colorMappings[colorHex] || colorHex;
        picker.dataset.originalColor = colorHex;
        picker.title = 'Color change karein';

        const info = document.createElement('div');
        info.className = 'color-info-mascot';
        info.textContent = (colorMappings[colorHex] || colorHex).toUpperCase();

   picker.addEventListener('click', function() {
    openBackendColorModal(colorHex);
});


        colorItem.appendChild(label);
        colorItem.appendChild(picker);
        colorItem.appendChild(info);
        colorEditGrid.appendChild(colorItem);
    });
}

function openBackendColorModal(hex){
console.log('popup open', hex);

    backendTargetHex = hex;

    renderBackendColors(window.backendColors);

    document.getElementById('backendColorModal').style.display='flex';
}

function renderBackendColors(colors){

    let grid = document.getElementById('backendColorGrid');
    grid.innerHTML='';

    colors.forEach(c=>{

        let div=document.createElement('div');
        div.className='backend-color-item';
        div.style.background=c.code;
        div.title=c.name;

div.onclick=function(){

    // IMAGE COLORS
    if(backendTargetHex !== '#text'){

        colorMappings[backendTargetHex]=c.code;

        applyColorChanges();
        buildUploadModalColorGrid();
        updateUploadModalPreview();
    }

    // TEXT COLORS
    else{

        let obj = fabricCanvas.getActiveObject();
        if(!obj) return;

        if(backendTextMode==='fill'){
            obj.set('fill',c.code);
            document.getElementById('textFillColor').value=c.code;
        }

        if(backendTextMode==='stroke'){
            obj.set('stroke',c.code);
            document.getElementById('textStrokeColor').value=c.code;
        }

        fabricCanvas.renderAll();
    }

    backendTextMode=null;
    closeBackendColorModal();
};




        grid.appendChild(div);
    });
}

function closeBackendColorModal(){
    document.getElementById('backendColorModal').style.display='none';
}

function filterBackendColors(val){

    val=val.toLowerCase();

    let filtered=window.backendColors.filter(c=>
        c.name.toLowerCase().includes(val) ||
        c.code.toLowerCase().includes(val)
    );

    renderBackendColors(filtered);
}



function applyColorChanges() {
    if (!currentImage || !originalImageData) return;

    clearTimeout(colorChangeTimeout);
    colorChangeTimeout = setTimeout(() => {
        if (isProcessing) return;

        isProcessing = true;
        showLoading('Applying color changes...');

        const img = new Image();
        img.onload = function() {
            applyColorChangesAsync(img);
        };
        img.src = originalImageData;
    }, 300);
}

function applyColorChangesAsync(img) {
    if (!hiddenCanvas || !hiddenCtx) return;

    let width = img.width;
    let height = img.height;
    let scale = 1;

    if (width > MAX_PROCESSING_SIZE || height > MAX_PROCESSING_SIZE) {
        scale = Math.min(MAX_PROCESSING_SIZE / width, MAX_PROCESSING_SIZE / height);
        width = Math.floor(width * scale);
        height = Math.floor(height * scale);
    }

    hiddenCanvas.width = width;
    hiddenCanvas.height = height;

    if (scale < 1) {
        hiddenCtx.drawImage(img, 0, 0, width, height);
    } else {
        hiddenCtx.drawImage(img, 0, 0);
    }

    const imageData = hiddenCtx.getImageData(0, 0, width, height);
    const data = imageData.data;

    const colorMap = new Map();
    const targetColors = [];
    detectedColors.forEach(color => {
        if (selectedColorsForVector.includes(color.hex)) {
            const newColor = colorMappings[color.hex] || color.hex;
            const rgb = hexToRgb(newColor);
            if (rgb) {
                targetColors.push(rgb);
                if (color.originalKeys) {
                    color.originalKeys.forEach(key => {
                        colorMap.set(key, { r: rgb.r, g: rgb.g, b: rgb.b });
                    });
                }
            }
        }
    });

    const chunkSize = 10000;
    let processedPixels = 0;
    const totalPixels = data.length / 4;

    function processChunk() {
        const end = Math.min(processedPixels + chunkSize, totalPixels);

        for (let i = processedPixels * 4; i < end * 4; i += 4) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            const a = data[i + 3];

            if (a < 128) {
                data[i] = 0;
                data[i + 1] = 0;
                data[i + 2] = 0;
                data[i + 3] = 0;
                continue;
            }

            const colorKey = `${r},${g},${b}`;
            let nc = null;
            if (colorMap.has(colorKey)) {
                nc = colorMap.get(colorKey);
            } else {
                for (const [key, newColor] of colorMap.entries()) {
                    const [or, og, ob] = key.split(',').map(Number);
                    const distance = Math.sqrt(
                        Math.pow(r - or, 2) +
                        Math.pow(g - og, 2) +
                        Math.pow(b - ob, 2)
                    );
                    if (distance <= COLOR_TOLERANCE) { nc = newColor; break; }
                }
            }
            if (!nc && targetColors.length > 0) {
                let bestIdx = 0, minD = 1e9;
                for (let t = 0; t < targetColors.length; t++) {
                    const tr = targetColors[t].r, tg = targetColors[t].g, tb = targetColors[t].b;
                    const d = Math.sqrt(Math.pow(r - tr, 2) + Math.pow(g - tg, 2) + Math.pow(b - tb, 2));
                    if (d < minD) { minD = d; bestIdx = t; }
                }
                var darkSum = r + g + b;
                if (darkSum < 45 && minD > 80) {
                    data[i] = 0;
                    data[i + 1] = 0;
                    data[i + 2] = 0;
                    data[i + 3] = 0;
                    continue;
                }
                nc = targetColors[bestIdx];
            }
            if (!nc && targetColors.length > 0) { nc = targetColors[0]; }
            if (nc) {
                data[i] = Math.round(nc.r);
                data[i + 1] = Math.round(nc.g);
                data[i + 2] = Math.round(nc.b);
            }
        }

        processedPixels = end;

        if (processedPixels < totalPixels) {
            requestAnimationFrame(processChunk);
        } else {
            hiddenCtx.putImageData(imageData, 0, 0);

            const modifiedDataUrl = hiddenCanvas.toDataURL('image/png');

            fabric.Image.fromURL(modifiedDataUrl, function(modifiedImg) {
                const maxWidth = fabricCanvas.width - 40;
                const maxHeight = fabricCanvas.height - 40;
                const imgScale = Math.min(
                    maxWidth / modifiedImg.width,
                    maxHeight / modifiedImg.height,
                    1
                );
                modifiedImg.scale(imgScale);
                var left = (fabricCanvas.width - modifiedImg.width * imgScale) / 2;
                var top = (fabricCanvas.height - modifiedImg.height * imgScale) / 2;
                if (currentImage && (currentImage.left != null || currentImage.top != null)) {
                    left = currentImage.left;
                    top = currentImage.top;
                    if (currentImage.scaleX != null) modifiedImg.scaleX = currentImage.scaleX;
                    if (currentImage.scaleY != null) modifiedImg.scaleY = currentImage.scaleY;
                }
                modifiedImg.set({
                    left: left,
                    top: top,
                    selectable: true,
                    hasControls: true,
                    hasBorders: true
                });
                isApplyingColorChange = true;
                if (currentImage) {
                    fabricCanvas.remove(currentImage);
                }
                modifiedImg.name = currentImage ? currentImage.name : `Vector Image ${++layerCounter}`;
                modifiedImg.set('colorMappings', Object.assign({}, colorMappings));
                currentImage = modifiedImg;
                fabricCanvas.add(modifiedImg);
                fabricCanvas.setActiveObject(modifiedImg);
                fabricCanvas.renderAll();
                updateLayers();
                updateObjectSettingsPanel();
                isApplyingColorChange = false;
                hideLoading();
                isProcessing = false;
            });
        }
    }

    processChunk();
}

var ERASER_CURSOR_SVG = "data:image/svg+xml," + encodeURIComponent(
    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#333" stroke="#fff" stroke-width="1" d="M20 20H7L2 15l8-8 5 5-5 5 5 5z"/></svg>'
);
////////////////
function openBackendFontModal(){
    renderBackendFonts(window.backendFonts);
    document.getElementById('backendFontModal').style.display='flex';
}

function closeBackendFontModal(){
    document.getElementById('backendFontModal').style.display='none';
}

function renderBackendFonts(fonts){

    let grid=document.getElementById('backendFontGrid');
    grid.innerHTML='';

    fonts.forEach(f=>{

        let div=document.createElement('div');
        div.innerText=f.name;
        div.style.fontFamily='font_'+f.id;
        div.style.cursor='pointer';
        div.style.padding='10px';
        div.style.borderBottom='1px solid #ddd';

        div.onclick=function(){

            let obj=fabricCanvas.getActiveObject();
         if(obj){

    // 🔥 Normal text
    if(obj.type === 'i-text' || obj.type === 'text'){
        obj.set('fontFamily','font_'+f.id);
    }

    // 🔥 Shaped text (group)
    else if(obj.type === 'group' && obj.textShape){

        obj._objects.forEach(letter=>{
            letter.set('fontFamily','font_'+f.id);
        });

        obj.fontFamily = 'font_'+f.id;
    }

    fabricCanvas.renderAll();
}

            closeBackendFontModal();
        };

        grid.appendChild(div);
    });
}

function filterBackendFonts(val){

    val=val.toLowerCase();
    renderBackendFonts(
        window.backendFonts.filter(f=>f.name.toLowerCase().includes(val))
    );
}


function toggleEraser() {

    activateSelectTool(); // reset first

    eraserMode = true;

    fabricCanvas.selection = false;

    fabricCanvas.defaultCursor = "url('" + ERASER_CURSOR_SVG + "') 4 20, crosshair";
    fabricCanvas.hoverCursor = fabricCanvas.defaultCursor;

    document.getElementById('eraserToolBtn')?.classList.add('active');
    document.getElementById('selectToolBtn')?.classList.remove('active');

    document.querySelector('.canvas-wrapper')?.classList.add('eraser-cursor');
}


function handleCanvasClick(e) {
    if (eraserMode && currentImage) {
        e.e.preventDefault();
        e.e.stopPropagation();
        applyEraserAt(fabricCanvas.getPointer(e.e).x, fabricCanvas.getPointer(e.e).y);
        return false;
    }
    if (eyeDropperAddMode && currentImage) {
        e.e.preventDefault();
        e.e.stopPropagation();
        addColorFromImagePick(fabricCanvas.getPointer(e.e).x, fabricCanvas.getPointer(e.e).y);
        return false;
    }
    if (eyeDropperMode && currentImage) {
        e.e.preventDefault();
        e.e.stopPropagation();
        pickColorFromImage(fabricCanvas.getPointer(e.e).x, fabricCanvas.getPointer(e.e).y);
        return false;
    }
}

function applyEraserAt(x, y) {
    if (!currentImage || !hiddenCanvas || !hiddenCtx) return;
    const imgElement = currentImage.getElement();
    if (!imgElement) return;

    // Use Fabric's bounding rect so click position maps correctly (fix wrong-area erase)
    const bound = currentImage.getBoundingRect ? currentImage.getBoundingRect(true) : null;
    const imgRect = bound ? {
        left: bound.left,
        top: bound.top,
        width: bound.width,
        height: bound.height
    } : {
        left: currentImage.left,
        top: currentImage.top,
        width: (currentImage.width || 0) * (currentImage.scaleX || 1),
        height: (currentImage.height || 0) * (currentImage.scaleY || 1)
    };

    if (x < imgRect.left || x > imgRect.left + imgRect.width ||
        y < imgRect.top || y > imgRect.top + imgRect.height) return;

    const localX = x - imgRect.left;
    const localY = y - imgRect.top;
    const scaleX = imgElement.width / imgRect.width;
    const scaleY = imgElement.height / imgRect.height;
    const pixelX = Math.floor(localX * scaleX);
    const pixelY = Math.floor(localY * scaleY);

    // Ensure coordinates are within bounds
    if (pixelX < 0 || pixelX >= imgElement.width || pixelY < 0 || pixelY >= imgElement.height) {
        return;
    }

    // Draw image to hidden canvas to get pixel data
    hiddenCanvas.width = imgElement.width;
    hiddenCanvas.height = imgElement.height;
    hiddenCtx.drawImage(imgElement, 0, 0);

    // Get pixel data
    const imageData = hiddenCtx.getImageData(0, 0, imgElement.width, imgElement.height);
    const data = imageData.data;

    // Get the color at clicked pixel
    const index = (pixelY * imgElement.width + pixelX) * 4;
    const r = data[index];
    const g = data[index + 1];
    const b = data[index + 2];
    const a = data[index + 3];

    // Skip if already transparent
    if (a === 0) {
        return;
    }

    // Find all connected pixels with same color
    const pixelsToRemove = findConnectedPixels(data, pixelX, pixelY, imgElement.width, imgElement.height, r, g, b);

    // Make pixels transparent
    pixelsToRemove.forEach(pos => {
        const idx = (pos.y * imgElement.width + pos.x) * 4;
        data[idx + 3] = 0; // Set alpha to 0 (transparent)
    });

    // Put modified data back
    hiddenCtx.putImageData(imageData, 0, 0);
originalImageData = hiddenCanvas.toDataURL('image/png');

    const newDataUrl = hiddenCanvas.toDataURL('image/png');
    saveStateForUndo();
    fabric.Image.fromURL(newDataUrl, function(newImg) {
        const maxWidth = fabricCanvas.width - 40;
        const maxHeight = fabricCanvas.height - 40;
        const imgScale = Math.min(
            maxWidth / newImg.width,
            maxHeight / newImg.height,
            1
        );

        newImg.scale(imgScale);
        newImg.set({
            left: currentImage.left,
            top: currentImage.top,
            selectable: true,
            hasControls: true,
            hasBorders: true
        });

        if (currentImage) {
            fabricCanvas.remove(currentImage);
        }

        newImg.name = currentImage ? currentImage.name : `Vector Image ${++layerCounter}`;
        currentImage = newImg;
        fabricCanvas.add(newImg);
        fabricCanvas.setActiveObject(newImg);
        fabricCanvas.renderAll();
        updateLayers();
    });
}

function findConnectedPixels(data, startX, startY, width, height, targetR, targetG, targetB) {
    const visited = new Set();
    const result = [];
    const stack = [{x: startX, y: startY}];

    while (stack.length > 0) {
        const {x, y} = stack.pop();
        const key = `${x},${y}`;

        if (visited.has(key)) continue;
        if (x < 0 || x >= width || y < 0 || y >= height) continue;

        const index = (y * width + x) * 4;
        const r = data[index];
        const g = data[index + 1];
        const b = data[index + 2];
        const a = data[index + 3];

        if (a === 0) continue;

        const distance = Math.sqrt(
            Math.pow(r - targetR, 2) +
            Math.pow(g - targetG, 2) +
            Math.pow(b - targetB, 2)
        );

        if (distance <= COLOR_MATCH_TOLERANCE) {
            visited.add(key);
            result.push({x, y});

            stack.push({x: x + 1, y});
            stack.push({x: x - 1, y});
            stack.push({x, y: y + 1});
            stack.push({x, y: y - 1});
        }
    }

    return result;
}

function activateEyeDropperForColor(colorHex) {
    // Deactivate eraser if active
    if (eraserMode) {
        eraserMode = false;
        const eraserBtn = document.getElementById('eraserToolBtn');
        if (eraserBtn) {
            eraserBtn.classList.remove('active');
        }
        fabricCanvas.selection = true;
    }

    // Toggle eye dropper
    if (eyeDropperMode && eyeDropperTargetColor === colorHex) {
        // Deactivate if already active for this color
        resetEyeDropper();
    } else {
        // Activate eye dropper
        eyeDropperMode = true;
        eyeDropperTargetColor = colorHex;
        fabricCanvas.defaultCursor = 'crosshair';
        fabricCanvas.selection = false; // Disable selection when eye dropper is active

        // Update button states
        const eyeDropperBtns = document.querySelectorAll('.eye-dropper-btn-mascot');
        eyeDropperBtns.forEach(btn => {
            const colorItem = btn.closest('.color-item-mascot');
            const picker = colorItem?.querySelector('.color-picker-input-mascot');
            const originalColor = picker?.dataset.originalColor;

            if (originalColor === colorHex) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }
}

function pickColorFromImage(x, y) {
    if (!currentImage || !hiddenCanvas || !hiddenCtx) { resetEyeDropper(); return; }
    const imgElement = currentImage.getElement();
    if (!imgElement) { resetEyeDropper(); return; }
    const bound = currentImage.getBoundingRect ? currentImage.getBoundingRect(true) : null;
    const imgRect = bound ? { left: bound.left, top: bound.top, width: bound.width, height: bound.height } : {
        left: currentImage.left, top: currentImage.top,
        width: (currentImage.width || 0) * (currentImage.scaleX || 1),
        height: (currentImage.height || 0) * (currentImage.scaleY || 1)
    };
    if (x < imgRect.left || x > imgRect.left + imgRect.width || y < imgRect.top || y > imgRect.top + imgRect.height) {
        resetEyeDropper();
        return;
    }
    const localX = x - imgRect.left;
    const localY = y - imgRect.top;
    const scaleX = imgElement.width / imgRect.width;
    const scaleY = imgElement.height / imgRect.height;
    const pixelX = Math.floor(localX * scaleX);
    const pixelY = Math.floor(localY * scaleY);

    // Ensure coordinates are within bounds
    if (pixelX < 0 || pixelX >= imgElement.width || pixelY < 0 || pixelY >= imgElement.height) {
        resetEyeDropper();
        return;
    }

    // Draw image to hidden canvas to get pixel data
    hiddenCanvas.width = imgElement.width;
    hiddenCanvas.height = imgElement.height;
    hiddenCtx.drawImage(imgElement, 0, 0);

    // Get pixel color
    const imageData = hiddenCtx.getImageData(0, 0, imgElement.width, imgElement.height);
    const data = imageData.data;

    const index = (pixelY * imgElement.width + pixelX) * 4;
    const r = data[index];
    const g = data[index + 1];
    const b = data[index + 2];
    const a = data[index + 3];

    // Skip transparent pixels
    if (a < 128) {
        resetEyeDropper();
        return;
    }

    const pickedColor = rgbToHex(r, g, b);

    // Update color mapping
    if (eyeDropperTargetColor) {
        // Update the color mapping
        colorMappings[eyeDropperTargetColor] = pickedColor;

        // Also update in detectedColors if it exists
        const colorObj = detectedColors.find(c => c.hex === eyeDropperTargetColor);
        if (colorObj) {
            const rgb = hexToRgb(pickedColor);
            if (rgb) {
                colorObj.r = rgb.r;
                colorObj.g = rgb.g;
                colorObj.b = rgb.b;
                colorObj.hex = pickedColor;
            }
        }
        const idx = selectedColorsForVector.indexOf(eyeDropperTargetColor);
        if (idx !== -1) selectedColorsForVector[idx] = pickedColor;

        const colorItems = document.querySelectorAll('.color-item-mascot');
        colorItems.forEach(item => {
            const isTarget = item.dataset.colorHex === eyeDropperTargetColor;
            const picker = item.querySelector('.color-picker-input-mascot');
            if (isTarget || (picker && picker.dataset.originalColor === eyeDropperTargetColor)) {
                const preview = item.querySelector('.color-preview-mascot');
                const hexEl = item.querySelector('.color-hex-mascot');
                if (preview) preview.style.backgroundColor = pickedColor;
                if (hexEl) hexEl.textContent = pickedColor;
                if (picker) picker.value = pickedColor;
                item.dataset.colorHex = pickedColor;
            }
        });
        if (typeof applyColorChanges === 'function') applyColorChanges();
    }
    resetEyeDropper();
}

function resetEyeDropper() {
    eyeDropperMode = false;
    eyeDropperTargetColor = null;
    eyeDropperAddMode = false;
    fabricCanvas.defaultCursor = 'default';
    fabricCanvas.hoverCursor = 'move';
    const eyeDropperBtns = document.querySelectorAll('.eye-dropper-btn-mascot');
    eyeDropperBtns.forEach(btn => btn.classList.remove('active'));
    const addBtn = document.getElementById('addColorFromImageBtnMascot');
    if (addBtn) addBtn.classList.remove('active');
}

function activateAddColorFromImage() {
    if (!currentImage) { alert('Pehle image upload karein.'); return; }
    eyeDropperMode = false;
    eyeDropperTargetColor = null;
    eyeDropperAddMode = true;
    const addBtn = document.getElementById('addColorFromImageBtnMascot');
    if (addBtn) addBtn.classList.add('active');
    fabricCanvas.defaultCursor = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23333\' stroke-width=\'2\'%3E%3Cpath d=\'M2 22c1-2 3-4 5-4s4 2 6 4\'/%3E%3Cpath d=\'M15 8l5-5 2 2-5 5\'/%3E%3Cpath d=\'M13 10L4 19l-2 2 2-2 9-9\'/%3E%3C/svg%3E") 2 22, crosshair';
    fabricCanvas.hoverCursor = fabricCanvas.defaultCursor;
    alert('Image par jis color ko add karna hai us jagah click karein.');
}

function addColorFromImagePick(x, y) {
    if (!currentImage || !hiddenCanvas || !hiddenCtx) return;
    const imgElement = currentImage.getElement();
    if (!imgElement) return;
    const bound = currentImage.getBoundingRect ? currentImage.getBoundingRect(true) : null;
    const imgRect = bound ? { left: bound.left, top: bound.top, width: bound.width, height: bound.height } : {
        left: currentImage.left, top: currentImage.top,
        width: (currentImage.width || 0) * (currentImage.scaleX || 1),
        height: (currentImage.height || 0) * (currentImage.scaleY || 1)
    };
    if (x < imgRect.left || x > imgRect.left + imgRect.width || y < imgRect.top || y > imgRect.top + imgRect.height) return;
    const localX = x - imgRect.left, localY = y - imgRect.top;
    const scaleX = imgElement.width / imgRect.width, scaleY = imgElement.height / imgRect.height;
    const pixelX = Math.floor(localX * scaleX), pixelY = Math.floor(localY * scaleY);
    if (pixelX < 0 || pixelX >= imgElement.width || pixelY < 0 || pixelY >= imgElement.height) return;
    hiddenCanvas.width = imgElement.width;
    hiddenCanvas.height = imgElement.height;
    hiddenCtx.drawImage(imgElement, 0, 0);
    const imageData = hiddenCtx.getImageData(0, 0, imgElement.width, imgElement.height);
    const data = imageData.data;
    const idx = (pixelY * imgElement.width + pixelX) * 4;
    const r = data[idx], g = data[idx + 1], b = data[idx + 2], a = data[idx + 3];
    if (a < 128) { alert('Transparent area – solid color select karein.'); return; }
    const pickedHex = rgbToHex(r, g, b);
    if (detectedColors.some(c => c.hex.toLowerCase() === pickedHex.toLowerCase())) { alert('Yeh color pehle se list mein hai.'); return; }
    const canAdd = selectedColorCount === 8 || selectedColorsForVector.length < selectedColorCount;
    if (!canAdd) { alert('Aur color add karne ke liye pehle color count badhayen (e.g. 6 ya 7).'); return; }
    detectedColors.push({ hex: pickedHex, count: 1, r, g, b });
    selectedColorsForVector.push(pickedHex);
    colorMappings[pickedHex] = pickedHex;
    const colorsToShow = selectedColorCount === 8 ? Math.min(detectedColors.length, 20) : Math.min(selectedColorCount, detectedColors.length);
    displaySelectedColors(detectedColors.slice(0, colorsToShow));
    eyeDropperAddMode = false;
    const addBtn = document.getElementById('addColorFromImageBtnMascot');
    if (addBtn) addBtn.classList.remove('active');
    fabricCanvas.defaultCursor = 'default';
    fabricCanvas.hoverCursor = 'move';
}

function showLoading(text) {
    const overlay = document.getElementById('loadingOverlayMascot');
    if (overlay) {
        const textEl = overlay.querySelector('.loading-text-mascot');
        if (textEl) textEl.textContent = text || 'Processing...';
        overlay.style.display = 'flex';
    }
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlayMascot');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

function addText() {
    const ITextClass = fabric.IText || fabric.FabricIText || fabric.Text;
    const isIText = ITextClass && (ITextClass === fabric.IText || ITextClass === fabric.FabricIText);
    const text = isIText
        ? new fabric.IText('Double click to edit', {
            left: fabricCanvas.width / 2 - 100,
            top: fabricCanvas.height / 2 - 25,
            fontSize: 40,
            fontFamily: 'Arial',
            fill: '#000000',
            selectable: true,
            hasControls: true,
            hasBorders: true
          })
        : new fabric.Text('Double click to edit', {
            left: fabricCanvas.width / 2 - 100,
            top: fabricCanvas.height / 2 - 25,
            fontSize: 40,
            fontFamily: 'Arial',
            fill: '#000000',
            selectable: true,
            hasControls: true,
            hasBorders: true
          });

    text.name = `Text ${++layerCounter}`;
    saveStateForUndo();
    fabricCanvas.add(text);
    fabricCanvas.setActiveObject(text);
    fabricCanvas.renderAll();
    updateLayers();
    updateTextSettingsPanel();
}

function openColorPicker() {
    const color = prompt('Enter color hex code (e.g., #FF0000):', '#000000');
    if (color) {
        const activeObject = fabricCanvas.getActiveObject();
        if (activeObject) {
            activeObject.set('fill', color);
            fabricCanvas.renderAll();
        } else {
            alert('Please select an object first to change its color.');
        }
    }
}

function selectAll() {
    fabricCanvas.discardActiveObject();
    const sel = new fabric.ActiveSelection(fabricCanvas.getObjects(), {
        canvas: fabricCanvas
    });
    fabricCanvas.setActiveObject(sel);
    fabricCanvas.renderAll();
}

function duplicate() {
    const activeObject = fabricCanvas.getActiveObject();
    if (!activeObject) return alert('Please select object');

    activeObject.clone(function(cloned) {

        cloned.set({
            left: cloned.left + 20,
            top: cloned.top + 20
        });

        cloned.name = `${activeObject.name} Copy`;

        // IMPORTANT — color mappings bhi copy karo
        if (activeObject.colorMappings) {
            cloned.colorMappings = JSON.parse(JSON.stringify(activeObject.colorMappings));
        }

        saveStateForUndo();

        fabricCanvas.add(cloned);
        fabricCanvas.setActiveObject(cloned);

        // 🔥 VERY IMPORTANT
        currentImage = cloned;

        fabricCanvas.renderAll();
        updateLayers();
        updateObjectSettingsPanel();
    });
}

function activateSelectTool(){

    // disable eraser
    eraserMode = false;

    fabricCanvas.selection = true;
    fabricCanvas.defaultCursor = 'default';
    fabricCanvas.hoverCursor = 'move';

    document.getElementById('eraserToolBtn')?.classList.remove('active');
    document.getElementById('selectToolBtn')?.classList.add('active');

    document.querySelector('.canvas-wrapper')?.classList.remove('eraser-cursor');
}

function toggleLayers() {
    const sidebar = document.getElementById('rightSidebar');
    if (sidebar) {
        sidebar.classList.toggle('right-sidebar-hidden');
        if (!sidebar.classList.contains('right-sidebar-hidden')) {
            rightSidebarView = 'layers';
            updateRightSidebarView();
        }
    }
}

function updateRightSidebarView() {
    var layersSection = document.getElementById('layersPanelSection');
    var objectSection = document.getElementById('objectSettingsSection');
    if (layersSection) layersSection.style.display = rightSidebarView === 'layers' ? 'block' : 'none';
    if (objectSection) objectSection.style.display = rightSidebarView === 'object' ? 'block' : 'none';
}

function openRightSidebar() {
    var sidebar = document.getElementById('rightSidebar');
    if (sidebar) sidebar.classList.remove('right-sidebar-hidden');
}

function saveStateForUndo() {
    try {
        var json = fabricCanvas.toJSON([
            'name',
            'textShape',
            'textContent',
            'fontFamily',
            'fontSize',
            'fill',
            'fontWeight',
            'fontStyle',
            'stroke',
            'strokeWidth',
            'paintFirst',
            'charSpacing',
            'lineHeight',
            'textAlign',
            'colorMappings',
            'selectable',      // ✅ Add
            'hasControls',     // ✅ Add
            'hasBorders',      // ✅ Add
            'left',            // ✅ Add
            'top',             // ✅ Add
            'angle',           // ✅ Add
            'scaleX',          // ✅ Add
            'scaleY'           // ✅ Add
        ]);
        undoHistory.push(json);
        if (undoHistory.length > maxUndoSteps) undoHistory.shift();
        redoHistory = [];
    } catch (e) {
        console.warn('saveStateForUndo', e);
    }
}

function restoreStateFromUndo(json) {
    if (!json) return;
    isRestoringFromUndo = true;
    fabricCanvas.loadFromJSON(json, function() {
        fabricCanvas.renderAll();
        var objs = fabricCanvas.getObjects();
        if (objs.length > 0 && objs[objs.length - 1].name && objs[objs.length - 1].name.indexOf('Image') === 0) currentImage = objs[objs.length - 1];
        else currentImage = objs.find(function(o) { return o.name && o.name.indexOf('Image') === 0; }) || null;
        isRestoringFromUndo = false;
        updateLayers();
        updateLayerSelection();
        hideLayerControlsBar();
        updateObjectSettingsPanel();
    });
}

function undo() {
    if (undoHistory.length === 0) return;
    var currentJson = fabricCanvas.toJSON(['name', 'textShape', 'textContent', 'fontFamily', 'fontSize', 'fill', 'fontWeight', 'fontStyle', 'stroke', 'strokeWidth', 'paintFirst', 'charSpacing', 'lineHeight', 'textAlign']);
    redoHistory.push(currentJson);
    var prev = undoHistory.pop();
    restoreStateFromUndo(prev);
}

function redo() {
    if (redoHistory.length === 0) return;
    var currentJson = fabricCanvas.toJSON(['name', 'textShape', 'textContent', 'fontFamily', 'fontSize', 'fill', 'fontWeight', 'fontStyle', 'stroke', 'strokeWidth', 'paintFirst', 'charSpacing', 'lineHeight', 'textAlign']);
    undoHistory.push(currentJson);
    var next = redoHistory.pop();
    restoreStateFromUndo(next);
}

function resetColorState() {
    originalImageData = null;
    currentImage = null;
    detectedColors = [];
    colorMappings = {};
    selectedColorsForVector = [];
    selectedColorCount = 0;
    var colorSection = document.getElementById('colorSelectionSection');
    var colorEditSection = document.getElementById('colorEditSection');
    if (colorSection) colorSection.style.display = 'none';
    if (colorEditSection) colorEditSection.style.display = 'none';
    var grid = document.getElementById('colorsGridMascot');
    if (grid) grid.innerHTML = '';
    var editGrid = document.getElementById('colorEditGridMascot');
    if (editGrid) editGrid.innerHTML = '';
}

function updateLayers() {
    const layersList = document.getElementById('layersList');
    if (!layersList) return;
    const objects = fabricCanvas.getObjects();

    if (objects.length === 0 && !isApplyingColorChange && !isRestoringFromUndo && !isReorderingLayers) {
        layersList.innerHTML = '<p class="empty-layers-text">No layers yet</p>';
        resetColorState();
        return;
    }

    if (objects.length === 0) return;

    layersList.innerHTML = '';

    // 🔥 FIX: Filter out individual letters from shaped text groups
    const validLayers = objects.filter((obj, index) => {
        // Agar yeh object kisi group ka part hai to skip karo
        if (obj.group) return false;

        // Agar yeh ek single letter hai (shaped text ka part) to skip karo
        if (obj.type === 'i-text' || obj.type === 'text') {
            const text = obj.text || '';
            // Single character texts jo groups mein hain skip karo
            if (text.length === 1) {
                // Check if this is part of a shaped text
                const nextObj = objects[index + 1];
                const prevObj = objects[index - 1];
                if ((nextObj && nextObj.text && nextObj.text.length === 1) ||
                    (prevObj && prevObj.text && prevObj.text.length === 1)) {
                    return false;
                }
            }
        }

        return true;
    });

    validLayers.slice().reverse().forEach((obj, listIndex) => {
        const canvasIndex = objects.indexOf(obj);

        const layerItem = document.createElement('div');
        layerItem.className = 'layer-item';
        layerItem.draggable = true;
        layerItem.dataset.index = String(canvasIndex);

        const layerName = document.createElement('span');
        layerName.className = 'layer-name';

        // 🔥 Better layer naming
        let displayName = obj.name || 'Layer ' + (canvasIndex + 1);
        if (obj.type === 'group' && obj.textContent) {
            displayName = 'Text: ' + obj.textContent.substring(0, 20);
        }
        layerName.textContent = displayName;

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'layer-remove-btn';
        removeBtn.title = 'Remove layer';
        removeBtn.innerHTML = '&times;';
        removeBtn.addEventListener('click', function(ev) {
            ev.stopPropagation();
            removeLayerAt(canvasIndex);
        });

        layerItem.appendChild(layerName);
        layerItem.appendChild(removeBtn);

        layerItem.addEventListener('click', function(ev) {
            if (ev.target.classList.contains('layer-remove-btn')) return;
            fabricCanvas.setActiveObject(obj);
            fabricCanvas.renderAll();
            updateLayerSelection();
        });

        layerItem.addEventListener('dragstart', function(ev) {
            ev.dataTransfer.setData('text/plain', String(canvasIndex));
            ev.dataTransfer.effectAllowed = 'move';
            layerItem.classList.add('layer-dragging');
        });

        layerItem.addEventListener('dragend', function() {
            layerItem.classList.remove('layer-dragging');
        });

        layerItem.addEventListener('dragover', function(ev) {
            ev.preventDefault();
            ev.dataTransfer.dropEffect = 'move';
            var t = ev.currentTarget;
            if (t && t.classList && !t.classList.contains('layer-drag-over'))
                t.classList.add('layer-drag-over');
        });

        layerItem.addEventListener('dragleave', function(ev) {
            ev.currentTarget.classList.remove('layer-drag-over');
        });

        layerItem.addEventListener('drop', function(ev) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('layer-drag-over');
            var fromIndex = parseInt(ev.dataTransfer.getData('text/plain'), 10);
            var toIndex = parseInt(ev.currentTarget.dataset.index, 10);
            if (fromIndex === toIndex) return;
            reorderLayer(fromIndex, toIndex);
        });

        layersList.appendChild(layerItem);
    });

    updateLayerSelection();
}

function removeLayerAt(canvasIndex) {
    const objects = fabricCanvas.getObjects();
    if (canvasIndex < 0 || canvasIndex >= objects.length) return;
    const obj = objects[canvasIndex];
    saveStateForUndo();
    if (obj === currentImage) currentImage = null;
    fabricCanvas.remove(obj);
    fabricCanvas.renderAll();
    updateLayers();
}

function reorderLayer(fromIndex, toIndex) {
    var objects = fabricCanvas.getObjects().slice();
    if (fromIndex < 0 || fromIndex >= objects.length || toIndex < 0 || toIndex >= objects.length) return;
    saveStateForUndo();
    var obj = objects[fromIndex];
    objects.splice(fromIndex, 1);
    var insertAt = toIndex > fromIndex ? toIndex - 1 : toIndex;
    objects.splice(insertAt, 0, obj);
    isReorderingLayers = true;
    objects.forEach(function(o) { fabricCanvas.remove(o); });
    objects.forEach(function(o) { fabricCanvas.add(o); });
    isReorderingLayers = false;
    fabricCanvas.renderAll();
    updateLayers();
    updateObjectSettingsPanel();
}

function updateLayerSelection() {
    const activeObject = fabricCanvas.getActiveObject();
    const layerItems = document.querySelectorAll('.layer-item');
    layerItems.forEach(item => item.classList.remove('active'));
    if (activeObject) {
        const index = fabricCanvas.getObjects().indexOf(activeObject);
        if (index !== -1) {
            const layerItem = document.querySelector('.layer-item[data-index="' + index + '"]');
            if (layerItem) layerItem.classList.add('active');
        }
    }
    updateTextSettingsPanel();
}

function updateLayerControlsBar() {
    var active = fabricCanvas.getActiveObject();
    var btnLeft = document.getElementById('layerCtrlRemove');
    var btnCenter = document.getElementById('layerCtrlAngle');
    var btnRight = document.getElementById('layerCtrlGear');
    if (!btnLeft || !btnCenter || !btnRight || !active) {
        hideLayerControlsBar();
        return;
    }
    var canvasEl = fabricCanvas.upperCanvasEl || fabricCanvas.lowerCanvasEl;
    if (!canvasEl) {
        hideLayerControlsBar();
        return;
    }
    var cr = canvasEl.getBoundingClientRect();
    var bound = active.getBoundingRect(true);
    var scaleX = cr.width / fabricCanvas.width;
    var scaleY = cr.height / fabricCanvas.height;
    var iconW = 36;
    var iconH = 36;
    var gap = 8;
    var topY = cr.top + bound.top * scaleY - iconH - gap;
    var leftX = cr.left + bound.left * scaleX;
    var centerX = cr.left + (bound.left + bound.width / 2) * scaleX - iconW / 2;
    var rightX = cr.left + (bound.left + bound.width) * scaleX - iconW;
    btnLeft.style.left = Math.max(8, leftX) + 'px';
    btnLeft.style.top = Math.max(8, topY) + 'px';
    btnLeft.style.display = 'block';
    btnCenter.style.left = Math.max(8, centerX) + 'px';
    btnCenter.style.top = Math.max(8, topY) + 'px';
    btnCenter.style.display = 'block';
    btnRight.style.left = Math.max(8, rightX) + 'px';
    btnRight.style.top = Math.max(8, topY) + 'px';
    btnRight.style.display = 'block';
    if (!btnLeft._bound) {
        btnLeft._bound = btnCenter._bound = btnRight._bound = true;
        function handleLayerCtrlClick(ev) {
            ev.stopPropagation();
            var action = ev.currentTarget.getAttribute('data-action');
            var obj = fabricCanvas.getActiveObject();
            if (!obj) return;
            if (action === 'remove') {
                if (obj === currentImage) currentImage = null;
                fabricCanvas.remove(obj);
                fabricCanvas.renderAll();
                hideLayerControlsBar();
                updateLayers();
            } else if (action === 'angle') {
                var a = (obj.angle || 0) + 15;
                obj.set('angle', a >= 360 ? 0 : a);
                fabricCanvas.renderAll();
                updateLayerControlsBar();
            } else if (action === 'gear') {
                var menu = document.getElementById('contextMenu');
                if (menu) {
                    menu.style.left = btnRight.style.left;
                    menu.style.top = (parseInt(btnRight.style.top, 10) + iconH + 4) + 'px';
                    menu.style.display = 'block';
                }
            }
        }
        btnLeft.addEventListener('click', handleLayerCtrlClick);
        btnCenter.addEventListener('click', handleLayerCtrlClick);
        btnRight.addEventListener('click', handleLayerCtrlClick);
    }
}

function hideLayerControlsBar() {
    ['layerCtrlRemove', 'layerCtrlAngle', 'layerCtrlGear'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
}

function updateCenterGuide(obj) {
    var guide = document.getElementById('canvasCenterGuide');
    if (!guide) return;
    if (!obj || !fabricCanvas) {
        guide.style.display = 'none';
        return;
    }
    var center;
    try {
        center = obj.getCenterPoint ? obj.getCenterPoint() : null;
    } catch (e) { center = null; }
    if (!center) {
        guide.style.display = 'none';
        return;
    }
    var canvasCenterX = fabricCanvas.width / 2;
    var canvasCenterY = fabricCanvas.height / 2;
    var dx = Math.abs(center.x - canvasCenterX);
    var dy = Math.abs(center.y - canvasCenterY);
    if (dx <= CENTER_GUIDE_TOLERANCE && dy <= CENTER_GUIDE_TOLERANCE) {
        guide.style.display = 'block';
    } else {
        guide.style.display = 'none';
    }
}

function hideCenterGuide() {
    var guide = document.getElementById('canvasCenterGuide');
    if (guide) guide.style.display = 'none';
}

function updateObjectSettingsPanel() {
    var objectPanel = document.getElementById('objectSettingsPanel');
    var hintEl = document.getElementById('rightPanelHint');
    var objectColorsGrid = document.getElementById('objectColorsGrid');
    var opacityInput = document.getElementById('objectOpacity');
    var opacityValue = document.getElementById('objectOpacityValue');
    if (!objectPanel || !hintEl) return;
    var activeObject = fabricCanvas.getActiveObject();
    if (!activeObject) {
        objectPanel.style.display = 'none';
        hintEl.style.display = 'none';
        rightSidebarView = 'layers';
        updateRightSidebarView();
        return;
    }
    openRightSidebar();
    rightSidebarView = 'object';
    updateRightSidebarView();
    hintEl.style.display = 'none';
    objectPanel.style.display = 'block';
    var opacity = activeObject.opacity != null ? Math.round(activeObject.opacity * 100) : 100;
    if (opacityInput) {
        opacityInput.value = opacity;
        opacityInput.disabled = false;
    }
    if (opacityValue) opacityValue.textContent = opacity + '%';
var isColorEditableImage =
    (activeObject.type === 'image' || activeObject.type === 'group')
    && selectedColorsForVector.length > 0;
    if (isColorEditableImage) {
        currentImage = activeObject;
        var storedMappings = activeObject.get && activeObject.get('colorMappings');
        if (storedMappings && typeof storedMappings === 'object') {
            colorMappings = {};
            for (var key in storedMappings) if (storedMappings.hasOwnProperty(key)) colorMappings[key] = storedMappings[key];
        }
        if (objectColorsGrid) displayColorPickers('objectColorsGrid');
    } else {
        if (objectColorsGrid) objectColorsGrid.innerHTML = '<p class="empty-colors-hint">No color edit for this object.</p>';
    }
    if (!objectPanel._objectSettingsBound) {
        objectPanel._objectSettingsBound = true;
        if (opacityInput) {
            opacityInput.addEventListener('input', function() {
                var obj = fabricCanvas.getActiveObject();
                if (!obj) return;
                var v = parseInt(opacityInput.value, 10) || 100;
                obj.set('opacity', v / 100);
                fabricCanvas.renderAll();
                if (opacityValue) opacityValue.textContent = v + '%';
            });
        }
        var moveStep = 10;
        function moveSelected(dx, dy) {
            var obj = fabricCanvas.getActiveObject();
            if (!obj) return;
            saveStateForUndo();
            obj.set({ left: (obj.left || 0) + dx, top: (obj.top || 0) + dy });
            fabricCanvas.renderAll();
            updateLayerControlsBar();
        }
        var moveLeftBtn = document.getElementById('moveLeftBtn');
        var moveRightBtn = document.getElementById('moveRightBtn');
        var moveUpBtn = document.getElementById('moveUpBtn');
        var moveDownBtn = document.getElementById('moveDownBtn');
        if (moveLeftBtn) moveLeftBtn.addEventListener('click', function() { moveSelected(-moveStep, 0); });
        if (moveRightBtn) moveRightBtn.addEventListener('click', function() { moveSelected(moveStep, 0); });
        if (moveUpBtn) moveUpBtn.addEventListener('click', function() { moveSelected(0, -moveStep); });
        if (moveDownBtn) moveDownBtn.addEventListener('click', function() { moveSelected(0, moveStep); });
    }
}

function updateTextSettingsPanel() {
    const panel = document.getElementById('textSettingsPanel');
    const objectPanel = document.getElementById('objectSettingsPanel');
    const hintEl = document.getElementById('rightPanelHint');
    if (!panel) return;
    const activeObject = fabricCanvas.getActiveObject();
    const isFlatText = activeObject && (activeObject.type === 'i-text' || activeObject.type === 'itext' || activeObject.type === 'text');
    const isShapedGroup = activeObject && activeObject.type === 'group' && activeObject.textShape;
    const isText = isFlatText || isShapedGroup;
    if (!isText) {
        panel.style.display = 'none';
        updateObjectSettingsPanel();
        return;
    }
    openRightSidebar();
    rightSidebarView = 'object';
    updateRightSidebarView();
    panel.style.display = 'block';
    if (objectPanel) objectPanel.style.display = 'none';
    if (hintEl) hintEl.style.display = 'none';
    const textContentEl = document.getElementById('textContent');
    const textFontStyle = document.getElementById('textFontStyle');
    const textShapeEl = document.getElementById('textShape');
    const fillColor = document.getElementById('textFillColor');
    const fontFamily = document.getElementById('textFontFamily');
    const fontSize = document.getElementById('textFontSize');
    const bold = document.getElementById('textBold');
    const strokeColor = document.getElementById('textStrokeColor');
    const strokeWidth = document.getElementById('textStrokeWidth');
    const paintFirst = document.getElementById('textPaintFirst');
    const charSpacing = document.getElementById('textCharSpacing');
    const lineHeight = document.getElementById('textLineHeight');
    const textAlign = document.getElementById('textAlign');
    if (textContentEl) textContentEl.value = isFlatText ? (activeObject.text || '') : (activeObject.textContent || '');
    if (textFontStyle) textFontStyle.value = (activeObject.fontStyle || 'normal');
    if (textShapeEl) textShapeEl.value = (activeObject.textShape || 'normal');
    syncTextShapeTrigger();
    if (!fillColor) return;
    const hex = (c) => { const h = c.toString(16); return h.length === 1 ? '0' + h : h; };
    const toHex = (color) => color ? '#' + hex(color.r) + hex(color.g) + hex(color.b) : '#000000';
    const fill = activeObject.fill;
    fillColor.value = typeof fill === 'string' ? (fill.indexOf('#') === 0 ? fill : '#000000') : toHex(fill);
    if (fontFamily) fontFamily.value = activeObject.fontFamily || 'Arial';
    if (fontSize) fontSize.value = activeObject.fontSize || 40;
    if (bold) bold.checked = activeObject.fontWeight === 'bold';
    const stroke = activeObject.stroke;
    if (strokeColor) strokeColor.value = typeof stroke === 'string' ? (stroke.indexOf('#') === 0 ? stroke : '#000000') : (stroke ? toHex(stroke) : '#000000');
    if (strokeWidth) strokeWidth.value = activeObject.strokeWidth || 0;
    if (paintFirst) paintFirst.value = activeObject.paintFirst || 'fill';
    if (charSpacing) charSpacing.value = activeObject.charSpacing || 0;
    if (lineHeight) lineHeight.value = activeObject.lineHeight != null ? activeObject.lineHeight : 1.2;
    if (textAlign) textAlign.value = activeObject.textAlign || 'left';
    if (!panel._textSettingsBound) {
        panel._textSettingsBound = true;
        const apply = () => {
            var obj = fabricCanvas.getActiveObject();
            if (!obj) return;
            var str = textContentEl ? textContentEl.value : '';
            var shape = textShapeEl ? textShapeEl.value : 'normal';
            var opts = {
fontFamily: obj.fontFamily || (fontFamily ? fontFamily.value : 'Arial'),
                fontSize: parseInt(fontSize.value, 10) || 40,
                fill: fillColor ? fillColor.value : '#000000',
                fontWeight: bold && bold.checked ? 'bold' : 'normal',
                fontStyle: textFontStyle ? textFontStyle.value : 'normal',
                stroke: strokeColor ? strokeColor.value : '',
                strokeWidth: parseInt(strokeWidth.value, 10) || 0,
                paintFirst: paintFirst ? paintFirst.value : 'fill',
                charSpacing: parseInt(charSpacing.value, 10) || 0,
                lineHeight: parseFloat(lineHeight.value) || 1.2,
                textAlign: textAlign ? textAlign.value : 'left',
                name: obj.name
            };
            var left = obj.left || 0, top = obj.top || 0, angle = obj.angle || 0;
            if (obj.type !== 'group' && obj.getBoundingRect) {
                var b = obj.getBoundingRect(true);
                left = b.left + b.width / 2;
                top = b.top + b.height / 2;
            }
            var willReplace = (shape === 'normal' && obj.type === 'group' && obj.textShape) || (shape !== 'normal');
            if (willReplace) saveStateForUndo();
            if (shape === 'normal') {
                if (obj.type === 'group' && obj.textShape) {
                    var FlatTextClass = fabric.IText || fabric.Text;
                    var flat = new FlatTextClass(str, opts);
                    var br = flat.getBoundingRect(true);
                    flat.set({ left: left - br.width / 2, top: top - br.height / 2, angle: angle });
                    fabricCanvas.remove(obj);
                    fabricCanvas.add(flat);
                    fabricCanvas.setActiveObject(flat);
                } else {
                    obj.set('text', str);
                    obj.set('fontStyle', opts.fontStyle);
                    obj.set('fill', opts.fill);
                    obj.set('fontFamily', opts.fontFamily);
                    obj.set('fontSize', opts.fontSize);
                    obj.set('fontWeight', opts.fontWeight);
                    obj.set('stroke', opts.stroke);
                    obj.set('strokeWidth', opts.strokeWidth);
                    obj.set('paintFirst', opts.paintFirst);
                    obj.set('charSpacing', opts.charSpacing);
                    obj.set('lineHeight', opts.lineHeight);
                    obj.set('textAlign', opts.textAlign);
                }
            } else {
                var newGroup = createShapedTextGroup(shape, str, opts);
                if (newGroup) {
                    newGroup.set({ left: left, top: top, angle: angle });
                    fabricCanvas.remove(obj);
                    fabricCanvas.add(newGroup);
                    fabricCanvas.setActiveObject(newGroup);
                }
            }
            fabricCanvas.renderAll();
            updateLayers();
            updateLayerControlsBar();
        };
        var trigger = document.getElementById('textShapeTrigger');
        var optionsPanel = document.getElementById('textShapeOptions');
        if (trigger && optionsPanel) {
            trigger.addEventListener('click', function() {
                optionsPanel.classList.toggle('open');
                trigger.setAttribute('aria-expanded', optionsPanel.classList.contains('open'));
            });
            optionsPanel.querySelectorAll('.text-shape-option').forEach(function(opt) {
                opt.addEventListener('click', function() {
                    var v = opt.getAttribute('data-value');
                    if (textShapeEl) textShapeEl.value = v;
                    syncTextShapeTrigger();
                    optionsPanel.classList.remove('open');
                    trigger.setAttribute('aria-expanded', 'false');
                    apply();
                });
            });
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.text-shape-dropdown')) optionsPanel.classList.remove('open');
            });
        }
        [textContentEl, textFontStyle, textShapeEl, fillColor, fontFamily, fontSize, bold, strokeColor, strokeWidth, paintFirst, charSpacing, lineHeight, textAlign].forEach(function(el) {
            if (!el) return;
            el.addEventListener('change', apply);
            if (el.type === 'number') el.addEventListener('input', apply);
        });
        if (textContentEl) textContentEl.addEventListener('input', apply);
    }



    // 🔥 Text fill click → backend modal
if(fillColor){
    fillColor.addEventListener('click',function(e){
        e.preventDefault();
        backendTextMode = 'fill';
        openBackendColorModal('#text');
    });
}

// 🔥 Text stroke click → backend modal
if(strokeColor){
    strokeColor.addEventListener('click',function(e){
        e.preventDefault();
        backendTextMode = 'stroke';
        openBackendColorModal('#text');
    });
}

}

function syncTextShapeTrigger() {
    var sel = document.getElementById('textShape');
    var triggerLabel = document.getElementById('textShapeTriggerLabel');
    var triggerIcon = document.getElementById('textShapeTriggerIcon');
    var optionsPanel = document.getElementById('textShapeOptions');
    if (!sel || !triggerLabel || !triggerIcon || !optionsPanel) return;
    var val = sel.value;
    var opt = optionsPanel.querySelector('.text-shape-option[data-value="' + val + '"]');
    optionsPanel.querySelectorAll('.text-shape-option').forEach(function(o) { o.classList.remove('selected'); });
    if (opt) {
        opt.classList.add('selected');
        var iconEl = opt.querySelector('.shape-icon');
        triggerIcon.textContent = iconEl ? iconEl.textContent : '';
        triggerLabel.textContent = (iconEl ? opt.textContent.replace(iconEl.textContent, '').trim() : opt.textContent.trim());
    } else {
        triggerLabel.textContent = val;
    }
}
function createShapedTextGroup(shape, str, opts) {
    if (!str) return null;

    var chars = str.split('');
    var TextClass = fabric.Text;  // 🔥 Use fabric.Text, not IText
    var radius = Math.max(80, chars.length * (opts.fontSize || 24) * 0.5);
    var items = [];

    for (var i = 0; i < chars.length; i++) {
        var t = new TextClass(chars[i], {
            fontFamily: opts.fontFamily || 'Arial',
            fontSize: opts.fontSize || 24,
            fill: opts.fill || '#000000',
            fontWeight: opts.fontWeight || 'normal',
            fontStyle: opts.fontStyle || 'normal',
            stroke: opts.stroke || '',
            strokeWidth: opts.strokeWidth || 0,
            paintFirst: opts.paintFirst || 'fill',
            originX: 'center',
            originY: 'center',
            selectable: false,
            evented: false,
            hasControls: false,
            hasBorders: false,
            lockMovementX: true,
            lockMovementY: true,
            lockScalingX: true,
            lockScalingY: true,
            lockRotation: true,
            hoverCursor: 'default'  // 🔥 No pointer on letters
        });

        var angleStep, baseAngle, x, y, rot;

        if (shape === 'arch') {
            angleStep = Math.PI / Math.max(1, chars.length + 1);
            baseAngle = Math.PI;
            var a = baseAngle + angleStep * (i + 1);
            x = radius * Math.cos(a);
            y = radius * Math.sin(a);
            rot = (a - Math.PI / 2) * 180 / Math.PI;
        } else if (shape === 'arcDown') {
            angleStep = Math.PI / Math.max(1, chars.length + 1);
            baseAngle = 0;
            var a2 = baseAngle + angleStep * (i + 1);
            x = radius * Math.cos(a2);
            y = radius * Math.sin(a2);
            rot = (a2 + Math.PI / 2) * 180 / Math.PI;
        } else if (shape === 'circle') {
            angleStep = (2 * Math.PI) / chars.length;
            var a3 = -Math.PI / 2 + i * angleStep;
            x = radius * Math.cos(a3);
            y = radius * Math.sin(a3);
            rot = (a3 * 180 / Math.PI) + 90;
        } else if (shape === 'wave') {
            var step = (opts.fontSize || 24) * 1.2;
            x = (i - (chars.length - 1) / 2) * step;
            y = Math.sin(i * 0.5) * (opts.fontSize || 24) * 0.8;
            rot = 0;
        } else if (shape === 'zigzag') {
            var stepZ = (opts.fontSize || 24) * 1.1;
            x = (i - (chars.length - 1) / 2) * stepZ;
            y = (i % 2 === 0 ? 0 : (opts.fontSize || 24) * 0.9);
            rot = 0;
        } else if (shape === 'curveUp') {
            var stepC = (opts.fontSize || 24) * 1.2;
            x = (i - (chars.length - 1) / 2) * stepC;
            y = -Math.abs(Math.sin(i * 0.4)) * (opts.fontSize || 24) * 1.2;
            rot = 0;
        } else if (shape === 'curveDown') {
            var stepD = (opts.fontSize || 24) * 1.2;
            x = (i - (chars.length - 1) / 2) * stepD;
            y = Math.abs(Math.sin(i * 0.4)) * (opts.fontSize || 24) * 1.2;
            rot = 0;
        } else if (shape === 'semicircleTop') {
            angleStep = Math.PI / Math.max(1, chars.length + 1);
            var a4 = Math.PI + angleStep * (i + 1);
            x = radius * Math.cos(a4);
            y = radius * Math.sin(a4);
            rot = (a4 - Math.PI / 2) * 180 / Math.PI;
        } else if (shape === 'semicircleBottom') {
            angleStep = Math.PI / Math.max(1, chars.length + 1);
            var a5 = angleStep * (i + 1);
            x = radius * Math.cos(a5);
            y = radius * Math.sin(a5);
            rot = (a5 + Math.PI / 2) * 180 / Math.PI;
        } else if (shape === 'ellipse') {
            angleStep = (2 * Math.PI) / chars.length;
            var a6 = -Math.PI / 2 + i * angleStep;
            var rx = radius * 1.4, ry = radius * 0.7;
            x = rx * Math.cos(a6);
            y = ry * Math.sin(a6);
            rot = (a6 * 180 / Math.PI) + 90;
        } else if (shape === 'fan') {
            angleStep = (Math.PI * 0.7) / Math.max(1, chars.length);
            var a7 = Math.PI * 0.65 + angleStep * i;
            var rFan = radius * (0.6 + 0.4 * i / chars.length);
            x = rFan * Math.cos(a7);
            y = rFan * Math.sin(a7);
            rot = (a7 - Math.PI / 2) * 180 / Math.PI;
        } else if (shape === 'bulge') {
            var stepB = (opts.fontSize || 24) * 1.2;
            x = (i - (chars.length - 1) / 2) * stepB;
            var mid = (chars.length - 1) / 2;
            y = -Math.sin(((i - mid) / Math.max(1, mid)) * Math.PI * 0.5) * (opts.fontSize || 24) * 1.2;
            rot = 0;
        } else if (shape === 'pinch') {
            var stepP = (opts.fontSize || 24) * 1.2;
            x = (i - (chars.length - 1) / 2) * stepP;
            var midP = (chars.length - 1) / 2;
            y = Math.sin(((i - midP) / Math.max(1, midP)) * Math.PI * 0.5) * (opts.fontSize || 24) * 1.2;
            rot = 0;
        } else if (shape === 'spiral') {
            angleStep = (2 * Math.PI) / Math.max(1, chars.length);
            var a8 = -Math.PI / 2 + i * angleStep;
            var rSpiral = radius * (0.3 + 0.7 * i / chars.length);
            x = rSpiral * Math.cos(a8);
            y = rSpiral * Math.sin(a8);
            rot = (a8 * 180 / Math.PI) + 90;
        } else if (shape === 'rainbow') {
            angleStep = Math.PI / Math.max(1, chars.length + 1);
            var a9 = Math.PI * 0.2 + angleStep * (i + 1);
            x = radius * Math.cos(a9);
            y = radius * Math.sin(a9) * 0.6 - (opts.fontSize || 24) * 0.3;
            rot = (a9 - Math.PI / 2) * 180 / Math.PI;
        } else {
            x = (i - (chars.length - 1) / 2) * (opts.fontSize || 24) * 0.7;
            y = 0;
            rot = 0;
        }

        t.set({ left: x, top: y, angle: rot });
        items.push(t);
    }

    // 🔥 CRITICAL - Proper group creation
    var group = new fabric.Group(items, {
        originX: 'center',
        originY: 'center',
        subTargetCheck: false,     // ✅ No sub-selection
        interactive: false,        // ✅ Not interactive
        selectable: true,
        hasControls: true,
        hasBorders: true,
        lockUniScaling: false,
        objectCaching: false       // 🔥 Disable caching
    });

    // 🔥 Mark all children as unselectable
    group.forEachObject(function(obj) {
        obj.selectable = false;
        obj.evented = false;
    });

    group.textShape = shape;
    group.textContent = str;
    group.fontFamily = opts.fontFamily;
    group.fontSize = opts.fontSize;
    group.fill = opts.fill;
    group.fontWeight = opts.fontWeight;
    group.fontStyle = opts.fontStyle;
    group.stroke = opts.stroke;
    group.strokeWidth = opts.strokeWidth;
    group.paintFirst = opts.paintFirst;
    group.charSpacing = opts.charSpacing;
    group.lineHeight = opts.lineHeight;
    group.textAlign = opts.textAlign;
    group.name = opts.name || ('Text ' + (++layerCounter));

    return group;
}

function saveDesign() {
    const objects = fabricCanvas.getObjects();
    if (objects.length === 0) {
        alert('No design to save. Please add some elements first.');
        return;
    }

    // Open save dialog
    openSaveDesignDialog();
}

function openSaveDesignDialog() {
    document.getElementById('saveDesignModal').style.display = 'flex';
    document.getElementById('designTitle').value = '';
    document.getElementById('designTitle').focus();
}


function closeSaveDesignDialog() {
    document.getElementById('saveDesignModal').style.display = 'none';
}

function saveDesignToSVG(){

 let title = document.getElementById('designTitle').value;
 if(!title){
   alert("Title required");
   return;
 }

 const saveBtn = document.getElementById('saveDesignBtn');
 const btnText = document.getElementById('saveBtnText');
 const btnSpinner = document.getElementById('saveBtnSpinner');

 // 🔥 Spinner ON
 saveBtn.disabled = true;
 btnText.style.display = "none";
 btnSpinner.style.display = "inline-block";

 const svgData = fabricCanvas.toSVG({ suppressPreamble:false });
 const imageData = fabricCanvas.toDataURL({ format:'png', quality:1 });

 const templateId = document.getElementById('editingTemplateId')?.value;

 fetch(templateId ? `/templates/${templateId}` : '/templates/save-from-customizer', {
   method:'POST',
   headers:{
     'Content-Type':'application/json',
     'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
   },
   body:JSON.stringify({
     _method: templateId ? 'PUT' : 'POST',
     title:title,
     svg_data:svgData,
     image_data:imageData
   })
 })
 .then(r=>r.json())
 .then(data=>{
   if(data.success){

     // ✅ DIRECT INDEX PAGE
     window.location.href = "/templates";

   }else{
     resetBtn();
     alert("Save failed");
   }
 })
 .catch(err=>{
   console.error(err);
   resetBtn();
   alert("Server error");
 });

 function resetBtn(){
   saveBtn.disabled=false;
   btnSpinner.style.display="none";
   btnText.style.display="inline";
 }
}



