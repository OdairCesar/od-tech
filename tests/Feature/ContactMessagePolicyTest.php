<?php

use App\Models\ContactMessage;
use App\Models\User;

test('admin can view, edit and delete contact messages', function () {
    $admin = User::factory()->admin()->create();
    $message = ContactMessage::factory()->create();

    expect($admin->can('viewAny', ContactMessage::class))->toBeTrue()
        ->and($admin->can('view', $message))->toBeTrue()
        ->and($admin->can('update', $message))->toBeTrue()
        ->and($admin->can('delete', $message))->toBeTrue()
        ->and($admin->can('deleteAny', ContactMessage::class))->toBeTrue();
});

test('editor cannot view, edit or delete contact messages', function () {
    $editor = User::factory()->create();
    $message = ContactMessage::factory()->create();

    expect($editor->can('viewAny', ContactMessage::class))->toBeFalse()
        ->and($editor->can('view', $message))->toBeFalse()
        ->and($editor->can('update', $message))->toBeFalse()
        ->and($editor->can('delete', $message))->toBeFalse()
        ->and($editor->can('deleteAny', ContactMessage::class))->toBeFalse();

    $this->actingAs($editor);

    $this->get("/admin/contact-messages/{$message->id}/edit")->assertForbidden();
});

test('nobody can create contact messages, including admins', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->can('create', ContactMessage::class))->toBeFalse();

    $this->actingAs($admin);

    $this->get('/admin/contact-messages/create')->assertNotFound();
});
