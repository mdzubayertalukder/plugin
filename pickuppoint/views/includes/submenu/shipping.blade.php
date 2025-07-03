@if (isActivePluging('pickuppoint'))
    <li
        class="{{ Request::routeIs(['plugin.pickuppoint.edit.pickup.point', 'plugin.pickuppoint.pickup.points']) ? 'active ' : '' }}">
        <a href="{{ route('plugin.pickuppoint.pickup.points') }}">{{ translate('Pickup Points') }}</a>
    </li>
@endif
