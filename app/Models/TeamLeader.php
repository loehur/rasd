<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class TeamLeader extends Model
{
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
}
