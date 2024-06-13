<?php

namespace Inertia;

use Inertia\Facade\Inertia;
use think\Request;

class Controller
{
    public function index()
    {
        return Inertia::render(
            request()->route()['component'],
            request()->route()['props']
        );
    }

    public function __invoke(Request $request): ResponseFactory
    {
        return Inertia::render(
            $request->route()->options['component'],
            $request->route()->options['props']
        );
    }
}
