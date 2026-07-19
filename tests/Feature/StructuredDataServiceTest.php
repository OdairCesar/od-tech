<?php

use App\Models\City;
use App\Models\LandingPage;
use App\Models\Service;
use App\Services\Seo\StructuredDataService;

beforeEach(function () {
    $this->service = new StructuredDataService;
});

test('organization returns a schema.org Organization block', function () {
    $data = $this->service->organization();

    expect($data['@type'])->toBe('Organization')
        ->and($data['name'])->toBe('OD Tec')
        ->and($data['url'])->toBe(route('home'));
});

test('localBusiness returns a schema.org LocalBusiness block for the city', function () {
    $city = City::factory()->create(['name' => 'Bauru', 'uf' => 'SP']);

    $data = $this->service->localBusiness($city);

    expect($data['@type'])->toBe('LocalBusiness')
        ->and($data['address']['addressLocality'])->toBe('Bauru')
        ->and($data['address']['addressRegion'])->toBe('SP');
});

test('service returns a schema.org Service block tied to the landing page', function () {
    $service = Service::factory()->create(['title' => 'Criação de Sites']);
    $city = City::factory()->create(['name' => 'Bauru']);
    $landingPage = LandingPage::where('service_id', $service->id)->where('city_id', $city->id)->sole();

    $data = $this->service->service($landingPage);

    expect($data['@type'])->toBe('Service')
        ->and($data['name'])->toBe('Criação de Sites em Bauru')
        ->and($data['areaServed']['name'])->toBe('Bauru');
});

test('serviceGeneric returns a schema.org Service block without a city context', function () {
    $service = Service::factory()->create(['title' => 'Criação de Sites']);

    $data = $this->service->serviceGeneric($service, 'Presença digital para sua empresa.');

    expect($data['@type'])->toBe('Service')
        ->and($data['name'])->toBe('Criação de Sites')
        ->and($data['description'])->toBe('Presença digital para sua empresa.')
        ->and($data)->not->toHaveKey('areaServed');
});

test('faqPage returns null for an empty faq list', function () {
    expect($this->service->faqPage([]))->toBeNull();
});

test('faqPage maps question/answer pairs into schema.org Question entities', function () {
    $data = $this->service->faqPage([
        ['question' => 'Vocês atendem minha cidade?', 'answer' => 'Sim, atendemos.'],
    ]);

    expect($data['@type'])->toBe('FAQPage')
        ->and($data['mainEntity'][0]['@type'])->toBe('Question')
        ->and($data['mainEntity'][0]['name'])->toBe('Vocês atendem minha cidade?')
        ->and($data['mainEntity'][0]['acceptedAnswer']['text'])->toBe('Sim, atendemos.');
});

test('breadcrumbList numbers items by position and omits url on the last item', function () {
    $data = $this->service->breadcrumbList([
        ['label' => 'Início', 'url' => '/'],
        ['label' => 'Serviços', 'url' => '/servicos'],
        ['label' => 'Criação de Sites'],
    ]);

    expect($data['itemListElement'])->toHaveCount(3)
        ->and($data['itemListElement'][0]['position'])->toBe(1)
        ->and($data['itemListElement'][2]['position'])->toBe(3)
        ->and($data['itemListElement'][2])->not->toHaveKey('item');
});
