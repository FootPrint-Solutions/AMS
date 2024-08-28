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
    private $totalUpdatedRows = 0;

    public function getUnimportedRows()
    {
        return $this->unimportedRows;
    }

    public function getTotalRows()
    {
        return $this->totalRows;
    }

    public function getTotalUpdatedRows()
    {
        return $this->totalUpdatedRows;
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
        $newPrice = $row[14] ? intval(str_replace(['.', ','], ['', '.'], $row[14])) : 0;

        // Get battery based on code.
        $code = $row[0];
        $batteryId = BatteryCodeModel::where('code', $code)->first();
        if (!$batteryId) {
            if ($code == "") {
                $this->unimportedRows[] = $row;
                return;
            } else {
                //
                $battery = BatteryModel::where('name', $row[1])->first();
                if (!$battery) {
                    $this->unimportedRows[] = $row;
                    return;
                }

                if ($battery->price_retail != $newPrice) {
                    $battery->price_retail = $newPrice;

                    try {
                        $battery->saveOrFail();

                        $code = BatteryCodeModel::firstOrNew(['battery_id' => $battery->id]);
                        $code->code = $row[0];
                        $code->save();

                        $this->totalUpdatedRows++;
                    } catch (\Exception $e) {
                        $this->unimportedRows[] = $row;
                        Log::error($e);
                    }
                } else {
                    $this->unimportedRows[] = $row;
                }
                return $battery;
            }
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
                $this->totalUpdatedRows++;
            } catch (\Exception $e) {
                $this->unimportedRows[] = $row;
                Log::error($e);
            }
        } else {
            $this->unimportedRows[] = $row;
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
                $this->totalRows = $worksheet->getHighestDataRow() - 3;
            },
        ];
    }
}
