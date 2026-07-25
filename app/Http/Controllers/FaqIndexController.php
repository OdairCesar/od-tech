<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\Seo\ContentComposer;
use App\Services\Seo\StructuredDataService;
use Illuminate\Contracts\View\View;

class FaqIndexController extends Controller
{
    public function __construct(
        private readonly ContentComposer $composer,
        private readonly StructuredDataService $structuredData,
    ) {}

    public function index(): View
    {
        $groups = Service::query()->active()->orderBy('name')->get()
            ->map(fn (Service $service): array => [
                'title' => $service->title,
                'faq' => $this->composer->composeFaq($service->faq),
            ])
            ->filter(fn (array $group): bool => $group['faq'] !== [])
            ->values();

        $breadcrumbs = [
            ['label' => 'Início', 'url' => route('home')],
            ['label' => 'Perguntas frequentes'],
        ];

        $allFaq = $groups->flatMap(fn (array $group): array => $group['faq'])->all();

        $jsonLd = array_values(array_filter([
            $this->structuredData->faqPage($allFaq),
            $this->structuredData->breadcrumbList($breadcrumbs),
        ]));

        return view('pages.faq.index', [
            'groups' => $groups,
            'breadcrumbs' => $breadcrumbs,
            'jsonLd' => $jsonLd,
        ]);
    }
}
