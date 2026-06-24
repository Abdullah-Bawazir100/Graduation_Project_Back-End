<?php

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPdf\Facades\Pdf;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/run-link', function () {
    $target = storage_path('app/public');
    $shortcut = public_path('storage');

    if(file_exists($shortcut))
    {
        return 'The "public/storage" directory already exists.';
    }

    symlink($target, $shortcut);
    return 'The "public/storage" directory has been created successfully.';
});
