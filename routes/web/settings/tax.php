<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\Tax;

Route::get('/tax', [Tax::class, 'index'])->name('tax.index')->middleware('permission:view_tax');
Route::post('/tax/show', [Tax::class, 'show'])->name('tax.show')->middleware('permission:view_tax');
Route::get('/tax/create', [Tax::class, 'create'])->middleware('permission:add_tax');
Route::get('/tax/edit/{id}', [Tax::class, 'edit'])->name('tax.edit')->middleware('permission:edit_tax');
Route::post('/tax/store', [Tax::class, 'store'])->name('tax.store')->middleware('permission:add_tax');
Route::post('/tax/update', [Tax::class, 'update'])->name('tax.update')->middleware('permission:edit_tax');
Route::post('/tax/toggle', [Tax::class, 'updateStatus'])->name('tax.toggle')->middleware('permission:edit_tax');
Route::post('/tax/destroy', [Tax::class, 'destroy'])->name('tax.destroy')->middleware('permission:delete_tax');
