<?php

namespace App\Exports;

use App\Models\Accounting\JournalTransactionDetailModel;
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
    private string $title;

    public function __construct(?string $status = null, ?string $dateStart = null, ?string $dateEnd = null)
    {
        $query = JournalTransactionDetailModel::query()
            ->join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
            ->select(
                'journal_entries.date',
                'journal_entries.note',
                'journal_entry_details.account_number',
                'journal_entry_details.description',
                'journal_entry_details.debit',
                'journal_entry_details.credit'
            )
            ->orderBy('journal_entries.date', 'desc')
            ->orderBy('journal_entries.id', 'desc')
            ->orderBy('journal_entry_details.id', 'asc');

        if (!empty($status) && $status !== 'all') {
            $query->where('journal_entries.status', $status);
        }

        if (!empty($dateStart)) {
            $query->whereDate('journal_entries.date', '>=', $dateStart);
        }

        if (!empty($dateEnd)) {
            $query->whereDate('journal_entries.date', '<=', $dateEnd);
        }

        $this->rows = $query->get();

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

    public function map($detail): array
    {
        return [
            formatDate((string) $detail->date, 'Y-m-d'),
            $detail->note ?? '-',
            $detail->account_number ?? '',
            $detail->description ?? '',
            (float) ($detail->debit ?? 0),
            (float) ($detail->credit ?? 0),
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
                $sheet->mergeCells('A1:F1');

                $sheet->setCellValue('A2', 'Exported journal transaction details. Fill in one row for each detail with a valid account number from Chart of Accounts and date format YYYY-MM-DD.');
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
                $sheet->mergeCells('A2:F2');
                $sheet->getRowDimension(2)->setRowHeight(36);

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
                }
            },
        ];
    }
}
