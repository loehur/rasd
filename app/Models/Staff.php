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
        'ojk_case',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'hire_date' => 'date',
        'ojk_case' => 'integer',
    ];
}
