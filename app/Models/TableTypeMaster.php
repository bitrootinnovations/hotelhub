<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TableTypeMaster extends Model
{
    use SoftDeletes;

    protected $table      = 'table_type_masters';
    protected $primaryKey = 'table_type_id';

    protected $fillable = [
        'type_name', 'client_id', 'status_id', 'created_by',
    ];

    public function client()
    {
        return $this->belongsTo(ClientMaster::class, 'client_id', 'client_id');
    }

    public function tables()
    {
        return $this->hasMany(TableMaster::class, 'table_type_id', 'table_type_id');
    }
}
