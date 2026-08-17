<?php

namespace Modules\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class GymRule extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['rule_number', 'rule_text', 'is_active', 'sort_order'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('gym_rules');
    }
    protected $fillable = [
        'rule_number',
        'rule_text',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get active rules for UI rendering.
     */
    public static function getActiveRules()
    {
        Cache::forget('gym_rules_active');

        return static::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('rule_number', 'asc')
            ->get();
    }
}
