<?php

namespace Stepanenko3\LaravelInitializer;

use Illuminate\Support\ServiceProvider;
use NunoMaduro\LaravelConsoleTask\LaravelConsoleTaskServiceProvider;
use Stepanenko3\LaravelInitializer\Contracts\Runner;

class InitializerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->register(LaravelConsoleTaskServiceProvider::class);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(Runner::class, Run::class);
    }
}
