<?php

namespace App\Imports;

use App\Models\MasterData\Distributor\DistributorShopCommissionModel;
use App\Models\MasterData\Battery\BatteryModel;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;

class DistributorShopBatteryCommissionImport implements ToModel, WithStartRow, WithEvents
{
    private $shopId;
    private $totalRows = 0;
    private $totalImportedRows = 0;
    private $processedRows = 0;
    private $unimportedRows = [];

    public function __construct($shopId)
    {
        $this->shopId = $shopId;
    }

    public function getUnimportedRows()
    {
        return $this->unimportedRows;
    }

    public function getTotalRows()
    {
        return $this->totalRows;
    }

    public function getTotalImportedRows()
    {
        return $this->totalImportedRows;
    }

    public function startRow(): int
    {
        return 4;
    }

    public function model(array $row)
    {
        $excelRowNumber = $this->startRow() + $this->processedRows;
        $this->processedRows++;

        $batteryId = trim($row[0] ?? '');
        $commissionType = trim(strtolower($row[2] ?? ''));
        $commissionAmount = trim($row[3] ?? '');

        // Skip rows where battery ID, commission type, and commission amount are all empty (could be empty template rows)
        if (empty($batteryId) && empty($commissionType) && $commissionAmount === '') {
            return null;
        }

        // Basic validation: must have battery ID, type, and amount
        if (empty($batteryId) || empty($commissionType) || $commissionAmount === '') {
            $this->unimportedRows[] = [
                'row_number' => $excelRowNumber,
                'reason' => 'Required columns (Battery ID, Commission Type, Commission Amount) are missing.',
                'data' => $row,
            ];
            return null;
        }

        // Validate battery existence
        $battery = BatteryModel::find($batteryId);
        if (!$battery) {
            $this->unimportedRows[] = [
                'row_number' => $excelRowNumber,
                'reason' => "Battery with ID {$batteryId} not found.",
                'data' => $row,
            ];
            return null;
        }

        // Validate commission type
        $allowedTypes = ['technician', 'pic', 'pit_stop'];
        if (!in_array($commissionType, $allowedTypes)) {
            $this->unimportedRows[] = [
                'row_number' => $excelRowNumber,
                'reason' => "Commission type '{$commissionType}' is invalid. Allowed types: technician, pic, pit_stop.",
                'data' => $row,
            ];
            return null;
        }

        // Normalize commission amount
        $cleanAmount = str_replace(['.', ','], ['', '.'], $commissionAmount);
        if (!is_numeric($cleanAmount)) {
            $this->unimportedRows[] = [
                'row_number' => $excelRowNumber,
                'reason' => 'Commission amount must be a numeric value.',
                'data' => $row,
            ];
            return null;
        }
        $commissionAmountDouble = (double) $cleanAmount;

        try {
            $commission = DistributorShopCommissionModel::updateOrCreate(
                [
                    'distributor_shop_id' => $this->shopId,
                    'battery_id' => $batteryId,
                    'type' => $commissionType,
                ],
                [
                    'commission' => $commissionAmountDouble,
                ]
            );

            $this->totalImportedRows++;
            return $commission;
        } catch (\Exception $e) {
            Log::error('Error importing distributor shop commission row ' . $excelRowNumber . ': ' . $e->getMessage());
            $this->unimportedRows[] = [
                'row_number' => $excelRowNumber,
                'reason' => 'Failed to save commission record due to database/system error.',
                'data' => $row,
            ];
            return null;
        }
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $worksheet = $event->getDelegate()->getActiveSheet();
                $this->totalRows = max(0, $worksheet->getHighestDataRow() - 3);
            },
        ];
    }
}
