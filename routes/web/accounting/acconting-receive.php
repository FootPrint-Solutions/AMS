<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\AccountingReceive;

/*
|--------------------------------------------------------------------------
| Accounting Expense Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for accounting expense functionality.
|
*/

Route::prefix('accounting-receive')->group(function () {
    Route::get('/', [AccountingReceive::class, 'index'])->name('accounting-receive.index')->middleware('permission:view_expense_data');
    Route::get('/create', [AccountingReceive::class, 'create'])->name('accounting-receive.create')->middleware('permission:add_expense_data');
    Route::post('/store', [AccountingReceive::class, 'store'])->name('accounting-receive.store')->middleware('permission:add_expense_data');
    Route::post('/show', [AccountingReceive::class, 'show'])->name('accounting-receive.show')->middleware('permission:view_expense_data');
    Route::get('/edit/{id}', [AccountingReceive::class, 'edit'])->name('accounting-receive.edit')->middleware('permission:edit_expense_data');
    Route::post('/update', [AccountingReceive::class, 'update'])->name('accounting-receive.update')->middleware('permission:edit_expense_data');
    Route::post('/destroy', [AccountingReceive::class, 'destroy'])->name('accounting-receive.destroy')->middleware('permission:delete_expense_data');
    Route::post('/get-data', [AccountingReceive::class, 'getData'])->name('accounting-receive.get-data')->middleware('permission:view_expense_data');
    Route::post('/post', [AccountingReceive::class, 'post'])->name('accounting-receive.post')->middleware('permission:add_expense_data');
    Route::get('/items/{accountingReceiveId}', [AccountingReceive::class, 'getAccountingExpenseItems'])->name('accounting-receive.items')->middleware('permission:view_expense_data');
});
