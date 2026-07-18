<?php

namespace Database\Factories;

use App\Actions\Landing\GenerateLandingSlug;
use App\Enums\PageStatus;
use App\Models\City;
use App\Models\LandingPage;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LandingPage>
 */
class LandingPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'city_id' => City::factory(),
            'meta_title' => null,
            'meta_description' => null,
            'canonical' => null,
            'robots' => 'index,follow',
            'custom_h1' => null,
            'custom_subtitle' => null,
            'custom_intro' => null,
            'custom_cta' => null,
            'status' => PageStatus::Published,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (LandingPage $landingPage): void {
            $landingPage->slug = app(GenerateLandingSlug::class)(
                $landingPage->service ?? Service::find($landingPage->service_id),
                $landingPage->city ?? City::find($landingPage->city_id),
            );
        });
    }
}
