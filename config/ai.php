<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider Names
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the AI providers below should be the
    | default for AI operations when no explicit provider is provided
    | for the operation. This should be any provider defined below.
    |
    */

    'default' => 'omniroute',
    'default_for_images' => 'omniroute',
    'default_for_audio' => 'omniroute',
    'default_for_transcription' => 'omniroute',
    'default_for_embeddings' => 'ollama',
    'default_for_reranking' => 'omniroute',

    /*
    |--------------------------------------------------------------------------
    | Model Configuration
    |--------------------------------------------------------------------------
    |
    | Default models for each provider when none is explicitly specified.
    | Supports tiered models for OpenRouter (free vs premium).
    |
    */

    'models' => [
        'openrouter' => [
            'default' => env('OPENROUTER_DEFAULT_MODEL', 'openrouter/free'),
            'free' => [
                'primary' => env('OPENROUTER_FREE_PRIMARY_MODEL', 'openrouter/free'),
                'fallback' => env('OPENROUTER_FREE_FALLBACK_MODEL', 'openrouter/free'),
            ],
            'premium' => [
                'primary' => env('OPENROUTER_PREMIUM_PRIMARY_MODEL', 'openrouter/free'),
                'fallback' => env('OPENROUTER_PREMIUM_FALLBACK_MODEL', 'openrouter/free'),
            ],
        ],
        'omniroute' => [
            'default' => env('OMNIROUTE_DEFAULT_MODEL', 'openrouter/free'),
            'free' => [
                'primary' => env('OMNIROUTE_FREE_PRIMARY_MODEL', 'openrouter/free'),
                'fallback' => env('OMNIROUTE_FREE_FALLBACK_MODEL', 'openrouter/free'),
            ],
            'premium' => [
                'primary' => env('OMNIROUTE_PREMIUM_PRIMARY_MODEL', 'openrouter/free'),
                'fallback' => env('OMNIROUTE_PREMIUM_FALLBACK_MODEL', 'openrouter/free'),
            ],
        ],
        'omniroute2' => [
            'default' => env('OMNIROUTE2_DEFAULT_MODEL', 'Gratis'),
            'free' => [
                'primary' => env('OMNIROUTE2_FREE_PRIMARY_MODEL', 'Gratis'),
                'fallback' => env('OMNIROUTE2_FREE_FALLBACK_MODEL', 'Gratis'),
            ],
            'premium' => [
                'primary' => env('OMNIROUTE2_PREMIUM_PRIMARY_MODEL', 'Gratis'),
                'fallback' => env('OMNIROUTE2_PREMIUM_FALLBACK_MODEL', 'Gratis'),
            ],
        ],
        'opencode' => [
            'default' => env('OPENCODE_DEFAULT_MODEL', 'big-pickle'),
            'free' => [
                'primary' => env('OPENCODE_FREE_PRIMARY_MODEL', 'big-pickle'),
                'fallback' => env('OPENCODE_FREE_FALLBACK_MODEL', 'big-pickle'),
            ],
            'premium' => [
                'primary' => env('OPENCODE_PREMIUM_PRIMARY_MODEL', 'big-pickle'),
                'fallback' => env('OPENCODE_PREMIUM_FALLBACK_MODEL', 'big-pickle'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Below you may configure caching strategies for AI related operations
    | such as embedding generation. You are free to adjust these values
    | based on your application's available caching stores and needs.
    |
    */

    'caching' => [
        'embeddings' => [
            'cache' => false,
            'store' => env('CACHE_STORE', 'database'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Providers
    |--------------------------------------------------------------------------
    |
    | Below are each of your AI providers defined for this application. Each
    | represents an AI provider and API key combination which can be used
    | to perform tasks like text, image, and audio creation via agents.
    |
    */

    'providers' => [
        'ollama' => [
            'driver' => 'ollama',
            'key' => env('OLLAMA_API_KEY', ''),
            'url' => env('OLLAMA_URL', 'http://localhost:11434'),
        ],

        'openrouter' => [
            'driver' => 'openrouter',
            'key' => env('OPENROUTER_API_KEY'),
            'url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
            'models' => [
                'text' => [
                    'default' => env('OPENROUTER_DEFAULT_MODEL', 'openrouter/free'),
                ],
            ],
        ],
        'omniroute' => [
            'driver' => 'openrouter',
            'key' => env('OMNIROUTE_API_KEY', ''),
            'url' => env('OMNIROUTE_BASE_URL', 'http://localhost:20128/v1'),
            'models' => [
                'text' => [
                    'default' => env('OMNIROUTE_DEFAULT_MODEL', 'openrouter/free'),
                ],
            ],
        ],

        'omniroute2' => [
            'driver' => 'openrouter',
            'key' => env('OMNIROUTE2_API_KEY', 'sk-4cda540a951b42b5-75avmp-9cd6ab32'),
            'url' => env('OMNIROUTE2_BASE_URL', 'http://localhost:20129/v1'),
            'models' => [
                'text' => [
                    'default' => env('OMNIROUTE2_DEFAULT_MODEL', 'Gratis'),
                ],
            ],
        ],

        'opencode' => [
            'driver' => 'openai',
            'key' => env('OPENCODE_API_KEY'),
            'url' => env('OPENCODE_BASE_URL', 'https://opencode.ai/zen/v1'),
        ],
    ],

];
