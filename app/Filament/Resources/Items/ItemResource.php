<?php

namespace App\Filament\Resources\Items;

use App\Filament\Resources\Items\Pages\CreateItem;
use App\Filament\Resources\Items\Pages\EditItem;
use App\Filament\Resources\Items\Pages\ListItems;
use App\Models\Item;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('consignor_id')
                    ->relationship('consignor', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('unit_price')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('consignor.name')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('description')
                    ->searchable(),
                    
                TextColumn::make('unit_price')
                    ->money('PHP')
                    ->sortable(),
                    
                TextColumn::make('transactions_sum_quantity')
                    ->label('Current Stock')
                    ->numeric()
                    ->badge()
                    ->color(fn ($state): string => $state <= 10 ? 'danger' : 'success')
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ]);
            // The actions() and bulkActions() arrays have been completely removed to bypass the bug.
            // You can now just click anywhere on a row to edit it!
    }

    public static function getPages(): array
    {
        return [
            'index' => ListItems::route('/'),
            'create' => CreateItem::route('/create'),
            'edit' => EditItem::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withSum('transactions', 'quantity');
    }
}