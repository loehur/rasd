<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;

/**
 * TeamLeader Model
 *
 * This model now uses the staff table with role='tl'
 * It extends Staff and automatically filters by role
 */
class TeamLeader extends Staff
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staff';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'staff_id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Boot the model and apply global scope to filter by role='tl'
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically filter by role = 'tl'
        static::addGlobalScope('role', function ($builder) {
            $builder->where('role', 'tl');
        });

        // Automatically set role to 'tl' when creating
        static::creating(function ($model) {
            $model->role = 'tl';
        });
    }

    /**
     * Set default password: tl1230
     */
    public static function getDefaultPassword()
    {
        return 'tl1230';
    }

    /**
     * Get the staff members under this team leader.
     * Uses staff_id (which is the same as employee_id for TL)
     */
    public function staffMembers()
    {
        return $this->hasMany(Staff::class, 'team_leader_id', 'staff_id')
                    ->where('role', 'staff'); // Only get staff, not other TLs
    }

    /**
     * Get the attendances created by this team leader.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'team_leader_id', 'staff_id');
    }

    /**
     * Accessor for employee_id (alias for staff_id)
     */
    public function getEmployeeIdAttribute()
    {
        return $this->staff_id;
    }

    /**
     * Mutator for employee_id (alias for staff_id)
     */
    public function setEmployeeIdAttribute($value)
    {
        $this->attributes['staff_id'] = $value;
    }
}
