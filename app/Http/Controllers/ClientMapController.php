<?php

namespace App\Http\Controllers;

use App\Models\ClientMaster;
use Illuminate\Support\Facades\DB;

class ClientMapController extends Controller
{
    public function index()
    {
        return view('clients.map');
    }

    public function data()
    {
        $today   = now()->toDateString();

        $clients = ClientMaster::select([
                'client_id', 'client_name', 'city', 'contact_number',
                'latitude', 'longitude', 'image',
                'subscription_type', 'subscription_end_date',
                'subscription_price', 'status_id',
            ])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // Total sales per client
        $totalSales = DB::table('orders')
            ->select('client_id', DB::raw('SUM(total_amount) as total'))
            ->groupBy('client_id')
            ->pluck('total', 'client_id');

        // Today's sales per client
        $todaySales = DB::table('orders')
            ->select('client_id', DB::raw('SUM(total_amount) as total'))
            ->whereDate('created_at', $today)
            ->groupBy('client_id')
            ->pluck('total', 'client_id');

        // Total orders per client
        $totalOrders = DB::table('orders')
            ->select('client_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('client_id')
            ->pluck('cnt', 'client_id');

        $data = $clients->map(function ($c) use ($totalSales, $todaySales, $totalOrders) {
            $expiry  = $c->subscription_end_date
                ? \Carbon\Carbon::parse($c->subscription_end_date)
                : null;

            $isExpired = $expiry ? $expiry->isPast() : false;

            return [
                'client_id'      => $c->client_id,
                'name'           => $c->client_name,
                'city'           => $c->city,
                'contact'        => $c->contact_number,
                'lat'            => (float) $c->latitude,
                'lng'            => (float) $c->longitude,
                'logo'           => $c->image
                    ? config('app.url') . '/storage/app/public/' . $c->image
                    : null,
                'status'         => $c->status_id == 1 ? 'Active' : 'Inactive',
                'plan'           => $c->subscription_type ?? 'None',
                'plan_amount'    => $c->subscription_price ?? 0,
                'expiry_date'    => $expiry ? $expiry->format('d M Y') : 'No expiry',
                'is_expired'     => $isExpired,
                'total_sales'    => number_format($totalSales[$c->client_id] ?? 0, 2),
                'today_sales'    => number_format($todaySales[$c->client_id] ?? 0, 2),
                'total_orders'   => $totalOrders[$c->client_id] ?? 0,
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }
}
