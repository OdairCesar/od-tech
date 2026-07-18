<?php

use App\Enums\PageStatus;
use App\Models\City;
use App\Models\LandingPage;
use App\Models\Service;

test('sitemap.xml lists static pages and published services, cities and landing pages', function () {
    $service = Service::factory()->create(['status' => PageStatus::Published]);
    $city = City::factory()->create(['status' => PageStatus::Published]);
    $landingPage = LandingPage::where('service_id', $service->id)->where('city_id', $city->id)->sole();

    $response = $this->get(route('sitemap'))->assertOk();
    $response->assertHeader('Content-Type', 'application/xml');

    $response->assertSee(route('home'), false)
        ->assertSee(route('services.index'), false)
        ->assertSee(route('cities.index'), false)
        ->assertSee(route('services.show', $service), false)
        ->assertSee(route('cities.show', $city), false)
        ->assertSee(route('landing.show', $landingPage), false);
});

test('sitemap.xml excludes draft services, cities and landing pages', function () {
    $draftService = Service::factory()->create(['status' => PageStatus::Draft]);
    $draftCity = City::factory()->create(['status' => PageStatus::Draft]);

    $response = $this->get(route('sitemap'))->assertOk();

    $response->assertDontSee($draftService->slug, false)
        ->assertDontSee($draftCity->slug, false);
});

test('sitemap.xml reflects new published records after the cache is invalidated by the observers', function () {
    $this->get(route('sitemap'))->assertOk();

    $city = City::factory()->create(['name' => 'Nova Cidade', 'status' => PageStatus::Published]);

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee(route('cities.show', $city), false);
});

test('robots.txt disallows the admin panel and points to the sitemap', function () {
    $response = $this->get(route('robots'))->assertOk();
    $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');

    $response->assertSee('Disallow: /admin')
        ->assertSee('Sitemap: '.route('sitemap'));
});
