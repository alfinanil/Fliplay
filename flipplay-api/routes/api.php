<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\PenggunaController;
use Illuminate\Support\Facades\Route;

// CRUD standar. ->parameters() dipakai karena Laravel menyingularkan "media" menjadi "medium"
// sehingga nama parameter tidak cocok dengan type-hint Media $media (route model binding).
Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);
Route::apiResource('pengguna', PenggunaController::class)->parameters(['pengguna' => 'pengguna']);

// Custom action: koleksi media milik pengguna (relasi many-to-many)
Route::get('/pengguna/{pengguna}/koleksi', [PenggunaController::class, 'koleksi']);
Route::post('/pengguna/{pengguna}/koleksi', [PenggunaController::class, 'addToCollection']);
Route::delete('/pengguna/{pengguna}/koleksi/{media}', [PenggunaController::class, 'removeFromCollection']);
