<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterData\Company\Company;

Route::get('/company', [Company::class, 'index'])->name('company.index');
Route::post('/company/update', [Company::class, 'update'])->name('company.update');
