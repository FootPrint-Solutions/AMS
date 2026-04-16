<?php

namespace App\Http\Controllers\AccountingReport;

use App\Http\Controllers\Controller;
use App\Models\Accounting\JournalTransactionModel;
use App\Models\Master\CompanyProfile;
use App\Models\Accounting\ChartOfAccountModel;
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

    private function generateTableDetailPrint($row)
    {
        $detail = [];
        $detail["description"] = "(" . $row['account_number'] . ") " . $row['account_name'];

        if (!empty($row['description'])) {
            $detail["description"] .= "\n" . $row['description'];
        }

        $detail["total_debit"] = $row['total_debit'];
        $detail["total_credit"] = $row['total_credit'];
        return $detail;
    }
}
