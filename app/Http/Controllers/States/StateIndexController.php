<?php

namespace App\Http\Controllers\States;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Services\Seo\StructuredDataService;
use Illuminate\Contracts\View\View;

class StateIndexController extends Controller
{
    public function __construct(private readonly StructuredDataService $structuredData) {}

    public function index(): View
    {
        $breadcrumbs = [
            ['label' => 'Início', 'url' => route('home')],
            ['label' => 'Estados'],
        ];

        return view('pages.states.index', [
            'states' => State::query()->active()->orderBy('name')->get(),
            'breadcrumbs' => $breadcrumbs,
            'jsonLd' => [$this->structuredData->breadcrumbList($breadcrumbs)],
        ]);
    }
}
