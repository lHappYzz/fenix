<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/createOrder', [OrderController::class, 'createOrder'])->name('createOrder');
Route::get('/products', [OrderController::class, 'getProducts'])->name('getProducts');
