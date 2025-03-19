<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\PrintTemplate;

Route::get('/template/print', [PrintTemplate::class, 'index'])->name('print-template.index')->middleware('permission:view_print_templates');
Route::post('/template/print/update', [PrintTemplate::class, 'update'])->middleware('permission:edit_print_templates');
Route::post('/template/show', [PrintTemplate::class, 'show'])->name('print-template.show')->middleware('permission:view_print_templates');
Route::get('/template/create', [PrintTemplate::class, 'create'])->middleware('permission:add_print_templates');
Route::post('/template/store', [PrintTemplate::class, 'store'])->name('print-template.store')->middleware('permission:add_print_templates');
Route::post('/template/destroy', [PrintTemplate::class, 'destroy'])->name('print-template.destroy')->middleware('permission:delete_print_templates');
Route::get('/template/edit/{id}', [PrintTemplate::class, 'edit'])->name('print-template.edit')->middleware('permission:edit_print_templates');
Route::post('/template/update', [PrintTemplate::class, 'update'])->middleware('permission:edit_print_templates');
Route::get('/template/details/{id}', [PrintTemplate::class, 'details'])->name('print-template.details')->middleware('permission:view_print_templates');
Route::post('/template/print/update/details', [PrintTemplate::class, 'updateDetails'])->name('print-template.update.details')->middleware('permission:edit_print_templates');
Route::post('/template/print/get/sub-task', [PrintTemplate::class, 'getSubTask'])->name('print-template.getSubTask')->middleware('permission:view_print_templates');
Route::post('/template/print/update/sub-task', [PrintTemplate::class, 'updateSubTask'])->name('print-template.update.subTask')->middleware('permission:edit_print_templates');
Route::post('/template/print/delete/sub-task', [PrintTemplate::class, 'deleteSubTask'])->name('print-template.delete.subTask')->middleware('permission:delete_print_templates');
