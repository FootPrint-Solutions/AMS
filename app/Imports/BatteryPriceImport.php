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
    private $previewRows = [];
    private $totalRows = 0;
    private $totalUpdatedRows = 0;
    private $previewOnly = false;
    private $processedRows = 0;

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

    public function getPreviewRows()
    {
        return $this->previewRows;
    }

    public function setPreviewOnly(bool $previewOnly)
    {
        $this->previewOnly = $previewOnly;

        return $this;
    }

    private function appendPreviewRow(array $row, array $payload)
    {
        $this->previewRows[] = array_merge([
            'row_number' => $this->processedRows + 3,
            'id' => $row[0] ?? '',
            'code' => $row[1] ?? '',
            'name' => $row[2] ?? '',
        ], $payload);
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
        $this->processedRows++;

        // Get new values (to replace).
        $newName = $row[2] ? $row[2] : "";
        $newPrice = $row[15] ? intval(str_replace(['.', ','], ['', '.'], $row[15])) : 0;
        $newPriceBuy = (isset($row[16]) && $row[16] !== '')
            ? intval(str_replace(['.', ','], ['', '.'], $row[16]))
            : null;

        // Get battery based on code.
        $batteryId = $row[0];
        $battery = BatteryModel::find($batteryId);
        if (!$battery) {
            if ($batteryId == "") {
                $this->unimportedRows[] = $row;
                $this->appendPreviewRow($row, [
                    'action' => 'skipped',
                    'reason' => 'ID kosong, tidak dapat mencari data battery.',
                    'current_name' => '-',
                    'current_price_retail' => '-',
                    'current_price_buy' => '-',
                    'new_name' => $newName,
                    'new_price_retail' => $newPrice,
                    'new_price_buy' => $newPriceBuy,
                    'changes' => [],
                ]);
                return;
            } else {
                $battery = BatteryModel::where('name', $row[2])->first();
                if (!$battery) {
                    $this->unimportedRows[] = $row;
                    $this->appendPreviewRow($row, [
                        'action' => 'failed',
                        'reason' => 'Battery tidak ditemukan berdasarkan ID maupun nama.',
                        'current_name' => '-',
                        'current_price_retail' => '-',
                        'current_price_buy' => '-',
                        'new_name' => $newName,
                        'new_price_retail' => $newPrice,
                        'new_price_buy' => $newPriceBuy,
                        'changes' => [],
                    ]);
                    return;
                }

                $changes = [];
                if ($battery->name != $newName) {
                    $changes['name'] = [$battery->name, $newName];
                }
                if ($battery->price_retail != $newPrice) {
                    $changes['price_retail'] = [$battery->price_retail, $newPrice];
                }
                if (!is_null($newPriceBuy) && $battery->price_buy != $newPriceBuy) {
                    $changes['price_buy'] = [$battery->price_buy, $newPriceBuy];
                }

                if (!empty($changes)) {
                    if (!$this->previewOnly) {
                        $battery->name = $newName;
                        $battery->price_retail = $newPrice;
                        if (!is_null($newPriceBuy)) {
                            $battery->price_buy = $newPriceBuy;
                        }

                        try {
                            $battery->saveOrFail();

                            $code = BatteryCodeModel::firstOrNew(['battery_id' => $battery->id]);
                            $code->code = $row[1];
                            $code->save();

                            $this->totalUpdatedRows++;
                        } catch (\Exception $e) {
                            $this->unimportedRows[] = $row;
                            Log::error($e);
                        }
                    }

                    $this->appendPreviewRow($row, [
                        'action' => $this->previewOnly ? 'preview' : 'updated',
                        'reason' => $this->previewOnly ? 'Perubahan terdeteksi dan akan disimpan jika dikonfirmasi.' : 'Perubahan berhasil disimpan.',
                        'current_name' => $battery->name,
                        'current_price_retail' => $battery->price_retail,
                        'current_price_buy' => $battery->price_buy,
                        'new_name' => $newName,
                        'new_price_retail' => $newPrice,
                        'new_price_buy' => $newPriceBuy,
                        'changes' => $changes,
                    ]);
                } else {
                    $this->unimportedRows[] = $row;
                    $this->appendPreviewRow($row, [
                        'action' => 'skipped',
                        'reason' => 'Tidak ada perubahan data.',
                        'current_name' => $battery->name,
                        'current_price_retail' => $battery->price_retail,
                        'current_price_buy' => $battery->price_buy,
                        'new_name' => $newName,
                        'new_price_retail' => $newPrice,
                        'new_price_buy' => $newPriceBuy,
                        'changes' => $changes,
                    ]);
                }
                return $battery;
            }
        }

        $batteryId = $batteryId;
        $battery = BatteryModel::where('id', $batteryId)->first();
        if (!$battery) {
            $this->unimportedRows[] = $row;
            $this->appendPreviewRow($row, [
                'action' => 'failed',
                'reason' => 'Battery tidak ditemukan.',
                'current_name' => '-',
                'current_price_retail' => '-',
                'current_price_buy' => '-',
                'new_name' => $newName,
                'new_price_retail' => $newPrice,
                'new_price_buy' => $newPriceBuy,
                'changes' => [],
            ]);
            return;
        }

        $changes = [];
        if ($battery->name != $newName) {
            $changes['name'] = [$battery->name, $newName];
        }
        if ($battery->price_retail != $newPrice) {
            $changes['price_retail'] = [$battery->price_retail, $newPrice];
        }
        if (!is_null($newPriceBuy) && $battery->price_buy != $newPriceBuy) {
            $changes['price_buy'] = [$battery->price_buy, $newPriceBuy];
        }

        if (!empty($changes)) {
            if (!$this->previewOnly) {
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
            }

            $this->appendPreviewRow($row, [
                'action' => $this->previewOnly ? 'preview' : 'updated',
                'reason' => $this->previewOnly ? 'Perubahan terdeteksi dan akan disimpan jika dikonfirmasi.' : 'Perubahan berhasil disimpan.',
                'current_name' => $battery->name,
                'current_price_retail' => $battery->price_retail,
                'current_price_buy' => $battery->price_buy,
                'new_name' => $newName,
                'new_price_retail' => $newPrice,
                'new_price_buy' => $newPriceBuy,
                'changes' => $changes,
            ]);
        } else {
            $this->unimportedRows[] = $row;
            $this->appendPreviewRow($row, [
                'action' => 'skipped',
                'reason' => 'Tidak ada perubahan data.',
                'current_name' => $battery->name,
                'current_price_retail' => $battery->price_retail,
                'current_price_buy' => $battery->price_buy,
                'new_name' => $newName,
                'new_price_retail' => $newPrice,
                'new_price_buy' => $newPriceBuy,
                'changes' => $changes,
            ]);
        }

        if (!$this->previewOnly) {
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

    public function getPreviewData()
    {
        return [
            'total_rows' => $this->getTotalRows(),
            'total_updated_rows' => $this->getTotalUpdatedRows(),
            'unimported_rows' => $this->getUnimportedRows(),
            'preview_rows' => $this->getPreviewRows(),
        ];
    }
}
