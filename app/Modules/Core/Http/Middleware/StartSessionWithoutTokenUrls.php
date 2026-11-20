<?php

namespace App\Modules\Core\Http\Middleware;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;

class StartSessionWithoutTokenUrls extends StartSession
{
    /**
     * Routes whose address carries a verification or recovery token.
     *
     * @var array<int, string>
     */
    private const array TOKEN_ROUTES = ['verification.show', 'password.reset'];

    /**
     * Never keep those addresses as the "previous URL" in the session.
     *
     * @param  Session  $session
     */
    protected function storeCurrentUrl(Request $request, $session): void
    {
        if ($request->routeIs(...self::TOKEN_ROUTES)) {
            return;
        }

        parent::storeCurrentUrl($request, $session);
    }
}
