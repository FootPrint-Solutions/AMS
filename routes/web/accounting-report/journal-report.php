<?php

use App\Http\Controllers\AccountingReport\JournalReport;
use Illuminate\Support\Facades\Route;

Route::prefix('journal-report')->group(function () {
    Route::get('/', [JournalReport::class, 'index'])->name('journal-report.index')->middleware('permission:view_journal_report');
    Route::get('/print/{dateStart}/{dateEnd}/{filter?}', [JournalReport::class, 'print'])->name('journal-report.print')->middleware('permission:view_journal_report');
});
