<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
