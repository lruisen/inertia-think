<?php

namespace Inertia\Facade;

use Closure;
use Inertia\AlwaysProp;
use Inertia\LazyProp;
use Inertia\ResponseFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use think\contract\Arrayable;
use think\Facade;

/**
 * @method static void setRootView(string $name)
 * @method static void share(string|array|Arrayable $key, mixed $value = null)
 * @method static mixed getShared(string|null $key = null, mixed $default = null)
 * @method static void flushShared()
 * @method static void version(Closure|string|null $version)
 * @method static string getVersion()
 * @method static LazyProp lazy(callable $callback)
 * @method static AlwaysProp always(mixed $value)
 * @method static \Inertia\Response render(string $component, array|Arrayable $props = [])
 * @method static Response location(string|RedirectResponse $url)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 */
class Inertia extends Facade
{
    public static function getFacadeClass()
    {
        return ResponseFactory::class;
    }
}
