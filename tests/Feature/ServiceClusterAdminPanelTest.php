<?php

use App\Filament\Resources\ServiceClusters\Pages\CreateServiceCluster;
use App\Filament\Resources\ServiceClusters\Pages\ListServiceClusters;
use App\Models\Service;
use App\Models\ServiceCluster;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Images\CreateResponse as ImageCreateResponse;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

test('the generate hero image action fills the hero_image field with an ai generated image', function () {
    Storage::fake('cloudinary');

    OpenAI::fake([
        ImageCreateResponse::fake([
            'data' => [['b64_json' => base64_encode('fake-image-bytes')]],
        ]),
    ]);

    $service = Service::factory()->create(['title' => 'Criação de Sites']);

    $path = Livewire::test(CreateServiceCluster::class)
        ->fillForm([
            'service_id' => $service->id,
            'title' => 'Loja Virtual',
            'subtitle' => 'Lojas rápidas e profissionais',
            'description' => 'Criamos lojas virtuais modernas e otimizadas.',
            'benefits' => ['Rápido', 'Responsivo'],
        ])
        ->callAction(TestAction::make('generateHeroImage')->schemaComponent('hero_image'))
        ->assertNotified()
        ->get('data.hero_image');

    expect($path)->not->toBeNull();

    Storage::disk('cloudinary')->assertExists($path);
});

test('the generate hero image action is disabled until a service and title are filled in', function () {
    $service = Service::factory()->create();

    Livewire::test(CreateServiceCluster::class)
        ->assertActionDisabled(TestAction::make('generateHeroImage')->schemaComponent('hero_image'))
        ->fillForm(['title' => 'Loja Virtual'])
        ->assertActionDisabled(TestAction::make('generateHeroImage')->schemaComponent('hero_image'))
        ->fillForm(['service_id' => $service->id])
        ->assertActionEnabled(TestAction::make('generateHeroImage')->schemaComponent('hero_image'));
});

test('the service clusters list can be filtered to only those without a hero image', function () {
    $withImage = ServiceCluster::factory()->create(['hero_image' => 'clusters/example-hero']);
    $withoutImage = ServiceCluster::factory()->create(['hero_image' => null]);

    Livewire::test(ListServiceClusters::class)
        ->assertCanSeeTableRecords([$withImage, $withoutImage])
        ->filterTable('hero_image', false)
        ->assertCanSeeTableRecords([$withoutImage])
        ->assertCanNotSeeTableRecords([$withImage]);
});
