<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Publish\SalesOnline;

Route::get('/sales-online', [SalesOnline::class, 'index'])->name('sales-online.index')->middleware('permission:view_sales_(_online_)');
Route::post('/sales-online/view-details', [SalesOnline::class, 'viewDetails'])->name('sales-online.viewDetails')->middleware('permission:view_sales_(_online_)');
Route::post('/sales-online/sync-sales-online', [SalesOnline::class, 'syncSalesOnline'])->name('sales-online.syncSalesOnline')->middleware('permission:view_sales_(_online_)');
Route::post('/sales-online/save-to-sales-orders', [SalesOnline::class, 'saveToSalesOrders'])->name('sales-online.saveToSalesOrders')->middleware('permission:view_sales_(_online_)');
Route::post('/sales-online/get-form-sales-order', [SalesOnline::class, 'getFormSalesOrder'])->name('sales-online.getFormSalesOrder')->middleware('permission:view_sales_(_online_)');
Route::post('/sales-online/get-technician', [SalesOnline::class, 'getTechnicianByShop'])->name('sales-online.getTechnicianByShop')->middleware('permission:view_sales_(_online_)');
Route::post('/sales-online/show', [SalesOnline::class, 'show'])->name('sales-online.show')->middleware('permission:view_sales_(_online_)');
Route::post('/sales-online/send-queue-whatsapp', [SalesOnline::class, 'sendQueueWhatsapp'])->name('sales-online.sendQueueWhatsapp')->middleware('permission:view_sales_(_online_)');
