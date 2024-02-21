<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $subdirectories = $this->getAllSubdirectoriesOptimized(database_path('seeders'));
        foreach ($subdirectories as $subdirectory) {

            $this->loadSeedersFromSubdirectory($subdirectory);
        }
    }

    /**
     * @param $subdirectory
     * @return void
     */
    protected function loadSeedersFromSubdirectory($subdirectory)
    {
        $seederPath = database_path('seeders/' . $subdirectory);

        if (!is_dir($seederPath)) {
            return;
        }

        $files = scandir($seederPath);

        foreach ($files as $file) {
            if (Str::endsWith($file, '.php')) {
                $seederClass = 'Database\\Seeders\\' . $subdirectory . '\\' . Str::replaceLast('.php', '', $file);
                $this->call($seederClass);
            }
        }
    }


    /**
     * Get All Seeders SUb Directory
     * @param string $dir
     * @return array
     */
    private function getAllSubdirectoriesOptimized(string $dir): array
    {
        $subdirectories = [];
        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..') {
                $path = $dir . DIRECTORY_SEPARATOR . $item;
                if (is_dir($path)) {
                    $subdirectories[] = $path;
                    $subdirectoriesToAdd = $this->getAllSubdirectoriesOptimized($path);
                    foreach ($subdirectoriesToAdd as $subdirToAdd) {
                        $subdirToAdd = str_replace(database_path('seeders'), '', $subdirToAdd);
                        $subdirToAdd = ltrim($subdirToAdd, '\\');
                        $subdirectories[] = $subdirToAdd;
                    }
                }
            }
        }

        return $subdirectories;
    }
}
