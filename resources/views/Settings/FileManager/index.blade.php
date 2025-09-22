@extends('template.master', ['title' => 'File Manager'])

@section('content')
    <style>
        .file-manager-container {
            background: #f8f9fa;
            min-height: 100vh;
        }

        .toolbar {
            background: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .breadcrumb-nav {
            background: white;
            padding: 12px 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .breadcrumb {
            margin: 0;
            background: none;
        }

        .breadcrumb-item a {
            color: #1976d2;
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .files-container {
            padding: 20px;
        }

        .files-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .file-item {
            background: white;
            border: 2px solid transparent;
            border-radius: 8px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            user-select: none;
        }

        .file-item:hover {
            border-color: #e3f2fd;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .file-item.selected {
            border-color: #1976d2;
            background: #e3f2fd;
        }

        .file-checkbox {
            position: absolute;
            top: 8px;
            left: 8px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .file-item:hover .file-checkbox,
        .file-item.selected .file-checkbox {
            opacity: 1;
        }

        .file-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #666;
        }

        .file-icon.folder {
            color: #ffc107;
        }

        .file-icon.image {
            color: #4caf50;
        }

        .file-icon.document {
            color: #2196f3;
        }

        .file-icon.archive {
            color: #ff9800;
        }

        .file-name {
            font-weight: 500;
            margin-bottom: 4px;
            word-break: break-word;
            line-height: 1.3;
        }

        .file-info {
            color: #666;
            font-size: 0.875rem;
        }

        .upload-zone {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s;
        }

        .upload-zone.dragover {
            border-color: #1976d2;
            background: #e3f2fd;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 16px;
            display: block;
        }

        .selection-info {
            background: #e3f2fd;
            border: 1px solid #1976d2;
            border-radius: 4px;
            padding: 8px 16px;
            margin-bottom: 16px;
            display: none;
        }

        .progress-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .progress-card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            min-width: 300px;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        @media (max-width: 768px) {
            .files-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 12px;
            }

            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .toolbar-group {
                justify-content: center;
            }
        }

        .breadcrumb-item+.breadcrumb-item::before {
            float: left;
            padding-right: var(--bs-breadcrumb-item-padding-x);
            color: var(--bs-breadcrumb-divider-color);
            content: "/";
        }
    </style>

    <div class="file-manager-container">
        <!-- Toolbar -->
        <div class="toolbar">
            <div class="toolbar-group">
                <button class="btn btn-primary btn-sm" id="uploadBtn">
                    <i class="fas fa-upload"></i> Upload
                </button>
                <button class="btn btn-outline-primary btn-sm" id="createFolderBtn">
                    <i class="fas fa-folder-plus"></i> New Folder
                </button>
            </div>

            <div class="toolbar-group">
                <button class="btn btn-outline-success btn-sm" id="backupBtn" title="Backup Selected">
                    <i class="fas fa-download"></i> Backup
                </button>
                <button class="btn btn-outline-info btn-sm" id="archiveBtn" title="Archive Selected">
                    <i class="fas fa-archive"></i> Archive
                </button>
                <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#restoreModal">
                    <i class="fas fa-upload"></i> Restore
                </button>
            </div>

            <div class="toolbar-group ms-auto">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                    <label class="form-check-label" for="selectAllCheckbox">
                        Select All
                    </label>
                </div>
                <button class="btn btn-outline-danger btn-sm" id="deleteBtn" disabled>
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>

        <!-- Breadcrumb -->
        <div class="breadcrumb-nav">
            <nav>
                <ol class="breadcrumb">
                    @foreach ($breadcrumbs as $breadcrumb)
                        @if ($loop->last)
                            <li class="breadcrumb-item active">{{ $breadcrumb['name'] }}</li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="?path={{ $breadcrumb['path'] }}">{{ $breadcrumb['name'] }}</a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>

        <!-- Selection Info -->
        <div class="files-container">
            <div class="selection-info" id="selectionInfo">
                <span id="selectedCount">0</span> item(s) selected
                <button class="btn btn-sm btn-outline-primary ms-2" id="downloadSelectedBtn">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>

            <!-- Upload Zone -->
            <div class="upload-zone" id="uploadZone">
                <i class="fas fa-cloud-upload-alt fa-2x mb-3"></i>
                <h5>Drag and drop files here</h5>
                <p class="text-muted">or click the Upload button above</p>
            </div>

            <!-- Files Grid -->
            @if (count($items) > 0)
                <div class="files-grid">
                    @foreach ($items as $item)
                        <div class="file-item" data-path="{{ $item['path'] }}" data-type="{{ $item['type'] }}">
                            <input type="checkbox" class="form-check-input file-checkbox" value="{{ $item['path'] }}">

                            <div class="file-icon {{ $item['type'] }}">
                                @if ($item['type'] === 'folder')
                                    <i class="fas fa-folder"></i>
                                @elseif(in_array($item['extension'] ?? '', ['jpg', 'jpeg', 'png', 'gif', 'svg']))
                                    <i class="fas fa-image image"></i>
                                @elseif(in_array($item['extension'] ?? '', ['pdf', 'doc', 'docx', 'txt']))
                                    <i class="fas fa-file-alt document"></i>
                                @elseif(in_array($item['extension'] ?? '', ['zip', 'rar', '7z']))
                                    <i class="fas fa-file-archive archive"></i>
                                @else
                                    <i class="fas fa-file"></i>
                                @endif
                            </div>

                            <div class="file-name">{{ $item['name'] }}</div>
                            <div class="file-info">
                                @if ($item['type'] === 'folder')
                                    {{ $item['items_count'] }} items
                                @else
                                    {{ formatFileSize($item['size']) }}
                                @endif
                                <br>
                                <small class="text-muted">{{ date('M j, Y', $item['modified']) }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h5>No files or folders</h5>
                    <p class="text-muted">Upload files or create folders to get started</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Progress Overlay -->
    <div class="progress-overlay" id="progressOverlay">
        <div class="progress-card">
            <h6 class="mb-3">Processing...</h6>
            <div class="progress">
                <div class="progress-bar" id="progressBar" style="width: 0%"></div>
            </div>
            <div class="mt-2">
                <small id="progressText">Preparing...</small>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Files</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="path" value="{{ $currentPath }}">
                        <div class="mb-3">
                            <label for="fileInput" class="form-label">Select Files</label>
                            <input type="file" class="form-control" id="fileInput" name="files[]" multiple>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="uploadSubmitBtn">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Folder Modal -->
    <div class="modal fade" id="createFolderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createFolderForm">
                        @csrf
                        <input type="hidden" name="path" value="{{ $currentPath }}">
                        <div class="mb-3">
                            <label for="folderName" class="form-label">Folder Name</label>
                            <input type="text" class="form-control" id="folderName" name="name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="createFolderSubmitBtn">Create</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Archive Modal -->
    <div class="modal fade" id="archiveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Archive</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="archiveForm">
                        @csrf
                        <div class="mb-3">
                            <label for="archiveName" class="form-label">Archive Name</label>
                            <input type="text" class="form-control" id="archiveName" name="archive_name"
                                value="archive_{{ date('Y-m-d_H-i-s') }}">
                        </div>
                        <p class="text-muted">
                            <span id="archiveItemCount">0</span> item(s) will be archived
                        </p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="archiveSubmitBtn">Create Archive</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Restore Backup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="restoreForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="backupFile" class="form-label">Select Backup File (.zip)</label>
                            <input type="file" class="form-control" id="backupFile" name="backup_file"
                                accept=".zip" required>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This will overwrite existing files with the same names.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="restoreSubmitBtn">Restore</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/file-manager.js') }}"></script>

@endsection

@php
    function formatFileSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
@endphp
