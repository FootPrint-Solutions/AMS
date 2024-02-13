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
    <style>
        .dataTables_filter {
            margin-top: -30px
        }

        .dataTables_length {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div id="main-wrapper">
        {{-- Header --}}
        @include('template.header')

        {{-- Sidebar --}}
        @include('template.sidebar')

        {{-- Content --}}
        <div class="page-wrapper mb-5">
            <div class="content container-fluid">
                {{-- Loading Indicator --}}
                <div class="spinner-border text-primary" id="loading-indicator">
                    <span class="sr-only">Loading...</span>
                </div>

                @yield('content')
            </div>

            @include('template.footer')
        </div>
    </div>

    {{-- Bootstrap Core JS --}}
    <script src="{{ asset('/js/bootstrap.bundle.min.js') }}"></script>

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

    {{-- Custom JS --}}
    <script src="{{ asset('/js/script.js') }}"></script>
</body>

<script>
    /**
     * Go to a certain view by replacing main-wrapper (to achieve SPA functionality).
     *
     * @param {string} destination - The destination view
     */
    function goToPage(destination) {
        $.ajax({
            url: destination,
            beforeSend: function() {
                $("#loading-indicator").show();
            },
            success: function(response) {
                $("#loading-indicator").hide();
                $("#main-wrapper").html(response);
            }
        });
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

</html>
