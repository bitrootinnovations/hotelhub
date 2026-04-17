<?php

namespace App\Http\Controllers;

use App\Models\RoleMaster;
use Illuminate\Http\Request;

class RoleMasterController extends Controller
{
    public function index()
    {
        return view('roles.all');
    }

    public function allData()
    {
        $roles = RoleMaster::orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $roles]);
    }

    public function create()
    {
        return view('roles.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name'   => 'required|string|max:100|unique:role_masters,role_name',
            'description' => 'nullable|string',
            'status_id'   => 'nullable|integer|in:1,0',
        ]);

        RoleMaster::create([
            'role_name'   => $request->role_name,
            'description' => $request->description,
            'status_id'   => $request->status_id ?? 1,
        ]);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = RoleMaster::findOrFail($id);
        return view('roles.edit', ['role' => $role, 'id' => $id]);
    }

    public function update(Request $request, $id)
    {
        $role = RoleMaster::findOrFail($id);

        $request->validate([
            'role_name'   => 'required|string|max:100|unique:role_masters,role_name,' . $id . ',role_id',
            'description' => 'nullable|string',
            'status_id'   => 'nullable|integer|in:1,0',
        ]);

        $role->update([
            'role_name'   => $request->role_name,
            'description' => $request->description,
            'status_id'   => $request->status_id ?? 1,
        ]);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        $role = RoleMaster::findOrFail($request->role_id);

        // Protect admin role from being deactivated
        if ($role->role_id == 1) {
            return response()->json(['success' => false, 'message' => 'Admin role cannot be deactivated.']);
        }

        $role->status_id = $request->status;
        $role->save();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $role = RoleMaster::findOrFail($id);

        // Protect admin role from deletion
        if ($role->role_id == 1) {
            return response()->json(['success' => false, 'message' => 'Admin role cannot be deleted.']);
        }

        $role->delete();

        return response()->json(['success' => true]);
    }
}
