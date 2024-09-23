@extends('template.master')

@section('content')
    {{-- Form --}}

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Work Order Instruction
                    </h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{-- filter tanggal --}}
            <div class="row mt-2">
                <div class="col-md-1 d-flex align-items-center">
                    Date
                </div>

                <div class="col-md-4">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <input type="date" class="form-control" id="input-work-order-instruction-date-start"
                                onchange="reloadTable()">
                        </div>
                        <div class="col-2 text-center">
                            to
                        </div>
                        <div class="col-5">
                            <input type="date" class="form-control" id="input-work-order-instruction-date-end"
                                onchange="reloadTable()">
                        </div>
                    </div>
                </div>

                <div class="col-md-1"></div>

            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            {{-- Table --}}
            <table class="table table-striped" id="table-work-order-instruction">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Work Order Number</th>
                        <th scope="col">Sales Order Number</th>
                        <th scope="col">Date</th>
                        <th scope="col">Date Complete</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Total (IDR)</th>
                        <th scope="col">Address</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>



    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-work-order-instruction").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/work-order-instruction/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.dateStart = document.getElementById('input-work-order-instruction-date-start')
                            .value;
                        d.dateEnd = document.getElementById('input-work-order-instruction-date-end')
                            .value;
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
                            showModalPrint("/work-order-instruction/print/" + selectedRows[0][8]);
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

                            // show modal print technician report
                            $('#modal-print-technician-report').modal('show');
                            // redirect to print technician report
                            // window.location = "/work-order-instruction/print-technician-report/" +
                            //     selectedRows[0][8];
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
                                url: "/work-order-instruction/production-code",
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
                            $('#modal-upload-complete-work-order-instruction').modal('show');
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
                            url: "/work-order-instruction/delete",
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
                    url: "/work-order-instruction/detail",
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

        function reloadTable() {
            var dateStart = document.getElementById('input-work-order-instruction-date-start').value;
            var dateEnd = document.getElementById('input-work-order-instruction-date-end').value;

            // Reload the table.
            table.ajax.reload(null, false);
        }

        $('#form-upload-image').on('submit', function(e) {
            e.preventDefault();

            // Get the form data.
            let formData = new FormData(this);

            // Submit the form data.
            $.ajax({
                url: "/work-order-instruction/upload-image",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    // Show success message.
                    if (response.success)
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
                    $('#modal-upload-complete-work-order-instruction').modal('hide');

                    // refresh the table
                    table.ajax.reload();

                    loadWorkOrderList(true);
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

        // print technician report
        $('#btn-print-technician-report').on('click', function() {
            let workOrderId = $('#work_order_id').val();
            let selectionPrintTechnicianReport = $('#selection-print-technician-report').val();

            // redirect to print technician report
            window.location = "/work-order-instruction/print-technician-report/" + workOrderId + "/" +
                selectionPrintTechnicianReport;
        });
    </script>
@endsection
