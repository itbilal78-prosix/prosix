<?php

namespace App\Http\Controllers;

use App\Models\UserCustomization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserCustomizationController extends Controller
{
    /**
     * Save as new OR replace currently opened design.
     */
    public function store(Request $request, $id)
    {
        $user = auth('sanctum')->user() ?? auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first.',
            ], 401);
        }

        try {
            $validated = $request->validate([
                'save_type'       => 'required|in:new,replace',
                'design_id'       => 'nullable|integer',
                'name'            => 'nullable|string|max:255',
                'color_changes'   => 'nullable|array',
                'pattern_changes' => 'nullable|array',
                'mascot_changes'  => 'nullable|array',
                'applications'    => 'nullable|array',
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid design data.',
                'errors'  => $exception->errors(),
            ], 422);
        }

        $saveType = $validated['save_type'];

        Log::info('Saving user customization', [
            'user_id'   => $user->id,
            'model_id'  => $id,
            'design_id' => $validated['design_id'] ?? null,
            'save_type' => $saveType,
        ]);

        /*
        |--------------------------------------------------------------------------
        | REPLACE CURRENTLY OPENED DESIGN
        |--------------------------------------------------------------------------
        */
        if ($saveType === 'replace') {
            if (empty($validated['design_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Design ID is required for replacing a design.',
                ], 422);
            }

            /*
             * Important:
             * - Design user ka apna ho
             * - Design isi model ka ho
             * - Sirf currently opened design update ho
             */
            $customization = UserCustomization::query()
                ->where('id', $validated['design_id'])
                ->where('user_id', $user->id)
                ->where('customizer_model_id', $id)
                ->first();

            if (!$customization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Design not found or you cannot replace this design.',
                ], 404);
            }

            $customization->update([
                // Replace par purana name rakho.
                // Frontend name bheje to new name use ho jayega.
                'name' => !empty($validated['name'])
                    ? $validated['name']
                    : $customization->name,

                'color_changes'   => $validated['color_changes'] ?? [],
                'pattern_changes' => $validated['pattern_changes'] ?? [],
                'mascot_changes'  => $validated['mascot_changes'] ?? [],
                'applications'    => $validated['applications'] ?? [],
            ]);

            return response()->json([
                'success'          => true,
                'save_type'        => 'replace',
                'customization_id' => $customization->id,
                'message'          => 'Previous design replaced successfully.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE AS NEW DESIGN
        |--------------------------------------------------------------------------
        */
        $designName = trim($validated['name'] ?? '');

        if ($designName === '') {
            return response()->json([
                'success' => false,
                'message' => 'Design name is required.',
            ], 422);
        }

        $customization = UserCustomization::create([
            'user_id'             => $user->id,
            'customizer_model_id' => $id,
            'name'                => $designName,
            'color_changes'       => $validated['color_changes'] ?? [],
            'pattern_changes'     => $validated['pattern_changes'] ?? [],
            'mascot_changes'      => $validated['mascot_changes'] ?? [],
            'applications'        => $validated['applications'] ?? [],
        ]);

        return response()->json([
            'success'          => true,
            'save_type'        => 'new',
            'customization_id' => $customization->id,
            'message'          => 'New design saved successfully.',
        ], 201);
    }

    /**
     * Save thumbnail against exact customization ID.
     */
    public function saveThumbnail(Request $request, $id)
    {
        $user = auth('sanctum')->user() ?? auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $request->validate([
            'thumbnail'       => 'required|image|mimes:png,jpg,jpeg,webp|max:10240',
            'customization_id' => 'required|integer',
        ]);

        /*
         * Latest design use nahi karna.
         * Exact customization_id ko update karna hai.
         */
        $customization = UserCustomization::query()
            ->where('id', $request->customization_id)
            ->where('user_id', $user->id)
            ->where('customizer_model_id', $id)
            ->first();

        if (!$customization) {
            return response()->json([
                'success' => false,
                'message' => 'Design not found.',
            ], 404);
        }

        $directory = public_path('uploads/user-designs');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        /*
         * Purana thumbnail replace karte waqt delete kar dein.
         */
        if ($customization->thumbnail) {
            $oldThumbnail = $directory . DIRECTORY_SEPARATOR . $customization->thumbnail;

            if (File::exists($oldThumbnail)) {
                File::delete($oldThumbnail);
            }
        }

        $file = $request->file('thumbnail');

        $filename = 'user_thumb_' .
            $user->id . '_' .
            $customization->id . '_' .
            time() . '.' .
            $file->getClientOriginalExtension();

        $file->move($directory, $filename);

        $customization->update([
            'thumbnail' => $filename,
        ]);

        return response()->json([
            'success'          => true,
            'customization_id' => $customization->id,
            'thumbnail'        => asset('uploads/user-designs/' . $filename),
            'message'          => 'Thumbnail saved successfully.',
        ]);
    }

    /**
     * Return logged-in user's designs.
     */
    public function designs()
    {
        $user = auth('sanctum')->user() ?? auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $designs = UserCustomization::query()
            ->where('user_id', $user->id)
            ->with('model:id,title,front_svg,front_white,front_black')
            ->latest('updated_at')
            ->get()
            ->map(function ($design) {
                return [
                    'id'         => $design->id,
                    'name'       => $design->name,
                    'created_at' => $design->created_at,
                    'updated_at' => $design->updated_at,

                    'thumbnail' => $design->thumbnail
                        ? asset('uploads/user-designs/' . $design->thumbnail)
                        : null,

                    'model' => [
                        'id'    => $design->model?->id,
                        'title' => $design->model?->title,
                    ],
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $designs,
        ]);
    }

    /**
     * Delete design.
     */
    public function destroy($id)
    {
        $user = auth('sanctum')->user() ?? auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $design = UserCustomization::query()
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$design) {
            return response()->json([
                'success' => false,
                'message' => 'Design not found.',
            ], 404);
        }

        if ($design->thumbnail) {
            $thumbnailPath = public_path(
                'uploads/user-designs/' . $design->thumbnail
            );

            if (File::exists($thumbnailPath)) {
                File::delete($thumbnailPath);
            }
        }

        $design->delete();

        return response()->json([
            'success' => true,
            'message' => 'Design deleted successfully.',
        ]);
    }
}
