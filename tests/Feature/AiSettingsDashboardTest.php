<?php

namespace Tests\Feature;

use App\Models\AISetting;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiSettingsDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->regularUser = User::factory()->create();
    }

    public function test_provider_settings_page_loads(): void
    {
        AISetting::factory()->provider('openrouter')->create([
            'key' => 'providers.openrouter.key',
            'value' => null,
            'is_encrypted' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.ai-settings.index', ['category' => 'provider']));

        $response->assertOk();
        $response->assertSee('openrouter');
    }

    public function test_provider_settings_can_be_updated(): void
    {
        AISetting::factory()->provider('openrouter')->create([
            'key' => 'providers.openrouter.url',
            'value' => 'https://openrouter.ai/api/v1',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.ai-settings.update'), [
                'settings' => [
                    'providers.openrouter.url' => 'https://custom.openrouter.io/v1',
                ],
            ]);

        $response->assertSessionHas('success');
        $this->assertSame(
            'https://custom.openrouter.io/v1',
            config('ai.providers.openrouter.url')
        );
    }

    public function test_encrypted_provider_key_is_masked_in_display(): void
    {
        $setting = new AISetting();
        $setting->is_encrypted = true;
        $setting->key = 'providers.openrouter.key';
        $setting->value = 'sk-real-key-value';
        $setting->category = 'provider';
        $setting->data_type = 'string';
        $setting->group_name = 'openrouter';
        $setting->save();

        $setting->refresh();

        $this->assertSame('••••••••', $setting->display_value);
        $this->assertSame('sk-real-key-value', $setting->value);
    }

    public function test_test_provider_endpoint_rejects_unknown_provider(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ai-settings.test-provider', 'nonexistent'));

        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }

    public function test_sdk_status_page_shows_all_providers(): void
    {
        config(['ai.providers.openrouter' => ['driver' => 'openrouter', 'key' => 'test', 'url' => 'https://test.com']]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.ai-settings.sdk-status'));

        $response->assertOk();
        $response->assertSee('openrouter');
    }

    public function test_non_admin_cannot_access_ai_settings(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.ai-settings.index'));

        $response->assertStatus(403);
    }

    public function test_provider_update_persists_to_database(): void
    {
        AISetting::factory()->provider('openrouter')->create([
            'key' => 'providers.openrouter.key',
            'value' => 'sk-original-key',
            'is_encrypted' => true,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.ai-settings.update'), [
                'settings' => [
                    'providers.openrouter.key' => 'sk-new-key',
                ],
            ]);

        $dbValue = AISetting::where('key', 'providers.openrouter.key')->first()->value;
        $this->assertSame('sk-new-key', $dbValue);
    }
}
