<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Notifications\ClientVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ClientRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_client_and_redirects(): void
    {
        Notification::fake();

        $response = $this->post('/client/register', [
            'name' => 'PT Maju Jaya',
            'company_name' => 'PT Maju Jaya',
            'email' => 'client@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('clients', [
            'email' => 'client@example.com',
            'status' => 'active',
            'client_type' => 'company',
        ]);

        Notification::assertSentTo(
            Client::where('email', 'client@example.com')->first(),
            ClientVerifyEmail::class
        );
    }

    public function test_registration_requires_email(): void
    {
        $response = $this->post('/client/register', [
            'name' => 'Test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post('/client/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'wrong',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        Client::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/client/register', [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_restores_soft_deleted_client(): void
    {
        Notification::fake();

        $client = Client::factory()->create(['email' => 'deleted@example.com']);
        $client->delete();

        $this->assertSoftDeleted('clients', ['email' => 'deleted@example.com']);

        $response = $this->post('/client/register', [
            'name' => 'Restored User',
            'email' => 'deleted@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'terms' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $restored = Client::where('email', 'deleted@example.com')->first();
        $this->assertNotNull($restored, 'Client should be restored (not soft-deleted)');
        $this->assertNull($restored->deleted_at);
    }

    public function test_individual_client_sets_type_correctly(): void
    {
        Notification::fake();

        $response = $this->post('/client/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('clients', [
            'email' => 'budi@example.com',
            'client_type' => 'individual',
        ]);
    }

    public function test_client_can_login_after_registration(): void
    {
        Notification::fake();

        Client::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this->post('/client/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs(
            Client::where('email', 'login@example.com')->first(),
            'client'
        );
    }
}
