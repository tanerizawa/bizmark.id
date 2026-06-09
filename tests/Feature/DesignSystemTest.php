<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class DesignSystemTest extends TestCase
{
    use RefreshDatabase;

    // ─── Design Tokens File Existence ────────────────────

    public function test_design_tokens_css_file_exists(): void
    {
        $this->assertFileExists(resource_path('css/design-tokens.css'));
    }

    public function test_app_css_file_exists(): void
    {
        $this->assertFileExists(resource_path('css/app.css'));
    }

    // ─── Required Design Tokens ──────────────────────────

    /**
     * @dataProvider requiredDesignTokensProvider
     */
    public function test_required_design_token_exists(string $variableName): void
    {
        $content = file_get_contents(resource_path('css/design-tokens.css'));

        $this->assertStringContainsString(
            $variableName,
            $content,
            "Required design token `{$variableName}` is missing from design-tokens.css"
        );
    }

    public static function requiredDesignTokensProvider(): array
    {
        return [
            'primary color' => ['--color-primary'],
            'primary light' => ['--color-primary-light'],
            'primary dark' => ['--color-primary-dark'],
            'secondary color' => ['--color-secondary'],
            'accent color' => ['--color-accent'],
            'success color' => ['--color-success'],
            'warning color' => ['--color-warning'],
            'error color' => ['--color-error'],
            'info color' => ['--color-info'],
            'white' => ['--color-white'],
            'gray-50' => ['--color-gray-50'],
            'gray-100' => ['--color-gray-100'],
            'gray-200' => ['--color-gray-200'],
            'gray-300' => ['--color-gray-300'],
            'gray-400' => ['--color-gray-400'],
            'gray-500' => ['--color-gray-500'],
            'gray-600' => ['--color-gray-600'],
            'gray-700' => ['--color-gray-700'],
            'gray-800' => ['--color-gray-800'],
            'gray-900' => ['--color-gray-900'],
            'black' => ['--color-black'],
            'spacing 0' => ['--spacing-0'],
            'spacing 1' => ['--spacing-1'],
            'spacing 2' => ['--spacing-2'],
            'spacing 3' => ['--spacing-3'],
            'spacing 4' => ['--spacing-4'],
            'spacing 5' => ['--spacing-5'],
            'spacing 6' => ['--spacing-6'],
            'spacing 8' => ['--spacing-8'],
            'font sans' => ['--font-family-sans'],
            'font mono' => ['--font-family-mono'],
            'font display' => ['--font-family-display'],
            'font size xs' => ['--font-size-xs'],
            'font size sm' => ['--font-size-sm'],
            'font size base' => ['--font-size-base'],
            'font size lg' => ['--font-size-lg'],
            'font size xl' => ['--font-size-xl'],
            'font size 2xl' => ['--font-size-2xl'],
            'font size 3xl' => ['--font-size-3xl'],
            'font size 4xl' => ['--font-size-4xl'],
            'radius none' => ['--radius-none'],
            'radius sm' => ['--radius-sm'],
            'radius md' => ['--radius-md'],
            'radius lg' => ['--radius-lg'],
            'radius xl' => ['--radius-xl'],
            'radius 2xl' => ['--radius-2xl'],
            'radius full' => ['--radius-full'],
        ];
    }

    // ─── Blade Component Files Existence ─────────────────

    /**
     * @dataProvider requiredBladeComponentProvider
     */
    public function test_required_blade_component_exists(string $componentPath): void
    {
        $this->assertFileExists(
            $componentPath,
            "Required Blade component file is missing: {$componentPath}"
        );
    }

    public static function requiredBladeComponentProvider(): array
    {
        $base = getcwd().'/resources/views/components/ui';

        return [
            'accordion' => ["{$base}/accordion.blade.php"],
            'alert' => ["{$base}/alert.blade.php"],
            'avatar' => ["{$base}/avatar.blade.php"],
            'badge' => ["{$base}/badge.blade.php"],
            'breadcrumb' => ["{$base}/breadcrumb.blade.php"],
            'button' => ["{$base}/button.blade.php"],
            'button-spinner' => ["{$base}/button-spinner.blade.php"],
            'card' => ["{$base}/card.blade.php"],
            'checkbox' => ["{$base}/checkbox.blade.php"],
            'dropdown' => ["{$base}/dropdown.blade.php"],
            'dropdown-divider' => ["{$base}/dropdown-divider.blade.php"],
            'dropdown-item' => ["{$base}/dropdown-item.blade.php"],
            'empty-state' => ["{$base}/empty-state.blade.php"],
            'file-upload' => ["{$base}/file-upload.blade.php"],
            'input' => ["{$base}/input.blade.php"],
            'modal' => ["{$base}/modal.blade.php"],
            'pagination' => ["{$base}/pagination.blade.php"],
            'progress' => ["{$base}/progress.blade.php"],
            'radio-group' => ["{$base}/radio-group.blade.php"],
            'select' => ["{$base}/select.blade.php"],
            'skeleton' => ["{$base}/skeleton.blade.php"],
            'stat-card' => ["{$base}/stat-card.blade.php"],
            'table' => ["{$base}/table.blade.php"],
            'tabs' => ["{$base}/tabs.blade.php"],
            'textarea' => ["{$base}/textarea.blade.php"],
            'toast' => ["{$base}/toast.blade.php"],
            'toggle' => ["{$base}/toggle.blade.php"],
            'tooltip' => ["{$base}/tooltip.blade.php"],
        ];
    }

    // ─── Component Dark Mode Coverage ────────────────────

    /**
     * @dataProvider componentDarkModeProvider
     */
    public function test_component_has_dark_mode_support(string $componentPath): void
    {
        $content = file_get_contents($componentPath);

        $hasTailwindDark = str_contains($content, 'dark:');
        $hasCssVariableTheme = preg_match('/var\(--(?:dark|apple)/', $content);

        $this->assertTrue(
            $hasTailwindDark || $hasCssVariableTheme,
            "Component {$componentPath} does not have dark mode support — add dark: prefix or CSS custom properties"
        );
    }

    public static function componentDarkModeProvider(): array
    {
        $base = getcwd().'/resources/views/components/ui';

        return [
            'accordion' => ["{$base}/accordion.blade.php"],
            'alert' => ["{$base}/alert.blade.php"],
            'badge' => ["{$base}/badge.blade.php"],
            'button' => ["{$base}/button.blade.php"],
            'card' => ["{$base}/card.blade.php"],
            'dropdown' => ["{$base}/dropdown.blade.php"],
            'input' => ["{$base}/input.blade.php"],
            'modal' => ["{$base}/modal.blade.php"],
            'pagination' => ["{$base}/pagination.blade.php"],
            'select' => ["{$base}/select.blade.php"],
            'table' => ["{$base}/table.blade.php"],
            'tabs' => ["{$base}/tabs.blade.php"],
            'textarea' => ["{$base}/textarea.blade.php"],
            'toggle' => ["{$base}/toggle.blade.php"],
        ];
    }

    // ─── Accessibility: Role Attributes ──────────────────

    public function test_table_component_has_accessibility_attributes(): void
    {
        $content = file_get_contents(resource_path('views/components/ui/table.blade.php'));

        $this->assertStringContainsString('scope="col"', $content);
    }

    public function test_modal_component_has_accessibility_attributes(): void
    {
        $content = file_get_contents(resource_path('views/components/ui/modal.blade.php'));

        $this->assertStringContainsString('role="dialog"', $content);
        $this->assertStringContainsString('aria-modal="true"', $content);
        $this->assertStringContainsString('aria-labelledby', $content);
    }

    public function test_button_component_has_focus_ring(): void
    {
        $content = file_get_contents(resource_path('views/components/ui/button.blade.php'));

        $this->assertStringContainsString('focus:outline-none', $content);
        $this->assertStringContainsString('focus:ring-2', $content);
    }

    public function test_alert_component_has_role_alert(): void
    {
        $content = file_get_contents(resource_path('views/components/ui/alert.blade.php'));

        $this->assertStringContainsString('role="alert"', $content);
    }

    // ─── Alpine.js Usage Verification ────────────────────

    public function test_key_components_use_alpine_js_directives(): void
    {
        $alpineComponents = [
            resource_path('views/components/ui/accordion.blade.php'),
            resource_path('views/components/ui/dropdown.blade.php'),
            resource_path('views/components/ui/modal.blade.php'),
            resource_path('views/components/ui/tabs.blade.php'),
            resource_path('views/components/ui/toast.blade.php'),
            resource_path('views/components/ui/tooltip.blade.php'),
        ];

        foreach ($alpineComponents as $path) {
            if (! file_exists($path)) {
                continue;
            }

            $content = file_get_contents($path);
            $this->assertStringContainsString(
                'x-data',
                $content,
                "Component {$path} should use Alpine.js x-data directive"
            );
        }
    }

    // ─── Zero Inline JavaScript Enforcement ──────────────

    public function test_components_have_no_inline_js_event_handlers(): void
    {
        $componentDir = resource_path('views/components/ui');
        $files = glob("{$componentDir}/*.blade.php");

        $offendingHandlers = ['onmouseover', 'onmouseout', 'onclick', 'onchange', 'onsubmit', 'onload'];

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $basename = basename($file);

            foreach ($offendingHandlers as $handler) {
                $this->assertStringNotContainsString(
                    $handler.'=',
                    $content,
                    "Component {$basename} contains inline JS event handler `{$handler}=` — use Alpine.js instead"
                );
            }
        }
    }

    // ─── Zero Inline Styles Enforcement ──────────────────

    public function test_components_have_no_inline_styles(): void
    {
        $componentDir = resource_path('views/components/ui');
        $files = glob("{$componentDir}/*.blade.php");

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $basename = basename($file);

            // Skip files that intentionally use style="" for dynamic CSS or background images
            $skippedFiles = ['select.blade.php', 'toast.blade.php', 'progress.blade.php', 'skeleton.blade.php'];

            if (in_array($basename, $skippedFiles)) {
                continue;
            }

            // Count occurrences of style=" — allow only if it's a placeholder or dynamic
            $occurrences = substr_count($content, 'style="');
            $styleAttrOccurrences = substr_count($content, 'style="display: none;"');

            $this->assertTrue(
                $occurrences <= $styleAttrOccurrences,
                "Component {$basename} contains style=\"...\" — use Tailwind classes instead"
            );
        }
    }

    // ─── Component Uses Named Parameters ─────────────────

    public function test_components_use_named_parameters_via_props(): void
    {
        $componentDir = resource_path('views/components/ui');
        $files = glob("{$componentDir}/*.blade.php");

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $basename = basename($file);

            // All components should define @props
            $this->assertStringContainsString(
                '@props',
                $content,
                "Component {$basename} does not define @props — all components must use named parameters"
            );
        }
    }

    // ─── Design Token Integrity: Components Reference Tokens ──

    /**
     * @dataProvider componentReferencesDesignTokensProvider
     */
    public function test_component_references_design_tokens(string $componentPath, string $expectedToken): void
    {
        if (! file_exists($componentPath)) {
            $this->markTestSkipped("Component file does not exist: {$componentPath}");
        }

        $content = file_get_contents($componentPath);

        $this->assertStringContainsString(
            $expectedToken,
            $content,
            'Component '.basename($componentPath)." should reference design token `{$expectedToken}`"
        );
    }

    public static function componentReferencesDesignTokensProvider(): array
    {
        $base = getcwd().'/resources/views/components/ui';

        return [
            'badge primary' => ["{$base}/badge.blade.php", '--apple-blue'],
            'button primary' => ["{$base}/button.blade.php", '--apple-blue'],
            'pagination primary' => ["{$base}/pagination.blade.php", '--color-primary'],
            'input primary focus' => ["{$base}/input.blade.php", '--apple-blue'],
            'select primary focus' => ["{$base}/select.blade.php", '--apple-blue'],
        ];
    }

    // ─── CSS @import Architecture ────────────────────────

    public function test_app_css_uses_v4_import_syntax(): void
    {
        $content = file_get_contents(resource_path('css/app.css'));

        // Must use @import "tailwindcss"; not @tailwind directives
        $this->assertStringContainsString(
            '@import',
            $content,
            'app.css should use Tailwind CSS v4 @import syntax'
        );
    }

    // ─── Tailwind v4 @theme Section ──────────────────────

    /**
     * @dataProvider appCssContainsThemeSectionProvider
     */
    public function test_app_css_contains_theme_section(string $expectedContent): void
    {
        $content = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString(
            $expectedContent,
            $content,
            "app.css should contain `{$expectedContent}` for Tailwind v4 compatibility"
        );
    }

    public static function appCssContainsThemeSectionProvider(): array
    {
        return [
            '@theme block' => ['@theme'],
            '@source directive' => ['@source'],
            'font-sans definition' => ['--font-sans'],
            'primary color in theme' => ['--color-primary'],
        ];
    }

    // ─── Responsive Meta Tag Presence ────────────────────

    public function test_landing_layout_has_viewport_meta(): void
    {
        // Test by checking if a public page has proper viewport meta tag
        $response = $this->withSession(['locale' => 'id'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('viewport', false);
    }

    // ─── Favicon Presence ────────────────────────────────

    public function test_favicon_file_exists(): void
    {
        $this->assertFileExists(public_path('favicon.ico'));
    }
}
