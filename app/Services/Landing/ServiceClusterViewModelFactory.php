<?php

namespace App\Services\Landing;

use App\Models\ServiceCluster;
use App\Models\ServiceClusterLandingPage;
use App\Services\Seo\ContentComposer;
use App\Services\Seo\InternalLinkService;
use App\Services\Seo\SeoMetaBuilder;
use App\Services\Seo\StructuredDataService;
use App\ViewModels\LandingPageViewModel;
use App\ViewModels\ServiceViewModel;
use Illuminate\Support\Facades\Storage;

final readonly class ServiceClusterViewModelFactory
{
    public function __construct(
        private ContentComposer $composer,
        private SeoMetaBuilder $seoMetaBuilder,
        private StructuredDataService $structuredData,
        private InternalLinkService $internalLinks,
    ) {}

    public function makeForCluster(ServiceCluster $cluster): ServiceViewModel
    {
        $cluster->loadMissing('service');
        $service = $cluster->service;
        $clusterTitle = (string) $cluster->title;

        $breadcrumbs = [
            ['label' => 'Início', 'url' => route('home')],
            ['label' => 'Serviços', 'url' => route('services.index')],
            ['label' => $service->title, 'url' => route('services.show', $service)],
            ['label' => $clusterTitle],
        ];

        $subtitle = $this->composer->compose((string) $cluster->subtitle);
        $faq = $this->composer->composeFaq($cluster->faq ?? []);

        $jsonLd = array_values(array_filter([
            $this->structuredData->serviceGeneric($service, $subtitle),
            $this->structuredData->faqPage($faq),
            $this->structuredData->breadcrumbList($breadcrumbs),
        ]));

        $heroImagePath = $cluster->hero_image ?? $service->hero_image;

        return new ServiceViewModel(
            title: $clusterTitle,
            subtitle: $subtitle,
            description: $this->composer->compose((string) $cluster->description),
            benefits: $this->composer->composeList($cluster->benefits ?? []),
            faq: $faq,
            seo: $this->seoMetaBuilder->forServiceCluster($cluster),
            heroImageUrl: $heroImagePath ? Storage::disk('cloudinary')->url($heroImagePath) : null,
            breadcrumbs: $breadcrumbs,
            relatedLinks: $this->internalLinks->otherClustersForService($cluster),
            jsonLd: $jsonLd,
        );
    }

    public function makeForClusterCity(ServiceClusterLandingPage $pivot): LandingPageViewModel
    {
        $pivot->loadMissing('serviceCluster.service', 'city');
        $cluster = $pivot->serviceCluster;
        $service = $cluster->service;
        $city = $pivot->city;
        $clusterTitle = (string) $cluster->title;

        $benefits = $this->composer->composeList($cluster->benefits ?? [], $city);
        $faq = $this->composer->composeFaq($cluster->faq ?? [], $city);

        $breadcrumbs = [
            ['label' => 'Início', 'url' => route('home')],
            ['label' => $service->title, 'url' => route('services.show', $service)],
            ['label' => $clusterTitle, 'url' => route('services.clusters.show', [$service, $cluster])],
            ['label' => $city->name],
        ];

        $jsonLd = array_values(array_filter([
            $this->structuredData->serviceForClusterCity($pivot),
            $this->structuredData->faqPage($faq),
            $this->structuredData->breadcrumbList($breadcrumbs),
        ]));

        return new LandingPageViewModel(
            h1: $pivot->custom_h1 ?? "{$clusterTitle} em {$city->name}",
            subtitle: $pivot->custom_subtitle ?? $this->composer->compose((string) $cluster->subtitle, $city),
            intro: $pivot->custom_intro ?? $this->composer->compose((string) $cluster->description, $city),
            benefits: $benefits,
            faq: $faq,
            ctaLabel: $pivot->custom_cta ?? 'Solicitar orçamento',
            seo: $this->seoMetaBuilder->forServiceClusterLandingPage($pivot),
            breadcrumbs: $breadcrumbs,
            relatedLinks: $this->internalLinks->relatedLinksForCluster($pivot),
            jsonLd: $jsonLd,
        );
    }
}
