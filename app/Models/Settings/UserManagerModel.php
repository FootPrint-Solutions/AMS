<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


// TRAITS
use App\Traits\DataTablesTrait;

class UserManagerModel extends Model
{
    use HasFactory, DataTablesTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    // The attributes that are mass assignable.
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'level',
    ];


    /**
     * Get all data for DataTables.
     * 
     * @param \Illuminate\Http\Request $request The POST request obtained (for DataTables configuration).
     * @return array Associative array containing data for DataTables display.
     */
    public static function allForDataTables($request)
    {
        // Set the list of select and search columns.
        $selectColumns = ['id', 'name', 'username', 'email', 'level'];
        $searchColumns = ['name', 'username', 'email'];

        // Build the query to obtain all rows.
        $query = self::query();
        $query->select($selectColumns);

        return self::getAllRows($request, $query, $selectColumns, $searchColumns, ['column' => 'id', 'direction' => 'desc']);
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
