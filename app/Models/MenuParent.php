<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
}
