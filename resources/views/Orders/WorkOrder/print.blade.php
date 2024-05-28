<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            font-size: 12px;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #000;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header h2 {
            margin: 0;
            font-size: 14px;
            font-weight: normal;
        }

        .section {
            margin-bottom: 10px;
        }

        .section h3 {
            margin-bottom: 5px;
            font-size: 14px;
        }

        .section table {
            width: 100%;
            border-collapse: collapse;
        }

        .section table,
        .section th,
        .section td {
            border: 1px solid #000;
        }

        .section th,
        .section td {
            padding: 5px;
            text-align: left;
        }

        .section .details td {
            width: 50%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Work Order</h1>
            <h2>Work Order Number: {{ $workOrder['work_order_number'] }}</h2>
        </div>

        <div class="section">
            <h3>Work Order Master</h3>
            <table>
                <tr class="details">
                    <td>Date</td>
                    <td>{{ $workOrder['date'] }}</td>
                </tr>
                <tr class="details">
                    <td>Sales Order ID</td>
                    <td>{{ $workOrder['sales_order_id'] }}</td>
                </tr>
                <tr class="details">
                    <td>Customer ID</td>
                    <td>{{ $workOrder['customer_id'] }}</td>
                </tr>
                <tr class="details">
                    <td>Tax</td>
                    <td>{{ $workOrder['tax'] }}</td>
                </tr>
                <tr class="details">
                    <td>Tax Price</td>
                    <td>{{ $workOrder['tax_price'] }}</td>
                </tr>
                <tr class="details">
                    <td>Discount</td>
                    <td>{{ $workOrder['discount'] }}</td>
                </tr>
                <tr class="details">
                    <td>Discount Price</td>
                    <td>{{ $workOrder['discount_price'] }}</td>
                </tr>
                <tr class="details">
                    <td>Total</td>
                    <td>{{ $workOrder['total'] }}</td>
                </tr>
                <tr class="details">
                    <td>Address</td>
                    <td>{{ $workOrder['address'] }}</td>
                </tr>
                <tr class="details">
                    <td>Latitude</td>
                    <td>{{ $workOrder['latitude'] }}</td>
                </tr>
                <tr class="details">
                    <td>Longitude</td>
                    <td>{{ $workOrder['longitude'] }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>Work Order Battery (Detail)</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Work Order ID</th>
                    <th>Battery ID</th>
                    <th>Battery Name</th>
                    <th>Battery Price</th>
                    <th>Quantity</th>
                    <th>Created At</th>
                </tr>
                <!-- Example row, this should be generated dynamically -->
                <?php 
                $id = 1;
                foreach ($workOrder['batteries'] as $battery) {
                    $work_order_id = $battery['work_order_id'];
                    $battery_id = $battery['battery_id'];
                    $battery_name = $battery['battery_name'];
                    $battery_price = $battery['battery_price'];
                    $quantity = $battery['quantity'];
                    $created_at = $battery['created_at'];
                ?>
                <tr>
                    <td>{{ $id }}</td>
                    <td>{{ $work_order_id }}</td>
                    <td>{{ $battery_id }}</td>
                    <td>{{ $battery_name }}</td>
                    <td>{{ $battery_price }}</td>
                    <td>{{ $quantity }}</td>
                    <td>{{ $created_at }}</td>
                </tr>
                <?php $id++; } ?>
                <!-- Repeat for each battery detail -->
            </table>
        </div>
    </div>
</body>

</html>
