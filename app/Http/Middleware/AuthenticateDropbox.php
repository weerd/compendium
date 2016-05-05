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

        // dd($dropbox);

        // dd($request->user());
        // dd($dropbox);
        // dd($request->input('state'));

        // Is request coming from Dropbox OAuth2?
        // Are there other URL params need to consider?
        // isset($request->input('state'))
        if ($request->input('state')) {
            dd($_GET);
            // store the token for the current user
            $dropbox->storeToken($request, $request->user());

            // proceed along and finish request
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
