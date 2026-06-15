<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\Comission;

Route::get('/commission/', [Comission::class, 'index'])->name('commission.index')->middleware('permission:view_commission');
Route::get('/commission/create', [Comission::class, 'create'])->name('commission.create')->middleware('permission:add_commission');
Route::get('/commission/get-sales-orders', [Comission::class, 'getSalesOrders'])->name('commission.get_sales_orders')->middleware('permission:view_commission');
Route::post('/commission/store', [Comission::class, 'store'])->name('commission.store')->middleware('permission:add_commission');
Route::get('/commission/{id}/edit', [Comission::class, 'edit'])->name('commission.edit')->middleware('permission:edit_commission');
Route::post('/commission/update', [Comission::class, 'update'])->name('commission.update')->middleware('permission:edit_commission');
Route::post('/commission/delete', [Comission::class, 'destroy'])->name('commission.delete')->middleware('permission:delete_commission');
