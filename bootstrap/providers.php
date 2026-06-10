<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\DatabaseProtectionProvider::class,

    // Modules
    App\Modules\Finansial\Providers\FinansialServiceProvider::class,
    App\Modules\HRM\Providers\HRMServiceProvider::class,
    App\Modules\Perizinan\Providers\PerizinanServiceProvider::class,
    App\Modules\Proyek\Providers\ProyekServiceProvider::class,
    App\Modules\ContentSeo\Providers\ContentSeoServiceProvider::class,
    App\Modules\Email\Providers\EmailServiceProvider::class,
    App\Modules\AI\Providers\AIServiceProvider::class,
    App\Providers\AiConfigSyncServiceProvider::class,
    App\Providers\AiProviderServiceProvider::class,
];
