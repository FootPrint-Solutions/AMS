<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\ChartOfAccountModel;
use App\Models\Accounting\JournalTransactionDetailModel;
use App\Models\Accounting\JournalTransactionModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JournalTransaction extends Controller
{
    private $title = 'Journal Transaction';

    public function index()
    {
        return view(
            'Accounting.JournalTransaction.index',
            getIndexData($this->title)
        );
    }

    public function create()
    {
        $data = [
            'number' => JournalTransactionModel::generateVoucherNumber(),
            'chartOfAccounts' => ChartOfAccountModel::where('is_active', 1)
                ->orderBy('number')
                ->get(['id', 'number', 'name']),
        ];

        return view(
            'Accounting.JournalTransaction.create',
            getIndexData($this->title, $data)
        );
    }

    public function edit($id = null)
    {
        if ($id == null) {
            return redirect()->route('journal-transaction.index');
        }

        $journal = JournalTransactionModel::with('details')->find($id);
        if ($journal == null) {
            return redirect()->route('journal-transaction.index');
        }

        $data = [
            'number' => $journal->voucher_number,
            'chartOfAccounts' => ChartOfAccountModel::where('is_active', 1)
                ->orderBy('number')
                ->get(['id', 'number', 'name']),
            'profile' => $journal->toArray(),
        ];

        return view(
            'Accounting.JournalTransaction.create',
            getIndexData($this->title, $data)
        );
    }

    public function show(Request $request)
    {
        $draw = (int) $request->input('draw', 0);
        $start = (int) $request->input('start', 0);

        $data = JournalTransactionModel::allForDataTables($request);

        $rows = [];
        $no = $start + 1;
        foreach ($data['row'] as $item) {
            $statusBadgeClass = $item->status === 'post' ? 'badge-success' : 'badge-secondary text-dark';

            $row = [];
            $row[] = $item->id;
            $row[] = $no++;
            $row[] = $item->voucher_number;
            $row[] = formatDate((string) $item->date, 'j M Y');
            $row[] = $item->note ?? '-';
            $row[] = formatPrice($item->total ?? 0);
            $row[] = formatPrice($item->total ?? 0);
            $row[] = "<span class='badge $statusBadgeClass'>" . ($item->status ?? '-') . '</span>';
            $rows[] = $row;
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => JournalTransactionModel::count(),
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
                'note' => 'nullable|string|max:255',
            ]);

            $details = $this->extractDetails($request);
            if (count($details) < 1) {
                return getResponseData(false, 'Journal detail is required.');
            }

            [$isValid, $message, $normalizedDetails, $total] = $this->validateDetailRows($details);
            if (!$isValid) {
                DB::rollBack();
                return getResponseData(false, $message);
            }

            $journal = JournalTransactionModel::create([
                'date' => $request->date,
                'voucher_number' => $request->voucher_number ?: JournalTransactionModel::generateVoucherNumber(),
                'total' => $total,
                'status' => 'draft',
                'note' => $request->note,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            foreach ($normalizedDetails as $detail) {
                $detail['journal_entry_id'] = $journal->id;
                JournalTransactionDetailModel::create($detail);
            }

            DB::commit();

            return getResponseData(true, 'Journal transaction successfully created!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Journal Transaction Store Error: ' . $e->getMessage());
            return getResponseData(false, 'Failed to create journal transaction.');
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id' => 'required|exists:journal_entries,id',
                'date' => 'required|date',
                'voucher_number' => 'required|string|max:50',
                'note' => 'nullable|string|max:255',
            ]);

            $journal = JournalTransactionModel::findOrFail($request->id);
            if ($journal->status === 'post') {
                DB::rollBack();
                return getResponseData(false, 'Posted journal transaction cannot be edited.');
            }

            $details = $this->extractDetails($request);
            if (count($details) < 1) {
                DB::rollBack();
                return getResponseData(false, 'Journal detail is required.');
            }

            [$isValid, $message, $normalizedDetails, $total] = $this->validateDetailRows($details);
            if (!$isValid) {
                DB::rollBack();
                return getResponseData(false, $message);
            }

            $journal->update([
                'date' => $request->date,
                'voucher_number' => $request->voucher_number,
                'total' => $total,
                'note' => $request->note,
                'updated_by' => auth()->id(),
            ]);

            JournalTransactionDetailModel::where('journal_entry_id', $journal->id)->delete();
            foreach ($normalizedDetails as $detail) {
                $detail['journal_entry_id'] = $journal->id;
                JournalTransactionDetailModel::create($detail);
            }

            DB::commit();

            return getResponseData(true, 'Journal transaction successfully updated!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Journal Transaction Update Error: ' . $e->getMessage());
            return getResponseData(false, 'Failed to update journal transaction.');
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
                $journal = JournalTransactionModel::find($id);
                if (!$journal) {
                    continue;
                }

                if ($journal->status === 'post') {
                    DB::rollBack();
                    return getResponseData(false, 'Posted journal transaction cannot be deleted.');
                }

                JournalTransactionDetailModel::where('journal_entry_id', $journal->id)->delete();
                $journal->delete();
            }

            DB::commit();

            return getResponseData(true, 'Selected journal transaction(s) successfully deleted!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Journal Transaction Destroy Error: ' . $e->getMessage());
            return getResponseData(false, 'Failed to delete journal transaction.');
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
                $journal = JournalTransactionModel::find($id);
                if (!$journal) {
                    continue;
                }

                if ($journal->status === 'draft') {
                    $journal->status = 'post';
                    $journal->updated_by = auth()->id();
                    $journal->save();
                }
            }

            DB::commit();

            return getResponseData(true, 'Selected journal transaction(s) successfully posted!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Journal Transaction Post Error: ' . $e->getMessage());
            return getResponseData(false, 'Failed to post journal transaction.');
        }
    }

    public function getData(Request $request)
    {
        try {
            $journal = JournalTransactionModel::with('details.chartOfAccount')->findOrFail($request->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Journal transaction retrieved successfully.',
                'data' => $journal,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Journal transaction not found.',
            ], 404);
        }
    }

    public function getJournalTransactionItems($journalTransactionId)
    {
        try {
            $items = JournalTransactionDetailModel::where('journal_entry_id', $journalTransactionId)->with('chartOfAccount')
                ->orderBy('id')
                ->get([
                    'id',
                    'chart_of_account_id',
                    'account_number',
                    'account_name',
                    'description',
                    'debit',
                    'credit',
                    'ref',
                    DB::raw("CONCAT(account_number, ' - ', account_name) AS account_display"),
                    DB::raw('NULL AS ref_display'),
                ]);

            $refs = $items->pluck('ref')
                ->filter(fn ($ref) => !empty($ref))
                ->unique()
                ->values();

            $refDisplayMap = collect();

            if ($refs->isNotEmpty()) {
                $salesOrderMap = DB::table('sales_orders')
                    ->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id')
                    ->whereIn('sales_orders.sales_order_number', $refs)
                    ->select(
                        'sales_orders.sales_order_number as ref_key',
                        DB::raw("TRIM(CONCAT(sales_orders.sales_order_number, ' - ', COALESCE(customers.name, ''))) as ref_display")
                    )
                    ->get()
                    ->pluck('ref_display', 'ref_key');

                $purchaseOrderMap = DB::table('purchase_orders')
                    ->leftJoin('distributor_shops', function ($join) {
                        $join->on('purchase_orders.ship_to_id', '=', 'distributor_shops.id')
                            ->where('purchase_orders.ship_to_type', '=', 'App\\Models\\MasterData\\Distributor\\DistributorShopModel');
                    })
                    ->leftJoin('customers', function ($join) {
                        $join->on('purchase_orders.ship_to_id', '=', 'customers.id')
                            ->where('purchase_orders.ship_to_type', '=', 'App\\Models\\MasterData\\Customer\\CustomerModel');
                    })
                    ->leftJoin('suppliers', function ($join) {
                        $join->on('purchase_orders.ship_to_id', '=', 'suppliers.id')
                            ->where('purchase_orders.ship_to_type', '=', 'App\\Models\\MasterData\\Supplier\\SupplierModel');
                    })
                    ->leftJoin('distributors', function ($join) {
                        $join->on('purchase_orders.ship_to_id', '=', 'distributors.id')
                            ->where('purchase_orders.ship_to_type', '=', 'App\\Models\\MasterData\\Distributor\\DistributorModel');
                    })
                    ->whereIn('purchase_orders.purchase_order_number', $refs)
                    ->select(
                        'purchase_orders.purchase_order_number as ref_key',
                        DB::raw("TRIM(CONCAT(purchase_orders.purchase_order_number, ' - ', COALESCE(distributor_shops.name, customers.name, suppliers.name, distributors.name, ''))) as ref_display")
                    )
                    ->get()
                    ->pluck('ref_display', 'ref_key');

                $refDisplayMap = $salesOrderMap->merge($purchaseOrderMap);
            }

            $items = $items->map(function ($item) use ($refDisplayMap) {
                $item->ref_display = $refDisplayMap->get($item->ref, $item->ref);
                return $item;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Journal transaction items retrieved successfully.',
                'data' => $items,
            ]);
        } catch (Exception $e) {
            Log::error('Journal Transaction Items Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve journal transaction items.',
            ], 500);
        }
    }

    private function extractDetails(Request $request): array
    {
        $coaIds = (array) $request->input('detail_chart_of_account_id', []);
        $descriptions = (array) $request->input('detail_description', []);
        $debits = (array) $request->input('detail_debit', []);
        $credits = (array) $request->input('detail_credit', []);

        $max = max(count($coaIds), count($descriptions), count($debits), count($credits));
        $rows = [];

        for ($i = 0; $i < $max; $i++) {
            $coaId = $coaIds[$i] ?? null;
            $description = $descriptions[$i] ?? null;
            $debit = $this->parseNumber($debits[$i] ?? 0);
            $credit = $this->parseNumber($credits[$i] ?? 0);

            if (empty($coaId) && empty($description) && $debit == 0.0 && $credit == 0.0) {
                continue;
            }

            $rows[] = [
                'chart_of_account_id' => $coaId,
                'description' => $description,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        return $rows;
    }

    private function validateDetailRows(array $rows): array
    {
        $sumDebit = 0.0;
        $sumCredit = 0.0;
        $normalized = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 1;
            $coaId = (int) ($row['chart_of_account_id'] ?? 0);
            $debit = (float) ($row['debit'] ?? 0);
            $credit = (float) ($row['credit'] ?? 0);

            if ($coaId < 1) {
                return [false, 'Account is required on row ' . $rowNumber . '.', [], 0.0];
            }

            $account = ChartOfAccountModel::find($coaId);
            if (!$account) {
                return [false, 'Invalid account on row ' . $rowNumber . '.', [], 0.0];
            }

            if ($debit < 0 || $credit < 0) {
                return [false, 'Debit/Credit cannot be negative on row ' . $rowNumber . '.', [], 0.0];
            }

            if (($debit > 0 && $credit > 0) || ($debit == 0.0 && $credit == 0.0)) {
                return [false, 'Please fill either debit or credit on row ' . $rowNumber . '.', [], 0.0];
            }

            $sumDebit += $debit;
            $sumCredit += $credit;

            $normalized[] = [
                'chart_of_account_id' => $account->id,
                'account_number' => $account->number,
                'account_name' => $account->name,
                'description' => $row['description'] ?? null,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        if (round($sumDebit, 2) !== round($sumCredit, 2)) {
            return [false, 'Total debit and credit must be balanced.', [], 0.0];
        }

        return [true, '', $normalized, $sumDebit];
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
