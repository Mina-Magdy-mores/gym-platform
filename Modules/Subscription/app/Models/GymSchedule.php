<?php

namespace Modules\Subscription\Models;

use Illuminate\Database\Eloquent\Model;

class GymSchedule extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'target_gender',
        'days_label',
        'start_time',
        'end_time',
        'time_label',
        'is_off_day',
        'notes',
    ];

    /**
     * Scope a query to filter men's schedules.
     */
    public function scopeMen($query)
    {
        return $query->where('target_gender', 'men');
    }

    /**
     * Scope a query to filter women's schedules.
     */
    public function scopeWomen($query)
    {
        return $query->where('target_gender', 'women');
    }
}