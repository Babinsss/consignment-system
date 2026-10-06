<?php

namespace App\Filament\Resources\Consignors;

use App\Filament\Resources\Consignors\Pages\CreateConsignor;
use App\Filament\Resources\Consignors\Pages\EditConsignor;
use App\Filament\Resources\Consignors\Pages\ListConsignors;
use App\Filament\Resources\Consignors\Schemas\ConsignorForm;
use App\Filament\Resources\Consignors\Tables\ConsignorsTable;
use App\Models\Consignor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ConsignorResource extends Resource
{
    protected static ?string $model = Consignor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ConsignorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConsignorsTable::configure($table);
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
            'index' => ListConsignors::route('/'),
            'create' => CreateConsignor::route('/create'),
            'edit' => EditConsignor::route('/{record}/edit'),
        ];
    }
}
