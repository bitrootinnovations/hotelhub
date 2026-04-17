<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends BaseApiController
{
    /**
     * POST /api/v1/checkout
     *
     * Converts the user's cart into a confirmed, paid order.
     * Clears the cart on success.
     *
     * payment_type: Cash | UPI | Card | Online | Other
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_type' => 'required|in:Cash,UPI,Card,Online,Other',
            'table_id'     => 'nullable|integer|exists:table_masters,table_id',
            'order_type'   => 'nullable|in:dine_in,takeaway',
            'notes'        => 'nullable|string|max:500',
        ]);
        if ($validator->fails()) return $this->error('Validation failed', 422, $validator->errors());

        $clientId = $request->attributes->get('client_id');
        $userType = $request->attributes->get('user_type');
        $mobileUser = $request->attributes->get('mobile_user');
        $userRef  = $userType === 'client'
            ? 'client_' . $mobileUser->client_id
            : 'employee_' . $mobileUser->id;

        // Find cart
        $cart = Cart::where('client_id', $clientId)
                    ->where('user_ref', $userRef)
                    ->with('items.menu')
                    ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return $this->error('Your cart is empty. Add items before checkout.', 422);
        }

        DB::beginTransaction();
        try {
            $subtotal = 0; $gstTotal = 0;
            $orderRows = [];

            foreach ($cart->items as $cartItem) {
                $qty       = $cartItem->quantity;
                $unitPrice = $cartItem->unit_price;
                $gstPct    = $cartItem->gst_percentage;
                $lineTotal = round($unitPrice * $qty, 2);
                $lineGst   = round($lineTotal * $gstPct / 100, 2);

                $subtotal  += $lineTotal;
                $gstTotal  += $lineGst;

                $orderRows[] = [
                    'menu_id'        => $cartItem->menu_id,
                    'quantity'       => $qty,
                    'unit_price'     => $unitPrice,
                    'gst_percentage' => $gstPct,
                    'total_price'    => $lineTotal + $lineGst,
                    'notes'          => $cartItem->notes,
                ];
            }

            $order = Order::create([
                'client_id'      => $clientId,
                'table_id'       => $request->table_id ?? $cart->table_id,
                'user_id'        => $userType === 'employee' ? $mobileUser->id : null,
                'order_number'   => Order::generateOrderNumber($clientId),
                'status'         => 'served',
                'order_type'     => $request->order_type ?? $cart->order_type,
                'subtotal'       => round($subtotal, 2),
                'gst_amount'     => round($gstTotal, 2),
                'total_amount'   => round($subtotal + $gstTotal, 2),
                'payment_type'   => $request->payment_type,
                'payment_status' => 'paid',
                'checked_out_at' => now(),
                'notes'          => $request->notes,
            ]);

            $now = now();
            OrderItem::insert(array_map(fn($row) => array_merge($row, [
                'order_id'   => $order->order_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]), $orderRows));

            // Clear cart after successful checkout
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return $this->success([
                'order_id'       => $order->order_id,
                'order_number'   => $order->order_number,
                'status'         => $order->status,
                'payment_type'   => $order->payment_type,
                'payment_status' => $order->payment_status,
                'subtotal'       => $order->subtotal,
                'gst_amount'     => $order->gst_amount,
                'total_amount'   => $order->total_amount,
                'checked_out_at' => $order->checked_out_at->format('d M Y, h:i A'),
            ], 'Checkout successful', 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error('Checkout failed. Please try again.', 500);
        }
    }

    /**
     * POST /api/v1/checkout/order/{order_id}
     * Checkout an existing (already placed) order directly — payment only.
     */
    public function checkoutOrder(Request $request, int $orderId)
    {
        $validator = Validator::make($request->all(), [
            'payment_type' => 'required|in:Cash,UPI,Card,Online,Other',
        ]);
        if ($validator->fails()) return $this->error('Validation failed', 422, $validator->errors());

        $clientId = $request->attributes->get('client_id');

        $order = Order::where('order_id', $orderId)
                      ->where('client_id', $clientId)
                      ->whereIn('status', ['pending', 'confirmed', 'served'])
                      ->whereNull('deleted_at')
                      ->first();

        if (!$order) return $this->error('Order not found or already checked out', 404);

        $order->update([
            'payment_type'   => $request->payment_type,
            'payment_status' => 'paid',
            'status'         => 'served',
            'checked_out_at' => now(),
        ]);

        return $this->success([
            'order_id'       => $order->order_id,
            'order_number'   => $order->order_number,
            'payment_type'   => $order->payment_type,
            'payment_status' => $order->payment_status,
            'total_amount'   => $order->total_amount,
            'checked_out_at' => $order->checked_out_at->format('d M Y, h:i A'),
        ], 'Order checked out successfully');
    }
}
