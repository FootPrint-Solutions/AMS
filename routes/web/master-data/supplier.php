<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Supplier\Supplier;

Route::get('/supplier', [Supplier::class, 'index'])->name('supplier.index')->middleware('permission:view_supplier');
Route::post('/supplier/show', [Supplier::class, 'show'])->name('supplier.show')->middleware('permission:view_supplier');
Route::get('/supplier/create', [Supplier::class, 'create'])->middleware('permission:add_customer');
Route::get('/supplier/edit/{id}', [Supplier::class, 'edit'])->name('supplier.edit')->middleware('permission:edit_supplier');
Route::post('/supplier/store', [Supplier::class, 'store'])->name('supplier.store')->middleware('permission:add_supplier');
Route::post('/supplier/update', [Supplier::class, 'update'])->name('supplier.update')->middleware('permission:edit_supplier');
Route::post('/supplier/toggle', [Supplier::class, 'updateStatus'])->name('supplier.toggle')->middleware('permission:edit_supplier');
