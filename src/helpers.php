<?php

use Inertia\Facade\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;
use think\contract\Arrayable;

if (!function_exists('inertia')) {
    /**
     * Inertia helper.
     *
     * @param null|string $component
     * @param array|Arrayable $props
     * @return ResponseFactory|Response
     */
    function inertia($component = null, $props = [])
    {
        $instance = Inertia::getFacadeRoot();

        if ($component) {
            return $instance->render($component, $props);
        }

        return $instance;
    }
}

if (!function_exists('inertia_location')) {
    /**
     * Inertia location helper.
     *
     * @param string  url
     * @return Response
     */
    function inertia_location($url): Response
    {
        $instance = Inertia::getFacadeRoot();

        return $instance->location($url);
    }
}
