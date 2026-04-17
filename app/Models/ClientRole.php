<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientRole extends Model
{
    use SoftDeletes;

    protected $table    = 'client_roles';
    protected $fillable = [
        'client_id', 'role_name', 'status_id',
        'can_take_orders', 'can_checkout', 'can_manage_menu',
        'can_manage_employees', 'can_view_reports',
    ];

    protected $casts = [
        'can_take_orders'     => 'boolean',
        'can_checkout'        => 'boolean',
        'can_manage_menu'     => 'boolean',
        'can_manage_employees'=> 'boolean',
        'can_view_reports'    => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(ClientMaster::class, 'client_id', 'client_id');
    }

    public function employees()
    {
        return $this->hasMany(ClientEmployee::class, 'client_role_id', 'id');
    }
}
