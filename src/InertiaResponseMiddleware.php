<?php


namespace Inertia;

use Inertia\Enum\Header;

class InertiaResponseMiddleware
{
    public function handle($request, $next)
    {
        $response = $next($request);

        $content = $response->getContent();
        if (str_starts_with($content, 'inertia_')) {
            $content = ltrim($content, 'inertia_');
            $data = json_decode($content, true);
            
            return json($data, 200, [Header::INERTIA => 'true']);
        }

        return $response;
    }
}
