<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'order_id', 'menu_id', 'quantity',
        'unit_price', 'gst_percentage', 'total_price', 'notes',
    ];

    protected $casts = [
        'unit_price'     => 'float',
        'gst_percentage' => 'float',
        'total_price'    => 'float',
    ];

    public function menu()
    {
        return $this->belongsTo(MenuMaster::class, 'menu_id', 'menu_id')
                    ->select(['menu_id', 'menu_name', 'food_type', 'image']);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
