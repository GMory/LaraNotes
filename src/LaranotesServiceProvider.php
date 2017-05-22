<?php

namespace Gmory\Laranotes;

use Illuminate\Support\ServiceProvider;

class LaranotesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('gmory-laranotes', function() {
            return new Laranotes;
        });
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }
}