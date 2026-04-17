<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionMaster extends Model
{
    protected $table      = 'permission_masters';
    protected $primaryKey = 'permission_id';

    protected $fillable = [
        'role_id',
        'menu_name',
        'can_view',
        'can_add',
        'can_edit',
        'can_delete',
    ];

    protected $casts = [
        'can_view'   => 'integer',
        'can_add'    => 'integer',
        'can_edit'   => 'integer',
        'can_delete' => 'integer',
    ];

    public function role()
    {
        return $this->belongsTo(RoleMaster::class, 'role_id', 'role_id');
    }
}
