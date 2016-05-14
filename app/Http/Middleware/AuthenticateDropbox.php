<?php

namespace Compendium\Http\Middleware;

use Closure;
use Compendium\Services\Dropbox\Dropbox;

class AuthenticateDropbox
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
        $dropbox = app(Dropbox::class);

        // Request is redirect from Dropbox with OAuth token.
        if ($request->input('state') && $request->input('code')) {
            if (! $dropbox->validateToken($request->user())) {
                $dropbox->storeToken($request, $request->user());
            }

            return $next($request);
        }

        // Check if user has valid Dropbox token.
        if ($dropbox->validateToken($request->user())) {
            return $next($request);
        }

        // Authenticate with Dropbox to retrieve a new token.
        $dropbox->requestToken($request->user());
    }
}
