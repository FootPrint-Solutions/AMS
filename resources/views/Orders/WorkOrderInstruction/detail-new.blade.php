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
                        <ul class="twitter-bs-wizard-nav nav nav-pills nav-justified">
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
                            <form action="/work-order-instruction/update" id="form" method="POST"
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

                                        $replacements = [
                                            '<ADDRESSCUSTOMER>' => e(
                                                $data['workOrderInstruction']['workOrder']['address'],
                                            ),
                                            '<NAMECUSTOMER>' => e(
                                                $data['workOrderInstruction']['workOrder']['customer']['name'],
                                            ),
                                            '<PHONECUSTOMER>' => e(
                                                $data['workOrderInstruction']['workOrder']['customer']['phone'],
                                            ),
                                            '<EMAILCUSTOMER>' => e(
                                                $data['workOrderInstruction']['workOrder']['customer']['email'],
                                            ),
                                            '<VEHICLECUSTOMER>' => e(
                                                $data['workOrderInstruction']['workOrder']['salesOrder']['vehicle'][
                                                    'name'
                                                ],
                                            ),
                                            '<BATTERYCUSTOMER>' => e(implode(', ', $batteries)),
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
                                                                    <label for="detail-{{ $detail->id }}"
                                                                        class="form-label">{{ e($detail->instruction) }}</label>
                                                                    @if ($detail->type == 'checkbox')
                                                                        @php
                                                                            $class = '';
                                                                            $value = $detail->id;
                                                                        @endphp
                                                                    @else
                                                                        @php
                                                                            $class = 'form-control';
                                                                            $value = '';
                                                                        @endphp
                                                                    @endif

                                                                    @if ($detail->type == 'image')
                                                                        @php
                                                                            $type = 'file';
                                                                            $accept = "accept='image/*'";
                                                                        @endphp
                                                                    @else
                                                                        @php
                                                                            $type = $detail->type;
                                                                            $accept = '';
                                                                        @endphp
                                                                    @endif

                                                                    @if ($detail->is_required)
                                                                        @php
                                                                            $required = 'required';
                                                                        @endphp
                                                                    @else
                                                                        @php
                                                                            $required = '';
                                                                        @endphp
                                                                    @endif

                                                                    <input type="{{ $type }}"
                                                                        class="{{ $class }}"
                                                                        id="detail-{{ $detail->id }}"
                                                                        name="details[{{ $detail->id }}]"
                                                                        value="{{ $value }}" {{ $required }}
                                                                        {{ $accept }}>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                @endif
                                            </div>
                                            <ul class="pager wizard twitter-bs-wizard-pager-link">
                                                @if ($loop->last)
                                                    <li class="save">
                                                        <button type="submit" class="btn btn-success">Save <i
                                                                class="bx bx-check-circle ms-1"></i></button>
                                                    </li>
                                                @else
                                                    <li class="next">
                                                        <a href="javascript:void(0);"
                                                            class="btn btn-primary seller-next-btn">Next <i
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
            $('.seller-next-btn').click(function() {
                // check if all required fields are filled
                let requiredFields = $(this).closest('.tab-pane').find('input[required]');
                let isValid = true;

                requiredFields.each(function() {
                    if ($(this).val() == '') {
                        isValid = false;
                    }
                });

                if (isValid) {
                    $(this).closest('.tab-pane').removeClass('active');
                    $(this).closest('.tab-pane').next().addClass('active');
                    $(this).closest('.tab-pane').hide();
                    $(this).closest('.tab-pane').next().show();
                } else {
                    alert('Please fill all required fields');
                }

                return false;
            });
        });
    </script>
@endsection
