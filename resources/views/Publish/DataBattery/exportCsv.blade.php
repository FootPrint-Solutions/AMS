<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
</style>
<table>
    <thead>
        <tr>
            <th>Battery Size</th>
            <th>Brands & Names</th>
            <th>Years</th>
            <th>Fuels</th>
            <th>Transmissions</th>
            <th>Battery Size Alt</th>
            <th>Brand</th>
            <th>Model</th>
        </tr>
    </thead>
    <tbody>
        @php
            // Group the data by the 6th key (Battery Size)
            $groupedData = collect($data)->groupBy(6);
        @endphp

        @foreach ($groupedData as $batterySize => $items)
            <tr>
                <td>{{ $batterySize }}</td>
                <td>
                    @php
                        // Collect unique brands and names in format 'Brand > Name'
                        $brandsNames = $items
                            ->map(function ($key) {
                                return $key[5] . ' > ' . $key[0]; // Brand > Name
                            })
                            ->unique()
                            ->implode(', '); // Combine into a single string
                        echo $brandsNames;
                    @endphp
                </td>
                <td>
                    @php
                        // Collect all years for this battery size
                        $years = [];
                        foreach ($items as $key) {
                            for ($i = $key[1]; $i <= $key[2]; $i++) {
                                $years[] = $i;
                            }
                        }
                        // Display unique sorted years
                        echo collect($years)->unique()->sort()->implode(', ');
                    @endphp
                </td>
                <td>
                    @php
                        // Collect unique fuels
                        $fuels = $items->pluck(3)->unique()->implode(', ');
                        echo $fuels;
                    @endphp
                </td>
                <td>
                    @php
                        // Collect unique transmissions
                        $transmissions = $items->pluck(4)->unique()->implode(', ');
                        echo $transmissions;
                    @endphp
                </td>
                <td>
                    {{ $items->first()[7] }}
                </td>
                <td>
                    @php
                        // Collect unique brands and names in format 'Brand > Name'
                        $brandsNames = $items
                            ->map(function ($key) {
                                return $key[5]; // Brand > Name
                            })
                            ->unique()
                            ->implode(', '); // Combine into a single string
                        echo $brandsNames;
                    @endphp
                </td>
                <td>
                    @php
                        // Collect unique brands and names in format 'Brand > Name'
                        $brandsNames = $items
                            ->map(function ($key) {
                                return $key[0]; // Brand > Name
                            })
                            ->unique()
                            ->implode(', '); // Combine into a single string
                        echo $brandsNames;
                    @endphp
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


<br><br><br>
<h1>Group By 7</h1>

<table>
    <thead>
        <tr>
            <th>Battery Size</th>
            <th>Brands & Names</th>
            <th>Years</th>
            <th>Fuels</th>
            <th>Transmissions</th>
            <th>Battery Size Alt</th>
            <th>Brand</th>
            <th>Model</th>
        </tr>
    </thead>
    <tbody>
        @php
            // Group the data by the 6th key (Battery Size)
            $groupedData = collect($data)->groupBy(7);
        @endphp

        @foreach ($groupedData as $batterySize => $items)
            <tr>
                <td>{{ $batterySize }}</td>
                <td>
                    @php
                        // Collect unique brands and names in format 'Brand > Name'
                        $brandsNames = $items
                            ->map(function ($key) {
                                return $key[5] . ' > ' . $key[0]; // Brand > Name
                            })
                            ->unique()
                            ->implode(', '); // Combine into a single string
                        echo $brandsNames;
                    @endphp
                </td>
                <td>
                    @php
                        // Collect all years for this battery size
                        $years = [];
                        foreach ($items as $key) {
                            for ($i = $key[1]; $i <= $key[2]; $i++) {
                                $years[] = $i;
                            }
                        }
                        // Display unique sorted years
                        echo collect($years)->unique()->sort()->implode(', ');
                    @endphp
                </td>
                <td>
                    @php
                        // Collect unique fuels
                        $fuels = $items->pluck(3)->unique()->implode(', ');
                        echo $fuels;
                    @endphp
                </td>
                <td>
                    @php
                        // Collect unique transmissions
                        $transmissions = $items->pluck(4)->unique()->implode(', ');
                        echo $transmissions;
                    @endphp
                </td>
                <td>
                    {{ $items->first()[7] }}
                </td>
                <td>
                    @php
                        // Collect unique brands and names in format 'Brand > Name'
                        $brandsNames = $items
                            ->map(function ($key) {
                                return $key[5]; // Brand > Name
                            })
                            ->unique()
                            ->implode(', '); // Combine into a single string
                        echo $brandsNames;
                    @endphp
                </td>
                <td>
                    @php
                        // Collect unique brands and names in format 'Brand > Name'
                        $brandsNames = $items
                            ->map(function ($key) {
                                return $key[0]; // Brand > Name
                            })
                            ->unique()
                            ->implode(', '); // Combine into a single string
                        echo $brandsNames;
                    @endphp
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
