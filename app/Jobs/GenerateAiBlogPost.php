<?php

namespace App\Jobs;

use App\Actions\Blog\GenerateUniquePostSlug;
use App\Enums\PostStatus;
use App\Exceptions\AiGenerationException;
use App\Models\City;
use App\Models\Post;
use App\Services\Blog\PostAiBrief;
use App\Services\Blog\PostAiBriefPromptBuilder;
use App\Services\Blog\PostAiContentParser;
use App\Services\Blog\PostCoverImageGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;
use Throwable;

class GenerateAiBlogPost implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [10, 30, 60];

    public int $timeout = 300;

    public function __construct(public readonly Post $post) {}

    public function handle(
        PostAiBriefPromptBuilder $promptBuilder,
        PostAiContentParser $parser,
        GenerateUniquePostSlug $generateSlug,
        PostCoverImageGenerator $coverImageGenerator,
    ): void {
        $brief = PostAiBrief::fromArray($this->post->ai_brief ?? []);
        $city = $brief->cityId ? City::query()->find($brief->cityId) : null;

        try {
            $messages = $promptBuilder->build($brief, $city);

            $response = OpenAI::chat()->create([
                'model' => config('services.openai.model'),
                'messages' => [
                    ['role' => 'system', 'content' => $messages['system']],
                    ['role' => 'user', 'content' => $messages['user']],
                ],
                'response_format' => $promptBuilder->responseFormat(),
                'temperature' => 0.7,
            ]);

            $generated = $parser->parse($response->choices[0]->message->content ?? '');
        } catch (AiGenerationException $exception) {
            $this->markFailed($exception);

            return;
        }

        $coverImage = null;

        try {
            $coverImage = $coverImageGenerator->generate($generated->imagePrompt, $brief->imageStyle);
        } catch (Throwable $exception) {
            report($exception);
        }

        $title = $this->post->title ?: $generated->title;

        $this->post->update([
            'title' => $title,
            'slug' => $generateSlug($title, ignoreId: $this->post->id),
            'excerpt' => $generated->excerpt,
            'content' => $generated->contentHtml,
            'cover_image' => $coverImage,
            'tags' => $generated->tags,
            'meta_title' => $generated->metaTitle,
            'meta_description' => $generated->metaDescription,
            'ai_model' => $response->model,
            'status' => PostStatus::Draft,
            'ai_error' => null,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        $this->markFailed($exception);
    }

    private function markFailed(?Throwable $exception): void
    {
        $this->post->update([
            'status' => PostStatus::Failed,
            'ai_error' => $exception ? Str::limit($exception->getMessage(), 2000) : null,
        ]);
    }
}
