<?php

namespace App\Services\Seo;

use App\Models\City;
use App\Models\LandingPage;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

final class SitemapBuilder
{
    /**
     * @return array<int, array{url: string, lastmod: ?string}>
     */
    public function urls(): array
    {
        return Cache::remember(self::cacheKey(), now()->addHour(), fn (): array => $this->build());
    }

    public static function cacheKey(): string
    {
        return 'sitemap-urls';
    }

    /**
     * @return array<int, array{url: string, lastmod: ?string}>
     */
    private function build(): array
    {
        $urls = [
            $this->url(route('home')),
            $this->url(route('about')),
            $this->url(route('contact.show')),
            $this->url(route('services.index')),
            $this->url(route('cities.index')),
        ];

        foreach (Service::query()->active()->get() as $service) {
            $urls[] = $this->url(route('services.show', $service), $service->updated_at?->toAtomString());
        }

        foreach (City::query()->active()->get() as $city) {
            $urls[] = $this->url(route('cities.show', $city), $city->updated_at?->toAtomString());
        }

        foreach (LandingPage::query()->published()->get() as $landingPage) {
            $urls[] = $this->url(route('landing.show', $landingPage), $landingPage->updated_at?->toAtomString());
        }

        return $urls;
    }

    /**
     * @return array{url: string, lastmod: ?string}
     */
    private function url(string $url, ?string $lastmod = null): array
    {
        return ['url' => $url, 'lastmod' => $lastmod];
    }
}
