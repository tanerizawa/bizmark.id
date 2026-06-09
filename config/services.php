<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'ollama' => [
        'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
    ],

    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
        'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),

        // ── Default model untuk operasi AI umum ──
        'default_model' => env('OPENROUTER_DEFAULT_MODEL', 'openrouter/free'),

        // Legacy (backward compat) — not used directly by tiered system
        'model' => env('OPENROUTER_MODEL', 'openrouter/free'),

        // ── Free tier (konsultasi-gratis, public landing page) ──
        // Cost-optimized: menggunakan openrouter/free untuk konsistensi
        'free_primary_model' => env('OPENROUTER_FREE_PRIMARY_MODEL', 'openrouter/free'),
        'free_fallback_model' => env('OPENROUTER_FREE_FALLBACK_MODEL', 'openrouter/free'),

        // ── Premium tier (client portal, authenticated users) ──
        // Quality-optimized: menggunakan openrouter/free untuk konsistensi
        'premium_primary_model' => env('OPENROUTER_PREMIUM_PRIMARY_MODEL', 'openrouter/free'),
        'premium_fallback_model' => env('OPENROUTER_PREMIUM_FALLBACK_MODEL', 'openrouter/free'),
    ],

    // SearXNG — Open-source self-hosted metasearch (Priority 1: gratis, unlimited)
    // Docker: bizmark_searxng container — https://github.com/searxng/searxng
    'omniroute' => [
        'api_key' => env('OMNIROUTE_API_KEY'),
        'base_url' => env('OMNIROUTE_BASE_URL', 'http://localhost:20128/v1'),
    ],

    'omniroute2' => [
        'api_key' => env('OMNIROUTE2_API_KEY', 'sk-4cda540a951b42b5-75avmp-9cd6ab32'),
        'base_url' => env('OMNIROUTE2_BASE_URL', 'http://localhost:20129/v1'),
        'default_model' => env('OMNIROUTE2_DEFAULT_MODEL', 'Gratis'),
        'free_primary_model' => env('OMNIROUTE2_FREE_PRIMARY_MODEL', 'Gratis'),
        'free_fallback_model' => env('OMNIROUTE2_FREE_FALLBACK_MODEL', 'Gratis'),
        'premium_primary_model' => env('OMNIROUTE2_PREMIUM_PRIMARY_MODEL', 'Gratis'),
        'premium_fallback_model' => env('OMNIROUTE2_PREMIUM_FALLBACK_MODEL', 'Gratis'),
    ],

    'searxng' => [
        'url' => env('SEARXNG_URL', 'http://bizmark_searxng:8080'),
    ],

    // Google Custom Search API (Priority 2: gratis 100/hari)
    // https://developers.google.com/custom-search/v1/overview
    'google_search' => [
        'api_key' => env('GOOGLE_SEARCH_API_KEY'),
        'engine_id' => env('GOOGLE_SEARCH_ENGINE_ID'),
    ],

    // Google Search Console (GSC) — OAuth2 refresh token flow
    // Setup: Google Cloud Console → APIs & Services → Credentials → OAuth 2.0 Client ID
    // Scopes: https://www.googleapis.com/auth/webmasters.readonly
    // One-time auth URL: https://accounts.google.com/o/oauth2/auth?...
    // See docs/GSC_SETUP.md for step-by-step instructions
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'refresh_token' => env('GOOGLE_REFRESH_TOKEN'),
        'gsc_site_url' => env('GSC_SITE_URL', 'https://bizmark.id/'),  // must match GSC property URL exactly
        'analytics_id' => env('GOOGLE_ANALYTICS_ID'),
    ],

    'perizinan_ai' => [
        'url' => env('PERIZINAN_AI_URL', 'https://api.bizmark.id'),
        'username' => env('PERIZINAN_AI_USERNAME'),
        'password' => env('PERIZINAN_AI_PASSWORD'),
        'timeout' => env('PERIZINAN_AI_TIMEOUT', 30),
    ],

    'pexels' => [
        'api_key' => env('PEXELS_API_KEY'),
    ],

    'indexnow' => [
        'key' => env('INDEXNOW_KEY', 'b1zm4rk-1nd3xn0w-k3y-2026'),
    ],

    // Content Syndication Platforms
    'medium' => [
        'token' => env('MEDIUM_INTEGRATION_TOKEN'),
    ],

    'devto' => [
        'api_key' => env('DEVTO_API_KEY'),
    ],

    'linkedin' => [
        'access_token' => env('LINKEDIN_ACCESS_TOKEN'),
        'organization_id' => env('LINKEDIN_ORGANIZATION_ID'),
    ],

    // Telegram Bot for channel posting
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'channel_id' => env('TELEGRAM_CHANNEL_ID'),
    ],

    // Twitter/X API v2
    'twitter' => [
        'api_key' => env('TWITTER_API_KEY'),
        'api_secret' => env('TWITTER_API_SECRET'),
        'access_token' => env('TWITTER_ACCESS_TOKEN'),
        'access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET'),
        'bearer_token' => env('TWITTER_BEARER_TOKEN'),
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
    ],

    // Facebook Page
    'facebook' => [
        'page_id' => env('FACEBOOK_PAGE_ID'),
        'page_access_token' => env('FACEBOOK_PAGE_ACCESS_TOKEN'),
    ],

    // Google Business Profile
    'gbp' => [
        'refresh_token' => env('GBP_REFRESH_TOKEN'),
        'location_id' => env('GBP_LOCATION_ID'),
    ],

    'social_posting' => [
        'free_only' => env('SOCIAL_POSTING_FREE_ONLY', true),
    ],

    // WhatsApp Business Cloud API (Meta)
    'whatsapp' => [
        'phone_number_id' => env('WA_PHONE_NUMBER_ID'),
        'access_token' => env('WA_ACCESS_TOKEN'),
        'verify_token' => env('WA_VERIFY_TOKEN', 'bizmark_webhook_verify_2026'),
        'app_secret' => env('WA_APP_SECRET'),
        'api_version' => env('WA_API_VERSION', 'v21.0'),
        'admin_phone' => env('WA_ADMIN_PHONE'),
    ],

    'tawk' => [
        'property_id' => env('TAWK_PROPERTY_ID', '69f84236394ff41c326e2175'),
        'widget_id' => env('TAWK_WIDGET_ID', '1jnos59bv'),
        'api_key' => env('TAWK_API_KEY'),
    ],

    // Indonesia Civic Stack — internal Docker microservice (FastAPI)
    // Container: bizmark_civic_stack (port 8100 host, 8000 internal)
    // Modules: simbg, bpjph, oss_nib, jdih (+ bpom, ahu, kpu, ojk, lpse, lhkpn, bps, bmkg)
    // Docs: http://127.0.0.1:8100/docs (when container running)
    'civic_stack' => [
        'url' => env('CIVIC_STACK_URL', 'http://bizmark_civic_stack:8000'),
        'timeout' => (int) env('CIVIC_STACK_TIMEOUT', 15),
    ],

];
