<?php

namespace App\Http\Controllers\Api;

use App\Models\TableMaster;
use App\Models\Order;
use Illuminate\Http\Request;

class TableStatusController extends BaseApiController
{
    /**
     * GET /api/v1/tables/status?table_type_id=1
     *
     * Returns all tables with current occupancy status and color code.
     *
     * Color codes:
     *   free       → green   (#4CAF50)
     *   pending    → orange  (#FF9800)  order placed but not confirmed
     *   occupied   → red     (#F44336)  order confirmed/preparing/serving
     *   served     → blue    (#2196F3)  food served, awaiting checkout
     */
    public function index(Request $request)
    {
        $clientId = $request->attributes->get('client_id');

        $query = TableMaster::select('table_id', 'table_name', 'table_type_id', 'capacity', 'status_id')
            ->with('tableType:table_type_id,type_name')
            ->whereNull('deleted_at');

        if ($clientId) $query->where('client_id', $clientId);
        if ($request->filled('table_type_id')) $query->where('table_type_id', $request->table_type_id);

        $tables = $query->orderBy('table_name')->get();

        // Fetch active (not yet paid) orders for all these tables in ONE query
        // Exclude paid orders — those tables are free again after checkout
        $tableIds    = $tables->pluck('table_id');
        $activeOrders = Order::select('table_id', 'order_id', 'order_number', 'status')
            ->whereIn('table_id', $tableIds)
            ->whereIn('status', ['pending', 'confirmed', 'served'])
            ->where('payment_status', '!=', 'paid')
            ->whereNull('deleted_at')
            ->latest()
            ->get()
            ->keyBy('table_id');

        $colorMap = [
            'free'      => ['status' => 'free',     'color' => '#4CAF50', 'label' => 'Free'],
            'pending'   => ['status' => 'pending',   'color' => '#FF9800', 'label' => 'Order Placed'],
            'confirmed' => ['status' => 'occupied',  'color' => '#F44336', 'label' => 'Occupied'],
            'served'    => ['status' => 'served',    'color' => '#2196F3', 'label' => 'Served'],
        ];

        $result = $tables->map(function ($table) use ($activeOrders, $colorMap) {
            $order      = $activeOrders->get($table->table_id);
            $orderStatus = $order ? $order->status : 'free';
            $meta        = $colorMap[$orderStatus] ?? $colorMap['free'];

            return [
                'table_id'        => $table->table_id,
                'table_name'      => $table->table_name,
                'capacity'        => $table->capacity,
                'table_type_id'   => $table->table_type_id,
                'table_type_name' => optional($table->tableType)->type_name,
                'table_status'    => $meta['status'],
                'color_code'      => $meta['color'],
                'status_label'    => $meta['label'],
                'active_order'    => $order ? [
                    'order_id'     => $order->order_id,
                    'order_number' => $order->order_number,
                    'status'       => $order->status,
                ] : null,
            ];
        });

        return $this->success($result, 'Table status fetched');
    }
}
