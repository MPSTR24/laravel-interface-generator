<?php

namespace Mpstr24\InterfaceTyper;

use Illuminate\Support\ServiceProvider;
use Mpstr24\InterfaceTyper\Console\Commands\InterfaceGenerator;

class InterfaceTyperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InterfaceGenerator::class,
            ]);
        }
    }
}
