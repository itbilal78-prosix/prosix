<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // ════════════════════════════════════════
    // INDEX
    // ════════════════════════════════════════
    public function index(Request $request)
    {
        $query = Product::with(['category', 'subcategory']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products    = $query->latest()->paginate(20);
        $allProducts = Product::with(['category', 'subcategory'])->get();
        $categories  = Category::whereNull('parent_id')
            ->with('subcategories')
            ->orderBy('position')
            ->get();

        return view('products.index', compact('products', 'allProducts', 'categories'));
    }

    // ════════════════════════════════════════
    // CREATE
    // ════════════════════════════════════════
    public function create()
    {
        $categories = Category::with('subcategories')
            ->whereNull('parent_id')
            ->where('status', 1)
            ->get();

        return view('products.create', compact('categories'));
    }

    // ════════════════════════════════════════
    // EDIT
    // ════════════════════════════════════════
    public function edit(Product $product)
    {
        $categories = Category::with('subcategories')
            ->whereNull('parent_id')
            ->where('status', 1)
            ->get();

        return view('products.edit', compact('product', 'categories'));
    }

    // ─────────────────────────────────────────────────────────────
    //  STORE
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'description'         => 'nullable|string',
            'price'               => 'required|numeric|min:0',
            'category_id'         => 'nullable|exists:categories,id',
            'subcategory_id'      => 'nullable|exists:categories,id',
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'shipping_cost'       => 'nullable|numeric|min:0',
            'free_shipping_above' => 'nullable|numeric|min:0',
            'stock_quantity'      => 'nullable|integer|min:0',
            'sizes'               => 'nullable|array',
            'sizes.*'             => 'string',
            'gallery_images.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'size_chart_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        // ── Main image ──────────────────────────────────────────
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // ── Colors JSON ─────────────────────────────────────────
        $colorsData = [];
        $rawJson    = $request->input('colors_json', '[]');
        try {
            $decoded    = json_decode($rawJson, true);
            $colorsData = is_array($decoded) ? $decoded : [];
        } catch (\Exception $e) {
            $colorsData = [];
        }

        foreach ($colorsData as $idx => &$color) {
            $fileKey = "color_image_{$idx}";
            if ($request->hasFile($fileKey)) {
                $color['image'] = $request->file($fileKey)->store('products/colors', 'public');
            } else {
                $color['image'] = null;
            }

            $galKey  = "color_gallery_{$idx}";
            $galList = [];
            if ($request->hasFile($galKey)) {
                foreach ($request->file($galKey) as $gf) {
                    $galList[] = $gf->store('products/colors', 'public');
                }
            }
            $color['gallery'] = $galList;
        }
        unset($color);

        // ── Global gallery images ───────────────────────────────
        $galleryPaths = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $file->store('products/gallery', 'public');
            }
        }

        // ── Size chart ──────────────────────────────────────────
        $sizeChartPath = null;
        if ($request->hasFile('size_chart_image')) {
            $sizeChartPath = $request->file('size_chart_image')->store('products/sizechart', 'public');
        }

        $inStock         = $request->input('in_stock') == '1';
        $shippingEnabled = $request->input('shipping_enabled') == '1';
        $sizes           = $request->input('sizes', []);
        if (!is_array($sizes)) $sizes = [];

        Product::create([
            'name'                => $request->input('name'),
            'description'         => $request->input('description'),
            'price'               => $request->input('price'),
            'category_id'         => $request->input('category_id') ?: null,
            'subcategory_id'      => $request->input('subcategory_id') ?: null,
            'image'               => $imagePath,
            'show_in_category'    => $request->input('category_id') ? true : false,
            'shipping_enabled'    => $shippingEnabled,
            'shipping_cost'       => $request->input('shipping_cost') ?? 0,
            'free_shipping_above' => $request->input('free_shipping_above') ?: null,
            'in_stock'            => $inStock ? 1 : 0,
            'stock_quantity'      => $request->input('stock_quantity') ?: null,
            'sizes'               => $sizes,
            'colors'              => $colorsData,
            'gallery_images'      => $galleryPaths,
            'size_chart_image'    => $sizeChartPath,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product saved successfully!');
    }

    // ─────────────────────────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'description'         => 'nullable|string',
            'price'               => 'required|numeric|min:0',
            'category_id'         => 'nullable|exists:categories,id',
            'subcategory_id'      => 'nullable|exists:categories,id',
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'shipping_cost'       => 'nullable|numeric|min:0',
            'free_shipping_above' => 'nullable|numeric|min:0',
            'stock_quantity'      => 'nullable|integer|min:0',
            'sizes'               => 'nullable|array',
            'gallery_images.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'size_chart_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        // ── Main image ──────────────────────────────────────────
        $imagePath = $product->image;
        if ($request->input('remove_main_image') == '1') {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $imagePath = null;
        } elseif ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // ── Colors ──────────────────────────────────────────────
        // colors_json   = [{ name, hex }, ...]          — new color list (name+hex only)
        // color_images_meta = [{ keep_thumb, keep_gallery:[] }, ...] — per new-index what to keep
        // color_image_N     = new thumbnail file for color index N
        // color_gallery_N[] = new gallery files for color index N

        $colorsData = [];
        try {
            $decoded    = json_decode($request->input('colors_json', '[]'), true);
            $colorsData = is_array($decoded) ? $decoded : [];
        } catch (\Exception $e) {
            $colorsData = $product->colors ?? [];
        }

        // Parse meta sent by edit form
        $colorsMeta = [];
        try {
            $metaRaw    = $request->input('color_images_meta', '[]');
            $colorsMeta = json_decode($metaRaw, true);
            if (!is_array($colorsMeta)) $colorsMeta = [];
        } catch (\Exception $e) {
            $colorsMeta = [];
        }

        // Collect ALL old image paths so we can delete ones no longer needed
        $oldColors      = $product->colors ?? [];
        $allOldThumbs   = array_filter(array_column($oldColors, 'image'));
        $allOldGalPaths = [];
        foreach ($oldColors as $oc) {
            foreach ($oc['gallery'] ?? [] as $gp) {
                if ($gp) $allOldGalPaths[] = $gp;
            }
        }

        // Build new colors array using meta to decide what to keep
        foreach ($colorsData as $idx => &$color) {

            $meta = $colorsMeta[$idx] ?? ['keep_thumb' => null, 'keep_gallery' => []];

            // ── Thumbnail ──────────────────────────────────────
            $fileKey = "color_image_{$idx}";
            if ($request->hasFile($fileKey)) {
                // New file uploaded — store it; old thumb (if any) will be deleted below
                $color['image'] = $request->file($fileKey)->store('products/colors', 'public');
            } else {
                // Keep whatever the meta says to keep (null = removed by user)
                $color['image'] = $meta['keep_thumb'] ?: null;
            }

            // ── Gallery ────────────────────────────────────────
            // Start with paths the user chose to keep
            $keepGal = array_values(array_filter($meta['keep_gallery'] ?? []));

            // Append newly uploaded files
            $galKey = "color_gallery_{$idx}";
            if ($request->hasFile($galKey)) {
                foreach ($request->file($galKey) as $gf) {
                    $keepGal[] = $gf->store('products/colors', 'public');
                }
            }

            $color['gallery'] = $keepGal;
        }
        unset($color);

        // ── Delete orphaned color images from storage ───────────
        // Collect all paths still in use after update
        $newThumbs   = array_filter(array_column($colorsData, 'image'));
        $newGalPaths = [];
        foreach ($colorsData as $nc) {
            foreach ($nc['gallery'] ?? [] as $gp) {
                if ($gp) $newGalPaths[] = $gp;
            }
        }

        // Delete old thumbs not present in new set
        foreach ($allOldThumbs as $oldThumb) {
            if ($oldThumb && !in_array($oldThumb, $newThumbs)) {
                Storage::disk('public')->delete($oldThumb);
            }
        }

        // Delete old gallery images not present in new set
        foreach ($allOldGalPaths as $oldGal) {
            if ($oldGal && !in_array($oldGal, $newGalPaths)) {
                Storage::disk('public')->delete($oldGal);
            }
        }

        // ── Global gallery ──────────────────────────────────────
        $existingGallery = $product->gallery_images ?? [];
        $keepGallery     = $request->input('keep_gallery', []);

        // Delete global gallery images that were removed
        foreach ($existingGallery as $path) {
            if (!in_array($path, $keepGallery)) {
                Storage::disk('public')->delete($path);
            }
        }

        $galleryPaths = array_values(array_filter($existingGallery, fn($p) => in_array($p, $keepGallery)));

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $file->store('products/gallery', 'public');
            }
        }

        // ── Size chart ──────────────────────────────────────────
        $sizeChartPath = $product->size_chart_image;
        if ($request->input('remove_size_chart') == '1') {
            if ($sizeChartPath) Storage::disk('public')->delete($sizeChartPath);
            $sizeChartPath = null;
        } elseif ($request->hasFile('size_chart_image')) {
            if ($sizeChartPath) Storage::disk('public')->delete($sizeChartPath);
            $sizeChartPath = $request->file('size_chart_image')->store('products/sizechart', 'public');
        }

        $inStock         = $request->input('in_stock') == '1';
        $shippingEnabled = $request->input('shipping_enabled') == '1';
        $sizes           = $request->input('sizes', []);
        if (!is_array($sizes)) $sizes = [];

        $product->update([
            'name'                => $request->input('name'),
            'description'         => $request->input('description'),
            'price'               => $request->input('price'),
            'category_id'         => $request->input('category_id') ?: null,
            'subcategory_id'      => $request->input('subcategory_id') ?: null,
            'image'               => $imagePath,
            'show_in_category'    => $request->input('category_id') ? true : false,
            'shipping_enabled'    => $shippingEnabled,
            'shipping_cost'       => $request->input('shipping_cost') ?? 0,
            'free_shipping_above' => $request->input('free_shipping_above') ?: null,
            'in_stock'            => $inStock ? 1 : 0,
            'stock_quantity'      => $request->input('stock_quantity') ?: null,
            'sizes'               => $sizes,
            'colors'              => $colorsData,
            'gallery_images'      => $galleryPaths,
            'size_chart_image'    => $sizeChartPath,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }

    // ─────────────────────────────────────────────────────────────
    //  API: Single product — returns full URLs for Vue frontend
    // ─────────────────────────────────────────────────────────────
    public function showApi($id)
    {
        $p = Product::findOrFail($id);

        $colors = collect($p->colors ?? [])->map(function ($c) {
            return [
                'name'    => $c['name']  ?? '',
                'hex'     => $c['hex']   ?? '#000000',
                'image'   => !empty($c['image'])
                                ? asset('storage/' . $c['image'])
                                : null,
                'gallery' => collect($c['gallery'] ?? [])
                                ->map(fn($g) => asset('storage/' . $g))
                                ->values()
                                ->toArray(),
            ];
        })->values()->toArray();

        $gallery = collect($p->gallery_images ?? [])
            ->map(fn($i) => asset('storage/' . $i))
            ->values()
            ->toArray();

        return response()->json([
            'id'                  => $p->id,
            'name'                => $p->name,
            'description'         => $p->description,
            'price'               => (float) $p->price,
            'type'                => 'product',
            'is_new'              => false,
            'image'               => $p->image ? asset('storage/' . $p->image) : null,
            'gallery_images'      => $gallery,
            'sizes'               => $p->sizes ?? [],
            'colors'              => $colors,
            'in_stock'            => (bool) $p->in_stock,
            'stock_quantity'      => $p->stock_quantity ? (int) $p->stock_quantity : null,
            'shipping_enabled'    => (bool) $p->shipping_enabled,
            'shipping_cost'       => (float) ($p->shipping_cost ?? 0),
            'free_shipping_above' => $p->free_shipping_above ? (float) $p->free_shipping_above : null,
            'size_chart_image'    => $p->size_chart_image ? asset('storage/' . $p->size_chart_image) : null,
        ]);
    }

    // ════════════════════════════════════════
    // DESTROY
    // ════════════════════════════════════════
    public function destroy(Product $product)
    {
        if ($product->image) Storage::disk('public')->delete($product->image);
        foreach ($product->gallery_images ?? [] as $img) Storage::disk('public')->delete($img);
        // Delete all color images
        foreach ($product->colors ?? [] as $c) {
            if (!empty($c['image'])) Storage::disk('public')->delete($c['image']);
            foreach ($c['gallery'] ?? [] as $gp) {
                if ($gp) Storage::disk('public')->delete($gp);
            }
        }
        if ($product->size_chart_image) Storage::disk('public')->delete($product->size_chart_image);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted!');
    }

    // ════════════════════════════════════════
    // BULK: FEATURED
    // ════════════════════════════════════════
    public function featured(Request $request)
    {
        $request->validate(['product_ids' => 'required|array', 'product_ids.*' => 'integer|exists:products,id', 'action' => 'required|in:add,remove']);
        Product::whereIn('id', $request->product_ids)->update(['is_featured' => $request->action === 'add']);
        return response()->json(['success' => true, 'message' => $request->action === 'add' ? 'Products Featured mein add ho gaye!' : 'Products Featured se remove ho gaye!']);
    }

    // ════════════════════════════════════════
    // BULK: APPAREL
    // ════════════════════════════════════════
    public function apparel(Request $request)
    {
        $request->validate(['product_ids' => 'required|array', 'product_ids.*' => 'integer|exists:products,id', 'action' => 'required|in:add,remove']);
        Product::whereIn('id', $request->product_ids)->update(['is_apparel' => $request->action === 'add']);
        return response()->json(['success' => true, 'message' => $request->action === 'add' ? 'Products Apparel mein add ho gaye!' : 'Products Apparel se remove ho gaye!']);
    }

    // ════════════════════════════════════════
    // BULK: CATEGORY ASSIGN
    // ════════════════════════════════════════
    public function bulkCategory(Request $request)
    {
        $request->validate(['product_ids' => 'required|array', 'product_ids.*' => 'integer|exists:products,id', 'category_id' => 'nullable|exists:categories,id', 'subcategory_id' => 'nullable|exists:categories,id', 'show_in_category' => 'required|boolean']);

        $updateData = ['show_in_category' => $request->show_in_category];
        if ($request->show_in_category && $request->category_id) {
            $updateData['category_id']    = $request->category_id;
            $updateData['subcategory_id'] = $request->subcategory_id;
        }

        Product::whereIn('id', $request->product_ids)->update($updateData);

        return response()->json(['success' => true, 'message' => $request->show_in_category ? count($request->product_ids) . ' products category mein add ho gaye!' : count($request->product_ids) . ' products category se remove ho gaye!']);
    }

    // ════════════════════════════════════════
    // API: FEATURED PRODUCTS
    // ════════════════════════════════════════
    public function apiFeaturedProducts()
    {
        $products = Product::where('is_featured', 1)->get()->map(fn($p) => $this->mapProduct($p));
        $models   = \App\Models\CustomizerModel::where('is_featured', 1)->get()->map(fn($m) => $this->mapModel($m));
        return response()->json($products->concat($models)->values());
    }

    // ════════════════════════════════════════
    // API: APPAREL PRODUCTS
    // ════════════════════════════════════════
    public function apiApparelProducts()
    {
        $products = Product::where('is_apparel', 1)->get()->map(fn($p) => $this->mapProduct($p));
        $models   = \App\Models\CustomizerModel::where('is_apparel', 1)->get()->map(fn($m) => $this->mapModel($m));
        return response()->json($products->concat($models)->values());
    }

    // ════════════════════════════════════════
    // API: CATEGORY PRODUCTS
    // ════════════════════════════════════════
    public function apiCategoryProducts($categoryId)
    {
        $category      = Category::findOrFail($categoryId);
        $isSubcategory = !is_null($category->parent_id);

        $query = Product::where('show_in_category', true)
            ->select('id', 'name', 'price', 'image', 'in_stock', 'sizes', 'colors', 'gallery_images');

        if ($isSubcategory) {
            $query->where('subcategory_id', $categoryId);
        } else {
            $query->where('category_id', $categoryId);
        }

        return $query->get()->map(fn($p) => $this->mapProduct($p));
    }

    // ════════════════════════════════════════
    // API: ALL PRODUCTS
    // ════════════════════════════════════════
    public function indexApi()
    {
        return Product::select('id', 'name', 'price', 'image')->get()->map(fn($p) => $this->mapProduct($p));
    }

    // ════════════════════════════════════════
    // PRIVATE HELPERS
    // ════════════════════════════════════════
    private function mapProduct($p)
    {
        return [
            'id'             => $p->id,
            'name'           => $p->name,
            'price'          => (float) $p->price,
            'image'          => $p->image ? asset('storage/' . $p->image) : null,
            'in_stock'       => $p->in_stock ?? true,
            'sizes'          => $p->sizes ?? [],
            'colors'         => $p->colors ?? [],
            'gallery_images' => collect($p->gallery_images ?? [])->map(fn($i) => asset('storage/' . $i))->values(),
            'type'           => 'product',
        ];
    }

    private function mapModel($m)
    {
        return [
            'id'    => $m->id,
            'name'  => $m->title,
            'price' => (float) $m->price,
            'image' => $m->thumbnail ? asset('uploads/models/' . $m->thumbnail)
                     : ($m->custom_front_svg ? asset('uploads/models/' . $m->custom_front_svg)
                     : ($m->front_svg ? asset('uploads/models/' . $m->front_svg) : null)),
            'type'  => 'model',
        ];
    }
}
