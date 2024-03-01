<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;

class MenuParent extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menu_parents';

    /**
     * Get the menu parent that includes the menu.
     */
    public function menus()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id');
    }

    /**
     * Get all of the sub menus for the menu.
     */
    public function menuSubs(): HasManyThrough
    {
        return $this->hasManyThrough(MenuSub::class, Menu::class, "parent_id", "menu_id");
    }

    /**
     * Get the updated order and update the order of other menu items within its parent.
     *
     * @param int|null $menuId The ID of the menu positioned after the current menu.
     * @param int $parentId The ID of the parent menu.
     * @param int|null $originalOrder The original order of the menu being moved (optional).
     * @return int The new order of the menu item.
     */
    public function order($menuId, $originalOrder = null): int
    {
        if (is_null($menuId)) {
            // Get latest menu parent position.
            $lastRow = self::orderBy("order", "DESC")
                ->first();

            if (!is_null($originalOrder)) {
                $currentOrder = $originalOrder + 1;
                $currentId = null;

                while ($currentOrder <= $lastRow->order) {
                    // Get current menu based on order.
                    $current = self::where("order", $currentOrder)
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
                        $current = self::where("order", $currentOrder);
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
                        $current = self::where("order", $currentOrder)
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
}
