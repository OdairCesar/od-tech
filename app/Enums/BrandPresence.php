<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BrandPresence: string implements HasLabel
{
    case NotMentioned = 'not_mentioned';
    case Subtle = 'subtle';
    case CtaAtEnd = 'cta_at_end';
    case Throughout = 'throughout';

    public function getLabel(): string
    {
        return match ($this) {
            self::NotMentioned => 'Não aparece',
            self::Subtle => 'Discretamente',
            self::CtaAtEnd => 'CTA no final',
            self::Throughout => 'Ao longo do texto',
        };
    }

    public function promptInstruction(): string
    {
        return match ($this) {
            self::NotMentioned => 'Não mencione a OD Tec em nenhum momento do texto.',
            self::Subtle => 'Mencione a OD Tec apenas discretamente, sem soar como propaganda.',
            self::CtaAtEnd => 'Mencione a OD Tec apenas em uma chamada para ação (CTA) ao final do texto.',
            self::Throughout => 'Mencione a OD Tec de forma natural ao longo de todo o texto.',
        };
    }
}
