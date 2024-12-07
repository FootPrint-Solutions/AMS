@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="d-none d-lg-block">
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

                    {{-- filter status complete --}}
                    <div class="col-md-1 d-flex align-items-center">
                        Status
                    </div>

                    <div class="col-md-5 d-flex align-items-center">
                        <div class="form-check form-switch">
                            <select class="form-select" id="input-work-order-instruction-status" onchange="reloadTable()">
                                <option value="">All Status</option>
                                <option value="complete">Complete</option>
                                <option value="uncomplete">Uncomplete</option>
                            </select>
                        </div>
                    </div>
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
                            <th scope="col">WO #</th>
                            <th scope="col">SO #</th>
                            <th scope="col">WO Instruction #</th>
                            <th scope="col">Ordered Date</th>
                            <th scope="col">Completed Date</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Address</th>
                            <th scope="col">Technician</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('Mobile.Orders.WorkOrderInstruction.index')

    {{-- modal detail --}}
    <div class="modal fade" id="modal-detail" tabindex="-1" aria-labelledby="modal-detail" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Work Order Instruction Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-detail-body">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
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
                        d.status = document.getElementById('input-work-order-instruction-status')
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
                        text: "<i class='fas fa-copy'></i> Copy WO Instruction",
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
                            var newurl = window.location.origin + "/wo-new/" + work_order_id

                            // add option to copy work order url old or new
                            Swal.fire({
                                title: "Copy Work Order",
                                text: "Do you want to copy this work order?",
                                icon: "warning",
                                html: `<div class="form-group">
                                    <label for="print_option">Select Copy Work Order</label>
                                    <select class="form-select" id="print_option">
                                        @foreach ($data['print_options'] as $key => $value)
                                            <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>`,
                                showCancelButton: true,
                                confirmButtonText: "Copy New WO Instruction Link",
                                // cancelButtonText: "Copy Old WO Instruction Link",
                                reverseButtons: true,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // copy new work order
                                    var input = document.createElement('input');
                                    var print_option = document.getElementById(
                                        'print_option').value;
                                    var decode = window.btoa(work_order_id + '/' +
                                        print_option);
                                    var newurl = window.location.origin + "/wo-new/" +
                                        decode;
                                    input.setAttribute('value', newurl);
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
                                } else {
                                    // copy old work order
                                    // var input = document.createElement('input');
                                    // var decode = window.btoa(work_order_id);
                                    // var url = window.location.origin + "/wo/" + decode;
                                    // input.setAttribute('value', url);
                                    // document.body.appendChild(input);
                                    // input.select();
                                    // document.execCommand('copy');
                                    // document.body.removeChild(input);

                                    // // Show success message.
                                    // Swal.fire({
                                    //     title: "Success",
                                    //     text: "Work order copied to clipboard.",
                                    //     icon: "success",
                                    // });
                                }
                            });
                        },
                        className: "btn btn-outline-primary btn-sm",
                    },
                    // add view details button
                    {
                        text: "<i class='fas fa-eye'></i> View Details",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for viewing work order details.",
                                    icon: "error",
                                });
                                return;
                            }

                            showModalDetail(selectedRows[0][9]);
                        },
                        className: "btn btn-outline-info btn-sm",
                    },
                    // add set to uncomplete button
                    {
                        text: "<i class='fas fa-undo'></i> Set to Uncomplete",
                        action: function(e, dt, node, config) {
                            // Get the selected row's id.
                            let selectedRows = table.rows({
                                selected: true
                            }).data().toArray();
                            if (selectedRows.length !== 1) {
                                Swal.fire({
                                    title: "Error",
                                    text: "Please select a single row for setting work order to uncomplete.",
                                    icon: "error",
                                });
                                return;
                            }

                            // check if work order status is completed cannot be set to uncomplete
                            var work_order_id = selectedRows[0][9];
                            var work_order_status = selectedRows[0][9];
                            if (work_order_status == "completed") {
                                Swal.fire({
                                    title: "Error",
                                    text: "Work order status is completed, cannot be set to uncomplete.",
                                    icon: "error",
                                });
                                return;
                            }

                            Swal.fire({
                                title: "Set to Uncomplete",
                                text: "Do you want to set this work order instruction to uncomplete?",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Yes, set to uncomplete!",
                                cancelButtonText: "No, cancel!",
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: "/work-order-instruction/set-uncomplete",
                                        type: "POST",
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            work_order_instruction_id: work_order_id
                                        },
                                        success: function(response) {
                                            // Show success message.
                                            if (response.status == 'success') {
                                                Swal.fire({
                                                    title: "Success",
                                                    text: response
                                                        .message,
                                                    icon: "success",
                                                });

                                                // Refresh the table.
                                                table.ajax.reload();
                                            } else {
                                                Swal.fire({
                                                    title: "Error",
                                                    text: response
                                                        .message,
                                                    icon: "error",
                                                });
                                            }
                                        },
                                        error: function(xhr) {
                                            // Show error message.
                                            Swal.fire({
                                                title: "Error",
                                                text: xhr.responseJSON
                                                    .message,
                                                icon: "error",
                                            });
                                        }
                                    });
                                }
                            });
                        },
                        className: "btn btn-outline-warning btn-sm",
                    },
                ],
                language: getDatatablesLanguangeConfigurations("Work Order Instruction"),
                select: true,
                rowCallback: function(row, data) {
                    if (data[5] != null && data[5] != "")
                        $('td', row).addClass("text-info");
                    else if (data[9] == "completed")
                        $('td', row).addClass("text-info");

                    // double click to show modal detail
                    $(row).on('dblclick', function() {
                        showModalDetail(selectedRows[0][9]);
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
        });

        function reloadTable() {
            var dateStart = document.getElementById('input-work-order-instruction-date-start').value;
            var dateEnd = document.getElementById('input-work-order-instruction-date-end').value;
            var status = document.getElementById('input-work-order-instruction-status').value;

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

        function showModalDetail(id) {
            $('#modal-detail').modal('show');
            $('#modal-detail-body').html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);

            // AJAX request to get work order detail
            $.ajax({
                url: "/work-order-instruction/detail",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    work_order_id: id
                },
                success: function(response) {
                    const baseUrl = "{{ asset('storage/image/work-order/instruction/') }}";
                    const {
                        work_order,
                        work_order_instruction_number,
                        date,
                        date_complete,
                        photos,
                        answers,
                        updated_by
                    } = response.data;
                    const url = `${window.location.origin}/wo/${work_order_instruction_number}`;

                    // HTML template for work order details
                    let html = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-section">
                            ${generateInfoRow("Work Order Number", work_order ? work_order.work_order_number : "-")}
                            ${generateInfoRow("Sales Order Number", work_order ? work_order.sales_order_number : "-")}
                            ${generateInfoRow("Work Order Instruction Number", work_order_instruction_number)}
                            ${generateInfoRow("Date", date)}
                            ${generateInfoRow("Date Complete", date_complete || "-")}
                            ${generateInfoRow("Customer", work_order ? work_order.customer_id : "-")}
                            ${generateInfoRow("Total (IDR)", work_order ? work_order.total : "-")}
                            ${generateInfoRow("Address", work_order ? work_order.address : "-")}
                            ${generateInfoRow("Link WO Instruction", `<a href="${url}" target="_blank">${url}</a>`)}
                            ${generateInfoRow("Technicians", updated_by ? updated_by.name : "-")}
                        </div>
                    </div>
                </div>
                <div class="row d-none">
                    <h5 class="mt-4">Photos</h5>
                    <div class="d-flex flex-wrap gap-3">
                        ${generatePhotoGallery(photos, baseUrl)}
                    </div>
                </div>
                <div class="col-md-12 mt-4">
                    <h5>Instructions</h5>
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" width="20px">Step</th>
                                <th scope="col">Instruction</th>
                                <th scope="col">Answer</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${generateInstructionRows(answers, baseUrl)}
                        </tbody>
                    </table>
                </div>
            `;

                    $('#modal-detail-body').html(html);
                },
                error: function(xhr) {
                    $('#modal-detail-body').html(`
                <div class="text-center">
                    <p class="text-danger">Failed to load work order detail.</p>
                </div>
            `);
                }
            });

            // Helper function to generate information row
            function generateInfoRow(label, value) {
                return `
            <div class="row mb-2">
                <div class="col-md-4 font-weight-bold">${label}</div>
                <div class="col-md-8">${value}</div>
            </div>
        `;
            }

            // Helper function to generate photo gallery
            function generatePhotoGallery(photos, baseUrl) {
                return photos.map((photo, index) => {
                    let caption = "";
                    if (photo.step === "step8") caption = "Sticker Akikita Photo";
                    if (photo.step === "step9") caption = "Battery Production Number Photo";
                    if (photo.step === "step9-2") caption =
                        "Photo of battery under hood with vehicle license plate";

                    return `
                <div class="photo-item text-center">
                    ${caption ? `<h6>${caption}</h6>` : ""}
                    <img src="${baseUrl}/${photo.image}" width="150px" class="img-thumbnail" alt="Step ${index + 8} photo">
                </div>
            `;
                }).join('');
            }

            // Helper function to generate instruction rows
            function generateInstructionRows(answers, baseUrl) {
                return answers.map((instruction, index) => `
            <tr>
                <td>${instruction.instruction_step}</td>
                <td>${instruction.instruction}</td>
                <td>
                    ${instruction.type === "image" 
                        ? `<img src="${baseUrl}/${instruction.answer}" width="150px" class="img-thumbnail" alt="Step ${index + 1} photo">` 
                        : instruction.answer}
                </td>
            </tr>
        `).join('');
            }
        }
        // copy-work-btn 
        $(document).on('click', '.copy-work-btn', function() {
            var workOrderId = $('#work_order_id_mobile').val();
            var workOrderNumber = $('#work_order_number_mobile').val();
            if (workOrderId) {
                var url = window.location.origin + '/wo/' + workOrderId + '';
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

            } else {
                swal.fire({
                    title: 'No Work Order Selected',
                    text: 'Please select work order first',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
@endsection
