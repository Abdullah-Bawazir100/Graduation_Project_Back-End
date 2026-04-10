<?php

namespace App\Application\User\Services;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
class UploadFileService {
    public function uploadFile($file, $folder): ?string
    {
        if (!$file) return null;

        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $fileName, 'public');

        return asset(Storage::url($path));
    }
}
