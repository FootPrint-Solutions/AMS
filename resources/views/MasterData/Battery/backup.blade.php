<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Battery Backup Data</h3>
                    <a href="{{ route('battery.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Back to Battery List</a>
                </div>
                <div class="card-body">
                    @if ($backupData->isEmpty())
                        <div class="alert alert-info mb-0">No backup data available.</div>
                    @else
                        <div class="accordion" id="accordion-backup-group">
                            @foreach ($backupData as $backupNumber => $group)
                                @php
                                    $first = $group->first();
                                    $collapseId = 'collapse-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $backupNumber);
                                    $headingId = 'heading-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $backupNumber);
                                @endphp

                                <div class="accordion-item mb-2 border rounded">
                                    <h2 class="accordion-header" id="{{ $headingId }}">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                                            aria-expanded="false" aria-controls="{{ $collapseId }}">
                                            <div class="w-100 d-flex flex-column flex-md-row justify-content-between">
                                                <div>
                                                    <strong>Backup Number:</strong> {{ $backupNumber }}
                                                </div>
                                                <div>
                                                    <span class="badge bg-primary">{{ $group->count() }} rows</span>
                                                    <span class="ms-2 text-muted">
                                                        {{ \Carbon\Carbon::parse($first->backup_date)->format('d M Y, H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>

                                    <div id="{{ $collapseId }}" class="accordion-collapse collapse"
                                        aria-labelledby="{{ $headingId }}" data-bs-parent="#accordion-backup-group">
                                        <div class="accordion-body">
                                            <div class="d-flex justify-content-end gap-2 mb-3">
                                                <button type="button"
                                                    class="btn btn-outline-success btn-sm btn-restore-backup"
                                                    data-backup-number="{{ $backupNumber }}">
                                                    <i class="fa-solid fa-rotate-left"></i> Restore Backup
                                                </button>
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm btn-delete-backup"
                                                    data-backup-number="{{ $backupNumber }}">
                                                    <i class="fa-solid fa-trash"></i> Delete Backup
                                                </button>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-sm mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Battery ID</th>
                                                            <th>Code</th>
                                                            <th>Name</th>
                                                            <th>Alternate Name</th>
                                                            <th>Type</th>
                                                            <th>Brand</th>
                                                            <th>Subbrand Category</th>
                                                            <th>Usage Type</th>
                                                            <th>Technology</th>
                                                            <th>Size Category</th>
                                                            <th>Image</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($group as $backup)
                                                            <tr>
                                                                <td>{{ $backup->id }}</td>
                                                                <td>{{ $backup->battery_id }}</td>
                                                                <td>{{ $backup->code }}</td>
                                                                <td>{{ $backup->name }}</td>
                                                                <td>{{ $backup->name_alternate }}</td>
                                                                <td>{{ $backup->type }}</td>
                                                                <td>{{ $backup->brand->name ?? 'N/A' }}</td>
                                                                <td>{{ $backup->subbrandCategory->name ?? 'N/A' }}</td>
                                                                <td>{{ $backup->usageType->name ?? 'N/A' }}</td>
                                                                <td>{{ $backup->technology->name ?? 'N/A' }}</td>
                                                                <td>{{ $backup->sizeCategory->name ?? 'N/A' }}</td>
                                                                <td>{{ $backup->image ?? 'N/A' }}</td>
                                                                <td>{{ $backup->status ? 'Active' : 'Inactive' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
