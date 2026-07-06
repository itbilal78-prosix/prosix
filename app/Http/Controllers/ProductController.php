<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'subcategory']);
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $products    = $query->latest()->paginate(20);
        $allProducts = Product::with(['category', 'subcategory'])->get();
        $categories  = Category::whereNull('parent_id')->with('subcategories')->orderBy('position')->get();
        return view('products.index', compact('products', 'allProducts', 'categories'));
    }

    public function create()
    {
        $categories  = Category::with('subcategories')->get();
        $navigations = \App\Models\Navigation::where('status', 1)->orderBy('position')->get();
        return view('products.create', compact('categories', 'navigations'));
    }

    public function edit(Product $product)
    {
        $categories  = Category::with('subcategories')->get();
        $navigations = \App\Models\Navigation::where('status', 1)->orderBy('position')->get();
        return view('products.edit', compact('product', 'categories', 'navigations'));
    }

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

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $colorsData = [];
        try {
            $decoded    = json_decode($request->input('colors_json', '[]'), true);
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

        $galleryPaths = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $file->store('products/gallery', 'public');
            }
        }

        $sizeChartPath = null;
        if ($request->hasFile('size_chart_image')) {
            $sizeChartPath = $request->file('size_chart_image')->store('products/sizechart', 'public');
        }

        $inStock         = $request->input('in_stock') == '1';
        $shippingEnabled = $request->input('shipping_enabled') == '1';
        $sizes           = $request->input('sizes', []);
        if (!is_array($sizes)) $sizes = [];

        $product = Product::create([
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

        ActivityLogger::log('created', 'Product', $product->name, $product->id, [
            'price'       => $product->price,
            'category_id' => $product->category_id,
        ]);

        return redirect()->route('products.index')->with('success', 'Product saved successfully!');
    }

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

        $imagePath = $product->image;
        if ($request->input('remove_main_image') == '1') {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $imagePath = null;
        } elseif ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $colorsData = [];
        try {
            $decoded    = json_decode($request->input('colors_json', '[]'), true);
            $colorsData = is_array($decoded) ? $decoded : [];
        } catch (\Exception $e) {
            $colorsData = $product->colors ?? [];
        }

        $colorsMeta = [];
        try {
            $metaRaw    = $request->input('color_images_meta', '[]');
            $colorsMeta = json_decode($metaRaw, true);
            if (!is_array($colorsMeta)) $colorsMeta = [];
        } catch (\Exception $e) {
            $colorsMeta = [];
        }

        $oldColors      = $product->colors ?? [];
        $allOldThumbs   = array_filter(array_column($oldColors, 'image'));
        $allOldGalPaths = [];
        foreach ($oldColors as $oc) {
            foreach ($oc['gallery'] ?? [] as $gp) {
                if ($gp) $allOldGalPaths[] = $gp;
            }
        }

        foreach ($colorsData as $idx => &$color) {
            $meta    = $colorsMeta[$idx] ?? ['keep_thumb' => null, 'keep_gallery' => []];
            $fileKey = "color_image_{$idx}";
            if ($request->hasFile($fileKey)) {
                $color['image'] = $request->file($fileKey)->store('products/colors', 'public');
            } else {
                $color['image'] = $meta['keep_thumb'] ?: null;
            }
            $keepGal = array_values(array_filter($meta['keep_gallery'] ?? []));
            $galKey  = "color_gallery_{$idx}";
            if ($request->hasFile($galKey)) {
                foreach ($request->file($galKey) as $gf) {
                    $keepGal[] = $gf->store('products/colors', 'public');
                }
            }
            $color['gallery'] = $keepGal;
        }
        unset($color);

        $newThumbs   = array_filter(array_column($colorsData, 'image'));
        $newGalPaths = [];
        foreach ($colorsData as $nc) {
            foreach ($nc['gallery'] ?? [] as $gp) {
                if ($gp) $newGalPaths[] = $gp;
            }
        }
        foreach ($allOldThumbs as $oldThumb) {
            if ($oldThumb && !in_array($oldThumb, $newThumbs)) Storage::disk('public')->delete($oldThumb);
        }
        foreach ($allOldGalPaths as $oldGal) {
            if ($oldGal && !in_array($oldGal, $newGalPaths)) Storage::disk('public')->delete($oldGal);
        }

        $existingGallery = $product->gallery_images ?? [];
        $keepGallery     = $request->input('keep_gallery', []);
        foreach ($existingGallery as $path) {
            if (!in_array($path, $keepGallery)) Storage::disk('public')->delete($path);
        }
        $galleryPaths = array_values(array_filter($existingGallery, fn($p) => in_array($p, $keepGallery)));
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $file->store('products/gallery', 'public');
            }
        }

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

        ActivityLogger::log('updated', 'Product', $product->name, $product->id, [
            'price'       => $request->input('price'),
            'category_id' => $request->input('category_id'),
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function showApi($id)
    {
        $p = Product::findOrFail($id);
        $colors = collect($p->colors ?? [])->map(function ($c) {
            return [
                'name'    => $c['name']  ?? '',
                'hex'     => $c['hex']   ?? '#000000',
                'image'   => !empty($c['image']) ? asset('storage/' . $c['image']) : null,
                'gallery' => collect($c['gallery'] ?? [])->map(fn($g) => asset('storage/' . $g))->values()->toArray(),
            ];
        })->values()->toArray();

        $gallery = collect($p->gallery_images ?? [])->map(fn($i) => asset('storage/' . $i))->values()->toArray();

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

    // ✅ FIXED — sirf soft delete, files nahi hatao
    public function destroy(Product $product)
    {
        ActivityLogger::log('deleted', 'Product', $product->name, $product->id, [
            'product_name' => $product->name,
            'price'        => $product->price,
            'category'     => $product->category?->name ?? 'No Category',
            'image'        => $product->image ? asset('storage/' . $product->image) : null,
        ]);

        // ✅ Files DELETE NAHI KARNI — recycle bin ke liye
        $product->delete(); // sirf soft delete

        return redirect()->route('products.index')->with('success', 'Product deleted!');
    }

    public function featured(Request $request)
    {
        $request->validate(['product_ids' => 'required|array', 'product_ids.*' => 'integer|exists:products,id', 'action' => 'required|in:add,remove']);
        Product::whereIn('id', $request->product_ids)->update(['is_featured' => $request->action === 'add']);
        return response()->json(['success' => true, 'message' => $request->action === 'add' ? 'Products Featured mein add ho gaye!' : 'Products Featured se remove ho gaye!']);
    }

    public function apparel(Request $request)
    {
        $request->validate(['product_ids' => 'required|array', 'product_ids.*' => 'integer|exists:products,id', 'action' => 'required|in:add,remove']);
        Product::whereIn('id', $request->product_ids)->update(['is_apparel' => $request->action === 'add']);
        return response()->json(['success' => true, 'message' => $request->action === 'add' ? 'Products Apparel mein add ho gaye!' : 'Products Apparel se remove ho gaye!']);
    }

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
public function duplicateCategory(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
    ]);

    DB::transaction(function () use ($request) {

        $oldCat = Category::with('subcategories')->findOrFail($request->category_id);

        // Parent category duplicate
        $newCat = $oldCat->replicate();
        $newCat->name = $oldCat->name . ' Copy';
        $newCat->save();

        $subMap = [];

        // Subcategories duplicate
        foreach ($oldCat->subcategories as $oldSub) {
            $newSub = $oldSub->replicate();
            $newSub->parent_id = $newCat->id;
            $newSub->name = $oldSub->name . ' Copy';
            $newSub->save();

            $subMap[$oldSub->id] = $newSub->id;
        }

        // Products duplicate
        $products = Product::where('category_id', $oldCat->id)->get();

        foreach ($products as $oldProduct) {
            $newProduct = $oldProduct->replicate();
            $newProduct->name = $oldProduct->name . ' Copy';
            $newProduct->category_id = $newCat->id;

            if ($oldProduct->subcategory_id && isset($subMap[$oldProduct->subcategory_id])) {
                $newProduct->subcategory_id = $subMap[$oldProduct->subcategory_id];
            } else {
                $newProduct->subcategory_id = null;
            }

            $newProduct->show_in_category = true;
            $newProduct->save();
        }
    });

    return back()->with('success', 'Category, subcategories and products duplicated successfully!');
}
    public function apiFeaturedProducts()
    {
        $products = Product::where('is_featured', 1)->get()->map(fn($p) => $this->mapProduct($p));
        $models   = \App\Models\CustomizerModel::where('is_featured', 1)->get()->map(fn($m) => $this->mapModel($m));
        return response()->json($products->concat($models)->values());
    }

    public function apiApparelProducts()
    {
        $products = Product::where('is_apparel', 1)->get()->map(fn($p) => $this->mapProduct($p));
        $models   = \App\Models\CustomizerModel::where('is_apparel', 1)->get()->map(fn($m) => $this->mapModel($m));
        return response()->json($products->concat($models)->values());
    }

    public function apiCategoryProducts($categoryId)
    {
        $category      = Category::findOrFail($categoryId);
        $isSubcategory = !is_null($category->parent_id);
        $query = Product::where('show_in_category', true)->select('id', 'name', 'price', 'image', 'in_stock', 'sizes', 'colors', 'gallery_images');
        if ($isSubcategory) {
            $query->where('subcategory_id', $categoryId);
        } else {
            $query->where('category_id', $categoryId);
        }
        return $query->get()->map(fn($p) => $this->mapProduct($p));
    }

    public function indexApi()
    {
        return Product::select('id', 'name', 'price', 'image')->get()->map(fn($p) => $this->mapProduct($p));
    }

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
