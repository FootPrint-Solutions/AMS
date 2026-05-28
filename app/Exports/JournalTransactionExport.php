<?php

namespace App\Exports;

use App\Models\Accounting\JournalTransactionModel;
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

class JournalTransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    private Collection $rows;
    private int $rowNumber = 1;
    private string $title;

    public function __construct(?string $status = null, ?string $dateStart = null, ?string $dateEnd = null)
    {
        $this->rows = JournalTransactionModel::filteredListQuery($status, $dateStart, $dateEnd)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $parts = ['Journal Transaction Export'];

        if (!empty($status) && $status !== 'all') {
            $parts[] = 'Status: ' . ucfirst($status);
        }

        if (!empty($dateStart) || !empty($dateEnd)) {
            $parts[] = 'Period: ' . ($dateStart ?: '-') . ' to ' . ($dateEnd ?: '-');
        }

        $this->title = implode(' | ', $parts);
    }

    public function collection()
    {
        return $this->rows;
    }

    public function map($journal): array
    {
        return [
            $this->rowNumber++,
            $journal->voucher_number,
            formatDate((string) $journal->date, 'j M Y'),
            $journal->note ?? '-',
            (float) ($journal->total ?? 0),
            (float) ($journal->total ?? 0),
            ucfirst((string) ($journal->status ?? '-')),
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Voucher Number',
            'Date',
            'Note',
            'Total Debit',
            'Total Credit',
            'Status',
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

                $sheet->insertNewRowBefore(1, 1);
                $sheet->setCellValue('A1', $this->title);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
                $sheet->mergeCells('A1:G1');

                $sheet->getStyle('A2:G2')->applyFromArray([
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

                $sheet->freezePane('A3');

                if ($highestRow > 1) {
                    $sheet->getStyle('A2:G' . ($highestRow + 1))->applyFromArray([
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

                    $sheet->getStyle('E3:F' . ($highestRow + 1))->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle('E3:F' . ($highestRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
            },
        ];
    }
}
