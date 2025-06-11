@extends('template.master')

@section('content')
    <div class="card bg-white">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Post</h3>
                </div>
                <div class="col-auto text-end float-end ms-auto download-grp">
                    <button id="btn-add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New
                        Post</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{-- filter tanggal --}}
            <div class="row mt-2">
                <div class="col-md-1 d-flex align-items-center">
                    Status
                </div>

                <div class="col-md-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <select class="form-select" id="status-filter">
                                <option value="">All</option>
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-1"></div>

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="table-post">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Tags</th>
                        <th>Image</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        var table;

        $(document).ready(function() {
            table = $("#table-post").DataTable({
                lengthMenu: [
                    [5, 10, 25],
                    [5, 10, 25]
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "/post/show",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.status = $("#status-filter").val(); // Get the status filter value
                    }
                },
                columnDefs: [{
                        targets: [0, 5],
                        orderable: false
                    },
                    {
                        targets: 4, // Image
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    }
                ],
                dom: "lBfrtip",
                buttons: getDatatablesButtonConfigurations(),
                language: getDatatablesLanguangeConfigurations("Post"),
                select: true,
            });

            appendDatatablesToolbar(6, "/post/edit/", "/post/destroy");

            $("#btn-add").on("click", function() {
                goToPage("/post/create");
            });

            $('#table-post').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this post?')) {
                    $.ajax({
                        url: '/post/destroy',
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: [id]
                        },
                        success: function(res) {
                            table.ajax.reload();
                            alert(res.message);
                        },
                        error: function(err) {
                            alert('Failed to delete!');
                        }
                    });
                }
            });

            $("#status-filter").on("change", function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
