<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\Category;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->with([
                'templates' => function ($query) {
                    $query->latest();
                }
            ])
            ->get();

        $uncategorized = Template::whereNull('category_id')
            ->latest()
            ->get();

        return view('templates.index', compact(
            'categories',
            'uncategorized'
        ));
    }

    public function create()
    {
        return view('templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            'svg_data' => [
                'nullable',
                'string',
            ],

            'image_data' => [
                'nullable',
                'string',
            ],

            'source' => [
                'nullable',
                'string',
                'max:100',
            ],

            'box_index' => [
                'nullable',
                'integer',
            ],

            // Mascot colors
            'color_count' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],

            'selected_colors' => [
                'nullable',
                'array',
            ],

            'selected_colors.*' => [
                'nullable',
                'string',
                'max:50',
            ],

            'color_mappings' => [
                'nullable',
                'array',
            ],
        ]);

        $template = Template::create([
            'title' => $validated['title'],

            'description' =>
                $validated['description'] ?? null,

            'category_id' =>
                $validated['category_id'] ?? null,

            'svg_data' =>
                $validated['svg_data'] ?? null,

            'image_data' =>
                $validated['image_data'] ?? null,

            'source' =>
                $validated['source'] ?? null,

            'box_index' =>
                $validated['box_index'] ?? null,

            'color_count' =>
                $validated['color_count'] ?? null,

            'selected_colors' =>
                $validated['selected_colors'] ?? [],

            'color_mappings' =>
                $validated['color_mappings'] ?? [],
        ]);

        ActivityLogger::log(
            'created',
            'Template',
            $template->title,
            $template->id,
            [
                'title' => $template->title,
                'category_id' => $template->category_id,
                'color_count' => $template->color_count,
                'selected_colors' => $template->selected_colors,
            ]
        );

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template created!');
    }

    public function show(Template $template)
    {
        return view('templates.show', compact('template'));
    }

    public function edit(Template $template)
    {
        return view('templates.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = Template::findOrFail($id);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            'svg_data' => [
                'nullable',
                'string',
            ],

            'image_data' => [
                'nullable',
                'string',
            ],

            'source' => [
                'nullable',
                'string',
                'max:100',
            ],

            'box_index' => [
                'nullable',
                'integer',
            ],

            'color_count' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],

            'selected_colors' => [
                'nullable',
                'array',
            ],

            'selected_colors.*' => [
                'nullable',
                'string',
                'max:50',
            ],

            'color_mappings' => [
                'nullable',
                'array',
            ],
        ]);

        $oldData = [
            'title' => $template->title,
            'category_id' => $template->category_id,
            'color_count' => $template->color_count,
            'selected_colors' => $template->selected_colors,
        ];

        $template->update([
            'title' => $validated['title'],

            'description' =>
                array_key_exists('description', $validated)
                    ? $validated['description']
                    : $template->description,

            'category_id' =>
                array_key_exists('category_id', $validated)
                    ? $validated['category_id']
                    : $template->category_id,

            'svg_data' =>
                array_key_exists('svg_data', $validated)
                    ? $validated['svg_data']
                    : $template->svg_data,

            'image_data' =>
                array_key_exists('image_data', $validated)
                    ? $validated['image_data']
                    : $template->image_data,

            'source' =>
                array_key_exists('source', $validated)
                    ? $validated['source']
                    : $template->source,

            'box_index' =>
                array_key_exists('box_index', $validated)
                    ? $validated['box_index']
                    : $template->box_index,

            'color_count' =>
                array_key_exists('color_count', $validated)
                    ? $validated['color_count']
                    : $template->color_count,

            'selected_colors' =>
                array_key_exists('selected_colors', $validated)
                    ? $validated['selected_colors']
                    : $template->selected_colors,

            'color_mappings' =>
                array_key_exists('color_mappings', $validated)
                    ? $validated['color_mappings']
                    : $template->color_mappings,
        ]);

        ActivityLogger::log(
            'updated',
            'Template',
            $template->title,
            $template->id,
            [
                'old' => $oldData,

                'new' => [
                    'title' => $template->title,
                    'category_id' => $template->category_id,
                    'color_count' => $template->color_count,
                    'selected_colors' => $template->selected_colors,
                ],
            ]
        );

        /*
         * Customizer AJAX update ho to JSON return karo.
         */
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'id' => $template->id,
                'message' => 'Template updated successfully',
                'template' => $template,
            ]);
        }

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template updated!');
    }

    public function destroy(Template $template)
    {
        ActivityLogger::log(
            'deleted',
            'Template',
            $template->title,
            $template->id,
            [
                'title' => $template->title,
                'category_id' => $template->category_id,
                'color_count' => $template->color_count,
            ]
        );

        $template->delete();

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template deleted!');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
            ],

            'ids.*' => [
                'integer',
                'exists:templates,id',
            ],
        ]);

        $count = Template::whereIn(
            'id',
            $validated['ids']
        )->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} template(s) deleted successfully!",
        ]);
    }

    public function saveFromCustomizer(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'svg_data' => [
                'required',
                'string',
            ],

            'image_data' => [
                'nullable',
                'string',
            ],

            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            // Mascot color information
            'color_count' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],

            'selected_colors' => [
                'nullable',
                'array',
            ],

            'selected_colors.*' => [
                'nullable',
                'string',
                'max:50',
            ],

            'color_mappings' => [
                'nullable',
                'array',
            ],
        ]);

        $template = Template::create([
            'title' => $validated['title'],

            'svg_data' => $validated['svg_data'],

            'image_data' =>
                $validated['image_data'] ?? null,

            'source' => 'customizer',

            'category_id' =>
                $validated['category_id'] ?? null,

            'color_count' =>
                $validated['color_count'] ?? null,

            'selected_colors' =>
                $validated['selected_colors'] ?? [],

            'color_mappings' =>
                $validated['color_mappings'] ?? [],
        ]);

        ActivityLogger::log(
            'created',
            'Template',
            $template->title,
            $template->id,
            [
                'title' => $template->title,
                'source' => 'customizer',
                'category_id' => $template->category_id,
                'color_count' => $template->color_count,
                'selected_colors' => $template->selected_colors,
            ]
        );

        return response()->json([
            'success' => true,
            'id' => $template->id,
            'message' => 'Template saved successfully',
            'template' => $template,
        ]);
    }

    public function apiList()
    {
        $templates = Template::query()
            ->with('category:id,name')
            ->latest()
            ->get();

        return response()->json(
            $templates->map(function ($template) {
                return [
                    'id' => $template->id,
                    'title' => $template->title,

                    'category_id' =>
                        $template->category_id,

                    'category' =>
                        $template->category?->name,

                    'svg_data' =>
                        $template->svg_data,

                    'image_data' =>
                        $template->image_data,

                    'source' =>
                        $template->source,

                    // Exact saved mascot colors
                    'color_count' =>
                        $template->color_count,

                    'selected_colors' =>
                        $template->selected_colors ?? [],

                    'color_mappings' =>
                        $template->color_mappings ?? [],
                ];
            })
        );
    }
}
