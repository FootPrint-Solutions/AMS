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
                            <h3 class="page-title">Work Order</h3>
                        </div>
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
    </div>

    {{-- modal print --}}
    <div class="modal fade" id="modal-print" tabindex="-1" aria-labelledby="modal-print-label" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-light" id="modal-print-label"><i class="fas fa-print"></i> Print Work Order
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- select option --}}
                    <div class="form-group mb-3">
                        <form action="/work-order/print" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="work_order_id" id="work_order_id">
                            <label for="print_type">Print Type</label>
                            <label for="print_option">Select Print Option:</label>
                            <select class="form-select" id="print_option" name="print_option">
                                <option value="regular_dan_instalasi">1. Regular dan Instalasi</option>
                                <option value="tokopedia_dan_instalasi">2. Tokopedia dan Instalasi</option>
                                <option value="tokopedia_tanpa_instalasi">3. Tokopedia tanpa Instalasi</option>
                            </select>

                            <div id="upload-column"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-print"><i class="fas fa-print"></i>
                        Print</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="modal-upload-complete-work-order" tabindex="-1" aria-labelledby="modal-upload-image-label"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <form id="form-upload-image" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-light" id="modal-upload-image-label"><i class="fas fa-upload"></i>
                            Technician Report File Attachment
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body text-center">
                        <div class="production_code_data"></div>
                        <input type="hidden" name="work_order_id" id="work_order_id_image">
                        <label for="image" class="mt-3">Upload Technician Report File</label>
                        <input type="file" name="image" id="image" class="form-control" allow="image/*">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload & Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div class="modal fade" id="modal-detail" tabindex="-1" aria-labelledby="modal-detail-label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-light" id="modal-detail-label"><i class="fas fa-info-circle"></i> Work
                        Order
                        Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" id="modal-detail-body">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @include('Mobile.Orders.WorkOrder.index')

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
                buttons: [
                    // add delete button
                    {
                        text: "<i class='fas fa-trash'></i> Delete Work Order",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for deleting work order.",
                                    icon: "error",
                                });
                                return;
                            }

                            // check if work order status is completed cannot be deleted
                            if (selectedRows[0][9] == "completed") {
                                Swal.fire({
                                    title: "Error",
                                    text: "Work Order status is completed, cannot be deleted.",
                                    icon: "error",
                                });
                                return;
                            }

                            deleteData(selectedRows[0][8]);
                        },
                        className: "btn btn-outline-danger btn-sm",
                    }
                    // add button print work order    
                    , {
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
                            $("#work_order_id").val(selectedRows[0][8]);
                            showModalPrint("/work-order/print/" + selectedRows[0][8]);
                        },
                        className: "btn btn-outline-secondary btn-sm",
                    },
                    // add button print technician report
                    {
                        text: "<i class='fas fa-print'></i> Print Technician Report",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for printing technician report.",
                                    icon: "error",
                                });
                                return;
                            }

                            $("#work_order_id").val(selectedRows[0][8]);
                            // redirect to print technician report
                            window.location = "/work-order/print-technician-report/" +
                                selectedRows[0][8];
                        },
                        className: "btn btn-outline-secondary btn-sm",
                    },
                    // add upload image button 
                    {
                        text: "<i class='fas fa-upload'></i> Complete Work Order",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for Attach File.",
                                    icon: "error",
                                });
                                return;
                            }

                            // get data production code ajax request
                            $.ajax({
                                url: "/work-order/production-code",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    work_order_id: selectedRows[0][8]
                                },
                                success: function(responseData) {
                                    if (responseData.status == true) {
                                        let batteries = responseData.production_code
                                            .sales_order.batteries;
                                        let batteriesHtml = ``;
                                        batteriesHtml += `
                <table class="table">
                    <thead>
                        <tr>
                            <th>Production Code</th>
                            <th>Battery Name</th>
                            <th width="20%">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                                        batteries.forEach(function(battery) {
                                            // jika battery production code null, maka tampilkan input text ganti dengan kosong
                                            if (battery
                                                .battery_production_code == null
                                            ) {
                                                battery
                                                    .battery_production_code =
                                                    '';
                                            }
                                            batteriesHtml += `
                    <tr>
                        <input type="hidden" name="battery_id[]" value="${battery.id}">
                        <td><input type="text" name="production_code[]" value="${battery.battery_production_code}" class="form-control"></td>
                        <td><input type="text" name="battery_name[]" value="${battery.battery_name}" class="form-control" readonly></td>
                        <td><input type="number" name="battery_quantity[]" value="${battery.quantity}" class="form-control" readonly></td>
                    </tr>
                `;
                                        });

                                        batteriesHtml += `
                    </tbody>
                </table>
            `;


                                        $('.production_code_data').html(`
                                            <div class="text-center">
                                            <p>Work Order: ${responseData.production_code.work_order_number}</p>
                                            </div>
                                            <div class="batteries-data">${batteriesHtml}</div>`);
                                    } else {
                                        $('.production_code_data').html(`
                                            <div class="text-center">
                                                <p class="text-danger">Failed to load production code.</p>
                                            </div>
                                        `);
                                    }
                                },
                                error: function(xhr) {
                                    $('.production_code_data').html(`
                                        <div class="text-center">
                                            <p class="text-danger">Failed to load production code.</p>
                                        </div>
                                    `);
                                }
                            });

                            // show modal for upload image
                            $('#modal-upload-complete-work-order').modal('show');
                            $('#work_order_id_image').val(selectedRows[0][8]);
                        },
                        className: "btn btn-outline-primary btn-sm",
                    },
                ],
                language: getDatatablesLanguangeConfigurations("Work Order"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[9] == "posted")
                        $('td', row).addClass("text-success");
                    else if (data[9] == "completed")
                        $('td', row).addClass("text-info");

                    // double click to show modal detail
                    $(row).on('dblclick', function() {
                        showModalDetail(data[8]);
                    });
                }
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(9);

            function showModalPrint(url) {
                // Show the print modal.
                $('#modal-print').modal('show');
                showUploadImage();
            }

            function deleteData(id) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You will not be able to recover this work order!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel!",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/work-order/delete",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                work_order_id: id
                            },
                            success: function(response) {
                                // Show success message.
                                Swal.fire({
                                    title: "Success",
                                    text: response.message,
                                    icon: "success",
                                });

                                // Refresh the table.
                                table.ajax.reload();
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
                    }
                });
            }

            function showModalDetail(id) {
                $('#modal-detail').modal('show');
                $('#modal-detail-body').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                // ajax request to get work order detail
                $.ajax({
                    url: "/work-order/detail",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        work_order_id: id
                    },
                    success: function(response) {
                        $('#modal-detail-body').html(response);
                    },
                    error: function(xhr) {
                        $('#modal-detail-body').html(`
                                    <div class="text-center">
                                        <p class="text-danger">Failed to load work order detail.</p>
                                    </div>
                                `);
                    }
                });

            }
        });

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
                    if (response.status)
                        Swal.fire({
                            title: "Success",
                            text: response.message,
                            icon: "success",
                        });
                    else
                        Swal.fire({
                            title: "Error",
                            text: response.message,
                            icon: "error",
                        });

                    // Hide the modal.
                    $('#modal-upload-complete-work-order').modal('hide');

                    // refresh the table
                    table.ajax.reload();
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

        // jika print_option 2 / 3, maka tampilkan kolom upload image
        $('#print_option').on('change', function() {
            showUploadImage();
        });

        function showUploadImage() {
            let printOption = $('#print_option').val();
            let uploadColumn = $('#upload-column');

            if (printOption == 'tokopedia_dan_instalasi' || printOption == 'tokopedia_tanpa_instalasi') {
                uploadColumn.html(`
                    <label for="image" class="mt-3">Upload Label Image ( From Market Place )</label>
                    <input type="file" name="image" id="image" class="form-control" allow="image/*">
                `);
            } else {
                uploadColumn.html('');
            }
        }
    </script>
@endsection
