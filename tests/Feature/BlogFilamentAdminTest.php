<?php

use App\Enums\AudienceKnowledgeLevel;
use App\Enums\BrandPresence;
use App\Enums\ContentGoal;
use App\Enums\PostLength;
use App\Enums\PostStatus;
use App\Enums\WritingTone;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Posts\Pages\GenerateAiPost;
use App\Jobs\GenerateAiBlogPost;
use App\Models\AiBriefSuggestion;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

test('the categories resource index page renders', function () {
    Category::factory()->count(2)->create();

    $this->get('/admin/categories')->assertOk();
});

test('the posts resource index page renders', function () {
    Post::factory()->count(2)->create();

    $this->get('/admin/posts')->assertOk();
});

test('the posts resource index page renders when a post has a cover image on the cloudinary disk', function () {
    Post::factory()->create(['cover_image' => 'posts/example-cover']);

    $this->get('/admin/posts')->assertOk();
});

test('the generate ai post page renders', function () {
    $this->get('/admin/posts/generate')->assertOk();
});

test('creating a category through the resource form works end to end', function () {
    Livewire::test(CreateCategory::class)
        ->fillForm([
            'name' => 'Tecnologia',
            'slug' => 'tecnologia',
            'description' => 'Artigos sobre tecnologia.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Category::where('slug', 'tecnologia')->exists())->toBeTrue();
});

test('submitting the ai briefing form creates a generating post and dispatches the job', function () {
    Queue::fake();

    Livewire::test(GenerateAiPost::class)
        ->fillForm([
            'topic' => 'Como escolher um sistema para clínicas',
            'knowledge_level' => AudienceKnowledgeLevel::Intermediate->value,
            'goal' => ContentGoal::GenerateLeads->value,
            'brand_presence' => BrandPresence::Subtle->value,
            'length' => PostLength::Small->value,
            'tone' => WritingTone::Professional->value,
        ])
        ->call('generate');

    $post = Post::sole();

    expect($post->status)->toBe(PostStatus::Generating)
        ->and($post->ai_brief['topic'])->toBe('Como escolher um sistema para clínicas');

    Queue::assertPushed(GenerateAiBlogPost::class, fn (GenerateAiBlogPost $job): bool => $job->post->is($post));
});

test('the ai briefing form accepts a preset audience suggestion', function () {
    Queue::fake();

    Livewire::test(GenerateAiPost::class)
        ->fillForm([
            'topic' => 'Como escolher um sistema para clínicas',
            'target_audience' => 'Médico',
            'knowledge_level' => AudienceKnowledgeLevel::Intermediate->value,
            'goal' => ContentGoal::GenerateLeads->value,
            'brand_presence' => BrandPresence::Subtle->value,
            'length' => PostLength::Small->value,
            'tone' => WritingTone::Professional->value,
        ])
        ->call('generate');

    expect(Post::sole()->ai_brief['target_audience'])->toBe('Médico');
});

test('submitting the ai briefing form remembers new secondary keywords as future suggestions', function () {
    Queue::fake();

    Livewire::test(GenerateAiPost::class)
        ->fillForm([
            'topic' => 'Como escolher um sistema para clínicas',
            'secondary_keywords' => ['software para clínicas', 'sistema de gestão médica'],
            'knowledge_level' => AudienceKnowledgeLevel::Intermediate->value,
            'goal' => ContentGoal::GenerateLeads->value,
            'brand_presence' => BrandPresence::Subtle->value,
            'length' => PostLength::Small->value,
            'tone' => WritingTone::Professional->value,
        ])
        ->call('generate');

    expect(AiBriefSuggestion::query()->forField('secondary_keywords')->orderBy('value')->pluck('value')->all())
        ->toBe(['sistema de gestão médica', 'software para clínicas']);
});
