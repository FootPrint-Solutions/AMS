<?php

namespace App\Exports;

use App\Models\Orders\WorkOrder\WorkOrderModel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WorkOrderExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell
{
    public function collection()
    {
        return WorkOrderModel::with(['salesOrder', 'customer', 'distributorShop', 'paymentMethod'])->get();
    }

    public function headings(): array
    {
        return [
            'Work Order Number',
            'Sales Order Number',
            'Date',
            'Customer Name',
            'Qty',
            'Total',
            'Address'
        ];
    }

    public function map($data): array
    {
        return [
            $data->work_order_number ?? '-',
            $data->salesOrder->sales_order_number ?? '-',
            $data->date ?? '-',
            $data->customer->name ?? '-',
            $data->salesOrder->qty ?? '-',
            $data->total ?? '-',
            $data->address ?? '-',
        ];
    }

    public function startCell(): string
    {
        return 'A3';
    }
}
