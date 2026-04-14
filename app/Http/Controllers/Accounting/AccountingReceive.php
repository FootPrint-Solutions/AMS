<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\AccountingReceiveDetailModel;
use App\Models\Accounting\AccountingReceiveModel;
use App\Models\Accounting\ChartOfAccountModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountingReceive extends Controller
{
    private $title = 'Accounting receive';

    public function index()
    {
        return view(
            'Accounting.AccountingReceive.index',
            getIndexData($this->title)
        );
    }

    public function create()
    {
        $data = [
            'number' => AccountingReceiveModel::generateVoucherNumber(),
            'chartOfAccounts' => ChartOfAccountModel::where('is_active', 1)
                ->orderBy('number')
                ->get(['id', 'number', 'name']),
        ];

        return view(
            'Accounting.AccountingReceive.create',
            getIndexData($this->title, $data)
        );
    }

    public function edit($id = null)
    {
        if ($id == null) {
            return redirect()->route('accounting-receive.index');
        }

        $receive = AccountingReceiveModel::find($id);
        if ($receive == null) {
            return redirect()->route('accounting-receive.index');
        }

        $profile = $receive->toArray();
        $profile['details'] = AccountingReceiveDetailModel::where('cb_receive_id', $receive->id)
            ->orderBy('id')
            ->get(['account_id', 'account_name', 'description', 'total'])
            ->toArray();

        $data = [
            'number' => $receive->voucher_number,
            'chartOfAccounts' => ChartOfAccountModel::where('is_active', 1)
                ->orderBy('number')
                ->get(['id', 'number', 'name']),
            'profile' => $profile,
        ];

        return view(
            'Accounting.AccountingReceive.create',
            getIndexData($this->title, $data)
        );
    }

    public function show(Request $request)
    {
        $draw = (int) $request->input('draw', 0);
        $start = (int) $request->input('start', 0);

        $data = AccountingReceiveModel::allForDataTables($request);

        $rows = [];
        $no = $start + 1;
        foreach ($data['row'] as $item) {
            $statusBadgeClass = $item->status === 'post' ? 'badge-success' : 'badge-secondary text-dark';

            $row = [];
            $row[] = $item->id;
            $row[] = $no++;
            $row[] = $item->voucher_number;
            $row[] = formatDate((string) $item->date, 'j M Y');
            $row[] = $item->to ?? '-';
            $row[] = strtoupper((string) ($item->type ?? '-'));
            $row[] = formatPrice($item->total ?? 0);
            $row[] = "<span class='badge $statusBadgeClass'>" . ($item->status ?? '-') . '</span>';
            $rows[] = $row;
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => AccountingReceiveModel::count(),
            'recordsFiltered' => $data['count'],
            'data' => $rows,
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'date' => 'required|date',
                'voucher_number' => 'nullable|string|max:50',
                'to' => 'nullable|string|max:255',
                'bank_account_no' => 'nullable|string|max:100',
                'address' => 'nullable|string|max:255',
                'account_id' => 'required|exists:chart_of_accounts,id',
                'type' => 'required|in:cash,bank',
                'note' => 'nullable|string|max:255',
            ]);

            $details = $this->extractDetails($request);
            if (count($details) < 1) {
                DB::rollBack();
                return getResponseData(false, 'receive detail is required.');
            }

            [$isValid, $message, $normalizedDetails, $total] = $this->validateDetailRows($details);
            if (!$isValid) {
                DB::rollBack();
                return getResponseData(false, $message);
            }

            $mainAccount = ChartOfAccountModel::findOrFail((int) $request->account_id);

            $receive = AccountingReceiveModel::create([
                'date' => $request->date,
                'voucher_number' => $request->voucher_number ?: AccountingReceiveModel::generateVoucherNumber(),
                'to' => $request->to,
                'bank_account_no' => $request->bank_account_no,
                'address' => $request->address,
                'account_id' => $mainAccount->id,
                'account_name' => $mainAccount->name,
                'total' => $total,
                'status' => 'draft',
                'type' => $request->type,
                'note' => $request->note,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            foreach ($normalizedDetails as $detail) {
                $detail['cb_receive_id'] = $receive->id;
                AccountingReceiveDetailModel::create($detail);
            }

            DB::commit();

            return getResponseData(true, 'Accounting receive successfully created!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Accounting receive Store Error: ' . $e->getMessage());
            return getResponseData(false, 'Failed to create accounting receive.');
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id' => 'required|exists:accounting_expenses,id',
                'date' => 'required|date',
                'voucher_number' => 'required|string|max:50',
                'to' => 'nullable|string|max:255',
                'bank_account_no' => 'nullable|string|max:100',
                'address' => 'nullable|string|max:255',
                'account_id' => 'required|exists:chart_of_accounts,id',
                'type' => 'required|in:cash,bank',
                'note' => 'nullable|string|max:255',
            ]);

            $receive = AccountingReceiveModel::findOrFail($request->id);
            if ($receive->status === 'post') {
                DB::rollBack();
                return getResponseData(false, 'Posted accounting receive cannot be edited.');
            }

            $details = $this->extractDetails($request);
            if (count($details) < 1) {
                DB::rollBack();
                return getResponseData(false, 'receive detail is required.');
            }

            [$isValid, $message, $normalizedDetails, $total] = $this->validateDetailRows($details);
            if (!$isValid) {
                DB::rollBack();
                return getResponseData(false, $message);
            }

            $mainAccount = ChartOfAccountModel::findOrFail((int) $request->account_id);

            $receive->update([
                'date' => $request->date,
                'voucher_number' => $request->voucher_number,
                'to' => $request->to,
                'bank_account_no' => $request->bank_account_no,
                'address' => $request->address,
                'account_id' => $mainAccount->id,
                'account_name' => $mainAccount->name,
                'total' => $total,
                'type' => $request->type,
                'note' => $request->note,
                'updated_by' => auth()->id(),
            ]);

            AccountingReceiveDetailModel::where('cb_receive_id', $receive->id)->delete();
            foreach ($normalizedDetails as $detail) {
                $detail['cb_receive_id'] = $receive->id;
                AccountingReceiveDetailModel::create($detail);
            }

            DB::commit();

            return getResponseData(true, 'Accounting receive successfully updated!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Accounting receive Update Error: ' . $e->getMessage());
            return getResponseData(false, 'Failed to update accounting receive.');
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->input('ids', $request->input('id', []));
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            foreach ($ids as $id) {
                $receive = AccountingReceiveModel::find($id);
                if (!$receive) {
                    continue;
                }

                if ($receive->status === 'post') {
                    DB::rollBack();
                    return getResponseData(false, 'Posted accounting receive cannot be deleted.');
                }

                AccountingReceiveDetailModel::where('cb_receive_id', $receive->id)->delete();
                $receive->delete();
            }

            DB::commit();

            return getResponseData(true, 'Selected accounting receive(s) successfully deleted!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Accounting receive Destroy Error: ' . $e->getMessage());
            return getResponseData(false, 'Failed to delete accounting receive.');
        }
    }

    public function post(Request $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->input('ids', []);
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            foreach ($ids as $id) {
                $receive = AccountingReceiveModel::find($id);
                if (!$receive) {
                    continue;
                }

                if ($receive->status === 'draft') {
                    $receive->status = 'post';
                    $receive->updated_by = auth()->id();
                    $receive->save();
                }
            }

            DB::commit();

            return getResponseData(true, 'Selected accounting receive(s) successfully posted!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Accounting receive Post Error: ' . $e->getMessage());
            return getResponseData(false, 'Failed to post accounting receive.');
        }
    }

    public function getData(Request $request)
    {
        try {
            $receive = AccountingReceiveModel::with('details')->findOrFail($request->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Accounting receive retrieved successfully.',
                'data' => $receive,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Accounting receive not found.',
            ], 404);
        }
    }

    public function getAccountingExpenseItems($accountingExpenseId)
    {
        try {
            $items = AccountingReceiveDetailModel::where('cb_receive_id', $accountingExpenseId)
                ->orderBy('id')
                ->get([
                    'id',
                    'account_id',
                    'account_name',
                    'description',
                    'total',
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Accounting receive items retrieved successfully.',
                'data' => $items,
            ]);
        } catch (Exception $e) {
            Log::error('Accounting receive Items Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve accounting receive items.',
            ], 500);
        }
    }

    private function extractDetails(Request $request): array
    {
        $accountIds = (array) $request->input('detail_account_id', []);
        $descriptions = (array) $request->input('detail_description', []);
        $totals = (array) $request->input('detail_total', []);

        $max = max(count($accountIds), count($descriptions), count($totals));
        $rows = [];

        for ($i = 0; $i < $max; $i++) {
            $accountId = $accountIds[$i] ?? null;
            $description = $descriptions[$i] ?? null;
            $total = $this->parseNumber($totals[$i] ?? 0);

            if (empty($accountId) && empty($description) && $total == 0.0) {
                continue;
            }

            $rows[] = [
                'account_id' => $accountId,
                'description' => $description,
                'total' => $total,
            ];
        }

        return $rows;
    }

    private function validateDetailRows(array $rows): array
    {
        $sumTotal = 0.0;
        $normalized = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 1;
            $accountId = (int) ($row['account_id'] ?? 0);
            $total = (float) ($row['total'] ?? 0);

            if ($accountId < 1) {
                return [false, 'Account is required on row ' . $rowNumber . '.', [], 0.0];
            }

            $account = ChartOfAccountModel::find($accountId);
            if (!$account) {
                return [false, 'Invalid account on row ' . $rowNumber . '.', [], 0.0];
            }

            if ($total <= 0) {
                return [false, 'Total must be greater than zero on row ' . $rowNumber . '.', [], 0.0];
            }

            $sumTotal += $total;

            $normalized[] = [
                'account_id' => $account->id,
                'account_name' => $account->name,
                'description' => $row['description'] ?? null,
                'total' => $total,
            ];
        }

        return [true, '', $normalized, $sumTotal];
    }

    private function parseNumber($value): float
    {
        if (is_null($value) || $value === '') {
            return 0.0;
        }

        $raw = preg_replace('/[^0-9.\-]/', '', (string) $value);

        return $raw === '' ? 0.0 : (float) $raw;
    }
}
