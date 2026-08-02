<?php

namespace App\Services\Landing;

use App\Services\Ai\AiImageGenerator;

final class ServiceClusterHeroImageGenerator
{
    public function __construct(private readonly AiImageGenerator $generator) {}

    /**
     * @param  array<int, string>  $benefits
     */
    public function generate(string $serviceName, string $title, ?string $subtitle, ?string $description, array $benefits): string
    {
        return $this->generator->generate($this->buildPrompt($serviceName, $title, $subtitle, $description, $benefits), 'hero');
    }

    /**
     * @param  array<int, string>  $benefits
     */
    private function buildPrompt(string $serviceName, string $title, ?string $subtitle, ?string $description, array $benefits): string
    {
        $parts = [
            "Hero illustration for \"{$title}\", a specific sub-topic of the B2B technology service \"{$serviceName}\".",
        ];

        if (filled($subtitle)) {
            $parts[] = $subtitle;
        }

        if (filled($description)) {
            $parts[] = $description;
        }

        if ($benefits !== []) {
            $parts[] = 'Key benefits to convey visually: '.implode(', ', $benefits).'.';
        }

        $parts[] = 'Wide hero image with visual breathing room on one side for overlaid text, no embedded text or logos in the image itself.';

        return implode(' ', $parts);
    }
}
