<?php

namespace App\Http\Controllers;

use App\Models\SocialMedia;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    public function index()
    {
        $socials = SocialMedia::all();
        return view('admin.social.index', compact('socials'));
    }

    public function create()
    {
        return view('admin.social.create');
    }

  public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:50',
        'logo' => 'required|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
        'link' => 'required|url',
    ]);

    $logoPath = null;

    if($request->hasFile('logo')){
        $image = $request->file('logo');
        $name = time().'_'.$image->getClientOriginalName();
        $image->move(public_path('uploads/socials'), $name);
        $logoPath = 'uploads/socials/'.$name;
    }

    SocialMedia::create([
        'name' => $request->name,
        'logo' => $logoPath,
        'link' => $request->link,
    ]);

    return redirect()->route('socials.index')->with('success', 'Social Media added successfully.');
}


    public function edit(SocialMedia $social)
    {
        return view('admin.social.edit', compact('social'));
    }

    public function update(Request $request, SocialMedia $social)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'logo' => 'required|string|max:100',
            'link' => 'required|url',
        ]);

        $social->update($request->all());
        return redirect()->route('socials.index')->with('success', 'Social Media updated.');
    }

    public function destroy(SocialMedia $social)
    {
        $social->delete();
        return redirect()->route('socials.index')->with('success', 'Social Media deleted.');
    }
    public function apiIndex() {
    $socials = SocialMedia::all(); // id, name, logo, link
    return response()->json($socials);
}
}
