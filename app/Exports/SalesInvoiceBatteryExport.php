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
            'salesInvoice.customer',
            'salesInvoice.vehicle',
            'salesInvoice.distributorShop',
            'salesInvoice.technician',
            'salesInvoice.paymentMethod',
            'salesInvoice.salesOrder',
            'battery'
        ]);

        $query->whereHas('salesInvoice', function ($q) {
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
            $data->salesInvoice->sales_invoice_number ?? '-',
            $data->salesInvoice->salesOrder->sales_order_number ?? '-',
            $data->salesInvoice->date ?? '-',
            $data->salesInvoice->customer->name ?? '-',
            $data->battery_production_code ?? '-',
            $data->battery->name ?? '-',
            $data->quantity ?? '-',
            $data->price_net ?? '-',
            $data->subtotal ?? '-',
            $data->salesInvoice->vehicle->name ?? '-',
            $data->salesInvoice->distributorShop->name ?? '-',
            $data->salesInvoice->technician->name ?? '-',
            $data->salesInvoice->paymentMethod->name ?? '-',
            $data->salesInvoice->payment_status ?? '-',
            $data->salesInvoice->status ?? '-',
            $data->salesInvoice->customer->address ?? '-',
            $data->salesInvoice->customer->latitude ?? '-',
            $data->salesInvoice->customer->longitude ?? '-'
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
