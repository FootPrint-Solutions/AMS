<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orders\QuickQuotation;

Route::post('/quotation/mobile/checkout', [QuickQuotation::class, 'mobileCheckout'])->name('quotation.mobileCheckout')->middleware('permission:view_quick_quotation');
Route::post('/quotation/mobile/detail/battery', [QuickQuotation::class, 'getBatteryDetail'])->name('quotation.getBatteryDetail')->middleware('permission:view_quick_quotation');
Route::post('/quotation/mobile/payment', [QuickQuotation::class, 'mobilePayment'])->name('quotation.mobilePayment')->middleware('permission:view_quick_quotation');
Route::post('/quotation/mobile/save-data', [QuickQuotation::class, 'saveDataMobile'])->name('quotation.saveDataMobile')->middleware('permission:view_quick_quotation');
