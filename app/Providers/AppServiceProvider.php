<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\City;
use App\Models\LandingPage;
use App\Models\PortfolioItem;
use App\Models\Post;
use App\Models\Service;
use App\Models\ServiceCluster;
use App\Models\ServiceClusterLandingPage;
use App\Models\State;
use App\Observers\CategoryObserver;
use App\Observers\CityObserver;
use App\Observers\LandingPageObserver;
use App\Observers\PostObserver;
use App\Observers\ServiceClusterLandingPageObserver;
use App\Observers\ServiceClusterObserver;
use App\Observers\ServiceObserver;
use App\Services\Tools\ToolDefinition;
use App\Services\Tools\ToolRegistry;
use Illuminate\Support\Facades\Route;
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

        $this->bindRouteParameters();
    }

    /**
     * Registered here (rather than in routes/web.php) so these bindings still run when
     * `php artisan route:cache` is active — a cached route file is loaded without ever
     * executing routes/web.php again, so any Route::bind() calls placed there would
     * silently stop working in production the moment routes are cached.
     *
     * Scoped to the "published"/"active" state so draft records 404 on the public site.
     * These bindings only apply to routes using the matching {service}/{city}/{landingPage}
     * parameter names — the Filament admin panel resolves its own {record} bindings
     * independently and is unaffected.
     */
    private function bindRouteParameters(): void
    {
        Route::bind('service', fn (string $value): Service => Service::query()->active()->where('slug', $value)->firstOrFail());
        Route::bind('city', fn (string $value): City => City::query()->active()->where('slug', $value)->firstOrFail());
        Route::bind('landingPage', fn (string $value): LandingPage => LandingPage::query()->published()->where('slug', $value)->firstOrFail());
        Route::bind('post', fn (string $value): Post => Post::query()->published()->where('slug', $value)->firstOrFail());
        Route::bind('state', fn (string $value): State => State::query()->active()->where('slug', $value)->firstOrFail());
        Route::bind('portfolioItem', fn (string $value): PortfolioItem => PortfolioItem::query()->active()->where('slug', $value)->firstOrFail());
        Route::bind('category', fn (string $value): Category => Category::query()->where('slug', $value)->firstOrFail());
        Route::bind('cluster', fn (string $value): ServiceCluster => ServiceCluster::query()->published()->where('slug', $value)->firstOrFail());
        Route::bind('tool', fn (string $value): ToolDefinition => app(ToolRegistry::class)->find($value) ?? abort(404));
    }
}
