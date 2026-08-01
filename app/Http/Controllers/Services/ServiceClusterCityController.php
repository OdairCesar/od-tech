<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Service;
use App\Models\ServiceCluster;
use App\Models\ServiceClusterLandingPage;
use App\Services\Landing\ServiceClusterViewModelFactory;
use Illuminate\Contracts\View\View;

class ServiceClusterCityController extends Controller
{
    public function __construct(private readonly ServiceClusterViewModelFactory $viewModelFactory) {}

    public function show(Service $service, ServiceCluster $cluster, City $city): View
    {
        abort_unless($cluster->service_id === $service->id, 404);

        $pivot = ServiceClusterLandingPage::query()
            ->published()
            ->where('service_cluster_id', $cluster->id)
            ->where('city_id', $city->id)
            ->firstOrFail();

        return view('pages.landing.show', ['vm' => $this->viewModelFactory->makeForClusterCity($pivot)]);
    }
}
