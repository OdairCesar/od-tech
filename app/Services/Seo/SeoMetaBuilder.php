<?php

namespace App\Services\Seo;

use App\Models\City;
use App\Models\LandingPage;
use App\Models\Post;
use App\Models\Service;
use App\ViewModels\SeoMeta;
use Illuminate\Support\Str;

final readonly class SeoMetaBuilder
{
    public function __construct(private ContentComposer $composer) {}

    public function forLandingPage(LandingPage $landingPage): SeoMeta
    {
        $service = $landingPage->service;
        $city = $landingPage->city;

        $description = $landingPage->meta_description
            ?? Str::limit($this->composer->compose($service->description, $city), 155);

        return new SeoMeta(
            title: $landingPage->meta_title ?? "{$service->title} em {$city->name} | OD Tec",
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

    public function forCity(City $city): SeoMeta
    {
        return new SeoMeta(
            title: "Tecnologia em {$city->name}/{$city->uf} — OD Tec",
            description: Str::limit($city->intro, 155),
            canonical: route('cities.show', $city),
            robots: 'index,follow',
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
