<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\Expense;


Route::get('/expense/', [Expense::class, 'index'])->name('expense.index')->middleware('permission:view_expense');
Route::post('/expense/show', [Expense::class, 'show'])->name('expense.show')->middleware('permission:view_expense');
Route::get('/expense/create', [Expense::class, 'create'])->middleware('permission:add_expense');
Route::get('/expense/edit/{id}', [Expense::class, 'edit'])->name('expense.edit')->middleware('permission:edit_expense');
Route::post('/expense/store', [Expense::class, 'store'])->name('expense.store')->middleware('permission:add_expense');
Route::post('/expense/update', [Expense::class, 'update'])->name('expense.update')->middleware('permission:edit_expense');
Route::post('/expense/destroy', [Expense::class, 'destroy'])->name('expense.destroy')->middleware('permission:delete_expense');
Route::post('/expense/toggle', [Expense::class, 'toggle'])->name('expense.toggle')->middleware('permission:edit_expense');
