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

class JournalTransactionTemplateExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    public function collection()
    {
        return new Collection([
            [
                'date' => date('Y-m-d'),
                'note' => 'Sample journal transaction',
                'account_number' => '1101',
                'description' => 'Cash received',
                'debit' => 1000000,
                'credit' => 0,
            ],
            [
                'date' => date('Y-m-d'),
                'note' => 'Sample journal transaction',
                'account_number' => '4101',
                'description' => 'Sales revenue',
                'debit' => 0,
                'credit' => 1000000,
            ],
            [
                'date' => date('Y-m-d'),
                'note' => 'Office expense',
                'account_number' => '6101',
                'description' => 'Office supplies',
                'debit' => 250000,
                'credit' => 0,
            ],
            [
                'date' => date('Y-m-d'),
                'note' => 'Office expense',
                'account_number' => '1101',
                'description' => 'Paid from cash',
                'debit' => 0,
                'credit' => 250000,
            ],
        ]);
    }

    public function map($row): array
    {
        return [
            $row['date'] ?? '',
            $row['note'] ?? '',
            $row['account_number'] ?? '',
            $row['description'] ?? '',
            $row['debit'] ?? 0,
            $row['credit'] ?? 0,
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Note',
            'Account Number',
            'Description',
            'Debit',
            'Credit',
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
                $sheet->setCellValue('A1', 'Journal Transaction Import Template');

                $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $richText->createText('Fill in one row for each journal transaction detail. Be sure to use a valid account number and the date format is ');
                $boldDateFormat = $richText->createTextRun('YYYY-MM-DD');
                $boldDateFormat->getFont()->setBold(true);
                $richText->createText('. If multiple rows have the same ');
                $boldDate = $richText->createTextRun('date');
                $boldDate->getFont()->setBold(true);
                $richText->createText(' and ');
                $boldNote = $richText->createTextRun('note');
                $boldNote->getFont()->setBold(true);
                $richText->createText(', they will be treated as details of the same journal transaction (master).');

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

                $sheet->getStyle('A3:F3')->applyFromArray([
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
                    $sheet->getStyle('A3:F' . $highestRow)->applyFromArray([
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

                    $sheet->getStyle('E4:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('F4:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
            },
        ];
    }
}
