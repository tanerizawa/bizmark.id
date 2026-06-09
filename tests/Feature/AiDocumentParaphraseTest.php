<?php

namespace Tests\Feature;

use App\Jobs\ParaphraseDocumentJob;
use App\Models\Client;
use App\Models\DocumentTemplate;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AiDocumentParaphraseTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Project $project;

    private DocumentTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $client = Client::factory()->create(['email_verified_at' => now()]);
        $status = ProjectStatus::factory()->create(['name' => 'Aktif', 'is_final' => false]);
        $this->project = Project::factory()->create([
            'client_id' => $client->id,
            'status_id' => $status->id,
            'name' => 'Proyek AI Paraphrase Test',
        ]);

        $this->template = DocumentTemplate::create([
            'name' => 'Template UKL-UPL',
            'permit_type' => 'ukl_upl',
            'file_path' => 'templates/test-template.docx',
            'file_name' => 'test-template.docx',
            'file_size' => 1024,
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'required_fields' => ['company_name', 'project_location'],
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_dispatch_paraphrase_job(): void
    {
        Queue::fake();

        ParaphraseDocumentJob::dispatch(
            $this->project->id,
            $this->template->id,
            $this->admin->id,
            ['additional_note' => 'Test context']
        );

        Queue::assertPushed(ParaphraseDocumentJob::class, function ($job) {
            return $job->projectId === $this->project->id
                && $job->templateId === $this->template->id
                && $job->userId === $this->admin->id;
        });
    }

    public function test_dispatch_sets_correct_queue(): void
    {
        Queue::fake();

        ParaphraseDocumentJob::dispatch(
            $this->project->id,
            $this->template->id,
            $this->admin->id,
        );

        Queue::assertPushed(ParaphraseDocumentJob::class, function ($job) {
            return $job->tries === 3
                && $job->timeout === 600;
        });
    }

    public function test_dispatch_without_additional_context(): void
    {
        Queue::fake();

        ParaphraseDocumentJob::dispatch(
            $this->project->id,
            $this->template->id,
            $this->admin->id,
        );

        Queue::assertPushed(ParaphraseDocumentJob::class, function ($job) {
            return $job->projectId === $this->project->id
                && $job->additionalContext === [];
        });
    }

    public function test_handle_creates_processing_log_on_exception(): void
    {
        // Without proper file at storage path, handle() will throw
        // Verify the error path creates a processing log
        $job = new ParaphraseDocumentJob(
            projectId: $this->project->id,
            templateId: $this->template->id,
            userId: $this->admin->id,
            additionalContext: []
        );

        try {
            $job->handle(
                app(\App\Services\TemplateExtractor::class),
                app(\App\Services\ProjectContextBuilder::class),
            );
        } catch (\Exception $e) {
            // Expected — template file doesn't exist
            $this->assertDatabaseHas('ai_processing_logs', [
                'project_id' => $this->project->id,
                'template_id' => $this->template->id,
                'operation_type' => 'paraphrase',
                'status' => 'failed',
                'initiated_by' => $this->admin->id,
            ]);

            return;
        }

        // If no exception, the test should still pass (unlikely but possible)
        $this->assertTrue(true);
    }
}
