<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class TeamLeader extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'employee_id';

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

    protected $fillable = [
        'employee_id',
        'name',
        'password',
        'work_location',
        'position',
        'team',
        'team_quantity',
        'department',
        'hire_date',
        'rank',
        'first_day_tl',
        'warning_letter',
        'ojk_case',
        'former_tl',
        'area',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Set default password: tl1230
     */
    public static function getDefaultPassword()
    {
        return 'tl1230';
    }

    /**
     * Hash password before saving
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get the staff members under this team leader.
     */
    public function staffMembers()
    {
        return $this->hasMany(Staff::class, 'team_leader_id', 'employee_id');
    }

    /**
     * Get the attendances created by this team leader.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'team_leader_id', 'employee_id');
    }
}
