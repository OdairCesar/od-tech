<?php

use App\Models\City;
use App\Models\Service;

test('home composes service subtitles generically, without leaking raw tokens', function () {
    Service::factory()->create(['subtitle' => 'Atendemos empresas de {cidade}/{uf}']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Atendemos empresas de sua cidade/sua região')
        ->assertDontSee('{cidade}', false);
});

test('services index composes subtitles generically', function () {
    Service::factory()->create(['subtitle' => 'Presença digital em {cidade}']);

    $this->get(route('services.index'))
        ->assertOk()
        ->assertSee('Presença digital em sua cidade')
        ->assertDontSee('{cidade}', false);
});

test('service show composes subtitle, description, benefits and faq generically', function () {
    $service = Service::factory()->create([
        'subtitle' => 'Para empresas de {cidade}',
        'description' => 'Atuamos em {cidade} e {regiao}.',
        'benefits' => ['Suporte local em {cidade}'],
        'faq' => [['question' => 'Atendem {cidade}?', 'answer' => 'Sim, atendemos {cidade}.']],
    ]);

    $this->get(route('services.show', $service))
        ->assertOk()
        ->assertSee('Para empresas de sua cidade')
        ->assertSee('Atuamos em sua cidade e sua região.')
        ->assertSee('Suporte local em sua cidade')
        ->assertSee('Atendem sua cidade?')
        ->assertDontSee('{cidade}', false)
        ->assertDontSee('{regiao}', false);
});

test('city show composes the service subtitle with the actual city context', function () {
    $service = Service::factory()->create(['subtitle' => 'Presença digital em {cidade}/{uf}']);
    $city = City::factory()->create(['name' => 'Bauru', 'uf' => 'SP']);

    $this->get(route('cities.show', $city))
        ->assertOk()
        ->assertSee('Presença digital em Bauru/SP')
        ->assertDontSee('{cidade}', false);
});
