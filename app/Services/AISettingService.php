<?php

namespace App\Services;

use App\Models\AISetting;
use App\Models\AISettingHistory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AISettingService
{
    protected static array $cache = [];

    protected const CACHE_PREFIX = 'ai_setting:';

    protected const CACHE_TTL = 3600; // 1 hour

    /**
     * Get single setting value
     */
    public static function get(string $key, $default = null)
    {
        // Memory cache first
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        // Laravel cache second
        $cacheKey = static::CACHE_PREFIX.$key;
        $value = Cache::remember($cacheKey, static::CACHE_TTL, function () use ($key, $default) {
            $setting = AISetting::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });

        // Store in memory
        static::$cache[$key] = $value;

        return $value;
    }

    /**
     * Get all settings by category
     */
    public static function getByCategory(string $category): array
    {
        $cacheKey = static::CACHE_PREFIX.'category:'.$category;

        return Cache::remember($cacheKey, static::CACHE_TTL, function () use ($category) {
            return AISetting::where('category', $category)
                ->orderBy('display_order')
                ->orderBy('key')
                ->get()
                ->mapWithKeys(fn ($s) => [$s->key => $s->value])
                ->toArray();
        });
    }

    /**
     * Set setting value
     */
    public static function set(string $key, $value, ?string $reason = null): AISetting
    {
        $setting = AISetting::where('key', $key)->first();

        $oldValue = $setting ? $setting->getRawOriginal('value') : null;

        if ($setting) {
            $setting->update([
                'value' => $value,
                'updated_by' => auth()->id(),
            ]);
        } else {
            $category = explode('.', $key)[0] ?? 'global';
            $setting = AISetting::create([
                'category' => $category,
                'key' => $key,
                'value' => $value,
                'created_by' => auth()->id(),
            ]);
        }

        // Log to history
        static::logChange($setting, $oldValue, $value, $reason);

        // Clear cache
        static::forget($key);

        Log::info('AI Setting updated', [
            'key' => $key,
            'user' => auth()->id(),
            'reason' => $reason,
        ]);

        return $setting;
    }

    /**
     * Log setting change to history
     */
    protected static function logChange(AISetting $setting, $oldValue, $newValue, ?string $reason = null): void
    {
        if (! auth()->check() || ! ($user = auth()->user())) {
            return;
        }

        $req = request();

        AISettingHistory::create([
            'setting_id' => $setting->id,
            'key' => $setting->key,
            'old_value' => $oldValue,
            'new_value' => is_array($newValue) ? json_encode($newValue) : (string) $newValue,
            'changed_by_name' => $user->name,
            'changed_by' => auth()->id(),
            'reason' => $reason,
            'ip_address' => $req->ip() ?? '127.0.0.1',
            'user_agent' => $req->userAgent() ?? 'console',
        ]);
    }

    /**
     * Batch update settings
     */
    public static function updateBatch(array $settings, ?string $reason = null): void
    {
        foreach ($settings as $key => $value) {
            static::set($key, $value, $reason);
        }
    }

    /**
     * Forget cached value
     */
    public static function forget(string $key): void
    {
        $cacheKey = static::CACHE_PREFIX.$key;
        Cache::forget($cacheKey);
        unset(static::$cache[$key]);

        // Also forget category cache
        $category = explode('.', $key)[0] ?? null;
        if ($category) {
            Cache::forget(static::CACHE_PREFIX.'category:'.$category);
        }
    }

    /**
     * Clear all caches
     */
    public static function clearAllCache(): void
    {
        static::$cache = [];

        // Clear all ai_setting:* keys
        $keys = Cache::get('ai_setting_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Log::info('AI Settings cache cleared');
    }

    /**
     * Reset to default value
     */
    public static function reset(string $key): void
    {
        $setting = AISetting::where('key', $key)->first();

        if ($setting && $setting->default_value !== null) {
            static::set($key, $setting->default_value, 'Reset to default');
        }
    }
}
