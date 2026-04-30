<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\Category;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    // index: categories ke saath templates load karo
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->with(['templates' => function ($q) {
                $q->latest();
            }])
            ->get();

        $uncategorized = Template::whereNull('category_id')->latest()->get();

        return view('templates.index', compact('categories', 'uncategorized'));
    }

    public function create()
    {
        return view('templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'svg_data'    => 'nullable|string',
            'image_data'  => 'nullable|string',
        ]);

        Template::create($request->all());

        return redirect()->route('templates.index')->with('success', 'Template created!');
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

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $template->title = $request->title;
        $template->save();

        return redirect()->route('templates.index')->with('success', 'Template updated!');
    }

    public function destroy(Template $template)
    {
        $template->delete();
        return redirect()->route('templates.index')->with('success', 'Template deleted!');
    }

    // ─── Bulk Delete ───────────────────────────────────────────────────────────
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:templates,id',
        ]);

        $count = Template::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} template(s) deleted successfully!",
        ]);
    }

    // Mascot customizer se save hoga — category_id bhi aayega ab
    public function saveFromCustomizer(Request $request)
    {
        $request->validate([
            'title'       => 'required|string',
            'svg_data'    => 'required|string',
            'image_data'  => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $template = Template::create([
            'title'       => $request->title,
            'svg_data'    => $request->svg_data,
            'image_data'  => $request->image_data ?? null,
            'source'      => 'customizer',
            'category_id' => $request->category_id ?? null,
        ]);

        return response()->json([
            'success' => true,
            'id'      => $template->id,
            'message' => 'Template saved successfully',
        ]);
    }

    public function apiList()
    {
        return response()->json(Template::latest()->get());
    }
}
