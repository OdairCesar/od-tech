<?php

namespace App\Http\Controllers\Cities;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Services\Seo\StructuredDataService;
use Illuminate\Contracts\View\View;

class CityIndexController extends Controller
{
    public function __construct(private readonly StructuredDataService $structuredData) {}

    public function index(): View
    {
        $breadcrumbs = [
            ['label' => 'Início', 'url' => route('home')],
            ['label' => 'Cidades'],
        ];

        return view('pages.cities.index', [
            'cities' => City::query()->active()->orderByDesc('population')->get(),
            'breadcrumbs' => $breadcrumbs,
            'jsonLd' => [$this->structuredData->breadcrumbList($breadcrumbs)],
        ]);
    }
}
