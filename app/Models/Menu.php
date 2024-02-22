<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\MenuParent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menu';

    /**
     * Get the menu parent that includes the menu.
     */
    public function menuParent()
    {
        return $this->belongsTo(MenuParent::class, 'id_parent', 'id');
    }

    /**
     * Get the sub menus for the menu.
     */
    public function menuSubs(): HasMany
    {
        return $this->hasMany(MenuSub::class, "id_menu", "id");
    }

    /**
     * 
     */
    public function order($menuId, $parentId): int
    {
        if (is_null($menuId)) {
            // Get latest menu position in current menu parent.
            $lastRow = self::where("id_parent", $parentId)
                ->orderBy("order", "DESC")
                ->first();

            // Add new menu to the last position.
            return $lastRow ? $lastRow->order + 1 : 1;
        } else {
            // Move all menu to the correct position.
            $destinationPosition = self::where("id", $menuId)->first();

            self::where("order", ">=", $destinationPosition->order)
                ->update([
                    "order" => DB::raw("`order` + 1"),
                    "updated_at" => now()->toDateTimeString()
                ]);

            // Add new menu to the position.
            return $destinationPosition->order;
        }
    }
}
