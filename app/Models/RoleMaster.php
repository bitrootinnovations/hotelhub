<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleMaster extends Model
{
    use SoftDeletes;

    protected $table      = 'role_masters';
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_name',
        'description',
        'status_id',
    ];

    protected $casts = [
        'status_id' => 'integer',
    ];

    public function permissions()
    {
        return $this->hasMany(PermissionMaster::class, 'role_id', 'role_id');
    }

    public function isAdmin(): bool
    {
        return $this->role_id === 1;
    }
}
