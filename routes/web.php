<?php

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPdf\Facades\Pdf;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pdf-test', function () {
    return Pdf::html('
        <h1>Hello PDF</h1>
        <p>Laravel PDF is working!</p>
    ');
});
