<?php

use App\Mail\NewLeadReceived;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;

test('submitting the contact form creates a lead and sends a notification email', function () {
    Mail::fake();

    $response = $this->post(route('contact.store'), [
        'name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'phone' => '11999999999',
        'company' => 'Acme Ltda',
        'message' => 'Gostaria de um orçamento.',
    ]);

    $response->assertRedirect(route('contact.show'));

    $lead = Lead::sole();

    expect($lead->source)->toBe(Lead::SOURCE_CONTACT)
        ->and($lead->name)->toBe('Maria Silva')
        ->and($lead->email)->toBe('maria@example.com')
        ->and($lead->payload)->toBe(['company' => 'Acme Ltda']);

    Mail::assertSent(NewLeadReceived::class, fn (NewLeadReceived $mail): bool => $mail->lead->is($lead));
});

test('the contact form requires name, email and message', function () {
    $this->post(route('contact.store'), [])
        ->assertSessionHasErrors(['name', 'email', 'message']);

    expect(Lead::count())->toBe(0);
});

test('the contact form still succeeds when the notification email fails to send', function () {
    Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('SMTP down'));

    $response = $this->post(route('contact.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'Preciso de um orçamento.',
    ]);

    $response->assertRedirect(route('contact.show'));
    $response->assertSessionHas('status');

    expect(Lead::count())->toBe(1);
});
