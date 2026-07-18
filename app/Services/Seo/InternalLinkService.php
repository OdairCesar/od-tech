<?php

namespace App\Services\Seo;

use App\Models\City;
use App\Models\LandingPage;

final class InternalLinkService
{
    public function __construct(private readonly int $limit = 6) {}

    /**
     * @return array<int, array{label: string, url: string}>
     */
    public function otherCitiesForService(LandingPage $landingPage): array
    {
        $city = $landingPage->city;
        $sameRegionCityIds = City::query()->sameRegionAs($city)->pluck('id');

        $candidates = $landingPage->service->landingPages()
            ->published()
            ->where('city_id', '!=', $city->id)
            ->with('city')
            ->get()
            ->sortByDesc(fn (LandingPage $candidate): bool => $sameRegionCityIds->contains($candidate->city_id))
            ->take($this->limit);

        return $candidates
            ->map(fn (LandingPage $candidate): array => [
                'label' => $candidate->city->name,
                'url' => route('landing.show', $candidate),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, url: string}>
     */
    public function otherServicesForCity(LandingPage $landingPage): array
    {
        $candidates = $landingPage->city->landingPages()
            ->published()
            ->where('service_id', '!=', $landingPage->service_id)
            ->with('service')
            ->take($this->limit)
            ->get();

        return $candidates
            ->map(fn (LandingPage $candidate): array => [
                'label' => $candidate->service->title,
                'url' => route('landing.show', $candidate),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, url: string}>
     */
    public function relatedLinks(LandingPage $landingPage): array
    {
        return [
            ...$this->otherCitiesForService($landingPage),
            ...$this->otherServicesForCity($landingPage),
        ];
    }
}
