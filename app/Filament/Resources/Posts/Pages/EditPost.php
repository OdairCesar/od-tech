<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Enums\PostStatus;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Support\Actions\ViewOnLandingAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewOnLandingAction::make(
                url: fn (): string => route('blog.show', $this->record),
                visible: fn (): bool => $this->record->status === PostStatus::Published,
                label: 'Ver post',
            ),
            DeleteAction::make(),
        ];
    }
}
