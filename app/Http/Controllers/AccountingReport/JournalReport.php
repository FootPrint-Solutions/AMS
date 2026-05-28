<?php

namespace App\Http\Controllers\AccountingReport;

use App\Http\Controllers\Controller;
use App\Models\Accounting\JournalTransactionModel;
use App\Models\Master\CompanyProfile;
use App\Models\Accounting\ChartOfAccountModel;
use App\Models\Orders\PurchaseOrder\PurchaseOrderModel;
use App\Models\Orders\SalesOrder\SalesOrderModel;
use App\Exports\JournalReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class JournalReport extends Controller
{
    public function index(Request $request)
    {
        $title = "Journal Report";
        $breadcrumbs = [
            "/" => "Home",
            "#" => "Journal Report"
        ];
        return view('AccountingReport.JournalReport.index', compact('title', 'breadcrumbs'));
    }

    public function print($dateStart, $dateEnd, $filter = null)
    {
        // dd($dateStart);
        $dateStart = \DateTime::createFromFormat('m-Y', $dateStart);
        $dateStart->modify('first day of this month');
        $dateEnd = \DateTime::createFromFormat('m-Y', $dateEnd);
        $dateEnd->modify('last day of this month');

        // Set reports.
        $reports = [];
        $totalDebit = 0;
        $totalCredit = 0;

        $currentVoucherNumber = '';
        $currentParentIndex = -1;
        foreach (JournalTransactionModel::allForPrint($dateStart->format('Y-m-d'), $dateEnd->format('Y-m-d'), $filter) as $index => $row) {
            if ($currentVoucherNumber != $row['number']) {
                $currentVoucherNumber = $row['number'];
                $currentParentIndex = $index;

                $report = [];
                $report['date'] = date('d M Y', strtotime($row['date']));
                $report['number'] = $row['number'];
                $report['details'] = [];

                array_push($report['details'], $this->generateTableDetailPrint($row));

                $reports[$index] = $report;
            } else {
                array_push($reports[$currentParentIndex]['details'], $this->generateTableDetailPrint($row));
            }

            $totalDebit += $row['total_debit'];
            $totalCredit += $row['total_credit'];
        }

        return view('AccountingReport.JournalReport.print', array(
            'data' => array(
                'dateStart' => $dateStart->format('F Y'),
                'dateEnd' => $dateEnd->format('F Y') != $dateStart->format('F Y') ? $dateEnd->format('F Y') : null,
                'reports' => $reports,
                'totalDebit' => $totalDebit,
                'totalCredit' => $totalCredit,
            ),
        ));
    }

    public function coaAutocomplete(Request $request)
    {
        $term = trim((string) $request->input('term', ''));

        if ($term === '') {
            return response()->json([]);
        }

        $accounts = ChartOfAccountModel::query()
            ->select('number', 'name')
            ->where('is_active', 1)
            ->where(function ($query) use ($term) {
                $query->where('number', 'like', '%' . $term . '%')
                    ->orWhere('name', 'like', '%' . $term . '%');
            })
            ->orderBy('number')
            ->limit(20)
            ->get();

        $results = $accounts->map(function ($account) {
            return [
                'label' => $account->number . ' - ' . $account->name,
                'value' => $account->name,
            ];
        })->values();

        return response()->json($results);
    }

    public function refDetail($ref)
    {
        $ref = trim((string) urldecode($ref));

        if ($ref === '') {
            return redirect()->route('journal-report.index')->with('error', 'Reference is empty.');
        }

        $salesOrder = SalesOrderModel::select('id', 'sales_order_number', 'status', 'type')
            ->where('sales_order_number', $ref)
            ->first();

        if ($salesOrder) {
            if ($salesOrder->status === 'draft') {
                return redirect()->route('sales-order.edit', $salesOrder->id);
            }

            if (($salesOrder->type ?? null) !== 'recycle') {
                return redirect()->route('sales-order.invoice', $salesOrder->id);
            }

            return redirect()->route('sales-order.index', ['filter' => $salesOrder->sales_order_number]);
        }

        $purchaseOrder = PurchaseOrderModel::select('id', 'purchase_order_number', 'status')
            ->where('purchase_order_number', $ref)
            ->first();

        if ($purchaseOrder) {
            if ($purchaseOrder->status === 'draft') {
                return redirect()->route('purchase-order.edit', $purchaseOrder->id);
            }

            return redirect()->route('purchase-order.print', ['ids' => $purchaseOrder->id]);
        }

        return redirect()->route('journal-report.index')->with('error', 'Reference ' . $ref . ' not found.');
    }

    public function export($dateStart, $dateEnd, $filter = null)
    {
        $dateStartObj = \DateTime::createFromFormat('m-Y', $dateStart);
        $dateStartObj->modify('first day of this month');
        $dateEndObj = \DateTime::createFromFormat('m-Y', $dateEnd);
        $dateEndObj->modify('last day of this month');

        // Get raw data from database
        $rawData = JournalTransactionModel::allForPrint($dateStartObj->format('Y-m-d'), $dateEndObj->format('Y-m-d'), $filter);

        // Process data efficiently in single loop
        $reports = [];
        $currentVoucherNumber = '';
        $currentParentIndex = -1;

        foreach ($rawData as $index => $row) {
            if ($currentVoucherNumber != $row['number']) {
                $currentVoucherNumber = $row['number'];
                $currentParentIndex = $index;

                $reports[$index] = [
                    'date' => date('d M Y', strtotime($row['date'])),
                    'number' => $row['number'],
                    'details' => [
                        $this->generateTableDetail($row)
                    ]
                ];
            } else {
                $reports[$currentParentIndex]['details'][] = $this->generateTableDetail($row);
            }
        }

        $fileName = 'Journal_Report_' . $dateStart . '_to_' . $dateEnd;
        if (!empty($filter)) {
            $fileName .= '_' . str_replace(' ', '_', $filter);
        }
        $fileName .= '_' . date('YmdHis') . '.xlsx';

        return Excel::download(
            new JournalReportExport(
                $reports,
                $dateStartObj->format('F Y'),
                $dateEndObj->format('F Y') != $dateStartObj->format('F Y') ? $dateEndObj->format('F Y') : null
            ),
            $fileName
        );
    }

    private function generateTableDetail($row)
    {
        $description = "(" . $row['account_number'] . ") " . $row['account_name'];

        if (!empty($row['description'])) {
            $description .= "\n" . $row['description'];
        }

        return [
            "description" => $description,
            "total_debit" => $row['total_debit'],
            "total_credit" => $row['total_credit'],
            "ref" => $row['ref'] ?? null,
        ];
    }

    private function generateTableDetailPrint($row)
    {
        return $this->generateTableDetail($row);
    }
}
