@extends('layouts.dashboard')

@section('content')
<style>
/* ═══════════════════════════════════════════════
   PRODUCT FORM — GLOBAL STYLES (same as create)
═══════════════════════════════════════════════ */
.pf-wrap { max-width: 960px; margin: 0 auto; padding-bottom: 60px; }
.pf-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; flex-wrap: wrap; gap: 12px; }
.pf-header h1 { font-size: 22px; font-weight: 700; margin: 0; }
.pf-card { background: #fff; border: 1px solid #e8e8e8; border-radius: 14px; padding: 28px 32px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
.pf-card-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #666; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; gap: 8px; }
.pf-card-title i { font-size: 15px; color: #2563eb; }
.pf-row { display: grid; gap: 18px; }
.pf-row.cols-2 { grid-template-columns: 1fr 1fr; }
@media(max-width:640px){ .pf-row.cols-2{ grid-template-columns:1fr; } }
.pf-group { display: flex; flex-direction: column; gap: 6px; }
.pf-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: #444; }
.pf-label small { text-transform: none; font-weight: 500; color: #aaa; letter-spacing: 0; font-size: 11px; }
.pf-input, .pf-select, .pf-textarea { width: 100%; padding: 10px 13px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 14px; font-family: inherit; background: #fff; transition: border-color .2s; }
.pf-input:focus, .pf-select:focus, .pf-textarea:focus { outline: none; border-color: #2563eb; }
.pf-textarea { resize: vertical; min-height: 90px; }
.pf-hint { font-size: 11px; color: #999; }
.pf-toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f5f5f5; }
.pf-toggle-row:last-child { border-bottom: none; }
.pf-toggle-info strong { font-size: 13px; font-weight: 600; display: block; margin-bottom: 2px; }
.pf-toggle-info span { font-size: 11px; color: #999; }
.pf-toggle { position: relative; width: 44px; height: 24px; flex-shrink: 0; }
.pf-toggle input { opacity: 0; width: 0; height: 0; }
.pf-toggle-slider { position: absolute; inset: 0; background: #e0e0e0; border-radius: 24px; cursor: pointer; transition: .3s; }
.pf-toggle-slider:before { content: ''; position: absolute; width: 18px; height: 18px; left: 3px; top: 3px; background: #fff; border-radius: 50%; transition: .3s; }
.pf-toggle input:checked + .pf-toggle-slider { background: #2563eb; }
.pf-toggle input:checked + .pf-toggle-slider:before { transform: translateX(20px); }
/* Sizes */
.sizes-trigger { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border: 1.5px dashed #e0e0e0; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: #2563eb; background: #f0f6ff; width: fit-content; transition: .2s; }
.sizes-dropdown { background: #fff; border: 1.5px solid #e0e0e0; border-radius: 10px; padding: 16px; margin-top: 10px; display: none; }
.sizes-dropdown.open { display: block; }
.sizes-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 8px; }
.size-chip input[type=checkbox] { display: none; }
.size-chip label { display: flex; align-items: center; justify-content: center; min-width: 52px; height: 38px; padding: 0 10px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: .15s; background: #fff; user-select: none; }
.size-chip input:checked + label { background: #000; color: #fff; border-color: #000; }
.sizes-selected-tags { display: flex; flex-wrap: wrap; gap: 6px; min-height: 30px; margin-top: 10px; }
.size-tag { display: inline-flex; align-items: center; gap: 4px; background: #000; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
.size-tag-cross { cursor: pointer; font-size: 14px; line-height: 1; opacity: .7; }
/* Color add row */
.color-add-row { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
.color-hex-input { width: 48px; height: 40px; border: 1.5px solid #e0e0e0; border-radius: 8px; cursor: pointer; padding: 2px; }
.color-name-input { flex: 1; min-width: 140px; padding: 9px 13px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 13px; font-family: inherit; }
.color-name-input:focus { outline: none; border-color: #2563eb; }
.btn-add-color { height: 40px; padding: 0 16px; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit; white-space: nowrap; }
/* Color card */
.color-cards-list { display: flex; flex-direction: column; gap: 14px; }
.color-card { border: 1.5px solid #e0e0e0; border-radius: 12px; padding: 16px; background: #fafafa; position: relative; }
.color-card-header { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
.color-dot { width: 26px; height: 26px; border-radius: 50%; border: 2px solid rgba(0,0,0,.12); flex-shrink: 0; }
.color-card-name-lbl { font-size: 14px; font-weight: 700; }
.color-hex-lbl { font-size: 11px; color: #aaa; }
.color-card-remove { position: absolute; top: 12px; right: 12px; width: 26px; height: 26px; background: #fee2e2; color: #e53e3e; border: none; border-radius: 50%; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; line-height: 1; }
.color-card-body { display: grid; grid-template-columns: 160px 1fr; gap: 16px; }
@media(max-width:600px){ .color-card-body { grid-template-columns: 1fr; } }
/* Color thumbnail box */
.color-thumb-box { border: 2px dashed #e0e0e0; border-radius: 10px; padding: 10px; text-align: center; cursor: pointer; background: #fff; min-height: 130px; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: .2s; gap: 6px; }
.color-thumb-box:hover { border-color: #2563eb; background: #f0f6ff; }
.color-thumb-box img { max-width: 100%; max-height: 108px; object-fit: contain; border-radius: 6px; display: none; }
.color-thumb-box.has-img img { display: block; }
.color-thumb-box.has-img .ctb-placeholder { display: none; }
.ctb-placeholder i { font-size: 22px; color: #ccc; display: block; }
.ctb-placeholder p { font-size: 11px; color: #aaa; margin: 0; }
/* Color gallery */
.color-gal-box { border: 1.5px solid #e8e8e8; border-radius: 10px; padding: 12px; background: #fff; height: 100%; }
.color-gal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; font-size: 12px; font-weight: 700; color: #666; }
.btn-add-cgal { padding: 5px 12px; background: #f0f6ff; color: #2563eb; border: 1px solid #bfdbfe; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer; font-family: inherit; }
.cgal-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; }
.cgal-item { position: relative; aspect-ratio: 1; border: 1.5px solid #e0e0e0; border-radius: 6px; overflow: hidden; background: #f8f9fa; }
.cgal-item img { width: 100%; height: 100%; object-fit: contain; padding: 3px; }
.cgal-remove { position: absolute; top: 2px; right: 2px; width: 18px; height: 18px; background: #e53e3e; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 10px; border: none; }
.cgal-empty { font-size: 11px; color: #ccc; text-align: center; padding: 12px; grid-column: 1/-1; }
/* Main image */
.main-img-box { border: 2px dashed #e0e0e0; border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; background: #fafafa; min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: .2s; }
.main-img-box:hover { border-color: #2563eb; background: #f0f6ff; }
.main-img-box img { max-width: 100%; max-height: 180px; object-fit: contain; border-radius: 8px; display: none; }
.main-img-box.has-img img { display: block; }
.main-img-box.has-img .upload-placeholder { display: none; }
.upload-placeholder i { font-size: 32px; color: #ccc; display: block; margin-bottom: 8px; }
.upload-placeholder p { font-size: 12px; color: #aaa; margin: 0; }
.img-actions { display: flex; gap: 8px; margin-top: 10px; }
.btn-replace { flex: 1; padding: 7px 0; background: #f0f0f0; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-remove-img { flex: 1; padding: 7px 0; background: #fff0f0; color: #e53e3e; border: 1.5px solid #fca5a5; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit; }
/* Global gallery */
.gal-box { border: 1.5px solid #e8e8e8; border-radius: 12px; padding: 18px; }
.gal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; font-size: 13px; font-weight: 600; }
.btn-add-gal { padding: 7px 16px; background: #2563eb; color: #fff; border: none; border-radius: 7px; font-size: 12px; font-weight: 700; cursor: pointer; font-family: inherit; }
.gal-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.gal-item { position: relative; border: 1.5px solid #e0e0e0; border-radius: 8px; overflow: hidden; aspect-ratio: 1; background: #f8f9fa; }
.gal-item img { width: 100%; height: 100%; object-fit: contain; padding: 6px; }
.gal-item-remove { position: absolute; top: 4px; right: 4px; width: 22px; height: 22px; background: #e53e3e; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px; border: none; }
.gal-item-num { position: absolute; bottom: 4px; left: 6px; font-size: 10px; font-weight: 700; color: #888; }
.gal-empty { text-align: center; padding: 24px; color: #ccc; font-size: 13px; grid-column: 1/-1; }
/* Size chart */
.sc-box { border: 2px dashed #e0e0e0; border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; background: #fafafa; transition: .2s; }
.sc-box:hover { border-color: #2563eb; background: #f0f6ff; }
.sc-box img { max-width: 100%; max-height: 200px; object-fit: contain; border-radius: 8px; display: none; }
.sc-box.has-img img { display: block; }
.sc-box.has-img .upload-placeholder { display: none; }
/* Actions */
.pf-actions { display: flex; gap: 12px; padding-top: 8px; }
.btn-save { height: 48px; padding: 0 32px; background: #000; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; display: flex; align-items: center; gap: 8px; }
.btn-save:hover { background: #222; }
.btn-back { height: 48px; padding: 0 24px; background: #fff; color: #000; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: .2s; }
.btn-back:hover { border-color: #000; }
.img-section { display: grid; grid-template-columns: 260px 1fr; gap: 24px; }
@media(max-width:700px){ .img-section { grid-template-columns: 1fr; } }
/* Existing color image badge */
.existing-thumb-label { font-size: 10px; color: #2563eb; font-weight: 700; margin-top: 4px; text-align: center; }
.existing-gal-badge { position: absolute; bottom: 2px; left: 3px; font-size: 9px; background: #2563eb; color: #fff; border-radius: 3px; padding: 1px 4px; font-weight: 700; }
</style>

<div class="pf-wrap">
  <div class="pf-header">
    <h1><i class="bi bi-pencil-square me-2"></i>Edit Product</h1>
    <a href="{{ route('products.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Back</a>
  </div>

  @if($errors->any())
    <div class="alert alert-danger mb-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
  @endif
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
      {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <form id="productForm" action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- 1. Basic Info --}}
    <div class="pf-card">
      <div class="pf-card-title"><i class="bi bi-info-circle"></i> Basic Information</div>
      <div class="pf-row" style="margin-bottom:18px">
        <div class="pf-group">
          <label class="pf-label">Product Name <span style="color:#e53e3e">*</span></label>
          <input type="text" name="name" class="pf-input" value="{{ old('name', $product->name) }}" required>
        </div>
      </div>
      <div class="pf-row cols-2">
        <div class="pf-group">
          <label class="pf-label">Price ($) <span style="color:#e53e3e">*</span></label>
          <input type="number" step="0.01" min="0" name="price" class="pf-input" value="{{ old('price', $product->price) }}" required>
        </div>
        <div class="pf-group">
          <label class="pf-label">Description <small>(optional)</small></label>
          <textarea name="description" class="pf-textarea">{{ old('description', $product->description) }}</textarea>
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
            @foreach($categories as $category)
              <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
          <input type="checkbox" name="in_stock" value="1" {{ old('in_stock', $product->in_stock) ? 'checked' : '' }}>
          <span class="pf-toggle-slider"></span>
        </label>
      </div>
      <div style="margin-top:14px">
        <div class="pf-group" style="max-width:220px">
          <label class="pf-label">Stock Quantity <small>(optional)</small></label>
          <input type="number" min="0" name="stock_quantity" class="pf-input" value="{{ old('stock_quantity', $product->stock_quantity) }}" placeholder="Leave empty = unlimited">
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
          <input type="checkbox" name="shipping_enabled" id="shippingToggle" value="1" {{ old('shipping_enabled', $product->shipping_enabled) ? 'checked' : '' }}>
          <span class="pf-toggle-slider"></span>
        </label>
      </div>
      <div id="shippingFields" style="margin-top:16px">
        <div class="pf-row cols-2">
          <div class="pf-group">
            <label class="pf-label">Shipping Cost ($)</label>
            <input type="number" step="0.01" min="0" name="shipping_cost" class="pf-input" value="{{ old('shipping_cost', $product->shipping_cost ?? 0) }}" placeholder="0.00">
            <span class="pf-hint">0 = Free shipping</span>
          </div>
          <div class="pf-group">
            <label class="pf-label">Free Above ($) <small>(optional)</small></label>
            <input type="number" step="0.01" min="0" name="free_shipping_above" class="pf-input" value="{{ old('free_shipping_above', $product->free_shipping_above) }}" placeholder="e.g. 200">
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
          @php $productSizes = $product->sizes ?? []; @endphp
          @foreach(['All','YXS','YS','YM','YL','YXL','S','M','L','XL','2XL'] as $sz)
          <div class="size-chip">
            <input type="checkbox" id="sz_{{$sz}}" value="{{$sz}}" class="size-checkbox" {{ ($sz !== 'All' && in_array($sz, $productSizes)) ? 'checked' : '' }}>
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
        <br><span style="color:#2563eb;font-weight:600">🔵 Existing images show ho rahi hain — replace karne ke liye nayi upload karo.</span>
      </p>
      <div class="color-add-row">
        <input type="color" id="colorHex" class="color-hex-input" value="#000000">
        <input type="text" id="colorName" class="color-name-input" placeholder="Color name (e.g. Black, Navy Blue)">
        <button type="button" class="btn-add-color" onclick="addColor()"><i class="bi bi-plus"></i> Add Color</button>
      </div>
      <div class="color-cards-list" id="colorCardsList"></div>
      {{-- Stores final colors JSON (name+hex only) --}}
      <input type="hidden" name="colors_json" id="colorsJson" value="[]">
      {{-- Tracks which existing color images to keep/remove --}}
      <input type="hidden" name="color_images_meta" id="colorImagesMeta" value="[]">
    </div>

    {{-- 7. Main Image + Global Gallery --}}
    <div class="pf-card">
      <div class="pf-card-title"><i class="bi bi-images"></i> Product Images</div>
      <p style="font-size:12px;color:#888;margin-bottom:16px">Main image = default thumbnail. Global gallery = sab par dikhe.</p>
      <div class="img-section">
        <div>
          <div class="pf-label" style="margin-bottom:8px">Main Image (Default)</div>
          <div class="main-img-box {{ $product->image ? 'has-img' : '' }}" id="mainImgBox" onclick="document.getElementById('mainImgInput').click()">
            <div class="upload-placeholder"><i class="bi bi-cloud-upload"></i><p>Click to upload</p></div>
            <img id="mainImgPreview" src="{{ $product->image ? asset('storage/'.$product->image) : '' }}" alt="">
          </div>
          <div class="img-actions" id="mainImgActions" style="{{ $product->image ? 'display:flex' : 'display:none' }}">
            <button type="button" class="btn-replace" onclick="document.getElementById('mainImgInput').click()"><i class="bi bi-arrow-repeat"></i> Replace</button>
            <button type="button" class="btn-remove-img" onclick="removeMainImg()"><i class="bi bi-trash"></i> Remove</button>
          </div>
          <input type="hidden" name="remove_main_image" id="removeMainImageFlag" value="0">
          <input type="file" name="image" id="mainImgInput" accept="image/*" style="display:none">
        </div>
        <div>
          <div class="gal-box">
            <div class="gal-header">
              <span>Global Gallery</span>
              <button type="button" class="btn-add-gal" onclick="document.getElementById('galInput').click()"><i class="bi bi-plus"></i> Add Images</button>
            </div>
            <div class="gal-grid" id="galGrid">
              @php $gallery = $product->gallery_images ?? []; @endphp
              @if(count($gallery))
                @foreach($gallery as $i => $img)
                  <div class="gal-item existing-gal-item" data-path="{{ $img }}">
                    <img src="{{ asset('storage/'.$img) }}" alt="">
                    <button type="button" class="gal-item-remove" onclick="removeExistingGal(this)"><i class="bi bi-x"></i></button>
                    <span class="gal-item-num">#{{ $i + 1 }}</span>
                    <input type="hidden" name="keep_gallery[]" value="{{ $img }}">
                  </div>
                @endforeach
              @else
                <div class="gal-empty" id="galEmpty">No images added.</div>
              @endif
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
      <div class="sc-box {{ $product->size_chart_image ? 'has-img' : '' }}" id="scBox" onclick="document.getElementById('scInput').click()">
        <div class="upload-placeholder"><i class="bi bi-cloud-upload" style="font-size:28px;color:#ccc;display:block;margin-bottom:6px"></i><p style="font-size:12px;color:#aaa;margin:0">Click to upload size chart</p></div>
        <img id="scPreview" src="{{ $product->size_chart_image ? asset('storage/'.$product->size_chart_image) : '' }}" alt="">
      </div>
      <div id="scActions" style="{{ $product->size_chart_image ? 'display:block' : 'display:none' }};margin-top:8px">
        <button type="button" class="btn-replace" style="padding:6px 14px" onclick="document.getElementById('scInput').click()"><i class="bi bi-arrow-repeat"></i> Replace</button>
        <button type="button" class="btn-remove-img" style="padding:6px 14px;margin-left:8px" onclick="removeSizeChart()"><i class="bi bi-trash"></i> Remove</button>
      </div>
      <input type="hidden" name="remove_size_chart" id="removeSizeChartFlag" value="0">
      <input type="file" name="size_chart_image" id="scInput" accept="image/*" style="display:none">
    </div>

    <div class="pf-actions">
      <button type="submit" class="btn-save"><i class="bi bi-check-lg"></i> Update Product</button>
      <a href="{{ route('products.index') }}" class="btn-back">Cancel</a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

  // ── Category / Subcategory ──────────────────────────────────
  const categories     = @json($categories->load('subcategories'));
  const catSelect      = document.getElementById('categorySelect');
  const subSelect      = document.getElementById('subcategorySelect');
  const subWrapper     = document.getElementById('subcategoryWrapper');
  const selectedCatId  = parseInt(catSelect.value) || 0;
  const selectedSubId  = {{ old('subcategory_id', $product->subcategory_id ?? 'null') }};

  function loadSubcategories(catId, sub) {
    subSelect.innerHTML = '<option value="">-- No Subcategory --</option>';
    const cat = categories.find(c => c.id === parseInt(catId));
    if (cat && cat.subcategories && cat.subcategories.length) {
      cat.subcategories.forEach(s => {
        const o = document.createElement('option');
        o.value = s.id; o.textContent = s.name;
        if (sub && sub == s.id) o.selected = true;
        subSelect.appendChild(o);
      });
      subWrapper.style.display = 'block';
    } else { subWrapper.style.display = 'none'; }
  }
  if (selectedCatId) loadSubcategories(selectedCatId, selectedSubId);
  catSelect.addEventListener('change', function () { loadSubcategories(parseInt(this.value)); });

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
      document.getElementById('removeMainImageFlag').value = '0';
    }
  });
  window.removeMainImg = function () {
    document.getElementById('mainImgInput').value = '';
    document.getElementById('mainImgPreview').src = '';
    document.getElementById('mainImgBox').classList.remove('has-img');
    document.getElementById('mainImgActions').style.display = 'none';
    document.getElementById('removeMainImageFlag').value = '1';
  };

  // ── Global Gallery (existing + new) ────────────────────────
  let newGalFiles = [];

  window.removeExistingGal = function (btn) {
    const item = btn.closest('.gal-item');
    item.remove();
    renumberGal();
    if (!document.getElementById('galGrid').querySelector('.gal-item'))
      document.getElementById('galGrid').innerHTML = '<div class="gal-empty" id="galEmpty">No images added.</div>';
  };

  document.getElementById('galInput').addEventListener('change', function () {
    Array.from(this.files).forEach(file => {
      newGalFiles.push(file);
      const i = newGalFiles.length - 1;
      const ge = document.getElementById('galEmpty'); if (ge) ge.remove();
      const d = document.createElement('div'); d.className = 'gal-item'; d.dataset.ni = i;
      d.innerHTML = `<img src="${URL.createObjectURL(file)}"><button type="button" class="gal-item-remove" onclick="removeNewGal(this)"><i class="bi bi-x"></i></button><span class="gal-item-num">#?</span>`;
      document.getElementById('galGrid').appendChild(d);
      renumberGal();
    });
    rebuildGal(); this.value = '';
  });

  window.removeNewGal = function (btn) {
    const item = btn.closest('.gal-item');
    newGalFiles[parseInt(item.dataset.ni)] = null;
    item.remove(); renumberGal(); rebuildGal();
    if (!document.getElementById('galGrid').querySelector('.gal-item'))
      document.getElementById('galGrid').innerHTML = '<div class="gal-empty" id="galEmpty">No images added.</div>';
  };

  function renumberGal() {
    document.getElementById('galGrid').querySelectorAll('.gal-item').forEach((el, i) => {
      el.querySelector('.gal-item-num').textContent = '#' + (i + 1);
    });
  }

  function rebuildGal() {
    const dt = new DataTransfer();
    newGalFiles.filter(f => f).forEach(f => dt.items.add(f));
    document.getElementById('galInput').files = dt.files;
  }

  // ── Sizes ───────────────────────────────────────────────────
  let selectedSizes = @json($product->sizes ?? []);

  // Init checkboxes
  selectedSizes.forEach(sz => {
    const cb = document.getElementById('sz_' + sz); if (cb) cb.checked = true;
  });
  renderSizeTags();

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

  // ── Colors + Per-color Images ───────────────────────────────
  // Load existing colors from product
  // existingColorData: [{name, hex, image: 'path/or/null', gallery: ['path', ...]}]
  const existingColorData = @json($product->colors ?? []);

  // colors array: [{cid, name, hex}]
  let colors         = [];
  let colorCounter   = 0;
  let colorMainFiles = {};  // { cid: File }   — new uploads only
  let colorGalFiles  = {};  // { cid: [File|null, ...] } — new uploads only

  // For existing (saved) images: track removes
  // existingThumb[cid] = 'storage/path' | null (null = removed)
  // existingGals[cid]  = ['path', ...] (paths still kept)
  let existingThumb = {};
  let existingGals  = {};

  // Init from product data
  existingColorData.forEach(c => {
    const cid = colorCounter++;
    colors.push({ cid, name: c.name, hex: c.hex });
    existingThumb[cid] = c.image   || null;
    existingGals[cid]  = Array.isArray(c.gallery) ? [...c.gallery] : [];
    renderColorCard(cid, c.name, c.hex);
  });
  updateColorsJson();

  window.addColor = function () {
    const name = document.getElementById('colorName').value.trim();
    const hex  = document.getElementById('colorHex').value;
    if (!name) { alert('Color ka naam daalo!'); return; }
    const cid = colorCounter++;
    colors.push({ cid, name, hex });
    existingThumb[cid] = null;
    existingGals[cid]  = [];
    renderColorCard(cid, name, hex);
    updateColorsJson();
    document.getElementById('colorName').value = '';
  };

  function renderColorCard(cid, name, hex) {
    const list = document.getElementById('colorCardsList');
    const card = document.createElement('div');
    card.className = 'color-card'; card.id = `cc-${cid}`;

    // Decide thumb preview: existing image path OR empty
    const hasExistingThumb = existingThumb[cid];
    const thumbHasImg      = hasExistingThumb ? 'has-img' : '';
    const thumbSrc         = hasExistingThumb ? `/storage/${hasExistingThumb}` : '';

    card.innerHTML = `
      <button type="button" class="color-card-remove" onclick="removeColor(${cid})" title="Remove">×</button>
      <div class="color-card-header">
        <span class="color-dot" style="background:${hex};${hex==='#ffffff'?'border-color:#ddd':''}"></span>
        <span class="color-card-name-lbl">${name}</span>
        <span class="color-hex-lbl">${hex}</span>
      </div>
      <div class="color-card-body">
        <!-- Thumbnail -->
        <div>
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#888;margin-bottom:6px">Thumbnail</div>
          <div class="color-thumb-box ${thumbHasImg}" id="ctb-${cid}" onclick="document.getElementById('cti-${cid}').click()">
            <div class="ctb-placeholder"><i class="bi bi-camera"></i><p>Click to upload</p></div>
            <img id="ctp-${cid}" src="${thumbSrc}" alt="">
          </div>
          ${hasExistingThumb ? `
          <div style="display:flex;gap:6px;margin-top:6px">
            <button type="button" style="flex:1;padding:4px 0;background:#f0f0f0;border:none;border-radius:5px;font-size:11px;font-weight:600;cursor:pointer" onclick="document.getElementById('cti-${cid}').click()"><i class="bi bi-arrow-repeat"></i> Replace</button>
            <button type="button" style="flex:1;padding:4px 0;background:#fff0f0;color:#e53e3e;border:1.5px solid #fca5a5;border-radius:5px;font-size:11px;font-weight:600;cursor:pointer" id="ctbtn-rm-${cid}" onclick="removeExistingColorThumb(${cid})"><i class="bi bi-trash"></i> Remove</button>
          </div>` : ''}
          <input type="file" id="cti-${cid}" accept="image/*" style="display:none" onchange="handleColorThumb(${cid},this)">
        </div>
        <!-- Gallery -->
        <div>
          <div class="color-gal-box">
            <div class="color-gal-header">
              <span>Gallery</span>
              <button type="button" class="btn-add-cgal" onclick="document.getElementById('cgi-${cid}').click()"><i class="bi bi-plus"></i> Add</button>
            </div>
            <div class="cgal-grid" id="cgal-${cid}"></div>
          </div>
          <input type="file" id="cgi-${cid}" accept="image/*" multiple style="display:none" onchange="handleColorGal(${cid},this)">
        </div>
      </div>`;
    list.appendChild(card);

    // Render existing gallery items
    renderExistingColorGal(cid);
  }

  // Render existing gallery images inside color card
  function renderExistingColorGal(cid) {
    const grid = document.getElementById(`cgal-${cid}`);
    grid.innerHTML = '';
    const paths = existingGals[cid] || [];
    if (paths.length === 0 && (!colorGalFiles[cid] || colorGalFiles[cid].filter(f=>f).length === 0)) {
      grid.innerHTML = '<div class="cgal-empty">No images added</div>';
      return;
    }
    paths.forEach((path, gi) => {
      const d = document.createElement('div');
      d.className = 'cgal-item';
      d.dataset.cid = cid; d.dataset.existingGi = gi;
      d.innerHTML = `
        <img src="/storage/${path}" alt="">
        <button type="button" class="cgal-remove" onclick="removeExistingColorGal(this)"><i class="bi bi-x"></i></button>
        <span class="existing-gal-badge">saved</span>`;
      grid.appendChild(d);
    });
    // Also render any new files already added
    (colorGalFiles[cid] || []).forEach((file, fi) => {
      if (!file) return;
      const d = document.createElement('div');
      d.className = 'cgal-item'; d.dataset.cid = cid; d.dataset.newGi = fi;
      d.innerHTML = `<img src="${URL.createObjectURL(file)}" alt=""><button type="button" class="cgal-remove" onclick="removeCGal(this)"><i class="bi bi-x"></i></button>`;
      grid.appendChild(d);
    });
  }

  // Remove existing (saved) color thumbnail
  window.removeExistingColorThumb = function (cid) {
    existingThumb[cid] = null;
    document.getElementById(`ctp-${cid}`).src = '';
    document.getElementById(`ctb-${cid}`).classList.remove('has-img');
    const rmBtn = document.getElementById(`ctbtn-rm-${cid}`);
    if (rmBtn) rmBtn.closest('div').style.display = 'none';
    updateColorsJson();
  };

  // Remove existing gallery image from color card
  window.removeExistingColorGal = function (btn) {
    const item = btn.closest('.cgal-item');
    const cid  = parseInt(item.dataset.cid);
    const gi   = parseInt(item.dataset.existingGi);
    existingGals[cid].splice(gi, 1);
    renderExistingColorGal(cid);
    updateColorsJson();
  };

  // New thumb upload
  window.handleColorThumb = function (cid, input) {
    if (!input.files || !input.files[0]) return;
    colorMainFiles[cid] = input.files[0];
    document.getElementById(`ctp-${cid}`).src = URL.createObjectURL(input.files[0]);
    document.getElementById(`ctb-${cid}`).classList.add('has-img');
  };

  // New gallery upload
  window.handleColorGal = function (cid, input) {
    if (!input.files || !input.files.length) return;
    if (!colorGalFiles[cid]) colorGalFiles[cid] = [];
    Array.from(input.files).forEach(file => { colorGalFiles[cid].push(file); });
    renderExistingColorGal(cid);
    input.value = '';
  };

  // Remove new gallery item
  window.removeCGal = function (btn) {
    const item = btn.closest('.cgal-item');
    const cid  = parseInt(item.dataset.cid);
    const fi   = parseInt(item.dataset.newGi);
    if (colorGalFiles[cid]) colorGalFiles[cid][fi] = null;
    item.remove();
    const grid = document.getElementById(`cgal-${cid}`);
    if (!grid.querySelector('.cgal-item')) grid.innerHTML = '<div class="cgal-empty">No images added</div>';
  };

  window.removeColor = function (cid) {
    colors = colors.filter(c => c.cid !== cid);
    const card = document.getElementById(`cc-${cid}`); if (card) card.remove();
    delete colorMainFiles[cid]; delete colorGalFiles[cid];
    delete existingThumb[cid]; delete existingGals[cid];
    updateColorsJson();
  };

  function updateColorsJson () {
    const clean = colors.map(c => ({ name: c.name, hex: c.hex }));
    document.getElementById('colorsJson').value = JSON.stringify(clean);

    // Build meta: existing thumb + existing gallery paths to keep per color (by new sequential index)
    const meta = colors.map(c => ({
      keep_thumb:   existingThumb[c.cid] || null,
      keep_gallery: existingGals[c.cid]  || []
    }));
    document.getElementById('colorImagesMeta').value = JSON.stringify(meta);
  }

  // ── On Submit: inject new file inputs for color images ──────
  // Controller gets: color_image_0 (new upload), color_gallery_0[] (new uploads)
  // + colors_json + color_images_meta (for existing keep/remove logic)
  document.getElementById('productForm').addEventListener('submit', function () {
    updateColorsJson();
    colors.forEach((c, newIdx) => {
      const origCid = c.cid;
      // New thumbnail
      if (colorMainFiles[origCid]) {
        const inp = document.createElement('input');
        inp.type = 'file'; inp.name = `color_image_${newIdx}`; inp.style.display = 'none';
        const dt = new DataTransfer(); dt.items.add(colorMainFiles[origCid]); inp.files = dt.files;
        this.appendChild(inp);
      }
      // New gallery
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
      document.getElementById('removeSizeChartFlag').value = '0';
    }
  });
  window.removeSizeChart = function () {
    document.getElementById('scInput').value = '';
    document.getElementById('scPreview').src = '';
    document.getElementById('scBox').classList.remove('has-img');
    document.getElementById('scActions').style.display = 'none';
    document.getElementById('removeSizeChartFlag').value = '1';
  };

});
</script>
@endsection
