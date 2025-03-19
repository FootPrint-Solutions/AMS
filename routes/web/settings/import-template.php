<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\ImportTemplate\ImportTemplate;

Route::post('/template/import/update', [ImportTemplate::class, 'update'])->name('import-template.update')->middleware('permission:edit_import_templates');
Route::post('/template/import/delete', [ImportTemplate::class, 'delete'])->name('import-template.delete')->middleware('permission:delete_import_templates');
