<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Enums\PostStatus;
use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->label('Ver post')
                ->icon(Heroicon::OutlinedEye)
                ->url(fn (): string => route('blog.show', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->record->status === PostStatus::Published),
            DeleteAction::make(),
        ];
    }
}
