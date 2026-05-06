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
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="backup-table">
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
                                    <th>Backup Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($backupData as $backup)
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
                                        <td>{{ $backup->status }}</td>
                                        <td>{{ $backup->backup_date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
