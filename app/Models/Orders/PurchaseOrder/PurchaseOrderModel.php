<?php

namespace App\Models\Orders\PurchaseOrder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

// TRAITS
use App\Traits\DataTablesTrait;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as Logger;

// MODELS
use App\Models\Inventory\InventoryModel;
use App\Models\Inventory\InventoryDetailModel;
use App\Models\Inventory\InventoryRecycleModel;
use App\Models\Inventory\InventoryRecycleDetailModel;

class PurchaseOrderModel extends Model implements Auditable
{
    use HasFactory, SoftDeletes, DataTablesTrait, AuditableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_order_number',
        'invoice_number',
        'date',
        'vendor_id',
        'vendor_type',
        'ship_to_id',
        'ship_to_type',
        'discount_price',
        'subtotal',
        'total',
        'payment_status',
        'status',
        'address',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'discount_price' => 'double',
        'subtotal' => 'double',
        'total' => 'double',
    ];

    /**
     * Get the vendor associated with the purchase order.
     */
    public function vendor(): BelongsTo
    {
        return $this->morphTo();
    }

    /**
     * Get the ship to associated with the purchase order.
     */
    public function shipTo(): BelongsTo
    {
        return $this->morphTo();
    }

    /**
     * Get all of the batteries of the purchase order.
     */
    public function batteries(): HasMany
    {
        return $this->hasMany(PurchaseOrderBatteryModel::class, 'purchase_order_id')
            ->with('battery');
    }


    /**
     * Get all of the batteries of the purchase order.
     */
    public function details(): HasMany
    {
        return $this->hasMany(PurchaseOrderBatteryModel::class, 'purchase_order_id')
            ->with('battery');
    }

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        $query = self::with(['vendor', 'shipTo']);

        return self::dataTablesQuery($query, $request);
    }

    /**
     * Generate purchase order number.
     *
     * @return string
     */
    public static function generatePurchaseOrderNumber()
    {
        $prefix = 'KP';
        $yearShort = date('y');
        $yearFull = date('Y');
        $month = date('m');

        // Get the latest purchase order for the current month and year
        $latestOrder = self::whereYear('created_at', $yearFull)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($latestOrder && strlen($latestOrder->purchase_order_number) >= 6) {
            // Extract the last 6 digits as the sequence number
            $lastNumber = (int) substr($latestOrder->purchase_order_number, -6);
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return $prefix . $yearShort . $month . $newNumber;
    }

    public function supplier(): BelongsTo
    {
        return $this->morphTo('supplier', 'vendor_type', 'vendor_id');
    }

    public function ship_to(): BelongsTo
    {
        return $this->morphTo('ship_to', 'ship_to_type', 'ship_to_id');
    }

    public static function sendToInventorySystem($purchaseOrderIds)
    {
        $purchaseOrders = self::with('batteries')->whereIn('id', $purchaseOrderIds)->get();

        $logMessage = '';
        $logMessage .= "Function sendToInventorySystem: Processing Purchase Order IDs: " . implode(', ', $purchaseOrderIds) . "\n";
        foreach ($purchaseOrders as $purchaseOrder) {
            $logMessage .= "Function sendToInventorySystem: Processing Purchase Order ID " . $purchaseOrder->id . "\n";
            foreach ($purchaseOrder->batteries as $battery) {
                $logMessage .= "Function sendToInventorySystem: Processing Battery ID " . $battery->battery_id . "\n";
                $batteryId = $battery->battery_id;
                $batteryType = $battery->source ?? 'regular';
                $batteryCode = $battery->battery_production_code ?? null;
                $shopId = $purchaseOrder->ship_to_id;

                if ($batteryType == 'regular') {
                    $inventory = InventoryModel::where('battery_id', $batteryId)->first();
                    if ($inventory) {
                        $inventory->stock += $battery->quantity;
                        $inventory->save();
                    } else {
                        $inventory = InventoryModel::create([
                            'battery_id' => $batteryId,
                            'code' => $batteryCode,
                            'stock' => $battery->quantity,
                        ]);
                    }

                    InventoryDetailModel::create([
                        'inventory_id' => $inventory->id,
                        'distributor_shop_id' => $shopId,
                        'battery_id' => $batteryId,
                        'type' => 'in',
                        'reference' => 'purchase_order',
                        'quantity' => $battery->quantity,
                        'sold' => 0,
                        'note' => null,
                        'reference_id' => $purchaseOrder->id,
                        'reference_type' => PurchaseOrderModel::class,
                    ]);

                    $logMessage .= "Function sendToInventorySystem: Processed Battery ID " . $battery->battery_id . "\n";
                } else if ($batteryType == 'recycle') {
                    $inventoryRecycle = InventoryRecycleModel::where('battery_recycle_id', $batteryId)->first();
                    if ($inventoryRecycle) {
                        $inventoryRecycle->stock += $battery->quantity;
                        $inventoryRecycle->save();
                    } else {
                        $inventoryRecycle = InventoryRecycleModel::create([
                            'battery_id' => NULL,
                            'battery_recycle_id' => $batteryId,
                            'code' => $batteryCode,
                            'stock' => $battery->quantity,
                        ]);
                    }

                    InventoryRecycleDetailModel::create([
                        'inventory_id' => $inventoryRecycle->id,
                        'distributor_shop_id' => $shopId,
                        'battery_id' => NULL,
                        'battery_recycle_id' => $batteryId,
                        'type' => 'in',
                        'reference' => 'Purchase Order',
                        'quantity' => $battery->quantity,
                        'reference_id' => $purchaseOrder->id,
                        'reference_type' => PurchaseOrderModel::class,
                    ]);

                    $logMessage .= "Function sendToInventorySystem: Processed Recycle Battery ID " . $battery->battery_id . "\n";
                } else {
                    Log::warning('Unknown battery type for purchase order battery', [
                        'purchase_order_id' => $purchaseOrder->id,
                        'battery_id' => $batteryId,
                        'type' => $batteryType,
                    ]);
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unknown battery type found.'
                    ], 400);
                }
            }

            $logMessage .= "Function sendToInventorySystem: Completed processing Purchase Order ID " . $purchaseOrder->id . " - Purchase Order Number " . $purchaseOrder->purchase_order_number . "\n";
            Logger::info($logMessage);
            $logMessage = '';
        }
    }

    public static function sendToInventorySystemPurchaseBilling($purchaseOrderIds)
    {
        $purchaseOrders = self::with('batteries')->whereIn('id', $purchaseOrderIds)->get();

        $logMessage = '';
        $logMessage = "Function sendToInventorySystemPurchaseBilling: Processing Purchase Order IDs: " . implode(', ', $purchaseOrderIds) . "\n";
        foreach ($purchaseOrders as $purchaseOrder) {
            $logMessage .= "Function sendToInventorySystemPurchaseBilling: Processing Purchase Order ID " . $purchaseOrder->id . "\n";
            foreach ($purchaseOrder->batteries as $battery) {
                $logMessage .= "Function sendToInventorySystemPurchaseBilling: Processing Battery ID " . $battery->battery_id . "\n";
                $batteryId = $battery->battery_id;
                $batteryType = $battery->source ?? 'regular';
                $batteryCode = $battery->battery_production_code ?? null;
                $shopId = $purchaseOrder->ship_to_id;
                if ($batteryType == 'regular') {
                    $inventory = InventoryModel::where('battery_id', $batteryId)->first();
                    if ($inventory) {
                        $inventory->stock += $battery->quantity;
                        $inventory->save();
                    } else {
                        $inventory = InventoryModel::create([
                            'battery_id' => $batteryId,
                            'code' => $batteryCode,
                            'stock' => $battery->quantity,
                        ]);
                    }

                    InventoryDetailModel::create([
                        'inventory_id' => $inventory->id,
                        'distributor_shop_id' => $shopId,
                        'battery_id' => $batteryId,
                        'type' => 'in',
                        'reference' => 'purchase_order',
                        'quantity' => $battery->quantity,
                        'sold' => 0,
                        'note' => null,
                        'reference_id' => $purchaseOrder->id,
                        'reference_type' => PurchaseOrderModel::class,
                    ]);

                    $logMessage .= "Function sendToInventorySystemPurchaseBilling: Processed Battery ID " . $battery->battery_id . "\n";
                } else if ($batteryType == 'recycle') {
                    $inventoryRecycle = InventoryRecycleModel::where('battery_recycle_id', $batteryId)->first();
                    if ($inventoryRecycle) {
                        $inventoryRecycle->stock += $battery->quantity;
                        $inventoryRecycle->save();
                    } else {
                        $inventoryRecycle = InventoryRecycleModel::create([
                            'battery_id' => NULL,
                            'battery_recycle_id' => $batteryId,
                            'code' => $batteryCode,
                            'stock' => $battery->quantity,
                        ]);
                    }

                    InventoryRecycleDetailModel::create([
                        'inventory_id' => $inventoryRecycle->id,
                        'distributor_shop_id' => $shopId,
                        'battery_id' => NULL,
                        'battery_recycle_id' => $batteryId,
                        'type' => 'in',
                        'reference' => 'Purchase Order',
                        'quantity' => $battery->quantity,
                        'reference_id' => $purchaseOrder->id,
                        'reference_type' => PurchaseOrderModel::class,
                    ]);

                    $logMessage .= "Function sendToInventorySystemPurchaseBilling: Processed Recycle Battery ID " . $battery->battery_id . "\n";
                } else {
                    Log::warning('Unknown battery type for purchase order battery', [
                        'purchase_order_id' => $purchaseOrder->id,
                        'battery_id' => $batteryId,
                        'type' => $batteryType,
                    ]);
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unknown battery type found.'
                    ], 400);
                }
            }

            $logMessage .= "Function sendToInventorySystemPurchaseBilling: Completed processing Purchase Order ID " . $purchaseOrder->id . " - Purchase Order Number " . $purchaseOrder->purchase_order_number . "\n";
            Logger::info($logMessage);
            $logMessage = '';
        }
    }

    public static function sendToInventorySystemSalesBilling($purchaseOrderIds)
    {
        $purchaseOrders = self::with('batteries')->whereIn('id', $purchaseOrderIds)->get();

        $logMessage = '';
        $logMessage = "Function sendToInventorySystemSalesBilling: Processing Purchase Order IDs: " . implode(', ', $purchaseOrderIds) . "\n";
        foreach ($purchaseOrders as $purchaseOrder) {
            $logMessage .= "Function sendToInventorySystemSalesBilling: Processing Purchase Order ID " . $purchaseOrder->id . "\n";
            foreach ($purchaseOrder->batteries as $battery) {
                $logMessage .= "Function sendToInventorySystemSalesBilling: Processing Battery ID " . $battery->battery_id . "\n";
                $batteryId = $battery->battery_id;
                $batteryType = $battery->source ?? 'regular';
                $batteryCode = $battery->battery_production_code ?? null;
                $shopId = $purchaseOrder->ship_to_id;
                if ($batteryType == 'regular') {
                    $inventory = InventoryModel::where('battery_id', $batteryId)->first();
                    if ($inventory) {
                        $inventory->stock -= $battery->quantity;
                        $inventory->save();
                    } else {
                        $inventory = InventoryModel::create([
                            'battery_id' => $batteryId,
                            'code' => $batteryCode,
                            'stock' => $battery->quantity,
                        ]);
                    }

                    InventoryDetailModel::create([
                        'inventory_id' => $inventory->id,
                        'distributor_shop_id' => $shopId,
                        'battery_id' => $batteryId,
                        'type' => 'out',
                        'reference' => 'purchase_order',
                        'quantity' => $battery->quantity,
                        'sold' => 0,
                        'note' => null,
                        'reference_id' => $purchaseOrder->id,
                        'reference_type' => PurchaseOrderModel::class,
                    ]);

                    $logMessage .= "Function sendToInventorySystemSalesBilling: Processed Battery ID " . $battery->battery_id . "\n";
                } else if ($batteryType == 'recycle') {
                    $inventoryRecycle = InventoryRecycleModel::where('battery_recycle_id', $batteryId)->first();
                    if ($inventoryRecycle) {
                        $inventoryRecycle->stock -= $battery->quantity;
                        $inventoryRecycle->save();
                    } else {
                        $inventoryRecycle = InventoryRecycleModel::create([
                            'battery_id' => NULL,
                            'battery_recycle_id' => $batteryId,
                            'code' => $batteryCode,
                            'stock' => $battery->quantity,
                        ]);
                    }

                    InventoryRecycleDetailModel::create([
                        'inventory_id' => $inventoryRecycle->id,
                        'distributor_shop_id' => $shopId,
                        'battery_id' => NULL,
                        'battery_recycle_id' => $batteryId,
                        'type' => 'out',
                        'reference' => 'Purchase Order',
                        'quantity' => $battery->quantity,
                        'reference_id' => $purchaseOrder->id,
                        'reference_type' => PurchaseOrderModel::class,
                    ]);

                    $logMessage .= "Function sendToInventorySystemSalesBilling: Processed Recycle Battery ID " . $battery->battery_id . "\n";
                } else {
                    Log::warning('Unknown battery type for purchase order battery', [
                        'purchase_order_id' => $purchaseOrder->id,
                        'battery_id' => $batteryId,
                        'type' => $batteryType,
                    ]);
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unknown battery type found.'
                    ], 400);
                }
            }

            $logMessage .= "Function sendToInventorySystemSalesBilling: Completed processing Purchase Order ID " . $purchaseOrder->id . " - Purchase Order Number " . $purchaseOrder->purchase_order_number . "\n";
            Logger::info($logMessage);
            $logMessage = '';
        }
    }
}
