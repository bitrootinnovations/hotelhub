<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryMaster extends Model
{
    use SoftDeletes;

    protected $table      = 'category_masters';
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_name', 'client_id', 'status_id', 'created_by',
    ];

    public function client()
    {
        return $this->belongsTo(ClientMaster::class, 'client_id', 'client_id');
    }
}
