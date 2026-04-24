@extends('template.master')

@section('content')
    @php
        $summaryCards = [
            ['label' => 'Total Rows', 'value' => $totalRows ?? 0, 'class' => 'text-primary'],
            [
                'label' => 'Total Imported Rows',
                'value' => ($totalRows ?? 0) - count($unimportedRows ?? []),
                'class' => 'text-success',
            ],
            ['label' => 'Total Unimported Rows', 'value' => count($unimportedRows ?? []), 'class' => 'text-danger'],
            ['label' => 'Total Updated Rows', 'value' => $totalUpdatedRows ?? 0, 'class' => 'text-warning'],
            ['label' => 'Total Inserted Rows', 'value' => $totalInsertedRows ?? 0, 'class' => 'text-info'],
        ];

        $failedRows = collect($unimportedRows ?? [])->map(function ($row, $index) {
            if (is_array($row) && array_key_exists('data', $row)) {
                return $row;
            }

            return [
                'row_number' => $index + 1,
                'reason' => 'Baris gagal diimpor.',
                'data' => $row,
            ];
        });
    @endphp

    {{-- Form --}}
    <div class="d-none d-lg-block">
        <div class="card">
            <div class="card-body">
                {{-- Title --}}
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Import Status</h3>
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div class="row mb-3">
                    <div class="col-1 d-flex justify-content-center align-items-center">
                        @if ($status)
                            <span
                                class="border border-success rounded-circle d-inline-flex justify-content-center align-items-center"
                                style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-check text-success"></i>
                            </span>
                        @else
                            <span
                                class="border border-danger rounded-circle d-inline-flex justify-content-center align-items-center"
                                style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-x text-danger"></i>
                            </span>
                        @endif
                    </div>

                    <div class="col">
                        <div class="h5 fw-bold">
                            @if ($status)
                                Success
                            @else
                                Failed
                            @endif
                        </div>

                        @if ($status)
                            <div class="row g-3 mt-1">
                                @foreach ($summaryCards as $card)
                                    <div class="col-12 col-sm-6 col-xl-4">
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
                        @else
                            {{ $error }}
                        @endif
                    </div>
                </div>
                <br>

                {{-- List --}}
                @if ($status)
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">List of Unimported Rows</h5>
                        <span class="badge bg-light text-dark border">{{ $failedRows->count() }} row(s)</span>
                    </div>

                    @if ($failedRows->count() > 0)
                        @foreach ($failedRows as $failedRow)
                            @php
                                $rowData = $failedRow['data'] ?? [];
                                $dimensionValue = trim(
                                    implode(
                                        ' x ',
                                        array_filter(
                                            [
                                                data_get($rowData, 'dimension_length', data_get($rowData, 8, null)),
                                                data_get($rowData, 'dimension_width', data_get($rowData, 9, null)),
                                                data_get($rowData, 'dimension_height', data_get($rowData, 10, null)),
                                            ],
                                            function ($value) {
                                                return $value !== null && $value !== '' && $value !== '-';
                                            },
                                        ),
                                    ),
                                );

                                if ($dimensionValue === '') {
                                    $dimensionValue = '-';
                                }

                                $fields = [
                                    ['label' => 'ID', 'value' => data_get($rowData, 'id', data_get($rowData, 0, '-'))],
                                    [
                                        'label' => 'Name',
                                        'value' => data_get($rowData, 'name', data_get($rowData, 1, '-')),
                                    ],
                                    [
                                        'label' => 'Alternate Name',
                                        'value' => data_get($rowData, 'alternate_name', data_get($rowData, 2, '-')),
                                    ],
                                    [
                                        'label' => 'Brand',
                                        'value' => data_get($rowData, 'brand', data_get($rowData, 3, '-')),
                                    ],
                                    [
                                        'label' => 'Subbrand Category',
                                        'value' => data_get($rowData, 'subbrand_category', data_get($rowData, 4, '-')),
                                    ],
                                    [
                                        'label' => 'Usage Type',
                                        'value' => data_get($rowData, 'usage_type', data_get($rowData, 5, '-')),
                                    ],
                                    [
                                        'label' => 'Size Category',
                                        'value' => data_get($rowData, 'size_category', data_get($rowData, 6, '-')),
                                    ],
                                    [
                                        'label' => 'Technology',
                                        'value' => data_get($rowData, 'technology', data_get($rowData, 7, '-')),
                                    ],
                                    ['label' => 'Dimension', 'value' => $dimensionValue],
                                    [
                                        'label' => 'Standard CCA',
                                        'value' => data_get($rowData, 'standard_cca', data_get($rowData, 11, '-')),
                                    ],
                                    [
                                        'label' => 'Capacity',
                                        'value' => data_get($rowData, 'capacity', data_get($rowData, 12, '-')),
                                    ],
                                    [
                                        'label' => 'Warranty',
                                        'value' => data_get($rowData, 'warranty', data_get($rowData, 13, '-')),
                                    ],
                                    [
                                        'label' => 'Retail Price',
                                        'value' => data_get($rowData, 'retail_price', data_get($rowData, 14, '-')),
                                    ],
                                    [
                                        'label' => 'Buy Price',
                                        'value' => data_get($rowData, 'buy_price', data_get($rowData, 15, '-')),
                                    ],
                                ];
                            @endphp

                            <div class="card mb-3 shadow-sm border-danger">
                                <div
                                    class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <strong>Row {{ $failedRow['row_number'] ?? $loop->iteration }}</strong>
                                    <span
                                        class="badge bg-danger">{{ $failedRow['reason'] ?? 'Baris gagal diimpor.' }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        @foreach ($fields as $field)
                                            <div class="col-12 col-md-6 col-xl-4">
                                                <div class="text-muted small text-uppercase fw-semibold">
                                                    {{ $field['label'] }}
                                                </div>
                                                <div class="fw-semibold">
                                                    {{ $field['value'] === '' || $field['value'] === null ? '-' : $field['value'] }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-light border mb-0">
                            No failed row
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
