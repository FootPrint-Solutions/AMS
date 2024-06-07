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
                $this->title
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Get menu data (rows and count).
        $data = MenuModel::allForDataTables($request);

        $rows = [];
        $no = $request->input("start") + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->menu_parent_name;
            $row[] = $key->menu_name;
            $row[] = $key->id;
            $row[] = $key->hide;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $request->input("draw"),
            "recordsTotal" => MenuModel::count(),
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
    public function store(Request $request)
    {
        $menu = new MenuModel();
        $menu->name = $request->name;
        $menu->parent_id = $request->menuparent;
        $menu->order = $menu->order($request->after, $request->menuparent);
        $menu->url = $request->url;
        $menu->hide = $request->hide;
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
        $menu->hide = $request->hide;
        $status = $menu->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new menu was successfully updated!" : "Failed to update the new menu!"
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $menu = MenuModel::find($request->id);
        $parent_id = $menu->parent_id;
        $order = $menu->order;
        $status = $menu->delete();

        // Update menu order.
        MenuModel::updateOrder($parent_id, $order);

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected menu was successfully deleted!" : "Failed to deleted the selected menu!"
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

    public function refresh()
    {
        session([
            'menu' => MenuParentModel::with(['menus' => function ($query) {
                $query->orderBy('order');
            }])->orderBy('order')->get()->toArray(),
            'submenu' => MenuModel::with(['menuSubs' => function ($query) {
                $query->orderBy('order');
            }])->get()->mapWithKeys(function ($menu) {
                return [$menu->id => $menu->menuSubs->toArray()];
            })->toArray()
        ]);
    }
}
