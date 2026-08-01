<?php

namespace App\Actions\ServiceCluster;

use App\Models\City;
use App\Models\ServiceCluster;

final class GenerateClusterLandingSlug
{
    public function __invoke(ServiceCluster $cluster, City $city): string
    {
        return "{$cluster->slug}-em-{$city->slug}";
    }
}
