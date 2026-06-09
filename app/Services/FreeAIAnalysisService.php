<?php

namespace App\Services;

use App\Ai\Agents\PerizinanAgent;
use App\Services\Analysis\AnalysisEnricher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FreeAIAnalysisService
{
    public function __construct(
        private readonly AnalysisEnricher $enricher,
    ) {}

    private const CACHE_TTL = 12 * 60 * 60;

    private const TIER_FREE = 'free';

    private const TIER_PREMIUM = 'premium';

    /**
     * Analyze business and recommend permits using PerizinanAgent (Laravel AI SDK)
     *
     * @param  array  $formData  Business context data (KBLI, scale, location, etc.)
     * @param  string  $tier  'free' (landing page) or 'premium' (client portal)
     */
    public function analyze(array $formData, string $tier = 'free'): array
    {
        $startTime = microtime(true);

        $cacheKey = $this->generateCacheKey($formData, $tier);
        $cached = Cache::get($cacheKey);

        if ($cached) {
            Log::info('FreeAIAnalysisService: Using cached analysis', ['cache_key' => $cacheKey, 'tier' => $tier]);

            return array_merge($cached, [
                'cached' => true,
                'processing_time' => 0,
            ]);
        }

        try {
            $agent = new PerizinanAgent(tier: $tier, enricher: $this->enricher);

            Log::info('FreeAIAnalysisService: Starting analysis via PerizinanAgent', [
                'tier' => $tier,
                'provider' => $agent->aiProvider,
            ]);

            $analysis = $agent->analyze($formData, $tier);

            $analysis['ai_processing_time'] = (int) ((microtime(true) - $startTime) * 1000);
            $analysis['generated_at'] = now()->toIso8601String();
            $analysis['version'] = '3.2';
            $analysis['cached'] = false;

            Cache::put($cacheKey, $analysis, self::CACHE_TTL);

            Log::info('FreeAIAnalysisService: Analysis completed via SDK', [
                'tier' => $tier,
                'model' => $analysis['ai_model_used'] ?? 'unknown',
                'time_ms' => $analysis['ai_processing_time'],
                'permits_count' => count($analysis['recommended_permits'] ?? []),
            ]);

            return $analysis;

        } catch (\Exception $e) {
            Log::error('FreeAIAnalysisService: Analysis failed', [
                'tier' => $tier,
                'error' => $e->getMessage(),
                'kbli_code' => $formData['kbli_code'] ?? 'N/A',
            ]);

            throw $e;
        }
    }

    /**
     * Analyze from KBLI parameters — used by OpenRouterService delegation
     * Transforms minimal KBLI params into the full analyze() format.
     */
    public function analyzeFromKbli(
        string $kbliCode,
        string $kbliDescription,
        string $sector,
        ?string $businessScale = null,
        ?string $locationType = null
    ): array {
        $result = $this->analyze([
            'kbli_code' => $kbliCode,
            'kbli_description' => $kbliDescription,
            'business_activity' => $kbliDescription,
            'business_scale' => $this->mapExternalScale($businessScale),
            'location_category' => $this->mapExternalLocation($locationType),
            'sector' => $sector,
        ], self::TIER_PREMIUM);

        // ── Portal context override ──
        // analyzeFromKbli() is ONLY called from the client portal (premium flow)
        // via OpenRouterService → KbliPermitCacheService → ServiceController.
        // Replace free-tier messaging ("silakan daftar ke portal") with portal-appropriate text.
        $result = $this->applyPortalContext($result);

        return $result;
    }

    /**
     * Override limitations, next_steps, and risk_factors for portal (premium) context.
     * Called ONLY from analyzeFromKbli() — ensures logged-in client portal users
     * never see "silakan daftar ke portal BizMark.ID" messaging.
     */
    private function applyPortalContext(array $result): array
    {
        $result['limitations'] = 'Analisis ini dihasilkan oleh AI berdasarkan regulasi terkini 2026 (UU 6/2023, PP 5/2021) '
            .'dan data KBLI yang tersedia. Meskipun sudah dioptimalkan untuk akurasi tinggi, persyaratan spesifik '
            .'dapat bervariasi berdasarkan peraturan daerah (Perda) setempat dan kondisi lapangan. '
            .'Gunakan fitur "Ajukan Permohonan / Konsultasi" untuk pendampingan konsultan bersertifikat.';

        // Replace free-tier next_steps with portal-appropriate steps
        $portalNextSteps = [
            'Siapkan dokumen legalitas perusahaan (KTP, Akta, SK Kemenkumham)',
            'Periksa kesesuaian lokasi usaha dengan RTRW/RDTR setempat',
            'Daftar NIB melalui OSS RBA dengan pendampingan konsultan BizMark',
            'Urus dokumen lingkungan sesuai klasifikasi risiko usaha',
            'Klik "Ajukan Permohonan / Konsultasi" untuk memulai proses pendampingan',
            'Download ringkasan PDF sebagai referensi persiapan dokumen',
        ];

        // If AI provided next_steps, filter out free-tier messaging and merge
        if (! empty($result['next_steps'])) {
            $filtered = array_filter($result['next_steps'], function ($step) {
                return ! str_contains(strtolower($step), 'daftar ke portal')
                    && ! str_contains(strtolower($step), 'silakan daftar');
            });
            $result['next_steps'] = ! empty($filtered) ? array_values($filtered) : $portalNextSteps;
        } else {
            $result['next_steps'] = $portalNextSteps;
        }

        // Filter risk_factors that reference free-tier
        if (! empty($result['risk_factors'])) {
            $result['risk_factors'] = array_values(array_filter($result['risk_factors'], function ($factor) {
                return ! str_contains(strtolower($factor), 'daftar ke portal')
                    && ! str_contains(strtolower($factor), 'silakan daftar');
            }));
        }

        return $result;
    }

    /**
     * Map external scale parameter to internal format
     */
    private function mapExternalScale(?string $scale): string
    {
        if (! $scale) {
            return 'unknown';
        }
        $map = [
            'mikro' => 'micro', 'micro' => 'micro',
            'kecil' => 'small', 'small' => 'small',
            'menengah' => 'medium', 'medium' => 'medium',
            'besar' => 'large', 'large' => 'large',
        ];

        return $map[strtolower($scale)] ?? $scale;
    }

    /**
     * Map external location parameter to internal format
     */
    private function mapExternalLocation(?string $location): string
    {
        if (! $location) {
            return 'unknown';
        }
        $map = [
            'kawasan_industri' => 'industrial', 'industrial' => 'industrial',
            'area_komersial' => 'commercial', 'commercial' => 'commercial',
            'area_residensial' => 'residential', 'residential' => 'residential',
            'pedesaan' => 'rural', 'rural' => 'rural',
        ];

        return $map[strtolower($location)] ?? $location;
    }

    /**
     * Generate cache key based on business characteristics
     * Includes tier, province and company_type for more specific caching
     */
    private function generateCacheKey(array $formData, string $tier = self::TIER_FREE): string
    {
        $key = implode('_', [
            $tier,
            $formData['kbli_code'] ?? 'no-kbli',
            $formData['business_scale'] ?? 'unknown',
            $formData['location_province'] ?? 'unknown',
            $formData['location_category'] ?? 'unknown',
            $formData['estimated_investment'] ?? 'unknown',
            $formData['company_type'] ?? 'unknown',
            substr(md5($formData['business_activity'] ?? ''), 0, 12),
        ]);

        return 'ai_analysis_v3_'.md5($key);
    }

}
