<?php

use App\Filament\Resources\Consultations\Pages\ListConsultations;
use App\Models\Consultation;
use App\Models\User;
use Livewire\Livewire;

test('admin can view, edit and delete consultations', function () {
    $admin = User::factory()->admin()->create();
    $consultation = Consultation::factory()->create();

    expect($admin->can('viewAny', Consultation::class))->toBeTrue()
        ->and($admin->can('view', $consultation))->toBeTrue()
        ->and($admin->can('update', $consultation))->toBeTrue()
        ->and($admin->can('delete', $consultation))->toBeTrue()
        ->and($admin->can('deleteAny', Consultation::class))->toBeTrue();

    $this->actingAs($admin);

    $this->get('/admin/consultations')->assertOk();
    $this->get("/admin/consultations/{$consultation->id}/edit")->assertOk();
});

test('admin sees the generated report rendered visually instead of as raw json', function () {
    $admin = User::factory()->admin()->create();
    $consultation = Consultation::factory()->withInitialAnswers()->completed()->create();

    $this->actingAs($admin);

    $this->get("/admin/consultations/{$consultation->id}/edit")
        ->assertOk()
        ->assertSee($consultation->report['executive_summary'])
        ->assertSee($consultation->report['mvp'])
        ->assertSee($consultation->report['delivery_stages'][0]['name'])
        ->assertSee($consultation->report['delivery_stages'][0]['timeframe'])
        ->assertDontSee('Equipe')
        ->assertDontSee(json_encode($consultation->report, JSON_UNESCAPED_UNICODE));
});

test('editor cannot view, edit or delete consultations', function () {
    $editor = User::factory()->create();
    $consultation = Consultation::factory()->create();

    expect($editor->can('viewAny', Consultation::class))->toBeFalse()
        ->and($editor->can('view', $consultation))->toBeFalse()
        ->and($editor->can('update', $consultation))->toBeFalse()
        ->and($editor->can('delete', $consultation))->toBeFalse()
        ->and($editor->can('deleteAny', Consultation::class))->toBeFalse();

    $this->actingAs($editor);

    $this->get("/admin/consultations/{$consultation->id}/edit")->assertForbidden();
});

test('nobody can create consultations through the admin, including admins', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->can('create', Consultation::class))->toBeFalse();

    $this->actingAs($admin);

    $this->get('/admin/consultations/create')->assertNotFound();
});

test('consultations can be bulk marked as read and unread', function () {
    $this->actingAs(User::factory()->admin()->create());

    $consultations = Consultation::factory()->count(2)->create(['read_at' => null]);

    Livewire::test(ListConsultations::class)
        ->callTableBulkAction('markAsRead', $consultations);

    expect($consultations->fresh()->pluck('read_at')->filter()->count())->toBe(2);

    Livewire::test(ListConsultations::class)
        ->callTableBulkAction('markAsUnread', $consultations);

    expect($consultations->fresh()->pluck('read_at')->filter()->count())->toBe(0);
});
