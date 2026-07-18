<?php

namespace App\Observers;

use App\Actions\Landing\SyncLandingPagesForCity;
use App\Models\City;
use App\Services\Seo\SitemapBuilder;
use Illuminate\Support\Facades\Cache;

class CityObserver
{
    public function __construct(
        private readonly SyncLandingPagesForCity $syncLandingPagesForCity,
    ) {}

    public function created(City $city): void
    {
        ($this->syncLandingPagesForCity)($city);
    }

    public function saved(City $city): void
    {
        Cache::forget(SitemapBuilder::cacheKey());
    }
}
