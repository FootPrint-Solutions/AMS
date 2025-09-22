<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;
use Carbon\Carbon;

class FileManager extends Controller
{
    public function index()
    {
        $path = request('path', '');
        $storagePath = storage_path('app/' . $path);

        $files = [];
        $folders = [];

        if (File::exists($storagePath)) {
            $items = File::glob($storagePath . '/*');

            foreach ($items as $item) {
                $itemName = basename($item);
                $relativePath = str_replace(storage_path('app/'), '', $item);

                if (File::isDirectory($item)) {
                    $folders[] = [
                        'name' => $itemName,
                        'path' => $relativePath,
                        'type' => 'folder',
                        'size' => $this->getFolderSize($item),
                        'modified' => File::lastModified($item),
                        'items_count' => count(File::glob($item . '/*'))
                    ];
                } else {
                    $files[] = [
                        'name' => $itemName,
                        'path' => $relativePath,
                        'type' => 'file',
                        'size' => File::size($item),
                        'modified' => File::lastModified($item),
                        'extension' => File::extension($item),
                        'mime_type' => File::mimeType($item)
                    ];
                }
            }
        }

        // Sort folders first, then files
        $allItems = array_merge($folders, $files);

        return view('Settings.FileManager.index', [
            'items' => $allItems,
            'currentPath' => $path,
            'breadcrumbs' => $this->getBreadcrumbs($path)
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:102400', // 100MB max
            'path' => 'nullable|string'
        ]);

        $path = $request->input('path', '');
        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            $storedPath = $file->storeAs($path, $originalName);

            $uploadedFiles[] = [
                'name' => $originalName,
                'path' => $storedPath,
                'size' => $file->getSize()
            ];
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
            'files' => $uploadedFiles
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'string'
        ]);

        $deletedCount = 0;

        foreach ($request->items as $item) {
            $fullPath = storage_path('app/' . $item);

            if (File::exists($fullPath)) {
                if (File::isDirectory($fullPath)) {
                    File::deleteDirectory($fullPath);
                } else {
                    File::delete($fullPath);
                }
                $deletedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => $deletedCount . ' item(s) deleted successfully'
        ]);
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'path' => 'nullable|string'
        ]);

        $path = $request->input('path', '');
        $folderName = $request->input('name');
        $fullPath = storage_path('app/' . $path . '/' . $folderName);

        if (File::exists($fullPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Folder already exists'
            ]);
        }

        File::makeDirectory($fullPath, 0755, true);

        return response()->json([
            'success' => true,
            'message' => 'Folder created successfully',
            'folder' => [
                'name' => $folderName,
                'path' => $path . '/' . $folderName
            ]
        ]);
    }

    public function download(Request $request)
    {
        $item = $request->input('item');
        $fullPath = storage_path('app/' . $item);

        if (!File::exists($fullPath)) {
            abort(404);
        }

        if (File::isDirectory($fullPath)) {
            // Create ZIP for folder
            $zipName = basename($fullPath) . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.zip';
            $zipPath = storage_path('app/temp/' . $zipName);

            File::makeDirectory(dirname($zipPath), 0755, true);

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                $this->addFolderToZip($zip, $fullPath, basename($fullPath));
                $zip->close();

                return response()->download($zipPath)->deleteFileAfterSend(true);
            }
        } else {
            return response()->download($fullPath);
        }
    }

    public function backup(Request $request)
    {
        $items = $request->input('items', []);

        if (empty($items)) {
            // Backup entire storage
            $items = [''];
        }

        $backupName = 'backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.zip';
        $backupPath = storage_path('app/backups/' . $backupName);

        if (!File::exists(dirname($backupPath))) {
            File::makeDirectory(dirname($backupPath), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($backupPath, ZipArchive::CREATE) === TRUE) {
            foreach ($items as $item) {
                $fullPath = storage_path('app/' . $item);

                if (File::exists($fullPath)) {
                    if (File::isDirectory($fullPath)) {
                        $this->addFolderToZip($zip, $fullPath, $item ?: 'storage');
                    } else {
                        $zip->addFile($fullPath, $item);
                    }
                }
            }
            $zip->close();

            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully',
                'backup_file' => $backupName,
                'backup_path' => 'backups/' . $backupName
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to create backup'
        ]);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip'
        ]);

        $backupFile = $request->file('backup_file');
        $extractPath = storage_path('app/temp/restore_' . time());

        File::makeDirectory($extractPath, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($backupFile->getPathname()) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();

            // Move extracted files to storage
            $this->moveDirectory($extractPath, storage_path('app/'));
            File::deleteDirectory($extractPath);

            return response()->json([
                'success' => true,
                'message' => 'Backup restored successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to restore backup'
        ]);
    }

    public function archive(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'string',
            'archive_name' => 'nullable|string'
        ]);

        $items = $request->input('items');
        $archiveName = $request->input('archive_name', 'archive_' . Carbon::now()->format('Y-m-d_H-i-s')) . '.zip';
        $archivePath = storage_path('app/archives/' . $archiveName);

        if (!File::exists(dirname($archivePath))) {
            File::makeDirectory(dirname($archivePath), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($archivePath, ZipArchive::CREATE) === TRUE) {
            foreach ($items as $item) {
                $fullPath = storage_path('app/' . $item);

                if (File::exists($fullPath)) {
                    if (File::isDirectory($fullPath)) {
                        $this->addFolderToZip($zip, $fullPath, basename($fullPath));
                    } else {
                        $zip->addFile($fullPath, basename($fullPath));
                    }
                }
            }
            $zip->close();

            return response()->json([
                'success' => true,
                'message' => 'Archive created successfully',
                'archive_file' => $archiveName,
                'archive_path' => 'archives/' . $archiveName
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to create archive'
        ]);
    }

    private function getFolderSize($path)
    {
        $size = 0;
        $files = File::allFiles($path);

        foreach ($files as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    private function getBreadcrumbs($path)
    {
        $breadcrumbs = [['name' => 'Storage', 'path' => '']];

        if (!empty($path)) {
            $pathParts = explode('/', $path);
            $currentPath = '';

            foreach ($pathParts as $part) {
                $currentPath .= ($currentPath ? '/' : '') . $part;
                $breadcrumbs[] = ['name' => $part, 'path' => $currentPath];
            }
        }

        return $breadcrumbs;
    }

    private function addFolderToZip($zip, $folderPath, $zipPath)
    {
        $files = File::allFiles($folderPath);

        foreach ($files as $file) {
            $relativePath = str_replace($folderPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $zip->addFile($file->getPathname(), $zipPath . '/' . $relativePath);
        }
    }

    private function moveDirectory($source, $destination)
    {
        if (!File::isDirectory($source)) {
            return;
        }

        $items = File::glob($source . '/*');

        foreach ($items as $item) {
            $itemName = basename($item);
            $destPath = $destination . '/' . $itemName;

            if (File::isDirectory($item)) {
                if (!File::exists($destPath)) {
                    File::makeDirectory($destPath, 0755, true);
                }
                $this->moveDirectory($item, $destPath);
            } else {
                File::copy($item, $destPath);
            }
        }
    }
}
