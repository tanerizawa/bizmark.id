<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class ShapefileApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private array $validCoordinates = [
        [107.31234567, -6.32456789],
        [107.31345678, -6.32567890],
        [107.31456789, -6.32345678],
        [107.31234567, -6.32234567],
    ];

    // ==========================================
    // POST /api/shapefile/calculate
    // ==========================================

    public function test_calculate_returns_area_and_perimeter(): void
    {
        $response = $this->postJson('/api/shapefile/calculate', [
            'coordinates' => $this->validCoordinates,
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'area_m2',
                'area_ha',
                'perimeter_m',
                'num_points',
            ]);

        $data = $response->json();
        $this->assertGreaterThan(0, $data['area_m2']);
        $this->assertGreaterThan(0, $data['area_ha']);
        $this->assertGreaterThan(0, $data['perimeter_m']);
        $this->assertEquals(4, $data['num_points']);
    }

    public function test_calculate_rejects_fewer_than_3_coordinates(): void
    {
        $response = $this->postJson('/api/shapefile/calculate', [
            'coordinates' => [
                [107.31, -6.32],
                [107.32, -6.33],
            ],
        ]);

        $response->assertUnprocessable();
    }

    public function test_calculate_rejects_invalid_longitude(): void
    {
        $response = $this->postJson('/api/shapefile/calculate', [
            'coordinates' => [
                [200, -6.32],
                [107.31, -6.33],
                [107.32, -6.34],
            ],
        ]);

        $response->assertUnprocessable();
    }

    public function test_calculate_rejects_invalid_latitude(): void
    {
        $response = $this->postJson('/api/shapefile/calculate', [
            'coordinates' => [
                [107.31, -100],
                [107.32, -6.33],
                [107.33, -6.34],
            ],
        ]);

        $response->assertUnprocessable();
    }

    public function test_calculate_rejects_empty_body(): void
    {
        $response = $this->postJson('/api/shapefile/calculate', []);

        $response->assertUnprocessable();
    }

    public function test_calculate_area_is_consistent(): void
    {
        // Same coords should produce same area
        $r1 = $this->postJson('/api/shapefile/calculate', ['coordinates' => $this->validCoordinates]);
        $r2 = $this->postJson('/api/shapefile/calculate', ['coordinates' => $this->validCoordinates]);

        $this->assertEquals($r1->json('area_m2'), $r2->json('area_m2'));
    }

    // ==========================================
    // POST /api/shapefile/generate (validation only — no DB needed)
    // ==========================================

    public function test_generate_requires_name(): void
    {
        $response = $this->postJson('/api/shapefile/generate', [
            'coordinates' => $this->validCoordinates,
            'email' => 'test@example.com',
            'phone' => '08123456789',
            'agreed_terms' => true,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_generate_requires_coordinates(): void
    {
        $response = $this->postJson('/api/shapefile/generate', [
            'name' => 'Test Lahan',
            'email' => 'test@example.com',
            'phone' => '08123456789',
            'agreed_terms' => true,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['coordinates']);
    }

    public function test_generate_rejects_name_exceeding_max_length(): void
    {
        $response = $this->postJson('/api/shapefile/generate', [
            'coordinates' => $this->validCoordinates,
            'name' => str_repeat('a', 101),
            'email' => 'test@example.com',
            'phone' => '08123456789',
            'agreed_terms' => true,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_generate_requires_email(): void
    {
        $response = $this->postJson('/api/shapefile/generate', [
            'coordinates' => $this->validCoordinates,
            'name' => 'Test Lahan',
            'phone' => '08123456789',
            'agreed_terms' => true,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_generate_requires_phone(): void
    {
        $response = $this->postJson('/api/shapefile/generate', [
            'coordinates' => $this->validCoordinates,
            'name' => 'Test Lahan',
            'email' => 'test@example.com',
            'agreed_terms' => true,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_generate_requires_agreed_terms(): void
    {
        $response = $this->postJson('/api/shapefile/generate', [
            'coordinates' => $this->validCoordinates,
            'name' => 'Test Lahan',
            'email' => 'test@example.com',
            'phone' => '08123456789',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['agreed_terms']);
    }

    public function test_generate_rejects_invalid_email(): void
    {
        $response = $this->postJson('/api/shapefile/generate', [
            'coordinates' => $this->validCoordinates,
            'name' => 'Test Lahan',
            'email' => 'not-an-email',
            'phone' => '08123456789',
            'agreed_terms' => true,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    // ==========================================
    // POST /api/shapefile/check-email
    // ==========================================

    public function test_check_email_returns_false_for_unregistered(): void
    {
        $response = $this->postJson('/api/shapefile/check-email', [
            'email' => 'nonexistent-shptest-'.time().'@example.com',
        ]);

        $response->assertOk()
            ->assertJson(['registered' => false]);
    }

    public function test_check_email_returns_true_for_registered_client(): void
    {
        $email = 'shptest-'.uniqid().'@example.com';
        $client = Client::create([
            'name' => 'SHP Test Client',
            'email' => $email,
            'status' => 'active',
        ]);

        try {
            $response = $this->postJson('/api/shapefile/check-email', [
                'email' => $email,
            ]);

            $response->assertOk()
                ->assertJson(['registered' => true]);
        } finally {
            $client->forceDelete();
        }
    }

    public function test_check_email_requires_valid_email(): void
    {
        $response = $this->postJson('/api/shapefile/check-email', [
            'email' => 'not-an-email',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_generate_blocks_registered_email_without_auth(): void
    {
        $email = 'shptest-block-'.uniqid().'@example.com';
        $client = Client::create([
            'name' => 'SHP Block Test',
            'email' => $email,
            'status' => 'active',
        ]);

        try {
            $response = $this->postJson('/api/shapefile/generate', [
                'coordinates' => $this->validCoordinates,
                'name' => 'Test Lahan',
                'email' => $email,
                'phone' => '08123456789',
                'agreed_terms' => true,
            ]);

            $response->assertStatus(403)
                ->assertJson(['error' => 'Email sudah terdaftar. Silakan login terlebih dahulu.']);
        } finally {
            $client->forceDelete();
        }
    }

    // ==========================================
    // GET /polygon-shp-maker (page)
    // ==========================================

    public function test_polygon_shp_maker_page_loads(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        $this->withoutMiddleware(\Illuminate\View\Middleware\ShareErrorsFromSession::class);
        $this->withoutMiddleware(\App\Http\Middleware\LogReconciliationRequests::class);
        $this->withoutMiddleware(\App\Http\Middleware\NeuralResponseTime::class);

        $response = $this->get('/polygon-shp-maker');

        $response->assertOk();
        $response->assertSee('SHP Online Gratis');
        $response->assertSee('Peta Interaktif');
    }

    public function test_polygon_shp_maker_has_json_ld(): void
    {
        $response = $this->get('/polygon-shp-maker');

        $response->assertOk();
        $response->assertSee('WebApplication', false);
        $response->assertSee('HowTo', false);
        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('FAQPage', false);
    }

    public function test_polygon_shp_maker_has_proper_meta_tags(): void
    {
        $response = $this->get('/polygon-shp-maker');

        $response->assertOk();
        $response->assertSee('polygon shp maker', false);
        $response->assertSee('buat file shp', false);
        $response->assertSee('oss rba', false);
    }

    public function test_polygon_shp_maker_has_sri_integrity(): void
    {
        $response = $this->get('/polygon-shp-maker');

        $response->assertOk();
        $response->assertSee('integrity="sha384-', false);
    }

    public function test_polygon_shp_maker_has_aria_attributes(): void
    {
        $response = $this->get('/polygon-shp-maker');

        $response->assertOk();
        $response->assertSee('aria-label=', false);
        $response->assertSee('role="application"', false);
        $response->assertSee('aria-live=', false);
    }

    public function test_polygon_shp_maker_has_breadcrumb(): void
    {
        $response = $this->get('/polygon-shp-maker');

        $response->assertOk();
        $response->assertSee('aria-label="Breadcrumb"', false);
        $response->assertSee('aria-current="page"', false);
    }

    public function test_polygon_shp_maker_has_faq_section(): void
    {
        $response = $this->get('/polygon-shp-maker');

        $response->assertOk();
        $response->assertSee('Pertanyaan yang Sering Diajukan', false);
        $response->assertSee('Apa itu file SHP', false);
        $response->assertSee('Bagaimana cara membuat file SHP', false);
    }

    public function test_polygon_shp_maker_has_seo_content(): void
    {
        $response = $this->get('/polygon-shp-maker');

        $response->assertOk();
        $response->assertSee('Apa Itu File SHP (Shapefile)?', false);
        $response->assertSee('Mengapa Perlu File SHP untuk OSS?', false);
        $response->assertSee('Fitur Unggulan Polygon SHP Maker', false);
        $response->assertSee('Layanan Terkait', false);
    }

    public function test_navbar_shows_login_button_for_guests(): void
    {
        $response = $this->get('/polygon-shp-maker');

        $response->assertOk();
        // Navbar shows login link for guests — Indonesian locale shows 'Masuk ke Portal'
        $response->assertSee('Masuk', false);
        $response->assertSee('/login', false);
    }

    public function test_navbar_shows_profile_for_authenticated_client(): void
    {
        $client = Client::create([
            'name' => 'PT Test Client',
            'email' => 'shptest-nav-'.uniqid().'@example.com',
            'status' => 'active',
        ]);

        try {
            $response = $this->actingAs($client, 'client')->get('/polygon-shp-maker');

            $response->assertOk();
            $response->assertSee('PT Test Client', false);
            $response->assertDontSee('Login / Daftar', false);
        } finally {
            $client->forceDelete();
        }
    }

    public function test_page_prefills_form_for_authenticated_client(): void
    {
        $client = Client::create([
            'name' => 'PT Prefill Test',
            'email' => 'prefill-'.uniqid().'@bizmark.id',
            'phone' => '081234567890',
            'company_name' => 'PT Prefill',
            'status' => 'active',
        ]);

        try {
            $response = $this->actingAs($client, 'client')->get('/polygon-shp-maker');

            $response->assertOk();
            $response->assertSee($client->email, false);
            $response->assertSee('PT Prefill', false);
        } finally {
            $client->forceDelete();
        }
    }
}
