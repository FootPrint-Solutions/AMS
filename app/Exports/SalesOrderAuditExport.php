<?php

namespace App\Exports;

use App\Models\Developer\AuditModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class SalesOrderAuditExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    private Collection $rows;
    private string $title;

    public function __construct(?string $dateStart = null, ?string $dateEnd = null)
    {
        $query = AuditModel::query()
            ->join('users', 'audits.user_id', '=', 'users.id')
            ->leftJoin('sales_orders', function ($join) {
                $join->on('audits.auditable_id', '=', 'sales_orders.id')
                    ->where('audits.auditable_type', '=', SalesOrderModel::class);
            })
            ->where('audits.auditable_type', SalesOrderModel::class)
            ->select(
                'audits.id',
                'audits.created_at',
                'users.name as user_name',
                'audits.event',
                'audits.auditable_id',
                'sales_orders.sales_order_number',
                'audits.old_values',
                'audits.new_values'
            )
            ->orderBy('audits.created_at', 'desc');

        if (!empty($dateStart)) {
            $query->whereDate('audits.created_at', '>=', $dateStart);
        }

        if (!empty($dateEnd)) {
            $query->whereDate('audits.created_at', '<=', $dateEnd);
        }

        $this->rows = $query->get();

        $parts = ['Sales Order Audit Export'];

        if (!empty($dateStart) || !empty($dateEnd)) {
            $parts[] = 'Period: ' . ($dateStart ?: '-') . ' to ' . ($dateEnd ?: '-');
        }

        $this->title = implode(' | ', $parts);
    }

    public function collection()
    {
        return $this->rows;
    }

    private function parseValues($json): string
    {
        if (empty($json) || $json === '[]' || $json === '{}') {
            return '-';
        }

        $data = json_decode($json, true);

        if (!is_array($data) || empty($data)) {
            return '-';
        }

        $lines = [];

        foreach ($data as $field => $value) {
            if ($value === null || $value === '') {
                $value = '(empty)';
            }

            $label = ucfirst(str_replace('_', ' ', $field));
            $lines[] = $label . ': ' . $value;
        }

        return implode("\n", $lines);
    }

    private function getChangedFields($oldValues, $newValues): string
    {
        $old = json_decode($oldValues, true) ?? [];
        $new = json_decode($newValues, true) ?? [];
        $fields = array_unique(array_merge(array_keys($old), array_keys($new)));

        if (empty($fields)) {
            return '-';
        }

        $labels = array_map(function ($field) {
            return ucfirst(str_replace('_', ' ', $field));
        }, $fields);

        return implode(', ', $labels);
    }

    public function map($row): array
    {
        $eventLabels = [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'restored' => 'Restored',
        ];

        return [
            $row->created_at->format('Y-m-d H:i:s'),
            $row->user_name ?? '-',
            $eventLabels[$row->event] ?? ucfirst($row->event),
            $row->sales_order_number ?? 'SO#' . $row->auditable_id,
            $this->getChangedFields($row->old_values, $row->new_values),
            $this->parseValues($row->old_values),
            $this->parseValues($row->new_values),
        ];
    }

    public function headings(): array
    {
        return [
            'Date & Time',
            'User',
            'Event',
            'Sales Order',
            'Changed Fields',
            'Old Values',
            'New Values',
        ];
    }

    public function styles($sheet)
    {
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageMargins()
            ->setLeft(0.5)
            ->setRight(0.5)
            ->setTop(0.5)
            ->setBottom(0.5);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', $this->title);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 13,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
                $sheet->mergeCells('A1:G1');

                $sheet->setCellValue('A2', 'Audit trail for Sales Order changes — one row per change event.');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 11,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'wrapText' => true,
                    ],
                ]);
                $sheet->mergeCells('A2:G2');
                $sheet->getRowDimension(2)->setRowHeight(24);

                $sheet->getStyle('A3:G3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '4472C4'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->freezePane('A4');

                if ($highestRow > 3) {
                    $range = 'A3:G' . $highestRow;
                    $sheet->getStyle($range)->applyFromArray([
                        'font' => [
                            'size' => 11,
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CCCCCC'],
                            ],
                        ],
                    ]);

                    $sheet->getStyle('A4:G' . $highestRow)->getAlignment()
                        ->setVertical(Alignment::VERTICAL_TOP);

                    $sheet->getColumnDimension('F')->setWidth(40);
                    $sheet->getColumnDimension('G')->setWidth(40);
                }
            },
        ];
    }
}
