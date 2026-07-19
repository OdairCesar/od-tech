<?php

use App\Models\City;
use App\Models\LandingPage;
use App\Models\Service;
use App\Services\Seo\SeoMetaBuilder;

test('it generates fallback meta when the landing page has no overrides', function () {
    $service = Service::factory()->create([
        'title' => 'Criação de Sites',
        'description' => 'Sites rápidos e responsivos para empresas de {cidade}.',
    ]);
    $city = City::factory()->create(['name' => 'Bauru']);
    $landingPage = LandingPage::where('service_id', $service->id)->where('city_id', $city->id)->sole();

    $seo = app(SeoMetaBuilder::class)->forLandingPage($landingPage);

    expect($seo->title)->toBe('Criação de Sites em Bauru | OD Tec')
        ->and($seo->description)->toBe('Sites rápidos e responsivos para empresas de Bauru.')
        ->and($seo->canonical)->toBe(route('landing.show', $landingPage))
        ->and($seo->robots)->toBe('index,follow');
});

test('it truncates the generated description to 155 characters', function () {
    $service = Service::factory()->create([
        'description' => str_repeat('Texto institucional bem detalhado sobre o serviço prestado. ', 5),
    ]);
    $city = City::factory()->create();
    $landingPage = LandingPage::where('service_id', $service->id)->where('city_id', $city->id)->sole();

    $seo = app(SeoMetaBuilder::class)->forLandingPage($landingPage);

    expect(mb_strlen($seo->description))->toBeLessThanOrEqual(158);
});

test('manual overrides on the landing page take precedence over generated meta', function () {
    $service = Service::factory()->create();
    $city = City::factory()->create();
    $landingPage = LandingPage::where('service_id', $service->id)->where('city_id', $city->id)->sole();

    $landingPage->update([
        'meta_title' => 'Título customizado',
        'meta_description' => 'Descrição customizada',
        'canonical' => 'https://od.tech/customizada',
        'robots' => 'noindex,nofollow',
    ]);

    $seo = app(SeoMetaBuilder::class)->forLandingPage($landingPage->fresh(['service', 'city']));

    expect($seo->title)->toBe('Título customizado')
        ->and($seo->description)->toBe('Descrição customizada')
        ->and($seo->canonical)->toBe('https://od.tech/customizada')
        ->and($seo->robots)->toBe('noindex,nofollow');
});

test('forService generates title, description and canonical for the service page', function () {
    $service = Service::factory()->create([
        'title' => 'Criação de Sites',
        'description' => 'Sites rápidos e responsivos para empresas de {cidade}.',
    ]);

    $seo = app(SeoMetaBuilder::class)->forService($service);

    expect($seo->title)->toBe('Criação de Sites — OD Tec')
        ->and($seo->description)->toBe('Sites rápidos e responsivos para empresas de sua cidade.')
        ->and($seo->canonical)->toBe(route('services.show', $service))
        ->and($seo->robots)->toBe('index,follow');
});

test('forCity generates title, description and canonical for the city page', function () {
    $city = City::factory()->create(['name' => 'Bauru', 'uf' => 'SP', 'intro' => 'Bauru é um polo tecnológico da região.']);

    $seo = app(SeoMetaBuilder::class)->forCity($city);

    expect($seo->title)->toBe('Tecnologia em Bauru/SP — OD Tec')
        ->and($seo->description)->toBe('Bauru é um polo tecnológico da região.')
        ->and($seo->canonical)->toBe(route('cities.show', $city))
        ->and($seo->robots)->toBe('index,follow');
});
