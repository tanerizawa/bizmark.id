<?php

namespace Tests\Unit;

use App\Ai\Agents\DocumentAgent;
use App\Jobs\ParaphraseDocumentJob;
use App\Models\DocumentTemplate;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\User;
use App\Services\ProjectContextBuilder;
use App\Services\TemplateExtractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\Data\Meta;
use Laravel\Ai\Responses\Data\Usage;
use Tests\TestCase;

/**
 * Test double for AgentResponse that doesn't require mocking return types
 */
class TestAgentResponse extends AgentResponse
{
    public function __construct(public string $text)
    {
        parent::__construct(
            invocationId: 'test-invocation-id',
            text: $this->text,
            usage: new Usage(),
            meta: new Meta()
        );
    }

    public function getText(): string
    {
        return $this->text;
    }
}

class ParaphraseDocumentJobTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    private DocumentTemplate $template;

    private User $user;

    private ProjectStatus $status;

    protected function setUp(): void
    {
        parent::setUp();

        $this->status = ProjectStatus::factory()->create([
            'name' => 'Dalam Proses',
            'is_final' => false,
        ]);

        $this->user = User::factory()->create();
        $this->project = Project::factory()->create([
            'status_id' => $this->status->id,
        ]);
        $this->template = DocumentTemplate::create([
            'name' => 'Test Template',
            'permit_type' => 'other',
            'file_name' => 'test.docx',
            'file_path' => 'templates/test.docx',
            'file_size' => 1024,
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'required_fields' => ['company_name', 'director_name'],
            'created_by' => $this->user->id,
        ]);
    }

    public function test_job_properties(): void
    {
        $job = new ParaphraseDocumentJob(
            projectId: $this->project->id,
            templateId: $this->template->id,
            userId: $this->user->id,
        );

        $this->assertSame(3, $job->tries);
        $this->assertSame(600, $job->timeout);
        $this->assertSame([60, 300, 900], $job->backoff);
        $this->assertTrue($job->deleteWhenMissingModels);
    }

    public function test_handle_creates_processing_log_on_start(): void
    {
        $job = new ParaphraseDocumentJob(
            projectId: $this->project->id,
            templateId: $this->template->id,
            userId: $this->user->id,
        );

        // Mock services to throw so we can inspect the log before job completes
        $extractor = $this->createMock(TemplateExtractor::class);
        $extractor->method('extractFromFile')
            ->willThrowException(new \Exception('Simulated failure'));

        $contextBuilder = $this->createMock(ProjectContextBuilder::class);

        try {
            $job->handle($extractor, $contextBuilder);
        } catch (\Exception) {
            // Expected
        }

        $this->assertDatabaseHas('ai_processing_logs', [
            'project_id' => $this->project->id,
            'template_id' => $this->template->id,
            'operation_type' => 'paraphrase',
            'initiated_by' => $this->user->id,
        ]);
    }

    public function test_handle_logs_error_on_template_extraction_failure(): void
    {
        $job = new ParaphraseDocumentJob(
            projectId: $this->project->id,
            templateId: $this->template->id,
            userId: $this->user->id,
        );

        Log::shouldReceive('error')
            ->once()
            ->with('Document paraphrasing failed', \Mockery::on(function ($context) {
                return $context['project_id'] === $this->project->id
                    && str_contains($context['error'] ?? '', 'Template extraction failed');
            }));

        $extractor = $this->createMock(TemplateExtractor::class);
        $extractor->method('extractFromFile')
            ->willReturn(['success' => false, 'error' => 'Template extraction failed: File not found']);

        $contextBuilder = $this->createMock(ProjectContextBuilder::class);

        try {
            $job->handle($extractor, $contextBuilder);
        } catch (\Exception) {
            // Expected - job re-throws after logging
        }
    }

    public function test_handle_logs_error_on_missing_required_fields(): void
    {
        $job = new ParaphraseDocumentJob(
            projectId: $this->project->id,
            templateId: $this->template->id,
            userId: $this->user->id,
        );

        Log::shouldReceive('error')
            ->once()
            ->with('Document paraphrasing failed', \Mockery::on(function ($context) {
                return $context['project_id'] === $this->project->id
                    && str_contains($context['error'] ?? '', 'Missing required fields');
            }));

        $extractor = $this->createMock(TemplateExtractor::class);
        $extractor->method('extractFromFile')
            ->willReturn([
                'success' => true,
                'text' => 'Template text content for testing.',
                'page_count' => 1,
                'word_count' => 5,
                'char_count' => 40,
                'metadata' => ['format' => 'docx'],
            ]);

        $contextBuilder = $this->createMock(ProjectContextBuilder::class);
        $contextBuilder->method('buildContext')
            ->willReturn(['project_name' => 'Test']); // Missing company_name and director_name

        $contextBuilder->method('validateRequiredFields')
            ->willReturn([
                'valid' => false,
                'missing_fields' => ['company_name', 'director_name'],
            ]);

        try {
            $job->handle($extractor, $contextBuilder);
        } catch (\Exception) {
            // Expected - job re-throws after logging
        }
    }

    public function test_handle_creates_draft_on_success(): void
    {
        $responseData = [
            'full_text' => 'Hasil parafrase dokumen.',
            'chunks' => [['heading' => 'Bagian 1', 'content' => 'Konten']],
            'word_count' => 5,
            'changes_summary' => ['Replaced placeholders'],
        ];

        $agent = \Mockery::mock(DocumentAgent::class);
        $agent->shouldReceive('prompt')
            ->once()
            ->andReturn(new TestAgentResponse(json_encode($responseData)));

        $job = new ParaphraseDocumentJob(
            projectId: $this->project->id,
            templateId: $this->template->id,
            userId: $this->user->id,
            agent: $agent,
        );

        $extractor = $this->createMock(TemplateExtractor::class);
        $extractor->method('extractFromFile')
            ->willReturn([
                'success' => true,
                'text' => 'Template text for paraphrasing.',
                'page_count' => 1,
                'word_count' => 5,
                'char_count' => 35,
                'metadata' => ['format' => 'docx'],
            ]);

        $contextBuilder = $this->createMock(ProjectContextBuilder::class);
        $contextBuilder->method('buildContext')
            ->willReturn(['company_name' => 'PT BizMark', 'director_name' => 'John Doe']);
        $contextBuilder->method('buildContextSummary')
            ->willReturn('KONTEKS PROYEK:\nCompany Name: PT BizMark\nDirector Name: John Doe');

        $contextBuilder->method('validateRequiredFields')
            ->willReturn(['valid' => true, 'missing_fields' => []]);

        $job->handle($extractor, $contextBuilder);

        $this->assertDatabaseHas('document_drafts', [
            'project_id' => $this->project->id,
            'template_id' => $this->template->id,
        ]);
    }

    public function test_handle_logs_info_on_success(): void
    {
        $responseData = [
            'full_text' => 'Hasil parafrase.',
            'chunks' => [],
            'word_count' => 2,
            'changes_summary' => ['Replaced placeholders'],
        ];

        $agent = \Mockery::mock(DocumentAgent::class);
        $agent->shouldReceive('prompt')
            ->once()
            ->andReturn(new TestAgentResponse(json_encode($responseData)));

        Log::shouldReceive('info')
            ->once()
            ->with('Document paraphrasing completed', \Mockery::on(function ($context) {
                return $context['project_id'] === $this->project->id;
            }));

        $job = new ParaphraseDocumentJob(
            projectId: $this->project->id,
            templateId: $this->template->id,
            userId: $this->user->id,
            agent: $agent,
        );

        $extractor = $this->createMock(TemplateExtractor::class);
        $extractor->method('extractFromFile')
            ->willReturn([
                'success' => true,
                'text' => 'Template text.',
                'page_count' => 1,
                'word_count' => 2,
                'char_count' => 14,
                'metadata' => ['format' => 'docx'],
            ]);

        $contextBuilder = $this->createMock(ProjectContextBuilder::class);
        $contextBuilder->method('buildContext')
            ->willReturn(['company_name' => 'PT BizMark', 'director_name' => 'John Doe']);
        $contextBuilder->method('buildContextSummary')
            ->willReturn('KONTEKS PROYEK:\nCompany Name: PT BizMark\nDirector Name: John Doe');
        $contextBuilder->method('validateRequiredFields')
            ->willReturn(['valid' => true, 'missing_fields' => []]);

        $job->handle($extractor, $contextBuilder);
    }
}
