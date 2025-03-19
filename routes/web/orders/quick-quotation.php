<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orders\QuickQuotation;

Route::get('/quotation/quick', [QuickQuotation::class, 'index'])->name('quotation.index')->middleware('permission:view_quick_quotation');
Route::get('/quotation/customer/find', [QuickQuotation::class, 'findCustomer'])->name('quotation.findCustomer')->middleware('permission:view_quick_quotation');
Route::post('/quotation/customer/share', [QuickQuotation::class, 'shareFormPersonalDetails'])->name('quotation.shareFormPersonalDetails')->middleware('permission:view_quick_quotation');
Route::get('/quotation/customer/vehicle/find', [QuickQuotation::class, 'findVehicleByIdCustomer'])->name('quotation.findVehicleByIdCustomer')->middleware('permission:view_quick_quotation');
Route::get('/quotation/vehicle/find', [QuickQuotation::class, 'findVehicleByIdVehicle'])->name('quotation.findVehicleByIdVehicle')->middleware('permission:view_quick_quotation');
Route::get('/quotation/customer/maps/near', [QuickQuotation::class, 'getMapsNearAddressCustomer'])->name('quotation.getMapsNearAddressCustomer')->middleware('permission:view_quick_quotation');
Route::post('/quotation/battery/share', [QuickQuotation::class, 'shareBattery'])->name('quotation.shareBattery')->middleware('permission:view_quick_quotation');
Route::get('/quotation/checkout', [QuickQuotation::class, 'getCheckoutPreview'])->name('quotation.getCheckoutPreview')->middleware('permission:view_quick_quotation');
Route::get('/quotation/payment', [QuickQuotation::class, 'getPaymentPreview'])->name('quotation.getPaymentPreview')->middleware('permission:view_quick_quotation');
Route::post('/quotation/battery/copy', [QuickQuotation::class, 'getBatteryCopyDetail'])->name('quotation.getBatteryCopyDetail')->middleware('permission:view_quick_quotation');
Route::post('/quotation/share-invoice', [QuickQuotation::class, 'shareInvoice'])->name('quotation.shareInvoice')->middleware('permission:view_quick_quotation')->middleware('permission:view_quick_quotation');
Route::post('/quotation/share-payment-details', [QuickQuotation::class, 'sharePaymentDetails'])->name('quotation.sharePaymentDetails')->middleware('permission:view_quick_quotation');
Route::post('/quotation/save-data', [QuickQuotation::class, 'saveData'])->name('quotation.saveData')->middleware('permission:view_quick_quotation');
Route::post('/quotation/customer/copy', [QuickQuotation::class, 'getCustomerCopyDetail'])->name('quotation.getCustomerCopyDetail')->middleware('permission:view_quick_quotation');
Route::post('/quotation/checkout/copy', [QuickQuotation::class, 'getCheckoutCopyDetail'])->name('quotation.getCheckoutCopyDetail')->middleware('permission:view_quick_quotation');
Route::post('/quotation/payment-details/copy', [QuickQuotation::class, 'getPaymentDetailsCopyDetail'])->name('quotation.getPaymentDetailsCopyDetail')->middleware('permission:view_quick_quotation');
Route::get('/quotation/customer/findbycontact', [QuickQuotation::class, 'findCustomerByContact'])->name('quotation.findCustomerByContact')->middleware('permission:view_quick_quotation');
Route::get('/quotation/battery/find', [QuickQuotation::class, 'findBattery'])->name('quotation.findBattery')->middleware('permission:view_quick_quotation');
Route::get('/quotation/get-link-battery', [QuickQuotation::class, 'getLinkBattery'])->name('quotation.getLinkBattery')->middleware('permission:view_quick_quotation');
Route::get('/quotation/distributor/find', [QuickQuotation::class, 'findDistributor'])->name('quotation.findDistributor')->middleware('permission:view_quick_quotation');
Route::get('/quotation/battery/autoComplete', [QuickQuotation::class, 'autoCompleteBattery'])->name('quotation.autoCompleteBattery')->middleware('permission:view_quick_quotation');
Route::get('/quotation/work-order', [WorkOrder::class, 'index'])->name('quotation.workOrder')->middleware('permission:view_work_order');
Route::post('/quotation/battery/screenshot', [QuickQuotation::class, 'screenshotBattery'])->name('quotation.screenshotBattery')->middleware('permission:view_quick_quotation');
Route::post('/quotation/battery/save-screenshoot', [QuickQuotation::class, 'saveScreenshoot'])->name('quotation.saveScreenshoot')->middleware('permission:view_quick_quotation');
Route::get('/quotation/battery/filter/category', [QuickQuotation::class, 'filterBatteryByCategory'])->name('quotation.filterBatteryByCategory')->middleware('permission:view_quick_quotation');
Route::get('/quotation/battery/filter/cca', [QuickQuotation::class, 'filterBatteryByCCA'])->name('quotation.filterBatteryByCCA')->middleware('permission:view_quick_quotation');
Route::get('/quotation/battery/filter/capacity', [QuickQuotation::class, 'filterBatteryByCapacity'])->name('quotation.filterBatteryByCapacity')->middleware('permission:view_quick_quotation');
Route::get('/quotation/battery/filter/dimension', [QuickQuotation::class, 'filterBatteryByDimension'])->name('quotation.filterBatteryByDimension')->middleware('permission:view_quick_quotation');
Route::get('/quotation/fix/detail_percentage', [QuickQuotation::class, 'fixDetailPercentage'])->name('quotation.fixDetailPercentage')->middleware('permission:view_quick_quotation');
Route::get('/quotation/battery/autoCompleteCategory', [QuickQuotation::class, 'autoCompleteBatteryCategory'])->name('quotation.autoCompleteBatteryCategory')->middleware('permission:view_quick_quotation');
Route::get('/quotation/battery/autoCompleteCCA', [QuickQuotation::class, 'autoCompleteBatteryCCA'])->name('quotation.autoCompleteBatteryCCA')->middleware('permission:view_quick_quotation');
Route::get('/quotation/battery/autoCompleteCapacity', [QuickQuotation::class, 'autoCompleteBatteryCapacity'])->name('quotation.autoCompleteBatteryCapacity')->middleware('permission:view_quick_quotation');
Route::get('/quotation/battery/autoCompleteDimension', [QuickQuotation::class, 'autoCompleteBatteryDimension'])->name('quotation.autoCompleteBatteryDimension')->middleware('permission:view_quick_quotation');
Route::get('/quotation/battery/autoCompleteName', [QuickQuotation::class, 'autoCompleteBatteryName'])->name('quotation.autoCompleteBatteryName')->middleware('permission:view_quick_quotation');
