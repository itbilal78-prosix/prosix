@extends('layouts.dashboard')

@section('content')
<style>
.pf-wrap{max-width:960px;margin:0 auto;padding-bottom:60px}
.pf-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}
.pf-header h1{font-size:22px;font-weight:700;margin:0}
.pf-card{background:#fff;border:1px solid #e8e8e8;border-radius:14px;padding:28px 32px;margin-bottom:20px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
.pf-card-title{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#666;margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid #f0f0f0;display:flex;align-items:center;gap:8px}
.pf-card-title i{font-size:15px;color:#2563eb}
.pf-row{display:grid;gap:18px}
.pf-row.cols-2{grid-template-columns:1fr 1fr}
@media(max-width:640px){.pf-row.cols-2{grid-template-columns:1fr}}
.pf-group{display:flex;flex-direction:column;gap:6px}
.pf-label{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#444}
.pf-label small{text-transform:none;font-weight:500;color:#aaa;letter-spacing:0;font-size:11px}
.pf-input,.pf-select,.pf-textarea{width:100%;padding:10px 13px;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;transition:border-color .2s}
.pf-input:focus,.pf-select:focus,.pf-textarea:focus{outline:none;border-color:#2563eb}
.pf-textarea{resize:vertical;min-height:90px}
.pf-hint{font-size:11px;color:#999}
/* Toggle */
.pf-toggle-row{display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid #f5f5f5}
.pf-toggle-row:last-child{border-bottom:none}
.pf-toggle-info strong{font-size:13px;font-weight:600;display:block;margin-bottom:2px}
.pf-toggle-info span{font-size:11px;color:#999}
.pf-toggle{position:relative;width:44px;height:24px;flex-shrink:0}
.pf-toggle input{opacity:0;width:0;height:0}
.pf-toggle-slider{position:absolute;inset:0;background:#e0e0e0;border-radius:24px;cursor:pointer;transition:.3s}
.pf-toggle-slider:before{content:'';position:absolute;width:18px;height:18px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.3s}
.pf-toggle input:checked+.pf-toggle-slider{background:#2563eb}
.pf-toggle input:checked+.pf-toggle-slider:before{transform:translateX(20px)}
/* Sizes */
.sizes-trigger{display:flex;align-items:center;gap:8px;padding:10px 14px;border:1.5px dashed #e0e0e0;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;color:#2563eb;background:#f0f6ff;width:fit-content;transition:.2s}
.sizes-dropdown{background:#fff;border:1.5px solid #e0e0e0;border-radius:10px;padding:16px;margin-top:10px;display:none}
.sizes-dropdown.open{display:block}
.sizes-grid{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px}
.size-chip input[type=checkbox]{display:none}
.size-chip label{display:flex;align-items:center;justify-content:center;min-width:52px;height:38px;padding:0 10px;border:1.5px solid #e0e0e0;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;transition:.15s;background:#fff;user-select:none}
.size-chip input:checked+label{background:#000;color:#fff;border-color:#000}
.sizes-selected-tags{display:flex;flex-wrap:wrap;gap:6px;min-height:30px;margin-top:10px}
.size-tag{display:inline-flex;align-items:center;gap:4px;background:#000;color:#fff;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700}
.size-tag-cross{cursor:pointer;font-size:14px;line-height:1;opacity:.7}
/* Color add row */
.color-add-row{display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap}
.color-hex-input{width:48px;height:40px;border:1.5px solid #e0e0e0;border-radius:8px;cursor:pointer;padding:2px}
.color-name-input{flex:1;min-width:140px;padding:9px 13px;border:1.5px solid #e0e0e0;border-radius:8px;font-size:13px;font-family:inherit}
.color-name-input:focus{outline:none;border-color:#2563eb}
.btn-add-color{height:40px;padding:0 16px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;white-space:nowrap}
/* Color card */
.color-cards-list{display:flex;flex-direction:column;gap:14px}
.color-card{border:1.5px solid #e0e0e0;border-radius:12px;padding:16px;background:#fafafa;position:relative}
.color-card-header{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.color-dot{width:26px;height:26px;border-radius:50%;border:2px solid rgba(0,0,0,.12);flex-shrink:0}
.color-card-name-lbl{font-size:14px;font-weight:700}
.color-hex-lbl{font-size:11px;color:#aaa}
.color-card-remove{position:absolute;top:12px;right:12px;width:26px;height:26px;background:#fee2e2;color:#e53e3e;border:none;border-radius:50%;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;line-height:1}
.color-card-body{display:grid;grid-template-columns:160px 1fr;gap:16px}
@media(max-width:600px){.color-card-body{grid-template-columns:1fr}}
/* Color main image upload box */
.color-thumb-box{border:2px dashed #e0e0e0;border-radius:10px;padding:10px;text-align:center;cursor:pointer;background:#fff;min-height:130px;display:flex;flex-direction:column;align-items:center;justify-content:center;transition:.2s;gap:6px}
.color-thumb-box:hover{border-color:#2563eb;background:#f0f6ff}
.color-thumb-box img{max-width:100%;max-height:108px;object-fit:contain;border-radius:6px;display:none}
.color-thumb-box.has-img img{display:block}
.color-thumb-box.has-img .ctb-placeholder{display:none}
.ctb-placeholder i{font-size:22px;color:#ccc;display:block}
.ctb-placeholder p{font-size:11px;color:#aaa;margin:0}
/* Color gallery */
.color-gal-box{border:1.5px solid #e8e8e8;border-radius:10px;padding:12px;background:#fff;height:100%}
.color-gal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;font-size:12px;font-weight:700;color:#666}
.btn-add-cgal{padding:5px 12px;background:#f0f6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit}
.cgal-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:6px}
.cgal-item{position:relative;aspect-ratio:1;border:1.5px solid #e0e0e0;border-radius:6px;overflow:hidden;background:#f8f9fa}
.cgal-item img{width:100%;height:100%;object-fit:contain;padding:3px}
.cgal-remove{position:absolute;top:2px;right:2px;width:18px;height:18px;background:#e53e3e;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:10px;border:none}
.cgal-empty{font-size:11px;color:#ccc;text-align:center;padding:12px;grid-column:1/-1}
/* Main image */
.main-img-box{border:2px dashed #e0e0e0;border-radius:12px;padding:20px;text-align:center;cursor:pointer;background:#fafafa;min-height:200px;display:flex;flex-direction:column;align-items:center;justify-content:center;transition:.2s}
.main-img-box:hover{border-color:#2563eb;background:#f0f6ff}
.main-img-box img{max-width:100%;max-height:180px;object-fit:contain;border-radius:8px;display:none}
.main-img-box.has-img img{display:block}
.main-img-box.has-img .upload-placeholder{display:none}
.upload-placeholder i{font-size:32px;color:#ccc;display:block;margin-bottom:8px}
.upload-placeholder p{font-size:12px;color:#aaa;margin:0}
.img-actions{display:flex;gap:8px;margin-top:10px}
.btn-replace{flex:1;padding:7px 0;background:#f0f0f0;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit}
.btn-remove-img{flex:1;padding:7px 0;background:#fff0f0;color:#e53e3e;border:1.5px solid #fca5a5;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit}
/* Global gallery */
.gal-box{border:1.5px solid #e8e8e8;border-radius:12px;padding:18px}
.gal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;font-size:13px;font-weight:600}
.btn-add-gal{padding:7px 16px;background:#2563eb;color:#fff;border:none;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit}
.gal-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.gal-item{position:relative;border:1.5px solid #e0e0e0;border-radius:8px;overflow:hidden;aspect-ratio:1;background:#f8f9fa}
.gal-item img{width:100%;height:100%;object-fit:contain;padding:6px}
.gal-item-remove{position:absolute;top:4px;right:4px;width:22px;height:22px;background:#e53e3e;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:12px;border:none}
.gal-item-num{position:absolute;bottom:4px;left:6px;font-size:10px;font-weight:700;color:#888}
.gal-empty{text-align:center;padding:24px;color:#ccc;font-size:13px;grid-column:1/-1}
/* Sizechart */
.sc-box{border:2px dashed #e0e0e0;border-radius:12px;padding:20px;text-align:center;cursor:pointer;background:#fafafa;transition:.2s}
.sc-box:hover{border-color:#2563eb;background:#f0f6ff}
.sc-box img{max-width:100%;max-height:200px;object-fit:contain;border-radius:8px;display:none}
.sc-box.has-img img{display:block}
.sc-box.has-img .upload-placeholder{display:none}
/* Actions */
.pf-actions{display:flex;gap:12px;padding-top:8px}
.btn-save{height:48px;padding:0 32px;background:#000;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:8px}
.btn-save:hover{background:#222}
.btn-back{height:48px;padding:0 24px;background:#fff;color:#000;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:.2s}
.btn-back:hover{border-color:#000}
.img-section{display:grid;grid-template-columns:260px 1fr;gap:24px}
@media(max-width:700px){.img-section{grid-template-columns:1fr}}
</style>

<div class="pf-wrap">
  <div class="pf-header">
    <h1><i class="bi bi-plus-circle me-2"></i>Add Product</h1>
    <a href="{{ route('products.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Back</a>
  </div>

  @if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
  @endif

  {{-- ✅ form id set hai for JS submit intercept --}}
  <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- 1. Basic Info --}}
    <div class="pf-card">
      <div class="pf-card-title"><i class="bi bi-info-circle"></i> Basic Information</div>
      <div class="pf-row" style="margin-bottom:18px">
        <div class="pf-group">
          <label class="pf-label">Product Name <span style="color:#e53e3e">*</span></label>
          <input type="text" name="name" class="pf-input" value="{{ old('name') }}" required placeholder="e.g. Pro Football Jersey">
        </div>
      </div>
      <div class="pf-row cols-2">
        <div class="pf-group">
          <label class="pf-label">Price ($) <span style="color:#e53e3e">*</span></label>
          <input type="number" step="0.01" min="0" name="price" class="pf-input" value="{{ old('price') }}" required placeholder="0.00">
        </div>
        <div class="pf-group">
          <label class="pf-label">Description <small>(optional)</small></label>
          <textarea name="description" class="pf-textarea" placeholder="Product description...">{{ old('description') }}</textarea>
        </div>
      </div>
    </div>

    {{-- 2. Category --}}
    <div class="pf-card">
      <div class="pf-card-title"><i class="bi bi-tag"></i> Category</div>
      <div class="pf-row cols-2">
        <div class="pf-group">
          <label class="pf-label">Category <small>(optional)</small></label>
          <select name="category_id" id="categorySelect" class="pf-select">
            <option value="">-- No Category --</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id ? 'selected':'' }}>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="pf-group" id="subcategoryWrapper" style="display:none">
          <label class="pf-label">Subcategory <small>(optional)</small></label>
          <select name="subcategory_id" id="subcategorySelect" class="pf-select">
            <option value="">-- No Subcategory --</option>
          </select>
        </div>
      </div>
    </div>

    {{-- 3. Stock --}}
    <div class="pf-card">
      <div class="pf-card-title"><i class="bi bi-box-seam"></i> Stock</div>
      <div class="pf-toggle-row">
        <div class="pf-toggle-info">
          <strong>In Stock</strong>
          <span>Product available for purchase</span>
        </div>
        <label class="pf-toggle">
          {{-- ✅ value="1" — checkbox sends "1" when checked, nothing when unchecked --}}
          <input type="checkbox" name="in_stock" id="inStockToggle" value="1" checked>
          <span class="pf-toggle-slider"></span>
        </label>
      </div>
      <div style="margin-top:14px">
        <div class="pf-group" style="max-width:220px">
          <label class="pf-label">Stock Quantity <small>(optional)</small></label>
          <input type="number" min="0" name="stock_quantity" id="stockQtyInput" class="pf-input" value="{{ old('stock_quantity') }}" placeholder="Leave empty = unlimited">
          <span class="pf-hint">Frontend par is se zyada quantity select nahi hogi</span>
        </div>
      </div>
    </div>

    {{-- 4. Shipping --}}
    <div class="pf-card">
      <div class="pf-card-title"><i class="bi bi-truck"></i> Shipping</div>
      <div class="pf-toggle-row">
        <div class="pf-toggle-info">
          <strong>Enable Shipping</strong>
          <span>This product requires shipping</span>
        </div>
        <label class="pf-toggle">
          <input type="checkbox" name="shipping_enabled" id="shippingToggle" value="1" checked>
          <span class="pf-toggle-slider"></span>
        </label>
      </div>
      <div id="shippingFields" style="margin-top:16px">
        <div class="pf-row cols-2">
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

    {{-- 5. Sizes --}}
    <div class="pf-card">
      <div class="pf-card-title"><i class="bi bi-rulers"></i> Sizes</div>
      <div class="sizes-trigger" id="sizesTrigger">
        <i class="bi bi-plus-circle"></i><span>Select Sizes</span>
        <i class="bi bi-chevron-down" id="sizesChevron"></i>
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

    {{-- 6. Colors + Per-color Images --}}
    <div class="pf-card">
      <div class="pf-card-title"><i class="bi bi-palette"></i> Colors & Images</div>
      <p style="font-size:12px;color:#888;margin-bottom:14px">
        Har color ke liye alag thumbnail aur gallery upload kar sakte ho — <strong>optional hai</strong>.
        Jo color select ho frontend par, usi ki images dikhegi.
      </p>
      <div class="color-add-row">
        <input type="color" id="colorHex" class="color-hex-input" value="#000000">
        <input type="text" id="colorName" class="color-name-input" placeholder="Color name (e.g. Black, Navy Blue)">
        <button type="button" class="btn-add-color" onclick="addColor()"><i class="bi bi-plus"></i> Add Color</button>
      </div>
      <div class="color-cards-list" id="colorCardsList"></div>
      {{-- ✅ This hidden field stores final JSON before submit --}}
      <input type="hidden" name="colors_json" id="colorsJson" value="[]">
    </div>

    {{-- 7. Main Image + Global Gallery --}}
    <div class="pf-card">
      <div class="pf-card-title"><i class="bi bi-images"></i> Product Images</div>
      <p style="font-size:12px;color:#888;margin-bottom:16px">Main image = default thumbnail. Global gallery = sab par dikhe.</p>
      <div class="img-section">
        <div>
          <div class="pf-label" style="margin-bottom:8px">Main Image (Default)</div>
          <div class="main-img-box" id="mainImgBox" onclick="document.getElementById('mainImgInput').click()">
            <div class="upload-placeholder"><i class="bi bi-cloud-upload"></i><p>Click to upload</p></div>
            <img id="mainImgPreview" src="" alt="">
          </div>
          <div class="img-actions" id="mainImgActions" style="display:none">
            <button type="button" class="btn-replace" onclick="document.getElementById('mainImgInput').click()"><i class="bi bi-arrow-repeat"></i> Replace</button>
            <button type="button" class="btn-remove-img" onclick="removeMainImg()"><i class="bi bi-trash"></i> Remove</button>
          </div>
          <input type="file" name="image" id="mainImgInput" accept="image/*" style="display:none">
        </div>
        <div>
          <div class="gal-box">
            <div class="gal-header">
              <span>Global Gallery</span>
              <button type="button" class="btn-add-gal" onclick="document.getElementById('galInput').click()"><i class="bi bi-plus"></i> Add Images</button>
            </div>
            <div class="gal-grid" id="galGrid">
              <div class="gal-empty" id="galEmpty">No images added.</div>
            </div>
          </div>
          <input type="file" name="gallery_images[]" id="galInput" accept="image/*" multiple style="display:none">
        </div>
      </div>
    </div>

    {{-- 8. Size Chart --}}
    <div class="pf-card">
      <div class="pf-card-title"><i class="bi bi-table"></i> Size Chart Image</div>
      <p style="font-size:13px;color:#777;margin-bottom:14px">Graphic designer ki banai size chart image upload karo.</p>
      <div class="sc-box" id="scBox" onclick="document.getElementById('scInput').click()">
        <div class="upload-placeholder"><i class="bi bi-cloud-upload" style="font-size:28px;color:#ccc;display:block;margin-bottom:6px"></i><p style="font-size:12px;color:#aaa;margin:0">Click to upload size chart</p></div>
        <img id="scPreview" src="" alt="">
      </div>
      <div id="scActions" style="display:none;margin-top:8px">
        <button type="button" class="btn-replace" style="padding:6px 14px" onclick="document.getElementById('scInput').click()"><i class="bi bi-arrow-repeat"></i> Replace</button>
        <button type="button" class="btn-remove-img" style="padding:6px 14px;margin-left:8px" onclick="removeSizeChart()"><i class="bi bi-trash"></i> Remove</button>
      </div>
      <input type="file" name="size_chart_image" id="scInput" accept="image/*" style="display:none">
    </div>

    <div class="pf-actions">
      <button type="submit" class="btn-save"><i class="bi bi-check-lg"></i> Save Product</button>
      <a href="{{ route('products.index') }}" class="btn-back">Cancel</a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

  // ── Category / Subcategory ──────────────────────────────────
  const categories = @json($categories->load('subcategories'));
  document.getElementById('categorySelect').addEventListener('change', function () {
    const sw = document.getElementById('subcategoryWrapper');
    const ss = document.getElementById('subcategorySelect');
    ss.innerHTML = '<option value="">-- No Subcategory --</option>';
    const cat = categories.find(c => c.id === parseInt(this.value));
    if (cat && cat.subcategories && cat.subcategories.length) {
      cat.subcategories.forEach(s => {
        const o = document.createElement('option'); o.value = s.id; o.textContent = s.name; ss.appendChild(o);
      });
      sw.style.display = 'block';
    } else { sw.style.display = 'none'; }
  });

  // ── Shipping toggle ─────────────────────────────────────────
  const st = document.getElementById('shippingToggle');
  const sf = document.getElementById('shippingFields');
  sf.style.display = st.checked ? 'block' : 'none';
  st.addEventListener('change', () => sf.style.display = st.checked ? 'block' : 'none');

  // ── Main Image ──────────────────────────────────────────────
  document.getElementById('mainImgInput').addEventListener('change', function () {
    if (this.files && this.files[0]) {
      document.getElementById('mainImgPreview').src = URL.createObjectURL(this.files[0]);
      document.getElementById('mainImgBox').classList.add('has-img');
      document.getElementById('mainImgActions').style.display = 'flex';
    }
  });
  window.removeMainImg = function () {
    document.getElementById('mainImgInput').value = '';
    document.getElementById('mainImgPreview').src = '';
    document.getElementById('mainImgBox').classList.remove('has-img');
    document.getElementById('mainImgActions').style.display = 'none';
  };

  // ── Global Gallery ──────────────────────────────────────────
  let galFiles = [];
  document.getElementById('galInput').addEventListener('change', function () {
    Array.from(this.files).forEach(file => {
      galFiles.push(file);
      const i = galFiles.length - 1;
      const ge = document.getElementById('galEmpty'); if (ge) ge.remove();
      const d = document.createElement('div'); d.className = 'gal-item'; d.dataset.i = i;
      d.innerHTML = `<img src="${URL.createObjectURL(file)}"><button type="button" class="gal-item-remove" onclick="removeGalItem(this)"><i class="bi bi-x"></i></button><span class="gal-item-num">#${document.getElementById('galGrid').children.length + 1}</span>`;
      document.getElementById('galGrid').appendChild(d);
    });
    rebuildGal(); this.value = '';
  });
  window.removeGalItem = function (btn) {
    const item = btn.closest('.gal-item');
    galFiles[parseInt(item.dataset.i)] = null; item.remove(); rebuildGal();
    const gg = document.getElementById('galGrid');
    if (!gg.querySelector('.gal-item')) gg.innerHTML = '<div class="gal-empty" id="galEmpty">No images added.</div>';
  };
  function rebuildGal() {
    const dt = new DataTransfer(); galFiles.filter(f => f).forEach(f => dt.items.add(f));
    document.getElementById('galInput').files = dt.files;
  }

  // ── Sizes ───────────────────────────────────────────────────
  let selectedSizes = [];
  document.getElementById('sizesTrigger').addEventListener('click', function () {
    const d = document.getElementById('sizesDropdown'); d.classList.toggle('open');
    document.getElementById('sizesChevron').className = d.classList.contains('open') ? 'bi bi-chevron-up' : 'bi bi-chevron-down';
  });
  document.querySelectorAll('.size-checkbox').forEach(cb => {
    cb.addEventListener('change', function () {
      const all = ['YXS','YS','YM','YL','YXL','S','M','L','XL','2XL'];
      if (this.value === 'All') {
        if (this.checked) { selectedSizes = [...all]; document.querySelectorAll('.size-checkbox').forEach(c => { if (c.value !== 'All') c.checked = true; }); }
        else { selectedSizes = []; document.querySelectorAll('.size-checkbox').forEach(c => c.checked = false); }
      } else {
        if (this.checked) { if (!selectedSizes.includes(this.value)) selectedSizes.push(this.value); }
        else { selectedSizes = selectedSizes.filter(s => s !== this.value); document.getElementById('sz_All').checked = false; }
      }
      renderSizeTags();
    });
  });
  function renderSizeTags() {
    const tags = document.getElementById('sizesTags'); const hid = document.getElementById('sizesHidden');
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

  // ── Colors + Per-color Images ───────────────────────────────
  // colors array: [{name, hex}]  — stored as hidden field colors_json
  // Image files stored separately: colorMainFiles[idx], colorGalFiles[idx][]
  let colors        = [];
  let colorCounter  = 0; // monotonic, never reused after delete
  let colorMainFiles = {}; // { counter_idx: File }
  let colorGalFiles  = {}; // { counter_idx: [File|null, ...] }

  window.addColor = function () {
    const name = document.getElementById('colorName').value.trim();
    const hex  = document.getElementById('colorHex').value;
    if (!name) { alert('Color ka naam daalo!'); return; }
    const cid = colorCounter++;
    colors.push({ cid, name, hex });
    renderColorCard(cid, name, hex);
    updateColorsJson();
    document.getElementById('colorName').value = '';
  };

  function renderColorCard(cid, name, hex) {
    const list = document.getElementById('colorCardsList');
    const card = document.createElement('div');
    card.className = 'color-card'; card.id = `cc-${cid}`;
    card.innerHTML = `
      <button type="button" class="color-card-remove" onclick="removeColor(${cid})" title="Remove">×</button>
      <div class="color-card-header">
        <span class="color-dot" style="background:${hex};${hex==='#ffffff'?'border-color:#ddd':''}"></span>
        <span class="color-card-name-lbl">${name}</span>
        <span class="color-hex-lbl">${hex}</span>
      </div>
      <div class="color-card-body">
        <!-- Per-color thumbnail -->
        <div>
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#888;margin-bottom:6px">Thumbnail</div>
          <div class="color-thumb-box" id="ctb-${cid}" onclick="document.getElementById('cti-${cid}').click()">
            <div class="ctb-placeholder"><i class="bi bi-camera"></i><p>Click to upload</p></div>
            <img id="ctp-${cid}" src="">
          </div>
          <input type="file" id="cti-${cid}" accept="image/*" style="display:none" onchange="handleColorThumb(${cid},this)">
        </div>
        <!-- Per-color gallery -->
        <div>
          <div class="color-gal-box">
            <div class="color-gal-header">
              <span>Gallery</span>
              <button type="button" class="btn-add-cgal" onclick="document.getElementById('cgi-${cid}').click()"><i class="bi bi-plus"></i> Add</button>
            </div>
            <div class="cgal-grid" id="cgal-${cid}">
              <div class="cgal-empty">No images added</div>
            </div>
          </div>
          <input type="file" id="cgi-${cid}" accept="image/*" multiple style="display:none" onchange="handleColorGal(${cid},this)">
        </div>
      </div>`;
    list.appendChild(card);
  }

  window.handleColorThumb = function (cid, input) {
    if (!input.files || !input.files[0]) return;
    colorMainFiles[cid] = input.files[0];
    document.getElementById(`ctp-${cid}`).src = URL.createObjectURL(input.files[0]);
    document.getElementById(`ctb-${cid}`).classList.add('has-img');
  };

  window.handleColorGal = function (cid, input) {
    if (!input.files || !input.files.length) return;
    if (!colorGalFiles[cid]) colorGalFiles[cid] = [];
    const grid  = document.getElementById(`cgal-${cid}`);
    const empty = grid.querySelector('.cgal-empty'); if (empty) empty.remove();
    Array.from(input.files).forEach(file => {
      colorGalFiles[cid].push(file);
      const gi = colorGalFiles[cid].length - 1;
      const d  = document.createElement('div'); d.className = 'cgal-item'; d.dataset.cid = cid; d.dataset.gi = gi;
      d.innerHTML = `<img src="${URL.createObjectURL(file)}"><button type="button" class="cgal-remove" onclick="removeCGal(this)"><i class="bi bi-x"></i></button>`;
      grid.appendChild(d);
    });
    input.value = '';
  };

  window.removeCGal = function (btn) {
    const item = btn.closest('.cgal-item');
    const cid  = parseInt(item.dataset.cid); const gi = parseInt(item.dataset.gi);
    if (colorGalFiles[cid]) colorGalFiles[cid][gi] = null;
    item.remove();
    const grid = document.getElementById(`cgal-${cid}`);
    if (!grid.querySelector('.cgal-item')) grid.innerHTML = '<div class="cgal-empty">No images added</div>';
  };

  window.removeColor = function (cid) {
    colors = colors.filter(c => c.cid !== cid);
    const card = document.getElementById(`cc-${cid}`); if (card) card.remove();
    delete colorMainFiles[cid]; delete colorGalFiles[cid];
    updateColorsJson();
  };

  function updateColorsJson () {
    // Store only name+hex — images handled separately via file inputs on submit
    const clean = colors.map(c => ({ name: c.name, hex: c.hex }));
    document.getElementById('colorsJson').value = JSON.stringify(clean);
  }

  // ── On Submit: inject file inputs for color images ──────────
  // Controller expects: color_image_0, color_gallery_0[], color_image_1, etc.
  // Index = position in final colors array (0,1,2...)
  document.getElementById('productForm').addEventListener('submit', function (e) {
    // Update final JSON (cleaned, re-indexed)
    const finalColors = colors.map(c => ({ name: c.name, hex: c.hex }));
    document.getElementById('colorsJson').value = JSON.stringify(finalColors);

    // Inject file inputs for each color by its new sequential index
    colors.forEach((c, newIdx) => {
      const origCid = c.cid;

      // Thumbnail
      if (colorMainFiles[origCid]) {
        const inp = document.createElement('input');
        inp.type = 'file'; inp.name = `color_image_${newIdx}`; inp.style.display = 'none';
        const dt = new DataTransfer(); dt.items.add(colorMainFiles[origCid]); inp.files = dt.files;
        this.appendChild(inp);
      }

      // Gallery
      const gfs = (colorGalFiles[origCid] || []).filter(f => f);
      if (gfs.length) {
        const dt = new DataTransfer(); gfs.forEach(f => dt.items.add(f));
        const inp = document.createElement('input');
        inp.type = 'file'; inp.name = `color_gallery_${newIdx}[]`; inp.multiple = true; inp.style.display = 'none';
        inp.files = dt.files;
        this.appendChild(inp);
      }
    });
  });

  // ── Size Chart ──────────────────────────────────────────────
  document.getElementById('scInput').addEventListener('change', function () {
    if (this.files && this.files[0]) {
      document.getElementById('scPreview').src = URL.createObjectURL(this.files[0]);
      document.getElementById('scBox').classList.add('has-img');
      document.getElementById('scActions').style.display = 'block';
    }
  });
  window.removeSizeChart = function () {
    document.getElementById('scInput').value = '';
    document.getElementById('scPreview').src = '';
    document.getElementById('scBox').classList.remove('has-img');
    document.getElementById('scActions').style.display = 'none';
  };
});
</script>
@endsection
