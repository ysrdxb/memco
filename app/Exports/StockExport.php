<?php

namespace App\Exports;

use App\Models\StockRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return StockRecord::with('product')->get()->map(function ($record) {
            return [
                'ID' => $record->id,
                'Product Code' => $record->product->code,
                'Product Name' => $record->product->name,
                'Price' => $record->product->price, 
                'Quantity' => $record->quantity,
                'Total Amount' => $record->product->price * $record->quantity,
                'Date' => $record->updated_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Product Code',
            'Product Name',
            'Price',
            'Quantity',
            'Total Amount',
            'Last Updated',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Make the first row (headings) bold
        ];
    }    
}
