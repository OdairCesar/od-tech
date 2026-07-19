<?php

namespace App\Services\Seo;

use App\Models\City;
use App\Models\LandingPage;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;

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
            'name' => 'OD Tec',
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
            'name' => "OD Tec - {$city->name}",
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
                'name' => 'OD Tec',
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
                'name' => 'OD Tec',
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
     * @return array<string, mixed>
     */
    public function blogPosting(Post $post): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->meta_description ?? strip_tags((string) $post->excerpt),
            'image' => $post->cover_image ? Storage::disk('cloudinary')->url($post->cover_image) : null,
            'datePublished' => $post->published_at?->toAtomString(),
            'dateModified' => $post->updated_at?->toAtomString(),
            'author' => [
                '@type' => 'Organization',
                'name' => 'OD Tec',
                'url' => route('home'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'OD Tec',
                'url' => route('home'),
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('blog.show', $post),
            ],
        ], fn (mixed $value): bool => $value !== null);
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
