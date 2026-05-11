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
            ->with(['templates' => function ($q) { $q->latest(); }])
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

        $template = Template::create($request->all());

        // ✅ Activity Log
        ActivityLogger::log('created', 'Template', $template->title, $template->id, [
            'title'       => $template->title,
            'category_id' => $template->category_id,
        ]);

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

        // ✅ Activity Log
        ActivityLogger::log('updated', 'Template', $template->title, $template->id, [
            'title' => $request->title,
        ]);

        return redirect()->route('templates.index')->with('success', 'Template updated!');
    }

    public function destroy(Template $template)
    {
        // ✅ Activity Log
        ActivityLogger::log('deleted', 'Template', $template->title, $template->id, [
            'title'       => $template->title,
            'category_id' => $template->category_id,
        ]);

        $template->delete();
        return redirect()->route('templates.index')->with('success', 'Template deleted!');
    }

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

        // ✅ Activity Log
        ActivityLogger::log('created', 'Template', $template->title, $template->id, [
            'title'       => $template->title,
            'source'      => 'customizer',
            'category_id' => $template->category_id,
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
