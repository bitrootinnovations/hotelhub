<?php

namespace App\Http\Controllers;

use App\Models\TableTypeMaster;
use App\Models\ClientMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TableTypeMasterController extends Controller
{
    public function index()
    {
        return view('table-types.all');
    }

    public function allData()
    {
        $types = TableTypeMaster::with('client')
            ->select(['table_type_id', 'type_name', 'client_id', 'status_id', 'created_at'])
            ->get()
            ->map(fn($t) => [
                'table_type_id' => $t->table_type_id,
                'type_name'     => $t->type_name,
                'client_name'   => $t->client ? $t->client->client_name : 'Global',
                'status_id'     => $t->status_id,
                'created_at'    => $t->created_at,
            ]);

        return response()->json(['data' => $types]);
    }

    public function create()
    {
        $clients = ClientMaster::orderBy('client_name')->get();
        return view('table-types.add', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_name' => 'required|string|max:100',
        ]);

        TableTypeMaster::create([
            'type_name'  => $request->type_name,
            'client_id'  => $request->client_id ?: null,
            'status_id'  => $request->input('status_id', 1),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('table-types.index')->with('success', 'Table type added successfully.');
    }

    public function edit($id)
    {
        $tableType = TableTypeMaster::findOrFail($id);
        $clients   = ClientMaster::orderBy('client_name')->get();
        return view('table-types.edit', compact('tableType', 'clients', 'id'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type_name' => 'required|string|max:100',
        ]);

        TableTypeMaster::findOrFail($id)->update([
            'type_name' => $request->type_name,
            'client_id' => $request->client_id ?: null,
            'status_id' => $request->input('status_id', 1),
        ]);

        return redirect()->route('table-types.index')->with('success', 'Table type updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        TableTypeMaster::findOrFail($request->id)->update(['status_id' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        TableTypeMaster::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
