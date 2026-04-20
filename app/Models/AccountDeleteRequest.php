<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountDeleteRequest extends Model
{
    protected $fillable = [
        'full_name', 'email', 'phone', 'reason', 'notes', 'status', 'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            'no_longer_using'   => 'No longer using the app',
            'privacy_concerns'  => 'Privacy concerns',
            'switching_service' => 'Switching to another service',
            'data_concerns'     => 'Concerns about data usage',
            default             => 'Other',
        };
    }
}
