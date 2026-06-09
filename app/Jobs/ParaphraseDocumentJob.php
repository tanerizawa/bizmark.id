<?php

namespace App\Jobs;

use App\Ai\Agents\DocumentAgent;
use App\Models\AIProcessingLog;
use App\Models\DocumentDraft;
use App\Models\DocumentTemplate;
use App\Models\Project;
use App\Services\ProjectContextBuilder;
use App\Services\TemplateExtractor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParaphraseDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 600; // 10 minutes

    public array $backoff = [60, 300, 900];

    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $projectId,
        public int $templateId,
        public int $userId,
        public array $additionalContext = [],
        private ?DocumentAgent $agent = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        TemplateExtractor $extractor,
        ProjectContextBuilder $contextBuilder
    ): void {
        $startTime = microtime(true);

        // Create processing log
        $log = AIProcessingLog::create([
            'template_id' => $this->templateId,
            'project_id' => $this->projectId,
            'operation_type' => 'paraphrase',
            'status' => 'processing',
            'initiated_by' => $this->userId,
            'metadata' => [
                'started_at' => now()->toIso8601String(),
            ],
        ]);

        try {
            // Load project and template
            $project = Project::with('client')->findOrFail($this->projectId);
            $template = DocumentTemplate::findOrFail($this->templateId);

            // Extract text from template
            $extractionResult = $extractor->extractFromFile(
                storage_path('app/'.$template->file_path)
            );

            if (! $extractionResult['success']) {
                throw new \Exception('Template extraction failed: '.$extractionResult['error']);
            }

            $templateText = $extractionResult['text'];

            // Build project context
            $context = $contextBuilder->buildContext($project, $this->additionalContext);

            // Validate required fields if defined
            if ($template->required_fields) {
                $validation = $contextBuilder->validateRequiredFields(
                    $template->required_fields,
                    $context
                );

                if (! $validation['valid']) {
                    throw new \Exception(
                        'Missing required fields: '.implode(', ', $validation['missing_fields'])
                    );
                }
            }

            // Paraphrase document via DocumentAgent
            $contextSummary = $contextBuilder->buildContextSummary($context);
            $prompt = "Document Template:\n```\n{$templateText}\n```\n\n"
                ."Project Context:\n```\n{$contextSummary}\n```\n\n"
                .'Please paraphrase this document using the project context. Return JSON only.';

            $agent = $this->agent ?? new DocumentAgent(mode: 'paraphrase');
            $response = $agent->prompt($prompt, timeout: 600);

            $parsed = $response instanceof \Laravel\Ai\Responses\StructuredAgentResponse
                ? $response->structured
                : json_decode($response->text, true);

            if (empty($parsed) || empty($parsed['full_text'])) {
                throw new \Exception('AI paraphrasing failed: empty response');
            }

            $fullText = $parsed['full_text'];
            $chunks = $parsed['chunks'] ?? null;

            // Calculate duration
            $duration = round(microtime(true) - $startTime, 2);

            // Create document draft
            $draft = DocumentDraft::create([
                'project_id' => $this->projectId,
                'template_id' => $this->templateId,
                'ai_log_id' => $log->id,
                'title' => $template->name.' - '.$project->name,
                'content' => $fullText,
                'sections' => $chunks,
                'status' => 'draft',
                'created_by' => $this->userId,
            ]);

            // Update log as completed
            $usage = method_exists($response, 'usage') ? $response->usage() : [];
            $log->update([
                'status' => 'completed',
                'input_tokens' => $usage['prompt_tokens'] ?? null,
                'output_tokens' => $usage['completion_tokens'] ?? null,
                'cost' => $usage['cost'] ?? null,
                'metadata' => [
                    'started_at' => $log->metadata['started_at'] ?? null,
                    'completed_at' => now()->toIso8601String(),
                    'duration_seconds' => $duration,
                    'chunks_count' => is_array($chunks) ? count($chunks) : 0,
                    'draft_id' => $draft->id,
                    'word_count' => $extractionResult['word_count'] ?? null,
                    'page_count' => $extractionResult['page_count'] ?? null,
                    'model' => $usage['model'] ?? null,
                ],
            ]);

            Log::info('Document paraphrasing completed', [
                'project_id' => $this->projectId,
                'template_id' => $this->templateId,
                'draft_id' => $draft->id,
                'duration' => $duration,
            ]);

        } catch (\Exception $e) {
            // Update log as failed
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'metadata' => array_merge($log->metadata ?? [], [
                    'failed_at' => now()->toIso8601String(),
                    'duration_seconds' => round(microtime(true) - $startTime, 2),
                ]),
            ]);

            Log::error('Document paraphrasing failed', [
                'project_id' => $this->projectId,
                'template_id' => $this->templateId,
                'error' => $e->getMessage(),
            ]);

            // Re-throw to mark job as failed
            throw $e;
        }
    }
}
