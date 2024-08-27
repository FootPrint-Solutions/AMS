<?php

namespace App\Imports;

use App\Models\MasterData\Battery\BatteryCodeModel;
use App\Models\MasterData\Battery\BatteryModel;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\BeforeImport;

class BatteryPriceImport implements ToModel, WithStartRow, WithEvents
{
    private $unimportedRows = [];
    private $totalRows = 0;
    private $totalChangedRows = 0;
    private $totalUnchangedRows = 0;

    public function getUnimportedRows()
    {
        return $this->unimportedRows;
    }

    public function getTotalRows()
    {
        return $this->totalRows;
    }

    public function getTotalChangedRows()
    {
        return $this->totalChangedRows;
    }

    public function getTotalUnchangedRows()
    {
        return $this->totalUnchangedRows;
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
     * @return Battery|null
     */
    public function model(array $row)
    {
        // Get new values (to replace).
        $newName = $row[1] ? $row[1] : "";
        $newPrice = $row[14] ? intval($row[14]) : 0;

        // Get battery based on code.
        $code = $row[0];
        $batteryId = BatteryCodeModel::where('code', $code)->first();
        if (!$batteryId) {
            $this->unimportedRows[] = $row;
            return;
        }

        $batteryId = $batteryId->battery_id;

        $battery = BatteryModel::where('id', $batteryId)->first();
        if (!$battery) {
            $this->unimportedRows[] = $row;
            return;
        }

        if ($battery->name != $newName || $battery->price_retail != $newPrice) {
            $battery->name = $newName;
            $battery->price_retail = $newPrice;

            try {
                $battery->saveOrFail();
                $this->totalChangedRows++;
            } catch (\Exception $e) {
                $this->unimportedRows[] = $row;
                Log::error($e);
            }
        } else {
            $this->totalUnchangedRows++;
        }
        return $battery;
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
                $this->totalRows = $worksheet->getHighestDataRow();
            },
        ];
    }
}
