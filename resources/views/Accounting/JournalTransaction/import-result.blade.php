@extends('template.master')

@section('content')
    @php
        $failedGroups = collect($data['unimportedRows'] ?? []);
        $summaryCards = [
            ['label' => 'Total Detail Rows', 'value' => $data['totalRows'] ?? 0, 'class' => 'text-primary'],
            ['label' => 'Total Transactions', 'value' => $data['totalTransactions'] ?? 0, 'class' => 'text-secondary'],
            ['label' => 'Imported Transactions', 'value' => $data['totalInsertedRows'] ?? 0, 'class' => 'text-success'],
            ['label' => 'Failed Groups', 'value' => $failedGroups->count(), 'class' => 'text-danger'],
        ];
    @endphp

    <div class="card">
        <div class="card-body">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title mb-0">Journal Transaction Import Result</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="goToPage('/journal-transaction')">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </button>
                    </div>
                </div>
            </div>

            <div class="row align-items-center mb-3">
                <div class="col-12 col-md-1 d-flex justify-content-center align-items-center mb-3 mb-md-0">
                    @if (($data['status'] ?? false) && $failedGroups->count() === 0)
                        <span
                            class="border border-success rounded-circle d-inline-flex justify-content-center align-items-center"
                            style="width: 64px; height: 64px;">
                            <i class="fa-solid fa-check text-success"></i>
                        </span>
                    @elseif ($data['status'] ?? false)
                        <span
                            class="border border-warning rounded-circle d-inline-flex justify-content-center align-items-center"
                            style="width: 64px; height: 64px;">
                            <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                        </span>
                    @else
                        <span
                            class="border border-danger rounded-circle d-inline-flex justify-content-center align-items-center"
                            style="width: 64px; height: 64px;">
                            <i class="fa-solid fa-x text-danger"></i>
                        </span>
                    @endif
                </div>

                <div class="col">
                    <div class="h5 fw-bold mb-1">
                        @if (($data['status'] ?? false) && $failedGroups->count() === 0)
                            Import completed successfully
                        @elseif ($data['status'] ?? false)
                            Import completed with warnings
                        @else
                            Import failed
                        @endif
                    </div>

                    @if ($data['status'] ?? false)
                        <div class="row g-3 mt-1">
                            @foreach ($summaryCards as $card)
                                <div class="col-12 col-sm-6 col-xl-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="text-muted small text-uppercase fw-semibold">
                                                {{ $card['label'] }}
                                            </div>
                                            <div class="h4 mb-0 {{ $card['class'] }}">
                                                {{ number_format($card['value']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($failedGroups->count() > 0)
                            <div class="alert alert-warning border mt-4 mb-0">
                                Some rows could not be imported. Review the failed groups below and correct the file before
                                trying again.
                            </div>
                        @else
                            <div class="alert alert-success border mt-4 mb-0">
                                All journal transactions from the file were imported successfully.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-danger border mb-0">
                            {{ $data['error'] ?? 'The import could not be completed.' }}
                        </div>
                    @endif
                </div>
            </div>

            @if ($failedGroups->count() > 0)
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0">Failed Groups</h5>
                    <span class="badge bg-light text-dark border">{{ $failedGroups->count() }} group(s)</span>
                </div>

                @foreach ($failedGroups as $failedGroup)
                    <div class="card mb-3 shadow-sm border-danger">
                        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <strong>
                                Voucher
                                {{ data_get($failedGroup, 'rows.0.voucher_number', '-') ?: data_get($failedGroup, 'voucher_number', '-') }}
                            </strong>
                            <span class="badge bg-danger">
                                {{ $failedGroup['reason'] ?? 'Import validation failed.' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-4">
                                    <div class="text-muted small text-uppercase fw-semibold">Row Numbers</div>
                                    <div class="fw-semibold">
                                        {{ implode(', ', $failedGroup['row_numbers'] ?? []) ?: $failedGroup['row_number'] ?? '-' }}
                                    </div>
                                </div>
                                <div class="col-12 col-md-8">
                                    <div class="text-muted small text-uppercase fw-semibold">Reason</div>
                                    <div class="fw-semibold">{{ $failedGroup['reason'] ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Voucher Number</th>
                                            <th>Date</th>
                                            <th>Note</th>
                                            <th>Account Number</th>
                                            <th>Description</th>
                                            <th class="text-end">Debit</th>
                                            <th class="text-end">Credit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($failedGroup['rows'] ?? [] as $failedRow)
                                            <tr>
                                                <td>{{ $failedRow['voucher_number'] ?? '-' }}</td>
                                                <td>{{ $failedRow['date'] ?? '-' }}</td>
                                                <td>{{ $failedRow['note'] ?? '-' }}</td>
                                                <td>{{ $failedRow['account_number'] ?? '-' }}</td>
                                                <td>{{ $failedRow['description'] ?? '-' }}</td>
                                                <td class="text-end">
                                                    {{ number_format((float) ($failedRow['debit'] ?? 0), 2) }}</td>
                                                <td class="text-end">
                                                    {{ number_format((float) ($failedRow['credit'] ?? 0), 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection
