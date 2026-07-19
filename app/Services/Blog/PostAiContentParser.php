<?php

namespace App\Services\Blog;

use App\Exceptions\AiGenerationException;
use Illuminate\Support\Str;

final class PostAiContentParser
{
    public function parse(string $jsonPayload): GeneratedPostContent
    {
        $data = json_decode($jsonPayload, associative: true);

        if (! is_array($data)) {
            throw AiGenerationException::invalidResponseShape();
        }

        $tags = $data['tags'] ?? null;

        if (! is_array($tags)) {
            throw AiGenerationException::invalidResponseShape();
        }

        return new GeneratedPostContent(
            title: $this->requireString($data, 'title'),
            excerpt: $this->requireString($data, 'excerpt'),
            contentHtml: Str::sanitizeHtml($this->requireString($data, 'content_html')),
            metaTitle: $this->requireString($data, 'meta_title'),
            metaDescription: $this->requireString($data, 'meta_description'),
            tags: array_values(array_map($this->requireStringValue(...), $tags)),
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
}
