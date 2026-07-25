<?php

use App\Enums\PageStatus;
use App\Models\Service;

test('faq page lists questions from published services', function () {
    $service = Service::factory()->create([
        'title' => 'Criação de sites',
        'status' => PageStatus::Published,
        'faq' => [
            ['question' => 'Quanto custa um site?', 'answer' => 'Depende do escopo do projeto.'],
        ],
    ]);

    $this->get(route('faq.index'))
        ->assertOk()
        ->assertSee($service->title)
        ->assertSee('Quanto custa um site?');
});

test('faq page excludes questions from draft services', function () {
    Service::factory()->create([
        'status' => PageStatus::Draft,
        'faq' => [
            ['question' => 'Pergunta de serviço rascunho?', 'answer' => 'Resposta.'],
        ],
    ]);

    $this->get(route('faq.index'))
        ->assertOk()
        ->assertDontSee('Pergunta de serviço rascunho?');
});

test('faq page omits services with an empty faq list', function () {
    $service = Service::factory()->create([
        'title' => 'Serviço sem perguntas',
        'status' => PageStatus::Published,
        'faq' => [],
    ]);

    $this->get(route('faq.index'))
        ->assertOk()
        ->assertDontSee('Sobre '.$service->title);
});
