<?php

namespace App\Http\Controllers;

use App\Models\UserCustomization;
use Illuminate\Http\Request;

class UserCustomizationController extends Controller
{
    // ❌ 'auth' middleware hata do — API calls sanctum use karti hain
    // Controller mein middleware mat lagao, routes pe lagao

 public function store(Request $request, $id)
{
    // ✅ Sanctum aur session dono check karo
    $user = auth('sanctum')->user() ?? auth()->user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Please login first'
        ], 401);
    }

    // ✅ DEBUG
    \Log::info('Saving user design', [
        'user_id'       => $user->id,
        'model_id'      => $id,
        'color_changes' => $request->color_changes,
        'name'          => $request->name,
    ]);

    // ✅ Update karo agar exist kare, warna create karo
if ($request->design_id) {

    $customization = UserCustomization::where('id',$request->design_id)
        ->where('user_id',$user->id)
        ->first();

    if ($customization) {

        $customization->update([
            'color_changes'=>$request->color_changes ?? [],
            'pattern_changes'=>$request->pattern_changes ?? [],
            'mascot_changes'=>$request->mascot_changes ?? [],
            'applications'=>$request->applications ?? [],
        ]);

    }

} else {

    $customization = UserCustomization::create([
        'user_id'=>$user->id,
        'customizer_model_id'=>$id,
        'name'=>$request->name,
        'color_changes'=>$request->color_changes ?? [],
        'pattern_changes'=>$request->pattern_changes ?? [],
        'mascot_changes'=>$request->mascot_changes ?? [],
        'applications'=>$request->applications ?? [],
    ]);

}

    return response()->json([
        'success'          => true,
        'customization_id' => $customization->id,
        'message'          => 'Design saved!'
    ]);
}

    public function saveThumbnail(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        if ($request->hasFile('thumbnail')) {
            $file     = $request->file('thumbnail');
            $filename = "user_thumb_{$user->id}_{$id}_" . time() . ".png";

            // Folder banao agar exist nahi karta
            $dir = public_path('uploads/user-designs');
            if (!file_exists($dir)) mkdir($dir, 0755, true);

            $file->move($dir, $filename);

            // Latest customization update karo
            $customization = UserCustomization::where('user_id', $user->id)
                ->where('customizer_model_id', $id)
                ->latest()
                ->first();

            if ($customization) {
                $customization->update(['thumbnail' => $filename]);
            }
        }

        return response()->json(['success' => true]);
    }

   public function designs()
{
    $user = auth('sanctum')->user();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
    }

    $designs = UserCustomization::where('user_id', $user->id)
        ->with('model:id,title,front_svg,front_white,front_black')
        ->latest()
        ->get()
        ->map(function ($d) {
            return [
                'id'         => $d->id,
                'name'       => $d->name,
                'created_at' => $d->created_at,

                // ✅ User ka apna thumbnail — admin ka nahi
                'thumbnail' => $d->thumbnail
                    ? asset('uploads/user-designs/' . $d->thumbnail)
                    : null,

                'model' => [
                    'id'    => $d->model?->id,
                    'title' => $d->model?->title,
                ],
            ];
        });

    return response()->json(['success' => true, 'data' => $designs]);
}



public function destroy($id)
{
    $user = auth('sanctum')->user();
    $design = \App\Models\UserCustomization::where('id', $id)
                ->where('user_id', $user->id)  // ✅ ownership check
                ->first();

    if (!$design) {
        return response()->json(['message' => 'Design not found'], 404);
    }

    if ($design->thumbnail) {
        $path = public_path('uploads/user-designs/' . $design->thumbnail); // ✅ sahi folder
        if (file_exists($path)) unlink($path);
    }

    $design->delete();

    return response()->json(['message' => 'Deleted successfully']);
}



}
