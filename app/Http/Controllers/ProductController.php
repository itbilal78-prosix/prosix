<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ════════════════════════════════════════
    // INDEX
    // ════════════════════════════════════════
    public function index(Request $request)
    {
        $query = Product::with(['category', 'subcategory']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $products = $query->latest()->paginate(20);

        // ✅ YE LINE ADD KARO — category blocks ke liye saare products
        $allProducts = Product::with(['category', 'subcategory'])->get();

        $categories = Category::whereNull('parent_id')
            ->with('subcategories')
            ->orderBy('position')
            ->get();

        if ($request->ajax()) {
            return view('products.index', compact('products', 'allProducts', 'categories'));
        }

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
    // STORE
    // ════════════════════════════════════════
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'category_id' => $validated['category_id'] ?? null,
            'subcategory_id' => $validated['subcategory_id'] ?? null,
            'image' => $imagePath,
            // ✅ KEY: Create karte waqt show_in_category = false
            // Frontend pe tab show hoga jab manually "Add to Category" press karein
            'show_in_category' => $validated['category_id'] ? true : false,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product saved! Ab Products list se "Add to Category" click karo frontend pe dikhane ke liye.');
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

    // ════════════════════════════════════════
    // UPDATE
    // ════════════════════════════════════════
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Image handle
        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // ✅ Category change hone par show_in_category reset karo
        $showInCategory = $product->show_in_category;
        $showInCategory = $validated['category_id'] ? true : false;

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'category_id' => $validated['category_id'] ?? null,
            'subcategory_id' => $validated['subcategory_id'] ?? null,
            'image' => $imagePath,
            'show_in_category' => $showInCategory,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product updated!');
    }

    // ════════════════════════════════════════
    // DESTROY
    // ════════════════════════════════════════
    public function destroy(Product $product)
    {
        if ($product->image) {
            \Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted!');
    }

    // ════════════════════════════════════════
    // API: FEATURED PRODUCTS
    // ════════════════════════════════════════
    public function apiFeaturedProducts()
    {
        $products = \App\Models\Product::where('is_featured', 1)->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => (float) $p->price,
                'image' => $p->image ? asset('storage/'.$p->image) : null,
                'type' => 'product',
            ];
        });

        $models = \App\Models\CustomizerModel::where('is_featured', 1)->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->title,
                'price' => (float) $m->price,
                'image' => $m->thumbnail
                    ? asset('uploads/models/'.$m->thumbnail)
                    : ($m->custom_front_svg
                        ? asset('uploads/models/'.$m->custom_front_svg)
                        : ($m->front_svg
                            ? asset('uploads/models/'.$m->front_svg)
                            : null)),
                'type' => 'model',
            ];
        });

        return response()->json(
            $products->concat($models)->values()
        );
    }

    // ════════════════════════════════════════
    // API: APPAREL PRODUCTS
    // ════════════════════════════════════════
    public function apiApparelProducts()
    {
        $products = \App\Models\Product::where('is_apparel', 1)->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => (float) $p->price,
                'image' => $p->image ? asset('storage/'.$p->image) : null,
                'type' => 'product',
            ];
        });

        $models = \App\Models\CustomizerModel::where('is_apparel', 1)->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->title,
                'price' => (float) $m->price,
                'image' => $m->thumbnail
                    ? asset('uploads/models/'.$m->thumbnail)
                    : ($m->custom_front_svg
                        ? asset('uploads/models/'.$m->custom_front_svg)
                        : ($m->front_svg
                            ? asset('uploads/models/'.$m->front_svg)
                            : null)),
                'type' => 'model',
            ];
        });

        return response()->json(
            $products->concat($models)->values()
        );
    }

    // ════════════════════════════════════════
    // API: CATEGORY PRODUCTS
    // ════════════════════════════════════════
    public function apiCategoryProducts($categoryId)
    {
        // ✅ Sirf woh products jo show_in_category = true hain
        $category = Category::findOrFail($categoryId);
        $isSubcategory = ! is_null($category->parent_id);

        $query = Product::where('show_in_category', true)
            ->select('id', 'name', 'price', 'image');

        if ($isSubcategory) {
            $query->where('subcategory_id', $categoryId);
        } else {
            $query->where('category_id', $categoryId);
        }

        return $query->get()->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'price' => (float) $p->price,
            'image' => $p->image ? asset('storage/'.$p->image) : null,
        ]);
    }

    // ════════════════════════════════════════
    // BULK: FEATURED (add/remove)
    // ════════════════════════════════════════
    public function featured(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:products,id',
            'action' => 'required|in:add,remove',
        ]);

        Product::whereIn('id', $request->product_ids)
            ->update(['is_featured' => $request->action === 'add']);

        return response()->json([
            'success' => true,
            'message' => $request->action === 'add'
                ? 'Products Featured mein add ho gaye!'
                : 'Products Featured se remove ho gaye!',
        ]);
    }

    // ════════════════════════════════════════
    // BULK: APPAREL (add/remove)
    // ════════════════════════════════════════
    public function apparel(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:products,id',
            'action' => 'required|in:add,remove',
        ]);

        Product::whereIn('id', $request->product_ids)
            ->update(['is_apparel' => $request->action === 'add']);

        return response()->json([
            'success' => true,
            'message' => $request->action === 'add'
                ? 'Products Apparel mein add ho gaye!'
                : 'Products Apparel se remove ho gaye!',
        ]);
    }

    // ════════════════════════════════════════
    // ✅ BULK: CATEGORY ASSIGN
    // show_in_category = true  → frontend pe dikhao
    // show_in_category = false → frontend pe chhupao
    // ════════════════════════════════════════
    public function bulkCategory(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:products,id',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'show_in_category' => 'required|boolean',
        ]);

        $updateData = [
            'show_in_category' => $request->show_in_category,
        ];

        // Agar category assign ho rahi hai
        if ($request->show_in_category && $request->category_id) {
            $updateData['category_id'] = $request->category_id;
            $updateData['subcategory_id'] = $request->subcategory_id;
        }

        // Agar category remove ho rahi hai
        if (! $request->show_in_category) {
            $updateData['show_in_category'] = false;
            // category_id aur subcategory_id database mein rehne do (sirf hide karo)
        }

        Product::whereIn('id', $request->product_ids)->update($updateData);

        return response()->json([
            'success' => true,
            'message' => $request->show_in_category
                ? count($request->product_ids).' products category mein add ho gaye aur frontend pe dikh rahe hain!'
                : count($request->product_ids).' products category se remove ho gaye (frontend pe nahi dikhenge)!',
        ]);
    }

    // ════════════════════════════════════════
    // API: ALL PRODUCTS
    // ════════════════════════════════════════
    public function indexApi()
    {
        return Product::select('id', 'name', 'price', 'image')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => (float) $p->price,
                    'image' => $p->image ? asset('storage/'.$p->image) : null,
                ];
            });
    }

    // ════════════════════════════════════════
    // API: SINGLE PRODUCT
    // ════════════════════════════════════════
    public function showApi($id)
    {
        $product = Product::findOrFail($id);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => (float) $product->price,
            'image' => $product->image ? asset('storage/'.$product->image) : null,
            'type' => 'product',
        ]);
    }
}
