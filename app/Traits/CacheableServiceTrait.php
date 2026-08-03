<?php

namespace App\Traits;

use Closure;
use Illuminate\Support\Facades\Cache;

trait CacheableServiceTrait
{
    /**
     * Safely remember cache with self-healing fallback for stale classes or failures.
     */
    public function rememberSafe(string $key, int $ttl, Closure $callback)
    {
        $cached = Cache::remember($key, $ttl, $callback);

        if ($cached instanceof \__PHP_Incomplete_Class) {
            Cache::forget($key);
            return $callback();
        }

        return $cached;
    }
}
