<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Publish\DataCategory;

Route::get('/data-category', [DataCategory::class, 'index'])->name('data-category.index')->middleware('permission:view_category_(_online_)');
Route::post('/data-category/sync-category', [DataCategory::class, 'syncCategory'])->name('data-category.syncCategory')->middleware('permission:view_category_(_online_)');
Route::post('/data-category/count-parent-category', [DataCategory::class, 'countParentCategory'])->name('data-category.countParentCategory')->middleware('permission:view_category_(_online_)');
Route::post('/data-category/send-parent-category-partially', [DataCategory::class, 'sendParentCategoryPartially'])->name('data-category.sendParentCategoryPartially')->middleware('permission:view_category_(_online_)');
Route::post('/data-category/count-category', [DataCategory::class, 'countCategory'])->name('data-category.countCategory')->middleware('permission:view_category_(_online_)');
Route::post('/data-category/send-category-partially', [DataCategory::class, 'sendCategoryPartially'])->name('data-category.sendCategoryPartially')->middleware('permission:view_category_(_online_)');
Route::post('/work-order-instruction/set-uncomplete', [WorkOrderInstruction::class, 'setUncomplete'])->name('work-order-instruction.setUncomplete')->middleware('permission:view_wo_instruction');
