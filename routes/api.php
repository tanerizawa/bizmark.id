<?php

use App\Http\Controllers\Api\AiAgentController;
use App\Http\Controllers\Api\CivicStackController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\LandingStatsController;
use App\Http\Controllers\Api\OpsController;
use App\Http\Controllers\Api\PaymentCallbackController;
use App\Modules\Perizinan\Controllers\Api\KbliController;
use App\Modules\Perizinan\Controllers\Api\KbliRecommendationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Landing live stats (aggregate ranges, 5-min cache, public)
Route::get('landing/live-stats', LandingStatsController::class)
    ->middleware('throttle:120,1')
    ->name('api.landing.live-stats');

// KBLI Search & Autocomplete (Public with rate limiting)
Route::prefix('kbli')->middleware('throttle:60,1')->group(function () {
    Route::get('search', [KbliController::class, 'search']);
    Route::get('popular', [KbliController::class, 'popular']);
    Route::get('{code}', [KbliController::class, 'show']);
});

// Free Consultation (Public with stricter rate limiting)
Route::prefix('consultation')->middleware('throttle:10,1')->group(function () {
    Route::post('submit', [ConsultationController::class, 'submit']);
    Route::post('quick-estimate', [ConsultationController::class, 'quickEstimate'])->middleware('throttle:30,1');
});

// KBLI AI Recommendations
Route::prefix('kbli-recommendations')->group(function () {
    Route::post('/', [KbliRecommendationController::class, 'getRecommendations']);
    Route::post('refresh', [KbliRecommendationController::class, 'refresh'])->middleware(['internal.api', 'throttle:30,1']);
    Route::get('stats', [KbliRecommendationController::class, 'stats'])->middleware(['internal.api', 'throttle:30,1']);
});

// Payment callback endpoint (Midtrans)
Route::post('payment/callback', [PaymentCallbackController::class, 'callback'])
    ->middleware('throttle:120,1')
    ->name('api.payment.callback');

// Shapefile/Polygon Tool (Public with rate limiting + session for client auth)
Route::prefix('shapefile')->middleware(['throttle:10,1', \Illuminate\Session\Middleware\StartSession::class])->group(function () {
    Route::post('generate', [App\Modules\Perizinan\Controllers\Api\ShapefileApiController::class, 'generate']);
    Route::post('calculate', [App\Modules\Perizinan\Controllers\Api\ShapefileApiController::class, 'calculate'])->middleware('throttle:30,1');
    Route::post('check-email', [App\Modules\Perizinan\Controllers\Api\ShapefileApiController::class, 'checkEmail'])->middleware('throttle:15,1');
    Route::get('{projectId}/pdf', [App\Modules\Perizinan\Controllers\Api\ShapefileApiController::class, 'generatePdf'])->middleware('throttle:5,1');
    Route::post('{projectId}/rtrw', [App\Modules\Perizinan\Controllers\Api\ShapefileApiController::class, 'storeRtrwAnalysis'])->middleware('throttle:10,1');
});

// RTRW Spatial Zoning (GISTARU proxy)
Route::prefix('rtrw')->middleware('throttle:30,1')->group(function () {
    Route::get('provinces', [App\Modules\Perizinan\Controllers\Api\RtrwProxyController::class, 'provinces']);
    Route::get('zona', [App\Modules\Perizinan\Controllers\Api\RtrwProxyController::class, 'zona'])->middleware('throttle:20,1');
    Route::post('analyze', [App\Modules\Perizinan\Controllers\Api\RtrwProxyController::class, 'analyze'])->middleware('throttle:20,1');
    Route::get('layers/{provinceCode}', [App\Modules\Perizinan\Controllers\Api\RtrwProxyController::class, 'layers']);
    Route::get('legend/{provinceCode}', [App\Modules\Perizinan\Controllers\Api\RtrwProxyController::class, 'legend']);
    Route::get('map-export/{provinceCode}', [App\Modules\Perizinan\Controllers\Api\RtrwProxyController::class, 'mapExport'])->middleware('throttle:60,1');
});

Route::prefix('internal/ops')->middleware(['internal.api', 'throttle:30,1'])->group(function () {
    Route::get('permissions', [OpsController::class, 'permissions']);
});

// P3 — WhatsApp Bot Webhook (no auth — signed by Meta HMAC)
Route::prefix('whatsapp')->group(function () {
    Route::get('webhook', [App\Http\Controllers\Api\WhatsAppWebhookController::class, 'verify']);
    Route::post('webhook', [App\Http\Controllers\Api\WhatsAppWebhookController::class, 'handle'])->middleware('throttle:60,1');
});

// P5 — KBLI Semantic Search
Route::post('kbli/semantic-search', [App\Http\Controllers\Api\KbliSemanticSearchController::class, 'search'])
    ->middleware('throttle:20,1')
    ->name('api.kbli.semantic-search');

// ─── P8: B2B API Platform — v2 endpoints (ApiKeyAuth middleware) ────────────
Route::prefix('v2')->middleware(\App\Http\Middleware\ApiKeyAuth::class)->name('api.v2.')->group(function () {
    Route::post('kbli/search', [App\Http\Controllers\Api\KbliSemanticSearchController::class, 'search'])->name('kbli.search');
    Route::get('kbli/{code}', [App\Http\Controllers\Api\KbliSemanticSearchController::class, 'show'])->name('kbli.show');
});

// ─── Civic Stack — Indonesia government data microservice ────────────────────
// Proxies to bizmark_civic_stack Docker container (FastAPI, internal network).
// Endpoints used by the perizinan context form (v2-context.blade.php).
Route::prefix('civic')->middleware('throttle:30,1')->group(function () {
    Route::get('simbg-hints', [CivicStackController::class, 'simbgHints']);   // Step 2: building permit hints
    Route::get('bpjph-check', [CivicStackController::class, 'bpjphCheck']);   // Step 3: halal cert (F&B only)
    Route::get('nib-lookup', [CivicStackController::class, 'nibLookup']);    // Pre-step 1: auto-fill from NIB
    Route::get('jdih-search', [CivicStackController::class, 'jdihSearch']);   // Step 4: relevant regulations
});

// ─── AI SDK Agents — Laravel AI SDK endpoints ─────────────────────────────────
Route::prefix('ai/agent')->middleware(['auth:sanctum', 'throttle:20,1'])->name('api.ai.agent.')->group(function () {
    Route::post('perizinan', [AiAgentController::class, 'perizinan'])->name('perizinan');
    Route::post('paraphrase', [AiAgentController::class, 'paraphrase'])->name('paraphrase');
    Route::post('optimize-seo', [AiAgentController::class, 'optimizeSeo'])->name('optimize-seo');
});
