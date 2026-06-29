<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("settings:{$key}", 3600, fn () => Setting::query()->where('key', $key)->value('value') ?? $default);
    }

    public function set(string $key, mixed $value, string $group = 'general'): Setting
    {
        Cache::forget("settings:{$key}");
        Cache::forget('settings:public');

        return Setting::query()->updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
    }

    public function getPublicSettings(): array
    {
        return Cache::remember('settings:public', 3600, fn () => Setting::query()
            ->whereIn('key', ['company_name', 'currency_symbol', 'currency_code'])
            ->pluck('value', 'key')
            ->all());
    }
}
