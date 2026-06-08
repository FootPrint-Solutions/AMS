<?php

namespace App\Imports;

use App\Models\Accounting\ChartOfAccountModel;
use App\Models\Accounting\JournalTransactionDetailModel;
use App\Models\Accounting\JournalTransactionModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class JournalTransactionImport implements ToCollection, WithHeadingRow
{
    private int $totalRows = 0;

    private int $totalTransactions = 0;

    private int $totalInsertedRows = 0;

    private array $unimportedRows = [];

    private array $seenVoucherNumbers = [];

    public function headingRow(): int
    {
        return 3;
    }

    private function generateVoucherNumber()
    {
        return JournalTransactionModel::generateVoucherNumber();
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function getTotalTransactions(): int
    {
        return $this->totalTransactions;
    }

    public function getTotalInsertedRows(): int
    {
        return $this->totalInsertedRows;
    }

    public function getUnimportedRows(): array
    {
        return $this->unimportedRows;
    }

    public function collection(Collection $rows)
    {
        $groups = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $this->headingRow() + 1 + $index;

            if ($row->filter()->isEmpty()) {
                continue;
            }

            $this->totalRows++;

            $date = $row['date'] ?? null;
            $note = trim($row['note'] ?? '');
            $accountNumber = trim($row['account_number'] ?? '');
            $description = $row['description'] ?? null;
            $debit = (float) ($row['debit'] ?? 0);
            $credit = (float) ($row['credit'] ?? 0);

            if (!$date || !$note || !$accountNumber) {
                $this->unimportedRows[] = [
                    'row' => $rowNumber,
                    'reason' => 'Missing required fields (date, note, or account number)',
                ];
                continue;
            }

            try {
                if (is_numeric($date)) {
                    $date = Carbon::instance(
                        ExcelDate::excelToDateTimeObject($date)
                    )->format('Y-m-d');
                } else {
                    $date = Carbon::parse($date)->format('Y-m-d');
                }
            } catch (Exception $e) {
                $this->unimportedRows[] = [
                    'row' => $rowNumber,
                    'reason' => 'Invalid date format',
                ];
                continue;
            }

            $account = ChartOfAccountModel::where('number', $accountNumber)->first();

            if (!$account) {
                $this->unimportedRows[] = [
                    'row' => $rowNumber,
                    'reason' => "Account number {$accountNumber} not found",
                ];
                continue;
            }

            $groupKey = md5($date . '|' . $note);

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'date' => $date,
                    'note' => $note,
                    'details' => [],
                ];
            }

            $groups[$groupKey]['details'][] = [
                'chart_of_account_id' => $account->id,
                'account_number' => $account->number,
                'account_name' => $account->name,
                'description' => $description,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        foreach ($groups as $group) {
            DB::beginTransaction();

            try {
                $journal = JournalTransactionModel::create([
                    'date' => $group['date'],
                    'note' => $group['note'],
                    'voucher_number' => $this->generateVoucherNumber(),
                    'total' => collect($group['details'])->sum('debit'),
                ]);

                foreach ($group['details'] as $detail) {
                    JournalTransactionDetailModel::create([
                        'journal_entry_id' => $journal->id,
                        'chart_of_account_id' => $detail['chart_of_account_id'],
                        'account_number' => $detail['account_number'],
                        'account_name' => $detail['account_name'],
                        'description' => $detail['description'],
                        'debit' => $detail['debit'],
                        'credit' => $detail['credit'],
                    ]);
                }

                DB::commit();

                $this->totalTransactions++;
                $this->totalInsertedRows += count($group['details']);
            } catch (Exception $e) {
                DB::rollBack();

                Log::error(
                    'Failed importing journal transaction: ' . $e->getMessage()
                );

                $this->unimportedRows[] = [
                    'row' => '-',
                    'reason' => 'Database error: ' . $e->getMessage(),
                ];
            }
        }
    }
}
