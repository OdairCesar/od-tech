<?php

namespace App\Services\Landing;

use App\Models\City;
use App\Models\LandingPage;
use App\Models\Service;
use App\Services\Seo\ContentComposer;
use App\Services\Seo\SeoMetaBuilder;
use App\Services\Seo\StructuredDataService;
use App\ViewModels\CityViewModel;
use App\ViewModels\ServiceViewModel;

final readonly class ServiceCityViewModelFactory
{
    public function __construct(
        private ContentComposer $composer,
        private SeoMetaBuilder $seoMetaBuilder,
        private StructuredDataService $structuredData,
    ) {}

    public function makeForService(Service $service): ServiceViewModel
    {
        $breadcrumbs = [
            ['label' => 'Início', 'url' => route('home')],
            ['label' => 'Serviços', 'url' => route('services.index')],
            ['label' => $service->title],
        ];

        $subtitle = $this->composer->compose($service->subtitle);
        $faq = $this->composer->composeFaq($service->faq);

        $jsonLd = array_values(array_filter([
            $this->structuredData->serviceGeneric($service, $subtitle),
            $this->structuredData->faqPage($faq),
            $this->structuredData->breadcrumbList($breadcrumbs),
        ]));

        return new ServiceViewModel(
            title: $service->title,
            subtitle: $subtitle,
            description: $this->composer->compose($service->description),
            benefits: $this->composer->composeList($service->benefits),
            faq: $faq,
            seo: $this->seoMetaBuilder->forService($service),
            breadcrumbs: $breadcrumbs,
            jsonLd: $jsonLd,
        );
    }

    public function makeForCity(City $city): CityViewModel
    {
        $breadcrumbs = [
            ['label' => 'Início', 'url' => route('home')],
            ['label' => 'Cidades', 'url' => route('cities.index')],
            ['label' => $city->name],
        ];

        $landingPages = $city->landingPages()->published()->with('service')->get()
            ->map(fn (LandingPage $landingPage): array => [
                'label' => $landingPage->service->title,
                'subtitle' => $this->composer->compose($landingPage->service->subtitle, $city),
                'icon' => $landingPage->service->icon,
                'url' => route('landing.show', $landingPage),
            ])
            ->values()
            ->all();

        $jsonLd = array_values(array_filter([
            $this->structuredData->localBusiness($city),
            $this->structuredData->breadcrumbList($breadcrumbs),
        ]));

        return new CityViewModel(
            name: $city->name,
            uf: $city->uf,
            region: $city->region,
            intro: $city->intro,
            businessText: $city->business_text,
            landingPages: $landingPages,
            seo: $this->seoMetaBuilder->forCity($city),
            breadcrumbs: $breadcrumbs,
            jsonLd: $jsonLd,
        );
    }
}
