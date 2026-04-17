<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintJob extends Model
{
    protected $fillable = [
        'client_id', 'type', 'payload', 'status', 'picked_at', 'done_at',
    ];

    protected $casts = [
        'payload'   => 'array',
        'picked_at' => 'datetime',
        'done_at'   => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(ClientMaster::class, 'client_id', 'client_id');
    }
}
