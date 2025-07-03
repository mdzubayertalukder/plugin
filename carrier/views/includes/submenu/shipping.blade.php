@php
    $isactivateCarrier = isActivePluging('carrier');
@endphp
@if ($isactivateCarrier)
    <li class="{{ Request::routeIs(['plugin.carrier.list']) ? 'active ' : '' }}">
        <a href="{{ route('plugin.carrier.list') }}">{{ translate('Carriers') }}</a>
    </li>
@endif
