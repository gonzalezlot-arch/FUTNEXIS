<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLocalEnvironment
{
    /**
     * Respond as not found outside the local environment (DEC-CORE-007).
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(app()->environment('local'), Response::HTTP_NOT_FOUND);

        return $next($request);
    }
}
