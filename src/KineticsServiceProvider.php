<?php

namespace Kinetics;

use Illuminate\Support\ServiceProvider;

class KineticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/kinetics.php',
            'kinetics',
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/kinetics.php' => config_path('kinetics.php'),
            ], 'kinetics-config');
        }
    }
}
