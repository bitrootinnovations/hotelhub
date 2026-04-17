<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CategoryMaster;
use App\Models\MenuMaster;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TableMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TableOrderController extends Controller
{
    private function clientId(): int
    {
        return Auth::user()->client_id;
    }

    // ── Show table page ────────────────────────────────────────────────────────
    public function show(int $tableId)
    {
        $clientId = $this->clientId();

        $table = TableMaster::where('client_id', $clientId)
            ->with('tableType')
            ->findOrFail($tableId);

        // Active (unpaid) orders for this table, newest first
        $activeOrders = Order::where('client_id', $clientId)
            ->where('table_id', $tableId)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'served'])
            ->where('payment_status', '!=', 'paid')
            ->with(['items.menu'])
            ->orderBy('order_id', 'desc')
            ->get();

        // Menu grouped by category for this client
        $categories = CategoryMaster::where(function ($q) use ($clientId) {
                $q->where('client_id', $clientId)->orWhereNull('client_id');
            })
            ->where('status_id', 1)
            ->with(['menus' => function ($q) use ($clientId) {
                $q->where('client_id', $clientId)
                  ->where('status_id', 1)
                  ->orderBy('menu_name');
            }])
            ->get()
            ->filter(fn($c) => $c->menus->isNotEmpty());

        return view('client.table-order', compact('table', 'activeOrders', 'categories'));
    }

    // ── Place a new order (KOT) ────────────────────────────────────────────────
    public function placeOrder(Request $request, int $tableId)
    {
        $clientId = $this->clientId();

        $table = TableMaster::where('client_id', $clientId)->findOrFail($tableId);

        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.menu_id'   => 'required|integer',
            'items.*.qty'       => 'required|integer|min:1',
        ]);

        // Validate all menus belong to this client
        $menuIds  = collect($request->items)->pluck('menu_id')->unique()->values();
        $validIds = MenuMaster::where('client_id', $clientId)
            ->where('status_id', 1)
            ->whereIn('menu_id', $menuIds)
            ->pluck('menu_id');

        if ($validIds->count() !== $menuIds->count()) {
            return back()->withErrors(['items' => 'One or more selected items are invalid.']);
        }

        $menus = MenuMaster::whereIn('menu_id', $validIds)->get()->keyBy('menu_id');

        DB::transaction(function () use ($request, $clientId, $tableId, $menus) {
            $subtotal  = 0;
            $gstAmount = 0;

            $lines = [];
            foreach ($request->items as $item) {
                $menu      = $menus[$item['menu_id']];
                $qty       = (int) $item['qty'];
                $unitPrice = $menu->price;
                $gstPct    = $menu->gst_percentage ?? 0;
                $lineTotal = round($unitPrice * $qty, 2);
                $lineGst   = round($lineTotal * $gstPct / 100, 2);

                $subtotal  += $lineTotal;
                $gstAmount += $lineGst;

                $lines[] = [
                    'menu_id'        => $menu->menu_id,
                    'quantity'       => $qty,
                    'unit_price'     => $unitPrice,
                    'gst_percentage' => $gstPct,
                    'total_price'    => round($lineTotal + $lineGst, 2),
                    'notes'          => $item['notes'] ?? null,
                ];
            }

            $total = round($subtotal + $gstAmount, 2);

            $order = Order::create([
                'client_id'      => $clientId,
                'table_id'       => $tableId,
                'user_id'        => Auth::id(),
                'order_number'   => Order::generateOrderNumber($clientId),
                'status'         => 'pending',
                'order_type'     => 'dine_in',
                'subtotal'       => $subtotal,
                'gst_amount'     => $gstAmount,
                'total_amount'   => $total,
                'payment_status' => 'unpaid',
            ]);

            foreach ($lines as &$line) {
                $line['order_id'] = $order->order_id;
            }
            OrderItem::insert($lines);
        });

        return redirect()->route('client.table.show', $tableId)
            ->with('success', 'Order placed successfully.');
    }

    // ── Update order status (AJAX) ─────────────────────────────────────────────
    public function updateStatus(Request $request, int $orderId)
    {
        $clientId = $this->clientId();

        $order = Order::where('client_id', $clientId)->findOrFail($orderId);

        $allowed = ['pending', 'confirmed', 'preparing', 'served', 'cancelled'];
        $status  = $request->input('status');

        if (!in_array($status, $allowed)) {
            return response()->json(['success' => false, 'message' => 'Invalid status.'], 422);
        }

        $order->status = $status;
        $order->save();

        return response()->json(['success' => true, 'status' => $status]);
    }

    // ── Checkout order ─────────────────────────────────────────────────────────
    public function checkout(Request $request, int $orderId)
    {
        $clientId = $this->clientId();

        $order = Order::where('client_id', $clientId)->findOrFail($orderId);

        $request->validate([
            'payment_type' => 'required|in:Cash,UPI,Card,Online,Other',
        ]);

        $order->update([
            'payment_status' => 'paid',
            'payment_type'   => $request->payment_type,
            'status'         => 'served',
            'checked_out_at' => now(),
        ]);

        $tableId = $order->table_id;

        return redirect()->route('client.table.show', $tableId)
            ->with('success', 'Order #' . $order->order_number . ' checked out successfully.');
    }

    // ── Remove an item from an order (AJAX) ───────────────────────────────────
    public function removeItem(Request $request, int $itemId)
    {
        $clientId = $this->clientId();

        $item = OrderItem::whereHas('order', function ($q) use ($clientId) {
            $q->where('client_id', $clientId)
              ->whereIn('status', ['pending', 'confirmed', 'preparing'])
              ->where('payment_status', '!=', 'paid');
        })->findOrFail($itemId);

        $orderId = $item->order_id;
        $item->delete();

        // Recalculate order totals
        $order = Order::find($orderId);
        if ($order) {
            $remaining = OrderItem::where('order_id', $orderId)->get();
            if ($remaining->isEmpty()) {
                $order->delete();
                return response()->json(['success' => true, 'order_deleted' => true]);
            }
            $subtotal  = $remaining->sum('total_price') / (1 + ($remaining->avg('gst_percentage') / 100));
            $gstAmount = $remaining->sum(fn($i) => $i->total_price - ($i->unit_price * $i->quantity));
            $order->update([
                'subtotal'     => round($remaining->sum(fn($i) => $i->unit_price * $i->quantity), 2),
                'gst_amount'   => round($remaining->sum(fn($i) => $i->total_price - ($i->unit_price * $i->quantity)), 2),
                'total_amount' => round($remaining->sum('total_price'), 2),
            ]);
        }

        return response()->json(['success' => true, 'order_deleted' => false]);
    }
}
