<?php

namespace Inertia;

use Inertia\Facade\Inertia;
use think\Request;

class Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render(
            $request->route()->defaults['component'],
            $request->route()->defaults['props']
        );
    }
}
