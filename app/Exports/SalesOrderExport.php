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

    public function map($battery): array
    {
        return [
            $battery->sales_order_number ?? '-',
            $battery->invoice_number ?? '-',
            $battery->date ?? '-',
            $battery->customer->name ?? '-',
            $battery->vehicle->name ?? '-',
            $battery->distributorShop->name ?? '-',
            $battery->technicians->name ?? '-',
            $battery->total,
            $battery->paymentMethod->name,
            $battery->payment_status,
            $battery->status
        ];
    }

    public function startCell(): string
    {
        return 'A3';
    }
}
