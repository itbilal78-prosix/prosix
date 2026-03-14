{{-- =================== MASCOT SELECTION MODAL =================== --}}
{{-- Opens when user selects "Custom Mascot" type and clicks OK in Add Application modal --}}

<div id="mascotSelectModal"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center;">
    <div
        style="background:#fff; width:1100px; max-width:96vw; height:85vh; border-radius:12px; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,0.4);">

        {{-- ===== HEADER ===== --}}
        <div
            style="padding:18px 24px; background:#1a1a1a; color:#fff; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
            <h3 style="margin:0; font-size:17px; font-weight:700; letter-spacing:1px;">MASCOTS</h3>
            <span onclick="closeMascotSelectModal()"
                style="cursor:pointer; font-size:26px; line-height:1; opacity:.8; transition:opacity .2s;"
                onmouseenter="this.style.opacity=1" onmouseleave="this.style.opacity=.8">×</span>
        </div>

        {{-- ===== TABS ===== --}}
        <div style="display:flex; border-bottom:2px solid #e8e8e8; flex-shrink:0; background:#fafafa;">
            <button id="msTab1" onclick="switchMascotSelectTab('existing')"
                style="padding:14px 28px; border:none; background:transparent; font-weight:700; font-size:13px; cursor:pointer; border-bottom:3px solid #1a1a1a; color:#1a1a1a; transition:all .2s; letter-spacing:.5px;">
                Select Existing Mascot
            </button>
            <button id="msTab2" onclick="switchMascotSelectTab('create')"
                style="padding:14px 28px; border:none; background:transparent; font-weight:700; font-size:13px; cursor:pointer; border-bottom:3px solid transparent; color:#999; transition:all .2s; letter-spacing:.5px;">
                Create Custom Mascot
            </button>
            <button id="msTab3" onclick="switchMascotSelectTab('upload')"
                style="padding:14px 28px; border:none; background:transparent; font-weight:700; font-size:13px; cursor:pointer; border-bottom:3px solid transparent; color:#999; transition:all .2s; letter-spacing:.5px;">
                Upload Your Own Mascot File
            </button>
        </div>

        {{-- ===== BODY ===== --}}
        <div style="flex:1; overflow:hidden; display:flex;">

            {{-- ========== TAB 1: SELECT EXISTING ========== --}}
            <div id="msContent1" style="display:flex; width:100%; height:100%;">

                {{-- LEFT: Category sidebar --}}
                <div style="width:220px; border-right:1px solid #e8e8e8; overflow-y:auto; flex-shrink:0; background:#fafafa;">
                    <div style="padding:12px 16px; font-size:11px; font-weight:700; color:#999; letter-spacing:1px; border-bottom:1px solid #eee;">
                        CATEGORIES
                    </div>
                    <div id="mascotCategoryList" style="padding:8px 0;">
                        <div class="ms-cat-item ms-cat-active" data-category="all"
                            onclick="filterMascotCategory('all', this)"
                            style="padding:10px 16px; cursor:pointer; font-size:13px; font-weight:600; background:#1a1a1a; color:#fff;">
                            All Categories
                        </div>
                    </div>
                    <div style="padding:12px;">
                        <button onclick="toggleMascotDesignIdeas()"
                            style="width:100%; padding:10px; background:#fff; border:2px solid #1a1a1a; border-radius:6px; font-weight:700; font-size:12px; cursor:pointer; letter-spacing:.5px;">
                            Design Ideas
                        </button>
                    </div>
                </div>

                {{-- CENTER: Mascot grid --}}
                <div style="flex:1; display:flex; flex-direction:column; overflow:hidden;">
                    <div style="padding:12px 16px; border-bottom:1px solid #eee; flex-shrink:0;">
                        <div style="position:relative;">
                            <input type="text" id="mascotSearchInput" placeholder="Search mascot..."
                                oninput="searchMascots(this.value)"
                                style="width:100%; padding:9px 36px 9px 14px; border:1.5px solid #ddd; border-radius:8px; font-size:13px; outline:none; box-sizing:border-box; transition:border .2s;"
                                onfocus="this.style.borderColor='#1a1a1a'" onblur="this.style.borderColor='#ddd'">
                            <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:16px;">⌕</span>
                        </div>
                    </div>
                    <div id="mascotSelectGrid"
                        style="flex:1; overflow-y:auto; padding:16px; display:grid; grid-template-columns:repeat(auto-fill,minmax(110px,1fr)); gap:12px; align-content:start;">
                        <div style="grid-column:1/-1; text-align:center; padding:40px; color:#aaa; font-size:13px;">
                            Loading mascots...
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Preview panel --}}
                <div style="width:200px; border-left:1px solid #e8e8e8; display:flex; flex-direction:column; flex-shrink:0; background:#fafafa;">
                    <div style="padding:12px 16px; font-size:11px; font-weight:700; color:#999; letter-spacing:1px; border-bottom:1px solid #eee;">
                        PREVIEW
                    </div>
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:16px; gap:12px;">
                        <div id="mascotSelectPreviewBox"
                            style="width:130px; height:130px; background:#f0f0f0; border-radius:10px; display:flex; align-items:center; justify-content:center; border:2px dashed #ccc;">
                            <span style="color:#ccc; font-size:12px; text-align:center; line-height:1.4;">Select a<br>mascot</span>
                        </div>
                        <div id="mascotSelectPreviewName" style="font-size:13px; font-weight:700; color:#333; text-align:center;"></div>
                        <button id="mascotEditBtn" onclick="editSelectedMascot()"
                            style="display:none; width:100%; padding:10px; background:#1a1a1a; color:#fff; border:none; border-radius:6px; font-weight:700; font-size:12px; cursor:pointer; letter-spacing:.5px;">
                            Edit Mascots
                        </button>
                    </div>
                </div>
            </div>

            {{-- ========== TAB 2: CREATE CUSTOM (redirect) ========== --}}
            <div id="msContent2" style="display:none; width:100%; height:100%;"></div>

            {{-- ========== TAB 3: UPLOAD ========== --}}
            <div id="msContent3" style="display:none; width:100%; height:100%; overflow-y:auto; padding:32px;">
                <div style="max-width:560px; margin:0 auto;">
                    <h4 style="margin:0 0 8px 0; font-size:18px; font-weight:700;">Upload Your Own Mascot</h4>
                    <p style="color:#777; font-size:13px; margin-bottom:28px;">Upload an SVG or PNG file from your computer.</p>

<div id="mascotDropZone"
    onclick="(function(){ var inp = document.getElementById('mascotFileInput'); inp.value=''; inp.click(); })()"
                            ondragover="mascotDragOver(event)" ondragleave="mascotDragLeave(event)" ondrop="mascotDrop(event)"
                        style="border:2px dashed #ccc; border-radius:12px; padding:48px 24px; text-align:center; cursor:pointer; transition:all .2s; background:#fafafa;">
                        <div style="font-size:42px; margin-bottom:14px;">📁</div>
                        <div style="font-weight:700; font-size:15px; color:#333; margin-bottom:6px;">Drop your file here</div>
                        <div style="font-size:12px; color:#999; margin-bottom:14px;">or click to browse</div>
                        <div style="display:inline-block; padding:8px 20px; background:#1a1a1a; color:#fff; border-radius:6px; font-size:12px; font-weight:700; letter-spacing:.5px;">
                            Browse Files
                        </div>
                        <div style="margin-top:12px; font-size:11px; color:#bbb;">Supported: SVG, PNG, JPG (max 5MB)</div>
                    </div>

              <input type="file" id="mascotFileInput" accept="image/*,.svg" style="display:none;" onchange="mascotFileSelected(this)">

                    <div id="mascotUploadPreview" style="display:none; margin-top:24px; padding:20px; background:#f5f5f5; border-radius:10px;">
                        <div style="display:flex; align-items:center; gap:16px;">
                            <div id="mascotUploadThumb"
                                style="width:80px; height:80px; background:#e0e0e0; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden;"></div>
                            <div style="flex:1;">
                                <div id="mascotUploadFileName" style="font-weight:700; font-size:13px; margin-bottom:4px; color:#333;"></div>
                                <div id="mascotUploadFileSize" style="font-size:11px; color:#999;"></div>
                            </div>
                            <button onclick="clearMascotUpload()" style="background:none; border:none; font-size:20px; cursor:pointer; color:#999;">×</button>
                        </div>
                        <button onclick="applyUploadedMascotFile()"
                            style="width:100%; margin-top:16px; padding:12px; background:#1a1a1a; color:#fff; border:none; border-radius:6px; font-weight:700; cursor:pointer; font-size:13px; letter-spacing:.5px;">
                            Use This Mascot
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== FOOTER ===== --}}
        <div style="padding:16px 24px; border-top:1px solid #e8e8e8; display:flex; justify-content:flex-end; gap:12px; flex-shrink:0; background:#fafafa;">
            <button onclick="closeMascotSelectModal()"
                style="padding:12px 28px; background:#fff; border:2px solid #ccc; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; color:#555; letter-spacing:.5px;">
                CANCEL
            </button>
            <button onclick="applySelectedMascotToApplication()"
                style="padding:12px 28px; background:#1a1a1a; color:#fff; border:none; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; letter-spacing:.5px;">
                APPLY
            </button>
        </div>

    </div>
</div>

<style>
.ms-cat-item {
    padding: 10px 16px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: #444;
    transition: background .15s;
    border-left: 3px solid transparent;
}
.ms-cat-item:hover { background: #f0f0f0; }
.ms-cat-active { background: #1a1a1a !important; color: #fff !important; border-left-color: #1a1a1a !important; }

.ms-mascot-card {
    border: 2px solid #e8e8e8;
    border-radius: 8px;
    padding: 10px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    background: #fff;
}
.ms-mascot-card:hover { border-color: #1a1a1a; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
.ms-mascot-card.ms-selected { border-color: #1a1a1a; border-width: 3px; background: #f8f8f8; }
.ms-mascot-card img { width: 100%; height: 80px; object-fit: contain; background: #f5f5f5; border-radius: 4px; }
.ms-mascot-card p { margin: 6px 0 0; font-size: 11px; font-weight: 600; color: #444; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>

<script>
// ============================================================
// MASCOT SELECT MODAL — ALL functions defined on window DIRECTLY
// (NO IIFE wrapping — this fixes "not a function" error)
// ============================================================


window._mascotState = {
    allMascots: [],
    filteredMascots: [],
    selectedMascotData: null,
    currentTab: 'existing',
    pendingLayerId: null,
    uploadedFile: null
};

// ======= OPEN =======
window.openMascotSelectModal = function(layerId) {
    layerId = layerId || window.currentApplicationLayer;

    const modal = document.getElementById('mascotSelectModal');
    if (!modal) {
        console.error('❌ mascotSelectModal not found in DOM');
        return;
    }

    window._mascotState.pendingLayerId = layerId;
    window._mascotState.selectedMascotData = null;

    // Force open
    modal.style.display = 'flex';
    modal.style.position = 'fixed';
    modal.style.zIndex = '99999';

    // Reset to existing tab
    switchMascotSelectTab('existing');

    // Load mascots
    _loadMascotTemplates();

    console.log('✅ Mascot modal opened for layer:', layerId);
};

// ======= CLOSE =======
window.closeMascotSelectModal = function() {
    const modal = document.getElementById('mascotSelectModal');
    if (modal) modal.style.display = 'none';
    window._mascotState.selectedMascotData = null;
};

// ======= TABS =======
window.switchMascotSelectTab = function(tab) {
    if (tab === 'create') {
        closeMascotSelectModal();
        window.location.href = '/admin/mascots/create';
        return;
    }

    window._mascotState.currentTab = tab;

    const tabIds   = { existing: 'msTab1', create: 'msTab2', upload: 'msTab3' };
    const contIds  = { existing: 'msContent1', create: 'msContent2', upload: 'msContent3' };

    Object.keys(tabIds).forEach(function(t) {
        const btn = document.getElementById(tabIds[t]);
        if (btn) {
            btn.style.borderBottom = (t === tab) ? '3px solid #1a1a1a' : '3px solid transparent';
            btn.style.color        = (t === tab) ? '#1a1a1a' : '#999';
        }
        const cont = document.getElementById(contIds[t]);
        if (cont) {
            cont.style.display = (t === tab) ? (t === 'existing' ? 'flex' : 'block') : 'none';
        }
    });
};

// ======= LOAD MASCOTS =======
function _loadMascotTemplates() {
    const grid = document.getElementById('mascotSelectGrid');
    if (grid) grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;color:#aaa;font-size:13px;">Loading mascots...</div>';

    fetch('/api/mascot-templates')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            window._mascotState.allMascots = data;
            window._mascotState.filteredMascots = data;
            _buildCategoryList(data);
            _renderMascotGrid(data);
        })
        .catch(function() {
            const grid = document.getElementById('mascotSelectGrid');
            if (grid) grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;color:#aaa;font-size:13px;">Could not load mascots.</div>';
        });
}

function _buildCategoryList(mascots) {
    const categories = [...new Set(mascots.map(function(m) { return m.category; }).filter(Boolean))];
    const list = document.getElementById('mascotCategoryList');
    if (!list) return;

    list.innerHTML = '<div class="ms-cat-item ms-cat-active" data-category="all" onclick="filterMascotCategory(\'all\', this)">All Categories</div>';

    categories.forEach(function(cat) {
        const div = document.createElement('div');
        div.className = 'ms-cat-item';
        div.dataset.category = cat;
        div.textContent = cat;
        div.onclick = function() { filterMascotCategory(cat, this); };
        list.appendChild(div);
    });
}

window.filterMascotCategory = function(category, el) {
    document.querySelectorAll('.ms-cat-item').forEach(function(i) { i.classList.remove('ms-cat-active'); });
    if (el) el.classList.add('ms-cat-active');

    const state = window._mascotState;
    state.filteredMascots = (category === 'all') ? state.allMascots : state.allMascots.filter(function(m) { return m.category === category; });
    _renderMascotGrid(state.filteredMascots);
};

window.searchMascots = function(query) {
    const q = query.toLowerCase().trim();
    const state = window._mascotState;
    const base = state.filteredMascots.length ? state.filteredMascots : state.allMascots;
    const results = q ? base.filter(function(m) { return m.title && m.title.toLowerCase().includes(q); }) : base;
    _renderMascotGrid(results);
};

function _renderMascotGrid(mascots) {
    const grid = document.getElementById('mascotSelectGrid');
    if (!grid) return;

    if (!mascots.length) {
        grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;color:#aaa;font-size:13px;">No mascots found.</div>';
        return;
    }

    grid.innerHTML = '';
    mascots.forEach(function(m) {
        const card = document.createElement('div');
        card.className = 'ms-mascot-card';
        card.dataset.id = m.id;
        card.innerHTML = (m.image_data
            ? '<img src="' + m.image_data + '" style="width:100%;height:80px;object-fit:contain;">'
            : '<div style="width:100%;height:80px;overflow:hidden;">' + (m.svg_data || '') + '</div>')
            + '<p>' + (m.title || 'Untitled') + '</p>';

        card.onclick = function() { _selectMascotCard(m, card); };
        grid.appendChild(card);
    });
}

function _selectMascotCard(mascot, cardEl) {
    document.querySelectorAll('.ms-mascot-card').forEach(function(c) { c.classList.remove('ms-selected'); });
    cardEl.classList.add('ms-selected');

    window._mascotState.selectedMascotData = {
        svg:      mascot.svg_data  || '',
        imageUrl: mascot.image_data || '',
        title:    mascot.title,
        source:   'existing',
        mascotDbId: mascot.id
    };

    // Preview
    const previewBox = document.getElementById('mascotSelectPreviewBox');
    if (previewBox) {
        previewBox.innerHTML = '';
        previewBox.style.border = '2px solid #1a1a1a';
        if (mascot.svg_data && mascot.svg_data.trim().startsWith('<')) {
            previewBox.innerHTML = mascot.svg_data;
            const svg = previewBox.querySelector('svg');
            if (svg) { svg.style.width = '110px'; svg.style.height = '110px'; svg.style.display = 'block'; }
        } else if (mascot.image_data) {
            previewBox.innerHTML = '<img src="' + mascot.image_data + '" style="width:110px;height:110px;object-fit:contain;">';
        } else {
            previewBox.innerHTML = '<span style="color:#ccc;font-size:11px;">No preview</span>';
        }
    }

    const nameEl = document.getElementById('mascotSelectPreviewName');
    if (nameEl) nameEl.textContent = mascot.title || '';

    const editBtn = document.getElementById('mascotEditBtn');
    if (editBtn) editBtn.style.display = 'block';
}

window.editSelectedMascot = function() {
    const data = window._mascotState.selectedMascotData;
    if (!data) { alert('Please select a mascot first.'); return; }

    const selectedCard = document.querySelector('.ms-mascot-card.ms-selected');
    const mascotId = selectedCard ? selectedCard.dataset.id : null;
    if (!mascotId) { alert('Mascot ID not found.'); return; }

    try {
        localStorage.setItem('editMascotSvg', data.svg || '');
        localStorage.setItem('editMascotTitle', data.title || '');
    } catch(e) {}

    closeMascotSelectModal();
    window.location.href = '/admin/mascots/' + mascotId + '/edit';
};

window.toggleMascotDesignIdeas = function() {
    if (window.openDesignIdeas) window.openDesignIdeas();
};

// ======= APPLY =======
window.applySelectedMascotToApplication = function() {
    const state = window._mascotState;
    const mascotData = state.selectedMascotData;

    if (!mascotData) {
        alert('Please select a mascot first.');
        return;
    }

    const layerId = state.pendingLayerId || window.currentApplicationLayer;
    if (!layerId) {
        alert('No application layer found.');
        return;
    }

    window.currentApplicationLayer = layerId;

    const layer = window.findLayerById ? window.findLayerById(layerId) : null;

    if (layer && layer.type === 'direct-mascot') {
        layer.mascotTitle = mascotData.title || 'Mascot';

        if (mascotData.source === 'upload' && mascotData.isImage) {
            // PNG/JPG — wrapped in SVG already
            if (window.applyDirectMascotToLayer) {
                window.applyDirectMascotToLayer(mascotData.svg, layerId, true);
            }
        } else {
            const svgContent = mascotData.svg;
            if (!svgContent || !svgContent.trim()) {
                alert('This mascot has no SVG data. Please choose another.');
                return;
            }
            if (window.applyDirectMascotToLayer) {
                window.applyDirectMascotToLayer(svgContent, layerId, true);
                console.log('✅ Direct mascot applied to layer:', layerId);
            }
        }

    } else {
        // Text fill mascot
        if (window.applyMascotToText) {
            window.applyMascotToText(mascotData.svg, layerId);
            console.log('✅ Mascot applied as text fill to layer:', layerId);
        }
        if (window.switchTextCustomizationTab) {
            window.switchTextCustomizationTab('mascot');
        }
    }

    closeMascotSelectModal();
};

// ======= UPLOAD TAB =======
window.mascotDragOver = function(e) {
    e.preventDefault();
    const zone = document.getElementById('mascotDropZone');
    if (zone) { zone.style.borderColor = '#1a1a1a'; zone.style.background = '#f0f0f0'; }
};

window.mascotDragLeave = function(e) {
    const zone = document.getElementById('mascotDropZone');
    if (zone) { zone.style.borderColor = '#ccc'; zone.style.background = '#fafafa'; }
};

window.mascotDrop = function(e) {
    e.preventDefault();
    mascotDragLeave(e);
    const file = e.dataTransfer.files[0];
    if (file) _handleMascotFile(file);
};

window.mascotFileSelected = function(input) {
    const file = input.files && input.files[0];
    if (file) _handleMascotFile(file);
    // Reset input so same file can be re-selected
    input.value = '';
};
function _handleMascotFile(file) {
    if (file.size > 5 * 1024 * 1024) { alert('File too large (max 5MB)'); return; }

const allowed = ['image/svg+xml', 'image/png', 'image/jpeg', 'image/jpg'];
    if (!allowed.includes(file.type)) { alert('Please upload SVG, PNG or JPG'); return; }

    window._mascotState.uploadedFile = file;

    const reader = new FileReader();
    reader.onload = function(e) {
        const result = e.target.result;
        const thumb  = document.getElementById('mascotUploadThumb');

        if (file.type === 'image/svg+xml') {
            if (thumb) {
                thumb.innerHTML = result;
                const svg = thumb.querySelector('svg');
                if (svg) { svg.style.width = '100%'; svg.style.height = '100%'; }
            }
            window._mascotState.selectedMascotData = {
                svg:     result,
                title:   file.name.replace(/\.[^.]+$/, ''),
                source:  'upload',
                isImage: false
            };
        } else {
            // PNG/JPG — wrap in SVG <image>
            if (thumb) thumb.innerHTML = '<img src="' + result + '" style="width:100%;height:100%;object-fit:contain;border-radius:4px;">';

            const wrappedSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">'
                + '<image href="' + result + '" x="0" y="0" width="100" height="100" preserveAspectRatio="xMidYMid meet"/>'
                + '</svg>';

            window._mascotState.selectedMascotData = {
                svg:       wrappedSvg,
                imageData: result,
                title:     file.name.replace(/\.[^.]+$/, ''),
                source:    'upload',
                isImage:   true
            };
        }

        const fnEl = document.getElementById('mascotUploadFileName');
        const fsEl = document.getElementById('mascotUploadFileSize');
        const prev = document.getElementById('mascotUploadPreview');
        if (fnEl) fnEl.textContent = file.name;
        if (fsEl) fsEl.textContent = (file.size / 1024).toFixed(1) + ' KB';
        if (prev) prev.style.display = 'block';
    };

    if (file.type === 'image/svg+xml') {
        reader.readAsText(file);
    } else {
        reader.readAsDataURL(file);
    }
}

window.clearMascotUpload = function() {
    window._mascotState.uploadedFile = null;
    window._mascotState.selectedMascotData = null;
    const prev  = document.getElementById('mascotUploadPreview');
    const input = document.getElementById('mascotFileInput');
    const zone  = document.getElementById('mascotDropZone');
    if (prev)  prev.style.display = 'none';
    if (input) input.value = '';
    if (zone)  { zone.style.borderColor = '#ccc'; zone.style.background = '#fafafa'; }
};

window.applyUploadedMascotFile = function() {
    const data = window._mascotState.selectedMascotData;
    if (!data) {
        alert('Pehle koi file select karein.');
        return;
    }
    applySelectedMascotToApplication();
};
</script>
