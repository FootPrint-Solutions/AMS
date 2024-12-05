@extends('template.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Work Order Instruction <span class="badge bg-success">NEW</span></h4>
                </div>
                <div class="card-body">
                    <div id="basic-pills-wizard" class="twitter-bs-wizard">
                        <ul class="twitter-bs-wizard-nav nav nav-pills nav-justified d-none">
                            @foreach ($data['workOrderInstructionTemplate'] as $key)
                                <li class="nav-item {{ $loop->first ? 'active' : '' }}">
                                    <a href="#step-{{ $key->id }}" class="nav-link" data-toggle="tab">
                                        <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ e($key->name) }}">
                                            <i class="fa fa-info-circle"></i>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content twitter-bs-wizard-tab-content">
                            <form action="/work-order-instruction/update-new" id="form" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="work_order_instruction_id" id="work_order_instruction_id"
                                    value="{{ $data['workOrderInstruction']['id'] }}">

                                @foreach ($data['workOrderInstructionTemplate'] as $key)
                                    @php
                                        $batteries = collect(
                                            $data['workOrderInstruction']['workOrder']['salesOrder']['batteries'],
                                        )
                                            ->pluck('battery_name')
                                            ->toArray();
                                        $linkgooglemaps =
                                            'https://maps.google.com/?q=' .
                                            $data['workOrderInstruction']['workOrder']['salesOrder']['latitude'] .
                                            ',' .
                                            $data['workOrderInstruction']['workOrder']['salesOrder']['longitude'];
                                        $linkgoogleMapsDistributor =
                                            'https://maps.google.com/?q=' .
                                            $data['workOrderInstruction']['workOrder']['salesOrder']['distributorShop'][
                                                'latitude'
                                            ] .
                                            ',' .
                                            $data['workOrderInstruction']['workOrder']['salesOrder']['distributorShop'][
                                                'longitude'
                                            ];
                                        $phoneWhatsApp =
                                            'https://wa.me/62' .
                                            $data['workOrderInstruction']['workOrder']['customer']['contact'];
                                        $replacements = [
                                            '<ADDRESSCUSTOMER>' => e(
                                                $data['workOrderInstruction']['workOrder']['address'],
                                            ),
                                            '<NAMECUSTOMER>' => e(
                                                $data['workOrderInstruction']['workOrder']['customer']['name'],
                                            ),
                                            '<PHONECUSTOMER>' => e($phoneWhatsApp),
                                            '<EMAILCUSTOMER>' => e(
                                                $data['workOrderInstruction']['workOrder']['customer']['email'],
                                            ),
                                            '<VEHICLECUSTOMER>' => e(
                                                $data['workOrderInstruction']['workOrder']['salesOrder']['vehicle'][
                                                    'name'
                                                ],
                                            ),
                                            '<BATTERYCUSTOMER>' => e(implode(', ', $batteries)),
                                            '<ADDRESSCUSTOMERLINK>' => e($linkgooglemaps),
                                            '<ADDRESSSHOP>' => e($linkgoogleMapsDistributor),
                                        ];

                                        // Decode HTML entities and apply replacements
                                        $decodedDescription = html_entity_decode($key->description);
                                        $description = str_replace(
                                            array_keys($replacements),
                                            array_values($replacements),
                                            $decodedDescription,
                                        );
                                    @endphp

                                    <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="step-{{ $key->id }}"
                                        style="{{ $loop->first ? 'display: block;' : 'display: none;' }}">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4 class="card-title">{{ e($key->name) }}</h4>
                                                <p>{!! $description !!}</p>

                                                @if ($key->details->count())
                                                    <div class="row">
                                                        @foreach ($key->details as $detail)
                                                            <div class="col-md-6">
                                                                <div class="form-group">

                                                                    @php
                                                                        $class =
                                                                            $detail->type == 'checkbox'
                                                                                ? ''
                                                                                : 'form-control';
                                                                        $value =
                                                                            $detail->type == 'checkbox'
                                                                                ? $detail->id
                                                                                : '';
                                                                        $type =
                                                                            $detail->type == 'image'
                                                                                ? 'file'
                                                                                : $detail->type;
                                                                        $accept =
                                                                            $detail->type == 'image'
                                                                                ? "accept='image/*'"
                                                                                : '';
                                                                        $required = $detail->is_required
                                                                            ? 'required'
                                                                            : '';
                                                                        $required_sign = $required
                                                                            ? " <span class='text-danger'><b>*</span>"
                                                                            : '';
                                                                    @endphp
                                                                    @if ($detail->type == 'checkbox')
                                                                        <input type="{{ $type }}"
                                                                            class="{{ $class }}"
                                                                            id="detail-{{ $detail->id }}"
                                                                            name="details[{{ $detail->id }}]"
                                                                            value="{{ $value }}"
                                                                            {{ $required }} {{ $accept }}>
                                                                        <label for="detail-{{ $detail->id }}"
                                                                            class="form-label">{{ e($detail->instruction) }}</label>
                                                                        {!! $required_sign !!}
                                                                    @else
                                                                        <label for="detail-{{ $detail->id }}"
                                                                            class="form-label">{{ e($detail->instruction) }}</label>{!! $required_sign !!}
                                                                        <input type="{{ $type }}"
                                                                            class="{{ $class }}"
                                                                            id="detail-{{ $detail->id }}"
                                                                            name="details[{{ $detail->id }}]"
                                                                            value="{{ $value }}"
                                                                            {{ $required }} {{ $accept }}>
                                                                    @endif

                                                                    <input type="hidden" name="detail_ids[]"
                                                                        value="{{ $detail->id }}">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                @endif
                                            </div>
                                            <ul class="pager wizard twitter-bs-wizard-pager-link">
                                                {{-- button Sebelumnya --}}
                                                @if ($loop->first)
                                                    <li class="previous d-none">
                                                        <a href="javascript:void(0);" class="btn btn-primary">Sebelumnya <i
                                                                class="bx bx-chevron-left ms-1"></i></a>
                                                    </li>
                                                @else
                                                    <li class="previous">
                                                        <a href="javascript:void(0);" class="btn btn-primary">Sebelumnya <i
                                                                class="bx bx-chevron-left ms-1"></i></a>
                                                    </li>
                                                @endif
                                                @if ($loop->last)
                                                    <li class="next save">
                                                        <button type="button btn btn-primary next-check"
                                                            class="btn btn-success btn-save">Selesai <i
                                                                class="bx bx-check-circle ms-1"></i></button>
                                                    </li>
                                                @else
                                                    <li class="next">
                                                        <a class="btn btn-primary next-check">Lanjut <i
                                                                class="bx bx-chevron-right ms-1"></i></a>
                                                    </li>
                                                    <li class="next d-none">
                                                        <a href="javascript:void(0);"
                                                            class="btn btn-primary seller-next-btn" id="next-step">Lanjut <i
                                                                class="bx bx-chevron-right ms-1"></i></a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let currentStep = localStorage.getItem('currentStep');
            if (currentStep) {
                $('.tab-pane').removeClass('active').hide();
                $(`#${currentStep}`).addClass('active').show();
            }

            $('.next-check').click(function() {
                // check if all required fields are filled
                let requiredFields = $(this).closest('.tab-pane').find('input[required]');
                let isValid = true;

                requiredFields.each(function() {
                    if ($(this).val() == '') {
                        isValid = false;
                    }
                });

                if (isValid) {
                    $(this).closest('.tab-pane').find('.seller-next-btn').click();
                } else {
                    swal.fire({
                        title: 'Error',
                        text: 'Please fill all required fields',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                }
            });

            $('.seller-next-btn').click(function(event) {
                // check if all required fields are filled
                let requiredFields = $(this).closest('.tab-pane').find('input[required]');
                let isValid = true;

                requiredFields.each(function() {
                    if ($(this).val() == '') {
                        isValid = false;
                    }
                });

                if (isValid) {
                    let currentTab = $(this).closest('.tab-pane');
                    let nextTab = currentTab.next();

                    currentTab.removeClass('active').hide();
                    nextTab.addClass('active').show();

                    localStorage.setItem('currentStep', nextTab.attr('id'));
                } else {
                    alert('Please fill all required fields');
                    event.preventDefault();
                }
            });

            $('.previous').click(function() {
                let currentTab = $(this).closest('.tab-pane');
                let prevTab = currentTab.prev();

                currentTab.removeClass('active').hide();
                prevTab.addClass('active').show();

                localStorage.setItem('currentStep', prevTab.attr('id'));
            });

            $('.btn-save').click(function() {
                // disable button to prevent double click
                $(this).prop('disabled', true);

                // check if all required fields are filled
                let requiredFields = $(this).closest('.tab-pane').find('input[required]');
                let isValid = true;

                requiredFields.each(function() {
                    if ($(this).val() == '') {
                        isValid = false;
                    }
                });

                if (isValid) {
                    swal.fire({
                        title: 'Loading',
                        text: 'Please wait...',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        onBeforeOpen: () => {
                            swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: $('#form').attr('action'),
                        method: 'POST',
                        data: new FormData($('#form')[0]),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status == 'success') {
                                swal.fire({
                                    title: 'Success',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                }).then(() => {
                                    window.location.href = '/work-order-instruction';
                                });
                            } else {
                                swal.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                });

                                $(this).prop('disabled', false);
                            }
                        },
                        error: function(xhr) {
                            swal.fire({
                                title: 'Error',
                                text: xhr.responseJSON.message,
                                icon: 'error',
                                confirmButtonText: 'OK',
                            });

                            $(this).prop('disabled', false);
                        }
                    });
                } else {
                    swal.fire({
                        title: 'Error',
                        text: 'Please fill all required fields',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });

                    $(this).prop('disabled', false);
                }

            });

            $('input, select, textarea').each(function() {
                let name = $(this).attr('name');
                if (name && localStorage.getItem(name)) {
                    $(this).val(localStorage.getItem(name));
                }
            });

            $('input, select, textarea').on('input change', function() {
                let name = $(this).attr('name');
                let value = $(this).val();
                if (name) {
                    localStorage.setItem(name, value);
                }
            });

            $('.btn-save').click(function() {
                localStorage.clear();
            });
        });
    </script>
@endsection
