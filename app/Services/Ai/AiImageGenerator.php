<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class AiImageGenerator
{
    /**
     * Always applied so every generated image matches OD Tec's site identity,
     * regardless of what the caller's prompt describes.
     */
    private const BRAND_DIRECTION = 'The image must align with the visual identity of a modern Brazilian B2B '
        .'technology company website: clean and uncluttered composition, a color palette dominated by blue '
        .'(#2563eb) accents balanced with white and neutral slate/gray tones, contemporary and trustworthy, '
        .'polished and professional, no gimmicky stock-photo clichés.';

    public function __construct(private readonly ImageProvider $provider) {}

    /**
     * Generates an image via the configured AI provider and stores it on the
     * cloudinary disk, returning the stored path.
     */
    public function generate(string $prompt, string $filenameSuffix): string
    {
        $contents = $this->provider->generateImageBytes($prompt.' '.self::BRAND_DIRECTION);

        $path = Str::ulid().'-'.$filenameSuffix;

        Storage::disk('cloudinary')->put($path, $contents);

        return $path;
    }
}
