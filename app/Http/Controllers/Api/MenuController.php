<?php

namespace App\Http\Controllers\Api;

use App\Models\MenuMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MenuController extends BaseApiController
{
    /**
     * GET /api/v1/menu?category_id=1&food_type=1&search=pizza&page=1&per_page=20
     */
    public function index(Request $request)
    {
        $clientId = $request->attributes->get('client_id');
        $perPage  = min((int) $request->get('per_page', 20), 50);

        $query = MenuMaster::select(
                'menu_id', 'menu_name', 'category_id', 'food_type',
                'price', 'gst_percentage', 'stock_type', 'quantity', 'image', 'client_id'
            )
            ->with(['category:category_id,category_name'])
            ->where('status_id', 1)
            ->where('deleted_at', null);

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('food_type')) {
            $query->where('food_type', $request->food_type);
        }

        if ($request->filled('search')) {
            $query->where('menu_name', 'like', '%' . $request->search . '%');
        }

        $menu = $query->orderBy('menu_name')->paginate($perPage);

        $items = collect($menu->items())->map(fn($m) => [
            'menu_id'        => $m->menu_id,
            'menu_name'      => $m->menu_name,
            'category_id'    => $m->category_id,
            'category_name'  => optional($m->category)->category_name,
            'food_type'      => $m->food_type,
            'food_type_label'=> $m->food_type == 1 ? 'Veg' : 'Non-Veg',
            'price'          => (float) $m->price,
            'gst_percentage' => (float) $m->gst_percentage,
            'stock_type'     => $m->stock_type,
            'quantity'       => $m->quantity,
            'image'          => $m->image
                ? config('app.url') . '/storage/' . $m->image
                : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Menu fetched',
            'data'    => $items,
            'meta'    => [
                'total'        => $menu->total(),
                'per_page'     => $menu->perPage(),
                'current_page' => $menu->currentPage(),
                'last_page'    => $menu->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/v1/menu/{id}
     */
    public function show(Request $request, int $id)
    {
        $clientId = $request->attributes->get('client_id');

        $item = MenuMaster::select(
                'menu_id', 'menu_name', 'category_id', 'food_type',
                'price', 'gst_percentage', 'stock_type', 'quantity', 'image', 'client_id'
            )
            ->with(['category:category_id,category_name'])
            ->where('menu_id', $id)
            ->where('status_id', 1)
            ->where('deleted_at', null)
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->first();

        if (!$item) return $this->error('Menu item not found', 404);

        return $this->success([
            'menu_id'         => $item->menu_id,
            'menu_name'       => $item->menu_name,
            'category_id'     => $item->category_id,
            'category_name'   => optional($item->category)->category_name,
            'food_type'       => $item->food_type,
            'food_type_label' => $item->food_type == 1 ? 'Veg' : 'Non-Veg',
            'price'           => (float) $item->price,
            'gst_percentage'  => (float) $item->gst_percentage,
            'stock_type'      => $item->stock_type,
            'quantity'        => $item->quantity,
            'image'           => $item->image
                ? config('app.url') . '/storage/' . $item->image
                : null,
        ]);
    }

    /**
     * PUT /api/v1/menu/{id}
     * Client only — update a menu item's details.
     * Supports multipart/form-data for image upload.
     */
    public function update(Request $request, int $id)
    {
        if ($request->attributes->get('user_type') !== 'client') {
            return $this->error('Only the client owner can edit menu items', 403);
        }

        $clientId = $request->attributes->get('client_id');

        $item = MenuMaster::where('menu_id', $id)
            ->where('client_id', $clientId)
            ->whereNull('deleted_at')
            ->first();

        if (!$item) return $this->error('Menu item not found', 404);

        $validator = Validator::make($request->all(), [
            'menu_name'      => 'sometimes|string|max:255',
            'category_id'    => 'sometimes|integer|exists:category_masters,category_id',
            'food_type'      => 'sometimes|in:1,2',
            'price'          => 'sometimes|numeric|min:0',
            'gst_percentage' => 'sometimes|numeric|min:0|max:100',
            'stock_type'     => 'sometimes|in:limited,unlimited',
            'quantity'       => 'sometimes|integer|min:0',
            'image'          => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        if ($validator->fails()) return $this->error('Validation failed', 422, $validator->errors());

        $data = $request->only([
            'menu_name', 'category_id', 'food_type',
            'price', 'gst_percentage', 'stock_type', 'quantity',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image from storage
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $path = $request->file('image')->store('menu', 'public');
            $data['image'] = $path;
        }

        $item->update($data);
        $item->load('category:category_id,category_name');

        return $this->success([
            'menu_id'         => $item->menu_id,
            'menu_name'       => $item->menu_name,
            'category_id'     => $item->category_id,
            'category_name'   => optional($item->category)->category_name,
            'food_type'       => $item->food_type,
            'food_type_label' => $item->food_type == 1 ? 'Veg' : 'Non-Veg',
            'price'           => (float) $item->price,
            'gst_percentage'  => (float) $item->gst_percentage,
            'stock_type'      => $item->stock_type,
            'quantity'        => $item->quantity,
            'image'           => $item->image
                ? config('app.url') . '/storage/' . $item->image
                : null,
        ], 'Menu item updated');
    }
}
