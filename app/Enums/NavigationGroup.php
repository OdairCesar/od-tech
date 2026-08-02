<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup: string implements HasLabel
{
    case Crm = 'crm';
    case Servicos = 'servicos';
    case Paginas = 'paginas';
    case Blog = 'blog';
    case Localizacao = 'localizacao';

    public function getLabel(): string
    {
        return match ($this) {
            self::Crm => 'CRM',
            self::Servicos => 'Serviços',
            self::Paginas => 'Páginas',
            self::Blog => 'Blog',
            self::Localizacao => 'Localização',
        };
    }
}
