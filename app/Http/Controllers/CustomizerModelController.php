<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\CustomizerModel;
use App\Models\Font;
use App\Models\Navigation;
use Illuminate\Http\Request;

class CustomizerModelController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->with(['models' => function ($q) {
                $q->orderBy('model_name');
            }])
            ->get();

        return view('admin.models.index', compact('categories'));
    }

    // Show create form
    public function create()
    {
        $navigations = Navigation::where('status', 1)->get() ?? collect();

        $parentCategories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->with('subcategories')
            ->get();

        return view('admin.models.create', compact('navigations', 'parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'price' => 'nullable|numeric|min:0',
        ]);

        $data = $request->only([
            'model_name',
            'title',
            'price',
            'description',
            'navigation_id',
            'category_id',
            'subcategory_id',
        ]);
        // Handle file uploads
        $views = ['front', 'back', 'left', 'right'];
        $types = ['black', 'white', 'svg'];

        foreach ($views as $view) {
            foreach ($types as $type) {
                $inputName = $view.'_'.$type;
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $name = time()."_{$view}_{$type}.".$file->extension();
                    $file->move(public_path('uploads/models'), $name);
                    $data[$inputName] = $name;
                }
            }
        }

        // Thumbnail
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $name = time().'_thumbnail.'.$file->extension();
            $file->move(public_path('uploads/models'), $name);
            $data['thumbnail'] = $name;
        }

        CustomizerModel::create($data);

        return redirect()->route('models.index')
            ->with('success', 'Model Added Successfully');
    }

    // Show customizer view - YEH IMPORTANT HAI

    public function show($id)
    {
        $model = CustomizerModel::findOrFail($id);

        // Fetch all colors from DB
        $colors = Color::all();
        $fonts = Font::all()->map(function ($font) {
            return [
                'id' => $font->id,
                'name' => $font->name,
                'file_url' => asset('storage/'.$font->file),
            ];
        });
        // Check if this is an AJAX request
        if (request()->expectsJson()) {
            return response()->json([
                'id' => $model->id,
                'title' => $model->title,
                'description' => $model->description,
                'price' => $model->price,
                'category_id'    => $model->category_id,      // ← YEH ADD KARO
        'subcategory_id' => $model->subcategory_id,
                'views' => [
                    'front' => [
                        'black' => $model->front_black ? asset('uploads/models/'.$model->front_black) : null,
                        'white' => $model->front_white ? asset('uploads/models/'.$model->front_white) : null,
                        'svg' => $model->front_svg ? asset('uploads/models/'.$model->front_svg) : null,
                    ],
                    'back' => [
                        'black' => $model->back_black ? asset('uploads/models/'.$model->back_black) : null,
                        'white' => $model->back_white ? asset('uploads/models/'.$model->back_white) : null,
                        'svg' => $model->back_svg ? asset('uploads/models/'.$model->back_svg) : null,
                    ],
                    'left' => [
                        'black' => $model->left_black ? asset('uploads/models/'.$model->left_black) : null,
                        'white' => $model->left_white ? asset('uploads/models/'.$model->left_white) : null,
                        'svg' => $model->left_svg ? asset('uploads/models/'.$model->left_svg) : null,
                    ],
                    'right' => [
                        'black' => $model->right_black ? asset('uploads/models/'.$model->right_black) : null,
                        'white' => $model->right_white ? asset('uploads/models/'.$model->right_white) : null,
                        'svg' => $model->right_svg ? asset('uploads/models/'.$model->right_svg) : null,
                    ],
                ],
                'thumbnail' => $model->thumbnail ? asset('uploads/models/'.$model->thumbnail) : null,
            ]);
        }

        // Pass $colors to Blade
        return view('admin.models.show', compact('model', 'colors', 'fonts'));
    }

    // Edit form

    public function edit($id)
    {
        $model = CustomizerModel::findOrFail($id);
        $navigations = Navigation::where('status', 1)->get() ?? collect();
        $parentCategories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->with('subcategories')

            ->get();
        $fonts = Font::all();   // 🔥 ADD THIS

        return view('admin.models.edit', compact('model', 'navigations', 'parentCategories', 'fonts'));
    }

    // Update model
    public function update(Request $request, $id)
    {
        $model = CustomizerModel::findOrFail($id);

        // ✅ validate first

        // ✅ model data
        $data = $request->only([
            'model_name',
            'title',
            'price',
            'description',
            'navigation_id',
            'category_id',
            'subcategory_id',
        ]);

        // ✅ images
        $views = ['front', 'back', 'left', 'right'];
        $types = ['black', 'white', 'svg'];

        foreach ($views as $view) {
            foreach ($types as $type) {
                $inputName = $view.'_'.$type;
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    $name = time()."_{$view}_{$type}.".$file->extension();
                    $file->move(public_path('uploads/models'), $name);
                    $data[$inputName] = $name;
                }
            }
        }

        // ✅ thumbnail
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $name = time().'_thumbnail.'.$file->extension();
            $file->move(public_path('uploads/models'), $name);
            $data['thumbnail'] = $name;
        }

        // 🔥 CATEGORY POSITION SAVE

        // ✅ update model
        $model->update($data);

        return redirect()->route('models.index')
            ->with('success', 'Model Updated Successfully');
    }

    public function api($id)
    {
        $model = CustomizerModel::findOrFail($id);

        return response()->json([
            'id' => $model->id,
            'title' => $model->title,

            // 🔥 SEND BACK SAVED STATES
            'color_changes' => $model->color_changes ?? [],
            'pattern_changes' => $model->pattern_changes ?? [],
            'mascot_changes' => $model->mascot_changes ?? [],
            'applications' => $model->applications ?? [],   // 🔥

            'front_view' => [
                'svg_url' => $model->custom_front_svg
                    ? asset('uploads/models/'.$model->custom_front_svg)
                    : ($model->front_svg ? asset('uploads/models/'.$model->front_svg) : null),

                'white_image_url' => $model->front_white ? asset('uploads/models/'.$model->front_white) : null,
                'black_image_url' => $model->front_black ? asset('uploads/models/'.$model->front_black) : null,
            ],

            'back_view' => [
                'svg_url' => $model->custom_back_svg
                    ? asset('uploads/models/'.$model->custom_back_svg)
                    : ($model->back_svg ? asset('uploads/models/'.$model->back_svg) : null),

                'white_image_url' => $model->back_white ? asset('uploads/models/'.$model->back_white) : null,
                'black_image_url' => $model->back_black ? asset('uploads/models/'.$model->back_black) : null,
            ],

            'left_view' => [
                'svg_url' => $model->custom_left_svg
                    ? asset('uploads/models/'.$model->custom_left_svg)
                    : ($model->left_svg ? asset('uploads/models/'.$model->left_svg) : null),

                'white_image_url' => $model->left_white ? asset('uploads/models/'.$model->left_white) : null,
                'black_image_url' => $model->left_black ? asset('uploads/models/'.$model->left_black) : null,
            ],

            'right_view' => [
                'svg_url' => $model->custom_right_svg
                    ? asset('uploads/models/'.$model->custom_right_svg)
                    : ($model->right_svg ? asset('uploads/models/'.$model->right_svg) : null),

                'white_image_url' => $model->right_white ? asset('uploads/models/'.$model->right_white) : null,
                'black_image_url' => $model->right_black ? asset('uploads/models/'.$model->right_black) : null,
            ],
        ]);
    }

    // Delete model
    public function destroy($id)
    {
        $model = CustomizerModel::findOrFail($id);
        $model->delete();

        return back()->with('success', 'Model Deleted');
    }

    // Duplicate model
    public function duplicate($id)
    {
        $model = CustomizerModel::findOrFail($id);
        $new = $model->replicate();
        $new->title = $model->title;

        // 🔥 YEH ADD KARO — custom SVGs clear karo
        $new->custom_front_svg = null;
        $new->custom_back_svg = null;
        $new->custom_left_svg = null;
        $new->custom_right_svg = null;

        // 🔥 YEH BHI CLEAR KARO
        $new->color_changes = null;
        $new->pattern_changes = null;
        $new->mascot_changes = null;
        $new->applications = null;
        $new->customized_at = null;

        $new->save();

        return back()->with('success', 'Model Duplicated');
    }

    public function saveDesign(Request $request, $id)
    {
        $model = CustomizerModel::findOrFail($id);

        $svgs = $request->svgs ?? [];

        foreach ($svgs as $view => $svgContent) {

            if (! $svgContent) {
                continue;
            }

            // 🔥 ALWAYS SAME NAME (overwrite)
            $filename = "custom_{$view}_{$id}.svg";

            file_put_contents(
                public_path('uploads/models/'.$filename),
                $svgContent
            );

            $model->{"custom_{$view}_svg"} = $filename;
        }

        $model->pattern_changes = $request->pattern_changes;
        $model->color_changes = $request->color_changes;
        $model->mascot_changes = $request->mascot_changes;
        $model->applications = $request->applications;
        $model->customized_at = now();

        $model->save();

        return response()->json([
            'success' => true,
            'message' => 'Design saved',
        ]);
    }

    public function modelsByCategory($id)
    {
        $models = CustomizerModel::where('category_id', $id)
            ->whereNull('subcategory_id')
            // ->select('id', 'title', 'price', 'description', 'front_black', 'front_white', 'front_svg', 'thumbnail')
            ->select(
                'id',
                'model_name',
                'title',
                'price',
                'description',
                'front_black',
                'front_white',
                'front_svg',
                'custom_front_svg',   // ← add this
                'thumbnail'
            )

            ->get()
            ->map(function ($model) {
                return [
                    'id' => $model->id,
                    'model_name' => $model->model_name,   // 👈 ADD THIS

                    'title' => $model->title,
                    'price' => $model->price ? number_format($model->price, 2) : '0.00',  // ← YEHI CHANGE
                    'description' => $model->description ?? '',  // ← YEHI ADD
                    // Full asset URLs (same as subcategory)
                    'front_black' => $model->front_black ? asset('uploads/models/'.$model->front_black) : null,
                    'front_white' => $model->front_white ? asset('uploads/models/'.$model->front_white) : null,
                    'front_svg' => $model->custom_front_svg
                        ? asset('uploads/models/'.$model->custom_front_svg)
                        : ($model->front_svg ? asset('uploads/models/'.$model->front_svg) : null),
                    'thumbnail' => $model->thumbnail ? asset('uploads/models/'.$model->thumbnail) : null,

                ];
            });

        return response()->json([
            'success' => true,
            'models' => $models,
        ]);
    }

    // CustomizerModelController.php
    public function modelsByEntity($id)
    {
        $isSubcategory = Category::where('id', $id)
            ->whereNotNull('parent_id')
            ->exists();

        $models = CustomizerModel::query()
            ->when($isSubcategory, fn ($q) => $q->where('subcategory_id', $id))
            ->when(! $isSubcategory, fn ($q) => $q->where('category_id', $id)->whereNull('subcategory_id'))
            // ->select('id', 'title', 'price', 'description', 'front_black', 'front_white', 'front_svg', 'thumbnail')
            ->select(
                'id',
                'model_name',
                'title',
                'price',
                'description',
                'front_black',
                'front_white',
                'front_svg',
                'custom_front_svg',   // ← add this
                'thumbnail'
            )

            ->get()
            ->map(function ($model) {
                return [
                    'id' => $model->id,
                    'model_name' => $model->model_name,   // 👈 ADD THIS

                    'title' => $model->title,
                    'price' => $model->price ? number_format($model->price, 2) : '0.00',
                    'description' => $model->description ?? '',
                    'front_black' => $model->front_black ? asset('uploads/models/'.$model->front_black) : null,
                    'front_white' => $model->front_white ? asset('uploads/models/'.$model->front_white) : null,
                    'front_svg' => $model->custom_front_svg
                        ? asset('uploads/models/'.$model->custom_front_svg)
                        : ($model->front_svg ? asset('uploads/models/'.$model->front_svg) : null),
                    'thumbnail' => $model->thumbnail ? asset('uploads/models/'.$model->thumbnail) : null,
                ];
            });

        return response()->json([
            'success' => true,
            'models' => $models,
        ]);
    }

    public function modelsBySubcategory($id)
    {
        $models = CustomizerModel::where('subcategory_id', $id)
            // ->select('id', 'title', 'price', 'description', 'front_black', 'front_white', 'front_svg', 'thumbnail')
            ->select(
                'id',
                'model_name',
                'title',
                'price',
                'description',
                'front_black',
                'front_white',
                'front_svg',
                'custom_front_svg',   // ← add this
                'thumbnail'
            )

            ->get()
            ->map(function ($model) {
                return [
                    'id' => $model->id,
                    'model_name' => $model->model_name,   // 👈 ADD THIS
                    'title' => $model->title,
                    'price' => $model->price ? number_format($model->price, 2) : '0.00',
                    'description' => $model->description,
                    // Full asset URLs bhejo
                    'front_black' => $model->front_black ? asset('uploads/models/'.$model->front_black) : null,
                    'front_white' => $model->front_white ? asset('uploads/models/'.$model->front_white) : null,
                    'front_svg' => $model->custom_front_svg
                        ? asset('uploads/models/'.$model->custom_front_svg)
                        : ($model->front_svg ? asset('uploads/models/'.$model->front_svg) : null),
                    'thumbnail' => $model->thumbnail ? asset('uploads/models/'.$model->thumbnail) : null,
                ];
            });

        return response()->json([
            'success' => true,
            'models' => $models,
        ]);
    }

    public function saveThumbnail(Request $request, $id)
    {
        $model = CustomizerModel::findOrFail($id);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = "thumbnail_{$id}.png";
            $file->move(public_path('uploads/models'), $filename);
            $model->thumbnail = $filename;
            $model->save();
        }

        return response()->json(['success' => true]);
    }

    public function userCategoriesWithModels()
    {
        $categories = Category::where('status', 1)
            ->whereHas('models') // 🔥 sirf jisme models hain
            ->with(['models' => function ($q) {}])
            ->get();

        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
    }

    public function bulkFeatured(Request $request)
    {
        $ids = $request->product_ids;
        $action = $request->action;

        if (! $ids || ! is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'No models selected']);
        }

        $value = $action === 'add';

        CustomizerModel::whereIn('id', $ids)->update([
            'is_featured' => $value,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Models updated successfully',
        ]);
    }

    public function bulkApparel(Request $request)
    {
        $ids = $request->product_ids;
        $action = $request->action;

        if (! $ids || ! is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'No models selected']);
        }

        $value = $action === 'add';

        CustomizerModel::whereIn('id', $ids)->update([
            'is_apparel' => $value,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Models updated successfully',
        ]);
    }
}
