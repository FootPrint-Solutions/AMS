@extends('template.master')

@section('content')
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
                            <table>
                                {{-- Total Rows --}}
                                <tr>
                                    <td style="width: 40%">Total Rows</td>
                                    <td style="width: 5%">:</td>
                                    <td style="width: 45%">{{ $totalRows }}</td>
                                </tr>

                                {{-- Total Imported Rows --}}
                                <tr>
                                    <td>Total Imported Rows</td>
                                    <td>:</td>
                                    <td>{{ $totalRows - count($unimportedRows) }}</td>
                                </tr>

                                {{-- Unimported Rows --}}
                                <tr>
                                    <td>Total Unimported Rows</td>
                                    <td>:</td>
                                    <td>{{ count($unimportedRows) }}</td>
                                </tr>

                                {{-- Total Changed Rows --}}
                                <tr>
                                    <td>Total Updated Rows</td>
                                    <td>:</td>
                                    <td>{{ $totalUpdatedRows ?? 0 }}</td>
                                </tr>

                                {{-- Total Added Rows --}}
                                <tr>
                                    <td>Total Inserted Rows</td>
                                    <td>:</td>
                                    <td>{{ $totalInsertedRows ?? 0 }}</td>
                                </tr>
                            </table>
                        @else
                            {{ $error }}
                        @endif
                    </div>
                </div>
                <br>

                {{-- List --}}
                @if ($status)
                    <h5>List of Unimported Rows</h5>
                    <ul class="list-group">
                        @if ($status && count($unimportedRows) > 0)
                            @foreach ($unimportedRows as $row)
                                <li class="list-group-item">{{ implode(',', $row) }}</li>
                            @endforeach
                        @else
                            <li class="list-group-item disabled">No failed row</li>
                        @endif
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
