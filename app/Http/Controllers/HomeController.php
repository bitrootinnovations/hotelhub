<?php

namespace App\Http\Controllers;

use App\Models\ClientMaster;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Client users belong on the client portal dashboard
        if (!is_null(auth()->user()->client_id)) {
            return redirect()->route('client.dashboard');
        }

        $today     = now()->toDateString();
        $thisMonth = now()->startOfMonth()->toDateString();

        // ── Top stat cards ───────────────────────────────────────────────────
        $totalRevenue    = Order::where('payment_status', 'paid')->sum('total_amount');
        $todayRevenue    = Order::where('payment_status', 'paid')->whereDate('checked_out_at', $today)->sum('total_amount');
        $totalOrders     = Order::whereNull('deleted_at')->count();
        $todayOrders     = Order::whereNull('deleted_at')->whereDate('created_at', $today)->count();
        $activeClients   = ClientMaster::whereNull('deleted_at')->where('status_id', 1)->count();
        $activeOrders    = Order::whereNull('deleted_at')
                                ->whereIn('status', ['pending', 'confirmed', 'served'])
                                ->where('payment_status', '!=', 'paid')
                                ->count();

        // Total subscription revenue
        $totalSubscription = ClientMaster::whereNull('deleted_at')
            ->whereNotNull('subscription_price')
            ->sum('subscription_price');

        // Clients expiring within 30 days
        $expiringClients = ClientMaster::whereNull('deleted_at')
            ->whereNotNull('subscription_end_date')
            ->whereDate('subscription_end_date', '>=', $today)
            ->whereDate('subscription_end_date', '<=', now()->addDays(30)->toDateString())
            ->orderBy('subscription_end_date')
            ->get(['client_id', 'client_name', 'image', 'subscription_end_date']);

        // ── Revenue & Orders chart (last 12 months) ──────────────────────────
        $chartData = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN payment_status="paid" THEN total_amount ELSE 0 END) as revenue')
            )
            ->whereNull('deleted_at')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        $chartLabels  = [];
        $chartOrders  = [];
        $chartRevenue = [];
        foreach ($chartData as $row) {
            $chartLabels[]  = date('M Y', mktime(0, 0, 0, $row->month, 1, $row->year));
            $chartOrders[]  = (int) $row->total_orders;
            $chartRevenue[] = round($row->revenue, 2);
        }

        // ── Top clients by order count ────────────────────────────────────────
        $topClients = ClientMaster::select('client_masters.client_id', 'client_masters.client_name', 'client_masters.image')
            ->leftJoin('orders', function ($j) {
                $j->on('orders.client_id', '=', 'client_masters.client_id')
                  ->whereNull('orders.deleted_at');
            })
            ->whereNull('client_masters.deleted_at')
            ->groupBy('client_masters.client_id', 'client_masters.client_name', 'client_masters.image')
            ->selectRaw('client_masters.client_id, client_masters.client_name, client_masters.image,
                         COUNT(orders.order_id) as order_count,
                         SUM(CASE WHEN orders.payment_status="paid" THEN orders.total_amount ELSE 0 END) as total_revenue')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // ── Recent orders ────────────────────────────────────────────────────
        $recentOrders = Order::select(
                'orders.order_id', 'orders.order_number', 'orders.status',
                'orders.total_amount', 'orders.order_type', 'orders.created_at',
                'client_masters.client_name', 'client_masters.image as client_image'
            )
            ->leftJoin('client_masters', 'orders.client_id', '=', 'client_masters.client_id')
            ->whereNull('orders.deleted_at')
            ->latest('orders.created_at')
            ->limit(5)
            ->get();

        // Menu names for recent orders
        $menuNamesByOrder = DB::table('order_items')
            ->join('menu_masters', 'order_items.menu_id', '=', 'menu_masters.menu_id')
            ->whereIn('order_items.order_id', $recentOrders->pluck('order_id'))
            ->select('order_items.order_id', 'menu_masters.menu_name')
            ->get()
            ->groupBy('order_id');

        // ── Platform overview counts ─────────────────────────────────────────
        $totalEmployees = DB::table('client_employees')->whereNull('deleted_at')->count();
        $totalMenuItems = DB::table('menu_masters')->whereNull('deleted_at')->where('status_id', 1)->count();

        return view('dashboard', compact(
            'totalRevenue', 'todayRevenue',
            'totalOrders',  'todayOrders',
            'activeClients', 'activeOrders',
            'totalSubscription',
            'expiringClients',
            'chartLabels', 'chartOrders', 'chartRevenue',
            'topClients', 'recentOrders', 'menuNamesByOrder',
            'totalEmployees', 'totalMenuItems'
        ));
    }
}
