<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\CustomizerModel;
use App\Models\Font;
use App\Models\Navigation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomizerModelController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────────────
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->with(['subcategories'])
            ->get();

        return view('admin.models.index', compact('categories'));
    }

    // ─── CREATE ───────────────────────────────────────────────────
    public function create()
    {
        $navigations = Navigation::where('status', 1)->get() ?? collect();

        $parentCategories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->with('subcategories')
            ->get();

        $backendColors = Color::all();

        return view('admin.models.create', compact('navigations', 'parentCategories', 'backendColors'));
    }

    // ─── STORE ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'title'              => 'required',
            'price'              => 'nullable|numeric|min:0',
            'shipping_cost'      => 'nullable|numeric|min:0',
            'free_shipping_above'=> 'nullable|numeric|min:0',
            'stock_quantity'     => 'nullable|integer|min:0',
            'sizes'              => 'nullable|array',
            'size_chart_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $data = $request->only([
            'model_name', 'title', 'price', 'description',
            'navigation_id', 'category_id', 'subcategory_id',
        ]);

        $data['in_stock']          = $request->input('in_stock') == '1';
        $data['stock_quantity']    = $request->input('stock_quantity') ?: null;
        $data['shipping_enabled']  = $request->input('shipping_enabled') == '1';
        $data['shipping_cost']     = $request->input('shipping_cost') ?? 0;
        $data['free_shipping_above'] = $request->input('free_shipping_above') ?: null;

        $sizes = $request->input('sizes', []);
        $data['sizes'] = is_array($sizes) ? $sizes : [];

        if ($request->hasFile('size_chart_image')) {
            $data['size_chart_image'] = $request->file('size_chart_image')
                ->store('models/sizechart', 'public');
        }

        $views = ['front', 'back', 'left', 'right'];
        $types = ['black', 'white', 'svg'];

        foreach ($views as $view) {
            foreach ($types as $type) {
                $key = "{$view}_{$type}";
                if ($request->hasFile($key)) {
                    $file = $request->file($key);
                    $name = time()."_{$view}_{$type}.".$file->extension();
                    $file->move(public_path('uploads/models'), $name);
                    $data[$key] = $name;
                }
            }
        }

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $name = time().'_thumbnail.'.$file->extension();
            $file->move(public_path('uploads/models'), $name);
            $data['thumbnail'] = $name;
        }

        $colorsData = [];
        try {
            $decoded    = json_decode($request->input('colors_json', '[]'), true);
            $colorsData = is_array($decoded) ? $decoded : [];
        } catch (\Exception $e) {
            $colorsData = [];
        }

        foreach ($colorsData as $idx => &$color) {
            $color['views'] = [];
            foreach ($views as $view) {
                $color['views'][$view] = [];
                foreach ($types as $type) {
                    $fileKey = "color_{$idx}_{$view}_{$type}";
                    $color['views'][$view][$type] = $request->hasFile($fileKey)
                        ? $request->file($fileKey)->store('models/colors', 'public')
                        : null;
                }
            }
        }
        unset($color);

        $data['colors_data'] = $colorsData;

        CustomizerModel::create($data);

        return redirect()->route('models.index')->with('success', 'Model Added Successfully');
    }

    // ─── SHOW ─────────────────────────────────────────────────────
    public function show($id)
    {
        $model  = CustomizerModel::findOrFail($id);
        $colors = Color::all();
        $fonts  = Font::all()->map(fn ($font) => [
            'id'       => $font->id,
            'name'     => $font->name,
            'file_url' => asset('storage/'.$font->file),
        ]);

        if (request()->expectsJson()) {
            return response()->json($this->modelApiData($model));
        }

        return view('admin.models.show', compact('model', 'colors', 'fonts'));
    }

    // ─── EDIT ─────────────────────────────────────────────────────
    public function edit($id)
    {
        $model            = CustomizerModel::findOrFail($id);
        $navigations      = Navigation::where('status', 1)->get() ?? collect();
        $parentCategories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->with('subcategories')
            ->get();
        $fonts        = Font::all();
        $backendColors = Color::all();

        return view('admin.models.edit', compact('model', 'navigations', 'parentCategories', 'fonts', 'backendColors'));
    }

    // ─── UPDATE ───────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $model = CustomizerModel::findOrFail($id);

        $request->validate([
            'title'              => 'required',
            'price'              => 'nullable|numeric|min:0',
            'shipping_cost'      => 'nullable|numeric|min:0',
            'free_shipping_above'=> 'nullable|numeric|min:0',
            'stock_quantity'     => 'nullable|integer|min:0',
            'sizes'              => 'nullable|array',
            'size_chart_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $data = $request->only([
            'model_name', 'title', 'price', 'description',
            'navigation_id', 'category_id', 'subcategory_id',
        ]);

        $data['in_stock']          = $request->input('in_stock') == '1';
        $data['stock_quantity']    = $request->input('stock_quantity') ?: null;
        $data['shipping_enabled']  = $request->input('shipping_enabled') == '1';
        $data['shipping_cost']     = $request->input('shipping_cost') ?? 0;
        $data['free_shipping_above'] = $request->input('free_shipping_above') ?: null;

        $sizes = $request->input('sizes', []);
        $data['sizes'] = is_array($sizes) ? $sizes : [];

        // Size chart
        if ($request->input('remove_size_chart') == '1') {
            if ($model->size_chart_image) {
                Storage::disk('public')->delete($model->size_chart_image);
            }
            $data['size_chart_image'] = null;
        } elseif ($request->hasFile('size_chart_image')) {
            if ($model->size_chart_image) {
                Storage::disk('public')->delete($model->size_chart_image);
            }
            $data['size_chart_image'] = $request->file('size_chart_image')
                ->store('models/sizechart', 'public');
        }

        $views = ['front', 'back', 'left', 'right'];
        $types = ['black', 'white', 'svg'];

        foreach ($views as $view) {
            foreach ($types as $type) {
                $key = "{$view}_{$type}";
                if ($request->hasFile($key)) {
                    $file = $request->file($key);
                    $name = time()."_{$view}_{$type}.".$file->extension();
                    $file->move(public_path('uploads/models'), $name);
                    $data[$key] = $name;
                }
            }
        }

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $name = time().'_thumbnail.'.$file->extension();
            $file->move(public_path('uploads/models'), $name);
            $data['thumbnail'] = $name;
        }

        $colorsData = [];
        try {
            $decoded    = json_decode($request->input('colors_json', '[]'), true);
            $colorsData = is_array($decoded) ? $decoded : [];
        } catch (\Exception $e) {
            $colorsData = $model->colors_data ?? [];
        }

        $oldColorsData = $model->colors_data ?? [];
        $oldImagePaths = [];
        foreach ($oldColorsData as $oc) {
            foreach ($oc['views'] ?? [] as $vData) {
                foreach ($vData as $path) {
                    if ($path) $oldImagePaths[] = $path;
                }
            }
        }

        $colorsMeta = [];
        try {
            $colorsMeta = json_decode($request->input('color_views_meta', '[]'), true) ?? [];
        } catch (\Exception $e) {}

        $newImagePaths = [];
        foreach ($colorsData as $idx => &$color) {
            $meta          = $colorsMeta[$idx] ?? [];
            $color['views'] = [];
            foreach ($views as $view) {
                $color['views'][$view] = [];
                foreach ($types as $type) {
                    $fileKey = "color_{$idx}_{$view}_{$type}";
                    if ($request->hasFile($fileKey)) {
                        $path = $request->file($fileKey)->store('models/colors', 'public');
                        $color['views'][$view][$type] = $path;
                        $newImagePaths[] = $path;
                    } else {
                        $keepPath = $meta[$view][$type] ?? null;
                        $color['views'][$view][$type] = $keepPath;
                        if ($keepPath) $newImagePaths[] = $keepPath;
                    }
                }
            }
        }
        unset($color);

        foreach ($oldImagePaths as $oldPath) {
            if (! in_array($oldPath, $newImagePaths)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $data['colors_data'] = $colorsData;
        $model->update($data);

        return redirect()->route('models.index')->with('success', 'Model Updated Successfully');
    }

    // ─── API: SINGLE MODEL ────────────────────────────────────────
    public function api($id)
    {
        $model = CustomizerModel::findOrFail($id);
        return response()->json($this->modelApiData($model));
    }

    // ─── showApi ─────────────────────────────────────────────────
    public function showApi($id)
    {
        $model = CustomizerModel::findOrFail($id);
        $views = ['front', 'back', 'left', 'right'];
        $types = ['black', 'white', 'svg'];

        $colorsData = collect($model->colors_data ?? [])->map(function ($c) use ($views, $types) {
            $viewsOut = [];
            foreach ($views as $view) {
                $viewsOut[$view] = [];
                foreach ($types as $type) {
                    $path = $c['views'][$view][$type] ?? null;
                    $viewsOut[$view][$type] = $path ? asset('storage/'.$path) : null;
                }
            }
            return ['name' => $c['name'] ?? '', 'hex' => $c['hex'] ?? '#000000', 'views' => $viewsOut];
        })->values()->toArray();

        return response()->json([
            'id'                  => $model->id,
            'name'                => $model->title,
            'title'               => $model->title,
            'description'         => $model->description,
            'price'               => (float) $model->price,
            'type'                => 'model',
            'in_stock'            => (bool) $model->in_stock,
            'category_id'         => $model->category_id,
            'subcategory_id'      => $model->subcategory_id,
            'stock_quantity'      => $model->stock_quantity ? (int) $model->stock_quantity : null,
            'shipping_enabled'    => (bool) $model->shipping_enabled,
            'shipping_cost'       => (float) ($model->shipping_cost ?? 0),
            'free_shipping_above' => $model->free_shipping_above ? (float) $model->free_shipping_above : null,
            'sizes'               => $model->sizes ?? [],
            'size_chart_image'    => $model->size_chart_image ? asset('storage/'.$model->size_chart_image) : null,
            'colors_data'         => $colorsData,
            'thumbnail'           => $model->thumbnail ? asset('uploads/models/'.$model->thumbnail) : null,
            'views' => [
                'front' => [
                    'black' => $model->front_black ? asset('uploads/models/'.$model->front_black) : null,
                    'white' => $model->front_white ? asset('uploads/models/'.$model->front_white) : null,
                    'svg'   => $model->custom_front_svg ? asset('uploads/models/'.$model->custom_front_svg) : ($model->front_svg ? asset('uploads/models/'.$model->front_svg) : null),
                ],
                'back' => [
                    'black' => $model->back_black ? asset('uploads/models/'.$model->back_black) : null,
                    'white' => $model->back_white ? asset('uploads/models/'.$model->back_white) : null,
                    'svg'   => $model->custom_back_svg ? asset('uploads/models/'.$model->custom_back_svg) : ($model->back_svg ? asset('uploads/models/'.$model->back_svg) : null),
                ],
                'left' => [
                    'black' => $model->left_black ? asset('uploads/models/'.$model->left_black) : null,
                    'white' => $model->left_white ? asset('uploads/models/'.$model->left_white) : null,
                    'svg'   => $model->custom_left_svg ? asset('uploads/models/'.$model->custom_left_svg) : ($model->left_svg ? asset('uploads/models/'.$model->left_svg) : null),
                ],
                'right' => [
                    'black' => $model->right_black ? asset('uploads/models/'.$model->right_black) : null,
                    'white' => $model->right_white ? asset('uploads/models/'.$model->right_white) : null,
                    'svg'   => $model->custom_right_svg ? asset('uploads/models/'.$model->custom_right_svg) : ($model->right_svg ? asset('uploads/models/'.$model->right_svg) : null),
                ],
            ],
        ]);
    }

    // ─── DESTROY ──────────────────────────────────────────────────
    public function destroy($id)
    {
        $model = CustomizerModel::findOrFail($id);

        foreach ($model->colors_data ?? [] as $c) {
            foreach ($c['views'] ?? [] as $vData) {
                foreach ($vData as $path) {
                    if ($path) Storage::disk('public')->delete($path);
                }
            }
        }
        if ($model->size_chart_image) {
            Storage::disk('public')->delete($model->size_chart_image);
        }

        $model->delete();

        return back()->with('success', 'Model Deleted');
    }

    // ─── DUPLICATE ────────────────────────────────────────────────
    public function duplicate($id)
    {
        $model      = CustomizerModel::findOrFail($id);
        $new        = $model->replicate();
        $new->title = $model->title.' (Copy)';
        $new->save();

        return back()->with('success', 'Model Duplicated with Design');
    }

    // ─── BULK DELETE ──────────────────────────────────────────────
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('product_ids', []);
        if (empty($ids) || ! is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Koi model select nahi kiya']);
        }
        $count = CustomizerModel::whereIn('id', $ids)->count();
        CustomizerModel::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => $count.' Model(s) deleted']);
    }

    // ─── BULK DUPLICATE ───────────────────────────────────────────
    public function bulkDuplicate(Request $request)
    {
        $ids = $request->input('product_ids', []);
        if (empty($ids) || ! is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Koi model select nahi kiya']);
        }
        $count = 0;
        foreach ($ids as $id) {
            $original = CustomizerModel::find($id);
            if (! $original) continue;
            $copy        = $original->replicate();
            $copy->title = $original->title.' (Copy)';
            $copy->save();
            $count++;
        }

        return response()->json(['success' => true, 'message' => $count.' Model(s) duplicated!']);
    }

    // ─── TOGGLE HIDDEN (single) ───────────────────────────────────
    public function toggleHidden(Request $request, $id)
    {
        $model            = CustomizerModel::findOrFail($id);
        $model->is_hidden = ! $model->is_hidden;
        $model->save();

        return response()->json([
            'success'   => true,
            'is_hidden' => $model->is_hidden,
            'message'   => $model->is_hidden ? 'Model hidden' : 'Model visible',
        ]);
    }

    // ─── BULK TOGGLE HIDDEN ───────────────────────────────────────
    public function bulkToggleHidden(Request $request)
    {
        $ids    = $request->input('product_ids', []);
        $action = $request->input('action', 'hide');

        if (empty($ids) || ! is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Koi model select nahi kiya']);
        }

        $isHidden = $action === 'hide';
        $count    = CustomizerModel::whereIn('id', $ids)->count();
        CustomizerModel::whereIn('id', $ids)->update(['is_hidden' => $isHidden]);

        return response()->json([
            'success' => true,
            'message' => $count.' Model(s) '.($isHidden ? 'hidden' : 'visible').' kar diye',
        ]);
    }

    // ─── SAVE DESIGN ──────────────────────────────────────────────
    public function saveDesign(Request $request, $id)
    {
        $model = CustomizerModel::findOrFail($id);
        foreach ($request->svgs ?? [] as $view => $svgContent) {
            if (! $svgContent) continue;
            $filename = "custom_{$view}_{$id}.svg";
            file_put_contents(public_path('uploads/models/'.$filename), $svgContent);
            $model->{"custom_{$view}_svg"} = $filename;
        }
        $model->pattern_changes = $request->pattern_changes;
        $model->color_changes   = $request->color_changes;
        $model->mascot_changes  = $request->mascot_changes;
        $model->applications    = $request->applications;
        $model->customized_at   = now();
        $model->save();

        return response()->json(['success' => true, 'message' => 'Design saved']);
    }

    // ─── MODELS BY CATEGORY ───────────────────────────────────────
    public function modelsByCategory($id)
    {
        return response()->json([
            'success' => true,
            'models'  => $this->getModelsMapped(
                CustomizerModel::where('category_id', $id)->orderBy('position')->whereNull('subcategory_id')
            ),
        ]);
    }

    public function modelsByEntity($id)
    {
        $isSubcategory = Category::where('id', $id)->whereNotNull('parent_id')->exists();
        $query         = CustomizerModel::query()->orderBy('position');
        $isSubcategory
            ? $query->where('subcategory_id', $id)
            : $query->where('category_id', $id)->whereNull('subcategory_id');

        return response()->json(['success' => true, 'models' => $this->getModelsMapped($query)]);
    }

    public function modelsBySubcategory($id)
    {
        return response()->json([
            'success' => true,
            'models'  => $this->getModelsMapped(
                CustomizerModel::where('subcategory_id', $id)->orderBy('position')
            ),
        ]);
    }

    // ─── SAVE THUMBNAIL ───────────────────────────────────────────
    public function saveThumbnail(Request $request, $id)
    {
        $model = CustomizerModel::findOrFail($id);
        if ($request->hasFile('thumbnail')) {
            $file     = $request->file('thumbnail');
            $filename = "thumbnail_{$id}.png";
            $file->move(public_path('uploads/models'), $filename);
            $model->thumbnail = $filename;
            $model->save();
        }

        return response()->json(['success' => true]);
    }

    // ─── USER CATEGORIES WITH MODELS ─────────────────────────────
    public function userCategoriesWithModels()
    {
        $categories = Category::where('status', 1)
            ->whereHas('models')
            ->with(['models' => fn ($q) => $q])
            ->get();

        return response()->json(['success' => true, 'categories' => $categories]);
    }

    // ─── BULK FEATURED ────────────────────────────────────────────
    public function bulkFeatured(Request $request)
    {
        $ids    = $request->product_ids;
        $action = $request->action;
        if (! $ids || ! is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'No models selected']);
        }
        CustomizerModel::whereIn('id', $ids)->update(['is_featured' => $action === 'add']);

        return response()->json(['success' => true, 'message' => 'Models updated successfully']);
    }

    // ─── BULK APPAREL ─────────────────────────────────────────────
    public function bulkApparel(Request $request)
    {
        $ids    = $request->product_ids;
        $action = $request->action;
        if (! $ids || ! is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'No models selected']);
        }
        CustomizerModel::whereIn('id', $ids)->update(['is_apparel' => $action === 'add']);

        return response()->json(['success' => true, 'message' => 'Models updated successfully']);
    }

    // ─── UPDATE ORDER ─────────────────────────────────────────────
    public function updateOrder(Request $request)
    {
        foreach ($request->order as $item) {
            CustomizerModel::where('model_name', $item['model_name'])
                ->update(['position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }

    // ─── USER API ────────────────────────────────────────────────
    public function userApi($id, $customization_id = null)
    {
        if (! $customization_id) {
            $customization_id = request()->query('customization_id');
        }

        $model      = CustomizerModel::findOrFail($id);
        $userDesign = null;

        if (auth()->check()) {
            $query = \App\Models\UserCustomization::where('user_id', auth()->id())
                ->where('customizer_model_id', $id);
            if ($customization_id) {
                $query->where('id', $customization_id);
            }
            $userDesign = $query->latest()->first();
        }

        $colorChanges   = $userDesign?->color_changes   ?? $model->color_changes   ?? [];
        $patternChanges = $userDesign?->pattern_changes ?? $model->pattern_changes ?? [];
        $mascotChanges  = $userDesign?->mascot_changes  ?? $model->mascot_changes  ?? [];
        $applications   = $userDesign?->applications    ?? $model->applications    ?? [];

        return response()->json([
            'id'              => $model->id,
            'title'           => $model->title,
            'color_changes'   => $colorChanges,
            'pattern_changes' => $patternChanges,
            'mascot_changes'  => $mascotChanges,
            'applications'    => $applications,
            'front_view' => [
                'svg_url'         => $model->front_svg   ? asset('uploads/models/'.$model->front_svg)   : null,
                'white_image_url' => $model->front_white ? asset('uploads/models/'.$model->front_white) : null,
                'black_image_url' => $model->front_black ? asset('uploads/models/'.$model->front_black) : null,
            ],
            'back_view' => [
                'svg_url'         => $model->back_svg   ? asset('uploads/models/'.$model->back_svg)   : null,
                'white_image_url' => $model->back_white ? asset('uploads/models/'.$model->back_white) : null,
                'black_image_url' => $model->back_black ? asset('uploads/models/'.$model->back_black) : null,
            ],
            'left_view' => [
                'svg_url'         => $model->left_svg   ? asset('uploads/models/'.$model->left_svg)   : null,
                'white_image_url' => $model->left_white ? asset('uploads/models/'.$model->left_white) : null,
                'black_image_url' => $model->left_black ? asset('uploads/models/'.$model->left_black) : null,
            ],
            'right_view' => [
                'svg_url'         => $model->right_svg   ? asset('uploads/models/'.$model->right_svg)   : null,
                'white_image_url' => $model->right_white ? asset('uploads/models/'.$model->right_white) : null,
                'black_image_url' => $model->right_black ? asset('uploads/models/'.$model->right_black) : null,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────

    private function modelApiData(CustomizerModel $model): array
    {
        return [
            'id'              => $model->id,
            'title'           => $model->title,
            'description'     => $model->description,
            'price'           => $model->price,
            'category_id'     => $model->category_id,
            'subcategory_id'  => $model->subcategory_id,
            'color_changes'   => $model->color_changes   ?? [],
            'pattern_changes' => $model->pattern_changes ?? [],
            'mascot_changes'  => $model->mascot_changes  ?? [],
            'applications'    => $model->applications    ?? [],
            'front_view' => [
                'svg_url'         => $model->custom_front_svg ? asset('uploads/models/'.$model->custom_front_svg) : ($model->front_svg ? asset('uploads/models/'.$model->front_svg) : null),
                'white_image_url' => $model->front_white ? asset('uploads/models/'.$model->front_white) : null,
                'black_image_url' => $model->front_black ? asset('uploads/models/'.$model->front_black) : null,
            ],
            'back_view' => [
                'svg_url'         => $model->custom_back_svg ? asset('uploads/models/'.$model->custom_back_svg) : ($model->back_svg ? asset('uploads/models/'.$model->back_svg) : null),
                'white_image_url' => $model->back_white ? asset('uploads/models/'.$model->back_white) : null,
                'black_image_url' => $model->back_black ? asset('uploads/models/'.$model->back_black) : null,
            ],
            'left_view' => [
                'svg_url'         => $model->custom_left_svg ? asset('uploads/models/'.$model->custom_left_svg) : ($model->left_svg ? asset('uploads/models/'.$model->left_svg) : null),
                'white_image_url' => $model->left_white ? asset('uploads/models/'.$model->left_white) : null,
                'black_image_url' => $model->left_black ? asset('uploads/models/'.$model->left_black) : null,
            ],
            'right_view' => [
                'svg_url'         => $model->custom_right_svg ? asset('uploads/models/'.$model->custom_right_svg) : ($model->right_svg ? asset('uploads/models/'.$model->right_svg) : null),
                'white_image_url' => $model->right_white ? asset('uploads/models/'.$model->right_white) : null,
                'black_image_url' => $model->right_black ? asset('uploads/models/'.$model->right_black) : null,
            ],
        ];
    }

    private function getModelsMapped($query): array
    {
        return $query->select(
            'id', 'model_name', 'title', 'price', 'description',
            'front_black', 'front_white', 'front_svg',
            'custom_front_svg', 'thumbnail',
            'colors_data', 'is_hidden',   // ← is_hidden added
            'position'
        )
        ->where('is_hidden', false)        // ← hidden models filter out
        ->get()
        ->map(fn ($model) => [
            'id'          => $model->id,
            'model_name'  => $model->model_name,
            'title'       => $model->title,
            'price'       => $model->price ? number_format($model->price, 2) : '0.00',
            'description' => $model->description ?? '',
            'front_black' => $model->front_black ? asset('uploads/models/'.$model->front_black) : null,
            'front_white' => $model->front_white ? asset('uploads/models/'.$model->front_white) : null,
            'front_svg'   => $model->custom_front_svg
                ? asset('uploads/models/'.$model->custom_front_svg)
                : ($model->front_svg ? asset('uploads/models/'.$model->front_svg) : null),
            'thumbnail'   => $model->thumbnail ? asset('uploads/models/'.$model->thumbnail) : null,
            'is_hidden'   => (bool) $model->is_hidden,   // ← returned to frontend
            'colors_data' => collect($model->colors_data ?? [])->map(function ($c) {
                $views = [];
                foreach (['front', 'back', 'left', 'right'] as $view) {
                    $views[$view] = [];
                    foreach (['black', 'white', 'svg'] as $type) {
                        $path = $c['views'][$view][$type] ?? null;
                        $views[$view][$type] = $path ? asset('storage/'.$path) : null;
                    }
                }
                return [
                    'name'  => $c['name'] ?? '',
                    'hex'   => $c['hex']  ?? '#000000',
                    'views' => $views,
                ];
            })->values()->toArray(),
        ])
        ->toArray();
    }
}
