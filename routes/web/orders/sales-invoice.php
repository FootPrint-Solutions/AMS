<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orders\SalesInvoice;
use App\Http\Controllers\Orders\SalesOrderBattery;

Route::get('/sales-invoice', [SalesInvoice::class, 'index'])->name('sales-invoice.index')->middleware('permission:view_sales_invoice');
Route::post('/sales-invoice/show', [SalesInvoice::class, 'show'])->name('sales-invoice.show')->middleware('permission:view_sales_invoice');
Route::get('/sales-invoice/invoice/{id}', [SalesInvoice::class, 'invoice'])->name('sales-invoice.invoice')->middleware('permission:view_sales_invoice');
Route::get('/sales-invoice/purchase-order/{id}', [SalesInvoice::class, 'purchaseOrder'])->name('sales-invoice.purchaseOrder')->middleware('permission:view_sales_invoice');
Route::get('/sales-invoice/create/{id}', [SalesInvoice::class, 'create'])->middleware('permission:add_sales_invoice');
Route::get('/sales-invoice/edit/{id}', [SalesInvoice::class, 'edit'])->name('sales-invoice.edit')->middleware('permission:edit_sales_invoice');
Route::post('/sales-invoice/store', [SalesInvoice::class, 'store'])->name('sales-invoice.store')->middleware('permission:add_sales_invoice');
Route::post('/sales-invoice/update', [SalesInvoice::class, 'update'])->name('sales-invoice.update')->middleware('permission:edit_sales_invoice');
Route::post('/sales-invoice/delete', [SalesInvoice::class, 'destroy'])->name('sales-invoice.delete')->middleware('permission:delete_sales_order_(so)');
Route::post('/sales-invoice/post', [SalesInvoice::class, 'post'])->name('sales-invoice.post')->middleware('permission:edit_sales_invoice');
Route::post('/sales-invoice/battery/show', [SalesOrderBattery::class, 'show'])->name('sales-invoice.battery.show')->middleware('permission:view_sales_invoice');
Route::post('/sales-invoice/battery/update/production-code', [SalesOrderBattery::class, 'updateProductionCode'])->name('sales-invoice.battery.update.production-code')->middleware('permission:edit_sales_invoice');
Route::get('/sales-invoice/technician/get/{shopId}', [SalesInvoice::class, 'getTechnicianByShop'])->name('sales-invoice.getTechnicianByShop')->middleware('permission:view_sales_invoice');
Route::get('/sales-invoice/work-order/{id}', [SalesInvoice::class, 'workOrderCreate'])->name('sales-invoice.workOrderCreate')->middleware('permission:view_sales_invoice');
Route::get('/sales-invoice/recreate-payment-link/{id}', [SalesInvoice::class, 'recreatePaymentLink'])->name('sales-invoice.recreatePaymentLink')->middleware('permission:view_sales_invoice');
Route::get('/sales-invoice/copy-link-payment/{id}', [SalesInvoice::class, 'copyPaymentLink'])->name('sales-invoice.copyPaymentLink')->middleware('permission:view_sales_invoice');
Route::get('/sales-invoice/get-purchase-order-number/{id}', [SalesInvoice::class, 'getPurchaseOrderNumber'])->name('sales-invoice.getPurchaseOrderNumber')->middleware('permission:view_sales_invoice');
Route::post('/sales-invoice/get-multiple-print-purchase-order', [SalesInvoice::class, 'multiplePurchaseOrder'])->name('sales-invoice.multiplePurchaseOrder')->middleware('permission:view_sales_invoice');
Route::get('/sales-invoice/multiple-print-purchase-order/{ids}', [SalesInvoice::class, 'multiplePrintPurchaseOrder'])->name('sales-invoice.multiplePrintPurchaseOrder')->middleware('permission:view_sales_invoice');
Route::post('/sales-invoice/export', [SalesInvoice::class, 'export'])->name('sales-invoice.export')->middleware('permission:view_sales_invoice');
Route::post('/sales-invoice/export/details', [SalesInvoice::class, 'exportDetails'])->name('sales-invoice.export.details')->middleware('permission:view_sales_invoice');
Route::post('/sales-invoice/get-multiple-print-consignment', [SalesInvoice::class, 'multipleConsignment'])->name('sales-invoice.multipleConsignment')->middleware('permission:view_sales_invoice');
Route::get('/sales-invoice/multiple-print-consignment/{ids}', [SalesInvoice::class, 'multipleConsignmentPrint'])->name('sales-invoice.multipleConsignmentPrint')->middleware('permission:view_sales_invoice');
Route::get('/sales-invoice/test-consignment', [SalesInvoice::class, 'testConsignment'])->name('sales-invoice.testConsignment'); // Test route
Route::get('/sales-invoice/consignment/create/{ids}', [SalesInvoice::class, 'createConsignment'])->name('sales-invoice.createConsignment')->middleware('permission:add_sales_invoice');
Route::get('/sales-invoices/by-distributor', [SalesInvoice::class, 'getByDistributor'])->name('sales-invoice.byDistributor')->middleware('permission:view_sales_invoice');
Route::post('/sales-invoices/by-distributor-datatable', [SalesInvoice::class, 'getByDistributorDataTable'])->name('sales-invoice.byDistributorDataTable')->middleware('permission:view_sales_invoice');
Route::post('/sales-invoices/add-consignment-temp', [SalesInvoice::class, 'addConsignmentTemp'])->name('sales-invoice.addConsignmentTemp')->middleware('permission:add_sales_invoice');
