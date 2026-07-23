<?php

namespace App\Services\Ai;

use App\Exceptions\AiGenerationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;

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

    /**
     * Generates an image via OpenAI (gpt-image-1, which always returns
     * base64-encoded images) and stores it on the cloudinary disk, returning
     * the stored path.
     */
    public function generate(string $prompt, string $filenameSuffix): string
    {
        $response = OpenAI::images()->create([
            'model' => config('services.openai.image_model'),
            'prompt' => $prompt.' '.self::BRAND_DIRECTION,
            'n' => 1,
            'size' => config('services.openai.image_size'),
            'quality' => config('services.openai.image_quality'),
        ]);

        $base64 = $response->data[0]->b64_json ?? null;

        if (! is_string($base64) || $base64 === '') {
            throw AiGenerationException::invalidImageResponse();
        }

        $contents = base64_decode($base64, strict: true);

        if ($contents === false) {
            throw AiGenerationException::invalidImageResponse();
        }

        $path = Str::ulid().'-'.$filenameSuffix;

        Storage::disk('cloudinary')->put($path, $contents);

        return $path;
    }
}
