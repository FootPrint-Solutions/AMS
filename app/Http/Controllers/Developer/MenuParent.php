<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MenuParent as MenuParentModel;

class MenuParent extends Controller
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
            'Developer.Menu.MenuParent.index',
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
            'Developer.Menu.MenuParent.create',
            getIndexData(
                $this->title,
                array(
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
        return view(
            'Developer.MenuParent.create',
            getIndexData(
                $this->title,
                array(
                    "profile" => MenuParentModel::find($id)->toArray(),
                    "menu_parents" => MenuParentModel::all()->toArray()
                )
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $menuParent = new MenuParentModel();
        $menuParent->name = $request->name;
        $menuParent->order = $menuParent->order($request->after);
        $menuParent->url = $request->url;
        $menuParent->icon = $request->icon;
        $status = $menuParent->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new menu parent was successfully created!" : "Failed to create the new menu parent!"
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
        $menuParent = MenuParentModel::find($request->id);
        $menuParent->name = $request->name;
        $menuParent->order = $menuParent->order($request->after, $menuParent->order);
        $menuParent->url = $request->url;
        $menuParent->icon = $request->icon;
        $status = $menuParent->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new menu parent was successfully updated!" : "Failed to update the new menu parent!"
        );
    }
}
