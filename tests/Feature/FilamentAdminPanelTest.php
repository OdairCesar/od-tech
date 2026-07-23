<?php

use App\Enums\PageStatus;
use App\Filament\Resources\LandingPages\Pages\ListLandingPages;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Jobs\RegenerateLandingPages;
use App\Models\City;
use App\Models\LandingPage;
use App\Models\Lead;
use App\Models\Service;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Images\CreateResponse as ImageCreateResponse;

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

test('the leads resource index page renders and has no create route', function () {
    Lead::factory()->count(2)->create();

    $this->get('/admin/leads')->assertOk();
    $this->get('/admin/leads/create')->assertNotFound();
});

test('an editor cannot view leads', function () {
    $this->actingAs(User::factory()->create());

    Lead::factory()->count(2)->create();

    $this->get('/admin/leads')->assertForbidden();
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

test('the services resource index page renders when a service has a hero image on the cloudinary disk', function () {
    Service::factory()->create(['hero_image' => 'services/example-hero']);

    $this->get('/admin/services')->assertOk();
});

test('the generate hero image action fills the hero_image field with an ai generated image', function () {
    Storage::fake('cloudinary');

    OpenAI::fake([
        ImageCreateResponse::fake([
            'data' => [['b64_json' => base64_encode('fake-image-bytes')]],
        ]),
    ]);

    $path = Livewire::test(CreateService::class)
        ->fillForm([
            'title' => 'Criação de Sites',
            'subtitle' => 'Sites rápidos e profissionais',
            'description' => 'Criamos sites modernos e otimizados.',
            'benefits' => ['Rápido', 'Responsivo'],
        ])
        ->callAction(TestAction::make('generateHeroImage')->schemaComponent('hero_image'))
        ->assertNotified()
        ->get('data.hero_image');

    expect($path)->not->toBeNull();

    Storage::disk('cloudinary')->assertExists($path);
});

test('the generate hero image action is disabled until a title is filled in', function () {
    Livewire::test(CreateService::class)
        ->assertActionDisabled(TestAction::make('generateHeroImage')->schemaComponent('hero_image'))
        ->fillForm(['title' => 'Criação de Sites'])
        ->assertActionEnabled(TestAction::make('generateHeroImage')->schemaComponent('hero_image'));
});

test('the services table view action only shows for published services', function () {
    $published = Service::factory()->create(['status' => PageStatus::Published]);
    $draft = Service::factory()->create(['status' => PageStatus::Draft]);

    Livewire::test(ListServices::class)
        ->assertTableActionVisible('view', $published)
        ->assertTableActionHidden('view', $draft);
});

test('the edit service page view action only shows for published services', function () {
    $published = Service::factory()->create(['status' => PageStatus::Published]);
    $draft = Service::factory()->create(['status' => PageStatus::Draft]);

    Livewire::test(EditService::class, ['record' => $published->getRouteKey()])
        ->assertActionVisible('view');

    Livewire::test(EditService::class, ['record' => $draft->getRouteKey()])
        ->assertActionHidden('view');
});

test('the regenerate all action dispatches the regeneration job and notifies the admin', function () {
    Bus::fake();

    Livewire::test(ListLandingPages::class)
        ->callAction('regenerateAll')
        ->assertNotified();

    Bus::assertDispatched(RegenerateLandingPages::class);
    expect(Cache::has(RegenerateLandingPages::LOCK_KEY))->toBeTrue();
});

test('the regenerate all action does not dispatch another job while one is already running', function () {
    Bus::fake();
    Cache::put(RegenerateLandingPages::LOCK_KEY, true, now()->addMinutes(30));

    Livewire::test(ListLandingPages::class)
        ->callAction('regenerateAll')
        ->assertNotified();

    Bus::assertNotDispatched(RegenerateLandingPages::class);
});
