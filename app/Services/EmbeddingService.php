<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    private const EMBEDDING_MODEL = 'nomic-embed-text';

    private const EMBEDDING_DIMS = 768;

    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ollama.base_url', 'http://localhost:11434'), '/');
    }

    /**
     * Embed a single text string. Returns float[] of length 768.
     * Returns empty array on failure.
     */
    public function embed(string $text): array
    {
        $text = mb_substr(trim($text), 0, 8000);

        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/api/embeddings", [
                    'model' => self::EMBEDDING_MODEL,
                    'prompt' => $text,
                ]);

            if (! $response->successful()) {
                Log::warning('[Embedding] Ollama API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            $embedding = $response->json('embedding', []);

            if (empty($embedding)) {
                Log::warning('[Embedding] Empty embedding returned');

                return [];
            }

            return $embedding;
        } catch (\Throwable $e) {
            Log::error('[Embedding] Exception: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Convert float array to pgvector literal string '[0.1,0.2,...]'.
     */
    public static function toVectorLiteral(array $embedding): string
    {
        return '['.implode(',', $embedding).']';
    }

    public static function dims(): int
    {
        return self::EMBEDDING_DIMS;
    }
}
