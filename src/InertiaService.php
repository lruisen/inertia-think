<?php

namespace Inertia;

use app\Request;
use Inertia\Commands\{CreateMiddleware, StartSsr, StopSsr};
use Inertia\Enum\Header;
use Inertia\Ssr\{Gateway, HttpGateway};
use think\facade\Config;
use think\Service;

class InertiaService extends Service
{
    public function register(): void
    {
        // 重新绑定路由类，使 Route 更方便适配 inertia.js
        $this->app->bind('route', InertiaRoute::class);

        $this->app->bind(ResponseFactory::class);

        $this->app->bind(Gateway::class, HttpGateway::class);

        $this->mergeViewConfig();

        $this->registerRequestMacro();
    }

    public function boot(): void
    {
        $this->commands([
            CreateMiddleware::class,
            StartSsr::class,
            StopSsr::class,
        ]);
    }

    protected function mergeViewConfig(): void
    {
        // 动态设置视图配置，加载自定义模板 InertiaTag
        $view_config = config('view');
        $config = array_merge($view_config, [
            'taglib_build_in' => '\Inertia\InertiaTag,cx',
        ]);

        Config::set($config, 'view');
    }

    protected function registerRequestMacro(): void
    {
        Request::macro('inertia', function () {
            return (bool)request()->header(Header::INERTIA);
        });
    }
}
