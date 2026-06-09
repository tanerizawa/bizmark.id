<?php

namespace Tests\Feature;

use App\Models\Kbli;
use App\Jobs\AnalyzeServiceInquiryJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ServiceInquiryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_rate_limit_requires_valid_email(): void
    {
        $response = $this->postJson('/konsultasi-gratis/api/check-rate-limit', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('allowed', false)
            ->assertJsonPath('message', 'Email tidak valid.')
            ->assertJsonStructure([
                'allowed',
                'message',
                'errors' => ['email'],
            ]);
    }

    public function test_store_rejects_invalid_optional_kbli_code(): void
    {
        $payload = [
            'email' => 'lead@example.com',
            'company_name' => 'PT Contoh Sukses',
            'phone' => '081234567890',
            'contact_person' => 'Budi',
            'business_activity' => 'Jasa konsultan bisnis',
            'kbli_code' => 'ABCDE',
            'business_scale' => 'small',
            'location_province' => 'DKI Jakarta',
            'location_city' => 'Jakarta Selatan',
            'location_category' => 'commercial',
            'estimated_investment' => '100m_500m',
        ];

        $response = $this->postJson('/konsultasi-gratis', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure([
                'success',
                'errors' => ['kbli_code'],
            ]);
    }

    public function test_store_accepts_valid_optional_kbli_code(): void
    {
        Queue::fake();

        Kbli::create([
            'code' => '62010',
            'description' => 'Aktivitas Pemrograman Komputer',
            'sector' => 'J',
            'category' => 'Menengah Rendah',
            'is_active' => true,
            'complexity_level' => 'medium',
        ]);

        $payload = [
            'email' => 'lead@example.com',
            'company_name' => 'PT Contoh Sukses',
            'phone' => '081234567890',
            'contact_person' => 'Budi',
            'business_activity' => 'Jasa konsultan bisnis',
            'kbli_code' => '62010',
            'business_scale' => 'small',
            'location_province' => 'DKI Jakarta',
            'location_city' => 'Jakarta Selatan',
            'location_category' => 'commercial',
            'estimated_investment' => '100m_500m',
        ];

        $response = $this->postJson('/konsultasi-gratis', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'inquiry_number',
                'message',
            ]);

        Queue::assertPushed(AnalyzeServiceInquiryJob::class);
    }
}
