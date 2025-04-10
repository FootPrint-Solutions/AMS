<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Profile;

Route::get('/profile', [Profile::class, 'index'])->name('profile.index');
Route::post('/profile/update', [Profile::class, 'update'])->name('profile.update');
Route::post('/profile/picture/update', [Profile::class, 'updateProfilePicture'])->name('profile.updateProfilePicture');
Route::post('/profile/password/update', [Profile::class, 'updatePassword'])->name('profile.updatePassword');
Route::get('/delete-session-whatsapp', [Profile::class, 'deleteSessionWhatsapp'])->name('profile.deleteSessionWhatsapp');
Route::post('/profile/api-key/update', [Profile::class, 'updateApiKey'])->name('profile.updateApiKey');
