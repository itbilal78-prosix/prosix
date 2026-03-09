<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Navigation;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CategoryController extends Controller
{
    /* =========================
       ADMIN (WEB) CRUD
    ==========================*/

    public function index()
    {
        $categories = Category::orderBy('position')->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $navigations = Navigation::where('status', 1)->orderBy('position')->get();

        return view('categories.create', compact('navigations'));
    }

    public function subCreate()
    {
        $navigations = Navigation::where('status', 1)->orderBy('position')->get();

        $parentCategories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('categories.sub_create', compact('navigations', 'parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon_image' => 'required|image|mimes:jpg,jpeg,png,webp',
            'highlight_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'status' => 'required|boolean',
            'navigation_id' => 'nullable|exists:navigations,id',
            'parent_id' => 'nullable|exists:categories,id',
            'highlight' => 'sometimes|accepted',
            'password' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['name', 'status', 'navigation_id', 'parent_id', 'highlight']);
        $data['highlight'] = $request->has('highlight') ? 1 : 0;

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('icon_image')) {
            $path = $request->file('icon_image')->store('categories', 'public');
            $data['icon_image'] = '/storage/'.$path;
        }

        if ($request->hasFile('highlight_image')) {
            $path = $request->file('highlight_image')->store('categories/highlight', 'public');
            $data['highlight_image'] = '/storage/'.$path;
        }

        // Make sure subcategory doesn't have navigation_id
        if ($request->parent_id) {
            $data['navigation_id'] = null;
        }

        Category::create($data);

        return redirect()->route('categories.index')
            ->with('success', 'Category Added Successfully');
    }

    public function edit(Category $category)
    {
        $navigations = Navigation::where('status', 1)->orderBy('position')->get();
        $categories = Category::where('status', 1)->get();

        return view('categories.edit', compact('category', 'navigations', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'highlight_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'status' => 'required|boolean',
            'navigation_id' => 'nullable|exists:navigations,id',
            'parent_id' => 'nullable|exists:categories,id',
            'highlight' => 'sometimes|boolean',
            'password' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['name', 'status', 'navigation_id', 'parent_id', 'highlight']);
        $data['highlight'] = $request->has('highlight') ? 1 : 0;

        // Important: Protect parent_id for subcategories
        if ($category->parent_id) {
            $data['parent_id'] = $category->parent_id;        // cannot change
            $data['navigation_id'] = null;
        } else {
            // Only top-level can have navigation
            $data['parent_id'] = $request->parent_id ?? null;
            if ($data['parent_id']) {
                $data['navigation_id'] = null;
            }
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } elseif ($request->has('password') && $request->password === '') {
            // If explicitly sent empty → remove password
            $data['password'] = null;
        }
        // If not sent at all → keep old password

        if ($request->hasFile('icon_image')) {
            $path = $request->file('icon_image')->store('categories', 'public');
            $data['icon_image'] = '/storage/'.$path;
        }

        if ($request->hasFile('highlight_image')) {
            $path = $request->file('highlight_image')->store('categories/highlight', 'public');
            $data['highlight_image'] = '/storage/'.$path;
        }

        $category->update($data);

        return redirect()->route('categories.index')
            ->with('success', 'Category Updated Successfully');
    }

    public function destroy(Category $category)
    {

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category Deleted Successfully');
    }

    public function toggleStatus(Category $category)
    {
        $category->update(['status' => ! $category->status]);

        return redirect()->route('categories.index')
            ->with('success', 'Category status updated');
    }

    /* =========================
       API ENDPOINTS
    ==========================*/

    public function apiIndex()
    {
        return Category::where('status', 1)
            ->where('highlight', 1)
            ->select('id', 'name', 'icon_image', 'highlight_image')
            ->orderBy('name')
            ->get();
    }

    public function apiHighlighted()
    {
        return Category::where('status', 1)
            ->where('highlight', 1)
            ->select('id', 'name', 'icon_image', 'highlight_image')
            ->orderBy('name')
            ->get();
    }

    public function apiCategoriesByNavigation()
    {
        $categories = Category::with(['subcategories' => function ($q) {
            $q->select('id', 'name', 'parent_id', 'icon_image', 'password')
                ->where('status', 1);
        }])
            ->where('status', 1)
            ->whereNotNull('navigation_id')
            ->select('id', 'name', 'navigation_id', 'icon_image', 'password')
            ->orderBy('name')
            ->get()
            ->groupBy('navigation_id');

        $grouped = [];
        foreach ($categories as $navId => $cats) {
            $grouped[(int) $navId] = $cats->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'icon_image' => $cat->icon_image,
                    'password' => ! empty($cat->password),

                    'subcategories' => $cat->subcategories->map(fn ($sub) => [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        'password' => ! empty($sub->password),
                    ])->values()->all(),
                ];
            })->values()->all();
        }

        return response()->json($grouped);
    }

    public function categoriesBySlug($slug)
    {
        $navigation = Navigation::where('slug', $slug)->where('status', 1)->first();

        if (! $navigation) {
            return response()->json([
                'error' => 'Menu not found',
                'slug_received' => $slug,
            ], 404);
        }

        $categories = Category::with(['subcategories' => function ($q) {
            $q->where('status', 1)
                ->select('id', 'name', 'parent_id', 'icon_image');
        }])
            ->where('navigation_id', $navigation->id)
            ->where('status', 1)
            ->whereNull('parent_id')
            ->select('id', 'name', 'icon_image', 'navigation_id', 'highlight_image')
            ->get();

        return response()->json([
            'navigation' => [
                'id' => $navigation->id,
                'title' => $navigation->title,
                'slug' => $navigation->slug,
            ],
            'categories' => $categories->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'icon_image' => $cat->icon_image,
                    'highlight_image' => $cat->highlight_image,

                    'password' => ! empty($cat->password),

                    'subcategories' => $cat->subcategories->map(fn ($sub) => [
                        'id' => $sub->id,
                        'name' => $sub->name,
                    ])->values(),
                ];
            }),
        ]);

    }

    public function categoryProducts($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->where('status', 1)
            ->take(15)
            ->get();

        return response()->json([
            'category_name' => $category->name,
            'products' => $products,

        ]);
    }

    public function apiMenuCategories($slug)
    {
        $navigation = Navigation::where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $categories = Category::with(['subcategories' => function ($q) {
            $q->where('status', 1)
                ->orderBy('position')
                ->select('id', 'name', 'parent_id');
        }])
            ->where('navigation_id', $navigation->id)
            ->where('status', 1)
            ->whereNull('parent_id')
            ->select('id', 'name', 'icon_image', 'highlight_image', 'password')
            ->orderBy('position')
            ->get();

        return response()->json([
            'navigation' => [
                'id' => $navigation->id,
                'title' => $navigation->title,
                'slug' => $navigation->slug,
            ],
            'categories' => $categories->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'icon_image' => $cat->icon_image,
                    'highlight_image' => $cat->highlight_image,
                    'password' => ! empty($cat->password), // parent password boolean
                    'subcategories' => $cat->subcategories->map(fn ($sub) => [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        'password' => ! empty($sub->password), // ✅ SUBCATEGORY PASSWORD BOOLEAN
                    ])->values(),
                ];
            }),

        ]);
    }

    public function subcategories($id)
    {
        $category = Category::with(['subcategories' => function ($q) {
            $q->where('status', 1)
                ->orderBy('position')    // 🔥 ADD
                ->select('id', 'name', 'icon_image', 'parent_id', 'password'); // ✅ add password
        }])
            ->where('id', $id)
            ->where('status', 1)
            ->select('id', 'name', 'icon_image')
            ->first();

        if (! $category) {
            return response()->json([
                'error' => 'Category not found',
                'id_received' => $id,
            ], 404);
        }

        $parentImage = $category->icon_image ? url($category->icon_image) : null;

        $subcategories = $category->subcategories->map(function ($sub) {
            return [
                'id' => $sub->id,
                'name' => $sub->name,
                'icon_image' => $sub->icon_image ? url($sub->icon_image) : null,
                'password' => ! empty($sub->password), // ✅ boolean flag
            ];
        })->values();

        return response()->json([
            'parent' => [
                'id' => $category->id,
                'name' => $category->name,
                'icon_image' => $parentImage,
            ],
            'subcategories' => $subcategories,
        ]);
    }

// CategoryController.php mein yeh method replace karo

public function products($id)
{
    $category = Category::find($id);

    if (!$category) {
        return response()->json(['error' => 'Category not found'], 404);
    }

    $isSubcategory = !is_null($category->parent_id);

    $query = Product::query()
        ->select('id', 'name', 'price', 'image')
        ->where('show_in_category', true); // ✅ SIRF YEH DIKHAO JO ADMIN NE ASSIGN KIYE

    if ($isSubcategory) {
        $query->where('subcategory_id', $id);
    } else {
        $query->where('category_id', $id)
              ->whereNull('subcategory_id');
    }

    $products = $query->get()->map(function ($product) {
        return [
            'id'    => $product->id,
            'name'  => $product->name,
            'price' => $product->price,
            'image' => $product->image ? asset('storage/' . $product->image) : null,
        ];
    });

    return response()->json([
        'category' => [
            'id'   => $category->id,
            'name' => $category->name,
        ],
        'products' => $products,
    ]);
}

    public function verifyCategoryPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $category = Category::findOrFail($id);

        if (! $category->password) {
            return response()->json(['success' => true]);
        }

        if (! \Hash::check($request->password, $category->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password',
            ], 401);
        }

        return response()->json(['success' => true]);
    }

    public function reorder(Request $request)
    {
        foreach ($request->order as $item) {
            Category::where('id', $item['id'])
                ->update(['position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }
}
