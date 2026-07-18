<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\Seo\ContentComposer;
use App\Services\Seo\StructuredDataService;
use Illuminate\Contracts\View\View;

class ServiceIndexController extends Controller
{
    public function __construct(
        private readonly ContentComposer $composer,
        private readonly StructuredDataService $structuredData,
    ) {}

    public function index(): View
    {
        $services = Service::query()->active()->orderBy('name')->get()
            ->map(fn (Service $service): array => [
                'title' => $service->title,
                'subtitle' => $this->composer->compose($service->subtitle),
                'icon' => $service->icon,
                'url' => route('services.show', $service),
            ]);

        $breadcrumbs = [
            ['label' => 'Início', 'url' => route('home')],
            ['label' => 'Serviços'],
        ];

        return view('pages.services.index', [
            'services' => $services,
            'breadcrumbs' => $breadcrumbs,
            'jsonLd' => [$this->structuredData->breadcrumbList($breadcrumbs)],
        ]);
    }
}
