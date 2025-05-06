<?php

namespace App\Exports;

use App\Models\Orders\SalesOrder\SalesOrderModel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesOrderExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell
{
    public function collection()
    {
        return SalesOrderModel::with(['customer', 'vehicle', 'distributorShop', 'technician'])->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Sales Order Number',
            'Marketplace Invoice Number',
            'Date',
            'Customer Name',
            'Vehicle Name',
            'Distributor Shop Name',
            'Distributor Shop Technician Name',
            'Total',
            'Payment Method',
            'Payment Status',
            'Status'
        ];
    }

    public function map($data): array
    {
        return [
            $data->sales_order_number ?? '-',
            $data->invoice_number ?? '-',
            $data->date ?? '-',
            $data->customer->name ?? '-',
            $data->vehicle->name ?? '-',
            $data->distributorShop->name ?? '-',
            $data->technicians->name ?? '-',
            $data->total,
            $data->paymentMethod->name,
            $data->payment_status,
            $data->status
        ];
    }

    public function startCell(): string
    {
        return 'A3';
    }
}
