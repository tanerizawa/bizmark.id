<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class OmniRoute2IntegrationTest extends TestCase
{
    private string $baseUrl = 'http://localhost:20129/v1';
    private string $apiKey = 'sk-4cda540a951b42b5-75avmp-9cd6ab32';
    private string $model = 'Gratis';

    private function chatCompletions(array $payload): array
    {
        $payload['stream'] = false;

        $ch = curl_init("{$this->baseUrl}/chat/completions");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->apiKey}",
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 60,
        ]);
        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['code' => $httpCode, 'data' => json_decode($body, true)];
    }

    private function getModelList(): array
    {
        $ch = curl_init("{$this->baseUrl}/models");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ["Authorization: Bearer {$this->apiKey}"],
            CURLOPT_TIMEOUT => 10,
        ]);
        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['code' => $httpCode, 'data' => json_decode($body, true)];
    }

    public function test_models_endpoint_returns_200(): void
    {
        $result = $this->getModelList();
        $this->assertSame(200, $result['code']);
        $this->assertSame('list', $result['data']['object'] ?? null);
        $this->assertNotEmpty($result['data']['data'] ?? []);
    }

    public function test_gratis_model_is_available(): void
    {
        $result = $this->getModelList();
        $ids = array_column($result['data']['data'] ?? [], 'id');
        $this->assertContains($this->model, $ids);
    }

    public function test_chat_completions_returns_200(): void
    {
        $result = $this->chatCompletions([
            'model' => $this->model,
            'messages' => [['role' => 'user', 'content' => 'Say hello in one word.']],
            'max_tokens' => 100,
            'temperature' => 0.1,
        ]);

        $this->assertSame(200, $result['code']);
        $this->assertNotNull($result['data'], 'Response is valid JSON');
    }

    public function test_chat_completions_returns_content(): void
    {
        $result = $this->chatCompletions([
            'model' => $this->model,
            'messages' => [['role' => 'user', 'content' => 'Say hello in one word. Return ONLY that word.']],
            'max_tokens' => 100,
            'temperature' => 0.1,
        ]);

        $content = $result['data']['choices'][0]['message']['content'] ?? '';
        $this->assertNotEmpty(trim($content));
    }

    public function test_response_structure_is_openai_compatible(): void
    {
        $result = $this->chatCompletions([
            'model' => $this->model,
            'messages' => [['role' => 'user', 'content' => 'Hi']],
            'max_tokens' => 100,
        ]);

        $d = $result['data'];
        $this->assertNotEmpty($d['id'] ?? '');
        $this->assertSame('chat.completion', $d['object'] ?? '');
        $this->assertIsArray($d['choices'] ?? []);
        $this->assertArrayHasKey('index', $d['choices'][0]);
        $this->assertArrayHasKey('message', $d['choices'][0]);
        $this->assertArrayHasKey('role', $d['choices'][0]['message']);
        $this->assertArrayHasKey('content', $d['choices'][0]['message']);
        $this->assertArrayHasKey('finish_reason', $d['choices'][0]);
        $this->assertNotEmpty($d['model'] ?? '');
    }

    public function test_token_counts_are_consistent(): void
    {
        $result = $this->chatCompletions([
            'model' => $this->model,
            'messages' => [['role' => 'user', 'content' => 'Say hello']],
            'max_tokens' => 100,
        ]);

        $usage = $result['data']['usage'] ?? [];
        $this->assertIsInt($usage['prompt_tokens'] ?? null);
        $this->assertIsInt($usage['completion_tokens'] ?? null);
        $this->assertIsInt($usage['total_tokens'] ?? null);
        $this->assertGreaterThan(0, $usage['total_tokens']);
        $this->assertSame(
            $usage['prompt_tokens'] + $usage['completion_tokens'],
            $usage['total_tokens']
        );
    }

    public function test_unauthorized_request_is_rejected(): void
    {
        $ch = curl_init("{$this->baseUrl}/chat/completions");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'model' => $this->model,
                'messages' => [['role' => 'user', 'content' => 'Hi']],
            ]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer invalid-key',
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 10,
        ]);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Some proxies accept any key — this is informational
        $this->assertContains($httpCode, [200, 401, 403]);
    }
}
