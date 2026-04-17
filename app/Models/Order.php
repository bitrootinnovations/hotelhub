<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'client_id', 'table_id', 'user_id',
        'order_number', 'status', 'order_type',
        'subtotal', 'gst_amount', 'total_amount', 'notes',
        'payment_type', 'payment_status', 'checked_out_at',
    ];

    protected $casts = [
        'subtotal'        => 'float',
        'gst_amount'      => 'float',
        'total_amount'    => 'float',
        'checked_out_at'  => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function table()
    {
        return $this->belongsTo(TableMaster::class, 'table_id', 'table_id');
    }

    public function client()
    {
        return $this->belongsTo(ClientMaster::class, 'client_id', 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────────
    public static function generateOrderNumber(int $clientId): string
    {
        $prefix = 'ORD-' . str_pad($clientId, 3, '0', STR_PAD_LEFT) . '-';
        $last   = static::where('client_id', $clientId)
                         ->where('order_number', 'like', $prefix . '%')
                         ->lockForUpdate()
                         ->count();
        return $prefix . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
}
