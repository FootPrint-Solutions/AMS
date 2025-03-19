<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\MessageTemplate;

Route::get('/template/message', [MessageTemplate::class, 'index'])->name('message-template.index')->middleware('permission:view_message_templates');
Route::post('/template/message/update', [MessageTemplate::class, 'update'])->name('message-template.update')->middleware('permission:edit_message_templates');
