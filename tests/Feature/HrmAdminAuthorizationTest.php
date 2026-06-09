<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrmAdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $hrManager;

    private User $viewerUser;

    private JobVacancy $vacancy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureTwoFactorVerified::class);

        $roleHr = Role::firstOrCreate(['name' => 'hr_manager'], ['display_name' => 'HR Manager']);
        $roleViewer = Role::firstOrCreate(['name' => 'viewer'], ['display_name' => 'Viewer']);

        $permView = Permission::firstOrCreate(['name' => 'recruitment.view'], ['display_name' => 'View Recruitment', 'group' => 'hrm']);
        $permManage = Permission::firstOrCreate(['name' => 'recruitment.manage'], ['display_name' => 'Manage Recruitment', 'group' => 'hrm']);
        $permManageJobs = Permission::firstOrCreate(['name' => 'recruitment.manage_jobs'], ['display_name' => 'Manage Jobs', 'group' => 'hrm']);
        $permProcess = Permission::firstOrCreate(['name' => 'recruitment.process_applications'], ['display_name' => 'Process Applications', 'group' => 'hrm']);
        $roleHr->permissions()->syncWithoutDetaching([$permView->id, $permManage->id, $permManageJobs->id, $permProcess->id]);

        $this->hrManager = User::factory()->create(['role_id' => $roleHr->id]);
        $this->viewerUser = User::factory()->create(['role_id' => $roleViewer->id]);

        $this->vacancy = JobVacancy::create([
            'title' => 'Staf Administrasi',
            'slug' => 'staf-administrasi',
            'position' => 'Staff',
            'description' => 'Posisi administrasi.',
            'responsibilities' => 'Tanggung jawab.',
            'qualifications' => 'D3 ke atas.',
            'employment_type' => 'full-time',
            'location' => 'Karawang',
            'status' => 'open',
            'deadline' => now()->addMonth()->toDateString(),
        ]);
    }

    public function test_hr_manager_can_list_vacancies(): void
    {
        $this->actingAs($this->hrManager)
            ->get(route('admin.jobs.index'))
            ->assertRedirect(route('admin.recruitment.index', ['tab' => 'jobs']));

        $this->actingAs($this->hrManager)
            ->get(route('admin.recruitment.index', ['tab' => 'jobs']))
            ->assertOk();
    }

    public function test_viewer_cannot_list_vacancies(): void
    {
        $this->actingAs($this->viewerUser)
            ->get(route('admin.jobs.index'))
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_list_vacancies(): void
    {
        $this->get(route('admin.jobs.index'))
            ->assertRedirect();
    }

    public function test_hr_manager_can_view_vacancy_detail(): void
    {
        $this->actingAs($this->hrManager)
            ->get(route('admin.jobs.show', $this->vacancy->id))
            ->assertOk();
    }

    public function test_hr_manager_can_create_vacancy(): void
    {
        $response = $this->actingAs($this->hrManager)
            ->post(route('admin.jobs.store'), [
                'title' => 'Konsultan Senior',
                'position' => 'Senior',
                'description' => 'Deskripsi konsultan senior.',
                'responsibilities' => ['Mengelola proyek lingkungan', 'Laporan perizinan'],
                'qualifications' => ['S1 Teknik Lingkungan', 'Pengalaman 2 tahun'],
                'employment_type' => 'full-time',
                'location' => 'Karawang',
                'status' => 'draft',
                'deadline' => now()->addMonths(2)->toDateString(),
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('job_vacancies', [
            'title' => 'Konsultan Senior',
            'position' => 'Senior',
        ]);
    }

    public function test_viewer_cannot_create_vacancy(): void
    {
        $this->actingAs($this->viewerUser)
            ->post(route('admin.jobs.store'), [
                'title' => 'Test Vacancy',
                'position' => 'Staff',
                'description' => 'Test',
                'responsibilities' => 'Test',
                'qualifications' => 'Test',
                'employment_type' => 'full_time',
                'location' => 'Jakarta',
                'status' => 'draft',
                'deadline' => now()->addMonth()->toDateString(),
            ])
            ->assertForbidden();
    }

    public function test_hr_manager_can_list_applications(): void
    {
        $this->actingAs($this->hrManager)
            ->get(route('admin.applications.index'))
            ->assertOk();
    }

    public function test_hr_manager_can_update_application_status(): void
    {
        $application = JobApplication::create([
            'job_vacancy_id' => $this->vacancy->id,
            'full_name' => 'Andi Pratama',
            'email' => 'andi@example.com',
            'phone' => '081234567890',
            'education_level' => 'S1',
            'major' => 'Teknik Sipil',
            'institution' => 'ITB',
            'cv_path' => 'applications/cv/dummy.pdf',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->hrManager)
            ->patch(route('admin.applications.update-status', $application->id), [
                'status' => 'reviewed',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => 'reviewed',
        ]);
    }
}
