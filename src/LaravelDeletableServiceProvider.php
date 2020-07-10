<?php

declare(strict_types=1);

namespace F9Web\LaravelDeletable;

use Illuminate\Support\ServiceProvider;

use function config_path;
use function resource_path;

class LaravelDeletableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/../config/f9web-laravel-deletable.php' => config_path('f9web-laravel-deletable.php'),
            ],
            'config'
        );

        $this->mergeConfigFrom(__DIR__ . '/../config/f9web-laravel-deletable.php', 'f9web-laravel-deletable');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang/', 'f9web-laravel-deletable');

        $this->publishes(
            [
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/f9web-laravel-deletable'),
            ]
        );
    }

    public function register()
    {
        //
    }
}
