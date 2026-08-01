<?php

namespace App\Services\Seo;

use App\Models\City;
use App\Models\LandingPage;
use App\Models\PortfolioItem;
use App\Models\Post;
use App\Models\Service;
use App\Models\ServiceCluster;
use App\Models\ServiceClusterLandingPage;
use App\Models\State;
use App\ViewModels\SeoMeta;
use Illuminate\Support\Str;

final readonly class SeoMetaBuilder
{
    public function __construct(private ContentComposer $composer) {}

    public function forLandingPage(LandingPage $landingPage): SeoMeta
    {
        $service = $landingPage->service;
        $city = $landingPage->city;

        $description = $landingPage->meta_description !== null
            ? $this->composer->compose($landingPage->meta_description, $city)
            : Str::limit($this->composer->compose($service->description, $city), 155);

        return new SeoMeta(
            title: $this->composer->compose($landingPage->meta_title ?? "{$service->title} em {$city->name} | OD Tec", $city),
            description: $description,
            canonical: $landingPage->canonical ?? route('landing.show', $landingPage),
            robots: $landingPage->robots ?? 'index,follow',
        );
    }

    public function forService(Service $service): SeoMeta
    {
        return new SeoMeta(
            title: "{$service->title} — OD Tec",
            description: Str::limit($this->composer->compose($service->description), 155),
            canonical: route('services.show', $service),
            robots: 'index,follow',
        );
    }

    public function forServiceCluster(ServiceCluster $cluster): SeoMeta
    {
        $description = $cluster->meta_description !== null
            ? $this->composer->compose($cluster->meta_description)
            : Str::limit($this->composer->compose((string) $cluster->description), 155);

        return new SeoMeta(
            title: $this->composer->compose($cluster->meta_title ?? "{$cluster->title} — OD Tec"),
            description: $description,
            canonical: $cluster->canonical ?? route('services.clusters.show', [$cluster->service, $cluster]),
            robots: $cluster->robots ?? 'index,follow',
        );
    }

    public function forServiceClusterLandingPage(ServiceClusterLandingPage $pivot): SeoMeta
    {
        $cluster = $pivot->serviceCluster;
        $city = $pivot->city;

        $description = $pivot->meta_description !== null
            ? $this->composer->compose($pivot->meta_description, $city)
            : Str::limit($this->composer->compose((string) $cluster->description, $city), 155);

        $defaultTitle = $this->composer->hasLocationTokens((string) $cluster->title)
            ? "{$cluster->title} | OD Tec"
            : "{$cluster->title} em {$city->name} | OD Tec";

        return new SeoMeta(
            title: $this->composer->compose($pivot->meta_title ?? $defaultTitle, $city),
            description: $description,
            canonical: $pivot->canonical ?? route('services.clusters.show', [$cluster->service, $pivot->slug]),
            robots: $pivot->robots ?? 'index,follow',
        );
    }

    public function forCity(City $city): SeoMeta
    {
        return new SeoMeta(
            title: "Tecnologia em {$city->name}/{$city->state->uf} — OD Tec",
            description: Str::limit($city->intro, 155),
            canonical: route('cities.show', $city),
            robots: 'index,follow',
        );
    }

    public function forState(State $state): SeoMeta
    {
        return new SeoMeta(
            title: $state->meta_title ?? "Tecnologia em {$state->name} — OD Tec",
            description: $state->meta_description ?? Str::limit($state->intro, 155),
            canonical: $state->canonical ?? route('states.show', $state),
            robots: $state->robots ?? 'index,follow',
        );
    }

    public function forPortfolioItem(PortfolioItem $portfolioItem): SeoMeta
    {
        return new SeoMeta(
            title: $portfolioItem->meta_title ?? "{$portfolioItem->title} — Portfólio OD Tec",
            description: $portfolioItem->meta_description ?? Str::limit($portfolioItem->excerpt, 155),
            canonical: $portfolioItem->canonical ?? route('portfolio.show', $portfolioItem),
            robots: $portfolioItem->robots ?? 'index,follow',
        );
    }

    public function forPost(Post $post): SeoMeta
    {
        return new SeoMeta(
            title: $post->meta_title ?? "{$post->title} — Blog OD Tec",
            description: $post->meta_description ?? Str::limit(strip_tags($post->excerpt ?? ''), 155),
            canonical: $post->canonical ?? route('blog.show', $post),
            robots: $post->robots ?? 'index,follow',
        );
    }
}
