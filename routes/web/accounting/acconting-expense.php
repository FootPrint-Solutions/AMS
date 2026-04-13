<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\AccountingExpense;

/*
|--------------------------------------------------------------------------
| Accounting Expense Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for accounting expense functionality.
|
*/

Route::prefix('accounting-expense')->group(function () {
    Route::get('/', [AccountingExpense::class, 'index'])->name('accounting-expense.index')->middleware('permission:view_expense_data');
    Route::get('/create', [AccountingExpense::class, 'create'])->name('accounting-expense.create')->middleware('permission:add_expense_data');
    Route::post('/store', [AccountingExpense::class, 'store'])->name('accounting-expense.store')->middleware('permission:add_expense_data');
    Route::post('/show', [AccountingExpense::class, 'show'])->name('accounting-expense.show')->middleware('permission:view_expense_data');
    Route::get('/edit/{id}', [AccountingExpense::class, 'edit'])->name('accounting-expense.edit')->middleware('permission:edit_expense_data');
    Route::post('/update', [AccountingExpense::class, 'update'])->name('accounting-expense.update')->middleware('permission:edit_expense_data');
    Route::post('/destroy', [AccountingExpense::class, 'destroy'])->name('accounting-expense.destroy')->middleware('permission:delete_expense_data');
    Route::post('/get-data', [AccountingExpense::class, 'getData'])->name('accounting-expense.get-data')->middleware('permission:view_expense_data');
    Route::post('/post', [AccountingExpense::class, 'post'])->name('accounting-expense.post')->middleware('permission:add_expense_data');
    Route::get('/items/{accountingExpenseId}', [AccountingExpense::class, 'getAccountingExpenseItems'])->name('accounting-expense.items')->middleware('permission:view_expense_data');
});
