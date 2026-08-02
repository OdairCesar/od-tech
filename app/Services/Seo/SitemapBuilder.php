<?php

namespace App\Services\Seo;

use App\Models\Category;
use App\Models\City;
use App\Models\LandingPage;
use App\Models\PortfolioItem;
use App\Models\Post;
use App\Models\Service;
use App\Models\ServiceCluster;
use App\Models\ServiceClusterLandingPage;
use App\Models\State;
use App\Services\Tools\ToolRegistry;
use Illuminate\Support\Facades\Cache;

final class SitemapBuilder
{
    public function __construct(private readonly ToolRegistry $toolRegistry) {}

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
            $this->url(route('states.index')),
            $this->url(route('blog.index')),
            $this->url(route('faq.index')),
            $this->url(route('portfolio.index')),
            $this->url(route('tools.index')),
        ];

        foreach ($this->toolRegistry->all() as $tool) {
            $urls[] = $this->url(route('tools.show', $tool->slug));
        }

        foreach (Service::query()->active()->get() as $service) {
            $urls[] = $this->url(route('services.show', $service), $service->updated_at?->toAtomString());
        }

        foreach (ServiceCluster::query()->published()->with('service')->get() as $cluster) {
            $urls[] = $this->url(route('services.clusters.show', [$cluster->service, $cluster]), $cluster->updated_at?->toAtomString());
        }

        foreach (ServiceClusterLandingPage::query()->published()->with('serviceCluster.service')->get() as $clusterLandingPage) {
            $urls[] = $this->url(
                route('services.clusters.show', [$clusterLandingPage->serviceCluster->service, $clusterLandingPage->slug]),
                $clusterLandingPage->updated_at?->toAtomString(),
            );
        }

        foreach (City::query()->active()->get() as $city) {
            $urls[] = $this->url(route('cities.show', $city), $city->updated_at?->toAtomString());
        }

        foreach (State::query()->active()->get() as $state) {
            $urls[] = $this->url(route('states.show', $state), $state->updated_at?->toAtomString());
        }

        foreach (PortfolioItem::query()->active()->get() as $portfolioItem) {
            $urls[] = $this->url(route('portfolio.show', $portfolioItem), $portfolioItem->updated_at?->toAtomString());
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
