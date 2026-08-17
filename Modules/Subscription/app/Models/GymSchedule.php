<?php

namespace Modules\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class GymSchedule extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['target_gender', 'days_label', 'start_time', 'end_time', 'time_label', 'is_off_day', 'notes'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('gym_schedules');
    }
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