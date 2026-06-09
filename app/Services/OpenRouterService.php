<?php

namespace App\Services;

use App\Models\AiQueryLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    protected string $apiKey;

    protected string $baseUrl;

    protected string $primaryModel;

    protected string $fallbackModel;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->baseUrl = config('services.openrouter.base_url', 'https://openrouter.ai/api/v1');
        $this->primaryModel = config('services.openrouter.default_model', 'openrouter/free');
        $this->fallbackModel = config('services.openrouter.free_fallback_model', 'openrouter/free');
    }

    /**
     * Generate permit recommendations — delegates to FreeAIAnalysisService (canonical system).
     * Transforms output to legacy format expected by ConsultationPricingEngine & KbliPermitCacheService.
     */
    public function generatePermitRecommendations(
        string $kbliCode,
        string $kbliDescription,
        string $sector,
        ?string $businessScale = null,
        ?string $locationType = null,
        ?int $clientId = null
    ): ?array {
        $startTime = microtime(true);

        try {
            /** @var FreeAIAnalysisService $analysisService */
            $analysisService = app(FreeAIAnalysisService::class);
            $analysis = $analysisService->analyzeFromKbli($kbliCode, $kbliDescription, $sector, $businessScale, $locationType);

            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            // Log query for tracking
            $this->logQuery([
                'client_id' => $clientId,
                'kbli_code' => $kbliCode,
                'business_context' => ['scale' => $businessScale, 'location' => $locationType],
                'prompt_text' => "Delegated to FreeAIAnalysisService v3.0 (kbli={$kbliCode}, sector={$sector})",
                'response_text' => substr(json_encode($analysis['recommended_permits'] ?? []), 0, 10000),
                'tokens_used' => $analysis['ai_tokens_used'] ?? null,
                'response_time_ms' => $responseTime,
                'status' => 'success',
                'ai_model' => $analysis['ai_model_used'] ?? 'unknown',
                'api_cost' => null,
            ]);

            return [
                'recommended_permits' => $analysis['recommended_permits'] ?? [],
                'required_documents' => $analysis['required_documents'] ?? [],
                'risk_assessment' => $analysis['risk_assessment'] ?? null,
                'estimated_timeline' => $analysis['estimated_timeline'] ?? null,
                'additional_notes' => json_encode([
                    'risk_factors' => $analysis['risk_factors'] ?? [],
                    'next_steps' => $analysis['next_steps'] ?? [],
                    'limitations' => $analysis['limitations'] ?? '',
                ]),
                'ai_model' => $analysis['ai_model_used'] ?? 'unknown',
                'ai_prompt_hash' => md5($kbliCode.$sector.($businessScale ?? '').($locationType ?? '')),
                'confidence_score' => $this->calculateConfidence([
                    'recommended_permits' => $analysis['recommended_permits'] ?? [],
                    'required_documents' => $analysis['required_documents'] ?? [],
                    'risk_assessment' => $analysis['risk_assessment'] ?? null,
                    'estimated_timeline' => $analysis['estimated_timeline'] ?? null,
                ]),
            ];

        } catch (\Exception $e) {
            Log::error('AI generation failed (delegated)', ['kbli_code' => $kbliCode, 'error' => $e->getMessage()]);
            $this->logQuery([
                'client_id' => $clientId,
                'kbli_code' => $kbliCode,
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function calculateConfidence(array $data): float
    {
        $score = 0.5;

        if (isset($data['recommended_permits'])) {
            $hasMandatory = collect($data['recommended_permits'])->where('type', 'mandatory')->isNotEmpty();
            if ($hasMandatory) {
                $score += 0.2;
            }
        }
        if (! empty($data['required_documents'])) {
            $score += 0.1;
        }
        if (! empty($data['risk_assessment'])) {
            $score += 0.1;
        }
        if (! empty($data['estimated_timeline'])) {
            $score += 0.1;
        }

        return min(1.0, $score);
    }

    protected function calculateCost(array $usage, string $model): ?float
    {
        if (empty($usage)) {
            return null;
        }

        // Cost mapping untuk model populer - openrouter/free tidak dikenakan biaya
        $costs = [
            'anthropic/claude-3.5-sonnet' => ['input' => 3.0, 'output' => 15.0],
            'google/gemini-pro-1.5' => ['input' => 1.25, 'output' => 5.0],
            'google/gemini-2.5-flash' => ['input' => 0.3, 'output' => 2.5],
            'deepseek/deepseek-v3.2' => ['input' => 0.25, 'output' => 0.4],
            'openrouter/free' => ['input' => 0.0, 'output' => 0.0],
        ];

        $modelCost = $costs[$model] ?? ['input' => 0.0, 'output' => 0.0];
        $inputCost = ($usage['prompt_tokens'] ?? 0) * $modelCost['input'] / 1000000;
        $outputCost = ($usage['completion_tokens'] ?? 0) * $modelCost['output'] / 1000000;

        return $inputCost + $outputCost;
    }

    protected function logQuery(array $data): void
    {
        try {
            AiQueryLog::create($data);
        } catch (\Exception $e) {
            Log::error('Failed to log AI query', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Generic chat method for flexible AI interactions.
     * Automatically falls back to secondary model on 400/404 errors.
     */
    public function chat(array $messages, array $options = []): array
    {
        $model = $options['model'] ?? $this->primaryModel;
        $temperature = $options['temperature'] ?? 0.7;
        $maxTokens = $options['max_tokens'] ?? 4000;

        $modelsToTry = [$model];
        if ($model !== $this->fallbackModel) {
            $modelsToTry[] = $this->fallbackModel;
        }

        $lastResult = null;

        foreach ($modelsToTry as $currentModel) {
            try {
                $payload = [
                    'model' => $currentModel,
                    'messages' => $messages,
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ];

                // When caller requires strict JSON output, instruct the API directly.
                // This prevents the model from wrapping the response in markdown fences.
                if (isset($options['response_format'])) {
                    $payload['response_format'] = $options['response_format'];
                }

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name'),
                ])->timeout(120)->post("{$this->baseUrl}/chat/completions", $payload);

                if ($response->successful()) {
                    $data = $response->json();

                    return [
                        'success' => true,
                        'content' => $data['choices'][0]['message']['content'] ?? '',
                        'tokens_used' => $data['usage']['total_tokens'] ?? null,
                        'prompt_tokens' => $data['usage']['prompt_tokens'] ?? null,
                        'completion_tokens' => $data['usage']['completion_tokens'] ?? null,
                        'cost' => $this->calculateCost($data['usage'] ?? [], $currentModel),
                        'model' => $currentModel,
                    ];
                }

                $status = $response->status();

                // Model deprecated/removed — try fallback
                if (in_array($status, [400, 404]) && $currentModel !== end($modelsToTry)) {
                    Log::warning('OpenRouter model unavailable, trying fallback', [
                        'failed_model' => $currentModel,
                        'status' => $status,
                        'fallback' => $this->fallbackModel,
                    ]);

                    continue;
                }

                Log::error('OpenRouter API error', [
                    'status' => $status,
                    'body' => $response->body(),
                    'model' => $currentModel,
                ]);

                $lastResult = [
                    'success' => false,
                    'error' => 'API request failed: '.$status,
                    'details' => $response->json(),
                ];
            } catch (\Exception $e) {
                Log::error('OpenRouter chat exception', [
                    'error' => $e->getMessage(),
                    'model' => $currentModel,
                ]);

                $lastResult = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $lastResult ?? ['success' => false, 'error' => 'All models failed'];
    }

    /**
     * Robustly extract a JSON object from an AI response string.
     *
     * Handles the common case where models wrap JSON in markdown code fences,
     * prepend explanatory prose, or include trailing commas.
     *
     * Strategy order (fastest/most common first):
     *   1. Direct parse
     *   2. Strip all markdown code-fence markers
     *   3. Extract first {...} block
     *   4. Fix trailing commas then retry
     *
     * Returns the decoded array on success, or null on complete failure.
     */
    public static function extractJson(string $raw): ?array
    {
        // 1. Direct parse
        $decoded = json_decode(trim($raw), true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // 2. Strip all markdown code-fence markers (```json ... ``` or ``` ... ```)
        $stripped = preg_replace('/```(?:json)?\s*/i', '', $raw) ?? $raw;
        $stripped = trim($stripped);
        $decoded = json_decode($stripped, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // 3. Extract first {...} ... last } block
        if (preg_match('/\{[\s\S]*\}/s', $raw, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }

            // 4. Fix trailing commas then retry
            $fixed = preg_replace('/,\s*([}\]])/s', '$1', $matches[0]) ?? $matches[0];
            $decoded = json_decode($fixed, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
