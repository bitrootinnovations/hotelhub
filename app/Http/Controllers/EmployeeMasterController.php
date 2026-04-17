<?php

namespace App\Http\Controllers;

use App\Models\EmployeeMaster;
use App\Models\RoleMaster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeMasterController extends Controller
{
    public function index()
    {
        return view('employees.all');
    }

    public function allData(Request $request)
    {
        $employees = EmployeeMaster::with('role')
            ->select(['employee_id', 'employee_name', 'email_id', 'contact_number', 'designation', 'department', 'profile_image', 'status_id', 'role_id'])
            ->get();

        $data = $employees->map(function ($emp) {
            return [
                'employee_id'    => $emp->employee_id,
                'employee_name'  => $emp->employee_name,
                'email_id'       => $emp->email_id,
                'contact_number' => $emp->contact_number,
                'designation'    => $emp->designation ?? '-',
                'department'     => $emp->department ?? '-',
                'profile_image'  => $emp->profile_image,
                'status_id'      => $emp->status_id,
                'role_name'      => $emp->role ? $emp->role->role_name : '-',
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function create()
    {
        $roles = RoleMaster::where('status_id', 1)->get();
        return view('employees.add', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_name'  => 'required|string|max:150',
            'email_id'       => 'required|email|max:150|unique:employee_masters,email_id|unique:users,email',
            'contact_number' => 'required|string|max:15',
            'role_id'        => 'required|exists:role_masters,role_id',
            'password'       => 'required|string|min:6|confirmed',
        ]);

        $data               = $request->only(['employee_name', 'email_id', 'contact_number', 'role_id', 'designation', 'department', 'address', 'status_id']);
        $data['status_id']  = $request->input('status_id', 1);
        $data['created_by'] = Auth::id();

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('employees/profile', 'public');
        }

        EmployeeMaster::create($data);

        // Create login account in users table
        User::create([
            'name'      => $request->employee_name,
            'email'     => $request->email_id,
            'password'  => Hash::make($request->password),
            'role_id'   => $request->role_id,
            'status_id' => $request->input('status_id', 1),
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee added successfully. Login credentials created.');
    }

    public function edit($id)
    {
        $employee = EmployeeMaster::findOrFail($id);
        $roles    = RoleMaster::where('status_id', 1)->get();
        return view('employees.edit', compact('employee', 'roles', 'id'));
    }

    public function update(Request $request, $id)
    {
        $employee = EmployeeMaster::findOrFail($id);

        $request->validate([
            'employee_name'  => 'required|string|max:150',
            'email_id'       => 'required|email|max:150|unique:employee_masters,email_id,' . $id . ',employee_id',
            'contact_number' => 'required|string|max:15',
            'role_id'        => 'required|exists:role_masters,role_id',
        ]);

        $data              = $request->only(['employee_name', 'email_id', 'contact_number', 'role_id', 'designation', 'department', 'address', 'status_id']);
        $data['status_id'] = $request->input('status_id', 1);

        if ($request->hasFile('profile_image')) {
            if ($employee->profile_image) {
                Storage::disk('public')->delete($employee->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('employees/profile', 'public');
        }

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        $employee = EmployeeMaster::findOrFail($request->id);
        $employee->update(['status_id' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $employee = EmployeeMaster::findOrFail($id);
        if ($employee->profile_image) {
            Storage::disk('public')->delete($employee->profile_image);
        }
        $employee->delete();
        return response()->json(['success' => true]);
    }
}
