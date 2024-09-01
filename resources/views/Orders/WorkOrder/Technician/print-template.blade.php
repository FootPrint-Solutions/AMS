@php
    // replace <WORKORDERID> with the work order id
    $template = str_replace('{WORKORDERID}', $workOrder->work_order_number, $importTemplate['template']);
    // replace <DATE> with the date
    $template = str_replace('{TANGGAL}', date('d-m-Y', strtotime($workOrder->salesOrder->date)), $template);
    // replace <ADDRESS> with the address
    $template = str_replace('{ALAMAT}', $workOrder->address, $template);
    // replace <BATTERIES> with the batteries
    $batteries = '';
    foreach ($workOrder->batteries as $battery) {
        $batteries .= $battery->battery_name . ', ';
    }
    $template = str_replace('{BATTERY}', $batteries, $template);

    echo $template;
@endphp

<script>
    window.print();
    window.onafterprint = function() {
        window.history.back();
    }
</script>
