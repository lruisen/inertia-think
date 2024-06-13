<?php

namespace Inertia;

use Closure;
use Inertia\Enum\Header;
use Inertia\Traits\Macroable;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use think\contract\Arrayable;
use think\facade\Request;
use think\helper\Arr;

class ResponseFactory
{
    use Macroable;

    /** @var string */
    protected string $rootView = 'app';

    /** @var array */
    protected array $sharedProps = [];

    /** @var Closure|string|null */
    protected string|null|Closure $version = null;

    public function setRootView(string $name): void
    {
        $this->rootView = $name;
    }

    /**
     * @param string|array|Arrayable $key
     * @param mixed $value
     */
    public function share($key, $value = null): void
    {
        if (is_array($key)) {
            $this->sharedProps = array_merge($this->sharedProps, $key);
        } elseif ($key instanceof Arrayable) {
            $this->sharedProps = array_merge($this->sharedProps, $key->toArray());
        } else {
            Arr::set($this->sharedProps, $key, $value);
        }
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function getShared(?string $key = null, $default = null)
    {
        if ($key) {
            return Arr::get($this->sharedProps, $key, $default);
        }

        return $this->sharedProps;
    }

    public function flushShared(): void
    {
        $this->sharedProps = [];
    }

    /**
     * @param Closure|string|null $version
     */
    public function version($version): void
    {
        $this->version = $version;
    }

    public function getVersion(): string
    {
        $version = $this->version instanceof Closure
            ? call_user_func($this->version)
            : $this->version;

        return (string)$version;
    }

    public function lazy(callable $callback): LazyProp
    {
        return new LazyProp($callback);
    }

    /**
     * @param mixed $value
     */
    public function always($value): AlwaysProp
    {
        return new AlwaysProp($value);
    }

    /**
     * @param string $component
     * @param array|Arrayable $props
     * @return Response
     */
    public function render(string $component, Arrayable|array $props = [])
    {
        if ($props instanceof Arrayable) {
            $props = $props->toArray();
        }

        return new Response(
            $component,
            array_merge($this->sharedProps, $props),
            $this->rootView,
            $this->getVersion()
        );
    }

    /**
     * @param string|SymfonyRedirect $url
     * @return SymfonyResponse|\think\Response
     */
    public function location(SymfonyRedirect|string $url)
    {
        if (Request::inertia()) {
            return \think\Response::create([], 'html', 409)
                ->header([
                    Header::LOCATION => $url instanceof SymfonyRedirect ? $url->getTargetUrl() : $url
                ]);
        }

        return $url instanceof SymfonyRedirect ? $url : redirect($url);
    }
}
