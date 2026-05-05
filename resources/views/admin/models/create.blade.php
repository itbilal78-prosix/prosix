@extends('layouts.dashboard')

@section('content')

<style>
/* ── Reset & Base ── */
*{box-sizing:border-box;margin:0;padding:0}
.pf-wrap{max-width:100%;padding:32px 28px 80px}
.pf-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:12px}
.pf-header h1{font-size:22px;font-weight:800;letter-spacing:-.3px}

/* ── 2-Column Grid ── */
.pf-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px}
@media(max-width:768px){.pf-grid{grid-template-columns:1fr}}
.pf-grid-full{margin-bottom:20px}

/* ── Card ── */
.pf-card{background:#fff;border:1.5px solid #e8e8e8;border-radius:13px;padding:24px 28px;height:100%}
.pf-card-title{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#000;margin-bottom:18px;padding-bottom:12px;border-bottom:2px solid #000;display:flex;align-items:center;gap:8px}
.pf-card-title i{font-size:14px}

/* ── Form Elements ── */
.pf-group{display:flex;flex-direction:column;gap:6px;margin-bottom:14px}
.pf-group:last-child{margin-bottom:0}
.pf-row-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:500px){.pf-row-2{grid-template-columns:1fr}}
.pf-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#333}
.pf-label small{text-transform:none;font-weight:500;color:#aaa;letter-spacing:0;font-size:10px}
.pf-input,.pf-select,.pf-textarea{width:100%;padding:10px 13px;border:1.5px solid #e0e0e0;border-radius:8px;font-size:13px;font-family:inherit;background:#fff;color:#000;transition:border-color .2s}
.pf-input:focus,.pf-select:focus,.pf-textarea:focus{outline:none;border-color:#000}
.pf-textarea{resize:vertical;min-height:80px}
.pf-hint{font-size:11px;color:#999;margin-top:3px}

/* ── Toggle ── */
.pf-toggle-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0}
.pf-toggle-row:last-child{border-bottom:none}
.pf-toggle-info strong{font-size:13px;font-weight:600;display:block}
.pf-toggle-info span{font-size:11px;color:#999}
.pf-toggle{position:relative;width:44px;height:24px;flex-shrink:0}
.pf-toggle input{opacity:0;width:0;height:0}
.pf-toggle-slider{position:absolute;inset:0;background:#e0e0e0;border-radius:24px;cursor:pointer;transition:.3s}
.pf-toggle-slider:before{content:'';position:absolute;width:18px;height:18px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.3s}
.pf-toggle input:checked+.pf-toggle-slider{background:#000}
.pf-toggle input:checked+.pf-toggle-slider:before{transform:translateX(20px)}

/* ── Sizes ── */
.sizes-trigger{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border:1.5px solid #000;border-radius:8px;cursor:pointer;font-size:12px;font-weight:700;background:#fff;color:#000;transition:.2s}
.sizes-trigger:hover{background:#000;color:#fff}
.sizes-dropdown{background:#fff;border:1.5px solid #e0e0e0;border-radius:10px;padding:16px;margin-top:10px;display:none}
.sizes-dropdown.open{display:block}
.sizes-grid{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px}
.size-chip input[type=checkbox]{display:none}
.size-chip label{display:flex;align-items:center;justify-content:center;min-width:48px;height:36px;padding:0 10px;border:1.5px solid #e0e0e0;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;transition:.15s;background:#fff;user-select:none}
.size-chip input:checked+label{background:#000;color:#fff;border-color:#000}
.sizes-selected-tags{display:flex;flex-wrap:wrap;gap:6px;min-height:28px;margin-top:10px}
.size-tag{display:inline-flex;align-items:center;gap:4px;background:#000;color:#fff;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700}
.size-tag-cross{cursor:pointer;opacity:.7;font-size:14px}

/* ── Size Chart ── */
.sc-box{border:2px dashed #e0e0e0;border-radius:12px;padding:20px;text-align:center;cursor:pointer;background:#fafafa;transition:.2s;min-height:160px;display:flex;flex-direction:column;align-items:center;justify-content:center}
.sc-box:hover{border-color:#000}
.sc-box img{max-width:100%;max-height:180px;object-fit:contain;border-radius:8px;display:none}
.sc-box.has-img img{display:block}
.sc-box.has-img .upload-placeholder{display:none}

/* ── Actions ── */
.pf-actions{display:flex;gap:12px;padding-top:8px}
.btn-save{height:46px;padding:0 32px;background:#000;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:8px}
.btn-save:hover{background:#222}
.btn-back{height:46px;padding:0 22px;background:#fff;color:#000;border:1.5px solid #000;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:.2s}
.btn-back:hover{background:#000;color:#fff}

/* ── Model Image Boxes ── */
.image-box{width:calc(33.33% - 0.5rem);margin-bottom:.5rem}
.img-preview{width:100%;height:60px;object-fit:contain;border:1px dotted #000;cursor:pointer;display:flex;justify-content:center;align-items:center;background:#f8f9fa;font-size:10px;color:#000;position:relative}
.img-preview img{width:100%;height:100%;object-fit:contain}
.img-preview .close-btn{position:absolute;top:2px;right:2px;background:#fff;border-radius:50%;width:16px;height:16px;display:flex;justify-content:center;align-items:center;font-size:12px;color:#000;cursor:pointer;z-index:10;border:1px solid #000}
.empty-preview span{pointer-events:none}

/* ── 3D Thumbnail ── */
.thumbnail-3d{width:180px;height:180px;margin:auto;position:relative;border:2px dashed #bbb;perspective:1000px;overflow:hidden}
.layer-img{position:absolute;inset:0;width:100%;height:100%;object-fit:contain;display:block}
#p_black{mix-blend-mode:screen;z-index:3}
#p_white{mix-blend-mode:multiply;z-index:2}
#p_svg{z-index:1}
</style>

<div class="pf-wrap">
  <div class="pf-header">
    <h1><i class="bi bi-plus-circle me-2"></i>Add Model</h1>
    <a href="{{ route('customizer.models.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Back</a>
  </div>

  @if($errors->any())
    <div class="alert alert-danger mb-3" style="border-radius:10px">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <form id="modelForm" action="{{ route('customizer.models.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- ROW 1: Basic Info + Navigation/Category --}}
    <div class="pf-grid">

      {{-- 1. Basic Info --}}
      <div class="pf-card">
        <div class="pf-card-title"><i class="bi bi-info-circle"></i> Basic Information</div>
        <div class="pf-group">
          <label class="pf-label">Model Name <span style="color:#e53e3e">*</span></label>
          <input type="text" name="model_name" class="pf-input" value="{{ old('model_name') }}" required placeholder="e.g. Pro Football Jersey">
        </div>
        <div class="pf-group">
          <label class="pf-label">Title (Display Name) <span style="color:#e53e3e">*</span></label>
          <input type="text" name="title" class="pf-input" value="{{ old('title') }}" required placeholder="e.g. Football Jersey">
        </div>
        <div class="pf-row-2">
          <div class="pf-group">
            <label class="pf-label">Price ($)</label>
            <input type="number" step="0.01" min="0" name="price" class="pf-input" value="{{ old('price') }}" placeholder="0.00">
          </div>
        </div>
        <div class="pf-group">
          <label class="pf-label">Description <small>(optional)</small></label>
          <textarea name="description" class="pf-textarea" placeholder="Model description...">{{ old('description') }}</textarea>
        </div>
      </div>

      {{-- 2. Navigation / Category / Subcategory --}}
      <div class="pf-card">
        <div class="pf-card-title"><i class="bi bi-diagram-3"></i> Navigation & Category</div>
        <div class="pf-group">
          <label class="pf-label">Navigation <small>(optional)</small></label>
          <select name="navigation_id" id="navSelect" class="pf-select">
            <option value="">-- None --</option>
            @foreach($navigations as $nav)
              <option value="{{ $nav->id }}" {{ old('navigation_id')==$nav->id ? 'selected':'' }}>
                {{ $nav->title ?? $nav->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="pf-group">
          <label class="pf-label">Category <small>(optional)</small></label>
          <select name="category_id" id="categorySelect" class="pf-select">
            <option value="">-- None --</option>
            @foreach($parentCategories as $cat)
              <option value="{{ $cat->id }}" data-nav="{{ $cat->navigation_id ?? '' }}"
                {{ old('category_id')==$cat->id ? 'selected':'' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="pf-group" id="subcategoryWrapper" style="display:none">
          <label class="pf-label">Subcategory <small>(optional)</small></label>
          <select name="subcategory_id" id="subcategorySelect" class="pf-select">
            <option value="">-- None --</option>
          </select>
        </div>
      </div>

    </div>{{-- end row 1 --}}

    {{-- ROW 2: Stock + Shipping --}}
    <div class="pf-grid">

      {{-- 3. Stock --}}
      <div class="pf-card">
        <div class="pf-card-title"><i class="bi bi-box-seam"></i> Stock</div>
        <div class="pf-toggle-row">
          <div class="pf-toggle-info"><strong>In Stock</strong><span>Model available for purchase</span></div>
          <label class="pf-toggle"><input type="checkbox" name="in_stock" value="1" checked><span class="pf-toggle-slider"></span></label>
        </div>
        <div style="margin-top:14px">
          <div class="pf-group">
            <label class="pf-label">Stock Quantity <small>(optional)</small></label>
            <input type="number" min="0" name="stock_quantity" class="pf-input" value="{{ old('stock_quantity') }}" placeholder="Leave empty = unlimited" style="max-width:220px">
            <span class="pf-hint">Frontend par is se zyada quantity select nahi hogi</span>
          </div>
        </div>
      </div>

      {{-- 4. Shipping --}}
      <div class="pf-card">
        <div class="pf-card-title"><i class="bi bi-truck"></i> Shipping</div>
        <div class="pf-toggle-row">
          <div class="pf-toggle-info"><strong>Enable Shipping</strong><span>This model requires shipping</span></div>
          <label class="pf-toggle"><input type="checkbox" name="shipping_enabled" id="shippingToggle" value="1" checked><span class="pf-toggle-slider"></span></label>
        </div>
        <div id="shippingFields" style="margin-top:14px">
          <div class="pf-row-2">
            <div class="pf-group">
              <label class="pf-label">Shipping Cost ($)</label>
              <input type="number" step="0.01" min="0" name="shipping_cost" class="pf-input" value="{{ old('shipping_cost','0') }}" placeholder="0.00">
              <span class="pf-hint">0 = Free shipping</span>
            </div>
            <div class="pf-group">
              <label class="pf-label">Free Above ($) <small>(optional)</small></label>
              <input type="number" step="0.01" min="0" name="free_shipping_above" class="pf-input" value="{{ old('free_shipping_above') }}" placeholder="e.g. 200">
            </div>
          </div>
        </div>
      </div>

    </div>{{-- end row 2 --}}

    {{-- ROW 3: Sizes (full width now, no main image) --}}
    <div class="pf-grid-full">
      <div class="pf-card">
        <div class="pf-card-title"><i class="bi bi-rulers"></i> Sizes</div>
        <div class="sizes-trigger" id="sizesTrigger">
          <i class="bi bi-plus-circle"></i><span>Select Sizes</span>
          <i class="bi bi-chevron-down" id="sizesChevron" style="margin-left:auto"></i>
        </div>
        <div class="sizes-dropdown" id="sizesDropdown">
          <div class="sizes-grid">
            @foreach(['All','YXS','YS','YM','YL','YXL','S','M','L','XL','2XL'] as $sz)
            <div class="size-chip">
              <input type="checkbox" id="sz_{{$sz}}" value="{{$sz}}" class="size-checkbox">
              <label for="sz_{{$sz}}">{{$sz}}</label>
            </div>
            @endforeach
          </div>
          <div class="pf-hint">"All" click karo sab select karne ke liye.</div>
        </div>
        <div class="sizes-selected-tags" id="sizesTags"></div>
        <div id="sizesHidden"></div>
      </div>
    </div>

    {{-- ROW 4: Size Chart (full width) --}}
    <div class="pf-grid-full">
      <div class="pf-card">
        <div class="pf-card-title"><i class="bi bi-table"></i> Size Chart Image</div>
        <p style="font-size:11px;color:#777;margin-bottom:14px">Upload your size chart image (optional).</p>
        <div class="sc-box" id="scBox" onclick="document.getElementById('scInput').click()">
          <div class="upload-placeholder">
            <i class="bi bi-cloud-upload" style="font-size:26px;color:#ccc;display:block;margin-bottom:6px"></i>
            <p style="font-size:11px;color:#aaa;margin:0">Click to upload size chart</p>
          </div>
          <img id="scPreview" src="" alt="">
        </div>
        <div id="scActions" style="display:none;margin-top:8px;gap:8px" class="img-actions">
          <button type="button" class="btn-back" style="height:36px;padding:0 16px;font-size:11px" onclick="document.getElementById('scInput').click()"><i class="bi bi-arrow-repeat"></i> Replace</button>
          <button type="button" class="btn-back" style="height:36px;padding:0 16px;font-size:11px;background:#000;color:#fff" onclick="removeSizeChart()"><i class="bi bi-trash"></i> Remove</button>
        </div>
        <input type="file" name="size_chart_image" id="scInput" accept="image/*" style="display:none">
      </div>
    </div>

    {{-- ROW 5: Model Images (front/back/left/right × black/white/svg) --}}
    <div class="pf-grid-full">
      <div class="pf-card">
        <div class="pf-card-title"><i class="bi bi-images"></i> Model View Images</div>
        <p style="font-size:11px;color:#888;margin-bottom:16px">Upload front, back, left, right views in black, white, and SVG formats.</p>

        <div style="display:grid;grid-template-columns:1fr auto;gap:20px;align-items:start">

          {{-- Image Upload Grid --}}
          <div class="d-flex flex-wrap gap-2">
            @php
              $views  = ['front', 'back', 'left', 'right'];
              $colors = ['black', 'white', 'svg'];
            @endphp
            @foreach($views as $view)
              @foreach($colors as $color)
                <div class="image-box">
                  <label class="pf-label" style="margin-bottom:4px;text-transform:capitalize">{{ $view }} {{ $color }}</label>
                  <input type="file" name="{{ $view }}_{{ $color }}"
                    class="d-none" accept="{{ $color === 'svg' ? 'image/svg+xml' : 'image/png' }}"
                    onchange="fieldPreview(this,'{{ $view }}','{{ $color }}')"
                    id="{{ $view }}_{{ $color }}_input">
                  <div class="img-preview empty-preview"
                    onclick="document.getElementById('{{ $view }}_{{ $color }}_input').click()">
                    <span>Click</span>
                  </div>
                </div>
              @endforeach
            @endforeach
          </div>

          {{-- 3D Thumbnail Preview --}}
          <div style="min-width:220px">
            <div style="border:1.5px solid #e8e8e8;border-radius:12px;padding:16px;text-align:center">
              <div class="pf-label" style="margin-bottom:10px;text-align:center">3D Preview</div>
              <div class="thumbnail-3d mb-2" id="thumbnail-3d">
                @foreach($colors as $color)
                  <img id="p_{{ $color }}" class="layer-img" src="">
                @endforeach
              </div>
              <div style="display:flex;justify-content:space-between;gap:8px;margin-top:10px">
                <button type="button" class="btn-back" style="height:34px;padding:0 14px;font-size:11px" onclick="prevView()">
                  <i class="bi bi-arrow-left"></i> Prev
                </button>
                <button type="button" class="btn-back" style="height:34px;padding:0 14px;font-size:11px" onclick="nextView()">
                  Next <i class="bi bi-arrow-right"></i>
                </button>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    {{-- Actions --}}
    <div class="pf-actions">
      <button type="submit" class="btn-save"><i class="bi bi-check-lg"></i> Save Model</button>
      <a href="{{ route('customizer.models.index') }}" class="btn-back">Cancel</a>
    </div>

  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

  // ── Navigation → Category → Subcategory ─────────────────────
  const allCategories = @json($parentCategories->load('subcategories'));
  const navSelect = document.getElementById('navSelect');
  const catSelect = document.getElementById('categorySelect');
  const subSelect = document.getElementById('subcategorySelect');

  navSelect.addEventListener('change', function () {
    const navId = this.value;
    catSelect.innerHTML = '<option value="">-- None --</option>';
    subSelect.innerHTML = '<option value="">-- None --</option>';
    document.getElementById('subcategoryWrapper').style.display = 'none';

    const filtered = navId
      ? allCategories.filter(c => String(c.navigation_id) === String(navId))
      : allCategories;

    filtered.forEach(cat => {
      const opt = document.createElement('option');
      opt.value = cat.id; opt.textContent = cat.name;
      catSelect.appendChild(opt);
    });
  });

  catSelect.addEventListener('change', function () {
    const sw  = document.getElementById('subcategoryWrapper');
    subSelect.innerHTML = '<option value="">-- None --</option>';
    const cat = allCategories.find(c => c.id === parseInt(this.value));
    if (cat && cat.subcategories && cat.subcategories.length) {
      cat.subcategories.forEach(s => {
        const o = document.createElement('option'); o.value = s.id; o.textContent = s.name; subSelect.appendChild(o);
      });
      sw.style.display = 'block';
    } else { sw.style.display = 'none'; }
  });

  // ── Shipping Toggle ──────────────────────────────────────────
  const st = document.getElementById('shippingToggle');
  const sf = document.getElementById('shippingFields');
  sf.style.display = st.checked ? 'block' : 'none';
  st.addEventListener('change', () => sf.style.display = st.checked ? 'block' : 'none');

  // ── Sizes ────────────────────────────────────────────────────
  let selectedSizes = [];
  document.getElementById('sizesTrigger').addEventListener('click', function () {
    const d = document.getElementById('sizesDropdown'); d.classList.toggle('open');
    document.getElementById('sizesChevron').className = d.classList.contains('open') ? 'bi bi-chevron-up' : 'bi bi-chevron-down';
  });
  document.querySelectorAll('.size-checkbox').forEach(cb => {
    cb.addEventListener('change', function () {
      const all = ['YXS','YS','YM','YL','YXL','S','M','L','XL','2XL'];
      if (this.value === 'All') {
        if (this.checked) {
          selectedSizes = [...all];
          document.querySelectorAll('.size-checkbox').forEach(c => { if (c.value !== 'All') c.checked = true; });
        } else {
          selectedSizes = [];
          document.querySelectorAll('.size-checkbox').forEach(c => c.checked = false);
        }
      } else {
        if (this.checked) { if (!selectedSizes.includes(this.value)) selectedSizes.push(this.value); }
        else { selectedSizes = selectedSizes.filter(s => s !== this.value); document.getElementById('sz_All').checked = false; }
      }
      renderSizeTags();
    });
  });
  function renderSizeTags() {
    const tags = document.getElementById('sizesTags');
    const hid  = document.getElementById('sizesHidden');
    tags.innerHTML = ''; hid.innerHTML = '';
    selectedSizes.forEach(sz => {
      const t = document.createElement('span'); t.className = 'size-tag';
      t.innerHTML = `${sz} <span class="size-tag-cross" onclick="removeSize('${sz}')">×</span>`; tags.appendChild(t);
      const i = document.createElement('input'); i.type = 'hidden'; i.name = 'sizes[]'; i.value = sz; hid.appendChild(i);
    });
  }
  window.removeSize = function (sz) {
    selectedSizes = selectedSizes.filter(s => s !== sz);
    const cb = document.getElementById('sz_' + sz); if (cb) cb.checked = false;
    document.getElementById('sz_All').checked = false; renderSizeTags();
  };

  // ── Size Chart ───────────────────────────────────────────────
  document.getElementById('scInput').addEventListener('change', function () {
    if (this.files && this.files[0]) {
      document.getElementById('scPreview').src = URL.createObjectURL(this.files[0]);
      document.getElementById('scBox').classList.add('has-img');
      document.getElementById('scActions').style.display = 'flex';
    }
  });
  window.removeSizeChart = function () {
    document.getElementById('scInput').value = '';
    document.getElementById('scPreview').src = '';
    document.getElementById('scBox').classList.remove('has-img');
    document.getElementById('scActions').style.display = 'none';
  };

  // ── Model View Image Previews ────────────────────────────────
  let currentView = 0;
  const viewsList = ['front', 'back', 'left', 'right'];

  window.fieldPreview = function (input, view, type) {
    const container = input.nextElementSibling;
    if (!input.files.length) { container.innerHTML = '<span>Click</span>'; return; }
    const reader = new FileReader();
    reader.onload = e => {
      container.innerHTML = `
        <img src="${e.target.result}" alt="${view}_${type}">
        <div class="close-btn" onclick="removeImage('${view}','${type}')">×</div>`;
      if (view === 'front') {
        const t = document.getElementById('p_' + type);
        if (t) t.src = e.target.result;
      }
    };
    reader.readAsDataURL(input.files[0]);
  };

  window.removeImage = function (view, type) {
    const input = document.getElementById(view + '_' + type + '_input');
    input.value = '';
    const container = input.nextElementSibling;
    container.innerHTML = '<span>Click</span>';
    if (view === 'front') { const t = document.getElementById('p_' + type); if (t) t.src = ''; }
  };

  window.showThumbnail = function (viewIndex) {
    const view = viewsList[viewIndex];
    ['black', 'white', 'svg'].forEach(color => {
      const input = document.getElementById(view + '_' + color + '_input');
      const img   = document.getElementById('p_' + color);
      if (input && input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { if (img) img.src = e.target.result; };
        reader.readAsDataURL(input.files[0]);
      } else { if (img) img.src = ''; }
    });
  };

  window.nextView = function () {
    currentView = (currentView + 1) % viewsList.length;
    showThumbnail(currentView);
  };
  window.prevView = function () {
    currentView = (currentView - 1 + viewsList.length) % viewsList.length;
    showThumbnail(currentView);
  };

});
</script>

@endsection
