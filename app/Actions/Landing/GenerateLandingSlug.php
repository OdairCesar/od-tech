<?php

namespace App\Actions\Landing;

use App\Models\City;
use App\Models\Service;

final class GenerateLandingSlug
{
    public function __invoke(Service $service, City $city): string
    {
        return "{$service->slug}-em-{$city->slug}";
    }
}
