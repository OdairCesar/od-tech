<?php

namespace App\Services\Seo;

use App\Models\Category;
use App\Models\City;
use App\Models\LandingPage;
use App\Models\Post;
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
            $this->url(route('blog.index')),
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

        foreach (Post::query()->published()->get() as $post) {
            $urls[] = $this->url(route('blog.show', $post), $post->updated_at?->toAtomString());
        }

        foreach (Category::query()->whereIn('id', Post::query()->published()->select('category_id'))->get() as $category) {
            $urls[] = $this->url(route('blog.category', $category));
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
