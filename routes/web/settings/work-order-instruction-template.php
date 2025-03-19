<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\WorkOrderInstructionTemplate;

Route::get('/wo-instruction-template', [WorkOrderInstructionTemplate::class, 'index'])->name('wo-instruction-template.index')->middleware('permission:view_wo_instruction_template');
Route::post('/wo-instruction-template/show', [WorkOrderInstructionTemplate::class, 'show'])->name('wo-instruction-template.show')->middleware('permission:view_wo_instruction_template');
Route::get('/wo-instruction-template/create', [WorkOrderInstructionTemplate::class, 'create'])->middleware('permission:add_wo_instruction_template');
Route::post('/wo-instruction-template/edit', [WorkOrderInstructionTemplate::class, 'update'])->name('wo-instruction-template.edit')->middleware('permission:edit_wo_instruction_template');
Route::post('/wo-instruction-template/store', [WorkOrderInstructionTemplate::class, 'store'])->name('wo-instruction-template.store')->middleware('permission:add_wo_instruction_template');
Route::post('/wo-instruction-template/destroy', [WorkOrderInstructionTemplate::class, 'destroy'])->name('wo-instruction-template.delete')->middleware('permission:delete_wo_instruction_template');
Route::get('/wo-instruction-template/option/{id}', [WorkOrderInstructionTemplate::class, 'option'])->name('wo-instruction-template.option')->middleware('permission:view_wo_instruction_template');
Route::post('/wo-instruction-template/store/option', [WorkOrderInstructionTemplate::class, 'storeOption'])->name('wo-instruction-template.storeOption')->middleware('permission:add_wo_instruction_template');
Route::post('/wo-instruction-template/destroy/option', [WorkOrderInstructionTemplate::class, 'destroyOption'])->name('wo-instruction-template.deleteOption')->middleware('permission:delete_wo_instruction_template');
Route::post('/wo-instruction-template/toggle-status/option', [WorkOrderInstructionTemplate::class, 'toggleStatusOption'])->name('wo-instruction-template.toggleStatusOption')->middleware('permission:edit_wo_instruction_template');
Route::post('/work-order-instruction/delete-instruction', [WorkOrderInstruction::class, 'deleteInstruction'])->name('work-order-instruction.deleteInstruction')->middleware('permission:delete_wo_instruction');
