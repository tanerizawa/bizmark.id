<?php

namespace App\Modules\AI\Providers;

use App\Mcp\Servers\CivicStackServer;
use App\Modules\Shared\Providers\ModuleServiceProvider;
use Laravel\Mcp\Facades\Mcp;

class AIServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'AI';

    protected string $moduleNamespace = 'App\Modules\AI';

    public function register(): void
    {
        parent::register();

        Mcp::local('civic-stack', CivicStackServer::class);
    }
}
