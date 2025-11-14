<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orders\SalesOrder;
use App\Http\Controllers\Orders\SalesOrderBattery;

Route::get('/sales-order', [SalesOrder::class, 'index'])->name('sales-order.index')->middleware('permission:view_sales_order_(so)');
Route::post('/sales-order/show', [SalesOrder::class, 'show'])->name('sales-order.show')->middleware('permission:view_sales_order_(so)');
Route::post('/sales-order/summary', [SalesOrder::class, 'summary'])->name('sales-order.summary')->middleware('permission:view_sales_order_(so)');
Route::get('/sales-order/invoice/{id}', [SalesOrder::class, 'invoice'])->name('sales-order.invoice')->middleware('permission:view_sales_order_(so)');
Route::get('/sales-order/purchase-order/{id}', [SalesOrder::class, 'purchaseOrder'])->name('sales-order.purchaseOrder')->middleware('permission:view_sales_order_(so)');
Route::get('/sales-order/create', [SalesOrder::class, 'create'])->middleware('permission:add_sales_order_(so)');
Route::get('/sales-order/edit/{id}', [SalesOrder::class, 'edit'])->name('sales-order.edit')->middleware('permission:edit_sales_order_(so)');
Route::post('/sales-order/store', [SalesOrder::class, 'store'])->name('sales-order.store')->middleware('permission:add_sales_order_(so)');
Route::post('/sales-order/update', [SalesOrder::class, 'update'])->name('sales-order.update')->middleware('permission:edit_sales_order_(so)');
Route::post('/sales-order/delete', [SalesOrder::class, 'destroy'])->name('sales-order.delete')->middleware('permission:delete_sales_order_(so)');
Route::post('/sales-order/post', [SalesOrder::class, 'post'])->name('sales-order.post')->middleware('permission:edit_sales_order_(so)');
Route::post('/sales-order/battery/show', [SalesOrderBattery::class, 'show'])->name('sales-order.battery.show')->middleware('permission:view_sales_order_(so)');
Route::post('/sales-order/battery/update/production-code', [SalesOrderBattery::class, 'updateProductionCode'])->name('sales-order.battery.update.production-code')->middleware('permission:edit_sales_order_(so)');
Route::get('/sales-order/technician/get/{shopId}', [SalesOrder::class, 'getTechnicianByShop'])->name('sales-order.getTechnicianByShop')->middleware('permission:view_sales_order_(so)');
Route::get('/sales-order/work-order/{id}', [SalesOrder::class, 'workOrderCreate'])->name('sales-order.workOrderCreate')->middleware('permission:view_sales_order_(so)');
Route::get('/sales-order/recreate-payment-link/{id}', [SalesOrder::class, 'recreatePaymentLink'])->name('sales-order.recreatePaymentLink')->middleware('permission:view_sales_order_(so)');
Route::get('/sales-order/copy-link-payment/{id}', [SalesOrder::class, 'copyPaymentLink'])->name('sales-order.copyPaymentLink')->middleware('permission:view_sales_order_(so)');
Route::get('/sales-order/get-purchase-order-number/{id}', [SalesOrder::class, 'getPurchaseOrderNumber'])->name('sales-order.getPurchaseOrderNumber')->middleware('permission:view_sales_order_(so)');
Route::post('/sales-order/get-multiple-print-purchase-order', [SalesOrder::class, 'multiplePurchaseOrder'])->name('sales-order.multiplePurchaseOrder')->middleware('permission:view_sales_order_(so)');
Route::get('/sales-order/multiple-print-purchase-order/{ids}', [SalesOrder::class, 'multiplePrintPurchaseOrder'])->name('sales-order.multiplePrintPurchaseOrder')->middleware('permission:view_sales_order_(so)');
Route::post('/sales-order/export', [SalesOrder::class, 'export'])->name('sales-order.export')->middleware('permission:view_sales_order_(so)');
Route::post('/sales-order/export/details', [SalesOrder::class, 'exportDetails'])->name('sales-order.export.details')->middleware('permission:view_sales_order_(so)');
Route::post('/sales-order/post/check', [SalesOrder::class, 'checkPost'])->name('sales-order.checkPost')->middleware('permission:edit_sales_order_(so)');


// Sales Order Recycle
Route::get('/sales-order/create-recycle', [SalesOrder::class, 'createRecycle'])->middleware('permission:add_sales_order_(so)');
Route::post('/sales-order/recycle/store', [SalesOrder::class, 'storeRecycle'])->name('sales-order.recycle.store')->middleware('permission:add_sales_order_(so)');
Route::post('/sales-order/recycle/update', [SalesOrder::class, 'updateRecycle'])->name('sales-order.recycle.update')->middleware('permission:edit_sales_order_(so)');
