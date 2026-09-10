<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'required|string',
        ]);

        // Manually handle ID if auto-increment is not set correctly in DB for roles, 
        // but typically it is. Looking at seeders, IDs are hardcoded (1, 2, 3..).
        // Best to let DB handle it if strict, or find max + 1.
        // Assuming auto-increment works or we can just use create().
        
        Role::create($validated);

        return redirect()->route('roles.index')->with('success', 'تم إضافة الدور بنجاح');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->role_id . ',role_id',
            'description' => 'required|string',
        ]);

        $role->update($validated);

        return redirect()->route('roles.index')->with('success', 'تم تحديث الدور بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Prevent deleting critical roles (Admin, Specialist, Client)
        if (in_array($role->name, ['Admin', 'Specialist', 'Client', 'Nutrition Manager', 'Restaurant Manager'])) {
             // Or maybe just check IDs 1-5
             if ($role->role_id <= 5) {
                return redirect()->route('roles.index')->withErrors(['لا يمكن حذف الأدوار الأساسية للنظام.']);
             }
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'تم حذف الدور بنجاح');
    }
}
