<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuReportController extends Controller
{
    public function index()
    {
        $today = now()->format('Y-m-d');
        return view('client.menu-report', compact('today'));
    }

    public function data(Request $request)
    {
        $clientId = Auth::user()->client_id;
        $today    = now()->format('Y-m-d');
        $fromDate = $request->input('from_date', $today);
        $toDate   = $request->input('to_date', $today);

        $rows = DB::table('order_items')
            ->join('menu_masters',    'order_items.menu_id',  '=', 'menu_masters.menu_id')
            ->join('orders',          'order_items.order_id', '=', 'orders.order_id')
            ->leftJoin('category_masters', 'menu_masters.category_id', '=', 'category_masters.category_id')
            ->where('orders.client_id', $clientId)
            ->whereNull('orders.deleted_at')
            ->whereDate('orders.created_at', '>=', $fromDate)
            ->whereDate('orders.created_at', '<=', $toDate)
            ->groupBy('menu_masters.menu_id', 'menu_masters.menu_name', 'menu_masters.food_type', 'category_masters.category_name')
            ->select(
                'menu_masters.menu_id',
                'menu_masters.menu_name',
                DB::raw("COALESCE(category_masters.category_name, '-') as category_name"),
                'menu_masters.food_type',
                DB::raw('COUNT(DISTINCT orders.order_id) as total_orders'),
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->get()
            ->map(function ($row) {
                $row->food_type_label = $row->food_type == 1 ? 'Veg' : 'Non-Veg';
                return $row;
            });

        $summary = [
            'total_orders'  => $rows->sum('total_orders'),
            'total_revenue' => $rows->sum('total_revenue'),
        ];

        return response()->json([
            'data'    => $rows,
            'summary' => $summary,
        ]);
    }

    public function topSelling(Request $request)
    {
        $clientId = Auth::user()->client_id;
        $today    = now()->format('Y-m-d');
        $fromDate = $request->input('from_date', $today);
        $toDate   = $request->input('to_date', $today);

        $rows = DB::table('order_items')
            ->join('menu_masters',    'order_items.menu_id',  '=', 'menu_masters.menu_id')
            ->join('orders',          'order_items.order_id', '=', 'orders.order_id')
            ->leftJoin('category_masters', 'menu_masters.category_id', '=', 'category_masters.category_id')
            ->where('orders.client_id', $clientId)
            ->whereNull('orders.deleted_at')
            ->whereDate('orders.created_at', '>=', $fromDate)
            ->whereDate('orders.created_at', '<=', $toDate)
            ->groupBy('menu_masters.menu_id', 'menu_masters.menu_name', 'menu_masters.food_type', 'category_masters.category_name')
            ->orderByRaw('SUM(order_items.quantity) DESC')
            ->limit(10)
            ->select(
                'menu_masters.menu_id',
                'menu_masters.menu_name',
                DB::raw("COALESCE(category_masters.category_name, '-') as category_name"),
                'menu_masters.food_type',
                DB::raw('COUNT(DISTINCT orders.order_id) as total_orders'),
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->get()
            ->map(function ($row) {
                $row->food_type_label = $row->food_type == 1 ? 'Veg' : 'Non-Veg';
                return $row;
            });

        return response()->json(['data' => $rows]);
    }
}
