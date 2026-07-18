<?php

use App\Http\Controllers\Cities\CityIndexController;
use App\Http\Controllers\Cities\CityShowController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\Services\ServiceIndexController;
use App\Http\Controllers\Services\ServiceShowController;
use App\Http\Controllers\SitemapController;
use App\Models\City;
use App\Models\LandingPage;
use App\Models\Service;
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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [PageController::class, 'about'])->name('about');

Route::get('/contato', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contato', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::get('/servicos', [ServiceIndexController::class, 'index'])->name('services.index');
Route::get('/servicos/{service}', [ServiceShowController::class, 'show'])->name('services.show');

Route::get('/cidades', [CityIndexController::class, 'index'])->name('cities.index');
Route::get('/cidades/{city}', [CityShowController::class, 'show'])->name('cities.show');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

// Rota curinga de SEO programático ({service}-em-{city}) — deve ficar por último.
Route::get('/{landingPage}', [LandingPageController::class, 'show'])->name('landing.show');
