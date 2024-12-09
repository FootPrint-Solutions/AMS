<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UploadWorkOrderImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tempPath, $imagePath, $imageName;

    public function __construct($tempPath, $imagePath, $imageName)
    {
        $this->tempPath = $tempPath;
        $this->imagePath = $imagePath;
        $this->imageName = $imageName;
    }

    public function handle()
    {
        // Pindahkan file dari direktori sementara
        $tempFile = storage_path("app/{$this->tempPath}");
        $destination = public_path("{$this->imagePath}/{$this->imageName}");

        if (file_exists($tempFile)) {
            copy($tempFile, $destination);
            unlink($tempFile); // Hapus file sementara

            // Kompres file jika lebih dari 1MB
            $img = Image::make($destination);
            if ($img->filesize() > 1000000) {
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destination);
            }
        }
    }
}
