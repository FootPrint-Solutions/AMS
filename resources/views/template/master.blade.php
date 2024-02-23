<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? '' }}</title>

    {{-- Favicon --}}
    {{-- <link rel="shortcut icon" href="assets/img/favicon.png"> --}}

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
</body>

<script>
    $(document).ready(function() {
        // OnClick Event Listener
        /**
         * Add a click listener to DataTables edit button in custom toolbar.
         */
        $("#content-container").on("click", ".edit-selected", function() {
            var selectedRows = table.rows({
                selected: true
            }).data().toArray();

            if (selectedRows.length === 0) {
                Swal.fire({
                    title: "Error",
                    text: "Please select at least one row to edit.",
                    icon: "error",
                });
                return;
            }

            var selectedRow = selectedRows[0];
            var id = selectedRow[$(this).attr("data-id")];
            edit(id);
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
                    text: "Please select at least one row to delete.",
                    icon: "error",
                });
                return;
            }
            var selectedRow = selectedRows[0];
            var id = selectedRow[$(this).attr("data-id")];
            destroy(id);
        });
        // End of OnClick Event Listener
    });

    /**
     * Go to a certain view by replacing main-wrapper (to achieve SPA functionality).
     *
     * @param {string} destination - The destination view
     */
    function goToPage(destination) {
        window.location.href = destination;
        // $.ajax({
        //     url: destination,
        //     beforeSend: function() {
        //         $("#loading-overlay").show();
        //         $("#loading-indicator").show();
        //     },
        //     success: function(response) {
        //         $("#loading-overlay").hide();
        //         $("#loading-indicator").hide();
        //         $("#main-wrapper").html(response);
        //     }
        // });
    }

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
                        if (callbakc !== null && typeof callback === "function") {
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
                    callback();
                }
            }
        });
    }

    /**
     * Append a custom toolbar component into DataTables table.
     * 
     * @param {int} idIdx - The index of data id.
     */
    function appendDatatablesToolbar(idIdx) {
        $.get("/datatables/toolbar", {
            idIdx: idIdx
        }, function(data) {
            $(".dt-buttons").append(data);
        });
    }

    /**
     * Get a list of custom DataTables button configurations in DataTables table.
     * 
     * @returns {Array} A list of button configurations DataTables.
     */
    function getDatatablesButtonConfigurations() {
        return [{
                text: "<i class='fas fa-file-alt'></i> Export to PDF",
                extend: "pdf",
                className: "btn btn-outline-danger btn-sm",
            },
            {
                text: "<i class='fas fa-file-excel'></i> Export to Excel",
                extend: "excel",
                className: "btn btn-outline-success btn-sm",
            },
            {
                text: "<i class='fas fa-sync-alt'></i> Refresh",
                action: function(e, dt, node, config) {
                    dt.ajax.reload();
                },
                className: "btn btn-outline-primary btn-sm",
            },
        ];
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
            inputField.val(n.toLocaleString());
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
