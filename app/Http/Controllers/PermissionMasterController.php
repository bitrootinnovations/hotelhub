<?php

namespace App\Http\Controllers;

use App\Models\PermissionMaster;
use App\Models\RoleMaster;
use Illuminate\Http\Request;

class PermissionMasterController extends Controller
{
    // All application modules — single source of truth
    public static array $modules = [
        'Dashboard',
        'Client Registration',
        'Role Master',
        'Permission Master',
        'Products',
        'Categories',
        'Sub Categories',
        'Brands',
        'Units',
        'Variant Attributes',
        'Warranties',
        'Manage Stock',
        'Stock Adjustment',
        'Stock Transfer',
        'Sales',
        'Purchase Orders',
        'Purchase Returns',
        'Expenses',
        'Income',
        'Bank Accounts',
        'Customers',
        'Suppliers',
        'Employees',
        'Departments',
        'Table Type Master',
        'Table Master',
        'Menu Master',
        'Payroll',
        'Reports',
        'Users',
        'Settings',
        // Client portal modules
        'Client Dashboard',
        'Menu Report',
        'Top Selling Menu',
        'Client Employees',
    ];

    public function index()
    {
        $roles = RoleMaster::orderBy('role_id')->get();
        return view('permissions.all', compact('roles'));
    }

    public function edit($roleId)
    {
        $role = RoleMaster::findOrFail($roleId);

        // Admin always has full access — show read-only view
        $isAdmin = $role->role_id == 1;

        // Load existing permissions keyed by menu_name
        $existing = PermissionMaster::where('role_id', $roleId)
            ->get()
            ->keyBy('menu_name');

        $modules = self::$modules;

        return view('permissions.edit', compact('role', 'modules', 'existing', 'isAdmin'));
    }

    public function update(Request $request, $roleId)
    {
        $role = RoleMaster::findOrFail($roleId);

        // Admin permissions cannot be changed — they always have full access
        if ($role->role_id == 1) {
            return redirect()->route('permissions.index')->with('info', 'Admin role always has full access.');
        }

        // Delete all existing permissions for this role, then re-insert
        PermissionMaster::where('role_id', $roleId)->delete();

        $permissions = $request->input('permissions', []);

        foreach (self::$modules as $module) {
            PermissionMaster::create([
                'role_id'    => $roleId,
                'menu_name'  => $module,
                'can_view'   => isset($permissions[$module]['can_view'])   ? 1 : 0,
                'can_add'    => isset($permissions[$module]['can_add'])    ? 1 : 0,
                'can_edit'   => isset($permissions[$module]['can_edit'])   ? 1 : 0,
                'can_delete' => isset($permissions[$module]['can_delete']) ? 1 : 0,
            ]);
        }

        return redirect()->route('permissions.index')->with('success', "Permissions for \"{$role->role_name}\" saved successfully.");
    }
}
