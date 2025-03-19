<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Developer\Audit;

Route::get('/audit', [Audit::class, 'index'])->name('audit.index')->middleware('permission:view_audit_log');
Route::post('/audit/show', [Audit::class, 'show'])->name('audit.show')->middleware('permission:view_audit_log');
