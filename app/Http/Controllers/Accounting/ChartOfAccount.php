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
use App\Models\Accounting\ChartOfAccountGroupModel;

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
        $groups = ChartOfAccountGroupModel::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view(
            "Accounting.ChartOfAccount.create",
            getIndexData($this->title, array("groups" => $groups->toArray()))
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

        $groups = ChartOfAccountGroupModel::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view(
            "Accounting.ChartOfAccount.create",
            getIndexData($this->title, array(
                "profile" => $account->toArray(),
                "groups" => $groups->toArray(),
            ))
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
            $row[] = $key->group ? $key->group->name : '-';
            $row[] = $key->is_active ? "<i class='fa-solid fa-circle text-success'></i>" : "<i class='fa-solid fa-circle text-danger'></i>";
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
                    'chart_of_account_group_id' => [
                        'required',
                        'integer',
                        Rule::exists('chart_of_account_group', 'id')->whereNull('deleted_at'),
                    ],
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
                    'chart_of_account_group_id' => [
                        'required',
                        'integer',
                        Rule::exists('chart_of_account_group', 'id')->whereNull('deleted_at'),
                    ],
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

    /**
     * Display list of account groups.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function groupList()
    {
        $groups = ChartOfAccountGroupModel::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'data' => $groups,
        ]);
    }

    /**
     * Store new account group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function groupStore(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate(
                [
                    'name' => [
                        'required',
                        'string',
                        Rule::unique('chart_of_account_group', 'name')->whereNull('deleted_at'),
                    ],
                ],
                [
                    'name.required' => 'Account group name is required!',
                    'name.unique' => 'Account group name already exists!',
                ]
            );

            $group = new ChartOfAccountGroupModel();
            $group->fill($validatedData);
            $status = $group->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? 'Account group was successfully created!' : 'Failed to create account group!'
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
     * Update account group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function groupUpdate(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id' => [
                    'required',
                    'integer',
                    Rule::exists('chart_of_account_group', 'id')->whereNull('deleted_at'),
                ],
            ]);

            $validatedData = $request->validate(
                [
                    'name' => [
                        'required',
                        'string',
                        Rule::unique('chart_of_account_group', 'name')
                            ->ignore($request->id)
                            ->whereNull('deleted_at'),
                    ],
                ],
                [
                    'name.required' => 'Account group name is required!',
                    'name.unique' => 'Account group name already exists!',
                ]
            );

            $group = ChartOfAccountGroupModel::find($request->id);
            $group->fill($validatedData);
            $status = $group->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? 'Account group was successfully updated!' : 'Failed to update account group!'
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
     * Delete account group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function groupDestroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id' => [
                    'required',
                    'integer',
                    Rule::exists('chart_of_account_group', 'id')->whereNull('deleted_at'),
                ],
            ]);

            $usedInAccount = ChartOfAccountModel::query()
                ->where('chart_of_account_group_id', $request->id)
                ->exists();

            if ($usedInAccount) {
                DB::rollBack();
                return getResponseData(false, 'This account group is still used by chart of account data!');
            }

            $group = ChartOfAccountGroupModel::find($request->id);
            $status = $group->delete();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? 'Account group was successfully deleted!' : 'Failed to delete account group!'
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
}
