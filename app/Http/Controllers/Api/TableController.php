<?php

namespace App\Http\Controllers\Api;

use App\Models\TableMaster;
use Illuminate\Http\Request;

class TableController extends BaseApiController
{
    public function index(Request $request)
    {
        $clientId = $request->attributes->get('client_id');

        $query = TableMaster::select('table_id', 'table_name', 'table_type_id', 'capacity', 'status_id')
            ->with(['tableType:table_type_id,type_name'])
            ->where('status_id', 1)
            ->whereNull('deleted_at');

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($request->filled('table_type_id')) {
            $query->where('table_type_id', $request->table_type_id);
        }

        $tables = $query->orderBy('table_name')->get()->map(fn($t) => [
            'table_id'        => $t->table_id,
            'table_name'      => $t->table_name,
            'capacity'        => $t->capacity,
            'table_type_id'   => $t->table_type_id,
            'table_type_name' => optional($t->tableType)->type_name,
            'status_id'       => $t->status_id,
        ]);

        return $this->success($tables, 'Tables fetched');
    }
}
