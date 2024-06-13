<?php

use Inertia\ResponseFactory;
use think\contract\Arrayable;
use think\Response;
use think\response\Json;

if (!function_exists('inertia')) {
    /**
     * Inertia helper.
     *
     * @param string|null $component
     * @param array|Arrayable $props
     * @return ResponseFactory|Response|Json|string
     */
    function inertia(string $component = null, Arrayable|array $props = []): Json|Response|ResponseFactory|string
    {
        $instance = new ResponseFactory();

        if (!empty($component)) {
            return $instance->render($component, $props);
        }

        return $instance;
    }
}

if (!function_exists('inertia_location')) {
    /**
     * Inertia location helper.
     *
     * @param  $url
     * @return \Symfony\Component\HttpFoundation\Response|Response
     */
    function inertia_location($url): Response|\Symfony\Component\HttpFoundation\Response
    {
        $instance = new ResponseFactory();

        return $instance->location($url);
    }
}
