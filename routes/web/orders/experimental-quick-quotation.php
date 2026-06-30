<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Experimental\SmoothQuotationController;

Route::middleware(['auth'])->group(function () {
    Route::prefix('experimental/quick-quotation')->group(function () {
        Route::get('/', [SmoothQuotationController::class, 'index'])->name('experimental.quotation.index');
        Route::get('/customer/find', [SmoothQuotationController::class, 'findCustomer'])->name('experimental.quotation.findCustomer');
        Route::post('/customer/share', [SmoothQuotationController::class, 'shareFormPersonalDetails'])->name('experimental.quotation.shareFormPersonalDetails');
        Route::get('/customer/vehicle/find', [SmoothQuotationController::class, 'findVehicleByIdCustomer'])->name('experimental.quotation.findVehicleByIdCustomer');
        Route::get('/vehicle/find', [SmoothQuotationController::class, 'findVehicleByIdVehicle'])->name('experimental.quotation.findVehicleByIdVehicle');
        Route::post('/vehicle/store', [SmoothQuotationController::class, 'storeVehicle'])->name('experimental.quotation.storeVehicle');
        Route::get('/vehicles/list', [SmoothQuotationController::class, 'getVehicleList'])->name('experimental.quotation.getVehicleList');
        Route::get('/customer/maps/near', [SmoothQuotationController::class, 'getMapsNearAddressCustomer'])->name('experimental.quotation.getMapsNearAddressCustomer');
        Route::post('/battery/share', [SmoothQuotationController::class, 'shareBattery'])->name('experimental.quotation.shareBattery');
        Route::get('/checkout', [SmoothQuotationController::class, 'getCheckoutPreview'])->name('experimental.quotation.getCheckoutPreview');
        Route::get('/payment', [SmoothQuotationController::class, 'getPaymentPreview'])->name('experimental.quotation.getPaymentPreview');
        Route::post('/battery/copy', [SmoothQuotationController::class, 'getBatteryCopyDetail'])->name('experimental.quotation.getBatteryCopyDetail');
        Route::post('/share-invoice', [SmoothQuotationController::class, 'shareInvoice'])->name('experimental.quotation.shareInvoice');
        Route::post('/share-payment-details', [SmoothQuotationController::class, 'sharePaymentDetails'])->name('experimental.quotation.sharePaymentDetails');
        Route::post('/save-data', [SmoothQuotationController::class, 'saveData'])->name('experimental.quotation.saveData');
        Route::post('/customer/copy', [SmoothQuotationController::class, 'getCustomerCopyDetail'])->name('experimental.quotation.getCustomerCopyDetail');
        Route::post('/checkout/copy', [SmoothQuotationController::class, 'getCheckoutCopyDetail'])->name('experimental.quotation.getCheckoutCopyDetail');
        Route::post('/payment-details/copy', [SmoothQuotationController::class, 'getPaymentDetailsCopyDetail'])->name('experimental.quotation.getPaymentDetailsCopyDetail');
        Route::get('/customer/findbycontact', [SmoothQuotationController::class, 'findCustomerByContact'])->name('experimental.quotation.findCustomerByContact');
        Route::get('/battery/find', [SmoothQuotationController::class, 'findBattery'])->name('experimental.quotation.findBattery');
        Route::get('/get-link-battery', [SmoothQuotationController::class, 'getLinkBattery'])->name('experimental.quotation.getLinkBattery');
        Route::get('/distributor/find', [SmoothQuotationController::class, 'findDistributor'])->name('experimental.quotation.findDistributor');
        Route::get('/battery/autoComplete', [SmoothQuotationController::class, 'autoCompleteBattery'])->name('experimental.quotation.autoCompleteBattery');
        Route::post('/battery/screenshot', [SmoothQuotationController::class, 'screenshotBattery'])->name('experimental.quotation.screenshotBattery');
        Route::post('/battery/save-screenshoot', [SmoothQuotationController::class, 'saveScreenshoot'])->name('experimental.quotation.saveScreenshoot');
        Route::get('/battery/filter/category', [SmoothQuotationController::class, 'filterBatteryByCategory'])->name('experimental.quotation.filterBatteryByCategory');
        Route::get('/battery/filter/cca', [SmoothQuotationController::class, 'filterBatteryByCCA'])->name('experimental.quotation.filterBatteryByCCA');
        Route::get('/battery/filter/capacity', [SmoothQuotationController::class, 'filterBatteryByCapacity'])->name('experimental.quotation.filterBatteryByCapacity');
        Route::get('/battery/filter/dimension', [SmoothQuotationController::class, 'filterBatteryByDimension'])->name('experimental.quotation.filterBatteryByDimension');
        Route::get('/fix/detail_percentage', [SmoothQuotationController::class, 'fixDetailPercentage'])->name('experimental.quotation.fixDetailPercentage');
        Route::get('/battery/autoCompleteCategory', [SmoothQuotationController::class, 'autoCompleteBatteryCategory'])->name('experimental.quotation.autoCompleteBatteryCategory');
        Route::get('/battery/autoCompleteCCA', [SmoothQuotationController::class, 'autoCompleteBatteryCCA'])->name('experimental.quotation.autoCompleteBatteryCCA');
        Route::get('/battery/autoCompleteCapacity', [SmoothQuotationController::class, 'autoCompleteBatteryCapacity'])->name('experimental.quotation.autoCompleteBatteryCapacity');
        Route::get('/battery/autoCompleteDimension', [SmoothQuotationController::class, 'autoCompleteBatteryDimension'])->name('experimental.quotation.autoCompleteBatteryDimension');
        Route::get('/battery/autoCompleteName', [SmoothQuotationController::class, 'autoCompleteBatteryName'])->name('experimental.quotation.autoCompleteBatteryName');
    });
});
