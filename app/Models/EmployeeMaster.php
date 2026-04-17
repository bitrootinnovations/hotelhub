<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeMaster extends Model
{
    use SoftDeletes;

    protected $table      = 'employee_masters';
    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'employee_name',
        'email_id',
        'contact_number',
        'role_id',
        'designation',
        'department',
        'address',
        'profile_image',
        'status_id',
        'created_by',
    ];

    public function role()
    {
        return $this->belongsTo(RoleMaster::class, 'role_id', 'role_id');
    }

    public function getStatusLabelAttribute()
    {
        return $this->status_id == 1 ? 'Active' : 'Inactive';
    }

    public function isActive()
    {
        return $this->status_id == 1;
    }
}
