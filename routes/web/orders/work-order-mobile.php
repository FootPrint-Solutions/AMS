<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orders\WorkOrder;

Route::get('/work-order/mobile/lazy-load/list', [WorkOrder::class, 'lazyLoadList'])->name('work-order.lazyLoadList')->middleware('permission:view_work_order_(wo)');
Route::get('/work-order/mobile/detail', [WorkOrder::class, 'getWorkOrderDetail'])->name('work-order.getWorkOrderDetail')->middleware('permission:view_work_order_(wo)');
Route::post('/work-order/mobile/delete', [WorkOrder::class, 'destroy'])->middleware('permission:delete_work_order_(wo)');
Route::get('/work-order/mobile/print-technician-report/{id}', [WorkOrder::class, 'printTechnicianReportMobile'])->name('work-order.printTechnicianReportMobile')->middleware('permission:view_work_order_(wo)');
Route::post('/work-order/mobile/print/', [WorkOrder::class, 'printMobile'])->name('work-order.printMobile')->middleware('permission:view_work_order_(wo)');
// tracking
Route::post('/work-order/mobile/track/start', [WorkOrder::class, 'startTracking'])->name('work-order.startTracking')->middleware('permission:view_work_order_(wo)');
Route::post('/work-order/mobile/track/end', [WorkOrder::class, 'endTracking'])->name('work-order.endTracking')->middleware('permission:view_work_order_(wo)');
Route::post('/work-order/mobile/track/update', [WorkOrder::class, 'updateTracking'])->name('work-order.updateTracking')->middleware('permission:view_work_order_(wo)');
