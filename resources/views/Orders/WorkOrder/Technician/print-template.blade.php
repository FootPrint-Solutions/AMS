@php
    $template = str_replace('{WORKORDERID}', $workOrder->work_order_number, $importTemplate['template']);
    $template = str_replace('{DATE}', date('d-m-Y', strtotime($workOrder->salesOrder->date)), $template);
    $template = str_replace('{ADDRESS}', $workOrder->address, $template);
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
