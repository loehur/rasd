<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'phone_numbers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_id',
        'employee_name',
        'team_leader_name',
        'team_leader_id',
        'phone_number',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the staff that owns this phone number entry.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }
}
