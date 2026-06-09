<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqAggregationController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PillarPageController;
use App\Http\Controllers\ProgrammaticSeoController;
use App\Http\Controllers\PublicArticleController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\ServiceComparisonController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Modules\HRM\Controllers\Admin\RecruitmentController;
use Illuminate\Support\Facades\Route;

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// RSS & Atom Feeds
Route::get('/feed/rss', [RssFeedController::class, 'rss'])->name('feed.rss');
Route::get('/feed/atom', [RssFeedController::class, 'atom'])->name('feed.atom');

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
})->name('health');

// IndexNow key verification
Route::get('/{key}.txt', function (string $key) {
    $indexNowKey = config('services.indexnow.key', '');
    if ($key === $indexNowKey && ! empty($indexNowKey)) {
        return response($indexNowKey, 200)->header('Content-Type', 'text/plain');
    }
    abort(404);
})->where('key', '[a-zA-Z0-9_-]+');

// Language Switcher
Route::get('/locale/{locale}', [LocaleController::class, 'setLocale'])
    ->name('locale.set')
    ->where('locale', 'id|en');

// Indonesian Landing Page (Root - Default)
Route::middleware('locale:id')->group(function () {
    Route::get('/layanan', [ServiceController::class, 'index'])->name('services.index.id');
    Route::get('/layanan/kota/{citySlug}', [ProgrammaticSeoController::class, 'cityIndex'])->name('programmatic.city.id');
    Route::get('/layanan/perbandingan', [ServiceComparisonController::class, 'index'])->name('comparison.index');
    Route::get('/layanan/perbandingan/{comparisonSlug}', [ServiceComparisonController::class, 'show'])->name('comparison.show');
    Route::get('/layanan/kategori/{categorySlug}', [ServiceController::class, 'showCategory'])->name('services.category.id');
    Route::get('/layanan/{slug}', [ServiceController::class, 'show'])->name('services.show.id');
    Route::get('/layanan/{serviceSlug}/sub/{subSlug}', [ServiceController::class, 'showSub'])->name('services.sub.id');
    Route::get('/layanan/{serviceSlug}/{citySlug}', [ProgrammaticSeoController::class, 'serviceLocation'])->name('programmatic.service-location.id');
    Route::get('/faq', [FaqAggregationController::class, 'index'])->name('faq.index');
    Route::get('/faq/{topicSlug}', [FaqAggregationController::class, 'show'])->name('faq.show');
    Route::get('/panduan', [PillarPageController::class, 'index'])->name('pillar.index');
    Route::get('/panduan/{pillarSlug}', [PillarPageController::class, 'show'])->name('pillar.show');
    Route::get('/blog', [PublicArticleController::class, 'index'])->name('blog.index.id');
    Route::get('/blog/kategori/{category}', [PublicArticleController::class, 'category'])->name('blog.category.id');
    Route::get('/blog/tag/{tag}', [PublicArticleController::class, 'tag'])->name('blog.tag.id');
    Route::get('/blog/{slug}', [PublicArticleController::class, 'show'])->name('blog.article.id');

    // Legal Pages (ID)
    Route::get('/kebijakan-privasi', function () {
        return view('legal.privacy');
    })->name('privacy.policy.id');

    Route::get('/syarat-ketentuan', function () {
        return view('legal.terms');
    })->name('terms.conditions.id');

    // Static Pages (ID)
    Route::get('/proses', function () {
        return view('landing.pages.process', ['locale' => 'id']);
    })->name('process.id');

    Route::get('/tentang', function () {
        return view('landing.pages.about', ['locale' => 'id']);
    })->name('about.id');

    Route::get('/harga', function () {
        return view('landing.pages.pricing', ['locale' => 'id']);
    })->name('pricing.id');

    Route::get('/status', function () {
        return view('landing.pages.status', ['locale' => 'id']);
    })->name('status.id');
});

// English/PMA Landing Page (Explicit) - Responsive (No Mobile Redirect)
Route::prefix('en')->middleware('locale:en')->group(function () {
    Route::get('/', function (\Illuminate\Http\Request $request) {
        // Fully responsive landing page — serves all devices
        // Manual mobile override handled by DeviceDetection middleware (?mobile=1)
        return app(PublicArticleController::class)->landing($request);
    })->name('landing.en');
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index.en');
    Route::get('/services/category/{categorySlug}', [ServiceController::class, 'showCategory'])->name('services.category.en');
    Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show.en');
    Route::get('/services/{serviceSlug}/sub/{subSlug}', [ServiceController::class, 'showSub'])->name('services.sub.en');
    Route::get('/blog', [PublicArticleController::class, 'index'])->name('blog.index.en');
    Route::get('/blog/category/{category}', [PublicArticleController::class, 'category'])->name('blog.category.en');
    Route::get('/blog/tag/{tag}', [PublicArticleController::class, 'tag'])->name('blog.tag.en');
    Route::get('/blog/{slug}', [PublicArticleController::class, 'show'])->name('blog.article.en');

    // PMA Inquiry Form (English)
    Route::get('/inquiry', [App\Http\Controllers\PMAInquiryController::class, 'create'])->name('pma.inquiry.create');
    Route::post('/inquiry', [App\Http\Controllers\PMAInquiryController::class, 'store'])
        ->middleware('throttle:10,1') // 10 submissions per minute per IP
        ->name('pma.inquiry.store');
    Route::get('/inquiry/result/{inquiryNumber}', [App\Http\Controllers\PMAInquiryController::class, 'result'])->name('pma.inquiry.result');

    // Legal Pages (EN)
    Route::get('/privacy-policy', function () {
        return view('legal.en.privacy');
    })->name('privacy.policy.en');

    Route::get('/terms-conditions', function () {
        return view('legal.en.terms');
    })->name('terms.conditions.en');

    // Static Pages (EN)
    Route::get('/process', function () {
        return view('landing.pages.process', ['locale' => 'en']);
    })->name('process.en');

    Route::get('/about', function () {
        return view('landing.pages.about', ['locale' => 'en']);
    })->name('about.en');

    Route::get('/pricing', function () {
        return view('landing.pages.pricing', ['locale' => 'en']);
    })->name('pricing.en');

    Route::get('/status', function () {
        return view('landing.pages.status', ['locale' => 'en']);
    })->name('status.en');
});

// Redirect old /id URLs to root for backward compatibility
Route::redirect('/id', '/', 301);
Route::redirect('/id/layanan', '/layanan', 301);
Route::redirect('/id/blog', '/blog', 301);

// Contact Page
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'submit'])
    ->middleware('throttle:5,1') // 5 submissions per minute per IP
    ->name('contact.submit');

// Consultation Request / Cost Estimate (Public)
Route::get('/estimasi-biaya', [App\Http\Controllers\ConsultationPageController::class, 'index'])->name('consultation.index');
Route::get('/estimasi-biaya/hasil/{requestId}', [App\Http\Controllers\ConsultationPageController::class, 'result'])->name('consultation.result');
Route::get('/estimasi-biaya/pdf/{requestId}', [App\Http\Controllers\ConsultationPageController::class, 'downloadPdf'])->name('consultation.pdf');

// Permohonan Penghitungan Biaya Jasa (Service Cost Request)
Route::prefix('permohonan')->group(function () {
    Route::get('/', [App\Http\Controllers\ServiceCostRequestController::class, 'index'])->name('permohonan.index');
    Route::post('/', [App\Http\Controllers\ServiceCostRequestController::class, 'store'])
        ->middleware('throttle:10,1') // 10 submissions per minute per IP (may include file uploads)
        ->name('permohonan.store');
    Route::post('/api/generate-letter-draft', [App\Http\Controllers\ServiceCostRequestController::class, 'generateLetterDraft'])
        ->middleware('throttle:5,1') // 5 AI letter generations per minute per IP (prevents AI API abuse)
        ->name('permohonan.generate-letter-draft');
    Route::get('/hasil/{requestNumber}', [App\Http\Controllers\ServiceCostRequestController::class, 'result'])->name('permohonan.result');
    Route::get('/api/status/{requestNumber}', [App\Http\Controllers\ServiceCostRequestController::class, 'checkStatus'])->name('permohonan.status');
});

// New Redesign Routes (staging at /new)
Route::prefix('new')->middleware('locale:id')->name('new.')->group(function () {
    Route::get('/', [App\Http\Controllers\NewLandingController::class, 'home'])->name('home');
    Route::get('/layanan', [App\Http\Controllers\NewLandingController::class, 'services'])->name('services');
    Route::get('/proses', [App\Http\Controllers\NewLandingController::class, 'process'])->name('process');
    Route::get('/harga', [App\Http\Controllers\NewLandingController::class, 'pricing'])->name('pricing');
    Route::get('/tentang', [App\Http\Controllers\NewLandingController::class, 'about'])->name('about');
    Route::get('/blog', [App\Http\Controllers\NewLandingController::class, 'blog'])->name('blog');
    Route::get('/kontak', [App\Http\Controllers\NewLandingController::class, 'contact'])->name('contact');
});

// Landing Page (Public) - Indonesian Default - Responsive (No Mobile Redirect)
Route::middleware('locale:id')->get('/', function (\Illuminate\Http\Request $request) {
    // Fully responsive landing page — serves all devices
    // Manual mobile override handled by DeviceDetection middleware (?mobile=1)
    return app(PublicArticleController::class)->landing($request);
})->name('landing.id');

// Service Inquiry - Free AI Analysis (Landing Page Lead Generation)
Route::prefix('konsultasi-gratis')->group(function () {
    Route::get('/', [App\Http\Controllers\Landing\ServiceInquiryController::class, 'create'])
        ->name('landing.service-inquiry.create');
    Route::post('/', [App\Http\Controllers\Landing\ServiceInquiryController::class, 'store'])
        ->middleware('throttle:10,1') // 10 submissions per minute per IP (also has app-level per-email limiter)
        ->name('landing.service-inquiry.store');
    Route::get('/hasil/{inquiryNumber}', [App\Http\Controllers\Landing\ServiceInquiryController::class, 'result'])
        ->name('landing.service-inquiry.result');
    Route::get('/api/status/{inquiryNumber}', [App\Http\Controllers\Landing\ServiceInquiryController::class, 'show'])
        ->name('landing.service-inquiry.show');
    Route::post('/api/check-rate-limit', [App\Http\Controllers\Landing\ServiceInquiryController::class, 'checkRateLimit'])
        ->middleware('throttle:30,1') // rate limit check endpoint itself
        ->name('landing.service-inquiry.check-rate-limit');
});

// Permit Calculator Tool (Public)
Route::get('/kalkulator-perizinan', [App\Http\Controllers\CalculatorController::class, 'index'])->name('calculator.index');
Route::post('/kalkulator-perizinan/calculate', [App\Http\Controllers\CalculatorController::class, 'calculate'])
    ->middleware('throttle:20,1') // 20 calculations per minute per IP
    ->name('calculator.calculate');

// Document Checklist AI Generator (Public)
Route::prefix('checklist-dokumen')->name('checklist.')->group(function () {
    Route::get('/', [App\Http\Controllers\ChecklistGeneratorController::class, 'index'])->name('index');
    Route::post('/generate', [App\Http\Controllers\ChecklistGeneratorController::class, 'generate'])->name('generate')->middleware('throttle:10,1');
    Route::get('/hasil/{checklist}', [App\Http\Controllers\ChecklistGeneratorController::class, 'result'])->name('result');
    Route::get('/download/{checklist}', [App\Http\Controllers\ChecklistGeneratorController::class, 'download'])->name('download');
});

// Polygon SHP Maker Tool (Public)
Route::get('/polygon-shp-maker', [App\Modules\Perizinan\Controllers\Public\PolygonToolController::class, 'index'])->name('polygon.shp.index');

// Career/Jobs Pages (Public)
Route::get('/karir', [App\Modules\HRM\Controllers\Public\JobVacancyController::class, 'index'])->name('career.index');
Route::get('/karir/{slug}', [App\Modules\HRM\Controllers\Public\JobVacancyController::class, 'show'])->name('career.show');
Route::get('/karir/{vacancy_id}/apply', [App\Modules\HRM\Controllers\Public\JobApplicationController::class, 'create'])->name('career.apply');
Route::post('/karir/apply', [App\Modules\HRM\Controllers\Public\JobApplicationController::class, 'store'])
    ->middleware('throttle:10,1') // 10 submissions per minute per IP
    ->name('career.apply.store');

// Newsletter Subscription (Public)
Route::post('/subscribe', [App\Http\Controllers\SubscriberController::class, 'subscribe'])
    ->middleware('throttle:5,1') // 5 submissions per minute per IP
    ->name('newsletter.subscribe');
Route::get('/unsubscribe/{email}/{token}', [App\Http\Controllers\SubscriberController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/email/track/{tracking_id}', [App\Http\Controllers\SubscriberController::class, 'trackOpen'])->name('email.track');

// Screen Width Detection API (for responsive mobile detection)
Route::post('/api/set-screen-width', function (\Illuminate\Http\Request $request) {
    $width = $request->input('width');
    if ($width && is_numeric($width)) {
        session(['screen_width' => (int) $width]);
    }

    return response()->json(['success' => true, 'width' => session('screen_width')]);
})->middleware('throttle:60,1')->name('api.screen-width');

// ========================================
// UNIFIED LOGIN SYSTEM
// ========================================
// Auto-detects Admin or Client based on credentials
// Main login portal for all users
Route::get('/login', [App\Http\Controllers\Auth\UnifiedLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\UnifiedLoginController::class, 'login'])
    ->middleware('throttle:5,1') // 5 attempts per minute
    ->name('login.post');
Route::post('/logout', [App\Http\Controllers\Auth\UnifiedLoginController::class, 'logout'])->name('logout');

// Admin-only direct login (hidden path, configurable via ADMIN_SECRET_PATH env)
// Rotate by changing ADMIN_SECRET_PATH in .env + php artisan config:clear
if ($adminSecretPath = trim((string) config('auth.admin_secret_path'), '/')) {
    Route::get('/'.$adminSecretPath, [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
        ->name('admin.login');
    Route::post('/'.$adminSecretPath, [App\Http\Controllers\Auth\LoginController::class, 'login'])
        ->middleware('throttle:3,1'); // Stricter rate limit for admin path
}

// ========================================
// BACKWARD COMPATIBILITY REDIRECTS
// Redirect old URLs without /admin/ to new URLs with /admin/
// ========================================
Route::middleware(['auth'])->group(function () {
    Route::redirect('/dashboard', '/admin/dashboard', 301);
    Route::redirect('/projects', '/admin/projects', 301);
    Route::redirect('/tasks', '/admin/tasks', 301);
    Route::redirect('/documents', '/admin/documents', 301);
    Route::redirect('/institutions', '/admin/institutions', 301);
    Route::redirect('/clients', '/admin/clients', 301);
    Route::redirect('/settings', '/admin/settings', 301);
    Route::redirect('/cash-accounts', '/admin/cash-accounts', 301);
    Route::redirect('/articles', '/admin/articles', 301);
    Route::redirect('/reconciliations', '/admin/reconciliations', 301);
    Route::redirect('/permit-types', '/admin/permit-types', 301);
    Route::redirect('/permit-templates', '/admin/permit-templates', 301);
    Route::redirect('/auto-post', '/admin/auto-post', 301);
});

// Protected Routes (require authentication)
// All admin panel routes are prefixed with /admin/

Route::middleware(['auth'])->prefix('admin')->group(function () {
    require __DIR__.'/web_admin.php';
});

// Client Portal Routes
Route::prefix('client')->name('client.')->group(function () {
    // Guest routes (login, register, password reset)
    Route::middleware('guest:client')->group(function () {
        // Redirect old client login to unified login
        Route::get('/login', fn () => redirect('/login'))->name('legacy.login');

        // Keep legacy POST for backwards compatibility (will be deprecated)
        Route::post('/login', [App\Http\Controllers\Auth\ClientAuthController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login'); // client.login

        Route::get('/register', [App\Http\Controllers\Auth\ClientAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [App\Http\Controllers\Auth\ClientAuthController::class, 'register'])
            ->middleware('throttle:10,1')
            ->name('register.store'); // client.register.store

        Route::get('/forgot-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'sendResetLinkEmail'])
            ->middleware('throttle:5,1') // Max 5 attempts per minute
            ->name('password.email');

        Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ClientAuthController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('/reset-password', [App\Http\Controllers\Auth\ClientAuthController::class, 'resetPassword'])
            ->middleware('throttle:3,1') // Max 3 attempts per minute
            ->name('password.update');
    });

    // Protected routes (requires authentication)
    Route::middleware('auth:client')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [App\Http\Controllers\Auth\ClientAuthController::class, 'logout'])->name('logout');

        // Service Catalog Routes (KBLI-based AI System)
        Route::get('/services', [App\Http\Controllers\Client\ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/{kbliCode}/context', [App\Http\Controllers\Client\ServiceController::class, 'context'])->name('services.context');
        Route::post('/services/{kbliCode}/context', [App\Http\Controllers\Client\ServiceController::class, 'storeContext'])->name('services.storeContext');
        Route::get('/services/{kbliCode}/download-summary', [App\Http\Controllers\Client\ServiceController::class, 'downloadSummary'])->name('services.downloadSummary');
        Route::get('/services/{kbliCode}', [App\Http\Controllers\Client\ServiceController::class, 'show'])->name('services.show');

        // Application Routes
        Route::get('/applications', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/create', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'create'])->name('applications.create');
        Route::post('/applications', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'store'])->name('applications.store');
        Route::post('/applications/select-permits', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'selectPermits'])->name('applications.select-permits');
        Route::get('/applications/create-package', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'createPackage'])->name('applications.create-package');
        Route::post('/applications/store-package', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'storeMultiple'])->name('applications.store-package');
        Route::get('/applications/{id}', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'show'])->name('applications.show');
        Route::get('/applications/{id}/edit', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'edit'])->name('applications.edit');
        Route::put('/applications/{id}', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'update'])->name('applications.update');
        Route::get('/applications/{id}/preview-submit', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'previewSubmit'])->name('applications.preview-submit');
        Route::post('/applications/{id}/submit', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'submit'])->name('applications.submit');
        Route::post('/applications/{id}/cancel', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'cancel'])->name('applications.cancel');
        Route::post('/applications/{id}/documents', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'uploadDocument'])->name('applications.documents.upload');
        Route::delete('/applications/{applicationId}/documents/{documentId}', [App\Modules\Proyek\Controllers\Client\ApplicationController::class, 'deleteDocument'])->name('applications.documents.delete');

        // Quotation Routes (Phase 3.4)
        Route::get('/applications/{id}/quotation', [App\Http\Controllers\Client\ClientQuotationController::class, 'show'])->name('quotations.show');
        Route::post('/applications/{id}/quotation/accept', [App\Http\Controllers\Client\ClientQuotationController::class, 'accept'])->name('quotations.accept');
        Route::post('/applications/{id}/quotation/reject', [App\Http\Controllers\Client\ClientQuotationController::class, 'reject'])->name('quotations.reject');

        // Payment Routes (Phase 4)
        Route::get('/applications/{id}/payment', [App\Http\Controllers\Client\PaymentController::class, 'show'])->name('payments.show');
        Route::post('/applications/{id}/payment/initiate', [App\Http\Controllers\Client\PaymentController::class, 'initiate'])->name('payments.initiate');
        Route::post('/applications/{id}/payment/manual', [App\Http\Controllers\Client\PaymentController::class, 'manual'])->name('payments.manual');
        Route::get('/applications/{id}/payment/{paymentId}/success', [App\Http\Controllers\Client\PaymentController::class, 'success'])->name('payments.success');

        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Client\NotificationController::class, 'index'])
            ->name('notifications.index');
        Route::post('/notifications/read-all', [App\Http\Controllers\Client\NotificationController::class, 'markAllRead'])
            ->name('notifications.read-all');

        // Project Routes
        Route::get('/projects', [App\Modules\Proyek\Controllers\Client\ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{id}', [App\Modules\Proyek\Controllers\Client\ProjectController::class, 'show'])->name('projects.show');

        // Document Routes
        Route::get('/documents', [App\Modules\Proyek\Controllers\Client\DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [App\Modules\Proyek\Controllers\Client\DocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{id}/download', [App\Modules\Proyek\Controllers\Client\DocumentController::class, 'download'])->name('documents.download');

        // Profile Routes
        Route::get('/profile', [App\Http\Controllers\Client\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/notifications', [App\Http\Controllers\Client\ProfileController::class, 'updateNotifications'])->name('profile.notifications');
        Route::put('/profile/password', [App\Http\Controllers\Client\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::get('/profile/two-factor/enable', [App\Http\Controllers\Client\ProfileController::class, 'twoFactorEnable'])->name('profile.two-factor.enable');
        Route::delete('/profile/two-factor', [App\Http\Controllers\Client\ProfileController::class, 'twoFactorDisable'])->name('profile.two-factor.disable');
        Route::delete('/profile', [App\Http\Controllers\Client\ProfileController::class, 'destroy'])->name('profile.destroy');

        // Email Verification Routes
        Route::get('/verify-email', [App\Http\Controllers\Auth\ClientAuthController::class, 'showVerifyEmailNotice'])
            ->name('verification.notice');
        Route::post('/email/verification-notification', [App\Http\Controllers\Auth\ClientAuthController::class, 'resendVerificationEmail'])
            ->middleware('throttle:3,1')
            ->name('verification.send');
    });

    // Email verification callback (accessible without auth to allow verification)
    Route::get('/verify-email/{id}/{hash}', [App\Http\Controllers\Auth\ClientAuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});

// Email Webhook Routes (untuk menerima email dari Cloudflare/external service)
Route::prefix('webhook/email')->name('webhook.email.')->group(function () {
    // Receive incoming email
    Route::post('/receive', [App\Http\Controllers\EmailWebhookController::class, 'receive'])
        ->middleware(['throttle:60,1', 'email.webhook.replay'])
        ->name('receive');

    // Test webhook dengan data dummy
    Route::post('/test', [App\Http\Controllers\EmailWebhookController::class, 'test'])
        ->middleware(['auth', 'role:admin'])
        ->name('test');

    // Check webhook status
    Route::get('/status', [App\Http\Controllers\EmailWebhookController::class, 'status'])
        ->middleware(['auth', 'role:admin'])
        ->name('status');
});

// AI Settings Management Routes
Route::middleware(['auth', 'permission:ai.manage_settings'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('ai-settings', [App\Http\Controllers\Admin\AISettingsController::class, 'index'])
        ->name('ai-settings.index');
    Route::get('ai-settings/recent-changes', [App\Http\Controllers\Admin\AISettingsController::class, 'recentChanges'])
        ->name('ai-settings.recent-changes');
    Route::get('ai-settings/history/{key}', [App\Http\Controllers\Admin\AISettingsController::class, 'history'])
        ->name('ai-settings.history');
    Route::post('ai-settings/update', [App\Http\Controllers\Admin\AISettingsController::class, 'update'])
        ->name('ai-settings.update');
    Route::post('ai-settings/reset/{key}', [App\Http\Controllers\Admin\AISettingsController::class, 'reset'])
        ->name('ai-settings.reset');
    Route::post('ai-settings/clear-cache', [App\Http\Controllers\Admin\AISettingsController::class, 'clearCache'])
        ->name('ai-settings.clear-cache');
});

// Multi-User Email System Routes dipindah ke App\Modules\Email\routes.php (EmailServiceProvider)

// Admin: Permit Application Management (Phase 3)
Route::prefix('admin')->name('admin.')->middleware(['auth:web'])->group(function () {
    // Unified Permit Management Interface (New Tabbed Interface)
    Route::get('permits', [App\Modules\Perizinan\Controllers\Admin\PermitManagementController::class, 'index'])
        ->middleware(['permission:permits.manage'])
        ->name('permits.index');

    // Unified Recruitment Management Interface (Tabbed Interface)
    Route::get('recruitment', [RecruitmentController::class, 'index'])
        ->name('recruitment.index');

    Route::middleware(['permission:permits.manage'])->group(function () {
        // Permit Application List & Detail
        Route::get('permit-applications', [App\Modules\Proyek\Controllers\Admin\ApplicationManagementController::class, 'index'])
            ->name('permit-applications.index');
        Route::get('permit-applications/{id}', [App\Modules\Proyek\Controllers\Admin\ApplicationManagementController::class, 'show'])
            ->name('permit-applications.show');

        // Review Actions
        Route::post('permit-applications/{id}/start-review', [App\Modules\Proyek\Controllers\Admin\ApplicationManagementController::class, 'startReview'])
            ->name('permit-applications.start-review');
        Route::post('permit-applications/{id}/update-status', [App\Modules\Proyek\Controllers\Admin\ApplicationManagementController::class, 'updateStatus'])
            ->name('permit-applications.update-status');
        Route::post('permit-applications/{id}/add-notes', [App\Modules\Proyek\Controllers\Admin\ApplicationManagementController::class, 'addNotes'])
            ->name('permit-applications.add-notes');

        // Document Verification
        Route::post('permit-applications/{application}/documents/{document}/verify', [App\Modules\Proyek\Controllers\Admin\ApplicationManagementController::class, 'verifyDocument'])
            ->name('permit-applications.documents.verify');
        Route::post('permit-applications/{id}/verify-all-documents', [App\Modules\Proyek\Controllers\Admin\ApplicationManagementController::class, 'verifyAllDocuments'])
            ->name('permit-applications.verify-all-documents');
        Route::post('permit-applications/{id}/request-document-revision', [App\Modules\Proyek\Controllers\Admin\ApplicationManagementController::class, 'requestDocumentRevision'])
            ->name('permit-applications.request-document-revision');
        Route::post('permit-applications/{id}/convert-to-project', [App\Modules\Proyek\Controllers\Admin\ApplicationManagementController::class, 'convertToProject'])
            ->name('permit-applications.convert-to-project');

        // Package Revision Management
        Route::get('permit-applications/{id}/revise', [App\Modules\Proyek\Controllers\Admin\PackageRevisionController::class, 'create'])
            ->name('permit-applications.revise');
        Route::post('permit-applications/{id}/revisions', [App\Modules\Proyek\Controllers\Admin\PackageRevisionController::class, 'store'])
            ->name('permit-applications.revisions.store');
        Route::get('permit-applications/{applicationId}/revisions/{revisionId}', [App\Modules\Proyek\Controllers\Admin\PackageRevisionController::class, 'show'])
            ->name('permit-applications.revisions.show');

        // Quotation Management
        Route::get('quotations/create', [App\Http\Controllers\Admin\QuotationController::class, 'create'])
            ->name('quotations.create');
        Route::post('quotations', [App\Http\Controllers\Admin\QuotationController::class, 'store'])
            ->name('quotations.store');
        Route::get('quotations/{id}/edit', [App\Http\Controllers\Admin\QuotationController::class, 'edit'])
            ->name('quotations.edit');
        Route::put('quotations/{id}', [App\Http\Controllers\Admin\QuotationController::class, 'update'])
            ->name('quotations.update');
        Route::get('quotations/{id}/pdf', [App\Http\Controllers\Admin\QuotationController::class, 'generatePdf'])
            ->name('quotations.pdf');
        Route::post('quotations/{id}/send-email', [App\Http\Controllers\Admin\QuotationController::class, 'sendEmail'])
            ->name('quotations.send-email');

        // Document Review (Phase 3)
        Route::post('documents/{document}/approve', [App\Modules\Proyek\Controllers\Admin\DocumentReviewController::class, 'approve'])
            ->name('documents.approve');
        Route::post('documents/{document}/reject', [App\Modules\Proyek\Controllers\Admin\DocumentReviewController::class, 'reject'])
            ->name('documents.reject');
        Route::post('documents/bulk-approve', [App\Modules\Proyek\Controllers\Admin\DocumentReviewController::class, 'bulkApprove'])
            ->name('documents.bulk-approve');
        Route::post('applications/{application}/documents/approve-all', [App\Modules\Proyek\Controllers\Admin\DocumentReviewController::class, 'approveAll'])
            ->name('applications.documents.approve-all');

        // Application Notes/Communication (Phase 4)
        Route::post('applications/{application}/notes', [App\Modules\Proyek\Controllers\Admin\ApplicationNoteController::class, 'store'])
            ->name('applications.notes.store');
        Route::delete('applications/{application}/notes/{note}', [App\Modules\Proyek\Controllers\Admin\ApplicationNoteController::class, 'destroy'])
            ->name('applications.notes.destroy');
        Route::post('notes/{note}/mark-read', [App\Modules\Proyek\Controllers\Admin\ApplicationNoteController::class, 'markAsRead'])
            ->name('notes.mark-read');
    });

    // Payment Verification (Phase 4)
    Route::middleware(['permission:finances.manage_payments', '2fa'])->group(function () {
        Route::get('payments', [App\Http\Controllers\Admin\PaymentVerificationController::class, 'index'])
            ->name('payments.index');
        Route::get('payments/{id}', [App\Http\Controllers\Admin\PaymentVerificationController::class, 'show'])
            ->name('payments.show');
        Route::get('payments/{id}/proof', [App\Http\Controllers\Admin\PaymentVerificationController::class, 'proof'])
            ->name('payments.proof');
        Route::post('payments/{id}/verify', [App\Http\Controllers\Admin\PaymentVerificationController::class, 'verify'])
            ->name('payments.verify');
        Route::post('payments/{id}/reject', [App\Http\Controllers\Admin\PaymentVerificationController::class, 'reject'])
            ->name('payments.reject');
    });

});

// Client: Application Notes (Phase 4)
Route::prefix('client')->name('client.')->middleware(['auth:client'])->group(function () {
    Route::post('applications/{application}/notes', [App\Modules\Proyek\Controllers\Client\ApplicationNoteController::class, 'store'])
        ->name('applications.notes.store');

    // Package Revision Management for Client
    Route::get('applications/{applicationId}/revisions/{revisionId}', [App\Modules\Proyek\Controllers\Client\RevisionController::class, 'show'])
        ->name('applications.revisions.show');
    Route::post('applications/{applicationId}/revisions/{revisionId}/approve', [App\Modules\Proyek\Controllers\Client\RevisionController::class, 'approve'])
        ->name('applications.revisions.approve');
    Route::post('applications/{applicationId}/revisions/{revisionId}/reject', [App\Modules\Proyek\Controllers\Client\RevisionController::class, 'reject'])
        ->name('applications.revisions.reject');

    // P1 — Compliance Monitor
    Route::get('compliance-monitor', [App\Http\Controllers\Client\ComplianceMonitorController::class, 'index'])
        ->name('compliance.index');
    Route::get('compliance-monitor/export', [App\Http\Controllers\Client\ComplianceMonitorController::class, 'export'])
        ->name('compliance-monitor.export');

    // P6 — Document Vault
    Route::get('vault', [App\Http\Controllers\Client\DocumentVaultController::class, 'index'])->name('vault.index');
    Route::get('vault/{document}/download', [App\Http\Controllers\Client\DocumentVaultController::class, 'download'])->name('vault.download');
    Route::post('vault/bulk-download', [App\Http\Controllers\Client\DocumentVaultController::class, 'bulkDownload'])->name('vault.bulk-download');

    // P4 — OSS-RBA Status Tracker
    Route::get('oss-tracker', [App\Http\Controllers\Client\OssTrackerController::class, 'index'])->name('oss-tracker.index');
    Route::post('oss-tracker/credential', [App\Http\Controllers\Client\OssTrackerController::class, 'storeCredential'])->name('oss-tracker.store-credential');
    Route::post('oss-tracker/{status}/refresh', [App\Http\Controllers\Client\OssTrackerController::class, 'refreshStatus'])->name('oss-tracker.refresh');

    // P8 — B2B API Key Management
    Route::get('api-keys', [App\Http\Controllers\Client\ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('api-keys', [App\Http\Controllers\Client\ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::patch('api-keys/{apiKey}/toggle', [App\Http\Controllers\Client\ApiKeyController::class, 'toggleActive'])->name('api-keys.toggle');
    Route::delete('api-keys/{apiKey}', [App\Http\Controllers\Client\ApiKeyController::class, 'destroy'])->name('api-keys.destroy');

    // Command Palette Live Search
    Route::get('search', [App\Http\Controllers\Client\SearchController::class, 'index'])->name('search');

    // P10 — Compliance Report Generator
    Route::get('compliance-reports', [App\Http\Controllers\Client\ComplianceReportController::class, 'index'])->name('compliance-reports.index');
    Route::get('compliance-reports/create', [App\Http\Controllers\Client\ComplianceReportController::class, 'create'])->name('compliance-reports.create');
    Route::post('compliance-reports', [App\Http\Controllers\Client\ComplianceReportController::class, 'store'])->name('compliance-reports.store');
    Route::get('compliance-reports/{report}/download', [App\Http\Controllers\Client\ComplianceReportController::class, 'download'])->name('compliance-reports.download');
    Route::post('compliance-reports/{report}/email', [App\Http\Controllers\Client\ComplianceReportController::class, 'sendEmail'])->name('compliance-reports.email');
    Route::get('compliance-reports/template-params/{template}', [App\Http\Controllers\Client\ComplianceReportController::class, 'templateParameters'])->name('compliance-reports.template-params');
});

// Client: Push Notifications API (Phase 2)
Route::prefix('api/client/push')->name('api.client.push.')->middleware(['auth:client'])->group(function () {
    Route::post('/subscribe', [App\Http\Controllers\Api\PushNotificationController::class, 'subscribe'])->name('subscribe');
    Route::post('/unsubscribe', [App\Http\Controllers\Api\PushNotificationController::class, 'unsubscribe'])->name('unsubscribe');
    Route::get('/status', [App\Http\Controllers\Api\PushNotificationController::class, 'status'])->name('status');
    Route::post('/test', [App\Http\Controllers\Api\PushNotificationController::class, 'test'])->name('test');
});

// Push Notification Test Tool (Debug only - remove in production)
Route::get('/test-push', function () {
    return view('test-push');
})->middleware(['auth:client'])->name('test.push');

// Twitter OAuth callback placeholder (used for developer portal app setup)
Route::get('/auth/twitter/callback', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Twitter callback endpoint is active.',
    ]);
})->name('auth.twitter.callback');

// KBLI API
Route::prefix('api/kbli')->group(function () {
    Route::get('/', [App\Modules\Perizinan\Controllers\Api\KbliController::class, 'index'])->name('api.kbli.index');
    Route::get('/search', [App\Modules\Perizinan\Controllers\Api\KbliController::class, 'search'])->name('api.kbli.search');
    Route::get('/{code}', [App\Modules\Perizinan\Controllers\Api\KbliController::class, 'show'])->name('api.kbli.show');
});

// P9 — Permit Timeline Simulator (public tool)
Route::get('/simulasi-timeline', [App\Http\Controllers\TimelineSimulatorController::class, 'index'])->name('timeline-simulator.index');
Route::post('/simulasi-timeline/hitung', [App\Http\Controllers\TimelineSimulatorController::class, 'simulate'])->name('timeline-simulator.simulate');

// ============================================================================
// RECRUITMENT SYSTEM ROUTES
// ============================================================================

// Admin: Recruitment Management
Route::prefix('admin/recruitment')->name('admin.recruitment.')->middleware(['auth:web', 'permission:recruitment.manage'])->group(function () {

    // Interview Scheduling (Calendar-based)
    Route::resource('interviews', App\Modules\HRM\Controllers\Admin\InterviewScheduleController::class);
    Route::get('interviews/{interview}/feedback', [App\Modules\HRM\Controllers\Admin\InterviewScheduleController::class, 'feedback'])
        ->name('interviews.feedback');
    Route::post('interviews/{interview}/feedback', [App\Modules\HRM\Controllers\Admin\InterviewScheduleController::class, 'storeFeedback'])
        ->name('interviews.feedback.store');

    // Test Management
    Route::resource('tests', App\Modules\HRM\Controllers\Admin\TestManagementController::class);
    Route::post('tests/assign', [App\Modules\HRM\Controllers\Admin\TestManagementController::class, 'assign'])
        ->name('tests.assign');
    Route::post('tests/{test}/assign', [App\Modules\HRM\Controllers\Admin\TestManagementController::class, 'assign'])
        ->name('tests.assign.legacy'); // Keep for backward compatibility
    Route::get('tests/sessions/{session}/results', [App\Modules\HRM\Controllers\Admin\TestManagementController::class, 'sessionResults'])
        ->name('tests.sessions.results');
    Route::delete('tests/sessions/{session}/cancel', [App\Modules\HRM\Controllers\Admin\TestManagementController::class, 'cancelSession'])
        ->name('tests.sessions.cancel');

    // Manual Evaluation Routes (Essay/Rating Questions)
    Route::get('tests/sessions/{session}/evaluate-manual', [App\Modules\HRM\Controllers\Admin\TestManagementController::class, 'showEvaluationForm'])
        ->name('tests.sessions.evaluate-manual');
    Route::post('tests/sessions/{session}/evaluate-manual', [App\Modules\HRM\Controllers\Admin\TestManagementController::class, 'submitEvaluation'])
        ->name('tests.sessions.submit-evaluation-manual');

    // Document Editing Test Routes
    Route::post('tests/{test}/upload-template', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'uploadTemplate'])
        ->name('tests.upload-template');
    Route::get('tests/{test}/download-template', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'downloadTemplateForHR'])
        ->name('tests.download-template');
    Route::get('tests/sessions/{session}/evaluate', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'showEvaluationForm'])
        ->name('tests.sessions.evaluate');
    Route::post('tests/sessions/{session}/evaluate', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'submitEvaluation'])
        ->name('tests.sessions.submit-evaluation');
    Route::get('tests/sessions/{session}/download-submission', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'downloadSubmission'])
        ->name('tests.sessions.download-submission');

    // Recruitment Pipeline Dashboard
    Route::get('pipeline', [App\Modules\HRM\Controllers\Admin\RecruitmentPipelineController::class, 'index'])
        ->name('pipeline.index');
    Route::get('pipeline/{application}', [App\Modules\HRM\Controllers\Admin\RecruitmentPipelineController::class, 'show'])
        ->name('pipeline.show');
    Route::post('pipeline/{application}/initialize', [App\Modules\HRM\Controllers\Admin\RecruitmentPipelineController::class, 'initializeStages'])
        ->name('pipeline.initialize');
    Route::patch('pipeline/stages/{stage}', [App\Modules\HRM\Controllers\Admin\RecruitmentPipelineController::class, 'updateStage'])
        ->name('pipeline.stages.update');
});

// Candidate: Interview Portal (ID-based access, no auth required)
Route::prefix('candidate')->name('candidate.')->middleware('throttle:60,1')->group(function () {

    // Interview Access
    Route::get('interview/{interview}', [App\Modules\HRM\Controllers\Candidate\InterviewController::class, 'show'])
        ->name('interview.show');
    Route::post('interview/{interview}/reschedule', [App\Modules\HRM\Controllers\Candidate\InterviewController::class, 'requestReschedule'])
        ->name('interview.reschedule');
    Route::get('interview/{interview}/join', [App\Modules\HRM\Controllers\Candidate\InterviewController::class, 'join'])
        ->name('interview.join');

    // Test Portal
    Route::get('test/{token}', [App\Modules\HRM\Controllers\Candidate\TestController::class, 'show'])
        ->name('test.show');
    Route::post('test/{token}/start', [App\Modules\HRM\Controllers\Candidate\TestController::class, 'start'])
        ->name('test.start');
    Route::post('test/{token}/answer', [App\Modules\HRM\Controllers\Candidate\TestController::class, 'submitAnswer'])
        ->name('test.answer');
    Route::post('test/{token}/complete', [App\Modules\HRM\Controllers\Candidate\TestController::class, 'complete'])
        ->name('test.complete');

    // Document Editing Test - Candidate Routes
    Route::get('test/{token}/download-template', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'downloadTemplate'])
        ->name('test.download-template');
    Route::post('test/{token}/submit-document', [App\Http\Controllers\Admin\DocumentEditingTestController::class, 'submitDocument'])
        ->name('test.submit-document');

    // AJAX endpoints for test interface
    Route::post('test/{token}/track-tab', [App\Modules\HRM\Controllers\Candidate\TestController::class, 'trackTabSwitch'])
        ->name('test.track-tab');
    Route::get('test/{token}/time', [App\Modules\HRM\Controllers\Candidate\TestController::class, 'getRemainingTime'])
        ->name('test.time');
});
