<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCluster;
use App\Services\Landing\ServiceClusterViewModelFactory;
use Illuminate\Contracts\View\View;

class ServiceClusterShowController extends Controller
{
    public function __construct(private readonly ServiceClusterViewModelFactory $viewModelFactory) {}

    public function show(Service $service, ServiceCluster $cluster): View
    {
        abort_unless($cluster->service_id === $service->id, 404);

        return view('pages.services.show', ['vm' => $this->viewModelFactory->makeForCluster($cluster)]);
    }
}
