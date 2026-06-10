<?php

namespace App\Services;

use App\Models\AISetting;
use Illuminate\Support\Facades\Http;
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
     * Test a provider connection by verifying the provider resolves
     * and sending a lightweight ping to the configured endpoint.
     */
    public function testProviderConnection(string $providerName): array
    {
        try {
            $this->aiManager->purge($providerName);
            $provider = $this->aiManager->instance($providerName);

            $url = rtrim((string) ($provider->additionalConfiguration()['url'] ?? ''), '/');
            $key = $provider->providerCredentials()['key'] ?? '';

            if (empty($url) || empty($key)) {
                return [
                    'success' => false,
                    'error' => 'Provider is not configured — missing URL or API key',
                ];
            }

            $response = Http::timeout(10)
                ->withToken($key)
                ->post("{$url}/chat/completions", [
                    'model' => config("ai.models.{$providerName}.default", 'openrouter/free'),
                    'messages' => [['role' => 'user', 'content' => 'Respond with exactly: OK']],
                    'max_tokens' => 10,
                ]);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'error' => "HTTP {$response->status()}: {$response->body()}",
                ];
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            return [
                'success' => true,
                'model' => $data['model'] ?? null,
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
