<?php

namespace App\Enums;

enum PageStatus: string
{
    case Published = 'published';
    case Draft = 'draft';

    public function label(): string
    {
        return match ($this) {
            self::Published => 'Publicado',
            self::Draft => 'Rascunho',
        };
    }
}
