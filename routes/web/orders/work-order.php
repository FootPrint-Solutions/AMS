<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orders\WorkOrder;

Route::get('/work-order', [WorkOrder::class, 'index'])->name('work-order.index')->middleware('permission:view_work_order_(wo)');
Route::post('/work-order/show', [WorkOrder::class, 'show'])->name('work-order.show')->middleware('permission:view_work_order_(wo)');
Route::post('/work-order/print/', [WorkOrder::class, 'print'])->name('work-order.print')->middleware('permission:view_work_order_(wo)');
Route::post('/work-order/upload-image', [WorkOrder::class, 'uploadImage'])->name('work-order.uploadImage')->middleware('permission:view_work_order_(wo)');
Route::get('/work-order/print-technician-report/{id}', [WorkOrder::class, 'printTechnicianReport'])->name('work-order.printTechnicianReport')->middleware('permission:view_work_order_(wo)');
Route::post('/work-order/delete', [WorkOrder::class, 'destroy'])->name('work-order.delete')->middleware('permission:delete_work_order_(wo)');
Route::post('/work-order/detail', [WorkOrder::class, 'detail'])->name('work-order.detail')->middleware('permission:view_work_order_(wo)');
Route::post('/work-order/production-code', [WorkOrder::class, 'getProductionCode'])->name('work-order.getProductionCode')->middleware('permission:view_work_order_(wo)');
Route::get('/work-order/print-technician-report/{id}/{selectionPrintTechnicianReport}', [WorkOrder::class, 'printTechnicianReportTemplate'])->name('work-order.printTechnicianReportTemplate')->middleware('permission:view_work_order_(wo)');
Route::post('/work-order/copy-instruction', [WorkOrder::class, 'copyInstruction'])->name('work-order.copyInstruction')->middleware('permission:view_work_order_(wo)');
Route::post('/work-order/export', [WorkOrder::class, 'export'])->name('work-order.export')->middleware('permission:view_work_order_(wo)');
