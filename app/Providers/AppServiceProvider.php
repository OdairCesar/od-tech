<?php

namespace App\Providers;

use App\Models\City;
use App\Models\LandingPage;
use App\Models\Service;
use App\Observers\CityObserver;
use App\Observers\LandingPageObserver;
use App\Observers\ServiceObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Service::observe(ServiceObserver::class);
        City::observe(CityObserver::class);
        LandingPage::observe(LandingPageObserver::class);
    }
}
