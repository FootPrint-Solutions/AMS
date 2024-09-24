@extends('template.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Work Order Instruction</h4>
                </div>
                <div class="card-body">
                    <div id="basic-pills-wizard" class="twitter-bs-wizard">
                        <ul class="twitter-bs-wizard-nav nav nav-pills nav-justified">
                            <li class="nav-item active">
                                <a href="#step-1" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        aria-label="Informasi Pesanan" data-bs-original-title="Informasi Pesanan">
                                        <i class="fa fa-info-circle"></i>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#step-2" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        aria-label="Siapkan Peralatan" data-bs-original-title="Siapkan Peralatan">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#step-3" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        aria-label="Kenakan Jaket Akikita" data-bs-original-title="Kenakan Jaket Akikita">
                                        <i class="fas fa-tshirt"></i>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#step-4" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        aria-label="Siapkan Google Maps" data-bs-original-title="Siapkan Google Maps">
                                        <i class="fas fa-map-pin"></i>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#step-5" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        aria-label="Nyalakan Sharing Lokasi"
                                        data-bs-original-title="Nyalakan Sharing Lokasi">
                                        <i class="fas fa-map"></i>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#step-6" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        aria-label="Menuju Pelanggan" data-bs-original-title="Menuju Pelanggan">
                                        <i class="fas fa-map"></i>
                                    </div>
                                </a>
                            </li>

                            {{-- 7. Ganti Aki Pelanggan --}}
                            <li class="nav-item">
                                <a href="#step-7" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        aria-label="Ganti Aki Pelanggan" data-bs-original-title="Ganti Aki Pelanggan">
                                        <i class="fas fa-car-battery"></i>
                                    </div>
                                </a>
                            </li>

                            {{-- 8. Tempelkan Stiker Akikita --}}
                            <li class="nav-item">
                                <a href="#step-8" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        aria-label="Tempelkan Stiker Akikita"
                                        data-bs-original-title="Tempelkan Stiker Akikita">
                                        <i class="fas fa-sticky-note"></i>
                                    </div>
                                </a>
                            </li>

                            {{-- 9. Foto 2 Bukti Instalasi --}}
                            <li class="nav-item">
                                <a href="#step-9" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        aria-label="Foto 2 Bukti Instalasi" data-bs-original-title="Foto 2 Bukti Instalasi">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </a>
                            </li>

                            {{-- 10. Tunggu Pembayaran --}}
                            <li class="nav-item">
                                <a href="#step-10" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        aria-label="Tunggu Pembayaran" data-bs-original-title="Tunggu Pembayaran">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                </a>
                            </li>

                            {{-- 11. Kembali ke Kantor --}}
                            <li class="nav-item">
                                <a href="#step-11" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        aria-label="Kembali ke Kantor" data-bs-original-title="Kembali ke Kantor">
                                        <i class="fas fa-home"></i>
                                    </div>
                                </a>
                            </li>

                            {{-- 12. Selesaikan Work Order --}}
                            <li class="nav-item">
                                <a href="#step-12" class="nav-link" data-toggle="tab">
                                    <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        aria-label="Selesaikan Work Order" data-bs-original-title="Selesaikan Work Order">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content twitter-bs-wizard-tab-content">
                            <div class="tab-pane active" id="step-1" style="display: block;">
                                <h5>Detail Pesanan</h5>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="basicpill-pancard-input" class="form-label">Nama Pelanggan</label>
                                            <input type="text" class="form-control" id="basicpill-pancard-input"
                                                value="{{ $data->workOrder->customer->name }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="basicpill-vatno-input" class="form-label">Kendaraan</label>
                                            <input type="text" class="form-control" id="basicpill-vatno-input"
                                                value="{{ $data->workOrder->salesOrder->vehicle->name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="basicpill-cstno-input" class="form-label">Lokasi
                                                Pelanggan</label>
                                            <input type="text" class="form-control" id="basicpill-cstno-input"
                                                value="{{ 'https://maps.google.com/?q=' . $data->workOrder->salesOrder->latitude . ',' . $data->workOrder->salesOrder->longitude }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <?php
                                    // foreach battery in sales order batteries
                                    $batteries = $data->workOrder->salesOrder->batteries;
                                    $batteryList = '';
                                    
                                    foreach ($batteries as $key) {
                                        $batteryList .= $key->battery_name . ', ';
                                    }
                                    ?>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="basicpill-servicetax-input" class="form-label">Aki yang
                                                dipesan</label>
                                            <input type="text" class="form-control" id="basicpill-servicetax-input"
                                                value="{{ $batteryList }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <ul class="pager wizard twitter-bs-wizard-pager-link">
                                    <li class="next"><a href="javascript: void(0);"
                                            class="btn btn-primary seller-next-btn">Next <i
                                                class="bx bx-chevron-right ms-1"></i></a></li>
                                </ul>
                            </div>

                            <div class="tab-pane" id="step-2" style="display: none; opacity: 1;">
                                <div>
                                    <div class="mb-4">
                                        <h5>Siapkan Peralatan</h5>
                                    </div>

                                    <form>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-namecard-input"
                                                        class="form-label">Ampelas</label>
                                                    <input type="checkbox" id="basicpill-namecard-input">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Tester Aki</label>
                                                    <input type="checkbox" id="basicpill-namecard-input">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-cardno-input" class="form-label">Set Kunci
                                                        Pas</label>
                                                    <input type="checkbox" id="basicpill-cardno-input">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-card-verification-input" class="form-label">Kain
                                                        Lap</label>
                                                    <input type="checkbox" id="basicpill-card-verification-input">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-expiration-input" class="form-label">Jumper
                                                        Portable</label>
                                                    <input type="checkbox" id="basicpill-expiration-input">
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <ul class="pager wizard twitter-bs-wizard-pager-link">
                                        <li class="previous disabled"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a></li>
                                        <li class="next"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-next-btn">Next <i
                                                    class="bx bx-chevron-right ms-1"></i></a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-pane" id="step-3" style="display: none; opacity: 1;">
                                <div>
                                    <div class="mb-4">
                                        <h5>Kenakan Jaket Akikita</h5>
                                    </div>


                                    <ul class="pager wizard twitter-bs-wizard-pager-link">
                                        <li class="previous disabled"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a></li>
                                        <li class="next"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-next-btn">Next <i
                                                    class="bx bx-chevron-right ms-1"></i></a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-pane" id="step-4" style="display: none; opacity: 1;">
                                <div>
                                    <div class="mb-4">
                                        <h5>Siapkan Google Maps</h5>
                                    </div>


                                    Klik link dibawah ini untuk membuka Google Maps <br>
                                    <a href="{{ 'https://maps.google.com/?q=' . $data->workOrder->salesOrder->latitude . ',' . $data->workOrder->salesOrder->longitude }}"
                                        target="_blank">{{ 'https://maps.google.com/?q=' . $data->workOrder->salesOrder->latitude . ',' . $data->workOrder->salesOrder->longitude }}</a>
                                    <br><br>


                                    <ul class="pager wizard twitter-bs-wizard-pager-link">
                                        <li class="previous disabled"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a></li>
                                        <li class="next"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-next-btn">Next <i
                                                    class="bx bx-chevron-right ms-1"></i></a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-pane" id="step-5" style="display: none; opacity: 1;">
                                <div>
                                    <div class="mb-4">
                                        <h5>Nyalakan Sharing Lokasi</h5>
                                    </div>


                                    Tekan tombol <strong>start</strong> <br>


                                    <ul class="pager wizard twitter-bs-wizard-pager-link">
                                        <li class="previous disabled"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a></li>
                                        <li class="next"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-next-btn">Next <i
                                                    class="bx bx-chevron-right ms-1"></i></a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-pane" id="step-6" style="display: none; opacity: 1;">
                                <div>
                                    <div class="mb-4">
                                        <h5>Menuju Pelanggan</h5>
                                    </div>

                                    Nama Pelanggan: {{ $data->workOrder->customer->name }} <br>
                                    Kendaraan: {{ $data->workOrder->salesOrder->vehicle->name }} <br>


                                    Kontak Pelanggan Lewat WhatsApp <br>
                                    <a href="https://wa.me/{{ $data->workOrder->salesOrder->customer->contact }}"
                                        target="_blank">{{ $data->workOrder->salesOrder->customer->contact }}</a>
                                    <br><br>

                                    <ul class="pager wizard twitter-bs-wizard-pager-link">
                                        <li class="previous disabled"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a></li>
                                        <li class="next"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-next-btn">Next <i
                                                    class="bx bx-chevron-right ms-1"></i></a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-pane" id="step-7" style="display: none; opacity: 1;">
                                <div>
                                    <div class="mb-4">
                                        <h5>Ganti Aki Pelanggan</h5>
                                    </div>

                                    <form>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-namecard-input" class="form-label">Buka kap
                                                        mesin / bonet</label>
                                                    <input type="checkbox" id="basicpill-namecard-input">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Lepaskan breket aki</label>
                                                    <input type="checkbox" id="basicpill-namecard-input">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-cardno-input" class="form-label">Gunakan jumper
                                                        dan ganti akinya</label>
                                                    <input type="checkbox" id="basicpill-cardno-input">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-card-verification-input"
                                                        class="form-label">Pasang
                                                        Kembali breket aki</label>
                                                    <input type="checkbox" id="basicpill-card-verification-input">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-expiration-input" class="form-label">Hidupkan
                                                        kendaraan pelanggan & cek voltase pengisian daya aki (Kondisi baik:
                                                        13.8v - 14.5v)</label>
                                                    <input type="checkbox" id="basicpill-expiration-input">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-expiration-input"
                                                        class="form-label">Daya</label>
                                                    <input type="text" class="form-control"
                                                        id="basicpill-expiration-input">
                                                </div>
                                            </div>
                                        </div>
                                    </form>


                                    <ul class="pager wizard twitter-bs-wizard-pager-link">
                                        <li class="previous disabled"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a></li>
                                        <li class="next"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-next-btn">Next <i
                                                    class="bx bx-chevron-right ms-1"></i></a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-pane" id="step-8" style="display: none; opacity: 1;">
                                <div>
                                    <div class="mb-4">
                                        <h5>Tempelkan Stiker Akikita</h5>
                                    </div>

                                    <form>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-namecard-input" class="form-label">Tuliskan
                                                        tanggal hari ini:</label>
                                                    <input type="text" class="form-control"
                                                        id="basicpill-namecard-input" value="{{ date('Y-m-d') }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Tempelkan stiker</label>
                                                    <input type="checkbox" id="basicpill-namecard-input">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-cardno-input" class="form-label">Foto stiker
                                                        Akikita di atas aki:</label>
                                                    <input type="file" id="basicpill-cardno-input">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- sample image  --}}
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <img src="{{ asset('storage/image/work-order/Contoh_Foto_Stiker.jpg') }}"
                                                        alt="sample image" class="img-fluid">
                                                </div>
                                            </div>
                                        </div>
                                    </form>


                                    <ul class="pager wizard twitter-bs-wizard-pager-link">
                                        <li class="previous disabled"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a></li>
                                        <li class="next"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-next-btn">Next <i
                                                    class="bx bx-chevron-right ms-1"></i></a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-pane" id="step-9" style="display: none; opacity: 1;">
                                <div>
                                    <div class="mb-4">
                                        <h5>Foto 2 Bukti Instalasi</h5>
                                    </div>


                                    <form>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-namecard-input" class="form-label">Nomor
                                                        Produksi Aki</label>
                                                    <input type="text" class="form-control"
                                                        id="basicpill-namecard-input">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Foto</label>
                                                    <input type="file" id="basicpill-namecard-input">

                                                    {{-- sample image  --}}
                                                    <div class="mb-3">
                                                        <img src="{{ asset('storage/image/work-order/Contoh_Foto_Nomor_Produksi.jpg') }}"
                                                            alt="sample image" class="img-fluid">
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="basicpill-cardno-input" class="form-label">Aki di dalam
                                                        kap mesin bersama plat nomor kendaraan</label>
                                                    <input type="file" id="basicpill-cardno-input">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    {{-- sample image  --}}
                                                    <div class="mb-3">
                                                        <img src="{{ asset('storage/image/work-order/Contoh_Foto_Aki_Dalam_Kap_Mesin.jpg') }}"
                                                            alt="sample image" class="img-fluid">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </form>

                                    <ul class="pager wizard twitter-bs-wizard-pager-link">
                                        <li class="previous disabled"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a></li>
                                        <li class="next"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-next-btn">Next <i
                                                    class="bx bx-chevron-right ms-1"></i></a></li>
                                    </ul>

                                </div>
                            </div>


                            <div class="tab-pane" id="step-10" style="display: none; opacity: 1;">
                                <div>
                                    <div class="mb-4">
                                        <h5>Tunggu Pembayaran</h5>
                                    </div>

                                    Kabarkan akikita di whatsapp, bahwa instalasi selesai. Lalu tunggu setelah pembayaran
                                    diterima <br>

                                    <ul class="pager wizard twitter-bs-wizard-pager-link">
                                        <li class="previous disabled"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a></li>
                                        <li class="next"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-next-btn">Next <i
                                                    class="bx bx-chevron-right ms-1"></i></a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-pane" id="step-11" style="display: none; opacity: 1;">
                                <div>
                                    <div class="mb-4">
                                        <h5>Kembali ke Kantor</h5>
                                    </div>

                                    Klik link dibawah ini untuk membuka Google Maps <br>
                                    @php
                                        $latitude = $data->workOrder->salesOrder->distributorShop->latitude ?? '';
                                        $longitude = $data->workOrder->salesOrder->distributorShop->longitude ?? '';

                                        if ($latitude == '' || $longitude == '') {
                                            $url = '';
                                        } else {
                                            $url = 'https://maps.google.com/?q=' . $latitude . ',' . $longitude;
                                        }
                                    @endphp
                                    <a href="{{ $url }}" target="_blank">{{ $url }}</a>
                                    <br><br>

                                    <ul class="pager wizard twitter-bs-wizard-pager-link">
                                        <li class="previous disabled"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a></li>
                                        <li class="next"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-next-btn">Next <i
                                                    class="bx bx-chevron-right ms-1"></i></a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-pane" id="step-12" style="display: none; opacity: 1;">
                                <div>
                                    <div class="mb-4">
                                        <h5>Selesaikan Work Order</h5>
                                    </div>

                                    <ul class="pager wizard twitter-bs-wizard-pager-link">
                                        <li class="previous disabled"><a href="javascript: void(0);"
                                                class="btn btn-primary seller-previous-btn"><i
                                                    class="bx bx-chevron-left me-1"></i> Previous</a></li>
                                        {{-- // save change  --}}
                                        <li class="float-end"><a href="javascript: void(0);" class="btn btn-primary">Save
                                                Changes</a></li>
                                    </ul>

                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
