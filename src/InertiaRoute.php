<?php

namespace Inertia;

use Inertia\Traits\Macroable;
use think\Route;
use think\route\RuleItem;

class InertiaRoute extends Route
{
    use Macroable;

    public function inertia(string $uri, string $component, array $props = []): RuleItem
    {
        return $this->rule($uri, '\Inertia\Controller/index', 'GET|HEAD')
            ->append([
                'component' => $component,
                'props' => $props,
            ]);
    }
}
