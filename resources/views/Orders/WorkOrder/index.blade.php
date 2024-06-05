@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Work Order</h3>
                    </div>

                    {{-- <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Sales Order</button>
                    </div> --}}
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-work-order">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Work Order Number</th>
                        <th scope="col">Sales Order Number</th>
                        <th scope="col">Date</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Total (IDR)</th>
                        <th scope="col">Address</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- DataTables Configurations --}}
    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-work-order").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/work-order/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false,
                }, {
                    targets: [6],
                    className: 'dt-body-right table-col-price'
                }, {
                    targets: [0, 7],
                    className: 'dt-body-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations([{
                        text: "<i class='fas fa-print'></i> Print Work Order",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for printing work order.",
                                    icon: "error",
                                });
                                return;
                            }

                            // Download invoice as pdf.
                            downloadPDF("/work-order/print/" + selectedRows[0][8]);
                        },
                        className: "btn btn-outline-danger btn-sm",
                    },
                    // add upload image button 
                    {
                        text: "<i class='fas fa-upload'></i> Upload Image",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for uploading image.",
                                    icon: "error",
                                });
                                return;
                            }

                            // show modal for upload image
                            $('#modal-upload-image').modal('show');
                            $('#work_order_id').val(selectedRows[0][8]);
                        },
                        className: "btn btn-outline-primary btn-sm",
                    }
                ]),
                language: getDatatablesLanguangeConfigurations("Sales Order"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(9);
        });
    </script>

    {{-- Modal --}}
    <div class="modal fade" id="modal-upload-image" tabindex="-1" aria-labelledby="modal-upload-image-label"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form-upload-image" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-upload-image-label">Upload Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body text-center">
                        <input type="hidden" name="work_order_id" id="work_order_id">
                        <input type="file" name="image" id="image" class="form-control" required allow="image/*">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Form Submit Handler --}}
    <script>
        $('#form-upload-image').on('submit', function(e) {
            e.preventDefault();

            // Get the form data.
            let formData = new FormData(this);

            // Submit the form data.
            $.ajax({
                url: "/work-order/upload-image",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Show success message.
                    Swal.fire({
                        title: "Success",
                        text: response.message,
                        icon: "success",
                    });

                    // Hide the modal.
                    $('#modal-upload-image').modal('hide');
                },
                error: function(xhr) {
                    // Show error message.
                    Swal.fire({
                        title: "Error",
                        text: xhr.responseJSON.message,
                        icon: "error",
                    });
                }
            });
        });
    </script>

    {{-- Click Event Handler --}}
    <script>
        $('#btn-add').on('click', function() {
            goToPage("/sales-order/create");
        });
    </script>
@endsection
