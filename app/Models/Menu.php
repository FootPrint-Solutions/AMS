<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\MenuParent;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
