<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait StoreFileTrait
{
    /**
     * Store single file.
     */
    public function storeFile(
        ?UploadedFile $file,
        string $folder = 'uploads',
        ?string $oldFile = null,
        string $disk = 'public'
    ): ?string {

        if (!$file) {
            return $oldFile;
        }

        if ($oldFile && Storage::disk($disk)->exists($oldFile)) {
            Storage::disk($disk)->delete($oldFile);
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($folder, $filename, $disk);
    }

    /**
     * Store multiple files.
     */
    public function storeMultipleFiles(
        array $files,
        string $folder = 'uploads',
        string $disk = 'public'
    ): array {

        $paths = [];

        foreach ($files as $file) {

            if (!$file instanceof UploadedFile) {
                continue;
            }

            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            $paths[] = $file->storeAs($folder, $filename, $disk);
        }

        return $paths;
    }

    /**
     * Delete file.
     */
    public function deleteFile(?string $path, string $disk = 'public'): bool
    {
        if (!$path) {
            return false;
        }

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Delete multiple files.
     */
    public function deleteMultipleFiles(array $paths, string $disk = 'public'): void
    {
        foreach ($paths as $path) {
            $this->deleteFile($path, $disk);
        }
    }
}