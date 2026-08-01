<?php

use App\Http\Controllers\Blog\BlogIndexController;
use App\Http\Controllers\Blog\BlogShowController;
use App\Http\Controllers\Cities\CityIndexController;
use App\Http\Controllers\Cities\CityShowController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqIndexController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Portfolio\PortfolioIndexController;
use App\Http\Controllers\Portfolio\PortfolioShowController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\Services\ServiceClusterCityController;
use App\Http\Controllers\Services\ServiceClusterShowController;
use App\Http\Controllers\Services\ServiceIndexController;
use App\Http\Controllers\Services\ServiceShowController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\States\StateIndexController;
use App\Http\Controllers\States\StateShowController;
use App\Models\Category;
use App\Models\City;
use App\Models\LandingPage;
use App\Models\PortfolioItem;
use App\Models\Post;
use App\Models\Service;
use App\Models\ServiceCluster;
use App\Models\State;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route model bindings (public site)
|--------------------------------------------------------------------------
|
| Scoped to the "published"/"active" state so draft records 404 on the
| public site. These bindings only apply to routes using the matching
| {service}/{city}/{landingPage} parameter names below — the Filament
| admin panel resolves its own {record} bindings independently and is
| unaffected.
*/

Route::bind('service', fn (string $value): Service => Service::query()->active()->where('slug', $value)->firstOrFail());
Route::bind('city', fn (string $value): City => City::query()->active()->where('slug', $value)->firstOrFail());
Route::bind('landingPage', fn (string $value): LandingPage => LandingPage::query()->published()->where('slug', $value)->firstOrFail());
Route::bind('post', fn (string $value): Post => Post::query()->published()->where('slug', $value)->firstOrFail());
Route::bind('state', fn (string $value): State => State::query()->active()->where('slug', $value)->firstOrFail());
Route::bind('portfolioItem', fn (string $value): PortfolioItem => PortfolioItem::query()->active()->where('slug', $value)->firstOrFail());
Route::bind('category', fn (string $value): Category => Category::query()->where('slug', $value)->firstOrFail());
Route::bind('cluster', fn (string $value): ServiceCluster => ServiceCluster::query()->published()->where('slug', $value)->firstOrFail());

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [PageController::class, 'about'])->name('about');

Route::get('/contato', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contato', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::get('/consultor-ia', [ConsultationController::class, 'show'])->name('consultation.show');

Route::get('/servicos', [ServiceIndexController::class, 'index'])->name('services.index');
Route::get('/servicos/{service}', [ServiceShowController::class, 'show'])->name('services.show');
Route::get('/servicos/{service}/{cluster}', [ServiceClusterShowController::class, 'show'])->name('services.clusters.show');
Route::get('/servicos/{service}/{cluster}/{city}', [ServiceClusterCityController::class, 'show'])->name('services.clusters.city.show');

Route::get('/cidades', [CityIndexController::class, 'index'])->name('cities.index');
Route::get('/cidades/{city}', [CityShowController::class, 'show'])->name('cities.show');

Route::get('/estados', [StateIndexController::class, 'index'])->name('states.index');
Route::get('/estados/{state}', [StateShowController::class, 'show'])->name('states.show');

Route::get('/blog', [BlogIndexController::class, 'index'])->name('blog.index');
Route::get('/blog/categoria/{category}', [BlogIndexController::class, 'index'])->name('blog.category');
Route::get('/blog/{post}', [BlogShowController::class, 'show'])->name('blog.show');

Route::get('/perguntas-frequentes', [FaqIndexController::class, 'index'])->name('faq.index');

Route::get('/portfolio', [PortfolioIndexController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/servico/{service}', [PortfolioIndexController::class, 'index'])->name('portfolio.service');
Route::get('/portfolio/{portfolioItem}', [PortfolioShowController::class, 'show'])->name('portfolio.show');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

// Rota curinga de SEO programático ({service}-em-{city}) — deve ficar por último.
Route::get('/{landingPage}', [LandingPageController::class, 'show'])->name('landing.show');
