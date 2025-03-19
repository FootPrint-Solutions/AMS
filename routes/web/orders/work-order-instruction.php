<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orders\WorkOrderInstruction;

Route::get('/work-order-instruction', [WorkOrderInstruction::class, 'index'])->name('work-order-instruction.index')->middleware('permission:view_wo_instruction');
Route::post('/work-order-instruction/show', [WorkOrderInstruction::class, 'show'])->name('work-order-instruction.show')->middleware('permission:view_wo_instruction');
Route::get('/wo/{work_order_instruction_number}', [WorkOrderInstruction::class, 'InstructionDetail'])->name('work-order-instruction.instructionDetail')->middleware('permission:view_wo_instruction');
Route::get('/wo-new/{work_order_instruction_number}', [WorkOrderInstruction::class, 'InstructionDetailNew'])->name('work-order-instruction.instructionDetailNew')->middleware('permission:view_wo_instruction');
Route::post('/work-order-instruction/delete', [WorkOrderInstruction::class, 'destroy'])->name('work-order-instruction.delete')->middleware('permission:delete_wo_instruction');
Route::post('/work-order-instruction/update', [WorkOrderInstruction::class, 'update'])->name('work-order-instruction.update')->middleware('permission:edit_wo_instruction');
Route::post('/work-order-instruction/detail', [WorkOrderInstruction::class, 'detail'])->name('work-order-instruction.detail')->middleware('permission:view_wo_instruction');
Route::get('/work-order-instruction/mobile/lazy-load/list', [WorkOrderInstruction::class, 'lazyLoadList'])->name('work-order-instruction.lazyLoadList')->middleware('permission:view_wo_instruction');
Route::post('/work-order-instruction/mobile/delete', [WorkOrderInstruction::class, 'destroy'])->middleware('permission:delete_wo_instruction');
Route::post('/work-order-instruction/upload-image', [WorkOrderInstruction::class, 'uploadImage'])->name('work-order-instruction.uploadImage')->middleware('permission:view_wo_instruction');
Route::post('/work-order-instruction/update-new', [WorkOrderInstruction::class, 'updateNewQueue'])->name('work-order-instruction.updateNew')->middleware('permission:edit_wo_instruction');
