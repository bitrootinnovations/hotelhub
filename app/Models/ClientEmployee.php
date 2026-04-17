<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class ClientEmployee extends Authenticatable implements JWTSubject
{
    use SoftDeletes;

    protected $table    = 'client_employees';
    protected $fillable = [
        'client_id', 'client_role_id', 'name', 'email',
        'password', 'phone', 'profile_image', 'status_id',
    ];

    protected $hidden = ['password', 'deleted_at'];

    // ── JWT ────────────────────────────────────────────────────────────────────
    public function getJWTIdentifier()
    {
        return 'employee_' . $this->id;
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'user_type'      => 'employee',
            'client_id'      => $this->client_id,
            'employee_id'    => $this->id,
            'client_role_id' => $this->client_role_id,
            'name'           => $this->name,
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────
    public function client()
    {
        return $this->belongsTo(ClientMaster::class, 'client_id', 'client_id');
    }

    public function role()
    {
        return $this->belongsTo(ClientRole::class, 'client_role_id', 'id');
    }
}
