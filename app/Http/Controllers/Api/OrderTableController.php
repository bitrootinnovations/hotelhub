<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTableController extends BaseApiController
{
    /**
     * GET /api/v1/tables/{table_id}/orders
     *
     * Returns the active order(s) and all items for a specific table.
     * Used for the waiter to see what was ordered at a table.
     */
    public function index(Request $request, int $tableId)
    {
        $clientId = $request->attributes->get('client_id');

        $orders = Order::with(['items.menu'])
            ->where('table_id', $tableId)
            ->where('client_id', $clientId)
            ->whereIn('status', ['pending', 'confirmed', 'served'])
            ->where('payment_status', '!=', 'paid')
            ->whereNull('deleted_at')
            ->latest()
            ->get()
            ->map(fn($o) => [
                'order_id'     => $o->order_id,
                'order_number' => $o->order_number,
                'status'       => $o->status,
                'order_type'   => $o->order_type,
                'subtotal'     => $o->subtotal,
                'gst_amount'   => $o->gst_amount,
                'total_amount' => $o->total_amount,
                'payment_type' => $o->payment_type,
                'notes'        => $o->notes,
                'created_at'   => $o->created_at->format('d M Y, h:i A'),
                'items'        => $o->items->map(fn($i) => [
                    'item_id'        => $i->item_id,
                    'menu_id'        => $i->menu_id,
                    'menu_name'      => optional($i->menu)->menu_name,
                    'food_type'      => optional($i->menu)->food_type,
                    'food_type_label'=> optional($i->menu)->food_type == 1 ? 'Veg' : 'Non-Veg',
                    'quantity'       => $i->quantity,
                    'unit_price'     => $i->unit_price,
                    'gst_percentage' => $i->gst_percentage,
                    'total_price'    => $i->total_price,
                    'notes'          => $i->notes,
                ]),
            ]);

        if ($orders->isEmpty()) {
            return $this->success([], 'No active orders for this table');
        }

        // Summary totals across all orders on this table
        $grandTotal = [
            'subtotal'     => round($orders->sum('subtotal'), 2),
            'gst_amount'   => round($orders->sum('gst_amount'), 2),
            'total_amount' => round($orders->sum('total_amount'), 2),
            'item_count'   => $orders->flatMap(fn($o) => $o['items'])->sum('quantity'),
        ];

        return $this->success([
            'table_id'    => $tableId,
            'orders'      => $orders,
            'grand_total' => $grandTotal,
        ], 'Table orders fetched');
    }
}
