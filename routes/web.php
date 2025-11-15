<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;

Route::get('/', [AddressController::class, 'index'])->name('index');
Route::post('/show', [AddressController::class, 'show'])->name('show');
