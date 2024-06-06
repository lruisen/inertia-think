<?php


namespace Inertia;

use Inertia\Commands\CreateMiddleware;
use Inertia\Commands\StartSsr;
use Inertia\Commands\StopSsr;
use Inertia\Ssr\Gateway;
use Inertia\Ssr\HttpGateway;
use think\Service;

class InertiaService extends Service
{
    public function register(): void
    {
        $this->app->bind(ResponseFactory::class);

        $this->app->bind(Gateway::class, HttpGateway::class);

    }

    public function boot(): void
    {
        $this->commands([
            CreateMiddleware::class,
            StartSsr::class,
            StopSsr::class,
        ]);
    }
}
