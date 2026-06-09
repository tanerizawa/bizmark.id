<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $unauthorized;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureTwoFactorVerified::class);

        $roleAdmin = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Admin']);
        $roleViewer = Role::firstOrCreate(['name' => 'viewer'], ['display_name' => 'Viewer']);

        $permManage = Permission::firstOrCreate(
            ['name' => 'settings.manage'],
            ['display_name' => 'Manage Settings', 'group' => 'settings']
        );
        $roleAdmin->permissions()->syncWithoutDetaching([$permManage->id]);

        $this->admin = User::factory()->create(['role_id' => $roleAdmin->id]);
        $this->unauthorized = User::factory()->create(['role_id' => $roleViewer->id]);
    }

    // ── Dashboard Page Access ─────────────────────────────────────

    public function test_dashboard_page_loads_for_authenticated_admin(): void
    {
        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_dashboard_redirects_unauthenticated_users(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect();
    }

    public function test_dashboard_redirects_home_to_dashboard(): void
    {
        $this->actingAs($this->admin)
            ->get(route('home'))
            ->assertRedirect(route('dashboard'));
    }

    // ── Dashboard Content Rendering ───────────────────────────────

    public function test_dashboard_renders_kpi_sections(): void
    {
        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeInOrder([
                'Dashboard',
                'Urgent',
                'Runway Kas',
            ]);
    }

    public function test_dashboard_renders_critical_alerts_section(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk();

        $content = $response->getContent();
        $this->assertTrue(
            str_contains($content, 'Fokus Kritis') || str_contains($content, 'Semua Terkendali'),
            'Dashboard must render critical alerts section (Fokus Kritis or Semua Terkendali).'
        );
    }

    public function test_dashboard_renders_project_status_distribution(): void
    {
        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Distribusi Proyek');
    }

    public function test_dashboard_renders_recent_activities(): void
    {
        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Aktivitas Terkini');
    }

    // ── Dashboard Cache Clearing ──────────────────────────────────

    public function test_dashboard_cache_clear_requires_settings_manage_permission(): void
    {
        $this->actingAs($this->unauthorized)
            ->post(route('dashboard.clear-cache'))
            ->assertForbidden();
    }

    public function test_authorized_user_can_clear_dashboard_cache(): void
    {
        $this->actingAs($this->admin)
            ->post(route('dashboard.clear-cache'))
            ->assertRedirect();
    }

    public function test_dashboard_cache_clear_redirects_with_success_message(): void
    {
        $this->actingAs($this->admin)
            ->post(route('dashboard.clear-cache'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');
    }

    public function test_unauthenticated_user_cannot_clear_dashboard_cache(): void
    {
        $this->post(route('dashboard.clear-cache'))
            ->assertRedirect();
    }

    // ── Admin Profile ─────────────────────────────────────────────

    public function test_admin_profile_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.profile.edit'))
            ->assertOk();
    }

    public function test_admin_profile_page_redirects_unauthenticated(): void
    {
        $this->get(route('admin.profile.edit'))
            ->assertRedirect();
    }

    // ── Admin Notifications ───────────────────────────────────────

    public function test_notifications_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.notifications'))
            ->assertOk();
    }

    public function test_notifications_page_redirects_unauthenticated(): void
    {
        $this->get(route('admin.notifications'))
            ->assertRedirect();
    }

    // ── Admin Search ──────────────────────────────────────────────

    public function test_admin_search_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.search').'?q=test')
            ->assertOk();
    }

    public function test_admin_search_redirects_unauthenticated(): void
    {
        $this->get(route('admin.search'))
            ->assertRedirect();
    }
}
