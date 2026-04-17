<?php

namespace App\Http\Controllers\Api;

use App\Models\CategoryMaster;
use Illuminate\Http\Request;

class CategoryController extends BaseApiController
{
    public function index(Request $request)
    {
        $clientId = $request->attributes->get('client_id');

        $categories = CategoryMaster::select('category_id', 'category_name', 'client_id', 'status_id')
            ->where('status_id', 1)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($clientId) {
                $q->whereNull('client_id');
                if ($clientId) $q->orWhere('client_id', $clientId);
            })
            ->orderBy('category_name')
            ->get();

        return $this->success($categories, 'Categories fetched');
    }
}
