<?php

namespace App\Http\Controllers;

use App\Models\TableMaster;
use App\Models\TableTypeMaster;
use App\Models\ClientMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TableMasterController extends Controller
{
    public function index()
    {
        return view('tables.all');
    }

    public function allData()
    {
        $tables = TableMaster::with(['tableType', 'client'])
            ->select(['table_id', 'table_name', 'table_type_id', 'client_id', 'capacity', 'status_id', 'created_at'])
            ->get()
            ->map(fn($t) => [
                'table_id'        => $t->table_id,
                'table_name'      => $t->table_name,
                'table_type_name' => $t->tableType ? $t->tableType->type_name : '-',
                'client_name'     => $t->client ? $t->client->client_name : '-',
                'capacity'        => $t->capacity ?? '-',
                'status_id'       => $t->status_id,
                'created_at'      => $t->created_at,
            ]);

        return response()->json(['data' => $tables]);
    }

    public function create()
    {
        $tableTypes = TableTypeMaster::where('status_id', 1)->get(['table_type_id', 'type_name', 'client_id']);
        $clients    = ClientMaster::where('status_id', 1)->orderBy('client_name')->get(['client_id', 'client_name']);
        return view('tables.add', compact('tableTypes', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'seat_label' => 'required|string|max:50',
            'seat_type'  => 'required|exists:table_type_masters,table_type_id',
            'client_id'  => 'required|exists:client_masters,client_id',
        ]);

        TableMaster::create([
            'table_name'    => $request->seat_label,
            'table_type_id' => $request->seat_type,
            'client_id'     => $request->client_id,
            'capacity'      => $request->capacity ?: null,
            'status_id'     => $request->input('status_id', 1),
            'created_by'    => Auth::id(),
        ]);

        return redirect()->route('tables.index')->with('success', 'Table added successfully.');
    }

    public function edit($id)
    {
        $table      = TableMaster::findOrFail($id);
        $tableTypes = TableTypeMaster::where('status_id', 1)->get(['table_type_id', 'type_name', 'client_id']);
        $clients    = ClientMaster::where('status_id', 1)->orderBy('client_name')->get(['client_id', 'client_name']);
        return view('tables.edit', compact('table', 'tableTypes', 'clients', 'id'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'seat_label' => 'required|string|max:50',
            'seat_type'  => 'required|exists:table_type_masters,table_type_id',
            'client_id'  => 'required|exists:client_masters,client_id',
        ]);

        TableMaster::findOrFail($id)->update([
            'table_name'    => $request->seat_label,
            'table_type_id' => $request->seat_type,
            'client_id'     => $request->client_id,
            'capacity'      => $request->capacity ?: null,
            'status_id'     => $request->input('status_id', 1),
        ]);

        return redirect()->route('tables.index')->with('success', 'Table updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        TableMaster::findOrFail($request->id)->update(['status_id' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        TableMaster::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
