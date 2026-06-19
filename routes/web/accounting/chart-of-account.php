<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\ChartOfAccount;

Route::get('/chart-of-account', [ChartOfAccount::class, 'index'])->name('chart-of-account.index')->middleware('permission:view_chart_of_account');
Route::post('/chart-of-account/show', [ChartOfAccount::class, 'show'])->name('chart-of-account.show')->middleware('permission:view_chart_of_account');
Route::get('/chart-of-account/create', [ChartOfAccount::class, 'create'])->middleware('permission:add_chart_of_account');
Route::get('/chart-of-account/edit/{id}', [ChartOfAccount::class, 'edit'])->name('chart-of-account.edit')->middleware('permission:edit_chart_of_account');
Route::post('/chart-of-account/store', [ChartOfAccount::class, 'store'])->name('chart-of-account.store')->middleware('permission:add_chart_of_account');
Route::post('/chart-of-account/update', [ChartOfAccount::class, 'update'])->name('chart-of-account.update')->middleware('permission:edit_chart_of_account');
Route::post('/chart-of-account/destroy', [ChartOfAccount::class, 'destroy'])->name('chart-of-account.destroy')->middleware('permission:delete_chart_of_account');

Route::post('/chart-of-account/group/list', [ChartOfAccount::class, 'groupList'])->name('chart-of-account.group.list')->middleware('permission:view_chart_of_account');
Route::post('/chart-of-account/group/store', [ChartOfAccount::class, 'groupStore'])->name('chart-of-account.group.store')->middleware('permission:add_chart_of_account');
Route::post('/chart-of-account/group/update', [ChartOfAccount::class, 'groupUpdate'])->name('chart-of-account.group.update')->middleware('permission:edit_chart_of_account');
Route::post('/chart-of-account/group/destroy', [ChartOfAccount::class, 'groupDestroy'])->name('chart-of-account.group.destroy')->middleware('permission:delete_chart_of_account');
Route::post('/chart-of-account/group/next-number', [ChartOfAccount::class, 'groupNextNumber'])->name('chart-of-account.group.next-number')->middleware('permission:add_chart_of_account');
