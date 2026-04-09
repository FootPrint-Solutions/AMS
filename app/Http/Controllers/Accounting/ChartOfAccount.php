<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Exception;

// MODELS
use App\Models\Accounting\ChartOfAccountModel;

class ChartOfAccount extends Controller
{
    private $title = "Chart of Account";

    /**
     * Show the Chart of Account index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "Accounting.ChartOfAccount.index",
            getIndexData($this->title)
        );
    }

    /**
     * Show the form for creating Chart of Account resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            "Accounting.ChartOfAccount.create",
            getIndexData($this->title)
        );
    }

    /**
     * Show the form for editing Chart of Account resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {
        if ($id == null) {
            return redirect()->route("chart-of-account.index");
        }
        $account = ChartOfAccountModel::find($id);
        if ($account == null) {
            return redirect()->route("chart-of-account.index");
        }

        return view(
            "Accounting.ChartOfAccount.create",
            getIndexData($this->title, array("profile" => $account->toArray()))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function show(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start');

        $data = ChartOfAccountModel::allForDataTables($request);

        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->number;
            $row[] = $key->name;
            $row[] = $key->chart_of_account_group_id;
            $row[] = $key->is_active;
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => ChartOfAccountModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Store a newly created Chart of Account resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate(
                [
                    'number' => 'required|string',
                    'name' => 'required|string',
                    'chart_of_account_group_id' => 'required|integer',
                    'is_active' => 'required|boolean',
                ],
                [
                    'number.required' => 'Account number is required!',
                    'name.required' => 'Account name is required!',
                    'chart_of_account_group_id.required' => 'Account group is required!',
                    'is_active.required' => 'Active status is required!',
                ]
            );

            $account = new ChartOfAccountModel();
            $account->fill($validatedData);
            $status = $account->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The new chart of account was successfully created!" : "Failed to create the new chart of account!"
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

    /**
     * Update the specified Chart of Account resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate(
                [
                    'number' => 'required|string',
                    'name' => 'required|string',
                    'chart_of_account_group_id' => 'required|integer',
                    'is_active' => 'required|boolean',
                ],
                [
                    'number.required' => 'Account number is required!',
                    'name.required' => 'Account name is required!',
                    'chart_of_account_group_id.required' => 'Account group is required!',
                    'is_active.required' => 'Active status is required!',
                ]
            );

            $account = ChartOfAccountModel::find($request->id);
            $account->fill($validatedData);
            $status = $account->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The chart of account was successfully updated!" : "Failed to update the chart of account!"
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $status = true;
            $ids = $request->id;

            foreach ($ids as $id) {
                $account = ChartOfAccountModel::find($id);
                $status = $account->delete();
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The selected account was successfully deleted!" : "Failed to delete the selected account!"
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return getResponseData(false);
        }
    }
}
