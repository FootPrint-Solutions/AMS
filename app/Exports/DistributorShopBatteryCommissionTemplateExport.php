<?php

namespace App\Exports;

use Illuminate\Support\Collection;
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

use App\Models\MasterData\Battery\BatteryModel;

class DistributorShopBatteryCommissionTemplateExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    public function collection()
    {
        return new Collection(BatteryModel::where('status', true)->get()->toArray());
    }

    public function map($row): array
    {
        return [
            $row['id'] ?? '',
            $row['name'] ?? '',
            '',
            '',
        ];
    }

    public function headings(): array
    {
        return [
            'Battery ID',
            'Battery Name',
            'Commission Type',
            'Commission Amount',
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
                $sheet->setCellValue('A1', 'Distributor Shop Battery Commission Template');

                $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $richText->createText('Fill in one row for each battery commission. Be sure to use a valid battery ID and the commission amount should be a number. Available commission types are "technician", "pic" and "pit_stop".');
                $sheet->setCellValue('A2', $richText);

                $sheet->getRowDimension(2)->setRowHeight(52);
                $sheet->getStyle('A2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);


                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 13,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);

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

                $sheet->getStyle('A3:D3')->applyFromArray([
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
                    $sheet->getStyle('A3:D' . $highestRow)->applyFromArray([
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

                    $sheet->getStyle('E4:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('F4:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
            },
        ];
    }
}
