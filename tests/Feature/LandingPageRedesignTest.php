<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LandingPageRedesignTest extends TestCase
{
    private User $author;

    protected function setUp(): void
    {
        parent::setUp();
        $this->author = User::factory()->create();
    }

    // ──────────────────────────────────────────────────────────
    // SPRINT 1: Accessibility & Security
    // ──────────────────────────────────────────────────────────

    /** @test */
    public function hero_dashboard_mockup_has_accessible_figure_element(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('role="img"', false);
        // Verify the mockup is wrapped in a <figure> not a plain <div>
        $response->assertSee('<figure class="hidden md:flex', false);
    }

    /** @test */
    public function hero_has_noscript_fallback_form(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('<noscript>', false);
        $response->assertSee('hero-quickcheck-noscript', false);
        $response->assertSee('Jenis usaha', false);
    }

    /** @test */
    public function hero_noscript_fallback_includes_both_locales(): void
    {
        $idResponse = $this->withSession(['locale' => 'id'])->get('/');
        $enResponse = $this->withSession(['locale' => 'en'])->get('/en');

        $idResponse->assertStatus(200);
        $enResponse->assertStatus(200);

        $idResponse->assertSee('Jenis usaha (cth: kafe, pabrik kemasan)', false);
        $enResponse->assertSee('Business type (e.g. coffee shop, packaging factory)', false);
    }

    /** @test */
    public function process_page_accordion_uses_button_elements(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/proses');

        $response->assertStatus(200);
        $response->assertSee('aria-controls="process-stage-', false);
        $response->assertSee('role="region"', false);
        $response->assertSee('aria-labelledby="process-stage-', false);
        $response->assertSee('aria-expanded', false);
        $response->assertDontSee('role="button" tabindex="0"', false);
    }

    /** @test */
    public function process_page_has_timeline_connector_class(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/proses');

        $response->assertStatus(200);
        $response->assertSee('process-stages', false);
    }

    /** @test */
    public function about_page_has_timeline_scroll_hint(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/tentang');

        $response->assertStatus(200);
        $response->assertSee('h-timeline-scroll-hint', false);
    }

    /** @test */
    public function about_page_scroll_hint_localized(): void
    {
        $idResponse = $this->withSession(['locale' => 'id'])->get('/tentang');
        $enResponse = $this->withSession(['locale' => 'en'])->get('/en/about');

        $idResponse->assertStatus(200);
        $enResponse->assertStatus(200);

        $idResponse->assertSee('Geser ke samping', false);
        $enResponse->assertSee('Scroll to explore', false);
    }

    /** @test */
    public function pricing_page_faq_allows_multiple_open(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/harga');

        $response->assertStatus(200);
        $response->assertSee('open: []', false);
        $response->assertSee('open.includes(', false);
        $response->assertSee('open.filter(x => x !==', false);
        $response->assertDontSee('open: -1', false);
    }

    /** @test */
    public function ecosystem_hub_tool_cards_have_distinct_colors(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('#b8860b', false);
        $response->assertSee('#047857', false);
        $response->assertSee('#1d4ed8', false);
        $response->assertSee('#b45309', false);
    }

    /** @test */
    public function ecosystem_hub_tool_cards_have_color_variables(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('--tool-accent:', false);
        $response->assertSee('--tool-accent-bg:', false);
    }

    // ──────────────────────────────────────────────────────────
    // SPRINT 2: External Link Security
    // ──────────────────────────────────────────────────────────

    /** @test */
    public function pricing_whatsapp_link_has_noopener_noreferrer(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/harga');

        $response->assertStatus(200);
        $response->assertSee('rel="noopener noreferrer"', false);
    }

    /** @test */
    public function final_cta_whatsapp_link_is_secure(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('rel="noopener noreferrer"', false);
    }

    /** @test */
    public function status_page_government_links_are_secure(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/status');

        $response->assertStatus(200);
        $response->assertSee('rel="noopener noreferrer nofollow"', false);
    }

    // ──────────────────────────────────────────────────────────
    // SPRINT 3: Layout & CTA
    // ──────────────────────────────────────────────────────────

    /** @test */
    public function landing_layout_has_sticky_cta_bar(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('scrollCta()', false);
        $response->assertSee('Cek Izin Gratis', false);
        $response->assertSee('IntersectionObserver', false);
    }

    /** @test */
    public function sticky_cta_localized_for_english(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/en');

        $response->assertStatus(200);
        $response->assertSee('Free Permit Check', false);
    }

    // ──────────────────────────────────────────────────────────
    // CROSS-CUTTING: All subpages respond
    // ──────────────────────────────────────────────────────────

    /** @test */
    public function process_page_id_returns_200(): void
    {
        $this->withSession(['locale' => 'id'])->get('/proses')->assertStatus(200);
    }

    /** @test */
    public function process_page_en_returns_200(): void
    {
        $this->withSession(['locale' => 'en'])->get('/en/process')->assertStatus(200);
    }

    /** @test */
    public function about_page_id_returns_200(): void
    {
        $this->withSession(['locale' => 'id'])->get('/tentang')->assertStatus(200);
    }

    /** @test */
    public function about_page_en_returns_200(): void
    {
        $this->withSession(['locale' => 'en'])->get('/en/about')->assertStatus(200);
    }

    /** @test */
    public function pricing_page_id_returns_200(): void
    {
        $this->withSession(['locale' => 'id'])->get('/harga')->assertStatus(200);
    }

    /** @test */
    public function pricing_page_en_returns_200(): void
    {
        $this->withSession(['locale' => 'en'])->get('/en/pricing')->assertStatus(200);
    }

    /** @test */
    public function status_page_id_returns_200(): void
    {
        $this->withSession(['locale' => 'id'])->get('/status')->assertStatus(200);
    }

    /** @test */
    public function status_page_en_returns_200(): void
    {
        $this->withSession(['locale' => 'en'])->get('/en/status')->assertStatus(200);
    }

    // ──────────────────────────────────────────────────────────
    // CSS Verification
    // ──────────────────────────────────────────────────────────

    /** @test */
    public function landing_css_contains_updated_accent_color(): void
    {
        $cssPath = base_path('resources/css/design-tokens.css');
        $content = file_get_contents($cssPath);

        $this->assertStringContainsString('--color-accent: #8b6914', $content);
        $this->assertStringNotContainsString('--color-accent: #b8860b', $content);
    }

    /** @test */
    public function landing_css_contains_accent_text_tokens(): void
    {
        $cssPath = base_path('resources/css/landing.css');
        $content = file_get_contents($cssPath);

        $this->assertStringContainsString('--color-accent-text:', $content);
        $this->assertStringContainsString('--color-accent-text-light:', $content);
    }

    /** @test */
    public function landing_css_contains_timeline_connector_styles(): void
    {
        $cssPath = base_path('resources/css/landing.css');
        $content = file_get_contents($cssPath);

        $this->assertStringContainsString('.process-stages', $content);
        $this->assertStringContainsString('.process-stages::before', $content);
    }

    /** @test */
    public function landing_css_contains_scroll_hint_styles(): void
    {
        $cssPath = base_path('resources/css/landing.css');
        $content = file_get_contents($cssPath);

        $this->assertStringContainsString('.h-timeline-scroll-hint', $content);
    }
}
