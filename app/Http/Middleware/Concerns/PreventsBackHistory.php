<?php

namespace App\Http\Middleware\Concerns;

use Symfony\Component\HttpFoundation\Response;

trait PreventsBackHistory
{
    /**
     * Prevent browser caching / back-button restoration of authenticated pages.
     */
    protected function withNoCacheHeaders(Response $response): Response
    {
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');

        return $response;
    }
}
