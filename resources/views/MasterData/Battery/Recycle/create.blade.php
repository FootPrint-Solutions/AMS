@extends('template.master')

@section('content')
    {{-- Form --}}
    <div class="card">
        <div class="card-body">
            {{-- Title --}}
            <div class="card-title h5">
                @isset($data['profile'])
                    Edit
                @else
                    Add New
                @endisset
                Battery Recycle
            </div>
            <br>

            {{-- Form --}}
            <form id="battery-recycle-form">
                @csrf

                {{-- Name --}}
                <div class="form-group local-forms">
                    <label for="name">Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Enter battery recycle name" required autocomplete="off"
                        @isset($data['profile'])
                            value="{{ $data['profile']['name'] }}"
                        @endisset>
                </div>

                {{-- Price --}}
                <div class="form-group local-forms">
                    <label for="price">Price</label>
                    <input type="text" class="form-control" id="price" name="price" placeholder="Enter price"
                        @isset($data['profile'])
                            value="{{ $data['profile']['price'] }}"
                        @endisset>
                    <small id="price-warning-number" class="form-text text-danger" style="display: none;">Please
                        enter a valid numeric value for the price.</small>
                </div>

                {{-- Weight --}}
                <div class="form-group local-forms">
                    <label for="weight">Weight</label>
                    <input type="number" step="0.01" class="form-control" id="weight" name="weight"
                        placeholder="Enter weight"
                        @isset($data['profile'])
                            value="{{ $data['profile']['weight'] }}"
                        @endisset>
                </div>

                {{-- Note --}}
                <div class="form-group local-forms">
                    <label for="note">Note</label>
                    <textarea class="form-control" id="note" name="note" placeholder="Enter note">
@isset($data['profile'])
{{ $data['profile']['note'] }}
@endisset
</textarea>
                </div>

                {{-- Hidden Inputs --}}
                @isset($data['profile'])
                    <input type="hidden" name="id" value="{{ $data['profile']['id'] }}">
                @endisset

                {{-- Buttons --}}
                <div class="d-flex flex-row-reverse">
                    {{-- Create/Update Button --}}
                    <button type="submit" class="btn btn-success mx-1" id="btn-save"
                        @isset($data['profile'])
                            value="update">Update Battery Recycle</button>
                        @else
                            value="create">Create Battery Recycle</button>
                        @endisset
                        {{-- Cancel Button --}} <button type="reset" type="button" class="btn btn-danger mx-1"
                        id="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#battery-recycle-form").on("submit", function(event) {
                event.preventDefault();

                $("#btn-save").attr("disabled", true);
                $("#btn-save").html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                let mode = $("#btn-save").attr("value");
                let url = "/battery-recycle/store";
                if (mode == "update") {
                    url = "/battery-recycle/update";
                }

                let formData = new FormData($(this)[0]);

                sendSubmitRequest(url, formData, function() {
                    goToPage("/battery-recycle");
                });
            });

            $("#battery-recycle-form").on("reset", function() {
                goToPage("/battery-recycle");
            });

            formatPrice($("#price"), $("#price-warning-number"));

            $('#price').on("keyup", function() {
                formatPrice($("#price"), $("#price-warning-number"));
            });
        });
    </script>
@endsection
