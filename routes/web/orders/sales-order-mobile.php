<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orders\SalesOrder;

Route::get('/sales-order/show/mobile/{status?}/{filter?}', [SalesOrder::class, 'getSalesOrders'])->name('sales-order.getSalesOrders')->middleware('permission:view_sales_order_(so)');
Route::get('/sales-order/show/detail/mobile/{id}', [SalesOrder::class, 'getSalesOrderDetail'])->name('sales-order.getSalesOrderDetail')->middleware('permission:view_sales_order_(so)');
Route::get('/sales-order/status', [SalesOrder::class, 'getSalesOrderStatus'])->name('sales-order.getSalesOrderStatus')->middleware('permission:view_sales_order_(so)');
