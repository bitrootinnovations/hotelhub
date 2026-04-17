<?php

namespace App\Http\Controllers\Api;

use App\Models\MenuMaster;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class OrderController extends BaseApiController
{
    /**
     * GET /api/v1/orders?status=pending&page=1
     */
    public function index(Request $request)
    {
        $clientId = $request->attributes->get('client_id');
        $perPage  = min((int) $request->get('per_page', 15), 50);

        $query = Order::select(
                'order_id', 'order_number', 'table_id', 'status',
                'order_type', 'subtotal', 'gst_amount', 'total_amount', 'notes', 'created_at'
            )
            ->with(['table:table_id,table_name'])
            ->where('deleted_at', null);

        if ($clientId) $query->where('client_id', $clientId);
        if ($request->filled('status')) $query->where('status', $request->status);

        $orders = $query->latest()->paginate($perPage);

        $items = collect($orders->items())->map(fn($o) => [
            'order_id'     => $o->order_id,
            'order_number' => $o->order_number,
            'table_name'   => optional($o->table)->table_name,
            'status'       => $o->status,
            'order_type'   => $o->order_type,
            'subtotal'     => $o->subtotal,
            'gst_amount'   => $o->gst_amount,
            'total_amount' => $o->total_amount,
            'notes'        => $o->notes,
            'created_at'   => $o->created_at->format('d M Y, h:i A'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Orders fetched',
            'data'    => $items,
            'meta'    => [
                'total'        => $orders->total(),
                'per_page'     => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/v1/orders/{id}
     */
    public function show(Request $request, int $id)
    {
        $clientId = $request->attributes->get('client_id');

        $order = Order::with([
            'table:table_id,table_name',
            'items.menu',
        ])
        ->where('order_id', $id)
        ->where('deleted_at', null)
        ->when($clientId, fn($q) => $q->where('client_id', $clientId))
        ->first();

        if (!$order) return $this->error('Order not found', 404);

        return $this->success([
            'order_id'     => $order->order_id,
            'order_number' => $order->order_number,
            'table_name'   => optional($order->table)->table_name,
            'status'       => $order->status,
            'order_type'   => $order->order_type,
            'subtotal'     => $order->subtotal,
            'gst_amount'   => $order->gst_amount,
            'total_amount' => $order->total_amount,
            'notes'        => $order->notes,
            'created_at'   => $order->created_at->format('d M Y, h:i A'),
            'items'        => $order->items->map(fn($i) => [
                'item_id'        => $i->item_id,
                'menu_id'        => $i->menu_id,
                'menu_name'      => optional($i->menu)->menu_name,
                'food_type'      => optional($i->menu)->food_type,
                'quantity'       => $i->quantity,
                'unit_price'     => $i->unit_price,
                'gst_percentage' => $i->gst_percentage,
                'total_price'    => $i->total_price,
                'notes'          => $i->notes,
            ]),
        ]);
    }

    /**
     * POST /api/v1/orders
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'table_id'           => 'nullable|integer|exists:table_masters,table_id',
            'order_type'         => 'required|in:dine_in,takeaway',
            'notes'              => 'nullable|string|max:500',
            'items'              => 'required|array|min:1',
            'items.*.menu_id'    => 'required|integer|exists:menu_masters,menu_id',
            'items.*.quantity'   => 'required|integer|min:1|max:99',
            'items.*.notes'      => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $clientId = $request->attributes->get('client_id');
        $userId   = optional($request->attributes->get('mobile_user'))->id;

        // Fetch all menu items in a single query
        $menuIds   = collect($request->items)->pluck('menu_id')->unique()->toArray();
        $menuItems = MenuMaster::whereIn('menu_id', $menuIds)
            ->where('status_id', 1)
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->get()
            ->keyBy('menu_id');

        if ($menuItems->count() !== count($menuIds)) {
            return $this->error('One or more menu items are invalid or unavailable', 422);
        }

        DB::beginTransaction();
        try {
            // If table already has an active unpaid order, add items to it
            $existingOrder = $request->table_id
                ? Order::where('client_id', $clientId)
                       ->where('table_id', $request->table_id)
                       ->whereIn('status', ['pending', 'confirmed', 'served'])
                       ->where('payment_status', 'pending')
                       ->whereNull('deleted_at')
                       ->latest()
                       ->first()
                : null;

            $newSubtotal = 0;
            $newGstTotal = 0;
            $orderRows   = [];

            foreach ($request->items as $line) {
                $menu      = $menuItems[$line['menu_id']];
                $qty       = (int) $line['quantity'];
                $unitPrice = (float) $menu->price;
                $gstPct    = (float) $menu->gst_percentage;
                $lineTotal = round($unitPrice * $qty, 2);
                $lineGst   = round($lineTotal * $gstPct / 100, 2);

                $newSubtotal += $lineTotal;
                $newGstTotal += $lineGst;

                $orderRows[] = [
                    'menu_id'        => $menu->menu_id,
                    'quantity'       => $qty,
                    'unit_price'     => $unitPrice,
                    'gst_percentage' => $gstPct,
                    'total_price'    => $lineTotal + $lineGst,
                    'notes'          => $line['notes'] ?? null,
                ];
            }

            if ($existingOrder) {
                // Append items to the existing order and recalculate totals
                $now = now();
                OrderItem::insert(array_map(fn($row) => array_merge($row, [
                    'order_id'   => $existingOrder->order_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]), $orderRows));

                $existingOrder->increment('subtotal',    round($newSubtotal, 2));
                $existingOrder->increment('gst_amount',  round($newGstTotal, 2));
                $existingOrder->increment('total_amount', round($newSubtotal + $newGstTotal, 2));

                $order = $existingOrder->fresh();
                $message = 'Items added to existing order';
            } else {
                // No active order on this table — create a new one
                $order = Order::create([
                    'client_id'      => $clientId,
                    'table_id'       => $request->table_id,
                    'user_id'        => $userId,
                    'order_number'   => Order::generateOrderNumber($clientId),
                    'status'         => 'pending',
                    'order_type'     => $request->order_type,
                    'subtotal'       => round($newSubtotal, 2),
                    'gst_amount'     => round($newGstTotal, 2),
                    'total_amount'   => round($newSubtotal + $newGstTotal, 2),
                    'payment_status' => 'pending',
                    'notes'          => $request->notes,
                ]);

                $now = now();
                OrderItem::insert(array_map(fn($row) => array_merge($row, [
                    'order_id'   => $order->order_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]), $orderRows));

                $message = 'Order placed successfully';
            }

            DB::commit();

            return $this->success([
                'order_id'     => $order->order_id,
                'order_number' => $order->order_number,
                'status'       => $order->status,
                'subtotal'     => $order->subtotal,
                'gst_amount'   => $order->gst_amount,
                'total_amount' => $order->total_amount,
            ], $message, 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error('Failed to place order. Please try again.', 500);
        }
    }
}
