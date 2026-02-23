<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomizerModel;
use App\Models\Color;
use Illuminate\Http\Request;

class CustomizerModelController extends Controller
{
    // ─── List all models (user-facing) ───
    public function index()
    {
        $models = CustomizerModel::latest()->paginate(12);
        return view('frontend.models.index', compact('models'));
    }

    // ─── Show single model customizer page (user-facing) ───
public function show($id)
{
    $model = \App\Models\CustomizerModel::findOrFail($id);
    $colors = \App\Models\Color::all();

    return view('frontend.models.show', compact('model', 'colors'));
}

    // ─── Public API — returns model JSON (same as admin api() method) ───
    public function api($id)
    {
        $model = CustomizerModel::findOrFail($id);

        return response()->json([
            'id'    => $model->id,
            'title' => $model->title,
            'color_changes' => $model->color_changes ?? null,

            'front_view' => [
                    'svg' => $model->front_svg ? asset('uploads/models/' . $model->front_svg) : null,
                'white_image_url' => $model->front_white ? asset('uploads/models/' . $model->front_white) : null,
                'black_image_url' => $model->front_black ? asset('uploads/models/' . $model->front_black) : null,
            ],

            'back_view' => [
                'svg_url'         => $model->back_svg   ? asset('uploads/models/' . $model->back_svg)   : null,
                'white_image_url' => $model->back_white ? asset('uploads/models/' . $model->back_white) : null,
                'black_image_url' => $model->back_black ? asset('uploads/models/' . $model->back_black) : null,
            ],

            'left_view' => [
                'svg_url'         => $model->left_svg   ? asset('uploads/models/' . $model->left_svg)   : null,
                'white_image_url' => $model->left_white ? asset('uploads/models/' . $model->left_white) : null,
                'black_image_url' => $model->left_black ? asset('uploads/models/' . $model->left_black) : null,
            ],

            'right_view' => [
                'svg_url'         => $model->right_svg   ? asset('uploads/models/' . $model->right_svg)   : null,
                'white_image_url' => $model->right_white ? asset('uploads/models/' . $model->right_white) : null,
                'black_image_url' => $model->right_black ? asset('uploads/models/' . $model->right_black) : null,
            ],
        ]);
    }

    // ─── Save design (user-facing, same logic as admin) ───
    public function saveDesign(Request $request, $id)
    {
        $model = CustomizerModel::findOrFail($id);

        $request->validate([
            'color_changes' => 'required|json',
        ]);

        $colorChanges = json_decode($request->color_changes, true);

        $model->update([
            'color_changes'  => $colorChanges,
            'customized_at'  => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Design saved successfully!'
        ]);
    }
}