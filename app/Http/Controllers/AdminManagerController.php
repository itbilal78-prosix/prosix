<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminManagerController extends Controller
{
    // Sabhi admins list
    public function index()
    {
        $admins = Admin::orderBy('created_at', 'desc')->get();

        return view('admin.admins.index', compact('admins'));
    }

    // Create form
    public function create()
    {
        return view('admin.admins.create');
    }

    // Save new admin
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_super_admin' => false,
            'can_products' => $request->has('can_products'),
            'can_categories' => $request->has('can_categories'),
            'can_customizer' => $request->has('can_customizer'),
            'can_orders' => $request->has('can_orders'),
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin created successfully!');
    }

    // Edit form
    public function edit($id)
    {
        $adminUser = Admin::findOrFail($id);

        return view('admin.admins.edit', compact('adminUser'));
    }

    // Update permissions
    public function update(Request $request, $id)
    {
        $adminUser = Admin::findOrFail($id);

        // 🔥 Super Admin ki permissions change mat karo
        if ($adminUser->is_super_admin) {
            return redirect()->route('admin.admins.index')
                ->with('success', 'Super Admin permissions cannot be modified.');
        }

        $data = [
            'can_products' => $request->has('can_products'),
            'can_categories' => $request->has('can_categories'),
            'can_customizer' => $request->has('can_customizer'),
            'can_orders' => $request->has('can_orders'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $adminUser->update($data);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Permissions updated successfully!');
    }

    // Delete admin
    public function destroy($id)
    {
        $adminUser = Admin::findOrFail($id);

        // Super admin ko delete mat karo
        if ($adminUser->is_super_admin) {
            return back()->with('error', 'Super Admin cannot be deleted!');
        }

        $adminUser->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin deleted!');
    }
}
