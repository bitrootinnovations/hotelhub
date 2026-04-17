<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuMaster extends Model
{
    use SoftDeletes;

    protected $table      = 'menu_masters';
    protected $primaryKey = 'menu_id';

    protected $fillable = [
        'menu_name', 'category_id', 'food_type', 'price',
        'gst_percentage', 'stock_type', 'quantity', 'image',
        'client_id', 'status_id', 'created_by',
    ];

    public function category()
    {
        return $this->belongsTo(CategoryMaster::class, 'category_id', 'category_id');
    }

    public function client()
    {
        return $this->belongsTo(ClientMaster::class, 'client_id', 'client_id');
    }

    public function getFoodTypeLabelAttribute()
    {
        return $this->food_type == 1 ? 'Veg' : 'Non-Veg';
    }
}
