<?php

use App\Enums\PageStatus;
use App\Enums\ServiceClusterStatus;
use App\Models\City;
use App\Models\Service;
use App\Models\ServiceCluster;
use App\Models\ServiceClusterLandingPage;

test('a published cluster page resolves under its parent service', function () {
    $service = Service::factory()->create();
    $cluster = ServiceCluster::factory()->create(['service_id' => $service->id]);

    $this->get(route('services.clusters.show', [$service, $cluster]))->assertOk();
});

test('a draft cluster page 404s on the public site', function () {
    $service = Service::factory()->create();
    $cluster = ServiceCluster::factory()->create(['service_id' => $service->id, 'status' => ServiceClusterStatus::Draft]);

    $this->get(route('services.clusters.show', [$service, $cluster]))->assertNotFound();
});

test('a cluster page 404s when accessed under a service it does not belong to', function () {
    $service = Service::factory()->create();
    $otherService = Service::factory()->create();
    $cluster = ServiceCluster::factory()->create(['service_id' => $service->id]);

    $this->get("/servicos/{$otherService->slug}/{$cluster->slug}")->assertNotFound();
});

test('a published cluster x city page resolves under its parent service and cluster', function () {
    $service = Service::factory()->create();
    $cluster = ServiceCluster::factory()->create(['service_id' => $service->id]);
    $city = City::factory()->create();

    $this->get(route('services.clusters.city.show', [$service, $cluster, $city]))->assertOk();
});

test('a cluster x city page 404s when the pivot row is not published', function () {
    $service = Service::factory()->create();
    $cluster = ServiceCluster::factory()->create(['service_id' => $service->id]);
    $city = City::factory()->create();

    ServiceClusterLandingPage::where('service_cluster_id', $cluster->id)
        ->where('city_id', $city->id)
        ->update(['status' => PageStatus::Draft]);

    $this->get(route('services.clusters.city.show', [$service, $cluster, $city]))->assertNotFound();
});
