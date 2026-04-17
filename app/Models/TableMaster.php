<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TableMaster extends Model
{
    use SoftDeletes;

    protected $table      = 'table_masters';
    protected $primaryKey = 'table_id';

    protected $fillable = [
        'table_name', 'table_type_id', 'client_id', 'capacity', 'status_id', 'created_by',
    ];

    public function tableType()
    {
        return $this->belongsTo(TableTypeMaster::class, 'table_type_id', 'table_type_id');
    }

    public function client()
    {
        return $this->belongsTo(ClientMaster::class, 'client_id', 'client_id');
    }
}
