<?php

namespace App\Services\Seo;

use App\Models\City;
use App\Models\LandingPage;
use App\Models\Service;

final class StructuredDataService
{
    /**
     * @return array<string, mixed>
     */
    public function organization(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'OD Tech',
            'url' => route('home'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function localBusiness(City $city): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => "OD Tech - {$city->name}",
            'url' => route('cities.show', $city),
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $city->name,
                'addressRegion' => $city->uf,
                'addressCountry' => 'BR',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function service(LandingPage $landingPage): array
    {
        $service = $landingPage->service;
        $city = $landingPage->city;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => "{$service->title} em {$city->name}",
            'description' => $service->subtitle,
            'areaServed' => [
                '@type' => 'City',
                'name' => $city->name,
            ],
            'provider' => [
                '@type' => 'Organization',
                'name' => 'OD Tech',
                'url' => route('home'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function serviceGeneric(Service $service, string $description): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service->title,
            'description' => $description,
            'provider' => [
                '@type' => 'Organization',
                'name' => 'OD Tech',
                'url' => route('home'),
            ],
        ];
    }

    /**
     * @param  array<int, array{question: string, answer: string}>  $faq
     * @return array<string, mixed>|null
     */
    public function faqPage(array $faq): ?array
    {
        if ($faq === []) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn (array $item): array => [
                '@type' => 'Question',
                'name' => $item['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['answer'],
                ],
            ], $faq),
        ];
    }

    /**
     * @param  array<int, array{label: string, url?: string}>  $items
     * @return array<string, mixed>
     */
    public function breadcrumbList(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn (array $item, int $index): array => array_filter([
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['label'],
                'item' => $item['url'] ?? null,
            ], fn (mixed $value): bool => $value !== null))->all(),
        ];
    }
}
