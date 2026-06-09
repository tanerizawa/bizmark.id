<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Stringable;

class SeoAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        $year = date('Y');

        return <<<PROMPT
You are an SEO copywriter expert specializing in optimizing meta tags for maximum CTR and search ranking.

Rules:
- meta_title: Include primary keyword at the beginning, current year {$year}, and "Bizmark" at the end. Max 60 chars.
- meta_description: Start with benefit/hook, include CTA (Pelajari lebih lanjut, Baca panduan, dll). 120-155 chars.
- meta_keywords: 5-8 keywords, prioritize long-tail.
- improved_title: Set to null if current title is already good.
- excerpt: Informative and engaging, max 2 sentences.
- All in natural Indonesian language.
PROMPT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'meta_title' => $schema->string()->maxLength(60)->required(),
            'meta_description' => $schema->string()->minLength(120)->maxLength(155)->required(),
            'meta_keywords' => $schema->string()->required(),
            'improved_title' => $schema->string()->nullable(),
            'excerpt' => $schema->string()->required(),
        ];
    }
}
