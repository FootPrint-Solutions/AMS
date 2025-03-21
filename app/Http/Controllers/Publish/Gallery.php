<?php

namespace App\Http\Controllers\Publish;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Publish\GalleryModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\MasterData\Vehicle\VehicleModel;
use App\Models\MasterData\Battery\BatteryModel;

class Gallery extends Controller
{
    private $title = "Gallery";

    /**
     * Show the Gallery index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "Publish.Gallery.index",
            getIndexData(
                $this->title
            )
        );
    }

    public function create()
    {
        // Get all vehicles and batteries for the select options.
        $vehicles = VehicleModel::get();
        $batteries = BatteryModel::get();
        $data = [
            'vehicles' => $vehicles,
            'batteries' => $batteries,
        ];
        return view(
            "Publish.Gallery.create",
            getIndexData($this->title, $data)
        );
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'battery_id' => 'required|exists:batteries,id',
                'vehicle_id' => 'required|exists:vehicles,id',
                'photo' => 'required|image',
                'status' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photo = $photo->store('gallery/', 'public');
                $request->merge(['photo' => basename($photo)]);
            }

            // Create the Gallery
            $gallery = new GalleryModel();
            $gallery->battery_id = $request->input('battery_id');
            $gallery->vehicle_id = $request->input('vehicle_id');
            if ($request->hasFile('photo')) {
                $gallery->photo = $request->input('photo');
            } else {
                $gallery->photo = null;
            }
            $gallery->status = $request->input('status');
            $status = $gallery->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The new gallery was successfully created!" : "Failed to create the new gallery!"
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return getResponseData(
                false,
                "Failed to create the new gallery!",
                $th->getMessage()
            );
        }
    }

    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input('draw');
        $start = $request->input('start');

        $data = GalleryModel::allForDataTables($request);
        $rows = [];
        $no = $start + 1;
        foreach ($data['row'] as $gallery) {
            $row = [];
            $row[] = $no++;
            $row[] = $gallery->battery_name;
            $row[] = $gallery->vehicle_name;
            $row[] = $gallery->photo ?
                '<img src="' . asset('storage/gallery/' . $gallery->photo) . '" alt="Gallery Photo" width="50" height="50" onerror="this.onerror=null;this.src=\'https://placehold.co/50x50\'">' : 'No Photo';
            // $row[] = $gallery->status ? 'Active' : 'Inactive';
            $row[] = $gallery->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => GalleryModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    public function edit($id)
    {
        $gallery = GalleryModel::findOrFail($id);
        $vehicles = VehicleModel::get();
        $batteries = BatteryModel::get();

        return view(
            "Publish.Gallery.create",
            getIndexData(
                $this->title,
                [
                    'gallery' => $gallery,
                    'vehicles' => $vehicles,
                    'batteries' => $batteries,
                ]
            )
        );
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'battery_id' => 'required|exists:batteries,id',
                'vehicle_id' => 'required|exists:vehicles,id',
                'photo' => 'required|image',
                'status' => 'required|boolean',
                'id' => 'required|exists:galleries,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photo = $photo->store('gallery/', 'public');
                $request->merge(['photo' => basename($photo)]);
            }

            // Update the Gallery
            $gallery = GalleryModel::findOrFail($request->input('id'));
            $gallery->battery_id = $request->input('battery_id');
            $gallery->vehicle_id = $request->input('vehicle_id');
            if ($request->hasFile('photo')) {
                $gallery->photo = $request->input('photo');
            } else {
                $gallery->photo = null;
            }
            $gallery->status = $request->input('status');
            $status = $gallery->save();


            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The gallery was successfully updated!" : "Failed to update the gallery!"
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return getResponseData(
                false,
                "Failed to update the gallery!",
                $th->getMessage()
            );
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids = $request->input('id');
            $status = GalleryModel::whereIn('id', $ids)->delete();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The selected galleries were successfully deleted!" : "Failed to delete the selected galleries!"
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return getResponseData(
                false,
                "Failed to delete the selected galleries!",
                $th->getMessage()
            );
        }
    }
}
