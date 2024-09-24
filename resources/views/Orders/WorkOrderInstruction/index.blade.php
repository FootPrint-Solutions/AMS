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
                        <th scope="col">Work Order Instruction Number</th>
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

                            deleteData(selectedRows[0][9]);
                        },
                        className: "btn btn-outline-danger btn-sm",
                    },
                    // add copy button to copy work order
                    {
                        text: "<i class='fas fa-copy'></i> Copy Work Order",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for copying work order.",
                                    icon: "error",
                                });
                                return;
                            }

                            // check if work order status is completed cannot be copied
                            var work_order_id = selectedRows[0][3];
                            var url = window.location.origin + "/wo/" + work_order_id

                            // swal copy work order
                            Swal.fire({
                                title: "Are you sure?",
                                text: "You want to copy this work order?",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Yes, copy it!",
                                cancelButtonText: "No, cancel!",
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // copy 
                                    var input = document.createElement('input');
                                    input.setAttribute('value', url);
                                    document.body.appendChild(input);
                                    input.select();
                                    document.execCommand('copy');
                                    document.body.removeChild(input);

                                    // Show success message.
                                    Swal.fire({
                                        title: "Success",
                                        text: "Work order copied to clipboard.",
                                        icon: "success",
                                    });

                                }
                            });
                        },
                        className: "btn btn-outline-primary btn-sm",
                    },
                ],
                language: getDatatablesLanguangeConfigurations("Work Order Instruction"),
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
                                if (response.status == 'success') {
                                    Swal.fire({
                                        title: "Success",
                                        text: response.message,
                                        icon: "success",
                                    });

                                    // Refresh the table.
                                    table.ajax.reload();
                                } else {
                                    Swal.fire({
                                        title: "Error",
                                        text: response.message,
                                        icon: "error",
                                    });
                                }
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
