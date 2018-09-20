<?php

namespace Hkp22\CacheLaraViewFragments\Middleware;

use Closure;

class FlushViewCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        \Cache::tags('views')->flush();

        return $next($request);
    }
}
