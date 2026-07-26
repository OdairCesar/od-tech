<?php

use App\Livewire\ConsultationChat;

test('the consultation page resolves and renders the chat component', function () {
    $this->get(route('consultation.show'))
        ->assertOk()
        ->assertSeeLivewire(ConsultationChat::class);
});

test('the consultation page uses the minimal chat layout without the regular site header and footer', function () {
    $response = $this->get(route('consultation.show'));

    $response->assertOk()
        ->assertSee('Ver site')
        ->assertDontSee('Fale com a gente');
});
