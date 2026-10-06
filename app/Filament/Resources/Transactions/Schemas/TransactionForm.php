<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('item_id')
                    ->required()
                    ->numeric(),
                Select::make('type')
                    ->options([
            'DELIVERY' => 'D e l i v e r y',
            'CONSUMED' => 'C o n s u m e d',
            'RETURNED' => 'R e t u r n e d',
        ])
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('reference_number'),
                Select::make('payment_type')
                    ->options(['CASH' => 'C a s h', 'CHARGED' => 'C h a r g e d']),
                DateTimePicker::make('transaction_date')
                    ->required(),
            ]);
    }
}
