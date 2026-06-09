import './bootstrap';

// Import self-hosted libraries
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import AOS from 'aos';
import Chart from 'chart.js/auto';

// Register Alpine plugins
Alpine.plugin(collapse);

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Expose Chart.js globally (for inline <script> usage in views)
window.Chart = Chart;

// Initialize AOS (Animate On Scroll)
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
AOS.init({
    duration: 800,
    easing: 'ease-out-cubic',
    once: true,
    offset: 100,
    disable: prefersReducedMotion,
});

/**
 * Global API fetch helper.
 * Wraps native fetch with:
 *   - Automatic CSRF token header
 *   - Standardized error handling (matches ApiResponse trait envelope)
 *   - Network error detection
 *
 * Usage:
 *   const data = await apiFetch('/api/kbli/search?q=restoran');
 *   const data = await apiFetch('/api/consultation/submit', { method: 'POST', body: JSON.stringify(payload) });
 */
window.apiFetch = async function (url, options = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const defaults = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
        },
        credentials: 'include',
    };

    const config = {
        ...defaults,
        ...options,
        headers: { ...defaults.headers, ...(options.headers ?? {}) },
        // Allow caller override for credentials (e.g. 'same-origin')
        ...(options.credentials ? {} : { credentials: 'include' }),
    };

    try {
        const response = await fetch(url, config);
        const json = await response.json().catch(() => ({
            success: false,
            message: 'Respons server tidak valid (bukan JSON).',
        }));

        // Server returned an error status but with API envelope
        if (!response.ok && json.success === undefined) {
            return {
                success: false,
                message: `Error ${response.status}: ${response.statusText}`,
            };
        }

        return json;
    } catch (networkError) {
        // Network failure (offline, DNS, CORS, etc.)
        console.error('[apiFetch] Network error:', networkError);
        return {
            success: false,
            message: 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
        };
    }
};

/**
 * Global axios defaults (used by some blade components).
 * CSRF token is already set in bootstrap.js via axios interceptor.
 */
