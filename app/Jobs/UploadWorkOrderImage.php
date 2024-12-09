<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;

class UploadWorkOrderImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $image, $imagePath, $imageName;

    public function __construct($image, $imagePath, $imageName)
    {
        $this->image = $image;
        $this->imagePath = $imagePath;
        $this->imageName = $imageName;
    }

    public function handle()
    {
        // Upload the image
        $this->image->move(public_path($this->imagePath), $this->imageName);

        // Compress if larger than 1MB
        $img = Image::make(public_path("{$this->imagePath}/{$this->imageName}"));
        if ($img->filesize() > 1000000) {
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path("{$this->imagePath}/{$this->imageName}"));
        }
    }
}
