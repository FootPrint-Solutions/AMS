<?php

namespace App\Imports;

use App\Models\MasterData\Battery\BatteryCodeModel;
use App\Models\MasterData\Battery\BatteryModel;
use App\Models\MasterData\Battery\BatteryPriceModel;
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
        $newPriceBuy = (isset($row[15]) && $row[15] !== '')
            ? intval(str_replace(['.', ','], ['', '.'], $row[15]))
            : null;

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

                if ($battery->price_retail != $newPrice || (!is_null($newPriceBuy) && $battery->price_buy != $newPriceBuy)) {
                    $battery->price_retail = $newPrice;
                    if (!is_null($newPriceBuy)) {
                        $battery->price_buy = $newPriceBuy;
                    }

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

        if ($battery->name != $newName || $battery->price_retail != $newPrice || (!is_null($newPriceBuy) && $battery->price_buy != $newPriceBuy)) {
            $battery->name = $newName;
            $battery->price_retail = $newPrice;
            if (!is_null($newPriceBuy)) {
                $battery->price_buy = $newPriceBuy;
            }

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

        $batteryPrices = BatteryPriceModel::where('battery_id', $batteryId)->get();
        if ($batteryPrices->count() > 0) {
            $batteryPrice = $batteryPrices->first();
            $batteryPrice->price_retail = $newPrice;
            $batteryPrice->save();
        } else {
            $batteryPrice = new BatteryPriceModel();
            $batteryPrice->battery_id = $batteryId;
            $batteryPrice->price_retail = $newPrice;
            $batteryPrice->save();
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
