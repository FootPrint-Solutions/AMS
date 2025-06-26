<?php

namespace App\Exports;

use App\Models\Orders\SalesOrder\SalesOrderBatteryModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesOrderDetailsExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
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
        $query = SalesOrderBatteryModel::with([
            'salesOrder.customer',
            'salesOrder.vehicle',
            'salesOrder.distributorShop',
            'salesOrder.technician',
            'salesOrder.paymentMethod',
            'battery'
        ]);

        if (isset($this->dateStart) && isset($this->dateEnd)) {
            $query->whereHas('salesOrder', function ($q) {
                $q->whereBetween('date', [$this->dateStart, $this->dateEnd]);
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
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
            $data->salesOrder->sales_order_number ?? '-',
            $data->salesOrder->date ?? '-',
            $data->salesOrder->customer->name ?? '-',
            $data->battery_production_code ?? '-',
            $data->battery->name ?? '-',
            $data->quantity ?? '-',
            $data->price_net ?? '-',
            $data->subtotal ?? '-',
            $data->salesOrder->vehicle->name ?? '-',
            $data->salesOrder->distributorShop->name ?? '-',
            $data->salesOrder->technician->name ?? '-',
            $data->salesOrder->paymentMethod->name ?? '-',
            $data->salesOrder->payment_status ?? '-',
            $data->salesOrder->status ?? '-',
            $data->salesOrder->customer->address ?? '-',
            $data->salesOrder->customer->latitude ?? '-',
            $data->salesOrder->customer->longitude ?? '-'
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
