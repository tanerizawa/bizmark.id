<?php

namespace App\Providers;

use App\Models\AISetting;
use Illuminate\Support\ServiceProvider;

class AiConfigSyncServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\AiRuntimeConfigService::class);
    }

    public function boot(): void
    {
        if (! app()->runningInConsole() && ! $this->app->runningUnitTests()) {
            try {
                $this->syncProviderSettings();
            } catch (\Exception $e) {
                logger()->warning('AiConfigSync: could not sync provider settings', [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function syncProviderSettings(): void
    {
        $settings = AISetting::where('category', 'provider')->get(['key', 'value', 'data_type']);

        foreach ($settings as $setting) {
            if ($setting->key === 'default_provider') {
                config(['ai.default' => $setting->value]);
                continue;
            }

            $configKey = 'ai.' . $setting->key;
            config([$configKey => $setting->value]);
        }
    }
}
