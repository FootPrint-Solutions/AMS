<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

// MODELS
use App\Models\Accounting\ExpenseModel;
use App\Models\Accounting\ChartOfAccountModel;

class Expense extends Controller
{
    private $title = "Expense";

    public function index()
    {
        return view(
            "Accounting.Expense.index",
            getIndexData($this->title)
        );
    }

    public function create()
    {
        return view(
            "Accounting.Expense.create",
            getIndexData(
                $this->title,
                [
                    "chart_of_accounts" => ChartOfAccountModel::get()->toArray()
                ]
            )
        );
    }

    public function edit($id = null)
    {
        if ($id == null) {
            return redirect()->route("expense.index");
        }
        $expense = ExpenseModel::find($id);
        if ($expense == null) {
            return redirect()->route("expense.index");
        }

        return view(
            "Accounting.Expense.create",
            getIndexData(
                $this->title,
                [
                    "profile" => $expense->toArray(),
                    "chart_of_accounts" => ChartOfAccountModel::get()->toArray()
                ]
            )
        );
    }

    public function show(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start');

        $data = ExpenseModel::allForDataTables($request);

        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->chartOfAccount ? ($key->chartOfAccount->number ? $key->chartOfAccount->number . ' - ' : '') . $key->chartOfAccount->name : '';
            $row[] = $key->name;
            $row[] = $key->description;
            $row[] = $key->is_active ? 'Active' : 'Inactive';
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json([
            "draw" => $draw,
            "recordsTotal" => ExpenseModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate(
                [
                    'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
                    'name' => 'required|string|max:100',
                    'description' => 'nullable|string|max:255',
                    'is_active' => 'required|boolean',
                ],
                [
                    'name.required' => 'Expense name is required!',
                    'is_active.required' => 'Status is required!',
                ]
            );

            $expense = new ExpenseModel();
            $expense->chart_of_account_id = $validatedData['chart_of_account_id'];
            $expense->name = $validatedData['name'];
            $expense->description = $validatedData['description'] ?? null;
            $expense->is_active = $validatedData['is_active'];
            $status = $expense->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "Expense successfully created!" : "Failed to create expense!"
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
                    'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
                    'name' => 'required|string|max:100',
                    'description' => 'nullable|string|max:255',
                    'is_active' => 'required|boolean',
                ],
                [
                    'chart_of_account_id.required' => 'Chart of account is required!',
                    'name.required' => 'Expense name is required!',
                    'is_active.required' => 'Status is required!',
                ]
            );

            $expense = ExpenseModel::find($request->id);
            $expense->chart_of_account_id = $validatedData['chart_of_account_id'];
            $expense->name = $validatedData['name'];
            $expense->description = $validatedData['description'] ?? null;
            $expense->is_active = $validatedData['is_active'];
            $status = $expense->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "Expense successfully updated!" : "Failed to update expense!"
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
                $expense = ExpenseModel::find($id);
                $status = $expense->delete();
            }

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "Selected expense(s) successfully deleted!" : "Failed to delete selected expense(s)!"
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return getResponseData(false);
        }
    }

    public function toggle(Request $request)
    {
        DB::beginTransaction();

        try {
            $expense = ExpenseModel::find($request->id);
            $expense->is_active = !$expense->is_active;
            $status = $expense->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "Expense status successfully updated!" : "Failed to update expense status!"
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return getResponseData(false);
        }
    }
}
