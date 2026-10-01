<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\PostController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/contact', function() {
    $name = 'Cristian';
    return view('contact', ['name' => $name]);
})->name('contact');

Route::resource('post', PostController::class);
