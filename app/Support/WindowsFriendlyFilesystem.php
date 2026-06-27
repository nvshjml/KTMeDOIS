<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem;

class WindowsFriendlyFilesystem extends Filesystem
{
    public function replace($path, $content, $mode = null)
    {
        if (DIRECTORY_SEPARATOR !== '\\') {
            parent::replace($path, $content, $mode);

            return;
        }

        clearstatcache(true, $path);

        $path = realpath($path) ?: $path;
        $tempPath = tempnam(dirname($path), basename($path));

        if (! is_null($mode)) {
            @chmod($tempPath, $mode);
        } else {
            @chmod($tempPath, 0777 - umask());
        }

        file_put_contents($tempPath, $content);

        if (@rename($tempPath, $path)) {
            return;
        }

        if (is_file($path)) {
            @unlink($path);
        }

        if (@rename($tempPath, $path)) {
            return;
        }

        file_put_contents($path, $content);
        @unlink($tempPath);
    }
}
