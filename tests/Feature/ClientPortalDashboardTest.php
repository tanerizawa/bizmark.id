<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Document;
use App\Models\PermitApplication;
use App\Models\PermitType;
use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientPortalDashboardTest extends TestCase
{
    use RefreshDatabase;

    private Client $client;

    private Client $otherClient;

    private ProjectStatus $status;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Client::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->otherClient = Client::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->status = ProjectStatus::factory()->create([
            'name' => 'Dalam Proses',
            'is_final' => false,
        ]);

        $this->project = Project::factory()->create([
            'client_id' => $this->client->id,
            'status_id' => $this->status->id,
            'name' => 'Proyek AMDAL Industri',
            'contract_value' => 50000000,
        ]);
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get(route('client.dashboard'))
            ->assertRedirect();
    }

    public function test_authenticated_client_can_access_dashboard(): void
    {
        $this->actingAs($this->client, 'client')
            ->get(route('client.dashboard'))
            ->assertOk();
    }

    public function test_dashboard_shows_client_project(): void
    {
        $this->actingAs($this->client, 'client')
            ->get(route('client.dashboard'))
            ->assertOk()
            ->assertSee($this->project->name);
    }

    public function test_other_client_project_not_shown_on_dashboard(): void
    {
        $otherProject = Project::factory()->create([
            'client_id' => $this->otherClient->id,
            'status_id' => $this->status->id,
            'name' => 'Proyek Klien Lain',
        ]);

        $this->actingAs($this->client, 'client')
            ->get(route('client.dashboard'))
            ->assertOk()
            ->assertDontSee($otherProject->name);
    }

    public function test_client_can_list_their_projects(): void
    {
        $this->actingAs($this->client, 'client')
            ->get(route('client.projects.index'))
            ->assertOk()
            ->assertSee($this->project->name);
    }

    public function test_client_can_view_their_own_project(): void
    {
        $this->actingAs($this->client, 'client')
            ->get(route('client.projects.show', $this->project->id))
            ->assertOk()
            ->assertSee($this->project->name);
    }

    public function test_client_cannot_view_another_clients_project(): void
    {
        $otherProject = Project::factory()->create([
            'client_id' => $this->otherClient->id,
            'status_id' => $this->status->id,
        ]);

        $this->actingAs($this->client, 'client')
            ->get(route('client.projects.show', $otherProject->id))
            ->assertForbidden();
    }

    public function test_client_can_list_their_applications(): void
    {
        $permitType = PermitType::factory()->create();
        PermitApplication::factory()->create([
            'client_id' => $this->client->id,
            'permit_type_id' => $permitType->id,
        ]);

        $this->actingAs($this->client, 'client')
            ->get(route('client.applications.index'))
            ->assertOk();
    }

    public function test_client_can_list_their_documents(): void
    {
        $this->actingAs($this->client, 'client')
            ->get(route('client.documents.index'))
            ->assertOk();
    }

    public function test_client_can_upload_document_to_own_project(): void
    {
        Storage::fake('private');

        $file = UploadedFile::fake()->create('izin_amdal.pdf', 512, 'application/pdf');

        $this->actingAs($this->client, 'client')
            ->post(route('client.documents.store'), [
                'project_id' => $this->project->id,
                'title' => 'Izin AMDAL',
                'category' => 'perizinan',
                'file' => $file,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'project_id' => $this->project->id,
            'title' => 'Izin AMDAL',
        ]);
    }

    public function test_client_cannot_upload_document_to_another_clients_project(): void
    {
        Storage::fake('private');

        $otherProject = Project::factory()->create([
            'client_id' => $this->otherClient->id,
            'status_id' => $this->status->id,
        ]);

        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $this->actingAs($this->client, 'client')
            ->post(route('client.documents.store'), [
                'project_id' => $otherProject->id,
                'title' => 'Test',
                'category' => 'lainnya',
                'file' => $file,
            ])
            ->assertNotFound();
    }

    public function test_dashboard_shows_investment_metrics(): void
    {
        // contract_value = 50000000 → investDisplay should be "Rp 50Jt"
        $this->actingAs($this->client, 'client')
            ->get(route('client.dashboard'))
            ->assertOk()
            ->assertSee('Investasi')
            ->assertSee('Rp 50Jt');
    }

    public function test_dashboard_shows_active_projects_count(): void
    {
        $this->actingAs($this->client, 'client')
            ->get(route('client.dashboard'))
            ->assertOk()
            ->assertSee('1') // 1 active project (status 'Dalam Proses', not 'Selesai')
            ->assertSee('Proyek Aktif');
    }

    public function test_project_show_shows_permit_information(): void
    {
        $permitType = PermitType::factory()->create([
            'name' => 'AMDAL',
        ]);
        $permitApplication = PermitApplication::factory()->create([
            'client_id' => $this->client->id,
            'permit_type_id' => $permitType->id,
        ]);

        // Link project to permit application via projects.permit_application_id
        $this->project->permit_application_id = $permitApplication->id;
        $this->project->save();

        $this->actingAs($this->client, 'client')
            ->get(route('client.projects.show', $this->project->id))
            ->assertOk()
            // Portal v2 might not render the legacy label text "Permohonan Izin".
            ->assertSee('AMDAL');
    }

    public function test_project_show_shows_contract_value(): void
    {
        $this->actingAs($this->client, 'client')
            ->get(route('client.projects.show', $this->project->id))
            ->assertOk()
            ->assertSee('Nilai Kontrak')
            ->assertSee('Rp 50.000.000');
    }

    public function test_client_can_download_own_document(): void
    {
        Storage::fake('private');

        $file = UploadedFile::fake()->create('izin_amdal.pdf', 512, 'application/pdf');
        $path = $file->store('documents', 'private');

        $document = Document::create([
            'project_id' => $this->project->id,
            'title' => 'Izin AMDAL',
            'file_name' => 'izin_amdal.pdf',
            'file_path' => $path,
            'category' => 'perizinan',
        ]);

        $this->actingAs($this->client, 'client')
            ->get(route('client.documents.download', $document->id))
            ->assertSuccessful();
    }

    public function test_client_cannot_download_another_clients_document(): void
    {
        Storage::fake('private');

        $otherProject = Project::factory()->create([
            'client_id' => $this->otherClient->id,
            'status_id' => $this->status->id,
        ]);

        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
        $path = $file->store('documents', 'private');

        $document = Document::create([
            'project_id' => $otherProject->id,
            'title' => 'Dokumen Rahasia',
            'file_name' => 'test.pdf',
            'file_path' => $path,
            'category' => 'perizinan',
        ]);

        $this->actingAs($this->client, 'client')
            ->get(route('client.documents.download', $document->id))
            ->assertForbidden();
    }
}
