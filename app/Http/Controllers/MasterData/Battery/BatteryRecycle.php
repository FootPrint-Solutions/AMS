<?php

namespace App\Http\Controllers\MasterData\Battery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

// MODELS
use App\Models\MasterData\Battery\BatteryRecycleModel;

class BatteryRecycle extends Controller
{
    private $title = "Battery Recycle";

    public function index()
    {
        return view(
            "MasterData.Battery.Recycle.index",
            getIndexData($this->title)
        );
    }

    public function create()
    {
        return view(
            "MasterData.Battery.Recycle.create",
            getIndexData($this->title)
        );
    }

    public function edit($id = null)
    {
        if ($id == null) {
            return redirect()->route("battery.recycle.index");
        }
        $recycle = BatteryRecycleModel::find($id);
        if ($recycle == null) {
            return redirect()->route("battery.recycle.index");
        }

        return view(
            "MasterData.Battery.Recycle.create",
            getIndexData(
                $this->title,
                ["profile" => $recycle->toArray()]
            )
        );
    }

    public function show(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start');

        // You may need to implement allForDataTables in BatteryRecycleModel model
        $data = BatteryRecycleModel::query();
        if ($request->has('search.value') && $request->input('search.value')) {
            $search = $request->input('search.value');
            $data->where('name', 'like', "%$search%");
        }
        $count = $data->count();
        $rowsData = $data->offset($start)->limit($request->input('length'))->get();

        $rows = [];
        $no = $start + 1;
        foreach ($rowsData as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->price;
            $row[] = $key->weight;
            $row[] = $key->note;
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json([
            "draw" => $draw,
            "recordsTotal" => BatteryRecycleModel::count(),
            "recordsFiltered" => $count,
            "data" => $rows
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate(
                [
                    'name' => 'required|string',
                    'price' => 'nullable|string',
                    'weight' => 'nullable|numeric',
                    'note' => 'nullable|string',
                ],
                [
                    'name.required' => 'Battery recycle name is required!',
                ]
            );

            $recycle = new BatteryRecycleModel();
            $recycle->name = $validatedData['name'];
            $recycle->price = str_replace('.', '', $validatedData['price']) ?? null;
            $recycle->weight = $validatedData['weight'] ?? null;
            $recycle->status = 1;
            $recycle->note = $validatedData['note'] ?? null;
            $status = $recycle->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The new battery recycle was successfully created!" : "Failed to create the new battery recycle!"
            );
        } catch (ValidationException $e) {
            DB::rollBack();
            return getResponseData(false, $e->validator->errors()->first());
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return getResponseData(false);
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate(
                [
                    'name' => 'required|string',
                    'price' => 'nullable|string',
                    'weight' => 'nullable|numeric',
                    'note' => 'nullable|string'
                ],
                [
                    'name.required' => 'Battery recycle name is required!'
                ]
            );

            $recycle = BatteryRecycleModel::find($request->id);
            if (!$recycle) {
                DB::rollBack();
                return getResponseData(false, "Battery recycle not found!");
            }
            $recycle->name = $validatedData['name'];
            $recycle->price = str_replace('.', '', $validatedData['price']) ?? null;
            $recycle->weight = $validatedData['weight'] ?? null;
            $recycle->note = $validatedData['note'] ?? null;
            $status = $recycle->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The battery recycle was successfully updated!" : "Failed to update the battery recycle!"
            );
        } catch (ValidationException $e) {
            DB::rollBack();
            return getResponseData(false, $e->validator->errors()->first());
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return getResponseData(false);
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $status = true;
            $ids = $request->id;

            foreach ($ids as $id) {
                $recycle = BatteryRecycleModel::find($id);
                if ($recycle) {
                    $status = $recycle->delete();
                }
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The selected recycle was successfully deleted!" : "Failed to delete the selected recycle!"
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return getResponseData(false);
        }
    }

    public function getBatteryRecycle($keyword)
    {
        $batteries = BatteryRecycleModel::where('name', 'like', '%' . $keyword . '%')
            ->where('status', 1)
            ->get();

        $result = $batteries->map(function ($b) {
            return [
                'id' => $b->id,
                'name' => $b->name,
                'price_retail' => $b->price !== null ? (int) $b->price : null,
                'discount' => '0.00',
                'type' => 'regular',
            ];
        })->values();

        return response()->json($result);
    }
}
