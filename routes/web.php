<?php

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

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


Route::get('storage/{folder}/{filename}', function ($folder, $filename) {
    $path = storage_path('app/public/' . $folder . '/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    $file = file_get_contents($path);
    $type = mime_content_type($path);

    return Response::make($file, 200, [
        'Content-Type' => $type,
        'Content-Disposition' => 'inline; filename="' . $filename . '"'
    ]);
});
