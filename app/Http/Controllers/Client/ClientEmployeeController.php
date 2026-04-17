<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientEmployeeController extends Controller
{
    public function index()
    {
        return view('client.employees.all');
    }

    public function allData()
    {
        $clientId  = Auth::user()->client_id;
        $employees = ClientEmployee::where('client_id', $clientId)->get();

        return response()->json(['data' => $employees]);
    }

    public function create()
    {
        return view('client.employees.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:191',
            'email'                 => 'required|email|max:191|unique:client_employees,email',
            'phone'                 => 'required|string|max:20',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        ClientEmployee::create([
            'client_id'      => Auth::user()->client_id,
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'password'       => Hash::make($request->password),
            'status_id'      => $request->input('status_id', 1),
            'client_role_id' => $request->input('client_role_id'),
        ]);

        return redirect()->route('client.employees.index')
            ->with('success', 'Employee added successfully.');
    }

    public function edit($id)
    {
        $clientId = Auth::user()->client_id;
        $employee = ClientEmployee::where('id', $id)
            ->where('client_id', $clientId)
            ->firstOrFail();

        return view('client.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $clientId = Auth::user()->client_id;
        $employee = ClientEmployee::where('id', $id)
            ->where('client_id', $clientId)
            ->firstOrFail();

        $request->validate([
            'name'  => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:client_employees,email,' . $id,
            'phone' => 'required|string|max:20',
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'status_id' => $request->input('status_id', $employee->status_id),
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:6|confirmed',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);

        return redirect()->route('client.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        $clientId = Auth::user()->client_id;
        $employee = ClientEmployee::where('id', $request->id)
            ->where('client_id', $clientId)
            ->firstOrFail();

        $employee->update(['status_id' => $request->status]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $clientId = Auth::user()->client_id;
        $employee = ClientEmployee::where('id', $id)
            ->where('client_id', $clientId)
            ->firstOrFail();

        $employee->delete();

        return response()->json(['success' => true]);
    }
}
