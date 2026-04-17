<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientMaster;
use App\Models\MenuMaster;
use App\Models\Order;
use App\Models\TableMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $clientId = Auth::user()->client_id;

        // Client record
        $client = ClientMaster::find($clientId);

        // Today's orders count
        $todayOrders = Order::where('client_id', $clientId)
            ->whereDate('created_at', today())
            ->count();

        // Today's revenue: payment_status = 'paid' AND checked_out_at is today
        $todayRevenue = Order::where('client_id', $clientId)
            ->where('payment_status', 'paid')
            ->whereDate('checked_out_at', today())
            ->sum('total_amount');

        // Active KOTs: orders with status in (pending,confirmed,preparing,served) AND payment_status != 'paid'
        $activeKots = Order::where('client_id', $clientId)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'served'])
            ->where('payment_status', 'pending')
            ->count();

        // Total active menu items for this client
        $totalMenuItems = MenuMaster::where('client_id', $clientId)
            ->where('status_id', 1)
            ->count();

        // All tables for this client (with tableType relation)
        $tables = TableMaster::where('client_id', $clientId)
            ->with('tableType')
            ->get();

        // Table IDs that have active (unpaid) orders
        $occupiedTableIds = Order::where('client_id', $clientId)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'served'])
            ->where('payment_status', 'pending')
            ->pluck('table_id');

        // Recent 10 orders with table name
        $recentOrders = DB::table('orders')
            ->leftJoin('table_masters', 'orders.table_id', '=', 'table_masters.table_id')
            ->where('orders.client_id', $clientId)
            ->whereNull('orders.deleted_at')
            ->orderBy('orders.order_id', 'desc')
            ->limit(10)
            ->select(
                'orders.order_id',
                'orders.order_number',
                'orders.order_type',
                'orders.total_amount',
                'orders.status',
                'orders.payment_status',
                'orders.created_at',
                'table_masters.table_name'
            )
            ->get();

        return view('client.dashboard', compact(
            'clientId',
            'client',
            'todayOrders',
            'todayRevenue',
            'activeKots',
            'totalMenuItems',
            'tables',
            'occupiedTableIds',
            'recentOrders'
        ));
    }
}
