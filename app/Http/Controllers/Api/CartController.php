<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MenuMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends BaseApiController
{
    /** Build a unique user ref from the token claims */
    private function userRef(Request $request): string
    {
        $type = $request->attributes->get('user_type');
        $user = $request->attributes->get('mobile_user');
        return $type === 'client'
            ? 'client_' . $user->client_id
            : 'employee_' . $user->id;
    }

    private function formatCart(Cart $cart): array
    {
        $cart->load('items.menu', 'table:table_id,table_name');
        $totals = $cart->getTotal();

        return [
            'cart_id'      => $cart->cart_id,
            'table_id'     => $cart->table_id,
            'table_name'   => optional($cart->table)->table_name,
            'order_type'   => $cart->order_type,
            'items'        => $cart->items->map(fn($i) => [
                'id'             => $i->id,
                'menu_id'        => $i->menu_id,
                'menu_name'      => optional($i->menu)->menu_name,
                'food_type'      => optional($i->menu)->food_type,
                'food_type_label'=> optional($i->menu)->food_type == 1 ? 'Veg' : 'Non-Veg',
                'image'          => optional($i->menu)->image
                    ? config('app.url') . '/storage/' . $i->menu->image
                    : null,
                'quantity'       => $i->quantity,
                'unit_price'     => $i->unit_price,
                'gst_percentage' => $i->gst_percentage,
                'line_total'     => round($i->unit_price * $i->quantity * (1 + $i->gst_percentage / 100), 2),
                'notes'          => $i->notes,
            ]),
            'subtotal'     => $totals['subtotal'],
            'gst_amount'   => $totals['gst_amount'],
            'total_amount' => $totals['total_amount'],
            'item_count'   => $cart->items->sum('quantity'),
        ];
    }

    /**
     * GET /api/v1/cart
     */
    public function show(Request $request)
    {
        $clientId = $request->attributes->get('client_id');
        $userRef  = $this->userRef($request);

        $cart = Cart::where('client_id', $clientId)->where('user_ref', $userRef)->first();
        if (!$cart || $cart->items()->count() === 0) {
            return $this->success(['cart_id' => null, 'items' => [], 'total_amount' => 0], 'Cart is empty');
        }

        return $this->success($this->formatCart($cart), 'Cart fetched');
    }

    /**
     * POST /api/v1/cart/items
     * Add or update item in cart. If menu_id already in cart, quantity is replaced.
     */
    public function addItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu_id'    => 'required|integer|exists:menu_masters,menu_id',
            'quantity'   => 'required|integer|min:1|max:99',
            'table_id'   => 'nullable|integer|exists:table_masters,table_id',
            'order_type' => 'nullable|in:dine_in,takeaway',
            'notes'      => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) return $this->error('Validation failed', 422, $validator->errors());

        $clientId = $request->attributes->get('client_id');
        $userRef  = $this->userRef($request);

        // Validate menu belongs to client
        $menu = MenuMaster::where('menu_id', $request->menu_id)
            ->where('status_id', 1)
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->first();
        if (!$menu) return $this->error('Menu item not found or unavailable', 404);

        $cart = Cart::forUser($clientId, $userRef, $request->table_id, $request->order_type ?? 'dine_in');

        // Upsert cart item
        CartItem::updateOrCreate(
            ['cart_id' => $cart->cart_id, 'menu_id' => $menu->menu_id],
            [
                'quantity'       => $request->quantity,
                'unit_price'     => $menu->price,
                'gst_percentage' => $menu->gst_percentage,
                'notes'          => $request->notes,
            ]
        );

        return $this->success($this->formatCart($cart->fresh()), 'Item added to cart', 201);
    }

    /**
     * DELETE /api/v1/cart/items/{menu_id}
     */
    public function removeItem(Request $request, int $menuId)
    {
        $clientId = $request->attributes->get('client_id');
        $userRef  = $this->userRef($request);

        $cart = Cart::where('client_id', $clientId)->where('user_ref', $userRef)->first();
        if (!$cart) return $this->error('Cart not found', 404);

        $cart->items()->where('menu_id', $menuId)->delete();

        return $this->success($this->formatCart($cart->fresh()), 'Item removed');
    }

    /**
     * DELETE /api/v1/cart
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        $clientId = $request->attributes->get('client_id');
        $userRef  = $this->userRef($request);

        Cart::where('client_id', $clientId)->where('user_ref', $userRef)->delete();
        return $this->success(null, 'Cart cleared');
    }
}
