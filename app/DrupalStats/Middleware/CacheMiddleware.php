<?php

namespace App\DrupalStats\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CacheMiddleware
{
    const CACHE_MINUTES = 720;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $key = $this->buildCacheKey($request);
        if (! is_null($response = Cache::get($key))) {
            return $response;
        }

        /** @var Response $response */
        $response = $next($request);
        if ($response->isSuccessful() || $response->isRedirection()) {
            Cache::put($key, $response, static::CACHE_MINUTES);
        }

        return $response;
    }

    /**
     * Derive a cache key from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $suffix
     *
     * @return string
     *   The cache key.
     */
    protected function buildCacheKey(Request $request, $suffix = '')
    {
        return 'route_' . Str::slug($request->getUri()) . $suffix;
    }
}
