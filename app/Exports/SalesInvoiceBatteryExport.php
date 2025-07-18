<?php

namespace App\Exports;

use App\Models\Orders\SalesInvoice\SalesInvoiceBatteryModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesInvoiceBatteryExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
{
    protected $dateStart;
    protected $dateEnd;

    public function __construct($dateStart, $dateEnd)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
    }

    public function collection()
    {
        $query = SalesInvoiceBatteryModel::with([
            'SalesInvoice.customer',
            'SalesInvoice.vehicle',
            'SalesInvoice.distributorShop',
            'SalesInvoice.technician',
            'SalesInvoice.paymentMethod',
            'battery'
        ]);

        $query->whereHas('SalesInvoice', function ($q) {
            $q->whereNull('deleted_at');
            if (isset($this->dateStart) && isset($this->dateEnd)) {
                $q->whereBetween('date', [$this->dateStart, $this->dateEnd]);
            }
        });

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Sales Invoice Number',
            'Sales Order Number',
            'Date',
            'Customer Name',
            'Production Code',
            'Product Name',
            'Qty',
            'Unit Price',
            'Subtotal',
            'Vehicle Name',
            'Distributor Shop Name',
            'Technician Name',
            'Payment Method',
            'Payment Status',
            'Status',
            'Address',
            'Latitude',
            'Longitude'
        ];
    }

    public function map($data): array
    {
        return [
            $data->SalesInvoice->number ?? '-',
            $data->SalesInvoice->sales_order_number ?? '-',
            $data->SalesInvoice->date ?? '-',
            $data->SalesInvoice->customer->name ?? '-',
            $data->battery_production_code ?? '-',
            $data->battery->name ?? '-',
            $data->quantity ?? '-',
            $data->price_net ?? '-',
            $data->subtotal ?? '-',
            $data->SalesInvoice->vehicle->name ?? '-',
            $data->SalesInvoice->distributorShop->name ?? '-',
            $data->SalesInvoice->technician->name ?? '-',
            $data->SalesInvoice->paymentMethod->name ?? '-',
            $data->SalesInvoice->payment_status ?? '-',
            $data->SalesInvoice->status ?? '-',
            $data->SalesInvoice->customer->address ?? '-',
            $data->SalesInvoice->customer->latitude ?? '-',
            $data->SalesInvoice->customer->longitude ?? '-'
        ];
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setCellValue('A1', 'Filter Date : ' . $this->dateStart . ' - ' . $this->dateEnd);
            },
        ];
    }
}
