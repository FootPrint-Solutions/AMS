<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\MessageTemplateModel;
use Illuminate\Http\Request;

class MessageTemplate extends Controller
{
    private $title = "Message Template Settings";
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
            'Settings.MessageTemplate.edit',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "templates" => MessageTemplateModel::all()->toArray()
                )
            )
        );
    }
}
