<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $primaryKey = 'cart_id';

    protected $fillable = [
        'client_id', 'table_id', 'user_ref', 'order_type',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'cart_id');
    }

    public function table()
    {
        return $this->belongsTo(TableMaster::class, 'table_id', 'table_id');
    }

    /** Resolve or create a cart for the current user session */
    public static function forUser(int $clientId, string $userRef, ?int $tableId = null, string $orderType = 'dine_in'): self
    {
        $cart = static::where('client_id', $clientId)
                      ->where('user_ref', $userRef)
                      ->first();

        if (!$cart) {
            $cart = static::create([
                'client_id'  => $clientId,
                'user_ref'   => $userRef,
                'table_id'   => $tableId,
                'order_type' => $orderType,
            ]);
        } else {
            // Update table/order_type if provided
            if ($tableId !== null)  $cart->table_id   = $tableId;
            if ($orderType)         $cart->order_type  = $orderType;
            $cart->save();
        }

        return $cart;
    }

    public function getTotalAttribute(): array
    {
        $subtotal = 0; $gst = 0;
        foreach ($this->items as $item) {
            $line     = round($item->unit_price * $item->quantity, 2);
            $lineGst  = round($line * $item->gst_percentage / 100, 2);
            $subtotal += $line;
            $gst      += $lineGst;
        }
        return [
            'subtotal'     => round($subtotal, 2),
            'gst_amount'   => round($gst, 2),
            'total_amount' => round($subtotal + $gst, 2),
        ];
    }
}
