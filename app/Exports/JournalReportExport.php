<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Illuminate\Support\Collection;

class JournalReportExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    private $data;
    private $dateStart;
    private $dateEnd;

    public function __construct($data, $dateStart, $dateEnd)
    {
        $this->data = $data;
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
    }

    public function collection()
    {
        $rows = collect();

        foreach ($this->data as $report) {
            foreach ($report['details'] as $index => $detail) {
                $row = [
                    'date' => $index === 0 ? $report['date'] : '',
                    'voucher_number' => $index === 0 ? $report['number'] : '',
                    'account_code' => substr($detail['description'], 1, strpos($detail['description'], ')') - 1),
                    'account_name' => substr($detail['description'], strpos($detail['description'], ')') + 2),
                    'description' => $detail['description'],
                    'debit' => $detail['total_debit'] ?? 0,
                    'credit' => $detail['total_credit'] ?? 0,
                    'reference' => $detail['ref'] ?? '',
                ];
                $rows->push($row);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Voucher Number',
            'Account Code',
            'Account Name',
            'Description',
            'Debit',
            'Credit',
            'Reference'
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

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(12);  // Date
                $sheet->getColumnDimension('B')->setWidth(15);  // Voucher Number
                $sheet->getColumnDimension('C')->setWidth(12);  // Account Code
                $sheet->getColumnDimension('D')->setWidth(25);  // Account Name
                $sheet->getColumnDimension('E')->setWidth(30);  // Description
                $sheet->getColumnDimension('F')->setWidth(12);  // Debit
                $sheet->getColumnDimension('G')->setWidth(12);  // Credit
                $sheet->getColumnDimension('H')->setWidth(15);  // Reference

                // Get highest row before manipulation
                $highestRow = $sheet->getHighestRow();

                // Insert 2 rows at the top for title and empty space
                for ($i = 0; $i < 2; $i++) {
                    $sheet->insertNewRowBefore(1);
                }

                // Set title
                $sheet->setCellValue('A1', 'Journal Report: ' . $this->dateStart . ' to ' . $this->dateEnd);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
                $sheet->mergeCells('A1:H1');

                // Update highest row after insertion
                $highestRow = $sheet->getHighestRow();

                // Header row styling (now at row 3)
                $sheet->getStyle('3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 11,
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

                // Freeze at row 4 (after headers)
                $sheet->freezePane('A4');

                // Apply minimal styling to all data rows at once (more efficient)
                if ($highestRow > 3) {
                    $dataRange = '4:' . $highestRow;

                    // Apply basic formatting to all data rows
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'font' => [
                            'size' => 10,
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

                    // Apply number formatting to debit/credit columns (range, not per-row)
                    $sheet->getStyle('F4:F' . $highestRow)->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        ],
                        'numberFormat' => [
                            'formatCode' => '#,##0.00',
                        ],
                    ]);

                    $sheet->getStyle('G4:G' . $highestRow)->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        ],
                        'numberFormat' => [
                            'formatCode' => '#,##0.00',
                        ],
                    ]);

                    // Apply alternating row colors using conditional formatting for better performance
                    $sheet->getStyle('A4:H' . $highestRow)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                    ]);
                }

                // Add summary row
                $summaryRow = $highestRow + 1;

                $sheet->setCellValue('E' . $summaryRow, 'TOTAL:');
                $sheet->setCellValue('F' . $summaryRow, '=SUM(F4:F' . $highestRow . ')');
                $sheet->setCellValue('G' . $summaryRow, '=SUM(G4:G' . $highestRow . ')');

                // Summary row styling
                $sheet->getStyle('E' . $summaryRow . ':G' . $summaryRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'D9E1F2'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'numberFormat' => [
                        'formatCode' => '#,##0.00',
                    ],
                ]);
            },
        ];
    }
}
