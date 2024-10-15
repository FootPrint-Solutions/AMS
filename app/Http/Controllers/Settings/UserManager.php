<?php

namespace App\Http\Controllers\Settings;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


// MODEL
use App\Models\Settings\UserManagerModel;
use App\Models\MenuParent;
use App\Models\Menu;

class UserManager extends Controller
{
    private $title = "User Manager";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'Settings.UserManager.index',
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Show the form for creating resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            "Settings.UserManager.create",
            getIndexData(
                $this->title,
                array(
                    'menu_parent' => MenuParent::with('menus')->orderBy('order', 'asc')->get(),
                )
            )
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Master\Customer  $Customer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('Settings.UserManager.create', array(
            'title' => $this->title,
            'data' => array(
                'profile' => UserManagerModel::find($id)->toArray(),
                'menu_parent' => MenuParent::with('menus')->orderBy('order', 'asc')->get(),
            )
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");

        // Get User data (rows and count).
        $data = UserManagerModel::allForDataTables($request);

        // Set rows to be displayed in User table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->username;
            $row[] = $key->email;
            $row[] = $key->level;
            $row[] = "<button class='btn btn-outline-warning' onclick='goToPage(\"/user-manager/edit/$key->id\")' title='Edit'><i class='fa fa-pencil'></i></button>";
            $row[] = "<button class='btn btn-outline-danger' onclick='sendDestroyRequest(\"$key->id\", \"/user-manager/destroy\", function() {reloadTable();})' title='Delete'><i class='fa fa-trash'></i></i></button>";
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => UserManagerModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->input('id');
            UserManagerModel::destroy($id);

            DB::commit();
            $status = true;
            return getResponseData(
                $status,
                $status ? "The customer was successfully updated!" : "Failed to update the customer!"
            );
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => "error",
                "message" => "Failed to update user. " . $e->getMessage()
            ]);
        }
    }

    /**
     * Update the user information based on the provided request data.
     *
     * This method begins a database transaction, retrieves the user ID and role from the request,
     * and attempts to update the user's level in the database. If the update is successful, the
     * transaction is committed and a success response is returned. If an exception occurs, the
     * transaction is rolled back and an error response is returned.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing user data.
     * @return \Illuminate\Http\JsonResponse The response indicating the result of the update operation.
     */
    public function update(Request $request)
    {
        DB::beginTransaction();
        $id = $request->input('id');
        $permission = '';

        foreach ($request->input('permission') as $key) {
            $permission .= $key . '|';
        }

        $data = array(
            'level' => $request->input('role'),
            'permission' => $permission
        );

        try {
            UserManagerModel::where('id', $id)->update($data);
            DB::commit();

            $status = true;

            return getResponseData(
                $status,
                $status ? "The customer was successfully updated!" : "Failed to update the customer!"
            );
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                "status" => "error",
                "message" => "Failed to update user. " . $e->getMessage()
            ]);
        }
    }



    /**
     * Store a newly created user in storage.
     *
     * This method handles the creation of a new user by accepting a request,
     * validating the input, and storing the user data in the database. It uses
     * a transaction to ensure data integrity and rolls back in case of any errors.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing user data.
     * @return \Illuminate\Http\JsonResponse The response indicating success or failure.
     *
     * @throws \Exception If there is an error during the user creation process.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $permission = '';

            foreach ($request->input('permission') as $key) {
                $permission .= $key . '|';
            }

            $data = array(
                'name' => $request->input('name'),
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('username')),
                'level' => $request->input('role'),
                'permission' => $permission
            );

            UserManagerModel::create($data);

            DB::commit();
            $status = true;
            return getResponseData(
                $status,
                $status ? "The customer was successfully updated!" : "Failed to update the customer!"
            );
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => "error",
                "message" => "Failed to update user. " . $e->getMessage()
            ]);
        }
    }
}
