<?php

namespace App\Services\Portfolio;

use App\Models\PortfolioItem;
use App\Services\Seo\SeoMetaBuilder;
use App\Services\Seo\StructuredDataService;
use App\ViewModels\PortfolioItemViewModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

final readonly class PortfolioItemViewModelFactory
{
    public function __construct(
        private SeoMetaBuilder $seoMetaBuilder,
        private StructuredDataService $structuredData,
    ) {}

    public function makeShow(PortfolioItem $portfolioItem): PortfolioItemViewModel
    {
        $breadcrumbs = [
            ['label' => 'Início', 'url' => route('home')],
            ['label' => 'Portfólio', 'url' => route('portfolio.index')],
            ['label' => $portfolioItem->title],
        ];

        $relatedItems = PortfolioItem::query()
            ->active()
            ->with('service')
            ->when($portfolioItem->service_id, fn (Builder $query): Builder => $query->where('service_id', $portfolioItem->service_id))
            ->whereKeyNot($portfolioItem->id)
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (PortfolioItem $related): array => $this->teaser($related))
            ->all();

        $jsonLd = [$this->structuredData->breadcrumbList($breadcrumbs)];

        return new PortfolioItemViewModel(
            title: $portfolioItem->title,
            excerpt: $portfolioItem->excerpt,
            content: $portfolioItem->content,
            coverImageUrl: $this->coverImageUrl($portfolioItem),
            externalUrl: $portfolioItem->external_url,
            serviceName: $portfolioItem->service?->title,
            serviceUrl: $portfolioItem->service ? route('services.show', $portfolioItem->service) : null,
            relatedItems: $relatedItems,
            seo: $this->seoMetaBuilder->forPortfolioItem($portfolioItem),
            breadcrumbs: $breadcrumbs,
            jsonLd: $jsonLd,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function teaser(PortfolioItem $portfolioItem): array
    {
        return [
            'title' => $portfolioItem->title,
            'excerpt' => $portfolioItem->excerpt,
            'url' => route('portfolio.show', $portfolioItem),
            'coverImageUrl' => $this->coverImageUrl($portfolioItem),
            'serviceName' => $portfolioItem->service?->title,
        ];
    }

    private function coverImageUrl(PortfolioItem $portfolioItem): ?string
    {
        return $portfolioItem->cover_image ? Storage::disk('cloudinary')->url($portfolioItem->cover_image) : null;
    }
}
