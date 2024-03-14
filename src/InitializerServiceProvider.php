<?php

namespace Stepanenko3\LaravelInitializer;

use Illuminate\Support\ServiceProvider;
use Stepanenko3\LaravelInitializer\Contracts\Runner;

class InitializerServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->bind(Runner::class, Run::class);
    }
}
