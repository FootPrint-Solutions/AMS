<?php

namespace App\Exports;

use App\Models\MasterData\Battery\BatteryModel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BatteryExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell
{
    public function collection()
    {
        return BatteryModel::with(['brand', 'subbrandCategory', 'usageType', 'sizeCategory', 'technology', 'code'])->get();
    }

    public function headings(): array
    {
        return [
            'Battery ID',
            'Battery Code',
            'Name',
            'Alternate Name',
            'Brand',
            'Subbrand Category',
            'Usage Type',
            'Size Category',
            'Technology',
            'Dimension (length)',
            'Dimension (width)',
            'Dimension (height)',
            'Standard CCA',
            'Capacity',
            'Warranty',
            'Retail Price',
            'Buy Price'
        ];
    }

    public function map($battery): array
    {
        return [
            $battery->id,
            $battery->code->code ?? "",
            $battery->name,
            $battery->name_alternate,
            $battery->brand->name ?? "-",
            $battery->subbrandCategory->name ?? "-",
            $battery->usageType->name ?? "-",
            $battery->sizeCategory->name ?? "-",
            $battery->technology->name ?? "-",
            $battery->dimension_length,
            $battery->dimension_width,
            $battery->dimension_height,
            $battery->standard_cca,
            $battery->capacity,
            $battery->warranty,
            $battery->price_retail,
            $battery->price_buy,
        ];
    }

    public function startCell(): string
    {
        return 'A3';
    }
}
