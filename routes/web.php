<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/forgot-password', function () {
    return view('auth.reset-password-form');
})->name('password.reset');


Route::get('/reset-password', function () {
    return redirect('/forgot-password?' . http_build_query(request()->all()));
});