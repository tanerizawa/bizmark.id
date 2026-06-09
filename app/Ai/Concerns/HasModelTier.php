<?php

namespace App\Ai\Concerns;

trait HasModelTier
{
    public string $tier = 'free';

    public string $aiProvider = 'omniroute';

    public function usingProvider(string $provider): static
    {
        $this->aiProvider = $provider;

        return $this;
    }

    public function modelConfig(): array
    {
        $provider = $this->aiProvider;
        $config = config("ai.models.{$provider}.{$this->tier}", config("ai.models.{$provider}.free"));

        return [
            'model' => $config['primary'] ?? config("ai.models.{$provider}.default", 'big-pickle'),
            'fallback' => $config['fallback'] ?? $config['primary'] ?? 'big-pickle',
        ];
    }

    public function promptOptions(): array
    {
        return [
            'model' => $this->modelConfig()['model'],
            'provider' => $this->aiProvider,
            'timeout' => 120,
        ];
    }

    public function withFailoverProviders(): array
    {
        $omnirouteModel = $this->tier === 'premium'
            ? config('ai.models.omniroute.premium.primary', 'openrouter/free')
            : config('ai.models.omniroute.free.primary', 'openrouter/free');

        return [
            [
                'provider' => $this->aiProvider,
                'model' => $this->modelConfig()['model'],
                'timeout' => 120,
            ],
            [
                'provider' => 'omniroute',
                'model' => $omnirouteModel,
                'timeout' => 60,
            ],
            [
                'provider' => 'openrouter',
                'model' => config('ai.models.openrouter.default', 'openrouter/free'),
                'timeout' => 60,
            ],
        ];
    }
}
