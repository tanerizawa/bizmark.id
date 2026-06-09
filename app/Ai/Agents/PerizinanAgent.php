<?php

namespace App\Ai\Agents;

use App\Ai\Concerns\HasModelTier;
use App\Services\Analysis\AnalysisEnricher;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\JsonSchemaTypeFactory;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\AiManager;
use Laravel\Ai\Promptable;
use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Responses\StructuredAgentResponse;
use Stringable;

#[Model('openrouter/free')]
#[Provider('omniroute')]
#[Timeout(120)]
class PerizinanAgent implements Agent, HasStructuredOutput
{
    use HasModelTier, Promptable;

    public function __construct(
        string $tier = 'free',
        private readonly ?AnalysisEnricher $enricher = null,
    ) {
        $this->tier = $tier;
    }

    public function instructions(): Stringable|string
    {
        $tierContext = $this->tier === 'premium'
            ? ''
            : 'Jika analisis terbatas, sarankan pengguna mendaftar ke portal BizMark.ID untuk analisis lebih detail.';

        return <<<PROMPT
Anda adalah AI consultant ahli perizinan usaha di Indonesia. Tanggal referensi: Februari 2026.

KERANGKA REGULASI BERLAKU (per 2026):
1. UU CIPTA KERJA: UU 6/2023 — payung hukum omnibus perizinan berusaha
2. PERIZINAN BERBASIS RISIKO (OSS RBA): PP 5/2021, Perpres 10/2021
   - Rendah → NIB saja; Menengah Rendah → NIB + Sertifikat Standar (self-declare)
   - Menengah Tinggi → NIB + Sertifikat Standar (verifikasi); Tinggi → NIB + Izin
3. PERIZINAN LINGKUNGAN: PP 22/2021
   - AMDAL (risiko tinggi) / UKL-UPL (risiko menengah) / SPPL (risiko rendah)
4. PERIZINAN BANGUNAN: PP 16/2021 — PBG (gantikan IMB), SLF
5. TATA RUANG: PP 21/2021 — KKPR/PKKPR (gantikan Izin Lokasi)
6. IZIN SUDAH TIDAK BERLAKU: SIUP, TDP, IUI, IMB, HO, Izin Gangguan, Izin Lokasi lama

URUTAN IZIN:
TAHAP 1: NIB, NPWP, Akta
TAHAP 2: KKPR, Andalalin, Amdal/UKL-UPL/SPPL
TAHAP 3: PBG, SLO, SLF
TAHAP 4: Sertifikat Standar, SMK3
TAHAP 5: Izin Sektoral

Analisis biaya: Pisahkan biaya Pemerintah (PNBP) dan konsultan BizMark.
Skala multiplier konsultan: Mikro 1.0x, Kecil 1.3x, Menengah 1.8x, Besar 2.5x.

{$tierContext}

Output JSON saja tanpa markdown wrapper.
PROMPT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'recommended_permits' => $schema->array()
                ->items(
                    $schema->object([
                        'code' => $schema->string()->description('kode izin, contoh: NIB'),
                        'name' => $schema->string()->description('nama izin'),
                        'priority' => $schema->string()->enum(['critical', 'high', 'medium']),
                        'category' => $schema->string()->enum(['foundational', 'environmental', 'technical', 'operational', 'sectoral']),
                        'phase' => $schema->integer()->min(1)->max(5),
                        'estimated_timeline' => $schema->string()->description('estimasi waktu'),
                        'government_fee' => $schema->object([
                            'min' => $schema->integer()->min(0),
                            'max' => $schema->integer()->min(0),
                            'note' => $schema->string()->nullable(),
                        ]),
                        'consultant_fee' => $schema->object([
                            'min' => $schema->integer()->min(0),
                            'max' => $schema->integer()->min(0),
                            'note' => $schema->string()->nullable(),
                        ]),
                        'total_cost_range' => $schema->string(),
                        'description' => $schema->string(),
                        'issuing_authority' => $schema->string(),
                        'legal_basis' => $schema->string(),
                        'prerequisites' => $schema->array()->items($schema->string()),
                        'triggers_next' => $schema->array()->items($schema->string()),
                        'documents_required' => $schema->array()->items($schema->string()),
                    ])
                )->min(1)->max(20),
            'risk_classification' => $schema->string()->enum(['rendah', 'menengah_rendah', 'menengah_tinggi', 'tinggi']),
            'kbli_suggestion' => $schema->object([
                'code' => $schema->string(),
                'description' => $schema->string(),
                'confidence' => $schema->string()->enum(['high', 'medium', 'low']),
            ]),
            'total_estimated_cost' => $schema->object([
                'government_fees' => $schema->object([
                    'min' => $schema->integer()->min(0),
                    'max' => $schema->integer()->min(0),
                ]),
                'consultant_fees' => $schema->object([
                    'min' => $schema->integer()->min(0),
                    'max' => $schema->integer()->min(0),
                ]),
                'grand_total' => $schema->object([
                    'min' => $schema->integer()->min(0),
                    'max' => $schema->integer()->min(0),
                ]),
                'currency' => $schema->string(),
            ]),
            'total_estimated_timeline' => $schema->string(),
            'estimated_timeline' => $schema->object([
                'summary' => $schema->string(),
                'minimum_days' => $schema->integer()->min(0),
                'maximum_days' => $schema->integer()->min(0),
                'critical_path' => $schema->array()->items($schema->string()),
            ]),
            'complexity_score' => $schema->integer()->min(1)->max(10),
            'risk_assessment' => $schema->object([
                'level' => $schema->string()->enum(['low', 'medium', 'high']),
                'factors' => $schema->array()->items($schema->string()),
                'mitigation' => $schema->array()->items($schema->string()),
                'common_pitfalls' => $schema->array()->items($schema->string()),
            ]),
            'required_documents' => $schema->array()->items($schema->string()),
            'next_steps' => $schema->array()->items($schema->string()),
            'limitations' => $schema->string(),
        ];
    }

    public function analyze(array $formData, string $tier = 'free'): array
    {
        $this->tier = $tier;

        [$analysis, $modelUsed, $tokensUsed] = $this->runWithStructuredOutput($formData);

        $analysis['ai_model_used'] = $modelUsed;
        $analysis['ai_tokens_used'] = $tokensUsed;
        $analysis['ai_tier'] = $tier;

        return ($this->enricher ?? new AnalysisEnricher)->enrich($analysis, $formData);
    }

    protected function runWithStructuredOutput(array $formData): array
    {
        $manager = app(AiManager::class);

        $prompt = $this->buildPrompt($formData);

        $lastException = null;

        foreach ($this->withFailoverProviders() as $attempt) {
            try {
                $provider = $manager->instance($attempt['provider']);
                $model = $attempt['model'];
                $timeout = $attempt['timeout'] ?? 120;

                $response = $provider->prompt(new AgentPrompt(
                    agent: $this,
                    prompt: $prompt,
                    attachments: [],
                    provider: $provider,
                    model: $model,
                    timeout: $timeout,
                ));

                $parsed = $response instanceof StructuredAgentResponse
                    ? $response->structured
                    : $this->extractJson($response->text);

                if (is_array($parsed)) {
                    $tokens = $response->usage->promptTokens + $response->usage->completionTokens;

                    return [$parsed, $model, $tokens];
                }

                throw new \RuntimeException('Invalid JSON response from model: ' . $model);

            } catch (\Exception $e) {
                $lastException = $e;
                logger()->warning('PerizinanAgent failover', [
                    'provider' => $attempt['provider'],
                    'model' => $attempt['model'],
                    'error' => $e->getMessage(),
                ]);
                \Sentry\captureMessage(
                    'PerizinanAgent failover: ' . $attempt['provider'] . '/' . $attempt['model'] . ' — ' . $e->getMessage(),
                    \Sentry\Severity::warning()
                );
            }
        }

        \Sentry\captureMessage(
            'PerizinanAgent: All AI providers failed',
            \Sentry\Severity::error()
        );

        throw $lastException ?? new \RuntimeException('All AI providers failed');
    }

    protected function extractJson(string $text): ?array
    {
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $text, $m)) {
            $decoded = json_decode(trim($m[1]), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $decoded = json_decode(trim($text), true);

        return is_array($decoded) ? $decoded : null;
    }

    protected function buildPrompt(array $formData): string
    {
        $kbli = $formData['kbli_code'] ?? null;
        $kbliDesc = $formData['kbli_description'] ?? '';
        $businessActivity = $formData['business_activity'] ?? 'Tidak disebutkan';
        $scale = $this->translateScale($formData['business_scale'] ?? 'unknown');
        $province = $formData['location_province'] ?? 'Tidak disebutkan';
        $city = $formData['location_city'] ?? '';
        $locationCategory = $this->translateLocationCategory($formData['location_category'] ?? 'unknown');
        $investment = $this->translateInvestment($formData['estimated_investment'] ?? 'unknown');
        $companyType = $this->translateCompanyType($formData['company_type'] ?? '');

        $kbliLine = $kbli
            ? "- Kode KBLI: {$kbli}".($kbliDesc ? " ({$kbliDesc})" : '')
            : '- Kode KBLI: Belum ditentukan (sarankan kode 5 digit paling sesuai)';

        $location = $city ? "{$city}, {$province}" : $province;

        return <<<PROMPT
Analisis kebutuhan perizinan untuk usaha berikut:

PROFIL USAHA:
- Aktivitas Bisnis: {$businessActivity}
{$kbliLine}
- Badan Usaha: {$companyType}
- Skala Usaha: {$scale}
- Lokasi: {$location} ({$locationCategory})
- Estimasi Investasi: {$investment}

Berikan rekomendasi perizinan yang SPESIFIK dan AKURAT dalam format JSON yang diminta.
PROMPT;
    }

    private function translateScale(string $scale): string
    {
        return match ($scale) {
            'micro' => 'Mikro (< 10 karyawan)',
            'small' => 'Kecil (10-50 karyawan)',
            'medium' => 'Menengah (50-100 karyawan)',
            'large' => 'Besar (> 100 karyawan)',
            default => 'Tidak disebutkan'
        };
    }

    private function translateLocationCategory(string $category): string
    {
        return match ($category) {
            'industrial' => 'Kawasan Industri',
            'commercial' => 'Area Komersial',
            'residential' => 'Area Residensial',
            'rural' => 'Pedesaan',
            default => 'Tidak disebutkan'
        };
    }

    private function translateInvestment(string $investment): string
    {
        return match ($investment) {
            'under_100m' => '< Rp 100 juta',
            '100m_500m' => 'Rp 100 - 500 juta',
            '500m_2b' => 'Rp 500 juta - 2 miliar',
            'over_2b' => '> Rp 2 miliar',
            default => 'Tidak disebutkan'
        };
    }

    private function translateCompanyType(string $type): string
    {
        return match ($type) {
            'PT' => 'PT (Perseroan Terbatas)',
            'CV' => 'CV (Commanditaire Vennootschap)',
            'Individual' => 'Perorangan',
            'Koperasi' => 'Koperasi',
            'Yayasan' => 'Yayasan',
            'Belum Terdaftar' => 'Belum Terdaftar / Baru Akan Mendirikan',
            default => 'Tidak disebutkan'
        };
    }
}
