<?php

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPdf\Facades\Pdf;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/run-link', function () {
    $shortcut = public_path('storage');
    $target = base_path('storage/app/public');

    if(file_exists($shortcut) || is_link($shortcut))
    {
        if(is_dir($shortcut) && !is_link($shortcut)) {
            rmdir($shortcut);
        }
        else {
            @unlink($shortcut);
        }
    }

    if(symlink($target, $shortcut))
    {
        return 'The "public/storage" directory has been created successfully.';
    }

    return 'Failed to create storage link. please check folder permissions.';

});
