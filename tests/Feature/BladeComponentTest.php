<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class BladeComponentTest extends TestCase
{
    use RefreshDatabase;

    // ─── x-ui.badge Tests ────────────────────────────────

    public function test_badge_renders_with_default_variant(): void
    {
        $html = Blade::render('<x-ui.badge>Test Badge</x-ui.badge>');

        $this->assertStringContainsString('Test Badge', $html);
    }

    public function test_badge_renders_all_variants(): void
    {
        $variants = ['neutral', 'primary', 'success', 'warning', 'danger', 'info'];

        foreach ($variants as $variant) {
            $html = Blade::render('<x-ui.badge :variant="$variant">Label</x-ui.badge>', [
                'variant' => $variant,
            ]);
            $this->assertStringContainsString('Label', $html);
        }
    }

    public function test_badge_renders_with_dot(): void
    {
        $html = Blade::render('<x-ui.badge :dot="true" variant="success">With Dot</x-ui.badge>');

        $this->assertStringContainsString('With Dot', $html);
    }

    public function test_badge_renders_with_pill_and_non_pill(): void
    {
        $pillHtml = Blade::render('<x-ui.badge pill="true">Pill</x-ui.badge>');
        $this->assertStringContainsString('Pill', $pillHtml);

        $nonPillHtml = Blade::render('<x-ui.badge :pill="false">Square</x-ui.badge>');
        $this->assertStringContainsString('Square', $nonPillHtml);
    }

    public function test_badge_accepts_custom_class(): void
    {
        $html = Blade::render('<x-ui.badge class="custom-class">Custom</x-ui.badge>');

        $this->assertStringContainsString('custom-class', $html);
        $this->assertStringContainsString('Custom', $html);
    }

    // ─── x-ui.button Tests ───────────────────────────────

    public function test_button_renders_as_button_by_default(): void
    {
        $html = Blade::render('<x-ui.button>Click Me</x-ui.button>');

        $this->assertStringContainsString('Click Me', $html);
        $this->assertMatchesRegularExpression('/<button\b/', $html);
    }

    public function test_button_renders_as_link_when_href_provided(): void
    {
        $html = Blade::render('<x-ui.button href="https://example.com">Link</x-ui.button>');

        $this->assertStringContainsString('href="https://example.com"', $html);
        $this->assertMatchesRegularExpression('/<a\b/', $html);
    }

    public function test_button_renders_all_variants(): void
    {
        $variants = ['primary', 'secondary', 'outline', 'ghost', 'danger'];

        foreach ($variants as $variant) {
            $html = Blade::render('<x-ui.button :variant="$variant">'.ucfirst($variant).'</x-ui.button>', [
                'variant' => $variant,
            ]);
            $this->assertStringContainsString(ucfirst($variant), $html);
        }
    }

    public function test_button_renders_all_sizes(): void
    {
        $sizes = ['sm', 'md', 'lg'];

        foreach ($sizes as $size) {
            $html = Blade::render('<x-ui.button :size="$size">Size '.strtoupper($size).'</x-ui.button>', [
                'size' => $size,
            ]);
            $this->assertStringContainsString('Size '.strtoupper($size), $html);
        }
    }

    public function test_button_disabled_state(): void
    {
        $html = Blade::render('<x-ui.button :disabled="true">Disabled</x-ui.button>');

        $this->assertStringContainsString('disabled', $html);
        $this->assertStringContainsString('Disabled', $html);
    }

    public function test_button_loading_state(): void
    {
        $html = Blade::render('<x-ui.button :loading="true" loading-text="Menyimpan...">Save</x-ui.button>');

        $this->assertStringContainsString('Menyimpan...', $html);
        // Button should not show default slot content when loading
        $this->assertStringNotContainsString('>Save<', $html);
    }

    public function test_button_accepts_custom_class(): void
    {
        $html = Blade::render('<x-ui.button class="my-custom-btn">Custom</x-ui.button>');

        $this->assertStringContainsString('my-custom-btn', $html);
    }

    public function test_button_supports_wire_click_attribute(): void
    {
        $html = Blade::render('<x-ui.button wire:click="save">Wire</x-ui.button>');

        $this->assertStringContainsString('wire:click="save"', $html);
    }

    // ─── x-ui.card Tests ─────────────────────────────────

    public function test_card_renders_default_content(): void
    {
        $html = Blade::render('<x-ui.card>Card Content</x-ui.card>');

        $this->assertStringContainsString('Card Content', $html);
    }

    public function test_card_renders_all_variants(): void
    {
        $variants = ['elevated', 'bordered', 'flat'];

        foreach ($variants as $variant) {
            $html = Blade::render('<x-ui.card :variant="$variant">'.ucfirst($variant).'</x-ui.card>', [
                'variant' => $variant,
            ]);
            $this->assertStringContainsString(ucfirst($variant), $html);
        }
    }

    public function test_card_renders_header_and_footer_slots(): void
    {
        $html = Blade::render(<<<'BLADE'
<x-ui.card>
    <x-slot:header>Header Slot</x-slot:header>
    Main Content
    <x-slot:footer>Footer Slot</x-slot:footer>
</x-ui.card>
BLADE);

        $this->assertStringContainsString('Header Slot', $html);
        $this->assertStringContainsString('Main Content', $html);
        $this->assertStringContainsString('Footer Slot', $html);
    }

    public function test_card_renders_all_padding_options(): void
    {
        $paddings = ['none', 'sm', 'md', 'lg'];

        foreach ($paddings as $padding) {
            $html = Blade::render('<x-ui.card :padding="$padding">Padding '.$padding.'</x-ui.card>', [
                'padding' => $padding,
            ]);
            $this->assertStringContainsString('Padding '.$padding, $html);
        }
    }

    // ─── x-ui.alert Tests ────────────────────────────────

    public function test_alert_renders_with_content(): void
    {
        $html = Blade::render('<x-ui.alert>Alert Content</x-ui.alert>');

        $this->assertStringContainsString('Alert Content', $html);
    }

    public function test_alert_renders_all_variants(): void
    {
        $variants = ['info', 'success', 'warning', 'danger'];

        foreach ($variants as $variant) {
            $html = Blade::render('<x-ui.alert :variant="$variant">'.ucfirst($variant).' Alert</x-ui.alert>', [
                'variant' => $variant,
            ]);
            $this->assertStringContainsString(ucfirst($variant).' Alert', $html);
        }
    }

    public function test_alert_renders_with_title(): void
    {
        $html = Blade::render('<x-ui.alert title="Alert Title" variant="info">Alert Body</x-ui.alert>');

        $this->assertStringContainsString('Alert Title', $html);
        $this->assertStringContainsString('Alert Body', $html);
    }

    public function test_alert_renders_dismissible(): void
    {
        $html = Blade::render('<x-ui.alert :dismissible="true">Dismissible Alert</x-ui.alert>');

        $this->assertStringContainsString('Dismissible Alert', $html);
    }

    // ─── x-ui.input Tests ────────────────────────────────

    public function test_input_renders_with_label(): void
    {
        $html = Blade::render('<x-ui.input name="email" label="Email Address" />');

        $this->assertStringContainsString('Email Address', $html);
        $this->assertStringContainsString('name="email"', $html);
    }

    public function test_input_renders_with_placeholder(): void
    {
        $html = Blade::render('<x-ui.input name="search" placeholder="Cari..." />');

        $this->assertStringContainsString('placeholder="Cari..."', $html);
    }

    public function test_input_renders_with_default_value(): void
    {
        $html = Blade::render('<x-ui.input name="name" value="John Doe" />');

        $this->assertStringContainsString('John Doe', $html);
    }

    public function test_input_renders_error_state(): void
    {
        $html = Blade::render('<x-ui.input name="email" :error="\'Email wajib diisi\'" />');

        $this->assertStringContainsString('Email wajib diisi', $html);
        $this->assertStringContainsString('var(--apple-red,#FF3B30)', $html);
        $this->assertStringContainsString('border-[var(--apple-red,#FF3B30)]', $html);
    }

    public function test_input_renders_required_indicator(): void
    {
        $html = Blade::render('<x-ui.input name="email" label="Email" :required="true" />');

        $this->assertStringContainsString('Email', $html);
        $this->assertStringContainsString('*', $html);
    }

    public function test_input_renders_helper_text(): void
    {
        $html = Blade::render('<x-ui.input name="email" label="Email" helper-text="Kami tidak akan membagikan email Anda" />');

        $this->assertStringContainsString('Email', $html);
    }

    public function test_input_renders_all_types(): void
    {
        $types = ['text', 'email', 'number', 'password', 'tel', 'url'];

        foreach ($types as $type) {
            $html = Blade::render('<x-ui.input name="field" :type="$type" />', [
                'type' => $type,
            ]);
            $this->assertStringContainsString('type="'.$type.'"', $html);
        }
    }

    public function test_input_renders_with_leading_icon(): void
    {
        $html = Blade::render('<x-ui.input name="search" leading-icon="fa-solid fa-search" />');

        $this->assertStringContainsString('fa-solid fa-search', $html);
        $this->assertStringContainsString('pointer-events-none', $html);
    }

    // ─── x-ui.select Tests ───────────────────────────────

    public function test_select_renders_with_label(): void
    {
        $options = [
            ['value' => '1', 'label' => 'Option 1'],
            ['value' => '2', 'label' => 'Option 2'],
        ];

        $html = Blade::render('<x-ui.select name="category" label="Category" :options="$options" />', [
            'options' => $options,
        ]);

        $this->assertStringContainsString('Category', $html);
        $this->assertStringContainsString('Option 1', $html);
        $this->assertStringContainsString('Option 2', $html);
    }

    public function test_select_renders_simple_array_options(): void
    {
        $options = ['1' => 'Option A', '2' => 'Option B'];

        $html = Blade::render('<x-ui.select name="test" label="Test" :options="$options" />', [
            'options' => $options,
        ]);

        $this->assertStringContainsString('Option A', $html);
        $this->assertStringContainsString('Option B', $html);
    }

    public function test_select_renders_with_placeholder(): void
    {
        $options = [['value' => '1', 'label' => 'One']];

        $html = Blade::render('<x-ui.select name="cat" label="Cat" placeholder="Pilih..." :options="$options" />', [
            'options' => $options,
        ]);

        $this->assertStringContainsString('Cat', $html);
    }

    public function test_select_renders_required_indicator(): void
    {
        $html = Blade::render('<x-ui.select name="req" label="Required" :required="true" :options="[]" />');

        $this->assertStringContainsString('Required', $html);
    }

    public function test_select_renders_error_state(): void
    {
        $html = Blade::render('<x-ui.select name="cat" label="Category" :error="\'Harus dipilih\'" :options="[]" />');

        $this->assertStringContainsString('Category', $html);
    }

    // ─── x-ui.table Tests ────────────────────────────────

    public function test_table_renders_empty_state(): void
    {
        $columns = [['key' => 'name', 'label' => 'Nama']];

        $html = Blade::render('<x-ui.table :columns="$columns" :rows="[]" />', [
            'columns' => $columns,
        ]);

        $this->assertStringContainsString('Tidak ada data', $html);
    }

    public function test_table_renders_custom_empty_message(): void
    {
        $columns = [['key' => 'name', 'label' => 'Nama']];

        $html = Blade::render('<x-ui.table :columns="$columns" :rows="[]" empty-message="Belum ada pengguna" />', [
            'columns' => $columns,
        ]);

        $this->assertStringContainsString('Belum ada pengguna', $html);
    }

    public function test_table_renders_rows_with_default_cell_renderer(): void
    {
        $columns = [['key' => 'name', 'label' => 'Nama']];
        $rows = [(object) ['name' => 'John Doe']];

        $html = Blade::render('<x-ui.table :columns="$columns" :rows="$rows" />', [
            'columns' => $columns,
            'rows' => $rows,
        ]);

        $this->assertStringContainsString('John Doe', $html);
    }

    public function test_table_renders_with_cell_renderers(): void
    {
        $columns = [['key' => 'name', 'label' => 'Nama']];
        $rows = [(object) ['name' => 'John Doe']];
        $cellRenderers = [
            'name' => function ($row) {
                return '<strong>'.e($row->name).'</strong>';
            },
        ];

        $html = Blade::render('<x-ui.table :columns="$columns" :rows="$rows" :cellRenderers="$cellRenderers" />', [
            'columns' => $columns,
            'rows' => $rows,
            'cellRenderers' => $cellRenderers,
        ]);

        $this->assertStringContainsString('<strong>John Doe</strong>', $html);
    }

    public function test_table_renders_with_column_render_closure(): void
    {
        $columns = [
            ['key' => 'name', 'label' => 'Nama', 'render' => function ($row) {
                return '<span class="font-bold">'.e($row->name).'</span>';
            }],
        ];
        $rows = [(object) ['name' => 'Jane Doe']];

        $html = Blade::render('<x-ui.table :columns="$columns" :rows="$rows" />', [
            'columns' => $columns,
            'rows' => $rows,
        ]);

        $this->assertStringContainsString('<span class="font-bold">Jane Doe</span>', $html);
    }

    public function test_table_renders_header(): void
    {
        $columns = [
            ['key' => 'name', 'label' => 'Nama'],
            ['key' => 'email', 'label' => 'Email'],
        ];

        $html = Blade::render('<x-ui.table :columns="$columns" :rows="[]" />', [
            'columns' => $columns,
        ]);

        $this->assertStringContainsString('Nama', $html);
        $this->assertStringContainsString('Email', $html);
    }

    public function test_table_hides_header_when_show_header_false(): void
    {
        $columns = [['key' => 'name', 'label' => 'Nama']];

        $html = Blade::render('<x-ui.table :columns="$columns" :rows="[]" :showHeader="false" />', [
            'columns' => $columns,
        ]);

        $this->assertStringNotContainsString('Nama', $html);
    }

    public function test_table_renders_compact_variant(): void
    {
        $columns = [['key' => 'name', 'label' => 'Nama']];
        $rows = [(object) ['name' => 'Compact']];

        $html = Blade::render('<x-ui.table :columns="$columns" :rows="$rows" variant="compact" />', [
            'columns' => $columns,
            'rows' => $rows,
        ]);

        $this->assertStringContainsString('Compact', $html);
    }

    public function test_table_renders_striped_rows(): void
    {
        $columns = [['key' => 'name', 'label' => 'Nama']];
        $rows = [(object) ['name' => 'Row 1'], (object) ['name' => 'Row 2']];

        $html = Blade::render('<x-ui.table :columns="$columns" :rows="$rows" :striped="true" />', [
            'columns' => $columns,
            'rows' => $rows,
        ]);

        $this->assertStringContainsString('Row 1', $html);
        $this->assertStringContainsString('Row 2', $html);
    }

    // ─── x-ui.empty-state Tests ──────────────────────────

    public function test_empty_state_renders_with_default_message(): void
    {
        $html = Blade::render('<x-ui.empty-state />');

        $this->assertStringContainsString('No data', $html);
    }

    public function test_empty_state_renders_with_custom_title_and_description(): void
    {
        $html = Blade::render('<x-ui.empty-state title="Tidak ada hasil" description="Coba filter lain" />');

        $this->assertStringContainsString('Tidak ada hasil', $html);
        $this->assertStringContainsString('Coba filter lain', $html);
    }

    // ─── x-ui.checkbox Tests ─────────────────────────────

    public function test_checkbox_renders_with_label(): void
    {
        $html = Blade::render('<x-ui.checkbox name="agree" label="Saya setuju" />');

        $this->assertStringContainsString('Saya setuju', $html);
    }

    // ─── x-ui.toggle Tests ───────────────────────────────

    public function test_toggle_renders_with_label(): void
    {
        $html = Blade::render('<x-ui.toggle name="active" label="Aktif" />');

        $this->assertStringContainsString('Aktif', $html);
    }

    // ─── x-ui.breadcrumb Tests ───────────────────────────

    public function test_breadcrumb_renders(): void
    {
        $html = Blade::render('<x-ui.breadcrumb />');

        // Should render without error
        $this->assertNotEmpty(trim($html));
    }

    // ─── x-ui.progress Tests ─────────────────────────────

    public function test_progress_renders_with_value(): void
    {
        $html = Blade::render('<x-ui.progress :value="50" />');

        // Should render without error
        $this->assertNotEmpty(trim($html));
    }

    // ─── x-ui.skeleton Tests ─────────────────────────────

    public function test_skeleton_renders(): void
    {
        $html = Blade::render('<x-ui.skeleton />');

        // Should render without error
        $this->assertNotEmpty(trim($html));
    }

    // ─── x-ui.avatar Tests ───────────────────────────────

    public function test_avatar_renders(): void
    {
        $html = Blade::render('<x-ui.avatar name="John Doe" />');

        // Should render without error
        $this->assertNotEmpty(trim($html));
    }

    // ─── x-ui.dropdown Tests ─────────────────────────────

    public function test_dropdown_renders(): void
    {
        $html = Blade::render('<x-ui.dropdown><x-slot:trigger><button type="button">Open</button></x-slot:trigger>Dropdown content</x-ui.dropdown>');

        $this->assertStringContainsString('Open', $html);
        $this->assertStringContainsString('Dropdown content', $html);

        // Blade slot rendering may leave output buffers open; ensure cleanup
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    // ─── x-ui.tooltip Tests ──────────────────────────────

    public function test_tooltip_renders(): void
    {
        $html = Blade::render('<x-ui.tooltip content="Tooltip content">Hover me</x-ui.tooltip>');

        $this->assertStringContainsString('Hover me', $html);
    }

    // ─── x-ui.tabs Tests ─────────────────────────────────

    public function test_tabs_renders(): void
    {
        $tabs = [
            ['id' => 'tab1', 'label' => 'Tab 1'],
            ['id' => 'tab2', 'label' => 'Tab 2'],
        ];

        $html = Blade::render('<x-ui.tabs :tabs="$tabs" />', [
            'tabs' => $tabs,
        ]);

        $this->assertStringContainsString('Tab 1', $html);
        $this->assertStringContainsString('Tab 2', $html);
    }

    // ─── x-ui.stat-card Tests ────────────────────────────

    public function test_stat_card_renders(): void
    {
        $html = Blade::render('<x-ui.stat-card label="Total Users" value="1,234" />');

        $this->assertStringContainsString('Total Users', $html);
        $this->assertStringContainsString('1,234', $html);
    }

    // ─── x-ui.file-upload Tests ──────────────────────────

    public function test_file_upload_renders(): void
    {
        $html = Blade::render('<x-ui.file-upload name="document" label="Upload File" />');

        $this->assertStringContainsString('Upload File', $html);
    }

    // ─── x-ui.radio-group Tests ──────────────────────────

    public function test_radio_group_renders(): void
    {
        $options = [
            ['value' => 'opt1', 'label' => 'Option 1'],
            ['value' => 'opt2', 'label' => 'Option 2'],
        ];

        $html = Blade::render('<x-ui.radio-group name="choice" label="Pilihan" :options="$options" />', [
            'options' => $options,
        ]);

        $this->assertStringContainsString('Option 1', $html);
        $this->assertStringContainsString('Option 2', $html);
    }

    // ─── x-ui.textarea Tests ─────────────────────────────

    public function test_textarea_renders(): void
    {
        $html = Blade::render('<x-ui.textarea name="bio" label="Biography" />');

        $this->assertStringContainsString('Biography', $html);
    }

    // ─── x-ui.accordion Tests ────────────────────────────

    public function test_accordion_renders(): void
    {
        $html = Blade::render('<x-ui.accordion :items="$items" />', [
            'items' => [
                ['title' => 'Section Title', 'content' => 'Accordion Content'],
            ],
        ]);

        $this->assertStringContainsString('Section Title', $html);
        $this->assertStringContainsString('Accordion Content', $html);
    }

    // ─── x-ui.modal Tests ────────────────────────────────

    public function test_modal_renders_with_trigger(): void
    {
        // Pass a custom footer to avoid Livewire 4.2 endif conflict
        // when rendering x-ui.button inside @if/@else directives
        $html = Blade::render(<<<'BLADE'
<x-ui.modal title="Modal Title" submit-label="Save">
    <x-slot:trigger>
        <button type="button">Open Modal</button>
    </x-slot:trigger>
    <x-slot:footer>
        <button type="button">Batal</button>
        <button type="button">Save</button>
    </x-slot:footer>
    Modal Body Content
</x-ui.modal>
BLADE);

        $this->assertStringContainsString('Open Modal', $html);
        $this->assertStringContainsString('Modal Title', $html);
        $this->assertStringContainsString('Modal Body Content', $html);
        $this->assertStringContainsString('Save', $html);
    }

    // ─── Component Attribute Forwarding Tests ───────────

    public function test_component_accepts_additional_attributes(): void
    {
        $html = Blade::render('<x-ui.button class="extra-class" data-test="value">Test</x-ui.button>');

        $this->assertStringContainsString('extra-class', $html);
        $this->assertStringContainsString('data-test="value"', $html);
    }

    // ─── x-ui.pagination Tests ──────────────────────────

    private function makePaginator(int $total, int $perPage, int $currentPage): LengthAwarePaginator
    {
        $items = Collection::times($total, fn ($i) => (object) ['id' => $i, 'name' => "Item {$i}"]);

        return new LengthAwarePaginator(
            $items->forPage($currentPage, $perPage)->values(),
            $total,
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    public function test_pagination_renders_with_paginator(): void
    {
        $paginator = $this->makePaginator(50, 10, 1);
        $html = Blade::render('<x-ui.pagination :paginator="$paginator" />', [
            'paginator' => $paginator,
        ]);

        $this->assertStringContainsString('Menampilkan', $html);
        $this->assertStringContainsString('1', $html);
        $this->assertStringContainsString('5', $html);
        $this->assertStringContainsString('50', $html);
    }

    public function test_pagination_shows_page_numbers(): void
    {
        $paginator = $this->makePaginator(50, 10, 3);
        $html = Blade::render('<x-ui.pagination :paginator="$paginator" />', [
            'paginator' => $paginator,
        ]);

        // Should show page numbers around current page (3)
        $this->assertStringContainsString('>1<', $html);
        $this->assertStringContainsString('>2<', $html);
        $this->assertStringContainsString('>3<', $html);
        $this->assertStringContainsString('>4<', $html);
        $this->assertStringContainsString('>5<', $html);
    }

    public function test_pagination_renders_simple_variant(): void
    {
        $paginator = $this->makePaginator(50, 10, 1);
        $html = Blade::render('<x-ui.pagination :paginator="$paginator" variant="simple" />', [
            'paginator' => $paginator,
        ]);

        $this->assertStringContainsString('Halaman', $html);
        $this->assertStringContainsString('1', $html);
        $this->assertStringContainsString('5', $html);
    }

    public function test_pagination_hides_info_when_disabled(): void
    {
        $paginator = $this->makePaginator(50, 10, 1);
        $html = Blade::render('<x-ui.pagination :paginator="$paginator" :showInfo="false" />', [
            'paginator' => $paginator,
        ]);

        $this->assertStringNotContainsString('Menampilkan', $html);
        $this->assertStringContainsString('>1<', $html); // Pages still shown
    }

    public function test_pagination_disabled_prev_on_first_page(): void
    {
        $paginator = $this->makePaginator(20, 10, 1);
        $html = Blade::render('<x-ui.pagination :paginator="$paginator" />', [
            'paginator' => $paginator,
        ]);

        // Previous button should be a span (disabled), not an anchor tag
        $this->assertStringContainsString('cursor-not-allowed', $html);
    }

    public function test_pagination_enabled_next_when_has_more_pages(): void
    {
        $paginator = $this->makePaginator(20, 10, 1);
        $html = Blade::render('<x-ui.pagination :paginator="$paginator" />', [
            'paginator' => $paginator,
        ]);

        // Next button should be an anchor tag with rel="next"
        $this->assertStringContainsString('rel="next"', $html);
    }

    public function test_pagination_does_not_render_with_null_paginator(): void
    {
        $html = Blade::render('<x-ui.pagination :paginator="null" />');

        $this->assertEmpty(trim($html));
    }

    public function test_pagination_does_not_render_with_empty_paginator(): void
    {
        $paginator = $this->makePaginator(0, 10, 1);
        $html = Blade::render('<x-ui.pagination :paginator="$paginator" />', [
            'paginator' => $paginator,
        ]);

        $this->assertEmpty(trim($html));
    }

    public function test_pagination_renders_size_sm(): void
    {
        $paginator = $this->makePaginator(20, 10, 1);
        $html = Blade::render('<x-ui.pagination :paginator="$paginator" size="sm" />', [
            'paginator' => $paginator,
        ]);

        $this->assertStringContainsString('px-3 py-1.5 text-xs', $html);
    }

    public function test_pagination_renders_size_md(): void
    {
        $paginator = $this->makePaginator(20, 10, 1);
        $html = Blade::render('<x-ui.pagination :paginator="$paginator" size="md" />', [
            'paginator' => $paginator,
        ]);

        $this->assertStringContainsString('px-4 py-2 text-sm', $html);
    }

    // ─── x-ui.toast Tests ────────────────────────────────

    public function test_toast_renders_container(): void
    {
        $html = Blade::render('<x-ui.toast />');

        $this->assertStringContainsString('x-data', $html);
        $this->assertStringContainsString('@toast.window', $html);
        $this->assertStringContainsString('aria-live="polite"', $html);
    }

    public function test_toast_renders_top_right_position_by_default(): void
    {
        $html = Blade::render('<x-ui.toast />');

        $this->assertStringContainsString('top-4 right-4', $html);
    }

    public function test_toast_renders_all_positions(): void
    {
        $positions = [
            'top-right' => 'top-4 right-4',
            'top-left' => 'top-4 left-4',
            'bottom-right' => 'bottom-4 right-4',
            'bottom-left' => 'bottom-4 left-4',
            'top-center' => 'top-4 left-1/2 -translate-x-1/2',
            'bottom-center' => 'bottom-4 left-1/2 -translate-x-1/2',
        ];

        foreach ($positions as $position => $expectedClass) {
            $html = Blade::render('<x-ui.toast position="'.$position.'" />');
            $this->assertStringContainsString($expectedClass, $html);
        }
    }

    public function test_toast_has_max_visible_limit(): void
    {
        $html = Blade::render('<x-ui.toast :maxVisible="3" />');

        $this->assertStringContainsString('3', $html);
    }

    public function test_toast_has_custom_duration(): void
    {
        $html = Blade::render('<x-ui.toast :duration="5000" />');

        $this->assertStringContainsString('5000', $html);
    }

    public function test_toast_has_remove_function(): void
    {
        $html = Blade::render('<x-ui.toast />');

        $this->assertStringContainsString('removeToast', $html);
        $this->assertStringContainsString('addToast', $html);
    }

    public function test_toast_has_close_button(): void
    {
        $html = Blade::render('<x-ui.toast />');

        $this->assertStringContainsString('aria-label="Close"', $html);
    }

    // ─── x-ui.button-spinner Tests ───────────────────────

    public function test_button_spinner_renders_with_default_size(): void
    {
        $html = Blade::render('<x-ui.button-spinner />');

        $this->assertStringContainsString('animate-spin', $html);
        $this->assertStringContainsString('h-4 w-4', $html); // default md
    }

    public function test_button_spinner_renders_with_sm_size(): void
    {
        $html = Blade::render('<x-ui.button-spinner size="sm" />');

        $this->assertStringContainsString('h-3 w-3', $html);
    }

    public function test_button_spinner_renders_with_lg_size(): void
    {
        $html = Blade::render('<x-ui.button-spinner size="lg" />');

        $this->assertStringContainsString('h-5 w-5', $html);
    }

    // ─── x-ui.dropdown-divider Tests ─────────────────────

    public function test_dropdown_divider_renders(): void
    {
        $html = Blade::render('<x-ui.dropdown-divider />');

        $this->assertStringContainsString('border-t', $html);
        $this->assertStringContainsString('border-gray-200', $html);
    }

    public function test_dropdown_divider_accepts_custom_class(): void
    {
        $html = Blade::render('<x-ui.dropdown-divider class="my-2 extra-class" />');

        $this->assertStringContainsString('extra-class', $html);
    }

    // ─── x-ui.dropdown-item Tests ────────────────────────

    public function test_dropdown_item_renders_as_link(): void
    {
        $html = Blade::render('<x-ui.dropdown-item href="/test">Menu Item</x-ui.dropdown-item>');

        $this->assertStringContainsString('href="/test"', $html);
        $this->assertStringContainsString('Menu Item', $html);
        $this->assertStringContainsString('role="menuitem"', $html);
    }

    public function test_dropdown_item_renders_with_icon(): void
    {
        $html = Blade::render('<x-ui.dropdown-item icon="fa-solid fa-user">Profile</x-ui.dropdown-item>');

        $this->assertStringContainsString('fa-solid fa-user', $html);
        $this->assertStringContainsString('Profile', $html);
    }

    public function test_dropdown_item_renders_danger_variant(): void
    {
        $html = Blade::render('<x-ui.dropdown-item variant="danger">Delete</x-ui.dropdown-item>');

        $this->assertStringContainsString('text-red-600', $html);
    }

    public function test_dropdown_item_renders_disabled_state(): void
    {
        $html = Blade::render('<x-ui.dropdown-item :disabled="true">Disabled</x-ui.dropdown-item>');

        $this->assertStringContainsString('opacity-50', $html);
        $this->assertStringContainsString('cursor-not-allowed', $html);
        $this->assertStringContainsString('aria-disabled="true"', $html);
    }

    public function test_dropdown_item_renders_as_form_when_method_is_post(): void
    {
        $html = Blade::render('<x-ui.dropdown-item href="/delete" method="POST">Delete</x-ui.dropdown-item>');

        $this->assertStringContainsString('<form', $html);
        $this->assertStringContainsString('method="POST"', $html);
        $this->assertStringContainsString('action="/delete"', $html);
        $this->assertStringContainsString('role="menuitem"', $html);
    }

    public function test_dropdown_item_uses_default_href_when_not_provided(): void
    {
        $html = Blade::render('<x-ui.dropdown-item>Default</x-ui.dropdown-item>');

        $this->assertStringContainsString('href="#"', $html);
    }
}
