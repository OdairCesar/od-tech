<?php

namespace App\Http\Controllers\Portfolio;

use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;
use App\Models\Service;
use App\Services\Portfolio\PortfolioItemViewModelFactory;
use App\Services\Seo\StructuredDataService;
use Illuminate\Contracts\View\View;

class PortfolioIndexController extends Controller
{
    public function __construct(
        private readonly PortfolioItemViewModelFactory $viewModelFactory,
        private readonly StructuredDataService $structuredData,
    ) {}

    public function index(?Service $service = null): View
    {
        $items = ($service?->portfolioItems() ?? PortfolioItem::query())
            ->active()
            ->with('service')
            ->latest()
            ->paginate(12)
            ->through(fn (PortfolioItem $item): array => $this->viewModelFactory->teaser($item));

        $services = Service::query()
            ->whereIn('id', PortfolioItem::query()->active()->select('service_id'))
            ->orderBy('name')
            ->get();

        $breadcrumbs = [
            ['label' => 'Início', 'url' => route('home')],
        ];

        $breadcrumbs[] = $service
            ? ['label' => 'Portfólio', 'url' => route('portfolio.index')]
            : ['label' => 'Portfólio'];

        if ($service) {
            $breadcrumbs[] = ['label' => $service->title];
        }

        return view('pages.portfolio.index', [
            'items' => $items,
            'services' => $services,
            'service' => $service,
            'breadcrumbs' => $breadcrumbs,
            'jsonLd' => [$this->structuredData->breadcrumbList($breadcrumbs)],
        ]);
    }
}
