<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? '' }}</title>

    {{-- Favicon --}}
    <link rel="shortcut icon" href="/img/logos/32x32.png">

    {{-- Fontfamily --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;0,900;1,400;1,500;1,700&amp;display=swap"
        rel="stylesheet">

    {{-- Bootstrap CSS --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    {{-- Feathericon CSS --}}
    <link rel="stylesheet" href="{{ asset('plugins/feather/feather.css') }}">

    {{-- Toatr CSS --}}
    <link rel="stylesheet" href=" {{ asset('plugins//toastr/toatr.css') }}">

    {{-- Fontawesome CSS --}}
    <link rel="stylesheet" href="{{ asset('/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/fontawesome/css/all.min.css') }}">

    {{-- Datatables CSS --}}
    <link rel="stylesheet" href="{{ asset('/plugins/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables/select.dataTables.min.css') }}">

    {{-- Select2 CSS --}}
    <link rel="stylesheet" href="{{ asset('/plugins/select2/css/select2.min.css') }}">

    {{-- Main Preschool CSS --}}
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">

    {{-- Personal CSS --}}
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    {{-- jQuery --}}
    <script src="{{ asset('/js/jquery-3.7.1.min.js') }}"></script>

    {{-- Bootstrap Core JS --}}
    <script src="{{ asset('/js/bootstrap.bundle.min.js') }}"></script>

    {{-- Boostrap Form Wizard --}}
    <link rel="stylesheet" href="{{ asset('/plugins/twitter-bootstrap-wizard/form-wizard.css') }}">
</head>

<body>
    <div id="main-wrapper">
        {{-- Header --}}
        @include('template.header')

        {{-- Sidebar --}}
        @include('template.sidebar')

        {{-- Content --}}
        <div class="page-wrapper mb-5" id="content-container">
            <div class="content container-fluid">
                {{-- Loading Overlay --}}
                <div id="loading-overlay"></div>

                {{-- Loading Indicator --}}
                <div class="spinner-border text-primary" id="loading-indicator">
                    <span class="sr-only">Loading...</span>
                </div>

                @yield('content')
            </div>

            @include('template.footer')
        </div>
    </div>

    {{-- Feather Icon JS --}}
    <script src="{{ asset('/js/feather.min.js') }}"></script>

    {{-- Slimscroll JS --}}
    <script src="{{ asset('/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

    {{-- Chart JS --}}
    <script src="{{ asset('/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('/plugins/apexchart/chart-data.js') }}"></script>

    {{-- Toastr JS --}}
    <script src="{{ asset('/plugins/toastr/toastr.min.js') }}"></script>

    {{-- Datatables JS --}}
    <script src="{{ asset('/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/dataTables.select.min.js') }}"></script>


    {{-- Select2 JS --}}
    <script src="{{ asset('/plugins/select2/js/select2.min.js') }}"></script>

    {{-- Sweetalert JS --}}
    <script src="{{ asset('/plugins/sweetalert/sweetalerts.min.js') }}"></script>
    <script src="{{ asset('/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>

    {{-- Clipboard JS --}}
    <script src="{{ asset('/plugins/clipboard/clipboard.min.js') }}" type=" text/javascript"></script>

    {{-- Custom JS --}}
    <script src="{{ asset('/js/script.js') }}"></script>

    {{-- Bootstrap Form Wizard --}}
    <script src="{{ asset('/plugins/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>
    <script src="{{ asset('/plugins/twitter-bootstrap-wizard/prettify.js') }}"></script>
    <script src="{{ asset('/plugins/twitter-bootstrap-wizard/form-wizard.js') }}"></script>
</body>

{{-- OnClick Event Handler --}}
<script>
    $(document).ready(function() {
        /**
         * Add a click listener to DataTables edit button in custom toolbar.
         */
        $("#content-container").on("click", ".edit-selected", function() {
            var selectedRows = table.rows({
                selected: true
            }).data().toArray();

            if (selectedRows.length !== 1) {
                Swal.fire({
                    title: "Error",
                    text: "Please select a single row for editing.",
                    icon: "error",
                });
                return;
            }
            let id = selectedRows[0][$(this).data("id")];
            goToPage($(this).data("url") + id);
        });

        /**
         * Add a click listener to DataTables delete button in custom toolbar.
         */
        $("#content-container").on("click", ".delete-selected", function() {
            var selectedRows = table.rows({
                selected: true
            }).data().toArray();

            if (selectedRows.length === 0) {
                Swal.fire({
                    title: "Error",
                    text: "Please select at least one row for deleting.",
                    icon: "error",
                });
                return;
            }
            let ids = selectedRows.map(row => row[$(this).data("id")]);
            sendDestroyRequest(ids, $(this).data("url"), function() {
                // Reload the index table.
                table.ajax.reload();
            });
        });

        /**
         * Add a click listener to DataTables toggle button in custom toolbar.
         */
        $("#content-container").on("click", ".toggle-selected", function() {
            var selectedRows = table.rows({
                selected: true
            }).data().toArray();

            if (selectedRows.length !== 1) {
                Swal.fire({
                    title: "Error",
                    text: "Please select a single row for updating.",
                    icon: "error",
                });
                return;
            }
            let id = selectedRows[0][$(this).data("id")];
            sendToggleRequest(id, $(this).data("url"), function() {
                // Reload the index table.
                table.ajax.reload();
            });
        });
        // End of OnClick Event Listener
    });
</script>

{{-- JS AJAX Functions --}}
<script>
    /**
     * Send a POST request to destroy an item in database.
     *
     * @param {int} id - The id of item to be destroyed.
     * @param {string} url - The url of the destroyer function.
     * @param {function|null} callback - The table reload function after destroy process.
     */
    function sendDestroyRequest(id, url, callback = null) {
        // Show an alert before destroying an item.
        Swal.fire({
            title: "Are you sure?",
            text: "You are about to delete the selected item!",
            icon: "warning",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!"
        }).then(function(e) {
            // If user has confirmed, do the destroy process.
            if (e.value === true) {
                // Send the destroy POST request to url.
                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": id
                    },
                    success: function(response) {
                        // Get response data from url (in JSON).
                        let responseData = JSON.parse(response);

                        // Show Toast message based on responseData.
                        showResponseToast(responseData.status, responseData.message);

                        // Call the callback table reload act (or any other acts after the deletion process is complete).
                        if (callback !== null && typeof callback === "function") {
                            callback();
                        }
                    }
                });
            }
        });
    }

    /**
     * Send a POST request to toggle an item in database.
     *
     * @param {int} id - The id of item to be toggled.
     * @param {string} url - The url of the toggler function.
     * @param {function|null} callback - The table reload function after toggle process.
     */
    function sendToggleRequest(id, url, callback = null) {
        // Show an alert before destroying an item.
        Swal.fire({
            title: "Are you sure?",
            text: "You are about to update the status of the selected item!",
            icon: "warning",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Yes, update it!",
            cancelButtonText: "No, cancel!"
        }).then(function(e) {
            // If user has confirmed, do the toggle process.
            if (e.value === true) {
                // Send the toggle POST request to url.
                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": id
                    },
                    success: function(response) {
                        // Get response data from url (in JSON).
                        let responseData = JSON.parse(response);

                        // Show Toast message based on responseData.
                        showResponseToast(responseData.status, responseData.message);

                        // Call the callback table reload act (or any other acts after the toggle process is complete).
                        if (callback !== null && typeof callback === "function") {
                            callback();
                        }
                    }
                });
            }
        });
    }

    /**
     * Send a POST request to store or update an item in database.
     *
     * @param {string} url - The url of the storer or updater function.
     * @param {FormData} data - The form data to be submitted.
     * @param {function|null} callback - The redirecting process after updating or storing process.
     */
    function sendSubmitRequest(url, data, callback = null) {
        $.ajax({
            url: url,
            method: "POST",
            data: data,
            processData: false,
            contentType: false,
            success: function(response) {
                // Get response data (in JSON).
                let responseData = JSON.parse(response);

                // Show Toast message based on responseData.
                showResponseToast(responseData.status, responseData.message);

                // Call the callback redirect act.
                if (callback !== null && typeof callback === "function") {
                    setTimeout(callback, 0.5 * 1000);
                }
            }
        });
    }
</script>

{{-- JS Toastr Configuration Functions --}}
<script>
    /**
     * Displays a toast message using Toastr.
     *
     * @param {boolean} status - The proccess status.
     * @param {string} message - The success message to be displayed.
     */
    function showResponseToast(status, message) {
        if (status) {
            // Show the success toast.
            toastr.success(message, {
                closeButton: true,
                tapToDismiss: !1,
            });
        } else {
            // Show the error toast.
            toastr.error(message, {
                closeButton: !0,
                tapToDismiss: !1,
            });
        }
    }

    /**
     * Displays a success toast message using Toastr.
     *
     * @param {string} message - The success message to be displayed.
     */
    function showSuccessToast(message) {
        toastr.success(message, {
            closeButton: true,
            tapToDismiss: !1,
        });
    }

    /**
     * Displays an error toast message using Toastr.
     *
     * @param {string} message - The error message to be displayed.
     */
    function showErrorToast(message) {
        toastr.error(message, {
            closeButton: !0,
            tapToDismiss: !1,
        });
    }
</script>

{{-- JS DataTables Configuration Functions --}}
<script>
    /**
     * Append a custom toolbar component into DataTables table.
     * You have the option to select which buttons to display — whether it be for editing, deleting, or toggling — by specifying the values corresponding to the desired buttons.
     * 
     * @param {int} idIdx - The index of data id.
     * @param {string} editUrl - (Optional) The url of edit page.
     * @param {string} deleteUrl - (Optional) The url of delete page.
     * @param {string} toggleUrl  - (Optional) The url of toggle page.
     * @param {string} parentId - (Optional) The parent div id of the DataTables.
     */
    function appendDatatablesToolbar(idIdx, editUrl = null, deleteUrl = null, toggleUrl = null, parentId = null) {
        $.get("/datatables/toolbar", {
            idIdx: idIdx,
            editUrl: editUrl,
            deleteUrl: deleteUrl,
            toggleUrl: toggleUrl
        }, function(data) {
            var querySelector = ".dt-buttons";

            // If a DataTables is implemented in a table view other than the main table, additional parentId is required.
            // Current usage : Distributor Shop
            if (parentId !== null) {
                querySelector = parentId + " " + querySelector;
            }
            $(querySelector).append(data);
        });
    }

    /**
     * Get a list of custom DataTables button configurations in DataTables table.
     * 
     * @param {Array} A list of extra buttons (for specific cases).
     * @returns {Array} A list of button configurations DataTables.
     */
    function getDatatablesButtonConfigurations(extraButtons = null) {
        var buttons = [{
                text: "<i class='fas fa-file-alt'></i> Export to PDF",
                extend: "pdf",
                className: "btn btn-outline-danger btn-sm",
                exportOptions: {
                    format: {
                        body: function(data, row, column, node) {
                            let tempElement = document.createElement('div');
                            tempElement.innerHTML = data;
                            return tempElement.textContent.trim();
                        }
                    }
                },
            },
            {
                text: "<i class='fas fa-file-excel'></i> Export to Excel",
                extend: "excel",
                className: "btn btn-outline-success btn-sm",
                exportOptions: {
                    format: {
                        body: function(data, row, column, node) {
                            let tempElement = document.createElement('div');
                            tempElement.innerHTML = data;
                            data = tempElement.textContent.trim();

                            // Check whether current column is a price column.
                            if (node.classList.contains("table-col-price")) {
                                // If it is, remove any non-numeric characters.
                                return data.replace(/\./g, '');
                            }
                            return data;
                        }
                    }
                },
            },
            {
                text: "<i class='fas fa-sync-alt'></i> Refresh",
                action: function(e, dt, node, config) {
                    dt.ajax.reload();
                },
                className: "btn btn-outline-primary btn-sm",
            },
        ];

        // Append extra buttons if any is provided.
        if (extraButtons !== null) {
            buttons.push(extraButtons);
        }

        return buttons;
    }

    /**
     * Get a list of custom DataTables language configurations in DataTables table.
     * 
     * @param {string} searchPlaceholderKey - The key to generate the search placeholder text.
     * @param {string} [search=""] - The search value to display in the DataTable.
     * @returns {Object} A list of language configurations DataTables.
     */
    function getDatatablesLanguangeConfigurations(searchPlaceholderKey, search = "") {
        return {
            searchPlaceholder: "Search " + searchPlaceholderKey,
            search: search,
            lengthMenu: "_MENU_ entries | ",
        };
    }
</script>

{{-- JS Custom Functions --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
<script>
    /**
     * Go to a certain view by replacing main-wrapper (to achieve SPA functionality).
     *
     * @param {string} destination - The destination view
     * @param {boolean} openInNewWindow - Specifies whether to open the destination in a new window (true) or the current window (false). Default is false.
     */
    function goToPage(destination, openInNewWindow = false) {
        if (openInNewWindow) {
            window.open(destination, '_blank');
        } else {
            window.location.href = destination;
        }
    }

    /**
     * Download a pdf document based on the url.
     *
     * @param {string} url - The url view to be downloaded as pdf.
     */
    function downloadPDF(url) {
        $.ajax({
            url: url,
            type: 'GET',
            contentType: 'application/json',
            responseType: 'document',
            success: function(response) {
                // Create an iframe element
                var iframe = document.createElement('iframe');
                iframe.style.visibility = 'hidden';

                // Append the iframe to the document body
                document.body.appendChild(iframe);

                // Write the HTML content into the iframe
                var doc = iframe.contentWindow.document;
                doc.open();
                doc.write(response);
                doc.close();

                // Wait for the content to load, then trigger the print dialog
                iframe.onload = function() {
                    iframe.contentWindow.print();
                    document.body.removeChild(iframe);
                };
            }
        });
    }

    /**
     * Format price input field and displays an error warning message.
     *
     * @param {jQuery} inputField - The jQuery input price field object.
     * @param {jQuery|null} warning - (Optional) The jQuery warning message object.
     */
    function formatPrice(inputField, warning = null) {
        let n = parseInt(inputField.val().replace(/\D/g, ''), 10);

        if (!isNaN(n)) {
            if (warning !== null) {
                warning.hide();
            }
            inputField.val(n.toLocaleString("id-ID"));
        } else {
            if (warning !== null) {
                warning.show();
            }
            inputField.val("");
        }
    }

    if ($('.clipboard').length > 0) {
        var clipboard = new Clipboard('.btn');
    }
</script>

</html>
