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
        try {
            ini_set('memory_limit', '512M');

            // Pindahkan file dari direktori sementara
            $tempFile = storage_path("app/{$this->tempPath}");
            $destination = public_path("{$this->imagePath}/{$this->imageName}");

            if (file_exists($tempFile)) {
                copy($tempFile, $destination);
                unlink($tempFile); // Hapus file sementara

                $img = Image::make($destination)->resize(900, 900, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                // if ($img->filesize() > 1000000) {
                $img->save($destination);
                // }
            }
        } catch (\Throwable $th) {
            // Do nothing
        }
    }
}
