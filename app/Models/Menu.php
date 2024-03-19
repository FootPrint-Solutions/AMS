<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\MenuParent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

// TRAITS
use App\Traits\DataTablesTrait;

class Menu extends Model
{
    use HasFactory, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menus';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['order'];

    /**
     * The list of columns in the associated table.
     * 
     * @var array<string>
     */
    private static $selectColumns = ['menus.id', 'menus.name AS menu_name', 'menu_parents.name AS menu_parent_name', 'menus.hide'];

    /**
     * Get the menu parent that includes the menu.
     */
    public function menuParent()
    {
        return $this->belongsTo(MenuParent::class, 'parent_id', 'id');
    }

    /**
     * Get the sub menus for the menu.
     */
    public function menuSubs(): HasMany
    {
        return $this->hasMany(MenuSub::class, "menu_id", "id");
    }

    /**
     * Get the updated order and update the order of other menu items within its parent.
     *
     * @param int|null $menuId The ID of the menu positioned after the current menu.
     * @param int $parentId The ID of the parent menu.
     * @param int|null $originalOrder The original order of the menu being moved (optional).
     * @return int The new order of the menu item.
     */
    public function order($menuId, $parentId, $originalOrder = null): int
    {
        if (is_null($menuId)) {
            // Get latest menu position in current menu parent.
            $lastRow = self::where("parent_id", $parentId)
                ->orderBy("order", "DESC")
                ->first();

            if (!is_null($originalOrder)) {
                $currentOrder = $originalOrder + 1;
                $currentId = null;

                while ($currentOrder <= $lastRow->order) {
                    // Get current menu based on order.
                    $current = self::where("parent_id", $parentId)
                        ->where("order", $currentOrder)
                        ->first();

                    // Update the current menu order.
                    $current->update(["order" => $currentOrder - 1]);
                    $currentOrder = $currentOrder + 1;
                }
                return $lastRow ? $lastRow->order : 1;
            }

            // Add new menu to the last position.
            return $lastRow ? $lastRow->order + 1 : 1;
        } else {
            // Move all menu to the correct position.
            $destinationPosition = self::where("id", $menuId)->first();

            // Updating order process.
            if (!is_null($originalOrder)) {
                if ($originalOrder > $destinationPosition->order) {
                    // Moving up
                    $currentOrder = $destinationPosition->order;
                    $currentId = null;

                    while ($currentOrder < $originalOrder) {
                        // Get current menu based on order.
                        $current = self::where("parent_id ", $parentId)
                            ->where("order", $currentOrder);
                        if ($currentId !== null) {
                            $current->whereNotIn("id", [$currentId]);
                        }
                        $current = $current->first();

                        // Update the current menu order.
                        $current->update(["order" => $currentOrder + 1]);
                        $currentId = $current->id;
                        $currentOrder = $currentOrder + 1;
                    }
                } else {
                    // Moving down
                    $currentOrder = $originalOrder + 1;
                    $currentId = null;

                    while ($currentOrder < $destinationPosition->order) {
                        // Get current menu based on order.
                        $current = self::where("parent_id", $parentId)
                            ->where("order", $currentOrder)
                            ->first();

                        // Update the current menu order.
                        $current->update(["order" => $currentOrder - 1]);
                        $currentOrder = $currentOrder + 1;
                    }

                    return $destinationPosition->order - 1;
                }
            } else {
                // When creating new menu, update all menus after the new menu.
                self::where("order", ">=", $destinationPosition->order)
                    ->update([
                        "order" => DB::raw("`order` + 1"),
                        "updated_at" => now()->toDateTimeString()
                    ]);
            }

            // Add new menu to the position.
            return $destinationPosition->order;
        }
    }

    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Build the query to obtain all rows.
        $query = self::query()
            ->join('menu_parents', 'menu_parents.id', '=', 'menus.parent_id');
        $query->select(self::$selectColumns);

        return self::getAllRows($request, $query, self::$selectColumns);
    }

    /**
     * Update order of all menus which is ordered after the deleted menu inside its menu parent.
     * 
     * @param int $parentId Id of the menu parent.
     * @param int $originalOrder The order of the deleted menu.
     */
    public static function updateOrder($parentId, $originalOrder)
    {
        $maxOrder = self::where("parent_id", $parentId)
            ->orderBy('order', 'desc')
            ->first()
            ->order;
        $currentOrder = $originalOrder + 1;

        while ($currentOrder <= $maxOrder) {
            // Get current menu based on order.
            $current = self::where("parent_id", $parentId)
                ->where("order", $currentOrder)
                ->first();

            // Update the current menu order.
            $current->update(["order" => $currentOrder - 1]);
            $currentOrder = $currentOrder + 1;
        }
    }
}
