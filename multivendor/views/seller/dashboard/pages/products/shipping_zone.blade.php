@php
    $zones = \Plugin\TlcommerceCore\Models\ShippingZone::withCount('cities')
        ->where('profile_id', $id)
        ->get();
@endphp

<table>
    <thead>
        <tr>
            <th>{{ translate('Avaiable Shipping Zone') }}</th>
            <th>{{ translate('Cities') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($zones as $zone)
            <tr>
                <td>{{ $zone->name }}</td>
                <td>{{ $zone->cities_count }} {{ translate('Total Cities') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
