<?php

namespace Compendium\Providers;

use Illuminate\Support\ServiceProvider;
use Compendium\Services\Dropbox\Dropbox;

class DropboxServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('dropbox', function ($app) {
            return new Dropbox();
        });
    }
}
