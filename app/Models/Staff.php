<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'name',
        'phone_number',
        'email',
        'position',
        'department',
        'superior',
        'group',
        'area',
        'work_location',
        'hire_date',
        'rank',
        'device',
        'team_leader_id',
        'warning_letter',
        'staff_status',
        'ojk_case',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'hire_date' => 'date:Y-m-d',
        'ojk_case' => 'integer',
    ];

    /**
     * Get the team leader that supervises this staff.
     */
    public function teamLeader()
    {
        return $this->belongsTo(TeamLeader::class, 'team_leader_id', 'employee_id');
    }

    /**
     * Get the attendances for this staff.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'staff_id', 'staff_id');
    }
}
