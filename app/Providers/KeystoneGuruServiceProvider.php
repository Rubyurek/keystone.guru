<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class KeystoneGuruServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Bind the interface to the actual service
        $this->app->bind('App\Service\EchoServerConfigServiceInterface', 'App\Service\EchoServerConfigService');
        $this->app->bind('App\Service\EchoServerHttpApiServiceInterface', 'App\Service\EchoServerHttpApiService');

        // Dashboard
        $this->app->bind('App\Service\Dashboard\StatisticsServiceInterface', 'App\Service\Dashboard\UsersStatisticsService');
        $this->app->bind('App\Service\Dashboard\StatisticsServiceInterface', 'App\Service\Dashboard\TeamsStatisticsService');

        // M+ Seasons
        $this->app->bind('App\Service\Season\SeasonServiceInterface', 'App\Service\Season\SeasonService');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}