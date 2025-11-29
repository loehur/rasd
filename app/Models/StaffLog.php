<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staff_logs';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_id',
        'change_type',
        'old_value',
        'new_value',
        'changed_by',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the staff that this log belongs to.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }
}
