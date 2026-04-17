<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'role_id', 'client_id', 'status_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime'];

    // ── JWT ────────────────────────────────────────────────────────────────────
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'client_id' => $this->client_id,
            'role_id'   => $this->role_id,
            'name'      => $this->name,
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────
    public function role()
    {
        return $this->belongsTo(RoleMaster::class, 'role_id', 'role_id');
    }

    public function client()
    {
        return $this->belongsTo(ClientMaster::class, 'client_id', 'client_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role_id == 1;
    }
}
