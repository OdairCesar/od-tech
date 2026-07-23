<?php

namespace App\Filament\Support\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class ViewOnLandingAction
{
    public static function make(Closure $url, Closure $visible, string $label = 'Ver na landing'): Action
    {
        return Action::make('view')
            ->label($label)
            ->icon(Heroicon::OutlinedEye)
            ->url($url)
            ->openUrlInNewTab()
            ->visible($visible);
    }
}
