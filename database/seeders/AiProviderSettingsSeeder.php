<?php

namespace Database\Seeders;

use App\Models\AISetting;
use Illuminate\Database\Seeder;

class AiProviderSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $providerDefinitions = [
            'ollama' => [
                'driver' => 'ollama',
                'config' => [
                    'providers.ollama.key' => ['data_type' => 'string', 'is_encrypted' => true, 'default' => env('OLLAMA_API_KEY', ''), 'order' => 0],
                    'providers.ollama.url' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OLLAMA_URL', 'http://localhost:11434'), 'order' => 1],
                ],
                'models' => [
                    'models.ollama.default' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OLLAMA_MODEL', 'llama3'), 'order' => 10],
                ],
            ],
            'openrouter' => [
                'driver' => 'openrouter',
                'config' => [
                    'providers.openrouter.key' => ['data_type' => 'string', 'is_encrypted' => true, 'default' => env('OPENROUTER_API_KEY'), 'order' => 0],
                    'providers.openrouter.url' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'), 'order' => 1],
                ],
                'models' => [
                    'models.openrouter.default' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OPENROUTER_DEFAULT_MODEL', 'openrouter/free'), 'order' => 10],
                    'models.openrouter.free.primary' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OPENROUTER_FREE_PRIMARY_MODEL', 'openrouter/free'), 'order' => 11],
                    'models.openrouter.free.fallback' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OPENROUTER_FREE_FALLBACK_MODEL', 'openrouter/free'), 'order' => 12],
                    'models.openrouter.premium.primary' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OPENROUTER_PREMIUM_PRIMARY_MODEL', 'openrouter/free'), 'order' => 13],
                    'models.openrouter.premium.fallback' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OPENROUTER_PREMIUM_FALLBACK_MODEL', 'openrouter/free'), 'order' => 14],
                ],
            ],
            'omniroute' => [
                'driver' => 'openrouter',
                'config' => [
                    'providers.omniroute.key' => ['data_type' => 'string', 'is_encrypted' => true, 'default' => env('OMNIROUTE_API_KEY', ''), 'order' => 0],
                    'providers.omniroute.url' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OMNIROUTE_BASE_URL', 'http://localhost:20128/v1'), 'order' => 1],
                ],
                'models' => [
                    'models.omniroute.default' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OMNIROUTE_DEFAULT_MODEL', 'openrouter/free'), 'order' => 10],
                    'models.omniroute.free.primary' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OMNIROUTE_FREE_PRIMARY_MODEL', 'openrouter/free'), 'order' => 11],
                    'models.omniroute.free.fallback' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OMNIROUTE_FREE_FALLBACK_MODEL', 'openrouter/free'), 'order' => 12],
                    'models.omniroute.premium.primary' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OMNIROUTE_PREMIUM_PRIMARY_MODEL', 'openrouter/free'), 'order' => 13],
                    'models.omniroute.premium.fallback' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OMNIROUTE_PREMIUM_FALLBACK_MODEL', 'openrouter/free'), 'order' => 14],
                ],
            ],
            'omniroute2' => [
                'driver' => 'openrouter',
                'config' => [
                    'providers.omniroute2.key' => ['data_type' => 'string', 'is_encrypted' => true, 'default' => env('OMNIROUTE2_API_KEY'), 'order' => 0],
                    'providers.omniroute2.url' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OMNIROUTE2_BASE_URL', 'http://localhost:20129/v1'), 'order' => 1],
                ],
                'models' => [
                    'models.omniroute2.default' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OMNIROUTE2_DEFAULT_MODEL', 'Gratis'), 'order' => 10],
                    'models.omniroute2.free.primary' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OMNIROUTE2_FREE_PRIMARY_MODEL', 'Gratis'), 'order' => 11],
                    'models.omniroute2.free.fallback' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OMNIROUTE2_FREE_FALLBACK_MODEL', 'Gratis'), 'order' => 12],
                    'models.omniroute2.premium.primary' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OMNIROUTE2_PREMIUM_PRIMARY_MODEL', 'Gratis'), 'order' => 13],
                    'models.omniroute2.premium.fallback' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OMNIROUTE2_PREMIUM_FALLBACK_MODEL', 'Gratis'), 'order' => 14],
                ],
            ],
            'opencode' => [
                'driver' => 'openai',
                'config' => [
                    'providers.opencode.key' => ['data_type' => 'string', 'is_encrypted' => true, 'default' => env('OPENCODE_API_KEY'), 'order' => 0],
                    'providers.opencode.url' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OPENCODE_BASE_URL', 'https://opencode.ai/zen/v1'), 'order' => 1],
                ],
                'models' => [
                    'models.opencode.default' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OPENCODE_DEFAULT_MODEL', 'big-pickle'), 'order' => 10],
                    'models.opencode.free.primary' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OPENCODE_FREE_PRIMARY_MODEL', 'big-pickle'), 'order' => 11],
                    'models.opencode.free.fallback' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OPENCODE_FREE_FALLBACK_MODEL', 'big-pickle'), 'order' => 12],
                    'models.opencode.premium.primary' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OPENCODE_PREMIUM_PRIMARY_MODEL', 'big-pickle'), 'order' => 13],
                    'models.opencode.premium.fallback' => ['data_type' => 'string', 'is_encrypted' => false, 'default' => env('OPENCODE_PREMIUM_FALLBACK_MODEL', 'big-pickle'), 'order' => 14],
                ],
            ],
        ];

        $descriptionTemplates = [
            'key' => 'API key for :provider — encrypted at rest',
            'url' => 'Base URL for :provider API endpoint',
            'default' => 'Default model name for :provider',
            'free.primary' => 'Free tier primary model for :provider',
            'free.fallback' => 'Free tier fallback model for :provider',
            'premium.primary' => 'Premium tier primary model for :provider',
            'premium.fallback' => 'Premium tier fallback model for :provider',
        ];

        $order = 0;
        foreach ($providerDefinitions as $providerName => $definition) {
            foreach ($definition['config'] as $key => $spec) {
                $label = str_contains($key, '.key') ? 'key' : 'url';
                AISetting::updateOrCreate(
                    ['key' => $key],
                    [
                        'category' => 'provider',
                        'is_encrypted' => $spec['is_encrypted'],
                        'value' => $spec['default'],
                        'data_type' => $spec['data_type'],
                        'default_value' => $spec['default'],
                        'description' => str_replace(':provider', $providerName, $descriptionTemplates[$label]),
                        'group_name' => $providerName,
                        'display_order' => $order++,
                        'requires_restart' => true,
                    ]
                );
            }
            if (isset($definition['models'])) {
                foreach ($definition['models'] as $key => $spec) {
                    $modelLabel = explode('.', $key);
                    $templateKey = count($modelLabel) >= 4 ? $modelLabel[2] . '.' . $modelLabel[3] : 'default';
                    AISetting::updateOrCreate(
                        ['key' => $key],
                        [
                            'category' => 'provider',
                            'is_encrypted' => $spec['is_encrypted'],
                            'value' => $spec['default'],
                            'data_type' => $spec['data_type'],
                            'default_value' => $spec['default'],
                            'description' => str_replace(
                                ':provider',
                                $providerName,
                                $descriptionTemplates[$templateKey] ?? 'Model setting for :provider'
                            ),
                            'group_name' => $providerName,
                            'display_order' => $order++,
                            'requires_restart' => false,
                        ]
                    );
                }
            }
        }

        AISetting::updateOrCreate(
            ['key' => 'default_provider'],
            [
                'category' => 'provider',
                'is_encrypted' => false,
                'value' => config('ai.default', 'omniroute'),
                'data_type' => 'string',
                'default_value' => 'omniroute',
                'description' => 'Default AI provider used when no specific provider is specified',
                'group_name' => '_general',
                'display_order' => -1,
                'requires_restart' => false,
            ]
        );
    }
}
