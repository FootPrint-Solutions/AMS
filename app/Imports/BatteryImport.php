<?php

namespace App\Imports;

use App\Models\MasterData\Battery\BatteryBrandModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatterySizeCategoryModel;
use App\Models\MasterData\Battery\BatteryTechnologyModel;
use App\Models\MasterData\Battery\BatteryUsageTypeModel;
use App\Models\MasterData\Battery\BatterySubbrandCategoryModel;
use App\Models\MasterData\Battery\BatteryCodeModel;
use App\Models\MasterData\Battery\BatteryPriceModel;


use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Support\Facades\Log;

class BatteryImport implements ToModel, WithStartRow, WithEvents
{
    private $unimportedRows = [];
    private $totalRows = 0;
    private $totalInsertedRows = 0;
    private $processedRows = 0;

    public function getUnimportedRows()
    {
        return $this->unimportedRows;
    }

    public function getTotalRows()
    {
        return $this->totalRows;
    }

    public function getTotalInsertedRows()
    {
        return $this->totalInsertedRows;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 4;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $excelRowNumber = $this->startRow() + $this->processedRows;
        $this->processedRows++;

        try {
            $normalizedRow = $this->normalizeRow($row);

            // Validate the row before processing.
            if (!$this->validateRow($normalizedRow)) {
                // Add invalid row to unimportedRows array.
                $this->pushFailedRow($excelRowNumber, $normalizedRow, 'Beberapa kolom wajib belum terisi.');
                return;
            }

            // Create or find related models
            $brand = BatteryBrandModel::firstOrCreate(['name' => $normalizedRow['brand']]);
            $sizeCategoryId = null;
            if ($this->hasValue($normalizedRow['size_category'])) {
                $sizeCategory = BatterySizeCategoryModel::firstOrCreate(['name' => $normalizedRow['size_category']]);
                $sizeCategoryId = $sizeCategory->id;
            }
            $usageType = BatteryUsageTypeModel::firstOrCreate(['name' => $normalizedRow['usage_type']]);
            $technology = BatteryTechnologyModel::firstOrCreate(['name' => $normalizedRow['technology']]);
            $subbrandCategory = BatterySubbrandCategoryModel::firstOrCreate(['name' => $normalizedRow['subbrand_category']]);

            // Clean and prepare data for battery creation
            $standardCca = $this->normalizeNumber($normalizedRow['standard_cca'], 0);
            $capacity = $this->normalizeNumber($normalizedRow['capacity'], 0);
            $warranty = $this->normalizeWarranty($normalizedRow['warranty']);
            $priceRetail = $this->normalizePrice($normalizedRow['retail_price']);
            $priceBuy = $this->normalizePrice($normalizedRow['buy_price']);

            // Create the Battery model
            $battery = BatteryModel::create([
                'name' => $normalizedRow['name'],
                'name_alternate' => $normalizedRow['alternate_name'],
                'brand_id' => $brand->id,
                'subbrand_category_id' => $subbrandCategory->id,
                'usage_type_id' => $usageType->id,
                'size_category_id' => $sizeCategoryId,
                'technology_id' => $technology->id,
                'dimension_length' => $this->normalizeNumber($normalizedRow['dimension_length'], 0),
                'dimension_width' => $this->normalizeNumber($normalizedRow['dimension_width'], 0),
                'dimension_height' => $this->normalizeNumber($normalizedRow['dimension_height'], 0),
                'standard_cca' => $standardCca,
                'capacity' => $capacity,
                'warranty' => $warranty,
                'price_retail' => $priceRetail,
                'price_buy' => $priceBuy,
            ]);

            $code = new BatteryCodeModel();
            $code->code = BatteryCodeModel::generateCode();
            $code->battery_id = $battery->id;
            $code->save();

            $batteryPrices = BatteryPriceModel::where('battery_id', $battery->id)->first();
            if (!$batteryPrices) {
                $batteryPrices = new BatteryPriceModel();
                $batteryPrices->battery_id = $battery->id;
            }
            $batteryPrices->price_retail = $priceRetail;
            $batteryPrices->save();

            $this->totalInsertedRows++;
            return $battery;
        } catch (\Exception $e) {
            // Log the error and add the row to unimportedRows array.
            Log::error('Error importing battery row ' . $excelRowNumber . ': ' . $e->getMessage());
            $this->pushFailedRow($excelRowNumber, $this->normalizeRow($row), 'Terjadi kesalahan saat menyimpan data.');
            return null;
        }
    }

    /**
     * Normalize the imported row into named columns.
     *
     * @param array $row
     * @return array
     */
    private function normalizeRow(array $row): array
    {
        return [
            'id' => $row[0] ?? '',
            'name' => trim((string) ($row[1] ?? '')),
            'alternate_name' => trim((string) ($row[2] ?? '')),
            'brand' => trim((string) ($row[3] ?? '')),
            'subbrand_category' => trim((string) ($row[4] ?? '')),
            'usage_type' => trim((string) ($row[5] ?? '')),
            'size_category' => trim((string) ($row[6] ?? '')),
            'technology' => trim((string) ($row[7] ?? '')),
            'dimension_length' => $row[8] ?? 0,
            'dimension_width' => $row[9] ?? 0,
            'dimension_height' => $row[10] ?? 0,
            'standard_cca' => $row[11] ?? 0,
            'capacity' => $row[12] ?? 0,
            'warranty' => $row[13] ?? 0,
            'retail_price' => $row[14] ?? null,
            'buy_price' => $row[15] ?? null,
        ];
    }

    /**
     * Build and store a readable failed-row entry.
     *
     * @param int $rowNumber
     * @param array $row
     * @param string $reason
     * @return void
     */
    private function pushFailedRow(int $rowNumber, array $row, string $reason): void
    {
        $this->unimportedRows[] = [
            'row_number' => $rowNumber,
            'reason' => $reason,
            'data' => $row,
        ];
    }

    /**
     * Check whether a column contains a real value.
     *
     * @param mixed $value
     * @return bool
     */
    private function hasValue($value): bool
    {
        $value = trim((string) $value);

        return $value !== '' && $value !== '-';
    }

    /**
     * Normalize numeric values from spreadsheet cells.
     *
     * @param mixed $value
     * @param mixed $default
     * @return float|int|mixed
     */
    private function normalizeNumber($value, $default = null)
    {
        if (!$this->hasValue($value)) {
            return $default;
        }

        $normalized = trim((string) $value);

        if (strpos($normalized, ',') !== false) {
            $normalized = str_replace('.', '', $normalized);
            $normalized = str_replace(',', '.', $normalized);
        } else {
            $normalized = str_replace(',', '', $normalized);
        }

        return is_numeric($normalized) ? $normalized + 0 : $default;
    }

    /**
     * Normalize the warranty value into a clean number.
     *
     * @param mixed $value
     * @return int|float|null
     */
    private function normalizeWarranty($value)
    {
        if (!$this->hasValue($value)) {
            return 0;
        }

        $cleanValue = strtolower(trim((string) $value));
        $cleanValue = str_replace('bulan', '', $cleanValue);

        return $this->normalizeNumber($cleanValue, 0);
    }

    /**
     * Normalize price values.
     *
     * @param mixed $value
     * @return float|int|null
     */
    private function normalizePrice($value)
    {
        return $this->normalizeNumber($value, null);
    }


    /**
     * Validate the row data
     *
     * @param array $row
     * @return bool
     */
    private function validateRow(array $row): bool
    {
        if (
            empty($row['name']) ||
            empty($row['brand']) ||
            empty($row['subbrand_category']) ||
            empty($row['usage_type']) ||
            empty($row['technology'])
        ) {
            return false;
        }

        return true;
    }

    /**
     * Register events
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $worksheet = $event->getDelegate()->getActiveSheet();
                $this->totalRows = $worksheet->getHighestDataRow() - 3;
            },
        ];
    }
}
