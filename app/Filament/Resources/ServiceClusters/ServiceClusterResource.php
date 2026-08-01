<?php

namespace App\Filament\Resources\ServiceClusters;

use App\Filament\Resources\ServiceClusters\Pages\CreateServiceCluster;
use App\Filament\Resources\ServiceClusters\Pages\EditServiceCluster;
use App\Filament\Resources\ServiceClusters\Pages\GenerateAiServiceCluster;
use App\Filament\Resources\ServiceClusters\Pages\ListServiceClusters;
use App\Filament\Resources\ServiceClusters\Schemas\ServiceClusterForm;
use App\Filament\Resources\ServiceClusters\Tables\ServiceClustersTable;
use App\Models\ServiceCluster;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServiceClusterResource extends Resource
{
    protected static ?string $model = ServiceCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquare3Stack3d;

    protected static ?string $modelLabel = 'cluster';

    protected static ?string $pluralModelLabel = 'clusters';

    public static function form(Schema $schema): Schema
    {
        return ServiceClusterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceClustersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceClusters::route('/'),
            'create' => CreateServiceCluster::route('/create'),
            'generate' => GenerateAiServiceCluster::route('/generate'),
            'edit' => EditServiceCluster::route('/{record}/edit'),
        ];
    }
}
