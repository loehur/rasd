<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'work_status',
        'staff_id',
        'name',
        'position',
        'superior',
        'department',
        'hire_date',
        'rank',
        'device',
        'report_day',
        'ranking_intervals',
        'group',
        'reason_for_resign',
        'proof',
        'status_code',
        'team_leader_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'hire_date' => 'date',
        'report_day' => 'date',
    ];

    /**
     * Get the staff that owns the attendance.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    /**
     * Get the team leader that created the attendance.
     */
    public function teamLeader()
    {
        return $this->belongsTo(TeamLeader::class, 'team_leader_id', 'employee_id');
    }
}
