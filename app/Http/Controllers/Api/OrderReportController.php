<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderReportController extends BaseApiController
{
    /**
     * GET /api/v1/reports/orders
     *
     * Query params:
     *   from_date  (Y-m-d) default: today
     *   to_date    (Y-m-d) default: today
     *   status     (pending|confirmed|served|cancelled)
     *   payment_type (Cash|UPI|Card|Online|Other)
     *   per_page   default 20, max 100
     */
    public function index(Request $request)
    {
        $clientId  = $request->attributes->get('client_id');
        $fromDate  = $request->get('from_date', now()->toDateString());
        $toDate    = $request->get('to_date',   now()->toDateString());
        $perPage   = min((int) $request->get('per_page', 20), 100);

        $query = Order::select(
                'order_id', 'order_number', 'table_id', 'status', 'order_type',
                'subtotal', 'gst_amount', 'total_amount',
                'payment_type', 'payment_status', 'checked_out_at', 'notes', 'created_at'
            )
            ->with('table:table_id,table_name')
            ->where('client_id', $clientId)
            ->whereNull('deleted_at')
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate);

        if ($request->filled('status'))       $query->where('status', $request->status);
        if ($request->filled('payment_type')) $query->where('payment_type', $request->payment_type);

        $orders = $query->latest()->paginate($perPage);

        // Summary stats for the filtered period
        $stats = Order::where('client_id', $clientId)
            ->whereNull('deleted_at')
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->when($request->filled('status'),       fn($q) => $q->where('status', $request->status))
            ->when($request->filled('payment_type'), fn($q) => $q->where('payment_type', $request->payment_type))
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(subtotal) as total_subtotal,
                SUM(gst_amount) as total_gst,
                SUM(total_amount) as total_revenue,
                SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_orders
            ')
            ->first();

        $items = collect($orders->items())->map(fn($o) => [
            'order_id'       => $o->order_id,
            'order_number'   => $o->order_number,
            'table_name'     => optional($o->table)->table_name,
            'status'         => $o->status,
            'order_type'     => $o->order_type,
            'subtotal'       => $o->subtotal,
            'gst_amount'     => $o->gst_amount,
            'total_amount'   => $o->total_amount,
            'payment_type'   => $o->payment_type,
            'payment_status' => $o->payment_status,
            'checked_out_at' => $o->checked_out_at?->format('d M Y, h:i A'),
            'created_at'     => $o->created_at->format('d M Y, h:i A'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order report fetched',
            'summary' => [
                'from_date'       => $fromDate,
                'to_date'         => $toDate,
                'total_orders'    => (int)   ($stats->total_orders    ?? 0),
                'cancelled_orders'=> (int)   ($stats->cancelled_orders ?? 0),
                'total_subtotal'  => round(  ($stats->total_subtotal  ?? 0), 2),
                'total_gst'       => round(  ($stats->total_gst       ?? 0), 2),
                'total_revenue'   => round(  ($stats->total_revenue   ?? 0), 2),
                'paid_amount'     => round(  ($stats->paid_amount     ?? 0), 2),
            ],
            'data' => $items,
            'meta' => [
                'total'        => $orders->total(),
                'per_page'     => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
            ],
        ]);
    }
}
