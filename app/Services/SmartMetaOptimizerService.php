<?php

namespace App\Services;

use App\Ai\Agents\SeoAgent;
use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SmartMetaOptimizerService
{
    protected IndexNowService $indexNow;

    public function __construct(IndexNowService $indexNow)
    {
        $this->indexNow = $indexNow;
    }

    /**
     * Optimize meta tags for a batch of articles
     */
    public function optimizeBatch(int $limit = 5, string $language = 'id'): array
    {
        $articles = Article::published()
            ->where('language', $language)
            ->where(function ($q) {
                $q->whereNull('meta_description')
                    ->orWhere('meta_description', '')
                    ->orWhereRaw('LENGTH(meta_description) < 80')
                    ->orWhereNull('meta_keywords')
                    ->orWhere('meta_keywords', '')
                    ->orWhereNull('meta_title')
                    ->orWhere('meta_title', '');
            })
            ->orderBy('views_count', 'desc')
            ->take($limit)
            ->get();

        if ($articles->isEmpty()) {
            $articles = Article::published()
                ->where('language', $language)
                ->orderBy('updated_at', 'asc')
                ->take($limit)
                ->get();
        }

        $results = [];
        foreach ($articles as $article) {
            $results[] = $this->optimizeArticle($article);
        }

        return $results;
    }

    /**
     * Optimize meta tags for a single article
     */
    public function optimizeArticle(Article $article): array
    {
        $result = [
            'article_id' => $article->id,
            'title' => $article->title,
            'status' => 'skipped',
            'changes' => [],
        ];

        try {
            $contentPreview = strip_tags(substr($article->content, 0, 500));

            $prompt = <<<PROMPT
Optimasi SEO meta tags untuk artikel ini:

Judul: {$article->title}
Meta Title saat ini: {$article->meta_title}
Meta Description saat ini: {$article->meta_description}
Meta Keywords saat ini: {$article->meta_keywords}
Excerpt saat ini: {$article->excerpt}
Kategori: {$article->category}
Bahasa: {$article->language}
Preview konten: {$contentPreview}
PROMPT;

            $response = (new SeoAgent)->prompt($prompt, timeout: 120);

            $optimized = $response instanceof \Laravel\Ai\Responses\StructuredAgentResponse
                ? $response->structured
                : json_decode($response->text, true);

            if (empty($optimized)) {
                return $result;
            }

            $changes = [];

            if (! empty($optimized['meta_title']) && $optimized['meta_title'] !== $article->meta_title) {
                $article->meta_title = Str::limit($optimized['meta_title'], 60, '');
                $changes[] = 'meta_title';
            }

            if (! empty($optimized['meta_description']) && $optimized['meta_description'] !== $article->meta_description) {
                $article->meta_description = Str::limit($optimized['meta_description'], 155, '');
                $changes[] = 'meta_description';
            }

            if (! empty($optimized['meta_keywords']) && $optimized['meta_keywords'] !== $article->meta_keywords) {
                $article->meta_keywords = $optimized['meta_keywords'];
                $changes[] = 'meta_keywords';
            }

            if (! empty($optimized['improved_title']) && $optimized['improved_title'] !== $article->title) {
                $article->title = $optimized['improved_title'];
                $changes[] = 'title';
            }

            if (! empty($optimized['excerpt']) && $optimized['excerpt'] !== $article->excerpt) {
                $article->excerpt = $optimized['excerpt'];
                $changes[] = 'excerpt';
            }

            if (! empty($changes)) {
                $article->save();
                $this->indexNow->submitUrl($article->getUrl());
                $result['status'] = 'optimized';
                $result['changes'] = $changes;

                Log::info("MetaOptimizer: Article #{$article->id} optimized", [
                    'title' => $article->title,
                    'changes' => $changes,
                ]);
            }

        } catch (\Throwable $e) {
            $result['status'] = 'error';
            $result['error'] = $e->getMessage();
            Log::error("MetaOptimizer: Failed for article #{$article->id}", ['error' => $e->getMessage()]);
        }

        return $result;
    }
}
