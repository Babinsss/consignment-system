<?php

namespace App\Filament\Resources\Consignors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ConsignorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
