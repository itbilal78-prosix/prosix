<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCustomization extends Model
{
    protected $fillable = [
        'user_id',
        'customizer_model_id',
        'name',
        'color_changes',
        'pattern_changes',
        'mascot_changes',
        'applications',
        'thumbnail',
        'notes',
        'is_public',
    ];

   protected $casts = [
    'color_changes'   => 'array',
    'pattern_changes' => 'array',
    'mascot_changes'  => 'array',
    'applications'    => 'array',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->belongsTo(
            \App\Models\CustomizerModel::class,
            'customizer_model_id'
        );
    }
public function customize($id, Request $request)
{
    $model = CustomizerModel::findOrFail($id);

    $design = null;

    if ($request->design_id) {
        $design = UserCustomization::find($request->design_id);
    }

    return view('admin.models.show', [
        'model' => $model,
        'design' => $design,
        'isUserMode' => true
    ]);
}
}
