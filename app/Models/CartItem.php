<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id', 'menu_id', 'quantity', 'unit_price', 'gst_percentage', 'notes',
    ];

    protected $casts = [
        'unit_price'     => 'float',
        'gst_percentage' => 'float',
    ];

    public function menu()
    {
        return $this->belongsTo(MenuMaster::class, 'menu_id', 'menu_id')
                    ->select(['menu_id', 'menu_name', 'food_type', 'image', 'price', 'gst_percentage']);
    }
}
