<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('checkout/success', function () {
    return 'success' ;
})->name('checkout.success');


Route::get('checkout/cancel', function () {
    return 'cancel' ;
})->name('checkout.cancel');
