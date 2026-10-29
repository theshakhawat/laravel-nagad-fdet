<?php

namespace Theshakhawat\NagadFdet;

use Illuminate\Support\ServiceProvider;
use Theshakhawat\NagadFdet\Console\Commands\InstallNagadPackage;
use Theshakhawat\NagadFdet\Services\NagadService;

class NagadServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/nagad.php', 'nagad');

        $this->app->singleton('nagad-fdet', function ($app) {
            return new NagadService();
        });

        $this->app->alias('nagad-fdet', NagadService::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Config/nagad.php' => config_path('nagad.php'),
            ], 'nagad-config');

            // Artisan Command রেজিস্টার করা
            $this->commands([
                InstallNagadPackage::class,
            ]);
        }
    }
}
