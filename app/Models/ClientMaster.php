<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class ClientMaster extends Authenticatable implements JWTSubject
{
    use SoftDeletes;

    protected $table      = 'client_masters';
    protected $primaryKey = 'client_id';

    protected $fillable = [
        'client_name', 'image', 'address', 'city', 'state', 'pincode',
        'latitude', 'longitude', 'contact_number', 'email_id', 'password',
        'aadhar_image', 'gst_number', 'upi_id', 'subscription_type', 'subscription_price',
        'subscription_start_date', 'subscription_end_date', 'plan_type', 'status_id', 'created_by',
    ];

    protected $hidden = ['password', 'deleted_at'];

    protected $casts = [
        'latitude'                => 'float',
        'longitude'               => 'float',
        'status_id'               => 'integer',
        'subscription_price'      => 'float',
        'subscription_start_date' => 'date',
        'subscription_end_date'   => 'date',
    ];

    // ── JWT ────────────────────────────────────────────────────────────────────
    public function getJWTIdentifier()
    {
        return 'client_' . $this->client_id;
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'user_type' => 'client',
            'client_id' => $this->client_id,
            'name'      => $this->client_name,
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────
    public function printers()
    {
        return $this->hasMany(\App\Models\Printer::class, 'client_id', 'client_id');
    }

    public function roles()
    {
        return $this->hasMany(ClientRole::class, 'client_id', 'client_id');
    }

    public function employees()
    {
        return $this->hasMany(ClientEmployee::class, 'client_id', 'client_id');
    }

    // ── Accessors ──────────────────────────────────────────────────────────────
    public function getSubscriptionStatusAttribute(): string
    {
        if (!$this->subscription_type) return 'None';
        if (!$this->subscription_end_date) return 'Active';
        return $this->subscription_end_date->isPast() ? 'Expired' : 'Active';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_id) {
            1 => 'Active', 2 => 'Inactive', 3 => 'Suspended', 4 => 'Trial',
            default => 'Unknown',
        };
    }

    public function isActive(): bool
    {
        return $this->status_id === 1;
    }
}
