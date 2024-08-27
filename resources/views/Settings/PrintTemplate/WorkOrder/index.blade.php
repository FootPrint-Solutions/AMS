@extends('template.master')


@section('content')
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Work Order Print Template</h3>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            New Template</button>
                    </div>
                </div>
            </div>
            <br>

            {{-- Table --}}
            <table class="table table-striped" id="table-work-order-template">
                <thead>
                    <tr>
                        <th scope="col" class="table-col-no">#</th>
                        <th scope="col">Name</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="header-title mb-4">Print Templates Import</h4>
            <p class="text-muted font-13 mb-4">
                Import your print templates from Microsoft Word. Please note that the imported template will be in HTML
                format.
            </p>
            <textarea id="basic-example"></textarea>
        </div>
    </div>


    <script src="{{ asset('plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>

    {{-- DataTables Configurations --}}
    <script>
        var table;

        $(document).ready(function() {
            // DataTables configuration
            table = $("#table-work-order-template").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/template/show",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }, {
                    targets: [0, 1],
                    className: 'dt-body-center'
                }],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations({
                    text: "<i class='fas fa-add'></i> Add Details Template",
                    action: function(e, dt, node, config) {
                        // Get the selected row's id.
                        let selectedRows = table.rows({
                            selected: true
                        }).data().toArray();
                        if (selectedRows.length !== 1) {
                            Swal.fire({
                                title: "Error",
                                text: "Please select a single row for this action.",
                                icon: "error",
                            });
                            return;
                        }

                        // Redirect to the details page.
                        goToPage("/template/details/" + selectedRows[0][2]);
                    },
                    className: "btn btn-outline-secondary btn-sm",
                }),
                language: getDatatablesLanguangeConfigurations("Work Order Print Templates"),
                select: true,
            });

            // Load DataTables toolbar component.
            appendDatatablesToolbar(2, "/template/edit/", "/template/destroy");
        });
    </script>

    {{-- Click Handler --}}
    <script>
        $('#btn-add').on('click', function() {
            goToPage("/template/create");
        });

        // Add loading effect
        var editor = tinymce.init({
            selector: 'textarea#basic-example',
            height: 500,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount', 'importword'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help | importword',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
            init_instance_callback: function(editor) {
                editor.on('init', function() {
                    // Add your loading effect code 

                });
            }
        });
    </script>
@endsection
