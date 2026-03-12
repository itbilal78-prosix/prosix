{{-- =================== MASCOT SELECTION MODAL =================== --}}
{{-- Opens when user selects "Custom Mascot" type and clicks OK in Add Application modal --}}

<div id="mascotSelectModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; width:1100px; max-width:96vw; height:85vh; border-radius:12px; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,0.4);">

        {{-- ===== HEADER ===== --}}
        <div style="padding:18px 24px; background:#1a1a1a; color:#fff; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
            <h3 style="margin:0; font-size:17px; font-weight:700; letter-spacing:1px;">MASCOTS</h3>
            <span onclick="closeMascotSelectModal()" style="cursor:pointer; font-size:26px; line-height:1; opacity:.8; transition:opacity .2s;" onmouseenter="this.style.opacity=1" onmouseleave="this.style.opacity=.8">×</span>
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
                        {{-- Dynamically loaded --}}
                        <div class="ms-cat-item ms-cat-active" data-category="all" onclick="filterMascotCategory('all', this)"
                            style="padding:10px 16px; cursor:pointer; font-size:13px; font-weight:600; background:#1a1a1a; color:#fff;">
                            All Categories
                        </div>
                    </div>

                    {{-- Design Ideas button --}}
                    <div style="padding:12px;">
                        <button onclick="toggleMascotDesignIdeas()"
                            style="width:100%; padding:10px; background:#fff; border:2px solid #1a1a1a; border-radius:6px; font-weight:700; font-size:12px; cursor:pointer; letter-spacing:.5px;">
                            Design Ideas
                        </button>
                    </div>
                </div>

                {{-- CENTER: Mascot grid --}}
                <div style="flex:1; display:flex; flex-direction:column; overflow:hidden;">

                    {{-- Search bar --}}
                    <div style="padding:12px 16px; border-bottom:1px solid #eee; flex-shrink:0;">
                        <div style="position:relative;">
                            <input type="text" id="mascotSearchInput" placeholder="Search mascot..."
                                oninput="searchMascots(this.value)"
                                style="width:100%; padding:9px 36px 9px 14px; border:1.5px solid #ddd; border-radius:8px; font-size:13px; outline:none; box-sizing:border-box; transition:border .2s;"
                                onfocus="this.style.borderColor='#1a1a1a'" onblur="this.style.borderColor='#ddd'">
                            <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:16px;">⌕</span>
                        </div>
                    </div>

                    {{-- Grid --}}
                    <div id="mascotSelectGrid" style="flex:1; overflow-y:auto; padding:16px; display:grid; grid-template-columns:repeat(auto-fill,minmax(110px,1fr)); gap:12px; align-content:start;">
                        {{-- Loaded dynamically --}}
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

                        <button id="mascotEditBtn" onclick="editSelectedMascot()" style="display:none; width:100%; padding:10px; background:#1a1a1a; color:#fff; border:none; border-radius:6px; font-weight:700; font-size:12px; cursor:pointer; letter-spacing:.5px;">
                            Edit Mascots
                        </button>
                    </div>
                </div>
            </div>

            {{-- ========== TAB 2: CREATE CUSTOM ========== --}}
            <div id="msContent2" style="display:none; width:100%; height:100%; overflow-y:auto; padding:32px;">

                <div style="max-width:600px; margin:0 auto;">

                    <h4 style="margin:0 0 8px 0; font-size:18px; font-weight:700;">Create Custom Mascot</h4>
                    <p style="color:#777; font-size:13px; margin-bottom:28px;">Design your own mascot by describing it below. Our AI will generate it for you.</p>

                    {{-- Description textarea --}}
                    <div style="margin-bottom:20px;">
                        <label style="display:block; font-weight:700; font-size:13px; margin-bottom:8px; color:#333;">Describe Your Mascot</label>
                        <textarea id="mascotCreateDesc" placeholder="e.g. A fierce eagle with wings spread, holding a football, in bold team colors..."
                            style="width:100%; height:120px; padding:12px; border:2px solid #ddd; border-radius:8px; font-size:13px; resize:none; outline:none; font-family:inherit; transition:border .2s; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#1a1a1a'" onblur="this.style.borderColor='#ddd'"></textarea>
                    </div>

                    {{-- Style options --}}
                    <div style="margin-bottom:20px;">
                        <label style="display:block; font-weight:700; font-size:13px; margin-bottom:10px; color:#333;">Style</label>
                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <label class="ms-style-opt" style="display:flex; align-items:center; gap:6px; padding:8px 14px; border:2px solid #1a1a1a; border-radius:6px; cursor:pointer; font-size:12px; font-weight:600; background:#1a1a1a; color:#fff;">
                                <input type="radio" name="mascotStyle" value="bold" checked style="display:none;"> Bold & Aggressive
                            </label>
                            <label class="ms-style-opt" style="display:flex; align-items:center; gap:6px; padding:8px 14px; border:2px solid #ddd; border-radius:6px; cursor:pointer; font-size:12px; font-weight:600; color:#555;">
                                <input type="radio" name="mascotStyle" value="cartoon" style="display:none;"> Cartoon / Fun
                            </label>
                            <label class="ms-style-opt" style="display:flex; align-items:center; gap:6px; padding:8px 14px; border:2px solid #ddd; border-radius:6px; cursor:pointer; font-size:12px; font-weight:600; color:#555;">
                                <input type="radio" name="mascotStyle" value="realistic" style="display:none;"> Realistic
                            </label>
                            <label class="ms-style-opt" style="display:flex; align-items:center; gap:6px; padding:8px 14px; border:2px solid #ddd; border-radius:6px; cursor:pointer; font-size:12px; font-weight:600; color:#555;">
                                <input type="radio" name="mascotStyle" value="vintage" style="display:none;"> Vintage
                            </label>
                        </div>
                    </div>

                    {{-- Generate button --}}
                    <button onclick="generateCustomMascot()"
                        style="width:100%; padding:14px; background:#1a1a1a; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; letter-spacing:.5px; display:flex; align-items:center; justify-content:center; gap:10px;">
                        <span>✦</span> Generate Mascot
                    </button>

                    {{-- Generated result --}}
                    <div id="mascotCreateResult" style="display:none; margin-top:24px; padding:20px; background:#f5f5f5; border-radius:10px; text-align:center;">
                        <div id="mascotCreatePreview" style="margin-bottom:14px;"></div>
                        <button onclick="applyCreatedMascot()"
                            style="padding:12px 28px; background:#1a1a1a; color:#fff; border:none; border-radius:6px; font-weight:700; cursor:pointer; font-size:13px;">
                            Use This Mascot
                        </button>
                    </div>
                </div>
            </div>

            {{-- ========== TAB 3: UPLOAD ========== --}}
            <div id="msContent3" style="display:none; width:100%; height:100%; overflow-y:auto; padding:32px;">

                <div style="max-width:560px; margin:0 auto;">

                    <h4 style="margin:0 0 8px 0; font-size:18px; font-weight:700;">Upload Your Own Mascot</h4>
                    <p style="color:#777; font-size:13px; margin-bottom:28px;">Upload an SVG or PNG file from your computer.</p>

                    {{-- Drop zone --}}
                    <div id="mascotDropZone"
                        onclick="document.getElementById('mascotFileInput').click()"
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

                    <input type="file" id="mascotFileInput" accept=".svg,.png,.jpg,.jpeg" style="display:none;" onchange="mascotFileSelected(this)">

                    {{-- Preview after upload --}}
                    <div id="mascotUploadPreview" style="display:none; margin-top:24px; padding:20px; background:#f5f5f5; border-radius:10px;">
                        <div style="display:flex; align-items:center; gap:16px;">
                            <div id="mascotUploadThumb" style="width:80px; height:80px; background:#e0e0e0; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden;"></div>
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

.ms-style-opt input:checked + * { /* handled via JS */ }
</style>

<script>
(function () {

    // ======= STATE =======
    let allMascots = [];
    let filteredMascots = [];
    let selectedMascotData = null;   // { svg, title, source: 'existing'|'upload'|'create' }
    let currentMascotTab = 'existing';
    let _pendingApplicationLayer = null;  // layer ID that will receive the mascot

    // ======= OPEN / CLOSE =======

 function openMascotSelectModal(layerId = null) {

    const modal = document.getElementById("mascotSelectModal");

    if (!modal) {
        console.error("Mascot modal not found");
        return;
    }

    modal.style.display = "flex";

    if (typeof switchMascotSelectTab === "function") {
        switchMascotSelectTab('existing');
    }

    // ✅ BACKEND TEMPLATES LOAD KARO
    loadMascotTemplates();
}

    window.closeMascotSelectModal = function () {
        document.getElementById('mascotSelectModal').style.display = 'none';
        selectedMascotData = null;
    };

    // ======= TABS =======

    window.switchMascotSelectTab = function (tab) {
        currentMascotTab = tab;

        const tabs = { existing: 'msTab1', create: 'msTab2', upload: 'msTab3' };
        const contents = { existing: 'msContent1', create: 'msContent2', upload: 'msContent3' };

        Object.keys(tabs).forEach(t => {
            const btn = document.getElementById(tabs[t]);
            if (btn) {
                btn.style.borderBottom = t === tab ? '3px solid #1a1a1a' : '3px solid transparent';
                btn.style.color = t === tab ? '#1a1a1a' : '#999';
            }
            const content = document.getElementById(contents[t]);
            if (content) content.style.display = t === tab ? (t === 'existing' ? 'flex' : 'block') : 'none';
        });
    };

    // ======= LOAD TEMPLATES =======

    function loadMascotTemplates() {
        fetch('/api/mascot-templates')
            .then(r => r.json())
            .then(data => {
                allMascots = data;
                buildCategoryList(data);
                renderMascotGrid(data);
            })
            .catch(() => {
                document.getElementById('mascotSelectGrid').innerHTML =
                    '<div style="grid-column:1/-1;text-align:center;padding:40px;color:#aaa;font-size:13px;">Could not load mascots.</div>';
            });
    }

    function buildCategoryList(mascots) {
        const categories = [...new Set(mascots.map(m => m.category).filter(Boolean))];
        const list = document.getElementById('mascotCategoryList');

        list.innerHTML = `<div class="ms-cat-item ms-cat-active" data-category="all" onclick="filterMascotCategory('all', this)">All Categories</div>`;

        categories.forEach(cat => {
            const div = document.createElement('div');
            div.className = 'ms-cat-item';
            div.dataset.category = cat;
            div.textContent = cat;
            div.onclick = function () { filterMascotCategory(cat, this); };
            list.appendChild(div);
        });
    }

    window.filterMascotCategory = function (category, el) {
        document.querySelectorAll('.ms-cat-item').forEach(i => i.classList.remove('ms-cat-active'));
        if (el) el.classList.add('ms-cat-active');

        filteredMascots = category === 'all' ? allMascots : allMascots.filter(m => m.category === category);
        renderMascotGrid(filteredMascots);
    };

    window.searchMascots = function (query) {
        const q = query.toLowerCase().trim();
        const base = filteredMascots.length ? filteredMascots : allMascots;
        const results = q ? base.filter(m => m.title?.toLowerCase().includes(q)) : base;
        renderMascotGrid(results);
    };

    function renderMascotGrid(mascots) {
        const grid = document.getElementById('mascotSelectGrid');

        if (!mascots.length) {
            grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;color:#aaa;font-size:13px;">No mascots found.</div>';
            return;
        }

        grid.innerHTML = '';

        mascots.forEach(m => {
            const card = document.createElement('div');
            card.className = 'ms-mascot-card';
            card.dataset.id = m.id;
            card.innerHTML = `
                <img src="${m.image_data || m.svg_url || ''}" alt="${m.title || ''}" onerror="this.style.display='none'">
                <p>${m.title || 'Untitled'}</p>
            `;
            card.onclick = function () { selectMascotCard(m, this); };
            grid.appendChild(card);
        });
    }

 function selectMascotCard(mascot, cardEl) {
    // Deselect others
    document.querySelectorAll('.ms-mascot-card').forEach(c => c.classList.remove('ms-selected'));
    cardEl.classList.add('ms-selected');

    // ✅ FIX: svg_data ya image_data dono check karo
    const svgContent = mascot.svg_data || '';
    selectedMascotData = {
        svg: svgContent,
        imageUrl: mascot.image_data || '',
        title: mascot.title,
        source: 'existing'
    };

    // Update preview
    const previewBox = document.getElementById('mascotSelectPreviewBox');
    previewBox.innerHTML = '';
    previewBox.style.border = '2px solid #1a1a1a';

    if (mascot.svg_data && mascot.svg_data.trim().startsWith('<')) {
        // ✅ SVG content hai - directly inject karo
        previewBox.innerHTML = mascot.svg_data;
        const svg = previewBox.querySelector('svg');
        if (svg) {
            svg.style.width = '110px';
            svg.style.height = '110px';
            svg.style.display = 'block';
        }
    } else if (mascot.image_data) {
        // ✅ Image URL hai
        const img = document.createElement('img');
        img.src = mascot.image_data;
        img.style.cssText = 'width:110px;height:110px;object-fit:contain;display:block;';
        previewBox.appendChild(img);
    } else {
        previewBox.innerHTML = '<span style="color:#ccc;font-size:11px;">No preview</span>';
    }

    document.getElementById('mascotSelectPreviewName').textContent = mascot.title || '';
    document.getElementById('mascotEditBtn').style.display = 'block';
}

    window.editSelectedMascot = function () {
        // Opens existing mascot editor (if available)
        if (window.openMascotEditor && selectedMascotData) {
            window.openMascotEditor(selectedMascotData);
        }
    };

    window.toggleMascotDesignIdeas = function () {
        console.log('Design Ideas clicked');
        // Hook into existing design ideas logic if available
        if (window.openDesignIdeas) window.openDesignIdeas();
    };

    // ======= APPLY =======

window.applySelectedMascotToApplication = function () {
    if (!selectedMascotData || !selectedMascotData.svg) {
        alert('Please select a mascot first.');
        return;
    }

    const layerId = _pendingApplicationLayer || window.currentApplicationLayer;
    if (!layerId) {
        alert('No application layer selected.');
        return;
    }

    window.currentApplicationLayer = layerId;

    // SVG content check karo
    const svgContent = selectedMascotData.svg;
    console.log('Applying mascot SVG:', svgContent.substring(0, 100));

    if (window.applyMascotToText) {
        window.applyMascotToText(svgContent, layerId);
        console.log('✅ Mascot applied to layer:', layerId);
    } else {
        console.error('❌ applyMascotToText function not found!');
    }

    closeMascotSelectModal();

    if (window.switchTextCustomizationTab) {
        window.switchTextCustomizationTab('mascot');
    }
};

    // ======= UPLOAD TAB =======

    let uploadedMascotFile = null;

    window.mascotDragOver = function (e) {
        e.preventDefault();
        document.getElementById('mascotDropZone').style.borderColor = '#1a1a1a';
        document.getElementById('mascotDropZone').style.background = '#f0f0f0';
    };

    window.mascotDragLeave = function (e) {
        document.getElementById('mascotDropZone').style.borderColor = '#ccc';
        document.getElementById('mascotDropZone').style.background = '#fafafa';
    };

    window.mascotDrop = function (e) {
        e.preventDefault();
        mascotDragLeave(e);
        const file = e.dataTransfer.files[0];
        if (file) handleMascotFile(file);
    };

    window.mascotFileSelected = function (input) {
        const file = input.files[0];
        if (file) handleMascotFile(file);
    };

    function handleMascotFile(file) {
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) { alert('File too large (max 5MB)'); return; }

        const allowed = ['image/svg+xml', 'image/png', 'image/jpeg'];
        if (!allowed.includes(file.type)) { alert('Please upload SVG, PNG or JPG'); return; }

        uploadedMascotFile = file;

        const reader = new FileReader();
        reader.onload = function (e) {
            const result = e.target.result;

            const thumb = document.getElementById('mascotUploadThumb');
            if (file.type === 'image/svg+xml') {
                thumb.innerHTML = result;
                const svg = thumb.querySelector('svg');
                if (svg) { svg.style.width = '100%'; svg.style.height = '100%'; }
                uploadedMascotFile._svgContent = result;
            } else {
                thumb.innerHTML = `<img src="${result}" style="width:100%;height:100%;object-fit:contain;">`;
            }

            document.getElementById('mascotUploadFileName').textContent = file.name;
            document.getElementById('mascotUploadFileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
            document.getElementById('mascotUploadPreview').style.display = 'block';

            selectedMascotData = { svg: result, title: file.name, source: 'upload' };
        };

        if (file.type === 'image/svg+xml') {
            reader.readAsText(file);
        } else {
            reader.readAsDataURL(file);
        }
    }

    window.clearMascotUpload = function () {
        uploadedMascotFile = null;
        selectedMascotData = null;
        document.getElementById('mascotUploadPreview').style.display = 'none';
        document.getElementById('mascotFileInput').value = '';
    };

    window.applyUploadedMascotFile = function () {
        if (!selectedMascotData) return;
        applySelectedMascotToApplication();
    };

    // ======= CREATE TAB =======

window.generateCustomMascot = function () {
    const desc = document.getElementById('mascotCreateDesc').value.trim();
    if (!desc) { alert('Please describe your mascot first.'); return; }

    // Mascot editor iframe ke taur par modal ke andar kholo
    const result = document.getElementById('mascotCreateResult');
    result.style.display = 'block';

    document.getElementById('mascotCreatePreview').innerHTML = `
        <iframe
            src="/admin/mascots/create?embed=1&desc=${encodeURIComponent(desc)}"
            style="width:100%; height:500px; border:2px solid #ddd; border-radius:8px;"
            id="mascotEditorIframe">
        </iframe>
        <p style="color:#999; font-size:12px; margin-top:8px;">
            Mascot editor mein design banao, phir Save karo aur neeche "Use This Mascot" dabao.
        </p>
    `;
};

window.applyCreatedMascot = function () {
    // iframe se saved mascot SVG lo
    const iframe = document.getElementById('mascotEditorIframe');

    if (iframe && iframe.contentWindow && iframe.contentWindow.getCanvasSvg) {
        const svgContent = iframe.contentWindow.getCanvasSvg();
        if (svgContent) {
            selectedMascotData = { svg: svgContent, title: 'Custom Mascot', source: 'create' };
            applySelectedMascotToApplication();
            return;
        }
    }

    // Fallback — agar iframe se nahi mila
    alert('Please save your mascot in the editor first, then click Use This Mascot.');
};

    // ======= STYLE RADIO FIX =======
    document.querySelectorAll('.ms-style-opt').forEach(label => {
        label.addEventListener('click', function () {
            document.querySelectorAll('.ms-style-opt').forEach(l => {
                l.style.background = '#fff';
                l.style.color = '#555';
                l.style.borderColor = '#ddd';
            });
            this.style.background = '#1a1a1a';
            this.style.color = '#fff';
            this.style.borderColor = '#1a1a1a';
        });
    });

})();
</script>
