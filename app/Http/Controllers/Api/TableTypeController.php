<?php

namespace App\Http\Controllers\Api;

use App\Models\TableTypeMaster;
use Illuminate\Http\Request;

class TableTypeController extends BaseApiController
{
    public function index(Request $request)
    {
        $clientId = $request->attributes->get('client_id');

        $types = TableTypeMaster::select('table_type_id', 'type_name', 'client_id')
            ->where('status_id', 1)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($clientId) {
                $q->whereNull('client_id');
                if ($clientId) $q->orWhere('client_id', $clientId);
            })
            ->orderBy('type_name')
            ->get();

        return $this->success($types, 'Table types fetched');
    }
}
