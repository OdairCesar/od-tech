<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCluster;
use App\Models\ServiceClusterLandingPage;
use App\Services\Landing\ServiceClusterViewModelFactory;
use Illuminate\Contracts\View\View;

class ServiceClusterShowController extends Controller
{
    public function __construct(private readonly ServiceClusterViewModelFactory $viewModelFactory) {}

    public function show(Service $service, string $slug): View
    {
        $cluster = ServiceCluster::query()
            ->published()
            ->where('service_id', $service->id)
            ->where('slug', $slug)
            ->first();

        if ($cluster !== null) {
            return view('pages.services.show', ['vm' => $this->viewModelFactory->makeForCluster($cluster)]);
        }

        $pivot = ServiceClusterLandingPage::query()
            ->published()
            ->where('slug', $slug)
            ->whereHas('serviceCluster', fn ($query) => $query->where('service_id', $service->id))
            ->first();

        abort_unless($pivot, 404);

        return view('pages.landing.show', ['vm' => $this->viewModelFactory->makeForClusterCity($pivot)]);
    }
}
