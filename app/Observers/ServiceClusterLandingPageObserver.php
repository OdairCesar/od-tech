<?php

namespace App\Observers;

use App\Models\ServiceClusterLandingPage;
use App\Services\Seo\SitemapBuilder;
use Illuminate\Support\Facades\Cache;

class ServiceClusterLandingPageObserver
{
    public function saved(ServiceClusterLandingPage $serviceClusterLandingPage): void
    {
        Cache::forget(SitemapBuilder::cacheKey());
    }
}
