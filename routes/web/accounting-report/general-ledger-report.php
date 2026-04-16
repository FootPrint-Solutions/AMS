<?php

use App\Http\Controllers\AccountingReport\GeneralLedgerReport;
use Illuminate\Support\Facades\Route;

Route::prefix('general-ledger-report')->group(function () {
    Route::get('/', [GeneralLedgerReport::class, 'index'])->name('general-ledger.index');
    Route::get('/print/{date}', [GeneralLedgerReport::class, 'print'])->name('general-ledgers.print');
});
