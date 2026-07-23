<?php

namespace App\Filament\Resources\Services\Pages;

use App\Enums\PageStatus;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Support\Actions\ViewOnLandingAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewOnLandingAction::make(
                url: fn (): string => route('services.show', $this->record),
                visible: fn (): bool => $this->record->status === PageStatus::Published,
            ),
            DeleteAction::make(),
        ];
    }
}
