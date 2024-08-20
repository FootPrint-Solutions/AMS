@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">

        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Inventory</h3>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-striped" id="table-battery">
                    <thead>
                        <tr>
                            <th scope="col" class="table-col-no">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Stock</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($data['inventories'] as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item[1] }}</td>
                                <td>{{ formatPrice($item[2]) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-battery").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                order: [],
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }, {
                    targets: [-1],
                    className: 'dt-body-right'
                }],
                dom: "lBrti",
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Battery"),
            });
        });
    </script>
@endsection
