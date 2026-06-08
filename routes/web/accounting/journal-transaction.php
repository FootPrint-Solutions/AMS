<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\JournalTransaction;

/*
|--------------------------------------------------------------------------
| Journal Transaction Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for journal transaction functionality.
|
*/

Route::prefix('journal-transaction')->group(function () {
    Route::get('/', [JournalTransaction::class, 'index'])->name('journal-transaction.index')->middleware('permission:view_journal_transaction');
    Route::get('/create', [JournalTransaction::class, 'create'])->name('journal-transaction.create')->middleware('permission:add_journal_transaction');
    Route::post('/store', [JournalTransaction::class, 'store'])->name('journal-transaction.store')->middleware('permission:add_journal_transaction');
    Route::post('/import', [JournalTransaction::class, 'import'])->name('journal-transaction.import')->middleware('permission:add_journal_transaction');
    Route::get('/template', [JournalTransaction::class, 'importTemplate'])->name('journal-transaction.template')->middleware('permission:add_journal_transaction');
    Route::post('/show', [JournalTransaction::class, 'show'])->name('journal-transaction.show')->middleware('permission:view_journal_transaction');
    Route::get('/export', [JournalTransaction::class, 'export'])->name('journal-transaction.export')->middleware('permission:view_journal_transaction');
    Route::get('/edit/{id}', [JournalTransaction::class, 'edit'])->name('journal-transaction.edit')->middleware('permission:edit_journal_transaction');
    Route::post('/update', [JournalTransaction::class, 'update'])->name('journal-transaction.update')->middleware('permission:edit_journal_transaction');
    Route::post('/destroy', [JournalTransaction::class, 'destroy'])->name('journal-transaction.destroy')->middleware('permission:delete_journal_transaction');
    Route::post('/get-data', [JournalTransaction::class, 'getData'])->name('journal-transaction.get-data')->middleware('permission:view_journal_transaction');
    Route::post('/post', [JournalTransaction::class, 'post'])->name('journal-transaction.post')->middleware('permission:add_journal_transaction');
    Route::get('/items/{journalTransactionId}', [JournalTransaction::class, 'getJournalTransactionItems'])->name('journal-transaction.items')->middleware('permission:view_journal_transaction');
});
