<?php

namespace Inertia;

use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Inertia\Enum\Header;
use Inertia\Traits\Macroable;
use Inertia\Utils\Str;
use think\contract\Arrayable;
use think\facade\View;
use think\helper\Arr;
use think\Request;
use think\Response as ResponseThink;
use think\response\Json;

class Response
{
    use Macroable;

    protected $component;

    protected $props;

    protected $rootView;

    protected $version;

    protected $viewData = [];

    /**
     * @param string $component
     * @param array|Arrayable $props
     * @param string $rootView
     * @param string $version
     */
    public function __construct(string $component, array|Arrayable $props, string $rootView = 'app', string $version = '')
    {
        $this->component = $component;
        $this->props = $props instanceof Arrayable ? $props->toArray() : $props;
        $this->rootView = $rootView;
        $this->version = $version;
    }

    /**
     * @param string|array $key
     * @param mixed $value
     *
     * @return $this
     */
    public function with($key, $value = null): self
    {
        if (is_array($key)) {
            $this->props = array_merge($this->props, $key);
        } else {
            $this->props[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string|array $key
     * @param mixed $value
     *
     * @return $this
     */
    public function withViewData($key, $value = null): self
    {
        if (is_array($key)) {
            $this->viewData = array_merge($this->viewData, $key);
        } else {
            $this->viewData[$key] = $value;
        }

        return $this;
    }

    public function rootView(string $rootView): self
    {
        $this->rootView = $rootView;

        return $this;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     *
     * @return string|Json
     */
    public function toResponse(Request $request): Json|string
    {
        $props = $this->resolveProperties($request, $this->props);

        $page = [
            'component' => $this->component,
            'props' => $props,
            'url' => Str::start(Str::after($request->url(true), $request->domain(true)), '/'),
            'version' => $this->version,
        ];

        if ($request->header(Header::INERTIA)) {
            return json($page, 200, [Header::INERTIA => 'true']);
        }

        return View::fetch($this->rootView, array_merge($this->viewData, ['page' => $page]));
    }

    /**
     * Resolve the properites for the response.
     */
    public function resolveProperties(Request $request, array $props): array
    {
        $isPartial = $request->header(Header::PARTIAL_COMPONENT) === $this->component;

        if (!$isPartial) {
            $props = array_filter($this->props, static function ($prop) {
                return !($prop instanceof LazyProp);
            });
        }

        $props = $this->resolveArrayableProperties($props, $request);

        if ($isPartial && !empty($request->header(Header::PARTIAL_ONLY))) {
            $props = $this->resolveOnly($request, $props);
        }

        if ($isPartial && !empty($request->header(Header::PARTIAL_EXCEPT))) {
            $props = $this->resolveExcept($request, $props);
        }

        $props = $this->resolveAlways($props);

        return $this->resolvePropertyInstances($props, $request);
    }


    /**
     * Resolve all arrayables properties into an array.
     */
    public function resolveArrayableProperties(array $props, Request $request, bool $unpackDotProps = true): array
    {
        foreach ($props as $key => $value) {
            if ($value instanceof Arrayable) {
                $value = $value->toArray();
            }

            if (is_array($value)) {
                $value = $this->resolveArrayableProperties($value, $request, false);
            }

            if ($unpackDotProps && str_contains($key, '.')) {
                Arr::set($props, $key, $value);
                unset($props[$key]);
            } else {
                $props[$key] = $value;
            }
        }

        return $props;
    }

    /**
     * Resolve the `only` partial request props.
     */
    public function resolveOnly(Request $request, array $props): array
    {
        $only = array_filter(explode(',', $request->header(Header::PARTIAL_ONLY, '')));

        $value = [];

        foreach ($only as $key) {
            Arr::set($value, $key, data_get($props, $key));
        }

        return $value;
    }

    /**
     * Resolve the `except` partial request props.
     */
    public function resolveExcept(Request $request, array $props): array
    {
        $except = array_filter(explode(',', $request->header(Header::PARTIAL_EXCEPT, '')));

        Arr::forget($props, $except);

        return $props;
    }

    /**
     * Resolve `always` properties that should always be included on all visits, regardless of "only" or "except" requests.
     */
    public function resolveAlways(array $props): array
    {
        $always = array_filter($this->props, static function ($prop) {
            return $prop instanceof AlwaysProp;
        });

        return array_merge(
            $always,
            $props
        );
    }

    /**
     * Resolve all necessary class instances in the given props.
     */
    public function resolvePropertyInstances(array $props, Request $request): array
    {
        foreach ($props as $key => $value) {
            if ($value instanceof Closure) {
                $value = call_user_func($value);
            }

            if ($value instanceof LazyProp) {
                $value = call_user_func($value);
            }

            if ($value instanceof AlwaysProp) {
                $value = call_user_func($value);
            }

            if ($value instanceof PromiseInterface) {
                $value = $value->wait();
            }

            if ($value instanceof ResponseThink) {
                $value = $value->getData();
            }

            if (is_array($value)) {
                $value = $this->resolvePropertyInstances($value, $request);
            }

            $props[$key] = $value;
        }

        return $props;
    }
}
