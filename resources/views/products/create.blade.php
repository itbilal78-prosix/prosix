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
.pf-card{background:#fff;border:1.5px solid #e8e8e8;border-radius:14px;padding:24px 28px;height:100%}
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

/* ── Colors ── */
.color-add-row{display:flex;align-items:stretch;gap:8px;margin-bottom:16px;flex-wrap:wrap}
.btn-open-color-popup{flex:1;min-width:160px;padding:10px 16px;background:#000;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:8px}
.btn-open-color-popup:hover{background:#222}
.btn-manual-color{padding:10px 14px;background:#fff;color:#000;border:1.5px solid #e0e0e0;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:6px;transition:.2s}
.btn-manual-color:hover{border-color:#000}
.color-hex-input{width:42px;height:42px;border:1.5px solid #e0e0e0;border-radius:8px;cursor:pointer;padding:2px}
.color-name-input{flex:1;min-width:130px;padding:9px 13px;border:1.5px solid #e0e0e0;border-radius:8px;font-size:13px;font-family:inherit}
.color-name-input:focus{outline:none;border-color:#000}

/* Color cards */
.color-cards-list{display:flex;flex-direction:column;gap:12px}
.color-card{border:1.5px solid #e0e0e0;border-radius:12px;padding:14px;background:#fafafa;position:relative}
.color-card-header{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.color-dot{width:24px;height:24px;border-radius:6px;border:2px solid rgba(0,0,0,.1);flex-shrink:0}
.color-card-name-lbl{font-size:13px;font-weight:700}
.color-hex-lbl{font-size:11px;color:#aaa}
.color-card-remove{position:absolute;top:10px;right:10px;width:24px;height:24px;background:#000;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:13px;display:flex;align-items:center;justify-content:center}
.color-card-body{display:grid;grid-template-columns:150px 1fr;gap:14px}
@media(max-width:580px){.color-card-body{grid-template-columns:1fr}}

/* Color thumb upload */
.color-thumb-box{border:2px dashed #e0e0e0;border-radius:10px;padding:10px;text-align:center;cursor:pointer;background:#fff;min-height:120px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;transition:.2s}
.color-thumb-box:hover{border-color:#000}
.color-thumb-box img{max-width:100%;max-height:100px;object-fit:contain;border-radius:6px;display:none}
.color-thumb-box.has-img img{display:block}
.color-thumb-box.has-img .ctb-placeholder{display:none}
.ctb-placeholder i{font-size:20px;color:#ccc;display:block}
.ctb-placeholder p{font-size:10px;color:#aaa;margin:3px 0 0}

/* Gallery */
.color-gal-box{border:1.5px solid #e8e8e8;border-radius:10px;padding:12px;background:#fff}
.color-gal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#666}
.btn-add-cgal{padding:4px 12px;background:#000;color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit}
.cgal-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:5px}
.cgal-item{position:relative;aspect-ratio:1;border:1.5px solid #e0e0e0;border-radius:6px;overflow:hidden;background:#f8f9fa}
.cgal-item img{width:100%;height:100%;object-fit:contain;padding:3px}
.cgal-remove{position:absolute;top:2px;right:2px;width:16px;height:16px;background:#000;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:9px;border:none}
.cgal-empty{font-size:10px;color:#ccc;text-align:center;padding:10px;grid-column:1/-1}

/* Main image */
.main-img-box{border:2px dashed #e0e0e0;border-radius:12px;padding:20px;text-align:center;cursor:pointer;background:#fafafa;min-height:190px;display:flex;flex-direction:column;align-items:center;justify-content:center;transition:.2s}
.main-img-box:hover{border-color:#000}
.main-img-box img{max-width:100%;max-height:170px;object-fit:contain;border-radius:8px;display:none}
.main-img-box.has-img img{display:block}
.main-img-box.has-img .upload-placeholder{display:none}
.upload-placeholder i{font-size:28px;color:#ccc;display:block;margin-bottom:6px}
.upload-placeholder p{font-size:11px;color:#aaa;margin:0}
.img-actions{display:flex;gap:8px;margin-top:8px}
.btn-replace{flex:1;padding:7px 0;background:#f0f0f0;border:1.5px solid #e0e0e0;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;font-family:inherit}
.btn-replace:hover{border-color:#000}
.btn-remove-img{flex:1;padding:7px 0;background:#fff;color:#000;border:1.5px solid #000;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;font-family:inherit}
.btn-remove-img:hover{background:#000;color:#fff}
.img-section{display:grid;grid-template-columns:240px 1fr;gap:20px}
@media(max-width:640px){.img-section{grid-template-columns:1fr}}

/* Gallery */
.gal-box{border:1.5px solid #e8e8e8;border-radius:12px;padding:16px}
.gal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;font-size:12px;font-weight:700}
.btn-add-gal{padding:6px 14px;background:#000;color:#fff;border:none;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit}
.gal-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
.gal-item{position:relative;border:1.5px solid #e0e0e0;border-radius:8px;overflow:hidden;aspect-ratio:1;background:#f8f9fa}
.gal-item img{width:100%;height:100%;object-fit:contain;padding:6px}
.gal-item-remove{position:absolute;top:3px;right:3px;width:20px;height:20px;background:#000;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:10px;border:none}
.gal-empty{text-align:center;padding:20px;color:#ccc;font-size:12px;grid-column:1/-1}

/* Size chart */
.sc-box{border:2px dashed #e0e0e0;border-radius:12px;padding:20px;text-align:center;cursor:pointer;background:#fafafa;transition:.2s}
.sc-box:hover{border-color:#000}
.sc-box img{max-width:100%;max-height:180px;object-fit:contain;border-radius:8px;display:none}
.sc-box.has-img img{display:block}
.sc-box.has-img .upload-placeholder{display:none}

/* Actions */
.pf-actions{display:flex;gap:12px;padding-top:8px}
.btn-save{height:46px;padding:0 32px;background:#000;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:8px}
.btn-save:hover{background:#222}
.btn-back{height:46px;padding:0 22px;background:#fff;color:#000;border:1.5px solid #000;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:.2s}
.btn-back:hover{background:#000;color:#fff}

/* ── COLOR POPUP MODAL ── */
.color-popup-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:none;align-items:center;justify-content:center}
.color-popup-overlay.open{display:flex}
.color-popup{background:#fff;border-radius:16px;padding:28px;width:520px;max-width:95vw;max-height:80vh;display:flex;flex-direction:column;gap:0}
.color-popup-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
.color-popup-header h3{font-size:16px;font-weight:800}
.btn-close-popup{width:32px;height:32px;background:#000;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center}
.color-popup-search{width:100%;padding:10px 14px;border:1.5px solid #e0e0e0;border-radius:8px;font-size:13px;font-family:inherit;margin-bottom:14px}
.color-popup-search:focus{outline:none;border-color:#000}
.color-popup-list{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;overflow-y:auto;max-height:320px;padding-right:4px}
.color-popup-list::-webkit-scrollbar{width:4px}
.color-popup-list::-webkit-scrollbar-track{background:#f0f0f0;border-radius:4px}
.color-popup-list::-webkit-scrollbar-thumb{background:#000;border-radius:4px}
.color-popup-item{border:1.5px solid #e0e0e0;border-radius:10px;padding:10px 8px;cursor:pointer;text-align:center;transition:.2s;position:relative}
.color-popup-item:hover{border-color:#000;transform:translateY(-2px)}
.color-popup-item.selected{border-color:#000;border-width:2.5px}
.color-popup-item.selected::after{content:'✓';position:absolute;top:4px;right:6px;font-size:11px;font-weight:900;color:#000}
.color-popup-swatch{width:36px;height:36px;border-radius:8px;margin:0 auto 6px;border:1.5px solid rgba(0,0,0,.1)}
.color-popup-name{font-size:11px;font-weight:700;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.color-popup-hex{font-size:10px;color:#aaa}
.color-popup-footer{display:flex;gap:10px;margin-top:16px;padding-top:16px;border-top:1px solid #f0f0f0}
.btn-popup-add{flex:1;height:42px;background:#000;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit}
.btn-popup-add:hover{background:#222}
.btn-popup-cancel{padding:0 20px;height:42px;background:#fff;color:#000;border:1.5px solid #e0e0e0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit}
.btn-popup-cancel:hover{border-color:#000}
.popup-selected-count{font-size:12px;color:#666;display:flex;align-items:center}

/* Manual color row (collapsed) */
.manual-color-section{border:1.5px solid #e0e0e0;border-radius:10px;padding:14px;margin-bottom:14px;display:none}
.manual-color-section.open{display:block}
.manual-color-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
</style>

<div class="pf-wrap">
  <div class="pf-header">
    <h1><i class="bi bi-plus-circle me-2"></i>Add Product</h1>
    <a href="{{ route('products.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Back</a>
  </div>

  @if($errors->any())
    <div class="alert alert-danger mb-3" style="border-radius:10px"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
  @endif

  <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- ROW 1: Basic Info + Navigation/Category --}}
    <div class="pf-grid">

      {{-- 1. Basic Info --}}
      <div class="pf-card">
        <div class="pf-card-title"><i class="bi bi-info-circle"></i> Basic Information</div>
        <div class="pf-group">
          <label class="pf-label">Product Name <span style="color:#e53e3e">*</span></label>
          <input type="text" name="name" class="pf-input" value="{{ old('name') }}" required placeholder="e.g. Pro Football Jersey">
        </div>
        <div class="pf-row-2">
          <div class="pf-group">
            <label class="pf-label">Price ($) <span style="color:#e53e3e">*</span></label>
            <input type="number" step="0.01" min="0" name="price" class="pf-input" value="{{ old('price') }}" required placeholder="0.00">
          </div>
          <div class="pf-group">
            <label class="pf-label">Product Type <small>(optional)</small></label>
            <input type="text" name="type" class="pf-input" value="{{ old('type') }}" placeholder="e.g. Jersey, T-Shirt">
          </div>
        </div>
        <div class="pf-group">
          <label class="pf-label">Description <small>(optional)</small></label>
          <textarea name="description" class="pf-textarea" placeholder="Product description...">{{ old('description') }}</textarea>
        </div>
      </div>

      {{-- 2. Navigation → Category → Subcategory --}}
      <div class="pf-card">
        <div class="pf-card-title"><i class="bi bi-diagram-3"></i> Navigation & Category</div>

        {{-- Navigation Menu --}}
        <div class="pf-group">
          <label class="pf-label">Navigation Menu <small>(optional)</small></label>
          <select id="navMenuSelect" class="pf-select">
            <option value="">-- Select Navigation --</option>
            @foreach($navigations ?? [] as $nav)
              <option value="{{ $nav->id }}">{{ $nav->title ?? $nav->name }}</option>
            @endforeach
          </select>
          <span class="pf-hint">Select navigation to auto-filter categories</span>
        </div>

        {{-- Category --}}
        <div class="pf-group">
          <label class="pf-label">Category <small>(optional)</small></label>
          <select name="category_id" id="categorySelect" class="pf-select">
            <option value="">-- No Category --</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}"
                data-nav="{{ $cat->navigation_id ?? '' }}"
                {{ old('category_id')==$cat->id ? 'selected':'' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Subcategory --}}
        <div class="pf-group" id="subcategoryWrapper" style="display:none">
          <label class="pf-label">Subcategory <small>(optional)</small></label>
          <select name="subcategory_id" id="subcategorySelect" class="pf-select">
            <option value="">-- No Subcategory --</option>
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
          <div class="pf-toggle-info"><strong>In Stock</strong><span>Product available for purchase</span></div>
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
          <div class="pf-toggle-info"><strong>Enable Shipping</strong><span>This product requires shipping</span></div>
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

    {{-- ROW 3: Sizes + Colors --}}
    <div class="pf-grid">

      {{-- 5. Sizes --}}
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

      {{-- 6. Colors (Backend + Manual) --}}
      <div class="pf-card">
        <div class="pf-card-title"><i class="bi bi-palette"></i> Colors & Images</div>
        <p style="font-size:11px;color:#888;margin-bottom:14px">Select colors from backend or add custom. Each color can have its own thumbnail and gallery.</p>

        {{-- Buttons --}}
        <div class="color-add-row">
          <button type="button" class="btn-open-color-popup" onclick="openColorPopup()">
            <i class="bi bi-grid-3x3-gap"></i> Select from Backend Colors
          </button>
          <button type="button" class="btn-manual-color" onclick="toggleManualColor()">
            <i class="bi bi-pencil"></i> Custom
          </button>
        </div>

        {{-- Manual Color Row (hidden by default) --}}
        <div class="manual-color-section" id="manualColorSection">
          <div class="manual-color-row">
            <input type="color" id="colorHex" class="color-hex-input" value="#000000">
            <input type="text" id="colorName" class="color-name-input" placeholder="Color name (e.g. Black, Navy Blue)">
            <button type="button" style="padding:9px 16px;background:#000;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;white-space:nowrap" onclick="addColorManual()">
              <i class="bi bi-plus"></i> Add
            </button>
          </div>
        </div>

        <div class="color-cards-list" id="colorCardsList"></div>
        <input type="hidden" name="colors_json" id="colorsJson" value="[]">
      </div>

    </div>{{-- end row 3 --}}

    {{-- ROW 4: Images (full width) --}}
    <div class="pf-grid-full">
      <div class="pf-card">
        <div class="pf-card-title"><i class="bi bi-images"></i> Product Images</div>
        <p style="font-size:11px;color:#888;margin-bottom:16px">Main image = default thumbnail. Global gallery = shown on all color selections.</p>
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
    </div>

    {{-- ROW 5: Size Chart (full width) --}}
    <div class="pf-grid-full">
      <div class="pf-card">
        <div class="pf-card-title"><i class="bi bi-table"></i> Size Chart Image</div>
        <p style="font-size:11px;color:#777;margin-bottom:14px">Upload your size chart image (optional).</p>
        <div class="sc-box" id="scBox" onclick="document.getElementById('scInput').click()">
          <div class="upload-placeholder"><i class="bi bi-cloud-upload" style="font-size:26px;color:#ccc;display:block;margin-bottom:6px"></i><p style="font-size:11px;color:#aaa;margin:0">Click to upload size chart</p></div>
          <img id="scPreview" src="" alt="">
        </div>
        <div id="scActions" style="display:none;margin-top:8px;display:flex;gap:8px">
          <button type="button" class="btn-replace" style="padding:6px 14px" onclick="document.getElementById('scInput').click()"><i class="bi bi-arrow-repeat"></i> Replace</button>
          <button type="button" class="btn-remove-img" style="padding:6px 14px" onclick="removeSizeChart()"><i class="bi bi-trash"></i> Remove</button>
        </div>
        <input type="file" name="size_chart_image" id="scInput" accept="image/*" style="display:none">
      </div>
    </div>

    <div class="pf-actions">
      <button type="submit" class="btn-save"><i class="bi bi-check-lg"></i> Save Product</button>
      <a href="{{ route('products.index') }}" class="btn-back">Cancel</a>
    </div>
  </form>
</div>

{{-- ══ COLOR POPUP MODAL ══ --}}
<div class="color-popup-overlay" id="colorPopupOverlay">
  <div class="color-popup">
    <div class="color-popup-header">
      <h3><i class="bi bi-palette me-2"></i>Select Colors</h3>
      <button class="btn-close-popup" onclick="closeColorPopup()">×</button>
    </div>
    <input type="text" class="color-popup-search" id="colorSearch" placeholder="Search color name..." oninput="filterColors()">
    <div class="color-popup-list" id="colorPopupList">
      {{-- Populated by JS from backend --}}
    </div>
    <div class="color-popup-footer">
      <span class="popup-selected-count" id="popupSelectedCount">0 selected</span>
      <button type="button" class="btn-popup-cancel" onclick="closeColorPopup()">Cancel</button>
      <button type="button" class="btn-popup-add" onclick="confirmColorSelection()">
        <i class="bi bi-check-lg"></i> Add Selected Colors
      </button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

  // ── Backend Colors Data ──────────────────────────────────────
  const backendColors = @json(\App\Models\Color::all());
  let popupSelectedIds = new Set();

  // ── Navigation → Category filter ────────────────────────────
  const categories = @json($categories->load('subcategories'));
  const navigations = @json($navigations ?? []);

  document.getElementById('navMenuSelect').addEventListener('change', function () {
    const navId = this.value;
    const catSelect = document.getElementById('categorySelect');
    // Show/hide categories based on nav
    Array.from(catSelect.options).forEach(opt => {
      if (!opt.value) return;
      if (!navId) { opt.style.display = ''; return; }
      opt.style.display = (opt.dataset.nav == navId) ? '' : 'none';
    });
    // Reset category if current doesn't match
    const currentOpt = catSelect.options[catSelect.selectedIndex];
    if (navId && currentOpt && currentOpt.value && currentOpt.dataset.nav != navId) {
      catSelect.value = '';
      document.getElementById('subcategoryWrapper').style.display = 'none';
      document.getElementById('subcategorySelect').innerHTML = '<option value="">-- No Subcategory --</option>';
    }
  });

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

  // ── Shipping toggle ──────────────────────────────────────────
  const st = document.getElementById('shippingToggle');
  const sf = document.getElementById('shippingFields');
  sf.style.display = st.checked ? 'block' : 'none';
  st.addEventListener('change', () => sf.style.display = st.checked ? 'block' : 'none');

  // ── Main Image ───────────────────────────────────────────────
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

  // ── Global Gallery ───────────────────────────────────────────
  let galFiles = [];
  document.getElementById('galInput').addEventListener('change', function () {
    Array.from(this.files).forEach(file => {
      galFiles.push(file);
      const i = galFiles.length - 1;
      const ge = document.getElementById('galEmpty'); if (ge) ge.remove();
      const d = document.createElement('div'); d.className = 'gal-item'; d.dataset.i = i;
      d.innerHTML = `<img src="${URL.createObjectURL(file)}"><button type="button" class="gal-item-remove" onclick="removeGalItem(this)"><i class="bi bi-x"></i></button>`;
      document.getElementById('galGrid').appendChild(d);
    });
    rebuildGal();
  });
  window.removeGalItem = function (btn) {
    const item = btn.closest('.gal-item');
    galFiles[parseInt(item.dataset.i)] = null; item.remove(); rebuildGal();
    const gg = document.getElementById('galGrid');
    if (!gg.querySelector('.gal-item')) gg.innerHTML = '<div class="gal-empty" id="galEmpty">No images added.</div>';
  };
  function rebuildGal() {
    const dt = new DataTransfer();
    galFiles.filter(f => f).forEach(f => dt.items.add(f));
    document.getElementById('galInput').files = dt.files;
  }

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

  // ── Color Popup ──────────────────────────────────────────────
  window.openColorPopup = function () {
    renderColorPopup('');
    document.getElementById('colorPopupOverlay').classList.add('open');
    document.getElementById('colorSearch').value = '';
    document.getElementById('colorSearch').focus();
  };
  window.closeColorPopup = function () {
    document.getElementById('colorPopupOverlay').classList.remove('open');
  };
  // Close on overlay click
  document.getElementById('colorPopupOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeColorPopup();
  });

  window.filterColors = function () {
    renderColorPopup(document.getElementById('colorSearch').value.trim().toLowerCase());
  };

  function renderColorPopup(search) {
    const list = document.getElementById('colorPopupList');
    list.innerHTML = '';
    const filtered = backendColors.filter(c =>
      c.name.toLowerCase().includes(search) || (c.code || '').toLowerCase().includes(search)
    );
    if (!filtered.length) {
      list.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:24px;color:#aaa;font-size:12px">No colors found</div>';
      return;
    }
    filtered.forEach(c => {
      const hex = c.code || '#000000';
      const isSelected = popupSelectedIds.has(c.id);
      // Check if already added
      const alreadyAdded = colors.some(col => col.backendId === c.id);
      const div = document.createElement('div');
      div.className = 'color-popup-item' + (isSelected ? ' selected' : '') + (alreadyAdded ? ' already' : '');
      div.dataset.id = c.id;
      div.style.opacity = alreadyAdded ? '0.4' : '1';
      div.title = alreadyAdded ? 'Already added' : c.name;
      div.innerHTML = `
        <div class="color-popup-swatch" style="background:${hex};${hex==='#ffffff'||hex==='#FFFFFF'?'border:1.5px solid #ddd':''}"></div>
        <div class="color-popup-name">${c.name}</div>
        <div class="color-popup-hex">${hex}</div>
      `;
      if (!alreadyAdded) {
        div.addEventListener('click', () => {
          if (popupSelectedIds.has(c.id)) popupSelectedIds.delete(c.id);
          else popupSelectedIds.add(c.id);
          div.classList.toggle('selected');
          updatePopupCount();
        });
      }
      list.appendChild(div);
    });
    updatePopupCount();
  }

  function updatePopupCount() {
    document.getElementById('popupSelectedCount').textContent = popupSelectedIds.size + ' selected';
  }

  window.confirmColorSelection = function () {
    popupSelectedIds.forEach(id => {
      const c = backendColors.find(bc => bc.id === id);
      if (c && !colors.some(col => col.backendId === c.id)) {
        const cid = colorCounter++;
        colors.push({ cid, name: c.name, hex: c.code || '#000000', backendId: c.id });
        renderColorCard(cid, c.name, c.code || '#000000');
      }
    });
    popupSelectedIds.clear();
    updateColorsJson();
    closeColorPopup();
  };

  // ── Manual Color Toggle ──────────────────────────────────────
  window.toggleManualColor = function () {
    const s = document.getElementById('manualColorSection');
    s.classList.toggle('open');
  };

  window.addColorManual = function () {
    const name = document.getElementById('colorName').value.trim();
    const hex  = document.getElementById('colorHex').value;
    if (!name) { alert('Please enter a color name!'); return; }
    const cid = colorCounter++;
    colors.push({ cid, name, hex });
    renderColorCard(cid, name, hex);
    updateColorsJson();
    document.getElementById('colorName').value = '';
  };

  // ── Colors State ─────────────────────────────────────────────
  let colors = [];
  let colorCounter = 0;
  let colorMainFiles = {};
  let colorGalFiles  = {};

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
        <div>
          <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#888;margin-bottom:6px">Thumbnail</div>
          <div class="color-thumb-box" id="ctb-${cid}" onclick="document.getElementById('cti-${cid}').click()">
            <div class="ctb-placeholder"><i class="bi bi-camera"></i><p>Click to upload</p></div>
            <img id="ctp-${cid}" src="">
          </div>
          <input type="file" id="cti-${cid}" accept="image/*" style="display:none" onchange="handleColorThumb(${cid},this)">
        </div>
        <div>
          <div class="color-gal-box">
            <div class="color-gal-header">
              <span>Gallery</span>
              <button type="button" class="btn-add-cgal" onclick="document.getElementById('cgi-${cid}').click()"><i class="bi bi-plus"></i> Add</button>
            </div>
            <div class="cgal-grid" id="cgal-${cid}"><div class="cgal-empty">No images</div></div>
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
    const grid = document.getElementById(`cgal-${cid}`);
    const empty = grid.querySelector('.cgal-empty'); if (empty) empty.remove();
    Array.from(input.files).forEach(file => {
      colorGalFiles[cid].push(file);
      const gi = colorGalFiles[cid].length - 1;
      const d = document.createElement('div'); d.className = 'cgal-item'; d.dataset.cid = cid; d.dataset.gi = gi;
      d.innerHTML = `<img src="${URL.createObjectURL(file)}"><button type="button" class="cgal-remove" onclick="removeCGal(this)"><i class="bi bi-x"></i></button>`;
      grid.appendChild(d);
    });
    input.value = '';
  };
  window.removeCGal = function (btn) {
    const item = btn.closest('.cgal-item');
    const cid = parseInt(item.dataset.cid); const gi = parseInt(item.dataset.gi);
    if (colorGalFiles[cid]) colorGalFiles[cid][gi] = null;
    item.remove();
    const grid = document.getElementById(`cgal-${cid}`);
    if (!grid.querySelector('.cgal-item')) grid.innerHTML = '<div class="cgal-empty">No images</div>';
  };
  window.removeColor = function (cid) {
    colors = colors.filter(c => c.cid !== cid);
    const card = document.getElementById(`cc-${cid}`); if (card) card.remove();
    delete colorMainFiles[cid]; delete colorGalFiles[cid];
    updateColorsJson();
  };
  function updateColorsJson() {
    document.getElementById('colorsJson').value = JSON.stringify(colors.map(c => ({ name: c.name, hex: c.hex })));
  }

  // ── Form Submit ──────────────────────────────────────────────
  document.getElementById('productForm').addEventListener('submit', function () {
    const finalColors = colors.map(c => ({ name: c.name, hex: c.hex }));
    document.getElementById('colorsJson').value = JSON.stringify(finalColors);
    colors.forEach((c, newIdx) => {
      const origCid = c.cid;
      if (colorMainFiles[origCid]) {
        const inp = document.createElement('input'); inp.type = 'file'; inp.name = `color_image_${newIdx}`; inp.style.display = 'none';
        const dt = new DataTransfer(); dt.items.add(colorMainFiles[origCid]); inp.files = dt.files; this.appendChild(inp);
      }
      const gfs = (colorGalFiles[origCid] || []).filter(f => f);
      if (gfs.length) {
        const dt = new DataTransfer(); gfs.forEach(f => dt.items.add(f));
        const inp = document.createElement('input'); inp.type = 'file'; inp.name = `color_gallery_${newIdx}[]`; inp.multiple = true; inp.style.display = 'none';
        inp.files = dt.files; this.appendChild(inp);
      }
    });
  });

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

}); // end DOMContentLoaded
</script>
@endsection
