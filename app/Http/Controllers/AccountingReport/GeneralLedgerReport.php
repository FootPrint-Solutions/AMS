<?php

namespace App\Http\Controllers\AccountingReport;

use App\Http\Controllers\Controller;
use App\Models\Accounting\JournalTransactionModel;
use Illuminate\Http\Request;
use App\Models\Accounting\ChartOfAccountModel;

class GeneralLedgerReport extends Controller
{
    public function index(Request $request)
    {
        $title = "General Ledger";
        $breadcrumbs = [
            "/" => "Home",
            "#" => "General Ledger"
        ];

        $accounts = ChartOfAccountModel::query()
            ->where('is_active', 1)
            ->orderBy('number', 'asc')
            ->get(['id', 'number', 'name']);

        return view('AccountingReport.GeneralLedgerReport.index', compact('title', 'breadcrumbs', 'accounts'));
    }

    public function print($date)
    {
        $dateStart = \DateTime::createFromFormat('m-Y', $date);
        if ($dateStart === false) {
            abort(404, 'Invalid period format. Use MM-YYYY.');
        }
        $dateStart->modify('first day of this month');
        $dateEnd = \DateTime::createFromFormat('m-Y', $date);
        if ($dateEnd === false) {
            abort(404, 'Invalid period format. Use MM-YYYY.');
        }
        $dateEnd->modify('last day of this month');

        // Handle multiple account filter
        $accountIds = request()->query('account_ids');
        $accountFilter = null;
        if ($accountIds) {
            $accountFilter = explode(',', $accountIds);
            $accountFilter = array_filter($accountFilter);
            sort($accountFilter, SORT_NUMERIC);
        }
        $tables = [];
        $data = JournalTransactionModel::allForGeneralLedger(
            $dateStart->format('Y-m-d'),
            $dateEnd->format('Y-m-d'),
            $accountFilter
        );

        if (empty($data)) {
            $coaQuery = ChartOfAccountModel::query();

            if (!empty($accountFilter)) {
                if (count($accountFilter) > 1) {
                    [$fromId, $toId] = $accountFilter;
                    $fromAccount = ChartOfAccountModel::find($fromId);
                    $toAccount = ChartOfAccountModel::find($toId);

                    if ($fromAccount && $toAccount) {
                        $coaQuery->whereBetween("number", [$fromAccount->number, $toAccount->number]);
                    } else {
                        $coaQuery->whereIn("id", $accountFilter);
                    }
                } else {
                    $coaQuery->where("id", $accountFilter[0]);
                }
            }

            $accounts = $coaQuery->orderBy('number', 'ASC')->get();

            foreach ($accounts as $acc) {
                $initial = JournalTransactionModel::initialBalanceEmpty($dateStart->format('Y-m-d'), $acc->id);
                $balance = $initial['totalDebit'] - $initial['totalCredit'];

                $tables[] = [
                    'number' => $acc->number,
                    'name' => $acc->name,
                    'initialBalance' => $initial,
                    'details' => [],
                    'totalDebit' => 0,
                    'totalCredit' => 0,
                    'endingDebitBalance' => $balance > 0 ? $balance : 0,
                    'endingCreditBalance' => $balance < 0 ? abs($balance) : 0,
                ];
            }
        }

        $reports = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $currentBalance = 0;
        $currentAccountId = -1;
        foreach ($data as $index => $datum) {
            if ($currentAccountId != $datum["account_id"]) {
                if ($currentAccountId != -1) {
                    $finalBalance = $reports['initialBalance']['totalDebit'] - $reports['initialBalance']['totalCredit'] + $totalDebit - $totalCredit;
                    if ($finalBalance < 0) {
                        $reports['endingDebitBalance'] = 0;
                        $reports['endingCreditBalance'] = abs($finalBalance);
                    } else {
                        $reports['endingDebitBalance'] = $finalBalance;
                        $reports['endingCreditBalance'] = 0;
                    }

                    // Set total debit and credit.
                    $reports['totalDebit'] = $totalDebit;
                    $reports['totalCredit'] = $totalCredit;

                    // Insert reports to tables.
                    $tables[] = $reports;

                    // Reset current reports.
                    $reports = [];
                }

                // Get initial balance.
                $reports['initialBalance'] = JournalTransactionModel::initialBalance($dateStart->format('Y-m-d'), $datum["account_id"]);
                $totalDebit = 0;
                $totalCredit = 0;
                $currentBalance = $reports['initialBalance']['totalDebit'] - $reports['initialBalance']['totalCredit'];

                // Set current account id.
                $currentAccountId = $datum["account_id"];

                // Get header information.
                if ($datum["account_master_id"]) {
                    $reports['number'] = $datum["account_master_number"];
                    $reports['name'] = $datum["account_master_name"];
                } else {
                    $reports['number'] = $datum["account_detail_number"];
                    $reports['name'] = $datum["account_detail_name"];
                }

                // Set details.
                $reports['details'] = [];
            }
            $contact = (!empty($datum['contact_person']) && strtolower($datum['contact_person']) !== 'bank')
                ? " (" . $datum['contact_person'] . ")"
                : "";
            $row = [];
            $row["date"] = formatDate($datum["date"]);
            $row["number"] = $reports['number'];
            $row["name"] = $reports["name"];
            $row["description"] = $datum["description"] . $contact;
            $row["number"] = $datum["number"];
            $row["totalDebit"] = $datum["total_debit"];
            $row["totalCredit"] = $datum["total_credit"];

            // Calculate balance.
            $currentBalance += $datum["total_debit"] - $datum["total_credit"];
            if ($currentBalance < 0) {
                $row["totalDebitBalance"] = 0;
                $row["totalCreditBalance"] = abs($currentBalance);
            } else {
                $row["totalDebitBalance"] = $currentBalance;
                $row["totalCreditBalance"] = 0;
            }
            $reports['details'][] = $row;

            $totalDebit += $datum["total_debit"];
            $totalCredit += $datum["total_credit"];

            if ($index == count($data) - 1) {
                // Calculate final ending balance for the last account
                $finalBalance = $reports['initialBalance']['totalDebit'] - $reports['initialBalance']['totalCredit'] + $totalDebit - $totalCredit;
                if ($finalBalance < 0) {
                    $reports['endingDebitBalance'] = 0;
                    $reports['endingCreditBalance'] = abs($finalBalance);
                } else {
                    $reports['endingDebitBalance'] = $finalBalance;
                    $reports['endingCreditBalance'] = 0;
                }

                // Set total debit and credit.
                $reports['totalDebit'] = $totalDebit;
                $reports['totalCredit'] = $totalCredit;

                // Insert reports to tables.
                $tables[] = $reports;
            }
        }


        $chartOfAccountDisplay = 'ALL';
        if ($accountFilter && count($accountFilter) > 0) {
            $accountNames = ChartOfAccountModel::whereIn('id', $accountFilter)->pluck('name')->toArray();
            if (count($accountNames) == 1) {
                $chartOfAccountDisplay = $accountNames[0];
            } elseif (count($accountNames) <= 5) {
                $chartOfAccountDisplay = implode(' | ', $accountNames);
            }
            else {
                $chartOfAccountDisplay = count($accountNames) . ' accounts: ' . implode(', ', array_slice($accountNames, 0, 3)) . '...';
            }
        }

        return view('AccountingReport.GeneralLedgerReport.print', array(
            'data' => array(
                'tables' => $tables,
                'dates' => $dateStart->format("F Y"),
                'chartOfAccount' => $chartOfAccountDisplay,
            ),
        ));
    }
}
