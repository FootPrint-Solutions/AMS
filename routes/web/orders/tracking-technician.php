<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orders\TrackingTechnician;

Route::get('/tracking-technician', [TrackingTechnician::class, 'index'])->name('tracking-technician.index')->middleware('permission:view_tracking_technician');
Route::post('/tracking-technician/show', [TrackingTechnician::class, 'show'])->name('tracking-technician.show')->middleware('permission:view_tracking_technician');
Route::post('/tracking-technician/share', [TrackingTechnician::class, 'share'])->name('tracking-technician.share')->middleware('permission:view_tracking_technician');
Route::post('/tracking-technician/delete', [TrackingTechnician::class, 'delete'])->name('tracking-technician.delete')->middleware('permission:delete_tracking_technician');
