<?php

namespace App\DrupalStats\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

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
        if (!is_null($response = Cache::get($key))) {
            return $this->unserializeResponse($response);
        }

        /** @var Response $response */
        $response = $next($request);
        Cache::put($key, $this->serializeResponse($response), static::CACHE_MINUTES);

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

    protected function serializeResponse(Response $response)
    {
        $content = $response->getContent();
        $status_code = $response->getStatusCode();
        $headers = $response->headers;
        return serialize(compact('content', 'status_code', 'headers'));
    }

    protected function unserializeResponse($serializedResponse)
    {
        $responseProperties = unserialize($serializedResponse);
        $response = new Response($responseProperties['content'], $responseProperties['status_code']);
        $response->headers = $responseProperties['headers'];
        return $response;
    }
}
