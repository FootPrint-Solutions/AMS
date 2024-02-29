<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\Menu as MenuModel;
use App\Models\MenuParent as MenuParentModel;

class Menu extends Controller
{
    private $title = "Menu Manager";
    private $menu = 4;
    private $submenu = 1;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'Developer.Menu.index',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(
            'Developer.Menu.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "menus" => [],
                    "menu_parents" => MenuParentModel::all()->toArray()
                )
            )
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Retrieve menu profile data (separated to retrieve its parent_id).
        $profile = MenuModel::with("menuSubs")->find($id)->toArray();

        return view(
            'Developer.Menu.create',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => $profile,
                    "menus" => MenuModel::where("parent_id", $profile["parent_id"])->get()->toArray(),
                    "menu_parents" => MenuParentModel::all()->toArray()
                )
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // DataTables configuration data.
        $draw = $request->input("draw");
        $start = $request->input("start");
        $length = $request->input("length");
        $searchValue = $request->input("search.value");
        $orderColumn = $request->input("order.0.column");
        $orderDirection = $request->input("order.0.dir");
        $orderColumnIndex = $request->input("order.0.column");

        $query = MenuModel::query()
            ->join('menu_parent', 'menu_parent.id', '=', 'menu.parent_id');

        $selectColumns = ['menu.id', 'menu.name AS menu_name', 'menu_parent.name AS menu_parent_name'];
        $query->select($selectColumns);

        if ($searchValue != null) {
            $query->where(function ($query) use ($searchValue, $selectColumns) {
                foreach ($selectColumns as $column) {
                    $query->orWhere($column, "like", "%" . $searchValue . "%");
                }
            });
        }

        if ($orderColumn !== null) {
            $columnName = $selectColumns[$orderColumnIndex] ?? null;
            if ($columnName !== null) {
                $query->orderBy($columnName, $orderDirection);
            }
        }

        $ListData = $query
            ->orderBy("menu_parent.order", "asc")
            ->orderBy("menu.order")
            ->skip($start)
            ->take($length)
            ->get();

        $data = [];
        $no = $start + 1;

        foreach ($ListData as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->menu_parent_name;
            $row[] = $key->menu_name;
            $row[] = $key->id;
            $data[] = $row;
        }

        $recordTotal  = MenuModel::count();
        $recordFiltered = ($searchValue != null) ? $query->count() : $recordTotal;

        $output = [
            "draw" => $draw,
            "recordsTotal" => $recordTotal,
            "recordsFiltered" => $recordFiltered,
            "data" => $data
        ];

        return response()->json($output);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $menu = new MenuModel();
        $menu->name = $request->name;
        $menu->parent_id = $request->menuparent;
        $menu->order = $menu->order($request->after, $request->menuparent);
        $menu->url = $request->url;
        $status = $menu->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new menu was successfully created!" : "Failed to create the new menu!"
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $menu = MenuModel::find($request->id);
        $menu->name = $request->name;
        $menu->parent_id = $request->menuparent;
        $menu->order = $menu->order($request->after, $request->menuparent, $menu->order);
        $menu->url = $request->url;
        $status = $menu->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new menu was successfully updated!" : "Failed to update the new menu!"
        );
    }

    /**
     * Obtain the list of menu belongs to submitted parent.
     *
     * @param  int  $idParent
     * @return \Illuminate\Http\Response
     */
    public function getMenu($idParent)
    {
        return MenuModel::where("parent_id", $idParent)->get()->toArray();
    }
}
