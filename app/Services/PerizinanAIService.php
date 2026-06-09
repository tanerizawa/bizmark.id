<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk integrasi dengan Perizinan AI RAG System
 *
 * API Endpoint: https://api.bizmark.id/
 * Documentation: https://api.bizmark.id/docs
 */
class PerizinanAIService
{
    private string $baseUrl;

    private int $timeout;

    private ?string $username;

    private ?string $password;

    public function __construct()
    {
        $this->baseUrl = config('services.perizinan_ai.url', 'https://api.bizmark.id');
        $this->timeout = config('services.perizinan_ai.timeout', 30);
        $this->username = config('services.perizinan_ai.username');
        $this->password = config('services.perizinan_ai.password');
    }

    /**
     * Get HTTP client with auth headers.
     */
    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        $client = Http::timeout($this->timeout)
            ->retry(3, 1000, function ($exception) {
                return $exception instanceof \Illuminate\Http\Client\ConnectionException
                    || ($exception->response && $exception->response->status() >= 500);
            })
            ->withHeaders(['Accept' => 'application/json']);

        if ($this->username && $this->password) {
            $client->withBasicAuth($this->username, $this->password);
        }

        return $client;
    }

    /**
     * Query RAG system for regulation context
     *
     * @param  string  $question  Question about regulations
     * @return array Response with answer, sources, and confidence
     *
     * @throws Exception
     */
    public function query(string $question): array
    {
        $startTime = microtime(true);

        try {
            $response = $this->client()
                ->post("{$this->baseUrl}/api/query", [
                    'question' => $question,
                ]);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($response->failed()) {
                Log::error('Perizinan AI query failed', [
                    'question' => substr($question, 0, 100),
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'response_time_ms' => $responseTime,
                ]);

                throw new Exception('RAG query failed with status: '.$response->status());
            }

            $result = $response->json();

            // Calculate average confidence from sources
            $sources = $result['sources'] ?? [];
            $avgConfidence = 0;
            if (! empty($sources)) {
                $scores = array_column($sources, 'score');
                $avgConfidence = count($scores) > 0 ? array_sum($scores) / count($scores) : 0;
            }

            // Add confidence_score to result
            $result['confidence_score'] = round($avgConfidence, 4);

            Log::info('Perizinan AI query successful', [
                'question_length' => strlen($question),
                'sources_count' => count($sources),
                'confidence' => $result['confidence_score'],
                'response_time_ms' => $responseTime,
            ]);

            return $result;

        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::error('Perizinan AI query exception', [
                'error' => $e->getMessage(),
                'question' => substr($question, 0, 100),
                'response_time_ms' => $responseTime,
            ]);

            throw $e;
        }
    }

    /**
     * Get regulation context for business type
     *
     * @param  string  $businessType  Business type (PT, CV, UMKM, etc)
     * @param  string  $location  Location/region
     * @return array RAG response
     */
    public function getBusinessTypeRegulations(string $businessType, string $location): array
    {
        $cacheKey = "rag_biztype:{$businessType}:{$location}";

        // Cache for 1 hour
        return Cache::remember($cacheKey, 3600, function () use ($businessType, $location) {
            $question = "Apa saja persyaratan perizinan untuk mendirikan {$businessType} di {$location}, Indonesia? Sebutkan dokumen yang diperlukan dan peraturan yang mengatur.";

            return $this->query($question);
        });
    }

    /**
     * Get KBLI-specific requirements
     *
     * @param  string  $kbliCode  KBLI code
     * @param  string  $description  Activity description
     * @return array RAG response
     */
    public function getKBLIRequirements(string $kbliCode, string $description): array
    {
        $cacheKey = "rag_kbli:{$kbliCode}";

        // Cache for 1 hour
        return Cache::remember($cacheKey, 3600, function () use ($kbliCode, $description) {
            $question = "Apa persyaratan perizinan spesifik untuk kegiatan usaha KBLI {$kbliCode} ({$description})? Jelaskan izin-izin yang dibutuhkan.";

            return $this->query($question);
        });
    }

    /**
     * Get location-specific regulations
     *
     * @param  string  $locationType  Location type (urban, industrial, etc)
     * @param  int  $employeeCount  Number of employees
     * @return array RAG response
     */
    public function getLocationRequirements(string $locationType, int $employeeCount): array
    {
        $cacheKey = "rag_location:{$locationType}:{$employeeCount}";

        // Cache for 2 hours (less volatile)
        return Cache::remember($cacheKey, 7200, function () use ($locationType, $employeeCount) {
            $question = "Apa persyaratan khusus untuk mendirikan usaha di area {$locationType} dengan jumlah karyawan {$employeeCount} orang? Sebutkan perizinan lingkungan dan ketenagakerjaan yang diperlukan.";

            return $this->query($question);
        });
    }

    /**
     * Test API connection
     */
    public function testConnection(): bool
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/health");

            return $response->successful() && $response->json('status') === 'healthy';
        } catch (\Exception $e) {
            Log::error('Perizinan AI connection test failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Clear all RAG-related caches
     */
    public function clearCache(): void
    {
        try {
            Cache::tags('rag')->flush();
        } catch (\BadMethodCallException|\Exception $e) {
            // Fallback for drivers that do not support tags (e.g. file, database)
            Cache::forget('perizinan_ai_token');
        }

        Log::info('Perizinan AI cache cleared');
    }

    /**
     * Get service status
     */
    public function getStatus(): array
    {
        try {
            $isConnected = $this->testConnection();

            return [
                'connected' => $isConnected,
                'base_url' => $this->baseUrl,
                'configured' => filled($this->username) && filled($this->password),
                'cache_enabled' => Cache::getStore() !== null,
            ];
        } catch (\Exception $e) {
            return [
                'connected' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
