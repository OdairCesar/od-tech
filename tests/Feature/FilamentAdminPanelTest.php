<?php

use App\Filament\Resources\Services\Pages\CreateService;
use App\Models\City;
use App\Models\ContactMessage;
use App\Models\LandingPage;
use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

test('guests are redirected to the admin login page', function () {
    auth()->logout();

    $this->get('/admin/services')->assertRedirect('/admin/login');
});

test('the services resource index page renders', function () {
    Service::factory()->count(2)->create();

    $this->get('/admin/services')->assertOk();
});

test('the cities resource index page renders', function () {
    City::factory()->count(2)->create();

    $this->get('/admin/cities')->assertOk();
});

test('the landing pages resource index page renders', function () {
    Service::factory()->create();
    City::factory()->create();

    expect(LandingPage::count())->toBeGreaterThan(0);

    $this->get('/admin/landing-pages')->assertOk();
});

test('the contact messages resource index page renders and has no create route', function () {
    ContactMessage::factory()->count(2)->create();

    $this->get('/admin/contact-messages')->assertOk();
    $this->get('/admin/contact-messages/create')->assertNotFound();
});

test('an editor cannot view contact messages', function () {
    $this->actingAs(User::factory()->create());

    ContactMessage::factory()->count(2)->create();

    $this->get('/admin/contact-messages')->assertForbidden();
});

test('creating a service through the resource form works end to end', function () {
    Livewire::test(CreateService::class)
        ->fillForm([
            'slug' => 'criacao-de-sites',
            'name' => 'Criação de sites',
            'title' => 'Criação de Sites',
            'subtitle' => 'Sites rápidos e profissionais',
            'description' => 'Criamos sites modernos e otimizados.',
            'benefits' => ['Rápido', 'Responsivo'],
            'faq' => [['question' => 'Quanto custa?', 'answer' => 'Depende do escopo.']],
            'keywords' => ['sites', 'landing pages'],
            'status' => 'published',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Service::where('slug', 'criacao-de-sites')->exists())->toBeTrue();
});
