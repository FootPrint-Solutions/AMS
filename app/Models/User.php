<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// TRAITS
use App\Traits\DataTablesTrait;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, DataTablesTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The list of columns in the associated table.
     */
    private static $selectColumns = [
        'id', 'username'
    ];

    /**
     * The menus that the user is allowed to open.
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, "user_menu");
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
        $query = self::query();
        $query->select(self::$selectColumns);

        return self::getAllRows($request, $query, self::$selectColumns);
    }

    /**
     * Checks if the user has a specific permission.
     *
     * This method splits the user's permissions string into an array using the '|' delimiter
     * and checks if the specified permission exists within that array.
     *
     * @param string $permission The permission to check for.
     * @return bool True if the user has the specified permission, false otherwise.
     */
    public function hasPermission($permission)
    {
        $permissions = explode('|', $this->permission);
        return in_array($permission, $permissions);
    }
}
