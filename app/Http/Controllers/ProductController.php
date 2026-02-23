<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;      // <-- important
use App\Models\Subcategory;   // if you want subcategories as separate table
use Illuminate\Http\Request;

class ProductController extends Controller
{
public function index(Request $request)
{
    $query = Product::query();

    if ($request->has('search') && $request->search != '') {
        $query->where('name', 'like', "%{$request->search}%");
    }

    $products = $query->orderBy('id', 'desc')->paginate(10);
    $products->appends($request->all());

    // Return different view for AJAX
    if ($request->ajax()) {
        return view('products.index', compact('products'))->render();
    }

    return view('products.index', compact('products'));
}





public function create()
{
    $categories = Category::with('subcategories')->whereNull('parent_id')->get();
    return view('products.create', compact('categories'));
}


public function store(Request $request)
{
    // Fetch the selected category with its subcategories
    $category = Category::with('subcategories')->findOrFail($request->category_id);

    // Validation rules
    $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];

    // If the selected category has subcategories, make subcategory_id required
    if ($category->subcategories->count() > 0) {
        $rules['subcategory_id'] = 'required|exists:categories,id';
    } else {
        $rules['subcategory_id'] = 'nullable|exists:categories,id';
    }

    // Validate request
    $validated = $request->validate($rules);

    // Handle image upload
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('products', 'public');
    }

    // Create the product
    Product::create([
        'name' => $validated['name'],
        'description' => $validated['description'] ?? null,
        'price' => $validated['price'],
        'category_id' => $validated['category_id'],
        'subcategory_id' => $validated['subcategory_id'] ?? null,
        'image' => $imagePath,
    ]);

    return redirect()->route('products.index')
                     ->with('success', 'Product created successfully.');
}


 public function edit(Product $product)
    {
        // Get all parent categories with their subcategories
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();

        return view('products.edit', compact('product', 'categories'));
    }

public function update(Request $request, Product $product)
{
    // Fetch the selected category with its subcategories
    $category = Category::with('subcategories')->findOrFail($request->category_id);

    // Validation rules
    $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];

    // If category has subcategories, make subcategory_id required
    if ($category->subcategories->count() > 0) {
        $rules['subcategory_id'] = 'required|exists:categories,id';
    } else {
        $rules['subcategory_id'] = 'nullable|exists:categories,id';
    }

    // Validate request
    $validated = $request->validate($rules);

    // Handle image upload
    if ($request->hasFile('image')) {
        if ($product->image) {
            \Storage::disk('public')->delete($product->image);
        }
        $validated['image'] = $request->file('image')->store('products', 'public');
    }

    // Update product
    $product->update([
        'name' => $validated['name'],
        'description' => $validated['description'] ?? null,
        'price' => $validated['price'],
        'category_id' => $validated['category_id'],
        'subcategory_id' => $validated['subcategory_id'] ?? null,
        'image' => $validated['image'] ?? $product->image,
    ]);

    return redirect()->route('products.index')
                     ->with('success', 'Product updated successfully.');
}



    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

// === API for Frontend ===
public function apiFeaturedProducts()
{
    $products = Product::where('is_featured', true)
        ->select('id', 'name', 'price', 'image')
        ->orderBy('id', 'desc')
        ->get();

    return $products->map(function ($p) {
        return [
            'id'    => $p->id,
            'name'  => $p->name,
            'price' => (float) $p->price,
            'image' => $p->image ? asset('storage/' . $p->image) : '',
        ];
    });
}

// === Featured (POST) - Improve this ===
public function featured(Request $request)
{
    $request->validate([
        'product_ids' => 'required|array',
        'product_ids.*' => 'integer|exists:products,id',
        'action' => 'required|in:add,remove',
    ]);

    $value = $request->action === 'add' ? true : false;

    Product::whereIn('id', $request->product_ids)
        ->update(['is_featured' => $value]);

    $message = $request->action === 'add'
        ? 'Selected products added to Featured successfully!'
        : 'Selected products removed from Featured successfully!';

    return response()->json([
        'success' => true,
        'message' => $message
    ]);
}
// === Apparel (POST) - Bulk update ===
public function apparel(Request $request)
{
    $request->validate([
        'product_ids' => 'required|array',
        'product_ids.*' => 'integer|exists:products,id',
        'action'      => 'required|in:add,remove',
    ]);

    $value = $request->action === 'add' ? true : false;

    Product::whereIn('id', $request->product_ids)
        ->update(['is_apparel' => $value]);

    $message = $request->action === 'add'
        ? 'Selected products added to Apparel successfully!'
        : 'Selected products removed from Apparel successfully!';

    return response()->json([
        'success' => true,
        'message' => $message
    ]);
}
public function apiApparelProducts()
{
    $products = Product::where('is_apparel', true)
        ->select('id', 'name', 'price', 'image')
        ->latest()
        ->get();

    return $products->map(function ($p) {
        return [
            'id'    => $p->id,
            'name'  => $p->name,
            'price' => (float) $p->price,
            'image' => $p->image ? asset('storage/' . $p->image) : null,
        ];
    });
}



}
