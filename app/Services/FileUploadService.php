<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    public function storeDeliveryOrderFile(UploadedFile $file, string $prefix): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = $prefix.'_'.Str::uuid().'.'.$extension;

        return $file->storeAs('delivery-orders', $filename, 'local');
    }

    public function exists(string $path): bool
    {
        return Storage::disk('local')->exists($path);
    }
}
