<!--Refunds Module-->
@if (auth()->user()->can('Manage Refund Requests') || auth()->user()->can('Manage Refund reasons'))
    <li
        class="{{ Request::routeIs(['plugin.refund.requests', 'plugin.refund.reason.edit', 'plugin.refund.reasons.list']) ? 'active sub-menu-opened' : '' }}">
        <a href="#">
            <i class="icofont-ui-previous"></i>
            <span class="link-title">{{ translate('Refunds') }}</span>
        </a>
        <ul class="nav sub-menu">
            @if (auth()->user()->can('Manage Refund Requests'))
                <li class="{{ Request::routeIs(['plugin.refund.requests']) ? 'active ' : '' }}">
                    <a href="{{ route('plugin.refund.requests') }}">{{ translate('Refund Requests') }}</a>
                </li>
            @endif
            @if (auth()->user()->can('Manage Refund reasons'))
                <li
                    class="{{ Request::routeIs(['plugin.refund.reason.edit', 'plugin.refund.reasons.list']) ? 'active ' : '' }}">
                    <a href="{{ route('plugin.refund.reasons.list') }}">{{ translate('Refund Reasons') }}</a>
                </li>
            @endif

        </ul>
    </li>
@endif
<!--End Refunds Module-->
