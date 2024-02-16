<?php

namespace App\Http\Controllers\MasterData\Battery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\MasterData\Battery\BatterySubbrandCategoryModel;

class BatterySubbrand extends Controller
{
    private $title = "Battery Subbrand Category";
    private $menu = 2;
    private $submenu = 4;

    /**
     * Show the Battery Subbrand Category index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "MasterData.Battery.Subbrand.index",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu
            )
        );
    }

    /**
     * Show the form for creating Battery Subbrand Category profile resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'MasterData.Battery.Subbrand.create',
            getIndexData(
                $this->title,
                2,
                4
            )
        );
    }

    /**
     * Show the form for editing Battery Subbrand resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            "MasterData.Battery.Subbrand.create",
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "profile" => BatterySubbrandCategoryModel::find($id)->toArray(),
                )
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return string
     */
    public function show(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start');
        $length = $request->input('length');
        $searchValue = $request->input('search.value');
        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');
        $orderColumnIndex = $request->input('order.0.column');

        $query =  BatterySubbrandCategoryModel::select('*');

        $selectColumns = $query->getModel()->getFillable(); // ini udah otomatis ambil fillable dari model / query yang di panggil
        $query->select($selectColumns); // ini udah otomatis ambil fillable dari model / query yang di panggil
        if ($searchValue != null) {
            $query->where(function ($query) use ($searchValue, $selectColumns) {
                foreach ($selectColumns as $column) {
                    $query->orWhere($column, 'like', '%' . $searchValue . '%');
                }
            });
        }

        if ($orderColumn !== null) {
            $columnName = $selectColumns[$orderColumnIndex] ?? null;
            if ($columnName !== null) {
                $query->orderBy($columnName, $orderDirection);
            }
        }

        $ListData = $query->orderBy('name', 'asc')
            ->skip($start)
            ->take($length)
            ->get();

        $data = [];
        $no = $start + 1;

        foreach ($ListData as $key) {
            $row = [];
            $row[] = $no;
            $row[] = $key->name;
            $row[] = $key->id;
            $data[] = $row;
            $no++;
        }
        $recordTotal =  BatterySubbrandCategoryModel::count();
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
     * Store a newly created Battery Subbrand Category resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        $subbrand = new BatterySubbrandCategoryModel();
        $subbrand->name = $request->name;
        $status = $subbrand->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The new battery subbrand category was successfully created!" : "Failed to create the new battery subbrand category!"
        );
    }

    /**
     * Update the specified Battery Subbrand Category resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $subbrand = BatterySubbrandCategoryModel::find($request->id);
        $subbrand->name = $request->name;
        $status = $subbrand->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The battery subbrand category was successfully updated!" : "Failed to update the battery subbrand category!"
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $subbrand = BatterySubbrandCategoryModel::find($request->id);
        $status = $subbrand->delete();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "The selected subbrand category was successfully deleted!" : "Failed to delete the selected subbrand category!"
        );
    }
}
