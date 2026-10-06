<?php

namespace App\Filament\Resources\Transactions;

use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Models\Consignor;
use App\Models\Transaction;
use BackedEnum;
use Closure;
use Filament\Actions\ExportAction; // <-- The v4 Namespace Fix
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('item_id')
                    ->relationship('item', 'description')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
                    
                Select::make('type')
                    ->options([
                        'DELIVERY' => 'Delivery (Stock In)',
                        'CONSUMED' => 'Consumed (Stock Out)',
                        'RETURNED' => 'Returned (Stock Out)',
                    ])
                    ->required()
                    ->live(),
                    
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->helperText('Just type the amount. The system will automatically handle the deductions.')
                    // Prevent Negative Stock Validation!
                    ->rules([
                        fn ($get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            if ($get('type') === 'CONSUMED' && $get('item_id')) {
                                $item = \App\Models\Item::withSum('transactions', 'quantity')->find($get('item_id'));
                                if ($item && $value > $item->transactions_sum_quantity) {
                                    $fail("Insufficient stock! Only {$item->transactions_sum_quantity} pieces remaining.");
                                }
                            }
                        },
                    ]),
                    
                TextInput::make('reference_number')
                    ->label('Reference No. (DR / Charge Slip)')
                    ->maxLength(255),
                    
                Select::make('payment_type')
                    ->options([
                        'CASH' => 'Cash',
                        'CHARGED' => 'Charged',
                    ])
                    ->visible(fn ($get): bool => $get('type') === 'CONSUMED'),
                    
                DateTimePicker::make('transaction_date')
                    ->required()
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(\App\Filament\Exports\TransactionExporter::class)
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down'),
            ])
            ->groups([
                Group::make('payment_type')
                    ->label('Payment Type')
                    ->collapsible(),
            ])
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                    
                TextColumn::make('item.consignor.name')
                    ->label('Supplier')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('item.description')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'DELIVERY' => 'success',
                        'CONSUMED' => 'danger',
                        'RETURNED' => 'warning',
                        default => 'primary',
                    }),
                    
                TextColumn::make('quantity')
                    ->label('Qty')
                    ->numeric()
                    ->sortable(),
                    
                TextColumn::make('item.unit_price')
                    ->label('Unit Price')
                    ->money('PHP')
                    ->sortable(),
                    
                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('PHP')
                    ->state(fn (Transaction $record): float => abs($record->quantity) * $record->item->unit_price),
                    
                TextColumn::make('payment_type')
                    ->label('Payment')
                    ->searchable(),
                    
                TextColumn::make('reference_number')
                    ->label('Ref No.')
                    ->searchable(),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'DELIVERY' => 'Delivery',
                        'CONSUMED' => 'Consumed',
                        'RETURNED' => 'Returned',
                    ]),
                    
                SelectFilter::make('consignor_id')
                    ->label('Filter by Supplier')
                    ->options(fn () => Consignor::pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['value'])) {
                            $query->whereHas('item', fn (Builder $q) => $q->where('consignor_id', $data['value']));
                        }
                        return $query;
                    }),
                    
                Filter::make('transaction_date')
                    ->form([
                        DatePicker::make('created_from')->label('From Date'),
                        DatePicker::make('created_until')->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $q, $date): Builder => $q->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $q, $date): Builder => $q->whereDate('transaction_date', '<=', $date),
                            );
                    })
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }
}