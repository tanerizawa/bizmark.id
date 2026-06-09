<?php

namespace App\Services;

use App\Models\KbliSemanticSearch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KbliSemanticSearchService
{
    public function __construct(
        private readonly EmbeddingService $embedder,
        private readonly OpenRouterService $openRouter
    ) {}

    /**
     * Semantic search: natural language query → top-N KBLI results.
     *
     * @return array{query: string, results: array, explanation: string|null, source: string}
     */
    public function search(string $query, int $limit = 5, ?string $ip = null): array
    {
        $query = mb_substr(trim($query), 0, 500);
        $cacheKey = 'kbli_sem:'.md5($query.$limit);

        if ($cached = Cache::get($cacheKey)) {
            return array_merge($cached, ['source' => 'cache']);
        }

        $startMs = microtime(true);

        // 1. Embed query
        $embedding = $this->embedder->embed($query);
        if (empty($embedding)) {
            // Fallback to keyword search
            return $this->keywordFallback($query, $limit);
        }

        $vectorLiteral = EmbeddingService::toVectorLiteral($embedding);

        // 2. pgvector cosine similarity search
        try {
            $rows = DB::select(<<<'SQL'
                SELECT id, code, activities, category, description,
                       ROUND((1 - (embedding <=> ?::vector))::numeric, 4) AS similarity
                FROM kbli
                WHERE embedding IS NOT NULL
                  AND is_active = true
                ORDER BY embedding <=> ?::vector
                LIMIT ?
            SQL, [$vectorLiteral, $vectorLiteral, $limit]);
        } catch (\Throwable $e) {
            Log::error('[KbliSemSearch] pgvector query failed: '.$e->getMessage());

            return $this->keywordFallback($query, $limit);
        }

        $latencyMs = round((microtime(true) - $startMs) * 1000, 1);

        if (empty($rows)) {
            return $this->keywordFallback($query, $limit);
        }

        // 3. AI explanation for top result
        $explanation = $this->explainTopMatch($query, $rows[0]);

        $output = [
            'query' => $query,
            'results' => $rows,
            'explanation' => $explanation,
            'latency_ms' => $latencyMs,
            'source' => 'vector',
        ];

        // 4. Analytics log (async-safe, fire and forget)
        try {
            KbliSemanticSearch::create([
                'query' => $query,
                'results' => array_map(fn ($r) => ['code' => $r->code, 'similarity' => $r->similarity], $rows),
                'latency_ms' => $latencyMs,
                'ip_address' => $ip,
            ]);
        } catch (\Throwable $e) {
            Log::warning('[KbliSemSearch] Analytics log failed: '.$e->getMessage());
        }

        Cache::put($cacheKey, $output, now()->addHours(24));

        return $output;
    }

    // ────────────────────────────────────────────────────────────────────────
    // AI Explanation
    // ────────────────────────────────────────────────────────────────────────

    private function explainTopMatch(string $query, object $topResult): ?string
    {
        try {
            $result = $this->openRouter->chat([
                [
                    'role' => 'system',
                    'content' => 'Anda adalah ahli KBLI (Klasifikasi Baku Lapangan Usaha Indonesia). Jelaskan dalam 2-3 kalimat mengapa kode KBLI ini relevan dengan query pengguna. Gunakan bahasa Indonesia yang mudah dipahami pengusaha awam.',
                ],
                [
                    'role' => 'user',
                    'content' => "Query: \"{$query}\"\n\nKBLI: {$topResult->code} — {$topResult->activities}\nDeskripsi: ".mb_substr($topResult->description ?? '', 0, 500),
                ],
            ], ['max_tokens' => 200]);

            return $result['success'] ? ($result['content'] ?? null) : null;
        } catch (\Throwable $e) {
            Log::warning('[KbliSemSearch] Explain failed: '.$e->getMessage());

            return null;
        }
    }

    // ────────────────────────────────────────────────────────────────────────
    // Fallback: text-based search when embedding unavailable
    // ────────────────────────────────────────────────────────────────────────

    private function keywordFallback(string $query, int $limit): array
    {
        $rows = DB::select(<<<'SQL'
            SELECT id, code, activities, category, description,
                    0.5 AS similarity
            FROM kbli
            WHERE is_active = true
              AND (activities ILIKE ? OR description ILIKE ?)
            LIMIT ?
        SQL, ["%{$query}%", "%{$query}%", $limit]);

        return [
            'query' => $query,
            'results' => $rows,
            'explanation' => null,
            'latency_ms' => null,
            'source' => 'keyword_fallback',
        ];
    }
}
