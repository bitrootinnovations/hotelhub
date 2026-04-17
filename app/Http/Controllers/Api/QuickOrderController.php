<?php

namespace App\Http\Controllers\Api;

use App\Models\MenuMaster;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuickOrderController extends BaseApiController
{
    /**
     * GET /api/v1/quick-orders
     * List all takeaway (quick) orders for this client.
     */
    public function index(Request $request)
    {
        $clientId = $request->attributes->get('client_id');
        $perPage  = min((int) $request->get('per_page', 15), 50);

        $query = Order::select(
                'order_id', 'order_number', 'status',
                'subtotal', 'gst_amount', 'total_amount',
                'payment_type', 'payment_status', 'notes', 'created_at'
            )
            ->where('client_id', $clientId)
            ->where('order_type', 'takeaway')
            ->whereNull('deleted_at');

        if ($request->filled('status')) $query->where('status', $request->status);

        $orders = $query->latest()->paginate($perPage);

        // map items to include fixed table/type fields
        $mapped = $orders->getCollection()->map(fn($o) => [
            'order_id'       => $o->order_id,
            'order_number'   => $o->order_number,
            'table'          => '-',
            'type'           => 'Takeaway',
            'status'         => $o->status,
            'subtotal'       => $o->subtotal,
            'gst_amount'     => $o->gst_amount,
            'total_amount'   => $o->total_amount,
            'payment_type'   => $o->payment_type  ?? '-',
            'payment_status' => $o->payment_status,
            'notes'          => $o->notes,
            'created_at'     => $o->created_at->format('d M Y, h:i A'),
        ]);
        $orders->setCollection($mapped);

        return $this->paginated($orders, 'Quick orders fetched');
    }

    /**
     * GET /api/v1/quick-orders/{id}
     * Show a single quick order with its items.
     */
    public function show(Request $request, int $id)
    {
        $clientId = $request->attributes->get('client_id');

        $order = Order::with('items.menu')
            ->where('order_id', $id)
            ->where('client_id', $clientId)
            ->where('order_type', 'takeaway')
            ->whereNull('deleted_at')
            ->first();

        if (!$order) return $this->error('Quick order not found', 404);

        return $this->success($this->formatOrder($order));
    }

    /**
     * POST /api/v1/quick-orders
     * Place a new quick (takeaway) order.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        $menuIds   = collect($request->items)->pluck('menu_id')->unique()->toArray();
        $menuItems = MenuMaster::whereIn('menu_id', $menuIds)
            ->where('status_id', 1)
            ->where('client_id', $clientId)
            ->get()
            ->keyBy('menu_id');

        if ($menuItems->count() !== count($menuIds)) {
            return $this->error('One or more menu items are invalid or unavailable', 422);
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $gstTotal = 0;
            $rows     = [];

            foreach ($request->items as $line) {
                $menu      = $menuItems[$line['menu_id']];
                $qty       = (int) $line['quantity'];
                $unitPrice = (float) $menu->price;
                $gstPct    = (float) $menu->gst_percentage;
                $lineTotal = round($unitPrice * $qty, 2);
                $lineGst   = round($lineTotal * $gstPct / 100, 2);

                $subtotal += $lineTotal;
                $gstTotal += $lineGst;

                $rows[] = [
                    'menu_id'        => $menu->menu_id,
                    'quantity'       => $qty,
                    'unit_price'     => $unitPrice,
                    'gst_percentage' => $gstPct,
                    'total_price'    => $lineTotal + $lineGst,
                    'notes'          => $line['notes'] ?? null,
                ];
            }

            $order = Order::create([
                'client_id'      => $clientId,
                'table_id'       => null,
                'user_id'        => $userId,
                'order_number'   => Order::generateOrderNumber($clientId),
                'status'         => 'pending',
                'order_type'     => 'takeaway',
                'subtotal'       => round($subtotal, 2),
                'gst_amount'     => round($gstTotal, 2),
                'total_amount'   => round($subtotal + $gstTotal, 2),
                'payment_status' => 'pending',
                'notes'          => $request->notes,
            ]);

            $now = now();
            OrderItem::insert(array_map(fn($row) => array_merge($row, [
                'order_id'   => $order->order_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]), $rows));

            DB::commit();

            $order->load('items.menu');

            return $this->success($this->formatOrder($order), 'Quick order placed successfully', 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error('Failed to place order. Please try again.', 500);
        }
    }

    /**
     * PUT /api/v1/quick-orders/{id}
     * Update a quick order — add items and/or change status.
     * Only allowed while order is pending or confirmed (not served/cancelled).
     */
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'status'             => 'sometimes|in:pending,confirmed,served,cancelled',
            'notes'              => 'sometimes|nullable|string|max:500',
            'items'              => 'sometimes|array|min:1',
            'items.*.menu_id'    => 'required_with:items|integer|exists:menu_masters,menu_id',
            'items.*.quantity'   => 'required_with:items|integer|min:1|max:99',
            'items.*.notes'      => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $clientId = $request->attributes->get('client_id');

        $order = Order::with('items.menu')
            ->where('order_id', $id)
            ->where('client_id', $clientId)
            ->where('order_type', 'takeaway')
            ->whereNull('deleted_at')
            ->first();

        if (!$order) return $this->error('Quick order not found', 404);

        if (in_array($order->status, ['served', 'cancelled'])) {
            return $this->error('Cannot update a ' . $order->status . ' order', 422);
        }

        DB::beginTransaction();
        try {
            // Add new items if provided
            if ($request->filled('items')) {
                $menuIds   = collect($request->items)->pluck('menu_id')->unique()->toArray();
                $menuItems = MenuMaster::whereIn('menu_id', $menuIds)
                    ->where('status_id', 1)
                    ->where('client_id', $clientId)
                    ->get()
                    ->keyBy('menu_id');

                if ($menuItems->count() !== count($menuIds)) {
                    return $this->error('One or more menu items are invalid or unavailable', 422);
                }

                $addSubtotal = 0;
                $addGst      = 0;
                $rows        = [];

                foreach ($request->items as $line) {
                    $menu      = $menuItems[$line['menu_id']];
                    $qty       = (int) $line['quantity'];
                    $unitPrice = (float) $menu->price;
                    $gstPct    = (float) $menu->gst_percentage;
                    $lineTotal = round($unitPrice * $qty, 2);
                    $lineGst   = round($lineTotal * $gstPct / 100, 2);

                    $addSubtotal += $lineTotal;
                    $addGst      += $lineGst;

                    $rows[] = [
                        'order_id'       => $order->order_id,
                        'menu_id'        => $menu->menu_id,
                        'quantity'       => $qty,
                        'unit_price'     => $unitPrice,
                        'gst_percentage' => $gstPct,
                        'total_price'    => $lineTotal + $lineGst,
                        'notes'          => $line['notes'] ?? null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }

                OrderItem::insert($rows);
                $order->increment('subtotal',     round($addSubtotal, 2));
                $order->increment('gst_amount',   round($addGst, 2));
                $order->increment('total_amount', round($addSubtotal + $addGst, 2));
            }

            // Update status if provided
            if ($request->filled('status')) {
                $order->status = $request->status;
            }

            // Update notes if provided
            if ($request->has('notes')) {
                $order->notes = $request->notes;
            }

            $order->save();

            DB::commit();

            $order->load('items.menu');

            return $this->success($this->formatOrder($order->fresh()), 'Quick order updated successfully');

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error('Failed to update order. Please try again.', 500);
        }
    }

    // ── Private helper ─────────────────────────────────────────────────────────

    private function formatOrder(Order $order): array
    {
        return [
            'order_id'       => $order->order_id,
            'order_number'   => $order->order_number,
            'table'          => '-',
            'type'           => 'Takeaway',
            'status'         => $order->status,
            'subtotal'       => $order->subtotal,
            'gst_amount'     => $order->gst_amount,
            'total_amount'   => $order->total_amount,
            'payment_type'   => $order->payment_type  ?? '-',
            'payment_status' => $order->payment_status,
            'notes'          => $order->notes,
            'created_at'     => $order->created_at->format('d M Y, h:i A'),
            'items'          => $order->items->map(fn($i) => [
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
        ];
    }
}
