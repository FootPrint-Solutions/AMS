@foreach ($all_battery as $battery)
    &lt;h4 style="margin-bottom: 10px; font-size: 20px;"&gt;Spesifikasi Produk {{ $battery->name }}&lt;/h4&gt;
    &lt;table style="height: 280px;" width="328"&gt;
    &lt;tbody&gt;
    &lt;tr&gt;
    &lt;td&gt;Teknologi&lt;/td&gt;
    &lt;td&gt;{{ $battery->technology->name }}&lt;/td&gt;
    &lt;/tr&gt;
    &lt;tr&gt;
    &lt;td&gt;Panjang&lt;/td&gt;
    &lt;td&gt;{{ $battery->dimension_length }} mm&lt;/td&gt;
    &lt;/tr&gt;
    &lt;tr&gt;
    &lt;td&gt;Lebar&lt;/td&gt;
    &lt;td&gt;{{ $battery->dimension_width }} mm&lt;/td&gt;
    &lt;/tr&gt;
    &lt;tr&gt;
    &lt;td&gt;Tinggi&lt;/td&gt;
    &lt;td&gt;{{ $battery->dimension_height }} mm&lt;/td&gt;
    &lt;/tr&gt;
    &lt;tr&gt;
    &lt;td&gt;Standard CCA&lt;/td&gt;
    &lt;td&gt;{{ $battery->standard_cca }} A&lt;/td&gt;
    &lt;/tr&gt;
    &lt;tr&gt;
    &lt;td&gt;Kapasitas&lt;/td&gt;
    &lt;td&gt;{{ $battery->capacity }} AH&lt;/td&gt;
    &lt;/tr&gt;
    &lt;tr&gt;
    &lt;td&gt;Garansi&lt;/td&gt;
    &lt;td&gt;{{ $battery->warranty }} Bulan&lt;/td&gt;
    &lt;/tr&gt;
    &lt;/tbody&gt;
    &lt;/table&gt;

    <br><br>
@endforeach
