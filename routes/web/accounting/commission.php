<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\Comission;

Route::get('/commission/', [Comission::class, 'index'])->name('commission.index')->middleware('permission:view_commission');
Route::get('/commission/create', [Comission::class, 'create'])->name('commission.create')->middleware('permission:add_commission');
Route::get('/commission/get-sales-orders', [Comission::class, 'getSalesOrders'])->name('commission.get_sales_orders')->middleware('permission:view_commission');
