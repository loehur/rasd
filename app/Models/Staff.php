<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Staff extends Model
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_id',
        'role',
        'name',
        'password',
        'phone_number',
        'email',
        'position',
        'department',
        'group',
        'area',
        'work_location',
        'hire_date',
        'first_day_tl',
        'rank',
        'device',
        'team_leader_id',
        'former_tl',
        'warning_letter',
        'staff_status',
        'ojk_case',
        'notes',
    ];

    /**
     * The attributes that should be hidden.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'hire_date' => 'date:Y-m-d',
        'first_day_tl' => 'date:Y-m-d',
        'ojk_case' => 'integer',
    ];

    /**
     * Hash password before saving (for TL role)
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    /**
     * Scope to get only staff members (role = staff)
     */
    public function scopeStaffOnly($query)
    {
        return $query->where('role', 'staff');
    }

    /**
     * Scope to get only team leaders (role = tl)
     */
    public function scopeTeamLeaders($query)
    {
        return $query->where('role', 'tl');
    }

    /**
     * Get the team leader that supervises this staff.
     */
    public function teamLeader()
    {
        return $this->belongsTo(Staff::class, 'team_leader_id', 'staff_id')->where('role', 'tl');
    }

    /**
     * Get the attendances for this staff.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'staff_id', 'staff_id');
    }
}
