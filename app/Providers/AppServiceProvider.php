<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\City;
use App\Models\LandingPage;
use App\Models\Post;
use App\Models\Service;
use App\Models\ServiceCluster;
use App\Models\ServiceClusterLandingPage;
use App\Observers\CategoryObserver;
use App\Observers\CityObserver;
use App\Observers\LandingPageObserver;
use App\Observers\PostObserver;
use App\Observers\ServiceClusterLandingPageObserver;
use App\Observers\ServiceClusterObserver;
use App\Observers\ServiceObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (config('telescope.enabled')) {
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Service::observe(ServiceObserver::class);
        City::observe(CityObserver::class);
        LandingPage::observe(LandingPageObserver::class);
        Post::observe(PostObserver::class);
        Category::observe(CategoryObserver::class);
        ServiceCluster::observe(ServiceClusterObserver::class);
        ServiceClusterLandingPage::observe(ServiceClusterLandingPageObserver::class);
    }
}
