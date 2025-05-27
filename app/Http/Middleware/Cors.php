<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Daftar origin yang diizinkan.
     *
     * @var string[]
     */
    protected array $whitelist = [
        'http://localhost:3000',
        'http://localhost:3001',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');

        // Jika origin ada di whitelist, tambahkan header
        $allowed = in_array($origin, $this->whitelist, true);

        // Untuk preflight (OPTIONS)
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', $allowed ? $origin : '')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers', '*'))
                ->header('Access-Control-Max-Age', '3600');
        }

        // Untuk request biasa
        /** @var Response $response */
        $response = $next($request);
        if ($allowed) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', '*');
        }

        return $response;
    }
}
