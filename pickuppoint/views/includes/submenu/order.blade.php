@php
    $isactivatePickupPoint = isActivePluging('pickuppoint');
@endphp
@if ($isactivatePickupPoint)
    @if (auth()->user()->can('Manage Pickup Point Order'))
        <li class="{{ Request::routeIs(['plugin.pickuppoint.orders']) ? 'active ' : '' }}">
            <a href="{{ route('plugin.pickuppoint.orders') }}">{{ translate('Pickup Point Order') }}</a>
        </li>
    @endif
@endif
