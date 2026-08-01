<?php

namespace App\Services\ServiceCluster;

use App\Exceptions\AiGenerationException;

final class ServiceClusterAiContentParser
{
    public function parse(string $jsonPayload): GeneratedServiceClusterContent
    {
        $data = json_decode($jsonPayload, associative: true);

        if (! is_array($data)) {
            throw AiGenerationException::invalidResponseShape();
        }

        $benefits = $data['benefits'] ?? null;
        $faq = $data['faq'] ?? null;
        $keywords = $data['keywords'] ?? null;

        if (! is_array($benefits) || ! is_array($faq) || ! is_array($keywords)) {
            throw AiGenerationException::invalidResponseShape();
        }

        return new GeneratedServiceClusterContent(
            title: $this->requireString($data, 'title'),
            subtitle: $this->requireString($data, 'subtitle'),
            description: $this->requireString($data, 'description'),
            benefits: array_values(array_map($this->requireStringValue(...), $benefits)),
            faq: array_values(array_map($this->requireFaqItem(...), $faq)),
            keywords: array_values(array_map($this->requireStringValue(...), $keywords)),
            metaTitle: $this->requireString($data, 'meta_title'),
            metaDescription: $this->requireString($data, 'meta_description'),
            imagePrompt: $this->requireString($data, 'image_prompt'),
        );
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private function requireString(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        if (! is_string($value) || $value === '') {
            throw AiGenerationException::invalidResponseShape();
        }

        return $value;
    }

    private function requireStringValue(mixed $value): string
    {
        if (! is_string($value) || $value === '') {
            throw AiGenerationException::invalidResponseShape();
        }

        return $value;
    }

    /**
     * @return array{question: string, answer: string}
     */
    private function requireFaqItem(mixed $value): array
    {
        if (! is_array($value)) {
            throw AiGenerationException::invalidResponseShape();
        }

        return [
            'question' => $this->requireString($value, 'question'),
            'answer' => $this->requireString($value, 'answer'),
        ];
    }
}
