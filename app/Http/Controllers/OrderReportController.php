<?php

namespace App\Http\Controllers;

use App\Models\ClientMaster;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderReportController extends Controller
{
    public function index(Request $request)
    {
        $authClientId = Auth::user()->client_id;

        $clients = $authClientId
            ? collect()
            : ClientMaster::select('client_id', 'client_name')
                ->whereNull('deleted_at')
                ->orderBy('client_name')
                ->get();

        $today    = now()->toDateString();
        $fromDate = $request->get('from_date', $today);
        $toDate   = $request->get('to_date',   $today);
        $status   = $request->get('status',    '');
        $clientId = $authClientId ?: $request->get('client_id', '');

        return view('reports.orders', compact('clients', 'fromDate', 'toDate', 'status', 'clientId'));
    }

    public function allData(Request $request)
    {
        $fromDate  = $request->get('from_date', now()->toDateString());
        $toDate    = $request->get('to_date',   now()->toDateString());
        $clientId  = Auth::user()->client_id ?: $request->get('client_id');
        $status    = $request->get('status');

        $query = Order::select(
                'orders.order_id', 'orders.order_number', 'orders.client_id',
                'orders.table_id', 'orders.status', 'orders.order_type',
                'orders.subtotal', 'orders.gst_amount', 'orders.total_amount',
                'orders.payment_type', 'orders.payment_status', 'orders.checked_out_at',
                'orders.created_at',
                'client_masters.client_name',
                'table_masters.table_name'
            )
            ->leftJoin('client_masters', 'orders.client_id', '=', 'client_masters.client_id')
            ->leftJoin('table_masters',  'orders.table_id',  '=', 'table_masters.table_id')
            ->whereNull('orders.deleted_at')
            ->whereDate('orders.created_at', '>=', $fromDate)
            ->whereDate('orders.created_at', '<=', $toDate);

        if ($clientId) $query->where('orders.client_id', $clientId);
        if ($status)   $query->where('orders.status', $status);

        $orders = $query->latest('orders.created_at')->get();

        $data = $orders->map(fn($o) => [
            'order_id'       => $o->order_id,
            'order_number'   => $o->order_number,
            'client_name'    => $o->client_name ?? '-',
            'table_name'     => $o->table_name  ?? 'Takeaway',
            'status'         => $o->status,
            'order_type'     => $o->order_type,
            'subtotal'       => number_format($o->subtotal, 2),
            'gst_amount'     => number_format($o->gst_amount, 2),
            'total_amount'   => number_format($o->total_amount, 2),
            'payment_type'   => $o->payment_type  ?? '-',
            'payment_status' => $o->payment_status ?? '-',
            'checked_out_at' => $o->checked_out_at ? $o->checked_out_at->format('d M Y, h:i A') : '-',
            'created_at'     => $o->created_at->format('d M Y, h:i A'),
        ]);

        // Summary totals
        $summary = [
            'total_orders'    => $orders->count(),
            'total_revenue'   => number_format($orders->sum('total_amount'), 2),
            'paid_amount'     => number_format($orders->where('payment_status', 'paid')->sum('total_amount'), 2),
            'cancelled_orders'=> $orders->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'data'    => $data,
            'summary' => $summary,
        ]);
    }
}
