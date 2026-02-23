<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    // Display all templates
    public function index()
    {
        $templates = Template::latest()->get();
        return view('templates.index', compact('templates'));
    }

    // Show create form
    public function create()
    {
        return view('templates.create');
    }

    // Store new template
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'svg_data' => 'nullable|string',
            'image_data' => 'nullable|string',
            'description' => 'nullable|string',
            'source' => 'nullable|string',
            'box_index' => 'nullable|integer'
        ]);

        Template::create($request->all());

        return redirect()->route('templates.index')
            ->with('success', 'Template created successfully!');
    }

    // Show single template
    public function show(Template $template)
    {
        return view('templates.show', compact('template'));
    }

    // Show edit form
    public function edit(Template $template)
    {
        return view('templates.edit', compact('template'));
    }

    // Update template
 public function update(Request $request, $id)
{
    $template = Template::findOrFail($id);

    $template->title = $request->title;
    $template->svg_data = $request->svg_data;
    $template->image_data = $request->image_data;

    $template->save();

    return response()->json(['success'=>true]);
}


    // Delete template
    public function destroy(Template $template)
    {
        $template->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Template deleted successfully!');
    }

    // API endpoint for saving from mascot customizer
    public function saveFromCustomizer(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'svg_data' => 'required|string',
            'image_data' => 'required|string',
        ]);

        $template = Template::create([
            'title' => $request->title,
            'svg_data' => $request->svg_data,
            'image_data' => $request->image_data,
            'source' => 'mascot-customizer',
            'box_index' => $request->box_index ?? 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Template saved successfully!',
            'template' => $template
        ]);
    }
}
