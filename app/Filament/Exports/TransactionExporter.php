<?php

namespace App\Filament\Exports;

use App\Models\Transaction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransactionExporter extends Exporter
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('transaction_date')
                ->label('Date')
                ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('M d, Y')),
                
            ExportColumn::make('item.consignor.name')
                ->label('Supplier'),
                
            ExportColumn::make('item.description')
                ->label('Item Description'),
                
            ExportColumn::make('type')
                ->label('Transaction Type'),
                
            ExportColumn::make('quantity')
                ->label('Quantity'),
                
            ExportColumn::make('item.unit_price')
                ->label('Unit Price'),
                
            // Computes the billing total directly into the Excel cell
            ExportColumn::make('total_amount')
                ->label('Total Amount')
                ->state(fn (Transaction $record): float => abs($record->quantity) * $record->item->unit_price),
                
            ExportColumn::make('payment_type')
                ->label('Payment Type'),
                
            ExportColumn::make('reference_number')
                ->label('Reference No. / Charge Slip'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your financial report export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}