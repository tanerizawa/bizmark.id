<?php

namespace App\Services;

use App\Models\AISetting;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\AiManager;

class AiRuntimeConfigService
{
    public function __construct(
        private AiManager $aiManager,
    ) {}

    /**
     * Apply a provider config change at runtime.
     *
     * 1. Persists to DB via AISettingService
     * 2. Updates the config() array immediately
     * 3. Purges the cached AiManager provider instance
     */
    public function applyProviderChange(string $key, mixed $value, string $reason): AISetting
    {
        $setting = AISettingService::set($key, $value, $reason);

        if ($key === 'default_provider') {
            config(['ai.default' => $value]);
        } elseif (str_starts_with($key, 'providers.')) {
            $parts = explode('.', $key);
            $providerName = $parts[1] ?? null;

            $configKey = 'ai.' . $key;
            config([$configKey => $value]);

            if ($providerName) {
                $this->aiManager->purge($providerName);
                Log::info('AiRuntimeConfig: purged cached provider', [
                    'provider' => $providerName,
                    'changed_key' => $key,
                ]);
            }
        } elseif (str_starts_with($key, 'models.')) {
            $configKey = 'ai.' . $key;
            config([$configKey => $value]);
        }

        return $setting;
    }

    /**
     * Test a provider connection by sending a minimal chat completion request.
     */
    public function testProviderConnection(string $providerName): array
    {
        try {
            $this->aiManager->purge($providerName);

            $provider = $this->aiManager->instance($providerName);

            $result = $provider->chat()->create([
                'model' => config("ai.models.{$providerName}.default", 'openrouter/free'),
                'messages' => [
                    ['role' => 'user', 'content' => 'Respond with exactly: OK'],
                ],
                'max_tokens' => 10,
            ]);

            $content = $result['choices'][0]['message']['content'] ?? '';
            $success = str_contains($content, 'OK');

            return [
                'success' => $success,
                'model' => $result['model'] ?? null,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get the current SDK status: which providers are configured, what's the default.
     */
    public function getSdkStatus(): array
    {
        $providers = config('ai.providers', []);
        $status = [];

        foreach ($providers as $name => $config) {
            $hasKey = filled($config['key'] ?? '');
            $url = $config['url'] ?? '';

            $status[$name] = [
                'configured' => $hasKey,
                'driver' => $config['driver'] ?? 'unknown',
                'url' => $url,
                'default_model' => config("ai.models.{$name}.default", 'unknown'),
                'is_default' => config('ai.default') === $name,
            ];
        }

        return $status;
    }
}
