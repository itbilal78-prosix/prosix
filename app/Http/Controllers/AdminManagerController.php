<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminManagerController extends Controller
{
    public function index()
{
    // Sirf super admin dekh sakta hai
    $currentAdmin = auth('admin')->user();
    if (!$currentAdmin->is_super_admin) {
        abort(403, 'Access denied.');
    }

    $admins = Admin::orderBy('created_at', 'desc')->get();
    return view('admin.admins.index', compact('admins'));
}

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
        ]);

        $admin = Admin::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'is_super_admin' => false,
            'can_products'   => $request->has('can_products'),
            'can_categories' => $request->has('can_categories'),
            'can_customizer' => $request->has('can_customizer'),
            'can_orders'     => $request->has('can_orders'),
        ]);

        // ✅ LOG
        ActivityLogger::log(
            action: 'created',
            module: 'Admin',
            targetName: $admin->name,
            targetId: $admin->id,
            changes: [
                'email'          => $admin->email,
                'can_products'   => $admin->can_products,
                'can_categories' => $admin->can_categories,
                'can_customizer' => $admin->can_customizer,
                'can_orders'     => $admin->can_orders,
            ]
        );

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin created successfully!');
    }

    public function edit($id)
    {
        $adminUser = Admin::findOrFail($id);
        return view('admin.admins.edit', compact('adminUser'));
    }

    public function update(Request $request, $id)
    {
        $adminUser = Admin::findOrFail($id);

        if ($adminUser->is_super_admin) {
            return redirect()->route('admin.admins.index')
                ->with('success', 'Super Admin permissions cannot be modified.');
        }

        // OLD values save karo comparison ke liye
        $old = [
            'can_products'   => $adminUser->can_products,
            'can_categories' => $adminUser->can_categories,
            'can_customizer' => $adminUser->can_customizer,
            'can_orders'     => $adminUser->can_orders,
        ];

        $data = [
            'can_products'   => $request->has('can_products'),
            'can_categories' => $request->has('can_categories'),
            'can_customizer' => $request->has('can_customizer'),
            'can_orders'     => $request->has('can_orders'),
        ];

        $passwordChanged = false;
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $passwordChanged = true;
        }

        $adminUser->update($data);

        // ✅ LOG with old vs new comparison
        ActivityLogger::log(
            action: 'updated',
            module: 'Admin',
            targetName: $adminUser->name,
            targetId: $adminUser->id,
            changes: [
                'old'              => $old,
                'new'              => $data,
                'password_changed' => $passwordChanged,
            ]
        );

        return redirect()->route('admin.admins.index')
            ->with('success', 'Permissions updated successfully!');
    }

    public function destroy($id)
    {
        $adminUser = Admin::findOrFail($id);

        if ($adminUser->is_super_admin) {
            return back()->with('error', 'Super Admin cannot be deleted!');
        }

        // ✅ LOG before delete
        ActivityLogger::log(
            action: 'deleted',
            module: 'Admin',
            targetName: $adminUser->name,
            targetId: $adminUser->id,
            changes: [
                'email'          => $adminUser->email,
                'can_products'   => $adminUser->can_products,
                'can_categories' => $adminUser->can_categories,
                'can_customizer' => $adminUser->can_customizer,
                'can_orders'     => $adminUser->can_orders,
            ]
        );

        $adminUser->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin deleted!');
    }
}
