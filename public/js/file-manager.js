/**
 * File Manager JavaScript - Google Drive Style Interface
 * Handles file operations like upload, delete, select, backup, restore, and archive
 */

class FileManager {
    constructor() {
        this.selectedItems = new Set();
        this.currentPath = new URLSearchParams(window.location.search).get('path') || '';
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateSelectionState();
    }

    bindEvents() {
        // File item selection
        document.querySelectorAll('.file-item').forEach(item => {
            item.addEventListener('click', (e) => this.handleFileItemClick(e));
        });

        // Checkbox events
        document.querySelectorAll('.file-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => this.handleCheckboxChange(e));
        });

        // Select all checkbox
        document.getElementById('selectAllCheckbox')?.addEventListener('change', (e) => {
            this.handleSelectAll(e.target.checked);
        });

        // Toolbar buttons
        document.getElementById('uploadBtn')?.addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
            modal.show();
        });

        document.getElementById('createFolderBtn')?.addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('createFolderModal'));
            modal.show();
        });

        document.getElementById('deleteBtn')?.addEventListener('click', () => {
            this.handleDelete();
        });

        document.getElementById('backupBtn')?.addEventListener('click', () => {
            this.handleBackup();
        });

        document.getElementById('archiveBtn')?.addEventListener('click', () => {
            this.handleArchive();
        });

        document.getElementById('downloadSelectedBtn')?.addEventListener('click', () => {
            this.handleDownloadSelected();
        });

        // Form submissions
        document.getElementById('uploadSubmitBtn')?.addEventListener('click', () => {
            this.handleUpload();
        });

        document.getElementById('createFolderSubmitBtn')?.addEventListener('click', () => {
            this.handleCreateFolder();
        });

        document.getElementById('archiveSubmitBtn')?.addEventListener('click', () => {
            this.handleArchiveSubmit();
        });

        document.getElementById('restoreSubmitBtn')?.addEventListener('click', () => {
            this.handleRestore();
        });

        // Drag and drop
        this.initDragAndDrop();
    }

    handleFileItemClick(e) {
        const item = e.currentTarget;
        const checkbox = item.querySelector('.file-checkbox');

        if (e.target === checkbox) return; // Don't handle if checkbox was clicked

        const path = item.dataset.path;
        const type = item.dataset.type;

        if (type === 'folder') {
            // Navigate to folder
            window.location.href = `?path=${encodeURIComponent(path)}`;
        } else if (e.ctrlKey || e.metaKey) {
            // Multi-select with Ctrl/Cmd
            this.toggleSelection(path, checkbox);
        } else {
            // Single select or double click to download
            if (this.selectedItems.has(path)) {
                // Double click - download file
                this.downloadFile(path);
            } else {
                // Single click - select only this item
                this.clearSelection();
                this.toggleSelection(path, checkbox);
            }
        }
    }

    handleCheckboxChange(e) {
        const checkbox = e.target;
        const path = checkbox.value;
        this.toggleSelection(path, checkbox);
    }

    toggleSelection(path, checkbox) {
        if (this.selectedItems.has(path)) {
            this.selectedItems.delete(path);
            checkbox.checked = false;
            checkbox.closest('.file-item').classList.remove('selected');
        } else {
            this.selectedItems.add(path);
            checkbox.checked = true;
            checkbox.closest('.file-item').classList.add('selected');
        }
        this.updateSelectionState();
    }

    clearSelection() {
        this.selectedItems.clear();
        document.querySelectorAll('.file-checkbox').forEach(checkbox => {
            checkbox.checked = false;
            checkbox.closest('.file-item').classList.remove('selected');
        });
        document.getElementById('selectAllCheckbox').checked = false;
        this.updateSelectionState();
    }

    handleSelectAll(checked) {
        if (checked) {
            document.querySelectorAll('.file-checkbox').forEach(checkbox => {
                const path = checkbox.value;
                this.selectedItems.add(path);
                checkbox.checked = true;
                checkbox.closest('.file-item').classList.add('selected');
            });
        } else {
            this.clearSelection();
        }
        this.updateSelectionState();
    }

    updateSelectionState() {
        const selectedCount = this.selectedItems.size;
        const totalItems = document.querySelectorAll('.file-checkbox').length;

        // Update selection info
        document.getElementById('selectedCount').textContent = selectedCount;
        const selectionInfo = document.getElementById('selectionInfo');

        if (selectedCount > 0) {
            selectionInfo.style.display = 'block';
        } else {
            selectionInfo.style.display = 'none';
        }

        // Update delete button
        const deleteBtn = document.getElementById('deleteBtn');
        deleteBtn.disabled = selectedCount === 0;

        // Update select all checkbox
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        if (selectedCount === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (selectedCount === totalItems) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    async handleUpload() {
        const form = document.getElementById('uploadForm');
        const formData = new FormData(form);

        if (!formData.get('files[]') || formData.get('files[]').name === '') {
            this.showAlert('Please select files to upload', 'warning');
            return;
        }

        this.showProgress('Uploading files...');

        try {
            const response = await fetch('/file-manager/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert(result.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Upload failed: ' + error.message, 'error');
        } finally {
            this.hideProgress();
            bootstrap.Modal.getInstance(document.getElementById('uploadModal'))?.hide();
        }
    }

    async handleCreateFolder() {
        const form = document.getElementById('createFolderForm');
        const formData = new FormData(form);

        try {
            const response = await fetch('/file-manager/create-folder', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert(result.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Failed to create folder: ' + error.message, 'error');
        } finally {
            bootstrap.Modal.getInstance(document.getElementById('createFolderModal'))?.hide();
        }
    }

    async handleDelete() {
        if (this.selectedItems.size === 0) return;

        if (!confirm(`Are you sure you want to delete ${this.selectedItems.size} item(s)? This action cannot be undone.`)) {
            return;
        }

        this.showProgress('Deleting items...');

        try {
            const response = await fetch('/file-manager/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    items: Array.from(this.selectedItems)
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert(result.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Delete failed: ' + error.message, 'error');
        } finally {
            this.hideProgress();
        }
    }

    async handleBackup() {
        const items = Array.from(this.selectedItems);
        this.showProgress('Creating backup...');

        try {
            const response = await fetch('/file-manager/backup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ items })
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert(result.message, 'success');
                // Auto download backup
                this.downloadFile(result.backup_path);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Backup failed: ' + error.message, 'error');
        } finally {
            this.hideProgress();
        }
    }

    handleArchive() {
        if (this.selectedItems.size === 0) {
            this.showAlert('Please select items to archive', 'warning');
            return;
        }

        document.getElementById('archiveItemCount').textContent = this.selectedItems.size;
        const modal = new bootstrap.Modal(document.getElementById('archiveModal'));
        modal.show();
    }

    async handleArchiveSubmit() {
        const form = document.getElementById('archiveForm');
        const formData = new FormData(form);

        const data = {
            items: Array.from(this.selectedItems),
            archive_name: formData.get('archive_name')
        };

        this.showProgress('Creating archive...');

        try {
            const response = await fetch('/file-manager/archive', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert(result.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Archive failed: ' + error.message, 'error');
        } finally {
            this.hideProgress();
            bootstrap.Modal.getInstance(document.getElementById('archiveModal'))?.hide();
        }
    }

    async handleRestore() {
        const form = document.getElementById('restoreForm');
        const formData = new FormData(form);

        if (!formData.get('backup_file') || formData.get('backup_file').name === '') {
            this.showAlert('Please select a backup file', 'warning');
            return;
        }

        this.showProgress('Restoring backup...');

        try {
            const response = await fetch('/file-manager/restore', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert(result.message, 'success');
                setTimeout(() => window.location.reload(), 2000);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Restore failed: ' + error.message, 'error');
        } finally {
            this.hideProgress();
            bootstrap.Modal.getInstance(document.getElementById('restoreModal'))?.hide();
        }
    }

    handleDownloadSelected() {
        if (this.selectedItems.size === 0) return;

        this.selectedItems.forEach(item => {
            this.downloadFile(item);
        });
    }

    downloadFile(path) {
        const url = `/file-manager/download?item=${encodeURIComponent(path)}`;
        window.open(url, '_blank');
    }

    initDragAndDrop() {
        const uploadZone = document.getElementById('uploadZone');
        if (!uploadZone) return;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadZone.addEventListener(eventName, this.preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadZone.addEventListener(eventName, () => {
                uploadZone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadZone.addEventListener(eventName, () => {
                uploadZone.classList.remove('dragover');
            }, false);
        });

        uploadZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            this.handleDroppedFiles(files);
        }, false);
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    async handleDroppedFiles(files) {
        if (files.length === 0) return;

        const formData = new FormData();
        formData.append('path', this.currentPath);

        for (let file of files) {
            formData.append('files[]', file);
        }

        this.showProgress('Uploading files...');

        try {
            const response = await fetch('/file-manager/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert(result.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Upload failed: ' + error.message, 'error');
        } finally {
            this.hideProgress();
        }
    }

    showProgress(message = 'Processing...') {
        const overlay = document.getElementById('progressOverlay');
        const text = document.getElementById('progressText');
        const bar = document.getElementById('progressBar');

        text.textContent = message;
        bar.style.width = '0%';
        overlay.style.display = 'flex';

        // Simulate progress
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            bar.style.width = progress + '%';
        }, 200);

        // Store interval for cleanup
        overlay.dataset.interval = interval;
    }

    hideProgress() {
        const overlay = document.getElementById('progressOverlay');
        const bar = document.getElementById('progressBar');

        // Complete progress
        bar.style.width = '100%';

        // Clear interval
        if (overlay.dataset.interval) {
            clearInterval(overlay.dataset.interval);
        }

        setTimeout(() => {
            overlay.style.display = 'none';
        }, 500);
    }

    showAlert(message, type = 'info') {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
        alertDiv.style.position = 'fixed';
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';

        alertDiv.innerHTML = `
            <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}

// Initialize File Manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new FileManager();
});

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Ctrl+A - Select All
    if (e.ctrlKey && e.key === 'a' && !e.target.matches('input, textarea')) {
        e.preventDefault();
        document.getElementById('selectAllCheckbox').click();
    }

    // Delete key - Delete selected
    if (e.key === 'Delete' && !e.target.matches('input, textarea')) {
        e.preventDefault();
        document.getElementById('deleteBtn')?.click();
    }
});